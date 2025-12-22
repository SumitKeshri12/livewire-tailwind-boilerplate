<div class="w-full m-0 p-0">
    @if(!empty($segments))
        <div class="flex items-center space-x-1 text-sm justify-start m-0 p-0">
            
            <!-- Breadcrumb Items -->
            <div class="flex items-center space-x-1">
                @if(isset($segments['item_1']))
                    @if(isset($segments['item_1_href']) && $segments['item_1_href'] !== '#')
                        <a href="{{ $segments['item_1_href'] }}" 
                           class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition-colors duration-200 font-medium">
                            {{ $segments['item_1'] }}
                        </a>
                    @else
                        <span class="text-gray-900 dark:text-white font-semibold">
                            {{ $segments['item_1'] }}
                        </span>
                    @endif
                @endif
                
                @if(isset($segments['item_2']))
                    <flux:icon.chevron-right class="w-4 h-4 text-gray-400 dark:text-gray-500" />
                    @if(isset($segments['item_2_href']) && $segments['item_2_href'] !== '#')
                        <a href="{{ $segments['item_2_href'] }}" 
                           class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition-colors duration-200 font-medium">
                            {{ $segments['item_2'] }}
                        </a>
                    @else
                        <span class="text-gray-900 dark:text-white font-semibold">
                            {{ $segments['item_2'] }}
                        </span>
                    @endif
                @endif
                
                @if(isset($segments['item_3']))
                    <flux:icon.chevron-right class="w-4 h-4 text-gray-400 dark:text-gray-500" />
                    @if(isset($segments['item_3_href']) && $segments['item_3_href'] !== '#')
                        <a href="{{ $segments['item_3_href'] }}" 
                           class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition-colors duration-200 font-medium">
                            {{ $segments['item_3'] }}
                        </a>
                    @else
                        <span class="text-gray-900 dark:text-white font-semibold">
                            {{ $segments['item_3'] }}
                        </span>
                    @endif
                @endif
            </div>
        </div>
    @endif
</div>
