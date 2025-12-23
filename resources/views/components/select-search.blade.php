@props([
'name',
'id' => null,
'label' => '',
'required' => false,
'options' => [],
'value' => null,
'placeholder' => 'Select an option',
'searchPlaceholder' => 'Search...',
'otherOptionValue' => null,
'otherOptionLabel' => 'Others(Please Specify)',
'showOtherInput' => false,
'otherInputName' => null,
'otherInputLabel' => 'Please specify',
'otherInputValue' => null,
'error' => null,
])

@php
$componentId = $id ?? $name . '_' . uniqid();
$otherInputId = $otherInputName ? ($otherInputName . '_' . uniqid()) : null;
@endphp

<div class="select-search-wrapper" style="margin-bottom: 20px;">
    @if($label)
    <label for="{{ $componentId }}" style="display: block; margin-bottom: 8px; font-weight: 500;">
        {{ $label }}
        @if($required)
        <span style="color: red;">*</span>
        @endif
    </label>
    @endif

    <div class="select-search-container" data-select-id="{{ $componentId }}">
        <input type="hidden" name="{{ $name }}" id="{{ $componentId }}" value="{{ old($name, $value) }}" @if($required)
            required @endif>

        <div class="select-search-display" id="{{ $componentId }}_display">
            <span class="select-search-text">{{ $placeholder }}</span>
            <span class="select-search-arrow">▼</span>
        </div>

        <div class="select-search-dropdown" id="{{ $componentId }}_dropdown" style="display: none;">
            <div class="select-search-input-wrapper">
                <input type="text" class="select-search-input" id="{{ $componentId }}_search"
                    placeholder="{{ $searchPlaceholder }}" autocomplete="off">
            </div>
            <div class="select-search-options" id="{{ $componentId }}_options">
                @foreach($options as $option)
                @php
                $optionValue = is_array($option) ? ($option['value'] ?? $option['name'] ?? '') : $option;
                $optionLabel = is_array($option) ? ($option['label'] ?? $option['name'] ?? $optionValue) : $option;
                $isSelected = old($name, $value) == $optionValue;
                @endphp
                <div class="select-search-option" data-value="{{ $optionValue }}" data-label="{{ $optionLabel }}"
                    @if($isSelected) data-selected="true" @endif>
                    {{ $optionLabel }}
                </div>
                @endforeach
                @if($otherOptionValue !== null)
                <div class="select-search-option" data-value="{{ $otherOptionValue }}"
                    data-label="{{ $otherOptionLabel }}" data-is-other="true">
                    {{ $otherOptionLabel }}
                </div>
                @endif
            </div>
        </div>
    </div>

    @if($error)
    <span style="color: red; font-size: 14px; margin-top: 5px; display: block;">{{ $error }}</span>
    @else
    @error($name)
    <span style="color: red; font-size: 14px; margin-top: 5px; display: block;">{{ $message }}</span>
    @enderror
    @endif

    @php
    $currentValue = old($name, $value);
    $shouldShowOther = $showOtherInput && $otherInputName && ($currentValue === $otherOptionValue || ($currentValue &&
    !in_array($currentValue, array_map(function($opt) {
    return is_array($opt) ? ($opt['value'] ?? $opt['name'] ?? '') : $opt;
    }, $options)) && $currentValue !== $otherOptionValue));
    @endphp

    @if($showOtherInput && $otherInputName)
    <div class="select-search-other-wrapper" id="{{ $componentId }}_other_wrapper"
        style="margin-top: 15px; {{ $shouldShowOther ? 'display: block;' : 'display: none;' }}">
        <label for="{{ $otherInputId }}" style="display: block; margin-bottom: 8px; font-weight: 500;">
            {{ $otherInputLabel }}
            @if($required)
            <span style="color: red;">*</span>
            @endif
        </label>
        <input type="text" name="{{ $otherInputName }}" id="{{ $otherInputId }}"
            value="{{ old($otherInputName, $otherInputValue) }}" placeholder="Enter your {{ strtolower($label) }}"
            class="select-search-other-input"
            style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;">
        @error($otherInputName)
        <span style="color: red; font-size: 14px; margin-top: 5px; display: block;">{{ $message }}</span>
        @enderror
    </div>
    @endif
</div>