@section('title', $this->title)

<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <!-- Session Messages -->
    <x-session-message></x-session-message>

    <!-- PowerGrid Table with integrated header -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-neutral-200 dark:border-neutral-700 p-6">
        @livewire('brand.table')
        @livewire('brand.show')
    </div>

    <!-- User Delete Component -->
    @livewire('brand.delete')
</div>
