<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">

    <form wire:submit="store" class="space-y-8">
        <!-- Basic Information Section -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="mb-6">
                <flux:heading size="lg" class="text-gray-900 dark:text-white">
                    {{ __('messages.user.create.basic_information.title') }}</flux:heading>
                <flux:description class="mt-1">
                    {{ __('messages.user.create.basic_information.description') }}</flux:description>
            </div>

            <div class="flex flex-col md:flex-row gap-6 mb-6">
                <div class="flex-1">
                    <flux:field>
                        <flux:label for="name" required>{{ __('messages.user.create.basic_information.full_name') }}
                            <span class="text-red-500">*</span></flux:label>
                        <flux:input data-testid="name" id="name" type="text" wire:model="name"
                            placeholder="{{ __('messages.user.create.basic_information.full_name_placeholder') }}"
                            icon="user" />
                        <flux:error name="name" />
                    </flux:field>
                </div>

                <div class="flex-1">
                    <flux:field>
                        <flux:label for="email" required>
                            {{ __('messages.user.create.basic_information.email_address') }} <span
                                class="text-red-500">*</span></flux:label>
                        <flux:input data-testid="email" id="email" type="email" wire:model="email"
                            placeholder="{{ __('messages.user.create.basic_information.email_placeholder') }}"
                            icon="envelope" />
                        <flux:error name="email" />
                    </flux:field>
                </div>
            </div>

            <div class="flex flex-col md:flex-row gap-6 mb-6">
                <div class="flex-1">
                    <flux:field>
                        <flux:label for="password" required>{{ __('messages.user.create.basic_information.password') }}
                            <span class="text-red-500">*</span></flux:label>
                        <flux:input data-testid="password" id="password" type="password" wire:model="password"
                            placeholder="{{ __('messages.user.create.basic_information.password_placeholder') }}"
                            icon="lock-closed" />
                        <flux:error name="password" />
                    </flux:field>
                </div>

                <div class="flex-1">
                    <flux:field>
                    <x-flux.autocomplete
                        name="role_id"
                        data-testid="role_id"
                        :labeltext="__('messages.user.create.basic_information.role')"
                        :placeholder="__('messages.user.create.basic_information.role_placeholder')"
                        :options="$roles"
                        :selected="$role_id"
                        displayOptions="10"
                        wire:model="role_id"
                        :required="true"
                    />
                        <flux:error name="role_id" />
                    </flux:field>
                </div>
            </div>

            <div class="flex flex-col md:flex-row gap-6">
                <div class="flex-1">
                    <x-flux.date-picker wireModel="dob"
                        for="dob" 
                        data-testid="dob"
                        label="{{ __('messages.user.create.basic_information.date_of_birth') }}" 
                        min="{{ \Carbon\Carbon::now()->subYears(100)->format('Y-m-d') }}"
                        max="{{ \Carbon\Carbon::now()->subYears(13)->format('Y-m-d') }}"
                        :required="true"
                    />
                </div>

                <div class="flex-1">
                    <x-flux.file-upload model="profile_image"
                        data-testid="profile_image"
                        label="{{ __('messages.user.create.basic_information.profile_image') }}"
                        note="Extensions: {{ implode(', ', config('constants.user.file.profile.extensions')) }} | Size: Maximum {{ config('constants.user.file.profile.max_size') }} KB"
                        accept="image/*" :required="true" />
                </div>
            </div>
        </div>

        <!-- Location Information Section -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="mb-6">
                <flux:heading size="lg" class="text-gray-900 dark:text-white">
                    {{ __('messages.user.create.location_information.title') }}</flux:heading>
                <flux:description class="mt-1">
                    {{ __('messages.user.create.location_information.description') }}</flux:description>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- <x-flux.searchable-dropdown
                    name="country_id"
                    data-testid="country_id"
                    :labeltext="__('messages.user.create.location_information.country')"
                    :placeholder="__('messages.user.create.location_information.country_placeholder')"
                    :options="$countries"
                    :selected="$country_id ? [$country_id] : []"
                    :multiple="false"
                    :required="true"
                    :searchable="true"
                    valueColumn="id"
                    labelColumn="name"
                    error="country_id"
                    wire:model="country_id"
                /> --}}
                <x-flux.single-select 
                    id="country_id" 
                    label="{{ __('messages.user.create.location_information.country') }}" 
                    wire:model.live="country_id" 
                    testid="country_id" 
                    required
                >
                    <option value="">{{ __('messages.user.create.location_information.country_placeholder') }}</option>
                    @foreach ($countries as $value)
                        <option value="{{ $value->id }}">{{ $value->name }}</option>
                    @endforeach
                </x-flux.single-select>


                {{-- <x-flux.searchable-dropdown
                    name="state_id"
                    data-testid="state_id"
                    :labeltext="__('messages.user.create.location_information.state')"
                    :placeholder="empty($states) ? __('messages.user.create.location_information.state_placeholder_empty') : __('messages.user.create.location_information.state_placeholder')"
                    :options="$states"
                    :selected="$state_id ? [$state_id] : []"
                    :multiple="false"
                    :required="true"
                    :searchable="false"
                    valueColumn="id"
                    labelColumn="name"
                    :disabled="empty($states)"
                    error="state_id"
                    wire:model="state_id"
                /> --}}
                <x-flux.single-select 
                    id="state_id" 
                    label="{{ __('messages.user.create.location_information.state') }}" 
                    wire:model.live="state_id" 
                    testid="state_id" 
                    required
                    :disabled="empty($states)"
                >
                <option value="">{{ empty($states) ? __('messages.user.create.location_information.state_placeholder_empty') : __('messages.user.create.location_information.state_placeholder') }}</option>
                    @if (!empty($states))
                        @foreach ($states as $state)
                            <option value="{{ $state->id }}">{{ $state->name }}</option>
                        @endforeach
                    @endif
                </x-flux.single-select>

                {{-- <flux:field>
                    <flux:label for="city_id" required>{{ __('messages.user.create.location_information.city') }}
                        <span class="text-red-500">*</span></flux:label>
                    <flux:select data-testid="city_id" id="city_id" wire:model="city_id" :disabled="empty($cities)" class="cursor-pointer">
                        <option value="">
                            {{ empty($cities) ? __('messages.user.create.location_information.city_placeholder_empty') : __('messages.user.create.location_information.city_placeholder') }}
                        </option>
                        @if (!empty($cities))
                            @foreach ($cities as $city)
                                <option value="{{ $city->id ?? ($city['id'] ?? ($city['value'] ?? $city)) }}">
                                    {{ $city->name ?? ($city['name'] ?? ($city['label'] ?? $city)) }}
                                </option>
                            @endforeach
                        @endif
                    </flux:select>
                    <flux:error name="city_id" />
                </flux:field> --}}

                <x-flux.single-select 
                    id="city_id" 
                    label="{{ __('messages.user.create.location_information.city') }}" 
                    wire:model="city_id" 
                    testid="city_id" 
                    required
                    :disabled="empty($cities)"
                >
                    <option value="">{{ empty($cities) ? __('messages.user.create.location_information.city_placeholder_empty') : __('messages.user.create.location_information.city_placeholder') }}</option>
                    @if (!empty($cities))
                        @foreach ($cities as $city)
                            <option value="{{ $city->id ?? ($city['id'] ?? ($city['value'] ?? $city)) }}">
                                {{ $city->name ?? ($city['name'] ?? ($city['label'] ?? $city)) }}
                            </option>
                        @endforeach
                    @endif
                </x-flux.single-select>
            </div>
        </div>

        <!-- Personal Details Section -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="mb-6">
                <flux:heading size="lg" class="text-gray-900 dark:text-white">
                    {{ __('messages.user.create.personal_details.title') }}</flux:heading>
                <flux:description class="mt-1">
                    {{ __('messages.user.create.personal_details.description') }}</flux:description>
            </div>

            <div class="flex flex-col md:flex-row gap-6">
                <div class="flex-1">
                    <flux:field>
                        <flux:label required>{{ __('messages.user.create.personal_details.gender') }} <span
                                class="text-red-500">*</span></flux:label>
                        <div class="flex gap-6">
                            <div class="flex items-center cursor-pointer">
                                <input data-testid="gender" id="gender_F" type="radio" value="F" wire:model="gender"
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 cursor-pointer" />
                                <label for="gender_F" class="ml-2 text-sm text-gray-700 dark:text-gray-300 cursor-pointer">
                                    {{ __('messages.user.create.personal_details.gender_female') }}
                                </label>
                            </div>
                            <div class="flex items-center cursor-pointer">
                                <input data-testid="gender" id="gender_M" type="radio" value="M" wire:model="gender"
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 cursor-pointer" />
                                <label for="gender_M" class="ml-2 text-sm text-gray-700 dark:text-gray-300 cursor-pointer">
                                    {{ __('messages.user.create.personal_details.gender_male') }}
                                </label>
                            </div>
                        </div>
                        <flux:error name="gender" />
                    </flux:field>
                </div>

                <div class="flex-1" x-data="{ status: @entangle('status') }">
                    <flux:field>
                        <flux:label for="status_switch">{{ __('messages.user.create.personal_details.status') }} <span
                                class="text-red-500">*</span></flux:label>
                        <div class="flex items-center gap-3">
                            <flux:switch 
                                id="status_switch" 
                                data-testid="status"
                                class="cursor-pointer"
                                x-bind:checked="status === 'Y'"
                                x-on:change="$wire.set('status', $event.target.checked ? 'Y' : 'N')"
                            />
                            <label for="status_switch" class="text-sm font-medium text-gray-700 dark:text-gray-300 cursor-pointer" x-text="status === 'Y' ? 'Active' : 'Inactive'">
                            </label>
                        </div>
                        <flux:error name="status" />
                    </flux:field>
                </div>
            </div>

            <div class="mt-6">
                <flux:field>
                    <flux:label for="description" required>
                        {{ __('messages.user.create.personal_details.description') }} <span
                            class="text-red-500">*</span></flux:label>
                    <x-flux.editor wireModel="description"
                        data-testid="description"
                        placeholder="{{ __('messages.user.create.personal_details.description_placeholder') }}"
                        height="300px" toolbar="full" error="description" />
                </flux:field>
            </div>

            <div class="mt-6">
                <flux:field>
                    <flux:label for="comments">
                        {{ __('messages.user.create.personal_details.comments') }}</flux:label>
                    <flux:textarea 
                        data-testid="comments" 
                        id="comments" 
                        wire:model="comments"
                        placeholder="{{ __('messages.user.create.personal_details.comments_placeholder') }}"
                        rows="4"
                        class="resize-none"
                    />
                    <flux:error name="comments" />
                </flux:field>
            </div>
        </div>

        <!-- Additional Information Section -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="mb-6">
                <flux:heading size="lg" class="text-gray-900 dark:text-white">
                    {{ __('messages.user.create.additional_information.title') }}</flux:heading>
                <flux:description class="mt-1">
                    {{ __('messages.user.create.additional_information.description') }}</flux:description>
            </div>

            <div class="flex flex-col md:flex-row gap-6 mb-6">
                <div class="flex-1">
                    <x-flux.searchable-dropdown
                        name="hobbies"
                        data-testid="hobbies"
                        :labeltext="__('messages.user.create.additional_information.hobbies')"
                        :placeholder="__('messages.user.create.additional_information.hobbies_placeholder')"
                        :options="config('constants.user.hobbies')"
                        :selected="$hobbies"    
                        :multiple="true"
                        :maxSelection="3"
                        valueColumn="id"
                        labelColumn="name"
                        error="hobbies"
                        wire:model="hobbies"
                    />
                </div>

                <div class="flex-1">
                    <x-flux.chips
                        wire-model="skills"
                        data-testid="skills"
                        for="skills"
                        label="{{ __('messages.user.create.additional_information.skills') }}"
                        :required="false"
                        :disabled="false"
                        :max-chips="8"
                    />
                </div>
            </div>


            <div class="flex flex-col md:flex-row gap-6 mb-6">

                {{-- <div class="flex-1">
                    <flux:field>
                        <flux:label for="languages">Languages</flux:label>
                        <flux:dropdown>
                            <flux:button icon:trailing="chevron-down" class="cursor-pointer">
                                @if (count($languages ?? []) > 0)
                                    {{ count($languages) }} languages selected
                                @else
                                    Select languages...
                                @endif
                            </flux:button>
                            <flux:menu>
                                @foreach (config('constants.user.languages') as $value => $label)
                                    <flux:menu.checkbox 
                                        keep-open
                                        wire:click="toggleArrayItem('languages', '{{ $value }}')"
                                        :checked="in_array('{{ $value }}', $languages ?? [])"
                                        class="cursor-pointer"
                                    >
                                        {{ $label }}
                                    </flux:menu.checkbox>
                                @endforeach
                                <flux:menu.separator />
                            </flux:menu>
                        </flux:dropdown>
                        <flux:description>Select the languages you speak</flux:description>
                        <flux:error name="languages" />
                    </flux:field>
                </div> --}}

                <x-flux.multi-select id="languages" model="languages" label="Languages">
                    @foreach (config('constants.user.languages') as $value => $label)
                        <label class="flex items-center px-3 py-2 hover:bg-gray-50 cursor-pointer transition">
                            <input type="checkbox" value="{{ $value }}" wire:model.live="languages"
                                class="mr-2 h-4 w-4 text-black-600 border-gray-300 rounded
                                focus:ring-black-500 focus:ring-2 cursor-pointer">
                            <span class="text-gray-700 text-sm">{{ $label }}</span>
                        </label>
                    @endforeach
                </x-flux.multi-select>
                <div class="flex-1">
                    <!-- Empty div for layout balance -->
                </div>
            </div>

            

            <div class="flex flex-col md:flex-row gap-6">
                <div class="flex-1">
                    <flux:field>
                        <flux:label for="bg_color">
                            {{ __('messages.user.create.additional_information.background_color') }}</flux:label>
                        <div class="w-12 h-10">
                            <flux:input data-testid="bg_color" id="bg_color" type="color" wire:model="bg_color"
                                data-colorpick-eyedropper="false"
                                class="cursor-pointer"
                                style="appearance: none; -webkit-appearance: none; -moz-appearance: none; cursor: pointer !important;" />
                        </div>
                        <flux:error name="bg_color" />
                    </flux:field>
                </div>

                <div class="flex-1">
                    <x-flux.autocomplete 
                        name="timezone"
                        data-testid="timezone"
                        labeltext="{{ __('messages.user.create.additional_information.timezone') }}"
                        placeholder="{{ __('messages.user.create.additional_information.timezone_placeholder') }}"
                        :options="config('constants.timezones')"
                        displayOptions="10"
                        wire:model="timezone"
                        :required="true"
                    /> <!-- displayOptions = Number of options to display initially, or 'all' for all options -->
                    <flux:error name="timezone" />
                </div>
            </div>
        </div>

        <!-- Event Information Section -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="mb-6">
                <flux:heading size="lg" class="text-gray-900 dark:text-white">
                    {{ __('messages.user.create.event_information.title') }}</flux:heading>
                <flux:description class="mt-1">
                    {{ __('messages.user.create.event_information.description') }}</flux:description>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <x-flux.date-picker wireModel="event_date" 
                data-testid="event_date"
                label="{{ __('messages.user.create.event_information.event_date') }}" 
                min="{{ \Carbon\Carbon::now()->format('Y-m-d') }}"
                max="{{ \Carbon\Carbon::now()->addDays(30)->format('Y-m-d') }}" 
                :required="false"
                />

                <x-flux.date-time-picker wireModel="event_datetime" for="event_datetime" 
                data-testid="event_datetime"
                label="{{ __('messages.user.create.event_information.event_datetime') }}" 
                min="{{ \Carbon\Carbon::parse('2025-02-15')->format('Y-m-d\TH:i') }}"
                max="{{ \Carbon\Carbon::parse('2026-02-15')->addDays(365)->format('Y-m-d\TH:i') }}"
                :required="false"
                />

                <x-flux.time-picker wireModel="event_time" 
                for="event_time"  
                data-testid="event_time"
                label="{{ __('messages.user.create.event_information.event_time') }}"
                :required="false"
                />
            </div>
        </div>

        <!-- Document Upload Section -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="mb-6">
                <flux:heading size="lg" class="text-gray-900 dark:text-white">
                    {{ __('messages.user.create.document_upload.title') }}</flux:heading>
                <flux:description class="mt-1">
                    {{ __('messages.user.create.document_upload.description') }}</flux:description>
            </div>

            <div class="w-full">
                <x-flux.file-upload model="document_image"
                    data-testid="document_image"
                    label="{{ __('messages.user.create.document_upload.document') }}"
                    note="Extensions: {{ implode(', ', config('constants.user.file.document.extensions')) }} | Size: Maximum {{ config('constants.user.file.document.max_size') }} KB | Max Files: {{ config('constants.user.file.document.max_files') }}"
                    accept=".{{ implode(',.', config('constants.user.file.document.extensions')) }}"
                    :multiple="true"
                    :maxFiles="config('constants.user.file.document.max_files')"
                    :required="true" 
                    :existingDocuments="[]"
                    />
                    <flux:error name="document_image" />
            </div>
        </div>

        <!-- Terms and Conditions Section -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="mb-6">
                <flux:heading size="lg" class="text-gray-900 dark:text-white">
                    {{ __('messages.user.create.terms_and_conditions.title') }}</flux:heading>
                <flux:description class="mt-1">
                    {{ __('messages.user.create.terms_and_conditions.description') }}</flux:description>
            </div>

            <div class="space-y-4">
                <!-- Terms and Conditions -->
                <flux:field>
                    <div class="flex items-start gap-3">
                        <flux:checkbox data-testid="terms_accepted" id="terms_accepted" wire:model="terms_accepted" />
                        <div class="flex-1">
                            <flux:label for="terms_accepted" class="text-sm font-medium cursor-pointer">
                                {{ __('messages.user.create.terms_and_conditions.i_accept') }} <a href="#" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 font-semibold"> {{ __('messages.user.create.terms_and_conditions.terms_and_conditions') }}</a> <span class="text-red-500">*</span>
                            </flux:label>
                            <flux:error name="terms_accepted" />
                        </div>
                    </div>
                </flux:field>

                <!-- Privacy Policy -->
                <flux:field>
                    <div class="flex items-start gap-3">
                        <flux:checkbox data-testid="privacy_policy_accepted" id="privacy_policy_accepted" wire:model="privacy_policy_accepted" />
                        <div class="flex-1">
                            <flux:label for="privacy_policy_accepted" class="text-sm font-medium cursor-pointer">
                                {{ __('messages.user.create.terms_and_conditions.i_accept') }} <a href="#" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 font-semibold"> {{ __('messages.user.create.terms_and_conditions.privacy_policy') }}</a> <span class="text-red-500">*</span>
                            </flux:label>
                            <flux:error name="privacy_policy_accepted" />
                        </div>
                    </div>
                </flux:field>

                <!-- Data Processing Consent -->
                <flux:field>
                    <div class="flex items-start gap-3">
                        <flux:checkbox data-testid="data_processing_consent" id="data_processing_consent" wire:model="data_processing_consent" />
                        <div class="flex-1">
                            <flux:label for="data_processing_consent" class="text-sm font-medium cursor-pointer">
                                {{ __('messages.user.create.terms_and_conditions.i_consent_to') }} <a href="#" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 font-semibold">{{ __('messages.user.create.terms_and_conditions.data_processing') }}</a> <span class="text-red-500">*</span>
                            </flux:label>
                            <flux:error name="data_processing_consent" />
                        </div>
                    </div>
                </flux:field>

                <!-- Marketing Consent (Optional) -->
                <flux:field>
                    <div class="flex items-start gap-3">
                        <flux:checkbox data-testid="marketing_consent" id="marketing_consent" wire:model="marketing_consent" />
                        <div class="flex-1">
                            <flux:label for="marketing_consent" class="text-sm font-medium cursor-pointer">
                                {{ __('messages.user.create.terms_and_conditions.i_agree_to_receive') }} <a href="#" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 font-semibold">{{ __('messages.user.create.terms_and_conditions.marketing_communications') }}</a>
                            </flux:label>
                            <flux:error name="marketing_consent" />
                        </div>
                    </div>
                </flux:field>
            </div>
        </div>

        <!-- Action Buttons -->

        <div class="flex items-center justify-top gap-3 mt-12">

            <flux:button type="submit" variant="primary" data-testid="submit-button" class="cursor-pointer"
                wire:loading.attr="disabled" wire:target="store">
                {{ __('messages.user.create.actions.create_user') }}
            </flux:button>

            <flux:button type="button" data-testid="cancel-button" variant="outline" href="/users" wire:navigate>
                {{ __('messages.user.create.actions.cancel') }}
            </flux:button>
        </div>
    </form>
</div>
