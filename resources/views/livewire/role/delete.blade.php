<div>
    <!-- Delete Confirmation Modal -->
    @if($showModal)
    <x-confirmation-modal modalName="delete-role-confirmation"
        :title="$isBulkDelete ? 'Bulk Delete Roles' : 'Delete Role'"
        :message="$isBulkDelete ? 'Are you sure you want to delete ' . $selectedRolesCount . ' selected role(s)? This action cannot be undone.' : 'Are you sure you want to delete role? This action cannot be undone.'"
        :confirmText="$isBulkDelete ? 'Delete All' : 'Delete Role'" cancelText="Cancel" confirmEvent="confirmDelete"
        variant="danger" />
    @endif
</div>
