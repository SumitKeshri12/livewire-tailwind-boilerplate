<?php

namespace App\Livewire\User;

use App\Helper;
use App\Jobs\ExportUserTable;
use App\Models\User;
use App\Services\CommonService;
use App\Traits\RefreshDataTable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\On;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Components\SetUp\Exportable;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;

use Throwable;

final class Table extends PowerGridComponent
{
    use RefreshDataTable;
    use WithExport;

    public bool $deferLoading = true; // default false

    public bool $showFilters = true;

    public string $tableName;

    public string $loadingComponent = 'components.powergrid-loading';

    public string $sortField = 'users.created_at';

    public string $sortDirection = 'desc';

    public string $errorMessage = '';

    // Custom per page
    public int $perPage;

    // Custom per page values
    public array $perPageValues;

    public function __construct()
    {
        if (! Gate::allows('view-user')) {
            abort(Response::HTTP_FORBIDDEN);
        }

        $this->tableName = config('constants.user.table.table_name');
        $this->perPage = config('constants.webPerPage');
        $this->perPageValues = config('constants.webPerPageValues');
    }

    public function header(): array
    {
        $buttons = [
            Button::add('add-user')
                ->slot('
                    <a href="/users/create" title="Add New User" data-testid="add_new" class="flex items-center justify-center" wire:navigate>
                        <svg class="h-5 w-5 text-pg-white-500 dark:text-pg-white-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                    </a>
                ')->class(
                    'flex rounded-md ring-1 transition focus:ring-2
                        dark:text-white text-white
                        bg-black hover:bg-gray-800
                        border-0 py-2 px-3
                        focus:outline-none
                        sm:text-sm sm:leading-6
                        w-11 h-9 inline-flex items-center justify-center ml-1
                        focus:ring-black focus:ring-offset-1
                        order-1'
                ),
        ];

        if (Gate::allows('delete-user')) {
            $buttons[] = Button::add('bulk-delete')
                ->slot('<div x-show="$wire.checkboxValues && $wire.checkboxValues.length > 0" x-transition>
                    <div class="flex items-center justify-center 
                        cursor-pointer focus:ring-red-600
                        flex rounded-md ring-1 transition focus:ring-2
                        text-white ring-red-700
                        bg-red-600 hover:bg-red-700
                        border-0 py-2 px-3
                        focus:outline-none
                        sm:text-sm sm:leading-6
                        w-11 h-9 inline-flex items-center justify-center ml-1
                        cursor-pointer"
                        data-testid="bulk_delete_button"
                        wire:click="bulkDelete"
                        title="Bulk Delete Selected Users">
                        <svg class="h-5 w-5 text-pg-white-500 dark:text-pg-white-300"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                            </path>
                        </svg>
                    </div>
                </div>
                ')->class('order-2');
        }

        $buttons[] =
           Button::add('export-user')
               ->slot('
                    <a wire:click="exportData" title="Download User Report" data-testid="export" class="flex items-center justify-center" wire:navigate>
                        <svg class="h-5 w-5 text-pg-white-500 dark:text-pg-white-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" wire:target="exportData">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                    </a>
                ')->class(
                   'flex rounded-md ring-1 transition focus:ring-2
                        dark:text-white text-white
                        bg-green-600 hover:bg-green-700
                        border-0 py-2 px-3
                        focus:outline-none
                        sm:text-sm sm:leading-6
                        w-11 h-9 inline-flex items-center justify-center ml-1
                        focus:ring-green-600 focus:ring-offset-1 cursor-pointer
                        order-3'
               );

        if (! empty($this->errorMessage)) {
            $buttons[] = Button::add('error-message')
                ->slot('<div class="ml-2 order-last inline-flex items-center rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-red-800 text-sm">' . e($this->errorMessage) . '</div>')
                ->id()
                ->class('ring-0 p-0 bg-transparent border-0 order-last');
        }

        return $buttons;
    }

    public function exportData()
    {
        try {
            // Define export parameters
            $exportClass = ExportUserTable::class;

            $headingColumn = 'Name,Email,Role,Dob,Country,State,City,Gender,Status,Description,Hobbies,Skills,Bg_color,Timezone,Event_date,Event_datetime,Event_time,Age,Terms_accepted,Privacy_policy_accepted,Data_processing_consent,Marketing_consent,Created_at';

            $batchName = 'Export User Table';
            $downloadPrefixFileName = 'UserReports_';
            $extraParam = [];

            // Run export job and handle result
            $result = Helper::runExportJob($this->total, $this->filters, $this->checkboxValues, $this->search, $headingColumn, $downloadPrefixFileName, $exportClass, $batchName, $extraParam);
            if (! $result['status']) {
                // Dispatch error alert if export fails
                $this->dispatch('alert', type: 'error', message: $result['message']);

                return false;
            }

            // Dispatch event to show export progress
            $this->dispatch('showExportProgressEvent', json_encode($result['data']))->to('common-code');
        } catch (Throwable $e) {
            // Log and dispatch error alert if exception occurs
            logger()->error('App\Livewire\UserTable: exportData: Throwable', ['Message' => $e->getMessage(), 'TraceAsString' => $e->getTraceAsString()]);
            session()->flash('error', __('messages.user.messages.common_error_message'));

            return false;
        }
    }

    public function setUp(): array
    {
        $this->showCheckBox();

        return [
            // PowerGrid::exportable('users-export')
            //     ->type(Exportable::TYPE_XLS, Exportable::TYPE_CSV),

            PowerGrid::header(),

            PowerGrid::footer()
                ->showPerPage($this->perPage, $this->perPageValues)
                ->showRecordCount(),

            PowerGrid::cache()
                ->ttl(300) // Increased to 5 minutes for large datasets (100K+ records)
                ->prefix((Auth::id() ?? 'guest') . '_')
                ->customTag('powergrid-user-users'),
        ];
    }

    public function datasource(): Builder
    {
        return User::query()
            ->leftJoin('roles', 'users.role_id', '=', 'roles.id')
            ->leftJoin('countries', 'users.country_id', '=', 'countries.id')
            ->leftJoin('states', 'users.state_id', '=', 'states.id')
            ->leftJoin('cities', 'users.city_id', '=', 'cities.id')
            ->leftJoin('user_hobbies', function ($join) {
                $join->on('users.id', '=', 'user_hobbies.user_id')
                    ->whereNull('user_hobbies.deleted_at');
            })
            ->select([
                'users.id',
                'users.name',
                'users.email',
                'users.profile',
                'users.role_id',
                'users.dob',
                'users.gender',
                'users.status',
                'users.country_id',
                'users.state_id',
                'users.city_id',
                'users.timezone',
                'users.skills',
                'users.bg_color',
                'users.description',
                'users.event_date',
                'users.event_datetime',
                'users.event_time',
                'users.created_at',
                'users.updated_at',
                'roles.name as role_name',
                'countries.name as country_name',
                'states.name as state_name',
                'cities.name as city_name',
                DB::raw('GROUP_CONCAT(DISTINCT user_hobbies.hobby) as hobbies_list'),
                DB::raw(
                    '(CASE
                        WHEN users.gender = "' . config('constants.user.gender.key.female') . '" THEN  "' . config('constants.user.gender.value.female') . '"
                        WHEN users.gender = "' . config('constants.user.gender.key.male') . '" THEN  "' . config('constants.user.gender.value.male') . '"
                        ELSE " "
                    END) AS gender_formatted'
                ),
                DB::raw(
                    '(CASE
                        WHEN users.status = "' . config('constants.user.status.key.active') . '" THEN  "' . config('constants.user.status.value.active') . '"
                        WHEN users.status = "' . config('constants.user.status.key.inactive') . '" THEN  "' . config('constants.user.status.value.inactive') . '"
                        ELSE " "
                    END) AS status_formatted'
                ),
            ])
            ->groupBy('users.id')
            ->orderBy('users.id', 'desc');
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('name')
            ->add('email')
            // ->add('profile_image', fn (User $model) => $this->getProfileImage($model))
            ->add('role_name')
            ->add('role_badge', fn (User $model) => CommonService::getRoleBadge($model->role_name))
            ->add('dob_formatted', fn (User $model) => $model->dob?->format(config('constants.date_formats.table_date')) ?? config('constants.user.table.default_values.not_set'))
            ->add('gender_formatted')
            ->add('status_formatted')
            ->add('status_badge', fn (User $model) => CommonService::getStatusBadge($model->status))
            ->add('country_name')
            ->add('state_name')
            ->add('city_name')
            ->add('timezone')
            ->add('timezone_label', fn (User $model) => (config('constants.timezones')[$model->timezone] ?? $model->timezone))
            ->add('hobbies_formatted', fn (User $model) => CommonService::getHobbiesFormattedFromList($model->hobbies_list))
            ->add('skills_formatted', fn (User $model) => CommonService::getSkillsFormatted($model->skills))
            ->add('event_date_formatted', fn (User $model) => $model->event_date?->format(config('constants.date_formats.table_date')) ?? config('constants.user.table.default_values.not_set'))
            ->add('event_datetime_formatted', fn (User $model) => $model->event_datetime?->format(config('constants.date_formats.table_datetime')) ?? config('constants.user.table.default_values.not_set'))
            ->add('event_time_formatted', fn (User $model) => CommonService::getEventTimeFormatted($model->event_time))
            ->add('bg_color_display', fn (User $model) => $model->bg_color ? '<span class="inline-block w-4 h-4 rounded border" style="background-color: ' . $model->bg_color . '"></span> ' . $model->bg_color : config('constants.user.table.default_values.not_set'))
            ->add('description_formatted', fn (User $model) => $model->description ? (strlen($model->description) > 50 ? substr($model->description, 0, 50) . '...' : $model->description) : config('constants.user.table.default_values.not_set'))
            ->add('created_at_formatted', fn (User $model) => $model->created_at->format(config('constants.date_formats.table_datetime')))
            ->add('updated_at_formatted', fn (User $model) => $model->updated_at->format(config('constants.date_formats.table_datetime')));
    }

    public function columns(): array
    {
        return [
            Column::add()
                ->title('ID')
                ->field('id', 'users.id')
                ->searchable()
                ->sortable(),

            Column::add()
                ->title('Name')
                ->field('name', 'users.name')
                ->searchable()
                ->sortable(),

            Column::add()
                ->title('Email')
                ->field('email', 'users.email')
                ->searchable()
                ->sortable(),

            Column::add()
                ->title('Role')
                ->field('role_badge', 'roles.name')
                ->searchable()
                ->sortable()
                ->visibleInExport(visible: false),

            Column::add()
                ->title('Date of Birth')
                ->field('dob_formatted', 'users.dob')
                ->sortable(),

            Column::add()
                ->title('Gender')
                ->field('gender_formatted', 'users.gender')
                ->sortable(),

            Column::add()
                ->title('Status')
                ->field('status_badge', 'users.status')
                ->searchable()
                ->sortable()
                ->visibleInExport(visible: false),

            Column::add()
                ->title(__('messages.user.table.columns.country'))
                ->field('country_name', 'countries.name')
                ->searchable()
                ->sortable(),

            Column::add()
                ->title('State')
                ->field('state_name', 'states.name')
                ->searchable()
                ->sortable(),

            Column::add()
                ->title('City')
                ->field('city_name', 'cities.name')
                ->searchable()
                ->sortable(),

            Column::add()
                ->title('Timezone')
                ->field('timezone_label', 'users.timezone')
                ->searchable()
                ->sortable(),

            Column::add()
                ->title('Hobbies')
                ->field('hobbies_formatted', 'users.hobbies')
                ->searchable()
                ->sortable(),

            Column::add()
                ->title('Skills')
                ->field('skills_formatted', 'users.skills')
                ->searchable()
                ->sortable(),

            Column::add()
                ->title('Event Date')
                ->field('event_date_formatted', 'users.event_date')
                ->sortable(),

            Column::add()
                ->title('Event Date & Time')
                ->field('event_datetime_formatted', 'users.event_datetime')
                ->sortable(),

            Column::add()
                ->title('Event Time')
                ->field('event_time_formatted', 'users.event_time')
                ->sortable(),

            Column::add()
                ->title('Background Color')
                ->field('bg_color_display', 'users.bg_color')
                ->sortable()
                ->bodyAttribute('class', 'text-center'),

            Column::add()
                ->title('Description')
                ->field('description_formatted', 'users.description')
                ->searchable()
                ->sortable(),

            Column::add()
                ->title('Created At')
                ->field('created_at_formatted', 'users.created_at')
                ->sortable(),

            Column::add()
                ->title('Updated At')
                ->field('updated_at_formatted', 'users.updated_at')
                ->sortable(),

            Column::action('Action')
                ->fixedOnResponsive(),
        ];
    }

    public function filters(): array
    {
        return [
            // Text input filters
            Filter::inputText('name', 'users.name')
                ->operators(['contains']),

            Filter::inputText('email', 'users.email')
                ->operators(['contains']),

            Filter::select('status_badge', 'users.status')
                ->dataSource([
                    ['label' => 'Active', 'value' => config('constants.user.status.key.active')],
                    ['label' => 'Inactive', 'value' => config('constants.user.status.key.inactive')],
                ])
                ->optionLabel('label')
                ->optionValue('value'),

            Filter::select('role_badge', 'users.role_id')
                ->dataSource(\App\Models\Role::all())
                ->optionLabel('name')
                ->optionValue('id'),

            Filter::datepicker('dob_formatted', 'users.dob'),

            Filter::datetimepicker('event_datetime_formatted', 'users.event_datetime'),

        ];
    }

    #[On('edit')]
    /**
     * edit
     *
     * @param mixed $rowId
     */
    public function edit($id)
    {
        return $this->redirect(route('users.edit', ['id' => $id]), navigate: true); // redirect to edit component
    }

    public function actions(User $row): array
    {
        $actions = [];
        if (Gate::allows('show-user')) {
            $actions[] = Button::add('view')
                ->slot('<div title="View user information" class="flex items-center justify-center" data-testid="view_button">' . view('components.flux.icon.eye', ['variant' => 'micro', 'attributes' => new \Illuminate\View\ComponentAttributeBag(['class' => 'text-green-600 hover:text-green-800'])])->render() . '</div>')
                ->class('border border-green-200 text-green-600 hover:bg-green-50 hover:border-green-300 py-2 px-2 rounded text-sm cursor-pointer hover:cursor-pointer')
                ->dispatchTo('user.show', 'show-user-info', ['id' => $row->id]);
        }

        if (Gate::allows('edit-user')) {
            $actions[] = Button::add('edit')
                ->slot('<div title="Edit user information" class="flex items-center justify-center" data-testid="edit_button">' . view('components.flux.icon.pencil', ['variant' => 'micro', 'attributes' => new \Illuminate\View\ComponentAttributeBag(['class' => 'text-blue-600 hover:text-blue-800'])])->render() . '</div>')
                ->dispatch('edit', ['id' => $row->id])
                ->class('border border-blue-200 text-blue-600 hover:bg-blue-50 hover:border-blue-300 py-2 px-2 rounded text-sm cursor-pointer hover:cursor-pointer');
        }

        if (Gate::allows('delete-user')) {
            $actions[] = Button::add('delete')
                ->slot('<div title="Delete user permanently" class="flex items-center justify-center" data-testid="delete_button">' . view('components.flux.icon.trash', ['variant' => 'micro', 'attributes' => new \Illuminate\View\ComponentAttributeBag(['class' => 'text-red-600 hover:text-red-800'])])->render() . '</div>')

                ->class('border border-red-200 text-red-600 hover:bg-red-50 hover:border-red-300 py-2 px-2 rounded text-sm cursor-pointer hover:cursor-pointer')
                ->dispatchTo('user.delete', 'delete-confirmation', ['ids' => [$row->id], 'tableName' => $this->tableName]);
        }

        return $actions;
    }

    /**
     * actionRules
     *
     * @param mixed $row
     */
    public function actionRules($row): array
    {
        return [];
    }

    /**
     * handlePageChange
     */
    public function handlePageChange()
    {
        $this->checkboxAll = false;
        $this->checkboxValues = [];
    }

    #[On('deSelectCheckBoxEvent')]
    public function deSelectCheckBox(): bool
    {
        $this->checkboxAll = false;
        $this->checkboxValues = [];
        $this->errorMessage = ''; // Clear error message when deselecting

        return true;
    }

    public function updatedCheckboxValues()
    {
        // Clear error message when users are selected
        if (! empty($this->checkboxValues)) {
            $this->errorMessage = '';
        }
    }

    public function bulkDelete(): void
    {
        try {
            // Clear any existing error message
            $this->errorMessage = '';

            if (! empty($this->checkboxValues)) {
                // Dispatch to the delete component
                $this->dispatch('bulk-delete-confirmation', [
                    'ids' => $this->checkboxValues,
                    'tableName' => $this->tableName,
                ]);
            } else {
                // Show flash message using Livewire event
                session()->flash('error', __('messages.user.bulk_delete.no_users_selected'));
            }
        } catch (Throwable $e) {
            // Defer logging to run after response
            defer(function () use ($e) {
                logger()->error('App\Livewire\User\Table: bulkDelete: Throwable', [
                    'Message' => $e->getMessage(),
                    'TraceAsString' => $e->getTraceAsString(),
                ]);
            });
            session()->flash('error', __('messages.user.bulk_delete.failed'));
        }
    }
}
