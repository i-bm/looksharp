@php
$prefix = $prefix ?? 'date';
$dayValue = $dayValue ?? '';
$monthValue = $monthValue ?? '';
$yearValue = $yearValue ?? '';
$required = $required ?? false;
$label = $label ?? 'Date';
$errorKey = $errorKey ?? $prefix;
$yearOptions = $yearOptions ?? [];

// Get options from helpers if not provided
$dayOptions = getDayOptions();
$monthOptions = getMonthOptions();

if (empty($yearOptions)) {
$currentYear = (int) date('Y');
$yearOptions = getYearOptions($currentYear - 100, $currentYear);
}
@endphp

<div class="date-selector-field">
    <label class="date-selector-label @if($required) date-selector-label-required @endif">
        {{ $label }} @if($required)<span class="required-indicator-red"></span>@endif
    </label>
    <div class="date-selector-grid">
        <!-- Day -->
        <div class="date-selector-group">
            <label for="{{ $prefix }}_day" class="date-selector-sub-label">Day</label>
            <select name="{{ $prefix }}_day" id="{{ $prefix }}_day" @if($required) required @endif
                class="date-selector-select">
                <option value="">Day</option>
                @foreach($dayOptions as $day)
                <option value="{{ $day }}" {{ $dayValue==$day ? 'selected' : '' }}>
                    {{ $day }}
                </option>
                @endforeach
            </select>
            @error("{$prefix}_day")
            <span class="date-selector-error">{{ $message }}</span>
            @enderror
        </div>

        <!-- Month -->
        <div class="date-selector-group">
            <label for="{{ $prefix }}_month" class="date-selector-sub-label">Month</label>
            <select name="{{ $prefix }}_month" id="{{ $prefix }}_month" @if($required) required @endif
                class="date-selector-select">
                <option value="">Month</option>
                @foreach($monthOptions as $monthNum => $monthName)
                <option value="{{ $monthNum }}" {{ $monthValue==$monthNum ? 'selected' : '' }}>
                    {{ $monthName }}
                </option>
                @endforeach
            </select>
            @error("{$prefix}_month")
            <span class="date-selector-error">{{ $message }}</span>
            @enderror
        </div>

        <!-- Year -->
        <div class="date-selector-group">
            <label for="{{ $prefix }}_year" class="date-selector-sub-label">Year</label>
            <select name="{{ $prefix }}_year" id="{{ $prefix }}_year" @if($required) required @endif
                class="date-selector-select">
                <option value="">Year</option>
                @foreach($yearOptions as $year)
                <option value="{{ $year }}" {{ $yearValue==$year ? 'selected' : '' }}>
                    {{ $year }}
                </option>
                @endforeach
            </select>
            @error("{$prefix}_year")
            <span class="date-selector-error">{{ $message }}</span>
            @enderror
        </div>
    </div>
    @error($errorKey)
    <span class="date-selector-error-full">{{ $message }}</span>
    @enderror
</div>