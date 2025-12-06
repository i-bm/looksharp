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

<div style="margin-bottom: 20px;">
    <label style="display: block; margin-bottom: 8px; font-weight: 500;">
        {{ $label }} @if($required)<span style="color: red;">*</span>@endif
    </label>
    <div style="display: flex; gap: 15px;">
        <!-- Day -->
        <div style="flex: 1;">
            <label for="{{ $prefix }}_day" style="display: block; margin-bottom: 4px; font-size: 14px; color: #666;">Day</label>
            <select name="{{ $prefix }}_day" id="{{ $prefix }}_day" @if($required) required @endif
                style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;">
                <option value="">Day</option>
                @foreach($dayOptions as $day)
                    <option value="{{ $day }}" {{ $dayValue == $day ? 'selected' : '' }}>
                        {{ $day }}
                    </option>
                @endforeach
            </select>
            @error("{$prefix}_day")
                <span style="color: red; font-size: 12px;">{{ $message }}</span>
            @enderror
        </div>

        <!-- Month -->
        <div style="flex: 1;">
            <label for="{{ $prefix }}_month" style="display: block; margin-bottom: 4px; font-size: 14px; color: #666;">Month</label>
            <select name="{{ $prefix }}_month" id="{{ $prefix }}_month" @if($required) required @endif
                style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;">
                <option value="">Month</option>
                @foreach($monthOptions as $monthNum => $monthName)
                    <option value="{{ $monthNum }}" {{ $monthValue == $monthNum ? 'selected' : '' }}>
                        {{ $monthName }}
                    </option>
                @endforeach
            </select>
            @error("{$prefix}_month")
                <span style="color: red; font-size: 12px;">{{ $message }}</span>
            @enderror
        </div>

        <!-- Year -->
        <div style="flex: 1;">
            <label for="{{ $prefix }}_year" style="display: block; margin-bottom: 4px; font-size: 14px; color: #666;">Year</label>
            <select name="{{ $prefix }}_year" id="{{ $prefix }}_year" @if($required) required @endif
                style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;">
                <option value="">Year</option>
                @foreach($yearOptions as $year)
                    <option value="{{ $year }}" {{ $yearValue == $year ? 'selected' : '' }}>
                        {{ $year }}
                    </option>
                @endforeach
            </select>
            @error("{$prefix}_year")
                <span style="color: red; font-size: 12px;">{{ $message }}</span>
            @enderror
        </div>
    </div>
    @error($errorKey)
        <span style="color: red; font-size: 14px; margin-top: 4px; display: block;">{{ $message }}</span>
    @enderror
</div>

