<?php

namespace App\Jobs;

use App\Helper;
use App\Models\User;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ExportUserTable implements ShouldQueue
{
    use Batchable;
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public $index;

    public $itemCountBatching;

    public $file;

    public $filters;

    public $checkboxValues;

    public $search;

    public $extraParam;

    /**
     * Create a new job instance.
     */
    public function __construct($index, $itemCountBatching, $file, $filters, $checkboxValues, $search, $extraParam)
    {
        $this->index = $index;
        $this->itemCountBatching = $itemCountBatching;
        $this->file = $file;
        $this->filters = $filters;
        $this->checkboxValues = $checkboxValues;
        $this->search = $search;
        $this->extraParam = $extraParam;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Extract parameters
        $index = $this->index;
        $itemCountBatching = $this->itemCountBatching;
        $sr_no = $offset = ($index - 1) * $itemCountBatching;
        $file = $this->file;
        $search = $this->search;

        // Initialize query builder with joins matching Show.php
        $query = User::query()
            ->leftJoin('roles', 'users.role_id', '=', 'roles.id')
            ->leftJoin('countries', 'users.country_id', '=', 'countries.id')
            ->leftJoin('states', 'users.state_id', '=', 'states.id')
            ->leftJoin('cities', 'users.city_id', '=', 'cities.id')
            ->leftJoin('user_hobbies', 'users.id', '=', 'user_hobbies.user_id')
            ->leftJoin('user_documents', 'users.id', '=', 'user_documents.user_id')
            ->select([
                'users.name',
                'users.email',
                'roles.name as role_name',
                DB::raw("DATE_FORMAT(users.dob, '%d-%m-%Y') as dob_formatted"),
                'countries.name as country_name',
                'states.name as state_name',
                'cities.name as city_name',
               DB::raw(
                    '(CASE
                        WHEN users.gender = "' . config('constants.user.gender.key.female') . '" THEN "' . config('constants.user.gender.value.female') . '"
                        WHEN users.gender = "' . config('constants.user.gender.key.male') . '" THEN "' . config('constants.user.gender.value.male') . '"
                        ELSE " "
                    END) AS gender_formatted'
                ),
                DB::raw(
                    '(CASE
                        WHEN users.status = "' . config('constants.user.status.key.active') . '" THEN "' . config('constants.user.status.value.active') . '"
                        WHEN users.status = "' . config('constants.user.status.key.inactive') . '" THEN "' . config('constants.user.status.value.inactive') . '"
                        ELSE " "
                    END) AS status_formatted'
                ),
                'users.description',
                DB::raw('GROUP_CONCAT(DISTINCT user_hobbies.hobby) as hobbies_list'),
                DB::raw('GROUP_CONCAT(DISTINCT users.skills) as skills_list'),
                'users.bg_color',
                'users.timezone',
                DB::raw("DATE_FORMAT(users.event_date, '%d-%m-%Y') as event_date_formatted"),
                DB::raw("DATE_FORMAT(users.event_datetime, '%d-%m-%Y %H:%i:%s') as event_datetime_formatted"),
                DB::raw("DATE_FORMAT(users.event_time, '%H:%i:%s') as event_time_formatted"),
                'users.age',
                DB::raw('JSON_EXTRACT(users.consent_data, "$.terms_accepted") as terms_accepted'),
                DB::raw('JSON_EXTRACT(users.consent_data, "$.privacy_policy_accepted") as privacy_policy_accepted'),
                DB::raw('JSON_EXTRACT(users.consent_data, "$.data_processing_consent") as data_processing_consent'),
                DB::raw('JSON_EXTRACT(users.consent_data, "$.marketing_consent") as marketing_consent'),
                DB::raw("DATE_FORMAT(users.created_at, '%d-%m-%Y %H:%i:%s') as created_at_formatted"),
            ])
            ->groupBy('users.id');

        // Apply name filters
        $where_name = $this->filters['input_text']['users']['name'] ?? null;
        if ($where_name) {
            $query->where('users.name', 'like', "%$where_name%");
        }

        // Apply email filters
        $where_email = $this->filters['input_text']['users']['email'] ?? null;
        if ($where_email) {
            $query->where('users.email', 'like', "%$where_email%");
        }

        // Apply role_id filters
        $where_role_id = $this->filters['select']['users']['role_id'] ?? null;
        if ($where_role_id) {
            $query->where('users.role_id', $where_role_id);
        }

        // Apply dob filters
        $where_start = $this->filters['date']['users.dob']['start'] ?? null;
        $where_end = $this->filters['date']['users.dob']['end'] ?? null;

        if ($where_start && $where_end) {
            $start_date = Carbon::parse($where_start)->format('Y-m-d');
            $end_date = Carbon::parse($where_end)->format('Y-m-d');

            $query->whereBetween('users.dob', [$start_date, $end_date]);
        }

        // Apply status filters
        $where_status = $this->filters['select']['users']['status'] ?? null;
        if ($where_status) {
            $query->where('users.status', $where_status);
        }

        // Apply event_datetime filters
        $where_event_datetime_start = $this->filters['datetime']['users.event_datetime']['start'] ?? null;
        $where_event_datetime_end = $this->filters['datetime']['users.event_datetime']['end'] ?? null;

        if ($where_event_datetime_start && $where_event_datetime_end) {
            $start_event_datetime = Carbon::parse($where_event_datetime_start)->format('Y-m-d H:i:s');
            $end_event_datetime = Carbon::parse($where_event_datetime_end)->format('Y-m-d H:i:s');

            $query->whereBetween('users.event_datetime', [$start_event_datetime, $end_event_datetime]);
        }

        // Apply checkbox filter: If user select checkbox then only that result will be exported
        if ($this->checkboxValues) {
            $query->whereIn('users.id', $this->checkboxValues);
        }

        // Execute query and fetch data
        $query_data = $query
            ->whereNull('users.deleted_at')
            ->orderByDesc('users.id')
            ->groupBy('users.id')
            ->skip($offset)->take($itemCountBatching)->get()->toArray();

        // Convert query result to array
        // $final_data = json_decode(json_encode($query_data), true);

        // Format dates in PHP
        $final_data = array_map(function($item) {
            if (isset($item['event_date']) && $item['event_date']) {
                $item['event_date'] = \Carbon\Carbon::parse($item['event_date'])->format('d/m/Y');
            }
            return $item;
        }, $query_data);

        // Call Helper method to put data into export file
        Helper::putExportData('ExportUserTable', $final_data, $file, $sr_no);
    }
}
