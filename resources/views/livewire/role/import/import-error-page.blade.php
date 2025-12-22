<div>
    <!-- Modal -->
    <div wire:ignore.self class="fixed inset-0 z-50 overflow-y-auto" id="importErrorShowModal" tabindex="-1" role="dialog" aria-labelledby="importErrorShowModal" aria-hidden="true">
        <!--begin::Modal dialog-->
        <div class="flex min-h-full items-center justify-center p-4">
            <!--begin::Modal content-->
            <div class="relative w-full max-w-2xl bg-white dark:bg-gray-800 rounded-lg shadow-xl">
                <!--begin::Modal header-->
                <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
                    <!--begin::Modal title-->
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">{{ __('messages.role.import_error_title') }}</h2>
                    <!--end::Modal title-->
                    <!--begin::Close-->
                    <button type="button" class="inline-flex items-center justify-center w-8 h-8 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300" data-bs-dismiss="modal">
                        <!--begin::Svg Icon | path: icons/duotune/arrows/arr061.svg-->
                        <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="currentColor" />
                                <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="currentColor" />
                            </svg>
                        </span>
                        <!--end::Svg Icon-->
                    </button>
                    <!--end::Close-->
                </div>
                <!--end::Modal header-->

                <div class="p-6 overflow-auto" style="max-height:500px;">
                    <div class="bg-white dark:bg-gray-800">
                        <div class="p-0">
                            <!--begin::Table wrapper-->
                            <div class="overflow-x-auto">
                                <!--begin::Table-->
                                <div id="kt_subscription_products_table_wrapper">
                                    <div class="overflow-x-auto">
                                        @if ($errorLogs)
                                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700" id="kt_subscription_products_table">
                                            <thead class="bg-gray-50 dark:bg-gray-700">
                                                <tr>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                        <h6>@lang('messages.role.import_error.header_one')</h6>
                                                    </th>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                        <h6>@lang('messages.role.import_error.header_two')</h6>
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                                @foreach($errorLogs as $eLog)
                                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $eLog->row }}</td>
                                                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                                                        @foreach($eLog->error as $err)
                                                        <p> {{ $err}} </p>
                                                        @endforeach
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        @endif
                                    </div>
                                </div>
                                <!--end::Table-->
                            </div>
                            <!--end::Table wrapper-->
                        </div>
                    </div>
                </div>

            </div>
            <!--end::Modal content-->
        </div>
        <!--end::Modal dialog-->
    </div>
</div>
