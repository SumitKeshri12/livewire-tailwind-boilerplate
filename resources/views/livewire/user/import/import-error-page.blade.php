<x-show-info-modal 
    modalTitle="{{ __('messages.user.import_error_title') }}" 
    :eventName="$event"
    :showSaveButton="false"
    :showCancelButton="false"
    cancelButtonText="{{ __('messages.cancel_button_text') }}"
>
    <div class="space-y-4">
        {{-- No Errors Found --}}
        @if(empty($errorLogs))
            <div class="flex items-start gap-3 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                <flux:icon icon="check-circle" class="w-5 h-5 text-green-600 dark:text-green-400 mt-0.5 flex-shrink-0" />
                <div class="flex-1 min-w-0">
                    <flux:heading size="sm" class="font-semibold text-green-900 dark:text-green-100">
                        @lang('messages.user.import_error.no_errors_title')
                    </flux:heading>
                    <flux:text class="text-sm text-green-700 dark:text-green-300 mt-1">
                        {{ __('messages.user.import_error.no_errors') }}
                    </flux:text>
                </div>
            </div>
        @else
            {{-- Error Summary --}}
            <div class="flex items-start gap-3 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                <flux:icon icon="exclamation-triangle" class="w-5 h-5 text-red-600 dark:text-red-400 mt-0.5 flex-shrink-0" />
                <div class="flex-1 min-w-0">
                    <flux:heading size="sm" class="font-semibold text-red-900 dark:text-red-100">
                        @lang('messages.user.import_error.errors_found_title')
                    </flux:heading>
                    <flux:text class="text-sm text-red-700 dark:text-red-300 mt-1">
                        @lang('messages.user.import_error.errors_found_count', ['count' => count($errorLogs)])
                    </flux:text>
                </div>
            </div>

            {{-- Error Details Table --}}
            <div class="overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-900 dark:text-gray-100 uppercase tracking-wider">
                                    {{ __('messages.user.import_error.header_one') }}
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-900 dark:text-gray-100 uppercase tracking-wider">
                                    {{ __('messages.user.import_error.header_two') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($errorLogs as $index => $eLog)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                        @lang('messages.user.import_error.row_label', ['row' => $eLog['row'] ?? ($index + 1)])
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                                        <div class="space-y-2">
                                            @php
                                                $errors = is_array($eLog['error'] ?? null) ? $eLog['error'] : [$eLog['error'] ?? $eLog];
                                            @endphp
                                            @foreach($errors as $error)
                                                <div class="flex items-start gap-2">
                                                    <flux:icon icon="x-circle" class="w-4 h-4 text-red-500 dark:text-red-400 mt-0.5 flex-shrink-0" />
                                                    <span class="text-red-600 dark:text-red-400 break-words">{{ $error }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</x-show-info-modal>