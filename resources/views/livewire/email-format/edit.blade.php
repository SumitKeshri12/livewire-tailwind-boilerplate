<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <x-session-message></x-session-message>

    <form wire:submit.prevent="store" class="space-y-6">
        @foreach($emailFormats as $i => $emailFormat)
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-neutral-200 dark:border-neutral-700 p-6">
            <flux:field>
                <flux:label for="body_{{ $i }}" required>{{ $emailFormat['label'] }}</flux:label>
                <x-flux.editor wireModel="emailFormats.{{ $i }}.body" id="body_{{ $i }}" height="300px" />
                <flux:error name="emailFormats.{{ $i }}.body" />
            </flux:field>
        </div>
        @endforeach

        <!-- Action Buttons -->
        <div class="flex flex-col md:flex-row gap-2">
            <flux:button type="submit" variant="primary" wire:loading.attr="disabled">
                {{ __('messages.submit_button_text') }}
            </flux:button>
        </div>
    </form>
</div>

