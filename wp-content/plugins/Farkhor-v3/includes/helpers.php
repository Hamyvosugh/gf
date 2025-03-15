<?php
/**
 * Helper functions for Farakhor Events plugin.
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Convert Persian month number to name.
 *
 * @param int $month_num Month number (1-12).
 * @return string Persian month name.
 */
function farakhor_get_persian_month_name($month_num) {
    $persian_months = array(
        1 => 'فروردین',
        2 => 'اردیبهشت',
        3 => 'خرداد',
        4 => 'تیر',
        5 => 'مرداد',
        6 => 'شهریور',
        7 => 'مهر',
        8 => 'آبان',
        9 => 'آذر',
        10 => 'دی',
        11 => 'بهمن',
        12 => 'اسفند'
    );

    return isset($persian_months[$month_num]) ? $persian_months[$month_num] : '';
}

/**
 * Convert Persian day of week number to name.
 *
 * @param int $day_num Day of week number (0-6, with 0 being Saturday).
 * @return string Persian day name.
 */
function farakhor_get_persian_day_name($day_num) {
    $persian_days = array(
        0 => 'شنبه',
        1 => 'یکشنبه',
        2 => 'دوشنبه',
        3 => 'سه‌شنبه',
        4 => 'چهارشنبه',
        5 => 'پنج‌شنبه',
        6 => 'جمعه'
    );

    return isset($persian_days[$day_num]) ? $persian_days[$day_num] : '';
}

/**
 * Convert western numerals to Persian numerals.
 *
 * @param string|int $number The number to convert.
 * @return string The Persian numeral.
 */
function farakhor_convert_to_persian_numeral($number) {
    $western = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
    $persian = array('۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹');
    
    return str_replace($western, $persian, (string)$number);
}

/**
 * Convert Persian date format (M-D) to full Persian date.
 *
 * @param string $persian_day Persian day in format "M-D".
 * @return string Full Persian date with day of week and month name.
 */
function farakhor_get_persian_full_date($persian_day) {
    // Extract month and day from persian_day
    $parts = explode('-', $persian_day);
    if (count($parts) !== 2) {
        return '';
    }
    
    $month_num = intval($parts[0]);
    $day_num = intval($parts[1]);
    
    // Get month name
    $month_name = farakhor_get_persian_month_name($month_num);
    
    // Convert to Persian numerals
    $day_persian = farakhor_convert_to_persian_numeral($day_num);
    
    // Calculate day of week (this is a simplified approach)
    // For a proper implementation, you might need a complete Persian calendar library
    // or API to determine the exact day of week
    
    // For demonstration purposes, we'll use the Gregorian date to determine day of week
    // This is not accurate for Persian calendar but serves as a placeholder
    $gregorian_date = farakhor_get_gregorian_from_persian($persian_day);
    $day_of_week = date('w', strtotime($gregorian_date));
    
    // In Persian calendar, the week starts on Saturday (0)
    $day_of_week = ($day_of_week + 1) % 7;
    
    $day_name = farakhor_get_persian_day_name($day_of_week);
    
    // Construct full Persian date
    return sprintf('%s %s %s', $day_name, $day_persian, $month_name);
}

/**
 * Convert Persian date to Gregorian date.
 * 
 * Note: For a proper implementation, a complete Persian calendar conversion library
 * like jDateTime would be recommended. This is a simplified approximation.
 *
 * @param string $persian_day Persian day in format "M-D".
 * @return string Gregorian date in Y-m-d format.
 */
function farakhor_get_gregorian_from_persian($persian_day) {
    global $wpdb;
    
    // Look up the actual gregorian_day from the database based on persian_day
    $table_name = $wpdb->prefix . 'farakhor';
    $query = $wpdb->prepare(
        "SELECT gregorian_day FROM {$table_name} WHERE persian_day = %s LIMIT 1",
        $persian_day
    );
    
    $result = $wpdb->get_var($query);
    
    if ($result) {
        return $result;
    }
    
    // Fallback to approximate conversion if not found in database
    // This is just a placeholder - a proper implementation would use a Persian calendar library
    $parts = explode('-', $persian_day);
    if (count($parts) !== 2) {
        return date('Y-m-d');
    }
    
    $month_num = intval($parts[0]);
    $day_num = intval($parts[1]);
    
    // Approximate conversion (this is not accurate)
    // Persian year starts around March 21
    $current_year = date('Y');
    
    if ($month_num <= 6) {
        // First half of Persian year
        return date('Y-m-d', strtotime($current_year . '-' . ($month_num + 2) . '-' . $day_num));
    } else {
        // Second half of Persian year
        return date('Y-m-d', strtotime($current_year . '-' . ($month_num - 6) . '-' . $day_num));
    }
}

/**
 * Calculate difference in days between a date and current date.
 *
 * @param string $date Date in Y-m-d format to compare with current date.
 * @return int Number of days difference (positive if in future, negative if in past).
 */
function farakhor_calculate_days_difference($date) {
    $today = new DateTime(current_time('Y-m-d'));
    $event_date = new DateTime($date);
    
    $interval = $today->diff($event_date);
    
    // Return positive days for future events, negative for past events
    return ($event_date > $today) ? $interval->days : -$interval->days;
}

/**
 * Get current Jalali month.
 *
 * @return int Current Jalali month (1-12).
 */
function farakhor_get_current_jalali_month() {
    // Use Verta library if available
    if (class_exists('Hekmatinasser\Verta\Verta')) {
        $verta = new \Hekmatinasser\Verta\Verta();
        return $verta->month;
    }
    
    // Fallback method if Verta is not available
    $current_month = intval(date('m'));
    $current_day = intval(date('d'));
    
    // Rough approximation of Jalali month
    // Persian new year is around March 21
    if ($current_month < 3 || ($current_month == 3 && $current_day < 21)) {
        // January, February, and first 20 days of March are in months 10-12 of Persian calendar
        return $current_month + 9;
    } else if ($current_month == 3 && $current_day >= 21) {
        // March 21 onwards is month 1 of Persian calendar
        return 1;
    } else {
        // April onwards: subtract 2 from Gregorian month and add 1 for Persian month
        return $current_month - 3 + 1;
    }
}