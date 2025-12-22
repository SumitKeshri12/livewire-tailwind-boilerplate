@props([
'label' => '',
'required' => false,
'wireModel' => '',
'error' => '',
'description' => '',
'id' => null,
'checked' => false,
'disabled' => false,
'class' => '',
])

@php
$checkboxId = $id ?? $wireModel . '_checkbox';
@endphp

<flux:field>
    <div class="flex items-center space-x-3">
        <flux:checkbox id="{{ $checkboxId }}" wire:model="{{ $wireModel }}" :checked="$checked" :disabled="$disabled"
            class="{{ $class }}" />

        @if($label)
        <flux:label for="{{ $checkboxId }}" class="cursor-pointer">
            {{ $label }}
            @if($required)
            <span class="text-red-500">*</span>
            @endif
        </flux:label>
        @endif
    </div>

    @if($description)
    <flux:description>{{ $description }}</flux:description>
    @endif

    @if($error)
    <flux:error name="{{ $error }}" />
    @endif
</flux:field>
