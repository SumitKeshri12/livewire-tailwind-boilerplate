<div>
    <!-- Delete Confirmation Modal -->
    @if($showModal)
    <x-confirmation-modal modalName="delete-brand-confirmation"
        :title="$isBulkDelete ? 'Bulk Delete Brands' : 'Delete Brand'"
        :message="$isBulkDelete ? 'Are you sure you want to delete ' . $selectedBrandsCount . ' selected brand(s)? This action cannot be undone.' : 'Are you sure you want to delete brand? This action cannot be undone.'"
        :confirmText="$isBulkDelete ? 'Delete All' : 'Delete Brand'" cancelText="Cancel" confirmEvent="confirmDelete"
        variant="danger" />
    @endif
</div>
