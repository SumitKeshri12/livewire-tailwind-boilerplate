<div>
    <x-show-info-modal modalTitle="{{ __('messages.brand.show.label_brand') }}" :eventName="$event">
        <div class="w-full">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 mb-4">
                <div class="p-6">
                    <x-show-info key="{{ __('messages.brand.show.details.country_name') }}"
                        value="{{ !is_null($brand) ? $brand->country_name : '-' }}" />
                    <x-show-info key="{{ __('messages.brand.show.details.status') }}"
                        value="{{ $brand?->status ?? '-' }}" />
                    <x-show-info data-testid="show_info_bob" key="{{ __('messages.brand.show.details.bob') }}" value="{{ !is_null($brand) && !is_null($brand->bob)
            ? Carbon\Carbon::parse($brand->bob)->format(config('constants.default_datetime_format'))
            : '-' }}" />
                    <x-show-info data-testid="show_info_start_date"
                        key="{{ __('messages.brand.show.details.start_date') }}" value="{{ !is_null($brand) && !is_null($brand->start_date)
            ? Carbon\Carbon::parse($brand->start_date)->format(config('constants.default_date_format'))
            : '-' }}" />
                    <x-show-info data-testid="show_info_start_time"
                        key="{{ __('messages.brand.show.details.start_time') }}" value="{{ !is_null($brand) && !is_null($brand->start_time)
            ? Carbon\Carbon::parse($brand->start_time)->format(config('constants.default_time_format'))
            : '-' }}" />
                </div>
            </div>
        </div>
    </x-show-info-modal>
</div>
