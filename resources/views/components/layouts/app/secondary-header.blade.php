<flux:sidebar sticky collapsible class="bg-zinc-50 dark:bg-zinc-900 border-r border-zinc-200 dark:border-zinc-700"
    x-data="{
        searchTerm: '',
        normalize(value) { return (value || '').toString().toLowerCase(); },
        matches(label) {
            const term = this.normalize(this.searchTerm);
            if (!term) return true;
            return this.normalize(label).includes(term);
        },
        matchesGroup(labels = []) {
            return labels.some(label => this.matches(label));
        }
    }">
    <flux:sidebar.header>
        <flux:sidebar.brand href="#" logo="https://fluxui.dev/img/demo/logo.png"
            logo:dark="https://fluxui.dev/img/demo/dark-mode-logo.png" name="Acme Inc." />
        <flux:sidebar.collapse
            class="in-data-flux-sidebar-on-desktop:not-in-data-flux-sidebar-collapsed-desktop:-mr-2 cursor-pointer" />
    </flux:sidebar.header>


    <flux:input icon="magnifying-glass" type="search" inputmode="search" placeholder="{{ __('messages.side_menus.label_search') }}"
        aria-label="{{ __('messages.side_menus.label_search') }}" x-model="searchTerm" x-on:keydown.escape="searchTerm = ''"
        class="w-full text-sm bg-white/80 dark:bg-zinc-800/70 border-zinc-200 dark:border-zinc-700 focus:ring-2 focus:ring-zinc-300 dark:focus:ring-zinc-600" />

    @php
        // Cache permission checks to avoid multiple database queries
        $canViewRole = Gate::allows('view-role');
        $canViewUser = Gate::allows('view-user');
        $canViewBrand = Gate::allows('view-brand');
    @endphp
    <flux:sidebar.nav>
        <flux:sidebar.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')"
            wire:navigate x-show="matches($el.dataset.label)" data-label="{{ __('messages.side_menus.dashboard') }}">
            {{ __('messages.side_menus.dashboard') }}</flux:sidebar.item>
        @if ($canViewRole)
            <flux:sidebar.item icon="users" :href="route('role.index')" :current="request()->routeIs('role.*')"
                wire:navigate x-show="matches($el.dataset.label)" data-label="{{ __('messages.side_menus.role') }}">
                {{ __('messages.side_menus.role') }}</flux:sidebar.item>
        @endif
        @if ($canViewUser)
            <flux:sidebar.item icon="users" :href="route('users.index')" :current="request()->routeIs('users.index')"
                wire:navigate x-show="matches($el.dataset.label)" data-label="{{ __('messages.side_menus.users') }}">
                {{ __('messages.side_menus.users') }}</flux:sidebar.item>
        @endif

        @if ($canViewBrand)
            <flux:sidebar.item icon="users" :href="route('brand.index')" :current="request()->routeIs('brand.*')"
                wire:navigate x-show="matches($el.dataset.label)" data-label="{{ __('messages.side_menus.brand') }}">
                {{ __('messages.side_menus.brand') }}</flux:sidebar.item>
        @endif

        <flux:sidebar.group expandable :expanded="request()->routeIs('users.imports')"
            heading="{{ __('messages.side_menus.import_history') }}" class="grid"
            x-show="matchesGroup([
                '{{ __('messages.side_menus.import_history') }}',
                '{{ __('messages.side_menus.import_history_users') }}'
            ])">
            <flux:sidebar.item icon="users" :href="route('users.imports')"
                :current="request()->routeIs('users.imports')" wire:navigate x-show="matches($el.dataset.label)"
                data-label="{{ __('messages.side_menus.import_history_users') }}">
                {{ __('messages.side_menus.import_history_users') }}</flux:sidebar.item>
        </flux:sidebar.group>

        <flux:sidebar.group expandable
            :expanded="request()->routeIs('email-format') || request()->routeIs('email-template.*')"
            heading="{{ __('messages.side_menus.templates') }}" class="grid"
            x-show="matchesGroup([
                '{{ __('messages.side_menus.templates') }}',
                '{{ __('messages.side_menus.templates_email_formats') }}',
                '{{ __('messages.side_menus.templates_email_templates') }}'
            ])">
            <flux:sidebar.item icon="envelope" :href="route('email-format')"
                :current="request()->routeIs('email-format')" wire:navigate x-show="matches($el.dataset.label)"
                data-label="{{ __('messages.side_menus.templates_email_formats') }}">
                {{ __('messages.side_menus.templates_email_formats') }}</flux:sidebar.item>
            <flux:sidebar.item icon="envelope" :href="route('email-template.index')"
                :current="request()->routeIs('email-template.*')" wire:navigate x-show="matches($el.dataset.label)"
                data-label="{{ __('messages.side_menus.templates_email_templates') }}">
                {{ __('messages.side_menus.templates_email_templates') }}</flux:sidebar.item>
        </flux:sidebar.group>


        {{-- <flux:sidebar.group expandable heading="Favorites" class="grid">
                <flux:sidebar.item href="#">Marketing site</flux:sidebar.item>
                <flux:sidebar.item href="#">Android app</flux:sidebar.item>
                <flux:sidebar.item href="#">Brand guidelines</flux:sidebar.item>
            </flux:sidebar.group> --}}
    </flux:sidebar.nav>

