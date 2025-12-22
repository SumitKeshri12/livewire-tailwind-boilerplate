<?php

namespace App\Livewire\Brand;

use App\Helper;
use App\Models\Brand;
use App\Traits\RefreshDataTable;
use Carbon\Carbon;
use DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\On;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Components\SetUp\Exportable;
use PowerComponents\LivewirePowerGrid\Components\SetUp\Header;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final class Table extends PowerGridComponent
{
    use RefreshDataTable;
    use WithExport;

    public bool $deferLoading = true; // default false

    public string $tableName;

    public string $loadingComponent = 'components.powergrid-loading';

    public string $sortField = 'brands.created_at';

    public string $sortDirection = 'desc';

    // Custom per page
    public int $perPage;

    // Custom per page values
    public array $perPageValues;

    public $currentUser;

    public string $entityName;

    public string $entityNamePlural;

    public string $createRoute = 'brand.add';

    public string $addMethod = 'addBrand';

    public string $exportMethod = 'exportBrands';

    public array $headerActions = [];

    protected $listeners = ['brand-created' => 'refresh'];

    public function __construct()
    {
        if (! Gate::allows('view-brand')) {
            abort(Response::HTTP_FORBIDDEN);
        }

        $this->tableName = __('messages.brand.listing.tableName');
        $this->entityName = __('messages.brand.listing.entity_name');
        $this->entityNamePlural = $this->entityName . 's';
        $this->perPage = config('constants.webPerPage');
        $this->perPageValues = config('constants.webPerPageValues');
    }

    public function exportData()
    {
        try {
            // Define export parameters
            $exportClass = ExportBrandTable::class;
            $headingColumn = 'Id';
            $batchName = 'Export Brand Table';
            $downloadPrefixFileName = 'brandReports_';
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
            logger()->error('App\Livewire\BrandTable: exportData: Throwable', ['Message' => $e->getMessage(), 'TraceAsString' => $e->getTraceAsString()]);

            session()->flash('error', __('messages.brand.messages.common_error_message'));

            return false;
        }
    }

    /**
     * header
     */
    public function header(): array
    {
        $headers = [];
        if (Gate::allows('add-brand')) {
            $headers[] = Button::add('create')
                ->slot('
                    <div class="flex items-center justify-center" data-testid="create_button" title="Add Brand">
                        <svg class="h-5 w-5 text-pg-primary-500 dark:text-pg-primary-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                    </div>
                ')
                ->id()
                ->class('focus:ring-primary-600 focus-within:focus:ring-primary-600 focus-within:ring-primary-600 dark:focus-within:ring-primary-600 flex rounded-md ring-1 transition focus-within:ring-2 dark:ring-pg-primary-600 dark:text-pg-primary-300 text-gray-600 ring-gray-300 dark:bg-pg-primary-800 bg-white dark:placeholder-pg-primary-400 rounded-md border-0 bg-transparent py-2 px-3 ring-0 placeholder:text-gray-400 focus:outline-none sm:text-sm sm:leading-6 w-11 h-11 inline-flex items-center justify-center ml-1')
                ->call('addBrand', []);
        }

        if (Gate::allows('bulkDelete-brand')) {
            $headers[] = Button::add('bulk-delete')
                ->slot('
                <div class="flex items-center justify-center" data-testid="bulk_delete_button" title="Delete Selected">
                    <svg class="h-5 w-5 text-pg-primary-500 dark:text-pg-primary-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </div>
            ')
                ->id()
                ->class('focus:ring-primary-600 focus-within:focus:ring-primary-600 focus-within:ring-primary-600 dark:focus-within:ring-primary-600 flex rounded-md ring-1 transition focus-within:ring-2 dark:ring-pg-primary-600 dark:text-pg-primary-300 text-gray-600 ring-gray-300 dark:bg-pg-primary-800 bg-white dark:placeholder-pg-primary-400 rounded-md border-0 bg-transparent py-2 px-3 ring-0 placeholder:text-gray-400 focus:outline-none sm:text-sm sm:leading-6 w-11 h-11 inline-flex items-center justify-center ml-1')
                ->call('bulkDelete', []);
        }

        return $headers;
    }

    public function addBrand()
    {
        // Navigate to create user page without page reload
        $this->redirect(route($this->createRoute), navigate: true);
    }

    public function exportBrands()
    {
        session()->flash('success', __('messages.brand.messages.export_functionality_will_be_implemented_soon'));
    }

    /**
     * setUp
     */
    public function setUp(): array
    {
        $this->showCheckBox();

        return [
            PowerGrid::exportable('brand-export')
                ->striped()
                ->columnWidth([
                    1 => 30,  // ID column
                    2 => 50,  // Name column
                    3 => 60,  // Email column
                ])
                ->queues(2)
                ->type(Exportable::TYPE_XLS, Exportable::TYPE_CSV),

            PowerGrid::header(),

            PowerGrid::footer()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    /**
     * datasource
     */
    public function datasource(): Builder
    {
        // Main query
        return Brand::query()
            ->leftJoin('countries', 'countries.id', '=', 'brands.country_id')
            ->select([
                'brands.id', 'countries.name as country_name', 'brands.created_at',
                DB::raw(
                    '(CASE
                                        WHEN brands.status = "' . config('constants.brand.status.key.active') . '" THEN  "' . config('constants.brand.status.value.active') . '"
                                        WHEN brands.status = "' . config('constants.brand.status.key.inactive') . '" THEN  "' . config('constants.brand.status.value.inactive') . '"
                                ELSE " "
                                END) AS status'
                ), 'brands.bob', 'brands.start_date', 'brands.start_time',
            ])->groupBy('brands.id');
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
            ->add('id')
            ->add('bob_formatted', fn ($row) => Carbon::parse($row->bob)->format(config('constants.default_datetime_format')))
            ->add('start_date_formatted', fn ($row) => Carbon::parse($row->start_date)->format(config('constants.default_date_format')))
            ->add('start_time_formatted', fn ($row) => Carbon::parse($row->start_time)->format(config('constants.default_time_format')))
            ->add('created_at_formatted', fn ($row) => Carbon::parse($row->created_at)->format(config('constants.default_datetime_format')));
    }

    /**
     * columns
     */
    public function columns(): array
    {
        return [
            Column::make(__('messages.brand.listing.id'), 'id')->sortable(),

            Column::make(__('messages.brand.listing.countries'), 'country_name')
                ->sortable()
                ->searchable(),

            Column::make(__('messages.brand.listing.status'), 'status')
                ->sortable()
                ->searchable(),

            Column::make(__('messages.brand.listing.bob'), 'bob_formatted', 'bob')
                ->sortable()
                ->searchable(),

            Column::make(__('messages.brand.listing.start_date'), 'start_date_formatted', 'start_date')
                ->sortable()
                ->searchable(),

            Column::make(__('messages.brand.listing.start_time'), 'start_time_formatted', 'start_time')
                ->sortable()
                ->searchable(),
            Column::make(__('messages.created_date'), 'created_at_formatted', 'created_at'),
            Column::action(__('messages.brand.listing.actions')),
        ];
    }

    /**
     * filters
     */
    public function filters(): array
    {
        return [
            Filter::select('country_name', 'countries.name')
                ->dataSource(\App\Models\Country::all())
                ->optionLabel('name')
                ->optionValue('name'),
            Filter::select('status', 'status')
                ->dataSource(Brand::status())
                ->optionLabel('label')
                ->optionValue('key'),
            Filter::datetimepicker('bob'),
            Filter::datepicker('start_date'),

            Filter::datetimepicker('created_at'),
        ];
    }

    public function actions(Brand $row): array
    {
        $actions = [];

        if (Gate::allows('show-brand')) {
            $actions[] = Button::add('view')
                ->slot('<div title="View brand information" class="flex items-center justify-center" data-testid="view_button">' . view('components.flux.icon.eye', ['variant' => 'micro', 'attributes' => new \Illuminate\View\ComponentAttributeBag(['class' => 'text-green-600 hover:text-green-800'])])->render() . '</div>')
                ->class('border border-green-200 text-green-600 hover:bg-green-50 hover:border-green-300 py-2 px-2 rounded text-sm cursor-pointer hover:cursor-pointer')
                ->tooltip(__('messages.tooltip.view'))
                ->dispatchTo('brand.show', 'show-brand-info', ['id' => $row->id]);
        }

        if (Gate::allows('edit-brand')) {
            $actions[] = Button::add('edit')
                ->slot('<div title="Edit Brand" class="flex items-center justify-center"><svg class="w-4 h-4 text-blue-600 hover:text-blue-800" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg></div>')
                ->id()
                ->class('border border-blue-200 text-blue-600 hover:bg-blue-50 hover:border-blue-300 py-2 px-2 rounded text-sm cursor-pointer hover:cursor-pointer')
                ->route('brand.edit', ['id' => $row->id]);
        }

        if (Gate::allows('delete-brand')) {
            $actions[] = Button::add('delete')
                ->slot('<div title="Delete role permanently" class="flex items-center justify-center" data-testid="delete_button">' . view('components.flux.icon.trash', ['variant' => 'micro', 'attributes' => new \Illuminate\View\ComponentAttributeBag(['class' => 'text-red-600 hover:text-red-800'])])->render() . '</div>')
                ->class('border border-red-200 text-red-600 hover:bg-red-50 hover:border-red-300 py-2 px-2 rounded text-sm cursor-pointer hover:cursor-pointer')
                ->dispatch('deleteBrand', ['id' => $row->id]);
        }

        return $actions;
    }

    #[On('deleteBrand')]
    public function deleteBrand($id)
    {
        $id = $id ?? null;

        // Emit event to show delete modal immediately
        $this->dispatch('show-delete-modal', [
            'id' => $id,
        ]);
    }

    public function bulkDelete(): void
    {
        try {
            if (! empty($this->checkboxValues)) {
                // Debug: Log the checkbox values
                logger()->info('Bulk Delete - Checkbox Values:', $this->checkboxValues);

                // Try different dispatch methods
                $this->dispatch('delete-confirmation', [
                    'ids' => $this->checkboxValues,
                    'tableName' => $this->tableName,
                ]);
            } else {
                session()->flash('error', __('messages.brand.messages.please_select_brands_to_delete'));
            }
        } catch (Throwable $e) {
            logger()->error('App\Livewire\Brand\Table: bulkDelete: Throwable', [
                'Message' => $e->getMessage(),
                'TraceAsString' => $e->getTraceAsString(),
            ]);
            session()->flash('error', __('messages.brand.messages.failed_to_initiate_bulk_delete'));
        }
    }
}
