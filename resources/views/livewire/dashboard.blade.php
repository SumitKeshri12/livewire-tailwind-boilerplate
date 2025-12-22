<div class="flex flex-col w-full gap-8 p-6">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

        <a href="/role" wire:navigate data-testid="role" class="group flex flex-col items-center justify-center p-4 bg-white dark:bg-zinc-700 rounded-lg shadow-sm hover:shadow-md dark:hover:shadow-lg transition-all duration-300 border border-gray-100 dark:border-zinc-600 hover:border-blue-200 dark:hover:border-blue-400 hover:-translate-y-1">
            <div class="p-2 bg-blue-50 dark:bg-blue-900/30 rounded-full mb-2 group-hover:bg-blue-100 dark:group-hover:bg-blue-900/50 transition-colors duration-300">
                <flux:icon name="users" class="h-6 w-6 text-blue-600 dark:text-blue-400" />
            </div>
            <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-200 group-hover:text-blue-700 dark:group-hover:text-blue-300 transition-colors duration-300">
                @lang('messages.side_menu.role')
            </h3>
        </a>

        <a href="/users" wire:navigate data-testid="user" class="group flex flex-col items-center justify-center p-4 bg-white dark:bg-zinc-700 rounded-lg shadow-sm hover:shadow-md dark:hover:shadow-lg transition-all duration-300 border border-gray-100 dark:border-zinc-600 hover:border-blue-200 dark:hover:border-blue-400 hover:-translate-y-1">
            <div class="p-2 bg-blue-50 dark:bg-blue-900/30 rounded-full mb-2 group-hover:bg-blue-100 dark:group-hover:bg-blue-900/50 transition-colors duration-300">
                <flux:icon name="users" class="h-6 w-6 text-blue-600 dark:text-blue-400" />
            </div>
            <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-200 group-hover:text-blue-700 dark:group-hover:text-blue-300 transition-colors duration-300">
                @lang('messages.side_menu.user')
            </h3>
        </a>

        <a href="/brand" wire:navigate data-testid="brand" class="group flex flex-col items-center justify-center p-4 bg-white dark:bg-zinc-700 rounded-lg shadow-sm hover:shadow-md dark:hover:shadow-lg transition-all duration-300 border border-gray-100 dark:border-zinc-600 hover:border-blue-200 dark:hover:border-blue-400 hover:-translate-y-1">
            <div class="p-2 bg-blue-50 dark:bg-blue-900/30 rounded-full mb-2 group-hover:bg-blue-100 dark:group-hover:bg-blue-900/50 transition-colors duration-300">
                <flux:icon name="users" class="h-6 w-6 text-blue-600 dark:text-blue-400" />
            </div>
            <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-200 group-hover:text-blue-700 dark:group-hover:text-blue-300 transition-colors duration-300">
                @lang('messages.side_menu.brand')
            </h3>
        </a>

        <a href="/sms-template" wire:navigate data-testid="sms-template" class="group flex flex-col items-center justify-center p-4 bg-white dark:bg-zinc-700 rounded-lg shadow-sm hover:shadow-md dark:hover:shadow-lg transition-all duration-300 border border-gray-100 dark:border-zinc-600 hover:border-blue-200 dark:hover:border-blue-400 hover:-translate-y-1">
            <div class="p-2 bg-blue-50 dark:bg-blue-900/30 rounded-full mb-2 group-hover:bg-blue-100 dark:group-hover:bg-blue-900/50 transition-colors duration-300">
                <flux:icon name="users" class="h-6 w-6 text-blue-600 dark:text-blue-400" />
            </div>
            <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-200 group-hover:text-blue-700 dark:group-hover:text-blue-300 transition-colors duration-300">
                @lang('messages.side_menu.sms_template')
            </h3>
        </a>

        <a href="/push-notification-template" wire:navigate data-testid="push-notification-template" class="group flex flex-col items-center justify-center p-4 bg-white dark:bg-zinc-700 rounded-lg shadow-sm hover:shadow-md dark:hover:shadow-lg transition-all duration-300 border border-gray-100 dark:border-zinc-600 hover:border-blue-200 dark:hover:border-blue-400 hover:-translate-y-1">
            <div class="p-2 bg-blue-50 dark:bg-blue-900/30 rounded-full mb-2 group-hover:bg-blue-100 dark:group-hover:bg-blue-900/50 transition-colors duration-300">
                <flux:icon name="users" class="h-6 w-6 text-blue-600 dark:text-blue-400" />
            </div>
            <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-200 group-hover:text-blue-700 dark:group-hover:text-blue-300 transition-colors duration-300">
                @lang('messages.side_menu.push_notification_template')
            </h3>
        </a>

        <a href="/whatsapp-template" wire:navigate data-testid="whatsapp-template" class="group flex flex-col items-center justify-center p-4 bg-white dark:bg-zinc-700 rounded-lg shadow-sm hover:shadow-md dark:hover:shadow-lg transition-all duration-300 border border-gray-100 dark:border-zinc-600 hover:border-blue-200 dark:hover:border-blue-400 hover:-translate-y-1">
            <div class="p-2 bg-blue-50 dark:bg-blue-900/30 rounded-full mb-2 group-hover:bg-blue-100 dark:group-hover:bg-blue-900/50 transition-colors duration-300">
                <flux:icon name="users" class="h-6 w-6 text-blue-600 dark:text-blue-400" />
            </div>
            <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-200 group-hover:text-blue-700 dark:group-hover:text-blue-300 transition-colors duration-300">
                @lang('messages.side_menu.whatsapp_template')
            </h3>
        </a>

    </div>
</div>
