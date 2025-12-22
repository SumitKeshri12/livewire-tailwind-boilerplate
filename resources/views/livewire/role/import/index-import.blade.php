<div class="min-h-screen bg-gray-50 dark:bg-gray-900" id="kt_content">
    <!--begin::Post-->
    <div class="flex flex-col min-h-screen" id="kt_post">
        <!--begin::Container-->
        <div id="kt_content_container" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- begin::Bulk Upload File -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 mb-8">
                <livewire:dropzone-component :importData="$importData" />
            </div>
            <!-- end::Bulk Upload File-->
            <!--begin::Card-->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                <x-session-message></x-session-message>
                <!--begin::Card body-->
                <div class="p-6">
                    <!--begin::Table-->
                    <livewire:role.import.import-table>
                        <!--end::Table-->
                        <livewire:role.import.import-error-page />
                </div>
                <!--end::Card body-->
            </div>
            <!--end::Card-->
        </div>
        <!--end::Container-->
    </div>
    <!--end::Post-->
</div>
