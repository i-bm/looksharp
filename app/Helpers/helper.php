<?php

use Carbon\Carbon;

function getGenderOptions()
{
    return [
        'male' => 'Male',
        'female' => 'Female',
        'other' => 'Other',
        'prefer_not_to_say' => 'Prefer not to say',
    ];
}

/**
 * Get day options (1-31).
 *
 * @return array<int>
 */
function getDayOptions(): array
{
    return range(1, 31);
}

/**
 * Get month options with names.
 *
 * @return array<string, string>
 */
function getMonthOptions(): array
{
    return [
        1 => 'January',
        2 => 'February',
        3 => 'March',
        4 => 'April',
        5 => 'May',
        6 => 'June',
        7 => 'July',
        8 => 'August',
        9 => 'September',
        10 => 'October',
        11 => 'November',
        12 => 'December',
    ];
}

/**
 * Get year options within a range.
 *
 * @param  int  $minYear  Minimum year
 * @param  int  $maxYear  Maximum year
 * @param  bool  $descending  Whether to return years in descending order
 * @return array<int>
 */
function getYearOptions(int $minYear, int $maxYear, bool $descending = true): array
{
    $years = range($minYear, $maxYear);

    return $descending ? array_reverse($years) : $years;
}

/**
 * Extract day, month, year from a date (Carbon instance or date string).
 *
 * @param  Carbon|string|null  $date
 * @return array{day: int|null, month: int|null, year: int|null}
 */
function extractDateComponents($date): array
{
    if ($date === null) {
        return ['day' => null, 'month' => null, 'year' => null];
    }

    if (is_string($date)) {
        try {
            $date = Carbon::parse($date);
        } catch (\Exception $e) {
            return ['day' => null, 'month' => null, 'year' => null];
        }
    }

    if (! ($date instanceof Carbon)) {
        return ['day' => null, 'month' => null, 'year' => null];
    }

    return [
        'day' => (int) $date->format('d'),
        'month' => (int) $date->format('m'),
        'year' => (int) $date->format('Y'),
    ];
}

/**
 * Combine day, month, year into a date string (Y-m-d format).
 *
 * @param  int|string  $day
 * @param  int|string  $month
 * @param  int|string  $year
 */
function combineDateComponents($day, $month, $year): string
{
    $day = str_pad((string) $day, 2, '0', STR_PAD_LEFT);
    $month = str_pad((string) $month, 2, '0', STR_PAD_LEFT);
    $year = (string) $year;

    return "{$year}-{$month}-{$day}";
}

/**
 * Validate date components and return validation result.
 *
 * @param  int|string  $day
 * @param  int|string  $month
 * @param  int|string  $year
 * @return array{valid: bool, date: string|null, error: string|null}
 */
function validateDateComponents($day, $month, $year): array
{
    $day = (int) $day;
    $month = (int) $month;
    $year = (int) $year;

    // Check if date is valid (e.g., not Feb 30)
    if (! checkdate($month, $day, $year)) {
        return [
            'valid' => false,
            'date' => null,
            'error' => 'Invalid date. Please select a valid date.',
        ];
    }

    $dateString = combineDateComponents($day, $month, $year);

    try {
        $date = Carbon::createFromFormat('Y-m-d', $dateString);

        return [
            'valid' => true,
            'date' => $dateString,
            'error' => null,
        ];
    } catch (\Exception $e) {
        return [
            'valid' => false,
            'date' => null,
            'error' => 'Invalid date format.',
        ];
    }
}

/**
 * Get date component values for form (handles old input and existing date).
 *
 * @param  string  $prefix  Prefix for old input keys (e.g., 'dob', 'start_date')
 * @param  Carbon|string|null  $existingDate  Existing date value
 * @return array{day: int|string, month: int|string, year: int|string}
 */
function getDateComponentValues(string $prefix, $existingDate = null): array
{
    $day = old("{$prefix}_day");
    $month = old("{$prefix}_month");
    $year = old("{$prefix}_year");

    // If no old values, extract from existing date
    if ($day === null && $month === null && $year === null && $existingDate !== null) {
        $components = extractDateComponents($existingDate);
        $day = $components['day'];
        $month = $components['month'];
        $year = $components['year'];
    }

    // Normalize to integers for comparison, but keep as string for empty values
    return [
        'day' => $day !== null ? (int) $day : '',
        'month' => $month !== null ? (int) $month : '',
        'year' => $year !== null ? (int) $year : '',
    ];
}
