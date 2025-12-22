<div class="flex flex-col flex-1" id="kt_content">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid gap-5">
            <div class="p-0">
                <form wire:submit.prevent="store" class="flex flex-col space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Role Name -->
                        <div>
                            <flux:input label="Role Name" wire:model="name" data-testid="name" id="name" required />
                        </div>

                        <!-- Status Switch -->
                        <div class="mt-3">
                            <flux:field>
                                <flux:label for="status">{{ __('messages.role.create.label_status') }}</flux:label>

                                <flux:switch wire:model.live="status" on-label="Yes" off-label="No" class="text-sm" />
                                <flux:error name="status" />
                            </flux:field>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col md:flex-row gap-2 md:gap-3">
                        <flux:button type="submit" variant="primary" data-testid="submit_button">
                            {{ __('messages.submit_button_text') }}
                        </flux:button>

                        <flux:button @click="hide()">
                            Cancel
                        </flux:button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
