<div class="w-full">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 mb-4">
        <div class="p-6">
            <x-show-info key="{{ __('messages.role.show.details.name') }}" value="{{ $role?->name ?? '-' }}" />
            <x-show-info key="{{ __('messages.role.show.details.status') }}" value="{{ $role?->status ?? '-' }}" />
        </div>
    </div>
</div>
