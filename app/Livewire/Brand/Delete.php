<?php

namespace App\Livewire\Brand;

use App\Models\Brand;
use Livewire\Attributes\On;
use Livewire\Component;

class Delete extends Component
{
    public bool $showModal = false;

    public $brand = null;

    public bool $isBulkDelete = false;

    public array $ids = [];

    public int $selectedBrandsCount = 0;

    public string $tableName = '';

    #[On('show-delete-modal')]
    public function showModal($data)
    {
        $id = $data['id'] ?? null;

        if ($id) {
            $this->brand = (object) ['id' => $id];
            $this->isBulkDelete = false;
            $this->ids = [];
            $this->showModal = true;
        }
    }

    #[On('delete-confirmation')]
    public function showDeleteConfirmation($data)
    {
        // Debug: Log the received data
        logger()->info('Delete Component - Received delete-confirmation event:', $data);

        $this->ids = $data['ids'] ?? [];
        $this->tableName = $data['tableName'] ?? '';
        $this->selectedBrandsCount = count($this->ids);
        $this->isBulkDelete = true;
        $this->brand = null;
        $this->showModal = true;

        // Debug: Log the modal state
        logger()->info('Delete Component - Modal state:', [
            'showModal' => $this->showModal,
            'isBulkDelete' => $this->isBulkDelete,
            'selectedBrandsCount' => $this->selectedBrandsCount,
            'ids' => $this->ids,
        ]);
    }

    public function hideModal()
    {
        $this->showModal = false;
        $this->brand = null;
        $this->isBulkDelete = false;
        $this->ids = [];
        $this->selectedBrandsCount = 0;
        $this->tableName = '';
    }

    public function confirmDelete()
    {
        if ($this->isBulkDelete) {
            $this->confirmBulkDelete();
        } else {
            $this->confirmIndividualDelete();
        }
    }

    private function confirmIndividualDelete()
    {
        if (! $this->brand || ! isset($this->brand->id)) {
            session()->flash('error', __('messages.brand.messages.not_found'));

            return;
        }

        try {
            // Find the user for deletion
            $brand = Brand::find($this->brand->id);

            if (! $brand) {
                session()->flash('error', __('messages.brand.messages.not_found'));

                return;
            }
            $brand->delete();

            // Hide modal
            $this->hideModal();

            // Emit event to refresh the table
            $this->dispatch('brand-deleted');

            session()->flash('success', __('messages.brand.messages.delete'));
        } catch (\Exception $e) {
            session()->flash('error', __('messages.brand.messages.delete_error'));
        }
    }

    private function confirmBulkDelete()
    {
        if (empty($this->ids)) {
            session()->flash('error', __('messages.brand.messages.no_record_selected'));
            $this->hideModal();

            return;
        }

        try {
            // Get user names before deletion for success message
            $brands = Brand::whereIn('id', $this->ids)->get(['id']);

            // Delete users
            $deletedCount = Brand::whereIn('id', $this->ids)->delete();

            if ($deletedCount > 0) {
                // Hide modal
                $this->hideModal();

                // Emit event to refresh the table
                $this->dispatch('brand-deleted');

                session()->flash('success', __('messages.brand.messages.delete'));
            } else {
                session()->flash('error', __('messages.brand.messages.no_record_deleted'));
            }
        } catch (\Exception $e) {
            session()->flash('error', __('messages.brand.messages.delete_error'));
        }
    }

    public function render()
    {
        return view('livewire.brand.delete');
    }
}
