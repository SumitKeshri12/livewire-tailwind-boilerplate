<div id="kt_content" class="flex flex-col flex-1">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid gap-5">
            @if ($errors->any())
            @foreach ($errors->all() as $error)
            <div>{{$error}}</div>
            @endforeach
            @endif
            <div class="p-0">
                <form wire:submit.prevent="store" class="space-y-5">
                    <!-- Card: Form Fields -->
                    <div class="bg-white dark:bg-gray-800 shadow rounded-xl p-6 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Country Select -->
                            <div>
                                <flux:select label="{{ __('messages.brand.create.label_countries') }}"
                                    wire:model="country_id"
                                    placeholder="{{ __('messages.brand.create.label_countries') }}...">
                                    @if (!empty($countries))
                                    @foreach ($countries as $value)
                                    <flux:select.option value="{{ $value->id }}">
                                        {{ $value->name }}
                                    </flux:select.option>
                                    @endforeach
                                    @endif
                                </flux:select>
                            </div>

                            <!-- Status Switch -->
                            <div>
                                <flux:field>
                                    <flux:label for="status">{{ __('messages.role.create.label_status') }}</flux:label>

                                    <flux:switch variant="inline" wire:model.live="status" on-label="Yes" off-label="No"
                                        class="text-sm" />
                                    <flux:error name="status" />
                                </flux:field>
                            </div>

                            <!-- BOB Date -->
                            <div>
                                <flux:field>
                                    <flux:label for="bob" required>{{ __('messages.brand.create.label_bob') }}
                                    </flux:label>
                                    <flux:input id="bob" type="date" wire:model="bob" />
                                    <flux:error name="bob" />
                                </flux:field>
                            </div>

                            <!-- Start Date -->
                            <div>
                                <flux:field>
                                    <flux:label for="start_date" required>{{
                                        __('messages.brand.create.label_start_date') }}</flux:label>
                                    <flux:input id="start_date" type="date" wire:model="start_date" />
                                    <flux:error name="start_date" />
                                </flux:field>
                            </div>
                        </div>
                    </div>

                    <!-- Card: Accordion for Adds -->
                    <div
                        class="bg-white flex flex-row justify-between dark:bg-gray-800 shadow rounded-xl p-6 space-y-4">
                        <h3 class="font-bold text-lg mb-4">Add New Entries</h3>
                        <flux:button icon:trailing="plus" wire:click.prevent="add" variant="primary"
                            data-testid="plus_button" />
                    </div>
                    <div class="bg-white dark:bg-gray-800 shadow rounded-xl p-6 space-y-4">
                        @if(!empty($adds))
                        <div class="space-y-4">
                            @foreach($adds as $index => $add)
                            @php
                            $hasError = collect($errors->getBag('default')->keys())->contains(fn($key) =>
                            str_starts_with($key, "adds.$index"));
                            $showAccordion = $isEdit || $hasError || $index === 0;
                            @endphp

                            <div x-data="{ open: {{ $showAccordion ? 'true' : 'false' }} }"
                                class="border rounded shadow-sm">
                                <!-- Accordion Header -->
                                <button type="button" @click="open = !open"
                                    class="flex justify-between items-center w-full px-4 py-2 font-semibold text-gray-800 dark:text-gray-100 bg-gray-100 dark:bg-gray-700 rounded-t hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                                    <span>Add New {{ $index + 1 }}</span>
                                    <span class="flex items-center gap-2">
                                        @if($index > 0)
                                        <flux:icon.trash variant="solid"
                                            wire:click.prevent="remove({{ $index }}, {{ $add['id'] ?? 0 }})"
                                            class="w-5 h-5" />
                                        @endif

                                        <!-- Chevron Icon with rotation -->
                                        <flux:icon.chevron-down :class="{ 'rotate-180': open }"
                                            class="transition-transform duration-200" />
                                    </span>
                                </button>


                                <!-- Accordion Body -->
                                <div x-show="open" x-transition
                                    class="px-4 py-4 border-t border-gray-200 dark:border-gray-700">
                                    <div class="grid grid-cols-1 gap-4">
                                        <input type="hidden" name="add_id[]" value="{{ $add['id'] }}">

                                        <!-- Description -->
                                        <flux:textarea rows="3" label="Description"
                                            wire:model="adds.{{$index}}.description" id="description_{{$index}}"
                                            data-testid="adds.{{$index}}.description" placeholder="Description" />

                                        <!-- Status Radio -->
                                        <flux:radio.group wire:model="adds.{{$index}}.status" label="Status">
                                            <flux:radio value="{{ config('constants.brand.status.key.active') }}"
                                                label="{{ config('constants.brand.status.value.active') }}" />
                                            <flux:radio value="{{ config('constants.brand.status.key.active') }}"
                                                label="{{ config('constants.brand.status.value.inactive') }}" />
                                        </flux:radio.group>

                                        <!-- Brand Image Upload -->
                                        <div class="w-full">
                                            <x-flux.document-upload
                                                label="{{ __('messages.brand.create.label_brand_image') }}"
                                                wireModel="adds.{{$index}}.brand_image" id="document_image_{{$index}}"
                                                accept=".jpg,.png,.jpeg,.pdf" maxSize="2048" error="document_image" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col md:flex-row gap-2 mt-4">
                        <flux:button type="submit" variant="primary" data-testid="submit_button">
                            {{ __('messages.submit_button_text') }}
                        </flux:button>

                        <flux:button href="{{ route('brand.index') }}">
                            Cancel
                        </flux:button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
