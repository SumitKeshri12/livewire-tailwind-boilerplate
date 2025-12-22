<?php

namespace App\Livewire\Role;

use App\Models\Role;
use Livewire\Attributes\On;
use Livewire\Component;

class Delete extends Component
{
    public bool $showModal = false;

    public $role = null;

    public bool $isBulkDelete = false;

    public array $ids = [];

    public int $selectedRolesCount = 0;

    public string $tableName = '';

    #[On('show-delete-modal')]
    public function showModal($data)
    {
        $id = $data['id'] ?? null;

        if ($id) {
            $this->role = (object) ['id' => $id];
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
        $this->selectedRolesCount = count($this->ids);
        $this->isBulkDelete = true;
        $this->role = null;
        $this->showModal = true;

        // Debug: Log the modal state
        logger()->info('Delete Component - Modal state:', [
            'showModal' => $this->showModal,
            'isBulkDelete' => $this->isBulkDelete,
            'selectedRolesCount' => $this->selectedRolesCount,
            'ids' => $this->ids,
        ]);
    }

    public function hideModal()
    {
        $this->showModal = false;
        $this->role = null;
        $this->isBulkDelete = false;
        $this->ids = [];
        $this->selectedRolesCount = 0;
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
        if (! $this->role || ! isset($this->role->id)) {
            session()->flash('error', __('messages.role.messages.not_found'));

            return;
        }

        try {
            // Find the user for deletion
            $role = Role::find($this->role->id);

            if (! $role) {
                session()->flash('error', __('messages.role.messages.not_found'));

                return;
            }
            $role->delete();

            // Hide modal
            $this->hideModal();

            // Emit event to refresh the table
            $this->dispatch('role-deleted');

            session()->flash('success', __('messages.role.messages.delete'));
        } catch (\Exception $e) {
            session()->flash('error', __('messages.role.messages.delete_error'));
        }
    }

    private function confirmBulkDelete()
    {
        if (empty($this->ids)) {
            session()->flash('error', __('messages.role.messages.no_record_selected'));
            $this->hideModal();

            return;
        }

        try {
            // Get user names before deletion for success message
            $roles = Role::whereIn('id', $this->ids)->get(['id']);

            // Delete users
            $deletedCount = Role::whereIn('id', $this->ids)->delete();

            if ($deletedCount > 0) {
                // Hide modal
                $this->hideModal();

                // Emit event to refresh the table
                $this->dispatch('role-deleted');

                session()->flash('success', __('messages.role.messages.delete'));
            } else {
                session()->flash('error', __('messages.role.messages.no_record_deleted'));
            }
        } catch (\Exception $e) {
            session()->flash('error', __('messages.role.messages.delete_error'));
        }
    }

    public function render()
    {
        return view('livewire.role.delete');
    }
}
