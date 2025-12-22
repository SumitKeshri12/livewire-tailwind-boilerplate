<?php

namespace App\Livewire\User;

use App\Models\User;
use Livewire\Attributes\On;
use Livewire\Component;

class Delete extends Component
{
    public $selectedUserIds = [];

    public $tableName;

    public bool $showModal = false;

    public bool $isBulkDelete = false;

    public int $selectedUsersCount = 0;

    public string $userName = '';

    #[On('delete-confirmation')]
    public function deleteConfirmation($ids, $tableName)
    {
        $this->handleDeleteConfirmation($ids, $tableName);
    }

    #[On('bulk-delete-confirmation')]
    public function bulkDeleteConfirmation($data)
    {
        $ids = $data['ids'] ?? [];
        $tableName = $data['tableName'] ?? '';
        $this->handleDeleteConfirmation($ids, $tableName);
    }

    private function handleDeleteConfirmation($ids, $tableName)
    {
        // Initialize table name and reset selected ids
        $this->tableName = $tableName;
        $this->selectedUserIds = [];

        // Fetch the ids of the roles that match the given IDs and organization ID
        $userIds = User::whereIn('id', $ids)
            ->pluck('id')
            ->toArray();

        if (! empty($userIds)) {
            $this->selectedUserIds = $ids;
            $this->selectedUsersCount = count($this->selectedUserIds);
            $this->isBulkDelete = $this->selectedUsersCount > 1;

            // Get user name for single delete
            if (! $this->isBulkDelete) {
                $user = User::find($ids[0]);
                $this->userName = $user ? $user->name : 'User';
            }

            $this->showModal = true;
        } else {
            // If no roles were found, show an error message
            $this->dispatch('alert', [
                'type' => 'error',
                'message' => __('messages.user.delete.record_not_found'),
            ]);
        }
    }

    public function hideModal()
    {
        $this->showModal = false;
        $this->selectedUserIds = [];
        $this->selectedUsersCount = 0;
        $this->isBulkDelete = false;
        $this->userName = '';
    }

    public function confirmDelete()
    {
        if (! empty($this->selectedUserIds)) {
            // Proceed with deletion of selected user
            User::whereIn('id', $this->selectedUserIds)->delete();

            $this->hideModal();

            session()->flash('success', __('messages.user.messages.delete'));

            return $this->redirect(route('users.index'), navigate: true);
        } else {
            $this->dispatch('alert', type: 'error', message: __('messages.user.messages.record_not_found'));
        }
    }

    /**
     * closeModal
     */
    public function closeModal()
    {
        $this->hideModal();
    }

    public function render()
    {
        return view('livewire.user.delete');
    }
}
