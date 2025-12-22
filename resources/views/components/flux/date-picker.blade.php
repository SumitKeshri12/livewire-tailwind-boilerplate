@props([
'wireModel' => '',
'label' => '',
'min' => '',
'max' => '',
'required' => false,
'for' => ''
])
<flux:field>
    <flux:label for="{{ $for }}" :required="$required">{{ $label }}@if($required) <span class="text-red-500">*</span>@endif
    </flux:label>
    <flux:input 
        id="{{ $for }}" 
        data-testid="{{ $wireModel }}" 
        type="date" 
        wire:model="{{ $wireModel }}" 
        min="{{ $min }}" 
        max="{{ $max }}" 
        class="cursor-pointer" 
        style="cursor: pointer !important;" 
    />
    <style>
        input[type="date"]::-webkit-calendar-picker-indicator {
            cursor: pointer !important;
        }

        input[type="date"]::-webkit-inner-spin-button,
        input[type="date"]::-webkit-outer-spin-button {
            cursor: pointer !important;
        }
    </style>
    <flux:error name="{{ $wireModel }}" data-testid="{{ $wireModel }}_error" />
</flux:field>
