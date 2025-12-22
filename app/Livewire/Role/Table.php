<?php

namespace App\Livewire\Role;

use App\Helper;
use App\Models\Role;
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

    public string $sortField = 'roles.created_at';

    public string $sortDirection = 'desc';

    // Custom per page
    public int $perPage;

    // Custom per page values
    public array $perPageValues;

    public $currentUser;

    public string $entityName;

    public string $entityNamePlural;

    public string $createRoute = 'role.add';

    public string $addMethod = 'addRole';

    public string $exportMethod = 'exportRoles';

    public array $headerActions = [];

    protected $listeners = ['role-created' => 'refresh'];

    public function __construct()
    {
        if (! Gate::allows('view-role')) {
            abort(Response::HTTP_FORBIDDEN);
        }

        $this->tableName = __('messages.role.listing.tableName');
        $this->entityName = __('messages.role.listing.entity_name');
        $this->entityNamePlural = $this->entityName . 's';
        $this->perPage = config('constants.webPerPage');
        $this->perPageValues = config('constants.webPerPageValues');
    }

    public function exportData()
    {
        try {
            // Define export parameters
            $exportClass = ExportRoleTable::class;
            $headingColumn = 'Id';
            $batchName = 'Export Role Table';
            $downloadPrefixFileName = 'RoleReports_';
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
            logger()->error('App\Livewire\RoleTable: exportData: Throwable', ['Message' => $e->getMessage(), 'TraceAsString' => $e->getTraceAsString()]);
            session()->flash('error', __('messages.role.messages.common_error_message'));

            return false;
        }
    }

    /**
     * header
     */
    public function header(): array
    {
        $headers = [];
        if (Gate::allows('add-role')) {
            $headers[] = Button::add('create')
                ->slot('
                 <div class="flex items-center justify-center" data-testid="create_button" title="Add User">
                    <svg class="h-5 w-5 text-pg-primary-500 dark:text-pg-primary-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                </div>
            ')
                ->class('focus:ring-primary-600 focus-within:focus:ring-primary-600 focus-within:ring-primary-600 dark:focus-within:ring-primary-600 flex rounded-md ring-1 transition focus-within:ring-2 dark:ring-pg-primary-600 dark:text-pg-primary-300 text-gray-600 ring-gray-300 dark:bg-pg-primary-800 bg-white dark:placeholder-pg-primary-400 rounded-md border-0 bg-transparent py-2 px-3 ring-0 placeholder:text-gray-400 focus:outline-none sm:text-sm sm:leading-6 w-11 h-11 inline-flex items-center justify-center ml-1')
                ->dispatch('open-slide', ['title' => 'Add Role', 'component' => 'role.create', 'params' => []]);
        }

        if (Gate::allows('bulkDelete-role')) {
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

    public function addRole()
    {
        // Navigate to create role page without page reload
        $this->redirect(route($this->createRoute), navigate: true);
    }

    public function exportRoles()
    {
        session()->flash('success', __('messages.role.messages.export_functionality_will_be_implemented_soon'));
    }

    /**
     * setUp
     */
    public function setUp(): array
    {
        $this->showCheckBox();

        return [
            PowerGrid::exportable('role-export')
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
        return Role::query()
            ->select([
                'roles.id', 'roles.name', 'roles.created_at',
                DB::raw(
                    '(CASE
                        WHEN roles.status = "' . config('constants.role.status.key.yes') . '" THEN  "' . config('constants.role.status.value.yes') . '"
                        WHEN roles.status = "' . config('constants.role.status.key.no') . '" THEN  "' . config('constants.role.status.value.no') . '"
                ELSE " "
                END) AS status'
                ),
            ]);
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
            ->add('created_at_formatted', fn ($row) => Carbon::parse($row->created_at)->format(config('constants.default_datetime_format')));
    }

    /**
     * columns
     */
    public function columns(): array
    {
        return [
            Column::make(__('messages.role.listing.id'), 'id')->sortable(),

            Column::make(__('messages.role.listing.name'), 'name')
                ->sortable()
                ->searchable(),
            Column::make(__('messages.created_date'), 'created_at_formatted', 'created_at'),
            Column::action(__('messages.role.listing.actions')),
        ];
    }

    /**
     * filters
     */
    public function filters(): array
    {
        return [
            Filter::inputText('name', 'roles.name')->operators(['contains']),
            Filter::datetimepicker('created_at'),
        ];
    }

    public function actions(Role $row): array
    {
        $actions = [];

        if (Gate::allows('show-role')) {
            $actions[] = Button::add('view')
                ->slot('<div title="View role information" class="flex items-center justify-center" data-testid="view_button">' . view('components.flux.icon.eye', ['variant' => 'micro', 'attributes' => new \Illuminate\View\ComponentAttributeBag(['class' => 'text-green-600 hover:text-green-800'])])->render() . '</div>')
                ->class('border border-green-200 text-green-600 hover:bg-green-50 hover:border-green-300 py-2 px-2 rounded text-sm cursor-pointer hover:cursor-pointer')
                ->dispatch('open-slide', ['title' => 'Role Details', 'component' => 'role.show', 'params' => ['id' => $row->id]]);
        }

        if (Gate::allows('edit-role')) {
            $actions[] = Button::add('edit')
                ->slot('<div title="Edit Role" class="flex items-center justify-center"><svg class="w-4 h-4 text-blue-600 hover:text-blue-800" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg></div>')
                ->class('border border-blue-200 text-blue-600 hover:bg-blue-50 hover:border-blue-300 py-2 px-2 rounded text-sm cursor-pointer hover:cursor-pointer')
                ->dispatch('open-slide', ['title' => 'Edit Role', 'component' => 'role.edit', 'params' => ['id' => $row->id]]);
        }

        if (Gate::allows('delete-role')) {
            $actions[] = Button::add('delete')
                ->slot('<div title="Delete role permanently" class="flex items-center justify-center" data-testid="delete_button">' . view('components.flux.icon.trash', ['variant' => 'micro', 'attributes' => new \Illuminate\View\ComponentAttributeBag(['class' => 'text-red-600 hover:text-red-800'])])->render() . '</div>')
                ->class('border border-red-200 text-red-600 hover:bg-red-50 hover:border-red-300 py-2 px-2 rounded text-sm cursor-pointer hover:cursor-pointer')
                ->dispatch('deleteRole', ['id' => $row->id]);
        }

        return $actions;
    }

    #[On('deleteRole')]
    public function deleteRole($id)
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
                session()->flash('error', __('messages.role.messages.please_select_roles_to_delete'));
            }
        } catch (Throwable $e) {
            logger()->error('App\Livewire\Role\Table: bulkDelete: Throwable', [
                'Message' => $e->getMessage(),
                'TraceAsString' => $e->getTraceAsString(),
            ]);
            session()->flash('error', __('messages.role.messages.failed_to_initiate_bulk_delete'));
        }
    }
}
