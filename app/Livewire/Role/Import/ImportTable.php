<?php

namespace App\Livewire\Role\Import;

use App\Helper;
use App\Models\ImportLog;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Footer;
use PowerComponents\LivewirePowerGrid\Header;
use PowerComponents\LivewirePowerGrid\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\Responsive;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;
use Symfony\Component\HttpFoundation\Response;

final class ImportTable extends PowerGridComponent
{
    use WithExport;

    public bool $deferLoading = true; // default false

    public string $loadingComponent = 'components.my-custom-loading';

    public string $sortField = 'id';

    public string $sortDirection = 'desc';

    // Custom per page
    public int $perPage;

    // Custom per page values
    public array $perPageValues;

    public $currentRole;

    public string $search = '';

    public function __construct()
    {
        if (! Gate::allows('import-role')) {
            abort(Response::HTTP_FORBIDDEN);
        }
        $this->currentRole = Auth::user();
        $this->perPage = config('constants.webPerPage');
        $this->perPageValues = config('constants.webPerPageValues');
    }

    public function header(): array
    {
        return [];
    }

    public function setUp(): array
    {
        Responsive::make();

        return [
            Header::make(),

            Footer::make()
                ->showPerPage($this->perPage, $this->perPageValues)
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        if (isset($_GET['file_name'])) {
            $this->search = $_GET['file_name'];
        }

        return ImportLog::query()->where('model_name', config('constants.import_type.role'));
    }

    public function relationSearch(): array
    {
        return [];
    }

    /**
     * fields
     */
    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('file_name')
            ->add('file_path')
            ->add('status', function (ImportLog $model) {
                return Helper::getImportStatusText($model->status);
            })
            ->add('no_of_rows')
            ->add('error_log')
            ->add('created_at_formatted', fn (ImportLog $model) => Carbon::parse($model->created_at)->format(config('constants.date_formats.default')));
    }

    public function columns(): array
    {
        return [
            Column::make(__('messages.import.file_lable'), 'file_name')
                ->sortable()
                ->searchable(),

            Column::make(__('messages.import.date_lable'), 'created_at_formatted', 'created_at'),

            Column::make(__('messages.import.no_of_rows_lable'), 'no_of_rows'),

            Column::make(__('messages.import.status_lable'), 'status'),

            Column::action(__('messages.import.actions_lable')),
        ];
    }

    public function filters(): array
    {
        return [
            Filter::inputText('file_name')->component('column-search-textbox', ['placeholder' => 'File']),
            Filter::select('status', 'status')
                ->dataSource(ImportLog::status())
                ->optionValue('status')
                ->optionLabel('label'),
            Filter::datetimepicker('created_at'),
            Filter::inputText('no_of_rows')->component('column-search-textbox', ['placeholder' => 'No. of rows']),
        ];
    }

    public function actions(ImportLog $row): array
    {
        if ($row->status == config('constants.import_csv_log.status.key.fail')) {
            return [

                Button::add('view')
                    ->slot('<i class="las la-eye fs-2 me-2"></i>')
                    ->class('inline-flex items-center justify-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500')
                    ->tooltip('View Details')
                    ->dispatchTo('role.import.import-error-page', 'viewImportErrors', ['errorLogs' => $row->error_log]),
            ];
        }

        return [];
    }
}
