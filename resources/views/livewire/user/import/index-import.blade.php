<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <!-- Session Messages -->
    <x-session-message></x-session-message>

    <!-- Bulk Upload File Section -->
    <livewire:dropzone-component :importData="$importData" />
    
    <!-- Import Table and Error Page Section -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <!-- Import Table -->
        <livewire:user.import.import-table />
        
        <!-- Import Error Page -->
        <livewire:user.import.import-error-page />
    </div>
</div>