</flux:sidebar>
<flux:header class="block! bg-white lg:bg-zinc-50 dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-700 px-0">
    <flux:navbar class="lg:hidden w-full">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />
        <flux:spacer />
    </flux:navbar>

    <!-- Header Content with proper alignment -->
    <div class="flex items-center justify-between w-full mt-3">
        <!-- Left-aligned Breadcrumb Section -->
        <div class="flex-1">
            <div class="pl-6" -ml-2>
                <livewire:breadcrumb />
            </div>
        </div>

        <flux:dropdown x-data align="end">
            <flux:button variant="subtle" square class="group cursor-pointer" aria-label="Preferred color scheme"
                data-testid="side_menu_appearance">
                <flux:icon.sun x-show="$flux.appearance === 'light'" variant="mini"
                    class="text-zinc-500 dark:text-white" />
                <flux:icon.moon x-show="$flux.appearance === 'dark'" variant="mini"
                    class="text-zinc-500 dark:text-white" />
                <flux:icon.moon x-show="$flux.appearance === 'system' && $flux.dark" variant="mini" />
                <flux:icon.sun x-show="$flux.appearance === 'system' && ! $flux.dark" variant="mini" />
            </flux:button>
            <flux:menu>
                <flux:menu.item icon="sun" class="cursor-pointer" x-on:click="$flux.appearance = 'light'">
                    {{ __('messages.appearance.light') }}</flux:menu.item>
                <flux:menu.item icon="moon" class="cursor-pointer" x-on:click="$flux.appearance = 'dark'">
                    {{ __('messages.appearance.dark') }}</flux:menu.item>
                <flux:menu.item icon="computer-desktop" class="cursor-pointer" x-on:click="$flux.appearance = 'system'">
                    {{ __('messages.appearance.system') }}
                </flux:menu.item>
            </flux:menu>
        </flux:dropdown>

        @php
            // Cache user instance to avoid multiple auth()->user() calls
            $user = auth()->user();
            $userInitials = $user->initials();
            $canEditPermission = Gate::allows('edit-permission') || $user->role_id == config('constants.roles.admin');
        @endphp

        <livewire:language-switcher :user="$user" />

        <!-- Right-aligned User Menu -->
        <div class="flex-shrink-0">
            <flux:dropdown position="top" align="end">
                <flux:profile class="cursor-pointer" :initials="$userInitials" />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                    <span
                                        class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                                        {{ $userInitials }}
                                    </span>
                                </span>

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <span class="truncate font-semibold">{{ $user->name }}</span>
                                    <span class="truncate text-xs">{{ $user->email }}</span>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    {{-- <flux:menu.item :href="route('settings.profile')" wire:navigate>@lang('messages.side_menus.label_update_profile')</flux:menu.item>
                    <flux:menu.separator /> --}}

                    <flux:menu.item :href="route('settings.password')" data-testid="side_menu_change_password"
                        wire:navigate>@lang('messages.side_menus.label_change_password')</flux:menu.item>
                    <flux:menu.separator />


                    @if ($canEditPermission)
                        <flux:menu.item :href="route('permission')" data-testid="side_menu_permissions" wire:navigate>
                            @lang('messages.side_menus.label_permissions')</flux:menu.item>
                        <flux:menu.separator />
                    @endif

                    <livewire:actions.logout />
                </flux:menu>
            </flux:dropdown>
        </div>
    </div>

</flux:header>
