<div>
    @if($show)
        <div
            x-data="{ show: true }"
            x-init="setTimeout(() => show = false, 5000)"
            x-show="show"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform scale-95 translate-x-full"
            x-transition:enter-end="opacity-100 transform scale-100 translate-x-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 transform scale-100 translate-x-0"
            x-transition:leave-end="opacity-0 transform scale-95 translate-x-full"
            class="fixed top-4 right-4 z-50 max-w-sm w-full"
        >
            <div class="flex items-center justify-between rounded-lg border px-4 py-3 shadow-lg
                @if($type === 'error') bg-red-50 border-red-200 text-red-800
                @elseif($type === 'success') bg-green-50 border-green-200 text-green-800
                @elseif($type === 'warning') bg-yellow-50 border-yellow-200 text-yellow-800
                @else bg-blue-50 border-blue-200 text-blue-800
                @endif"
                role="alert"
            >
                <div class="flex items-center">
                    @if($type === 'error')
                        <flux:icon.x-circle class="w-5 h-5 mr-2" />
                    @elseif($type === 'success')
                        <flux:icon.check-circle class="w-5 h-5 mr-2" />
                    @elseif($type === 'warning')
                        <flux:icon.exclamation-triangle class="w-5 h-5 mr-2" />
                    @else
                        <flux:icon.information-circle class="w-5 h-5 mr-2" />
                    @endif
                    <flux:text variant="subtle" class="text-sm font-medium">
                        {{ $message }}
                    </flux:text>
                </div>
                <flux:button 
                    variant="ghost" 
                    size="sm"
                    wire:click="hideMessage"
                    class="ml-3 inline-flex h-6 w-6 items-center justify-center rounded-md hover:bg-black hover:bg-opacity-10 focus:outline-none"
                >
                    <flux:icon.x-mark class="w-4 h-4" />
                </flux:button>
            </div>
        </div>
    @endif
</div>
