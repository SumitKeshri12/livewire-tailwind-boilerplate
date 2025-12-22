@props([
    'id',
    'label',
    'required' => false,
    'disabled' => false,
    'testid' => null,
])

@php
    // Extract the field name from wire:model attribute for error display
    $fieldName = $attributes->get('wire:model') ?? $attributes->get('wire:model.live') ?? $attributes->get('wire:model.defer') ?? $id;
@endphp

<div class="flex-1">
    <flux:field>
        <flux:label for="{{ $id }}">
            {{ $label }}
            @if ($required)
                <span class="text-red-500">*</span>
            @endif
        </flux:label>

        <flux:select
            id="{{ $id }}"
            data-testid="{{ $testid ?? $id }}"
            class="{{ $disabled ? 'cursor-not-allowed bg-gray-100' : 'cursor-pointer' }}"
            :disabled="$disabled"
            {{ $attributes->whereStartsWith('wire:model') }}
        >
            {{-- Parent will inject options --}}
            {{ $slot }}
        </flux:select>

        <flux:error
            name="{{ $fieldName }}"
            data-testid="{{ $testid ? $testid.'_error' : $id.'_error' }}"
        />
    </flux:field>
</div>

