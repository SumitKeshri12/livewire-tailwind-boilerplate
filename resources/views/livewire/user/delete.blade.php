<div>
    <!-- Delete Confirmation Modal -->
    @if($showModal)
        <x-confirmation-modal 
            modalName="delete-user-confirmation"
            :title="$isBulkDelete ? 'Bulk Delete Users' : 'Delete User'"
            :message="$isBulkDelete ? 'Are you sure you want to delete ' . $selectedUsersCount . ' selected user(s)? This action cannot be undone.' : 'Are you sure you want to delete user \'' . $userName . '\'? This action cannot be undone.'"
            :confirmText="$isBulkDelete ? 'Delete All' : 'Delete User'"
            cancelText="Cancel"
            confirmEvent="confirmDelete"
            variant="danger"
        />
    @endif
</div>
