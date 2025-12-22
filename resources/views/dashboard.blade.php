<x-layouts.app :title="__('Dashboard')">
    <div class="flex flex-col w-full gap-8 p-6">
        <!-- Cards Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <!-- Role Card -->
            <a href="/role" wire:navigate data-testid="role" class="group flex flex-col items-center justify-center p-4 bg-white dark:bg-zinc-700 rounded-lg shadow-sm hover:shadow-md dark:hover:shadow-lg transition-all duration-300 border border-gray-100 dark:border-zinc-600 hover:border-primary-200 dark:hover:border-primary-400 hover:-translate-y-1">
                <div class="p-2 bg-primary-50 dark:bg-primary-900/30 rounded-full mb-2 group-hover:bg-primary-100 dark:group-hover:bg-primary-900/50 transition-colors duration-300">
                    <flux:icon name="shield-check" class="h-6 w-6 text-primary-600 dark:text-primary-400" />
                </div>
                <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-200 group-hover:text-primary-700 dark:group-hover:text-primary-300 transition-colors duration-300">
                    @lang('messages.side_menu.role')
                </h3>
            </a>

            <!-- User Card -->
            <a href="/users" wire:navigate data-testid="user" class="group flex flex-col items-center justify-center p-4 bg-white dark:bg-zinc-700 rounded-lg shadow-sm hover:shadow-md dark:hover:shadow-lg transition-all duration-300 border border-gray-100 dark:border-zinc-600 hover:border-blue-200 dark:hover:border-blue-400 hover:-translate-y-1">
                <div class="p-2 bg-blue-50 dark:bg-blue-900/30 rounded-full mb-2 group-hover:bg-blue-100 dark:group-hover:bg-blue-900/50 transition-colors duration-300">
                    <flux:icon name="users" class="h-6 w-6 text-blue-600 dark:text-blue-400" />
                </div>
                <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-200 group-hover:text-blue-700 dark:group-hover:text-blue-300 transition-colors duration-300">
                    @lang('messages.side_menu.user')
                </h3>
            </a>

            <!-- Brand Card -->
            <a href="/brand" wire:navigate data-testid="brand" class="group flex flex-col items-center justify-center p-4 bg-white dark:bg-zinc-700 rounded-lg shadow-sm hover:shadow-md dark:hover:shadow-lg transition-all duration-300 border border-gray-100 dark:border-zinc-600 hover:border-purple-200 dark:hover:border-purple-400 hover:-translate-y-1">
                <div class="p-2 bg-purple-50 dark:bg-purple-900/30 rounded-full mb-2 group-hover:bg-purple-100 dark:group-hover:bg-purple-900/50 transition-colors duration-300">
                    <flux:icon name="tag" class="h-6 w-6 text-purple-600 dark:text-purple-400" />
                </div>
                <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-200 group-hover:text-purple-700 dark:group-hover:text-purple-300 transition-colors duration-300">
                    @lang('messages.side_menu.brand')
                </h3>
            </a>

            <!-- SMS Template Card -->
            <a href="/sms-template" wire:navigate data-testid="sms-template" class="group flex flex-col items-center justify-center p-4 bg-white dark:bg-zinc-700 rounded-lg shadow-sm hover:shadow-md dark:hover:shadow-lg transition-all duration-300 border border-gray-100 dark:border-zinc-600 hover:border-green-200 dark:hover:border-green-400 hover:-translate-y-1">
                <div class="p-2 bg-green-50 dark:bg-green-900/30 rounded-full mb-2 group-hover:bg-green-100 dark:group-hover:bg-green-900/50 transition-colors duration-300">
                    <flux:icon name="chat-bubble-left-right" class="h-6 w-6 text-green-600 dark:text-green-400" />
                </div>
                <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-200 group-hover:text-green-700 dark:group-hover:text-green-300 transition-colors duration-300">
                    @lang('messages.side_menu.sms_template')
                </h3>
            </a>

            <!-- Push Notification Template Card -->
            <a href="/push-notification-template" wire:navigate data-testid="push-notification-template" class="group flex flex-col items-center justify-center p-4 bg-white dark:bg-zinc-700 rounded-lg shadow-sm hover:shadow-md dark:hover:shadow-lg transition-all duration-300 border border-gray-100 dark:border-zinc-600 hover:border-orange-200 dark:hover:border-orange-400 hover:-translate-y-1">
                <div class="p-2 bg-orange-50 dark:bg-orange-900/30 rounded-full mb-2 group-hover:bg-orange-100 dark:group-hover:bg-orange-900/50 transition-colors duration-300">
                    <flux:icon name="bell" class="h-6 w-6 text-orange-600 dark:text-orange-400" />
                </div>
                <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-200 group-hover:text-orange-700 dark:group-hover:text-orange-300 transition-colors duration-300">
                    @lang('messages.side_menu.push_notification_template')
                </h3>
            </a>

            <!-- WhatsApp Template Card -->
            <a href="/whatsapp-template" wire:navigate data-testid="whatsapp-template" class="group flex flex-col items-center justify-center p-4 bg-white dark:bg-zinc-700 rounded-lg shadow-sm hover:shadow-md dark:hover:shadow-lg transition-all duration-300 border border-gray-100 dark:border-zinc-600 hover:border-emerald-200 dark:hover:border-emerald-400 hover:-translate-y-1">
                <div class="p-2 bg-emerald-50 dark:bg-emerald-900/30 rounded-full mb-2 group-hover:bg-emerald-100 dark:group-hover:bg-emerald-900/50 transition-colors duration-300">
                    <flux:icon name="chat-bubble-oval-left-ellipsis" class="h-6 w-6 text-emerald-600 dark:text-emerald-400" />
                </div>
                <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-200 group-hover:text-emerald-700 dark:group-hover:text-emerald-300 transition-colors duration-300">
                    @lang('messages.side_menu.whatsapp_template')
                </h3>
            </a>
        </div>
    </div>
</x-layouts.app>
