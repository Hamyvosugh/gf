<?php
/**
 * Plugin Name: Persian Jalali Calendar
 * Description: A WordPress plugin to display a Persian Jalali calendar with functionality to navigate months and years.
 * Version: 1.0
 * Author: Hamy Vosugh
 */

 if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Load necessary scripts and styles
function pjc_register_assets() {
    $current_post = get_post();
    
    // Log current post status and shortcode presence
    error_log('Running pjc_register_assets');
    if ($current_post) {
        error_log('Current post found: ' . $current_post->ID);
    } else {
        error_log('No current post found');
    }

    if ($current_post && is_singular() && has_shortcode($current_post->post_content, 'pjc_calendar')) {
        error_log('Shortcode [pjc_calendar] found in content');
        
        $style_path = get_stylesheet_directory_uri() . '/inc/jallali-calendar/assets/css/pjc-style.css';
        error_log('Enqueueing CSS file at path: ' . $style_path);
        
        wp_enqueue_style('pjc-style', $style_path);
        
        $script_path = get_stylesheet_directory_uri() . '/inc/jallali-calendar/assets/js/pjc-script.js';
        error_log('Enqueueing JS file at path: ' . $script_path);
        
        wp_enqueue_script('pjc-script', $script_path, array('jquery'), null, true);
        wp_localize_script('pjc-script', 'pjcAjax', array('ajax_url' => admin_url('admin-ajax.php')));
    } else {
        error_log('Shortcode [pjc_calendar] not found or not singular post');
    }
}
add_action('wp_enqueue_scripts', 'pjc_register_assets');

// Shortcode to display the calendar
function pjc_display_calendar() {
    ob_start();
    include get_stylesheet_directory() . '/inc/jallali-calendar/templates/calendar.php';
        return ob_get_clean();
}
add_shortcode('pjc_calendar', 'pjc_display_calendar');

// Handle AJAX to fetch the calendar content for a specific month and year
function pjc_update_calendar() {
    if (isset($_POST['month']) && isset($_POST['year'])) {
        $month = (int)$_POST['month'];
        $year = (int)$_POST['year'];

        try {
            $calendar = pjc_get_calendar($year, $month);
            wp_send_json_success($calendar);
        } catch (Exception $e) {
            wp_send_json_error('Error generating the calendar: ' . $e->getMessage());
        }
    } else {
        wp_send_json_error('Month or year not provided.');
    }
    wp_die();
}
add_action('wp_ajax_pjc_update_calendar', 'pjc_update_calendar');
add_action('wp_ajax_nopriv_pjc_update_calendar', 'pjc_update_calendar');

// تابع AJAX برای تبدیل تاریخ جلالی به تبری
function convert_to_tabari_ajax() {
    if (isset($_POST['year']) && isset($_POST['month']) && isset($_POST['day'])) {
        $year = intval($_POST['year']);
        $month = intval($_POST['month']);
        $day = intval($_POST['day']);
        
        $tabari_date = convert_jalali_to_tabari($year, $month, $day);
        
        wp_send_json_success($tabari_date);
    } else {
        wp_send_json_error('اطلاعات کافی ارسال نشده است.');
    }
    wp_die();
}
add_action('wp_ajax_convert_to_tabari', 'convert_to_tabari_ajax');
add_action('wp_ajax_nopriv_convert_to_tabari', 'convert_to_tabari_ajax');



// Utility function to convert English numbers to Persian
if (!function_exists('convertNumbersToPersian')) {
    function convertNumbersToPersian($string) {
        $persian_numbers = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        $english_numbers = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        return str_replace($english_numbers, $persian_numbers, $string);
    }
}

// Import necessary classes at the top
use Hekmatinasser\Verta\Verta;
use Morilog\Jalali\CalendarUtils;

// Adjusted Logic for the New Calendar Library (Morilog\Jalali)
function pjc_get_calendar($year, $month) {
    try {
        // Initialize the output variable
        $output = '';

        // Use Morilog to get the correct first day of the month
        $gregorianDate = CalendarUtils::toGregorian($year, $month, 1);
        $timestamp = strtotime("{$gregorianDate[0]}-{$gregorianDate[1]}-{$gregorianDate[2]}");
        $dayOfWeek = date('l', $timestamp); // Get English name of the day

        // Convert to Persian day names
        $dayNames = [
            'Saturday' => 'ش<br>Sa',
            'Sunday' => 'ی<br>Su',
            'Monday' => 'د<br>Mo',
            'Tuesday' => 'س<br>Tu',
            'Wednesday' => 'چ<br>We',
            'Thursday' => 'پ<br>Th',
            'Friday' => 'ج<br>Fr',
        ];

        // Additional Persian day titles
        $dayRoles = [
            'Saturday' => 'کیوان ‌شید',
            'Sunday' => 'مهر شید',
            'Monday' => 'مه شید',
            'Tuesday' => 'بهرام شید',
            'Wednesday' => 'تیر شید',
            'Thursday' => 'هرمزد شید',
            'Friday' => 'ناهید شید',
        ];

        // Determine the index for the first day of the week (RTL, Saturday to Friday)
        $dayName = $dayNames[$dayOfWeek];
        $dayIndex = array_search($dayName, ['شنبه', 'یکشنبه', 'دوشنبه', 'سه‌شنبه', 'چهارشنبه', 'پنج‌شنبه', 'جمعه']);

        $gregorianDate = CalendarUtils::toGregorian($year, $month, 1);
        $timestamp = strtotime("{$gregorianDate[0]}-{$gregorianDate[1]}-{$gregorianDate[2]}");
        $dayOfWeek = date('l', $timestamp);
        
        // محاسبه تعداد روزهای ماه شمسی
        $days_in_month = ($month <= 6) ? 31 : (($month <= 11) ? 30 : (CalendarUtils::isLeapJalaliYear($year) ? 30 : 29));
        $today = new Verta();

        // محاسبه محدوده ماه‌های میلادی برای این ماه جلالی
        $start_gregorian = CalendarUtils::toGregorian($year, $month, 1);
        $end_gregorian = CalendarUtils::toGregorian($year, $month, $days_in_month);
        $start_gregorian_month = date('F', strtotime("{$start_gregorian[0]}-{$start_gregorian[1]}-{$start_gregorian[2]}"));
        $end_gregorian_month = date('F', strtotime("{$end_gregorian[0]}-{$end_gregorian[1]}-{$end_gregorian[2]}"));

        // نمایش محدوده ماه‌های میلادی
        $gregorian_months_range = ($start_gregorian_month === $end_gregorian_month) 
            ? $start_gregorian_month 
            : "{$start_gregorian_month} - {$end_gregorian_month}";
        
            
        // شروع کد جدید
// تابع بررسی سال کبیسه
function isLeapJalaliYear($year) {
    $mod = ($year % 33);
    return ($mod == 1 || $mod == 5 || $mod == 9 || $mod == 13 || $mod == 17 || $mod == 22 || $mod == 26 || $mod == 30);
}

// تابع محاسبه تعداد روزهای ماه
function getJalaliMonthDays($month, $year) {
    if ($month <= 6) {
        return 31;
    } elseif ($month <= 11) {
        return 30;
    } else { // month == 12
        return isLeapJalaliYear($year) ? 30 : 29;
    }
}
// پایان کد جدید    

        // Check if it's the current month
        $is_current_month = ($today->year == $year && $today->month == $month);

        $output .= '<div class="gregorian-months-range">' . $gregorian_months_range . ' ' . $start_gregorian[0] . '</div>';
        $output .= '<table class="pjc-calendar-table"><thead><tr>';

        // First row for day names
        foreach ($dayRoles as $englishDay => $persian_day) {
            $highlightRole = ($is_current_month && $englishDay == $dayOfWeek) 
                ? 'style=" "' 
                : '';
            $output .= "<th $highlightRole class='day-role day-$englishDay'>$persian_day</th>";
        }

        $output .= '</tr><tr>';
       // Second row for shorter day names
foreach ($dayNames as $englishDay => $shortName) {
    $highlightRole = ($is_current_month && $englishDay == $dayOfWeek)
        ? 'style=" color: white;"'
        : '';
    $output .= "<th $highlightRole class='day-name day-$englishDay'>$shortName</th>";
}

        $output .= '</tr></thead><tbody><tr>';

        // Calculate the index of the first day of the week in Persian terms
        $firstDayIndex = array_search($dayOfWeek, array_keys($dayNames)); // Correct the index finding logic

        // Add empty cells before the first day of the month
        for ($i = 0; $i < $firstDayIndex; $i++) {
            $output .= '<td class="empty"></td>';
        }

        // Display the days of the month
        for ($day = 1; $day <= $days_in_month; $day++) {
            if (($firstDayIndex + $day - 1) % 7 == 0 && $day != 1) {
                $output .= '</tr><tr>';
            }

            $dayOfWeekIndex = ($firstDayIndex + $day - 1) % 7;
            $isFriday = ($dayOfWeekIndex == 6);

            $formattedDay = str_pad($month, 2, '0', STR_PAD_LEFT) . '-' . str_pad($day, 2, '0', STR_PAD_LEFT);
            global $wpdb;
            $table_name = $wpdb->prefix . 'farakhor';
            $isHoliday = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM $table_name WHERE persian_day = %s AND categories = 'تعطیل'",
                $formattedDay
            )) > 0;

            $highlight = ($is_current_month && $day == $today->day) ? 'style="background-color: #FF8200; color: white;"' : '';
            $fridayOrHolidayHighlight = ($isFriday || $isHoliday) ? 'style="color: #FF8200;"' : '';

            // محاسبه تاریخ میلادی برای هر روز
            $gregorianDay = CalendarUtils::toGregorian($year, $month, $day);
            $gregorianDate = "{$gregorianDay[2]}";

            $persian_day = convertNumbersToPersian($day);
            $output .= "<td class='day-cell' data-day='$day' data-month='$month' data-year='$year' $highlight $fridayOrHolidayHighlight onclick='fetchDayEvents($day, $month, $year)'>
                            $persian_day <span class='gregorian-day'>$gregorianDate</span>
                        </td>";
        }

        $remaining_cells = (7 - (($firstDayIndex + $days_in_month - 1) % 7 + 1)) % 7;
        for ($i = 0; $i < $remaining_cells; $i++) {
            $output .= '<td class="empty"></td>';
        }

        $output .= '</tr></tbody></table>';
        return $output;
    } catch (Exception $e) {
        return "خطا در محاسبه روز اول ماه: " . $e->getMessage();
    }
}

// Handle AJAX to fetch events for a specific day
function pjc_get_day_events() {
    if (isset($_POST['day']) && isset($_POST['month']) && isset($_POST['year'])) {
        $day = str_pad($_POST['day'], 2, '0', STR_PAD_LEFT);
        $month = str_pad($_POST['month'], 2, '0', STR_PAD_LEFT);
        $persianDay = "{$month}-{$day}";

        global $wpdb;
        $table_name = $wpdb->prefix . 'farakhor';

        // Query to fetch events
        $results = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM $table_name WHERE persian_day = %s AND (module = 'YES' OR module IS NULL)",
                $persianDay
            )
        );

        if ($results) {
            $output = '';
            foreach ($results as $index => $row) {
                // Convert line numbers to Persian
                $lineNumber = convertNumbersToPersian($index + 1);
                
                $output .= "<div class='event-row' style='text-align: right; direction: rtl; padding-right: 5px;'>";
                $output .= "<span class='event-number'>{$lineNumber}.</span> ";
                $output .= "<span class='event-title'>" . esc_html($row->event_title) . "</span>: ";
                $output .= "<span class='event-text'>" . esc_html($row->event_text) . "</span>";
                $output .= "</div>";
            }
            wp_send_json_success($output);
        } else {
            $output = "<p class='no-event-message' style='text-align: center; direction: rtl;'>هیچ رویدادی برای امروز موجود نیست.</p>";
            wp_send_json_success($output);
        }

    } else {
        wp_send_json_error('مشکل در دریافت داده‌ها.');
    }
    wp_die();
}
add_action('wp_ajax_pjc_get_day_events', 'pjc_get_day_events');
add_action('wp_ajax_nopriv_pjc_get_day_events', 'pjc_get_day_events');



// Handle AJAX to load shortcode content
// Handle AJAX to load shortcode content
add_action('wp_ajax_load_shortcode', 'load_shortcode_content');
add_action('wp_ajax_nopriv_load_shortcode', 'load_shortcode_content');

function load_shortcode_content() {
    if (isset($_POST['shortcode'])) {
        $shortcode = sanitize_text_field($_POST['shortcode']);
        // اطمینان از بارگذاری اسکریپت‌ها و استایل‌ها
        pjc_register_assets();
        echo do_shortcode($shortcode);
    }
    wp_die();
}




register_activation_hook(__FILE__, 'pjc_create_farakhor_table');

function pjc_create_farakhor_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'farakhor';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        persian_day varchar(10) NOT NULL,
        event_title text NOT NULL,
        event_text text NOT NULL,
        tag varchar(50) NULL,
        module varchar(10) NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

function get_current_jalali() {
    $current = new Verta();
    wp_send_json_success([
        'month' => $current->month,
        'year' => $current->year
    ]);
}
add_action('wp_ajax_get_current_jalali', 'get_current_jalali');
add_action('wp_ajax_nopriv_get_current_jalali', 'get_current_jalali');


/**
 * تبدیل تاریخ جلالی به تاریخ تبری
 * 
 * @param int $jalali_year سال جلالی
 * @param int $jalali_month ماه جلالی
 * @param int $jalali_day روز جلالی
 * @return array آرایه‌ای شامل سال، ماه و روز تبری
 */
function convert_jalali_to_tabari($jalali_year, $jalali_month, $jalali_day) {
    // محاسبه سال تبری (132 سال اضافه می‌شود)
    $tabari_year = $jalali_year + 132;
    
    // تعیین ماه و روز تبری بر اساس جدول تطبیقی
    $tabari_month = '';
    $tabari_day = 0;
    
    // ماه‌های تبری با شروع از 2 مرداد
    $tabari_months = [
        'فردینه ما', // 2 مرداد - 31 مرداد
        'کرچه ما',   // 1 شهریور - 30 شهریور
        'هره ما',    // 31 شهریور - 29 مهر
        'تیر ما',    // 30 مهر - 29 آبان
        'ملاره ما',  // 30 آبان - 29 آذر
        'شروینه ما', // 30 آذر - 29 دی
        'میره ما',   // 30 دی - 29 بهمن
        'اونه ما',   // 30 بهمن - 29 اسفند
        'ارکه ما',   // 6 فروردین - 4 اردیبهشت (بعد از ایام پیتک)
        'ده ما',     // 5 اردیبهشت - 3 خرداد
        'وهمنه ما',  // 4 خرداد - 2 تیر
        'نوروز ما'   // 3 تیر - 1 مرداد
    ];
    
    // تبدیل تاریخ بر اساس جدول تطبیقی
    if ($jalali_month == 5 && $jalali_day >= 2) {
        // فردینه ما: 2 مرداد - 31 مرداد
        $tabari_month = $tabari_months[0];
        $tabari_day = $jalali_day - 1;
    } else if ($jalali_month == 6) {
        // کرچه ما: 1 شهریور - 30 شهریور
        $tabari_month = $tabari_months[1];
        $tabari_day = $jalali_day;
    } else if (($jalali_month == 7 && $jalali_day == 1) || ($jalali_month == 6 && $jalali_day == 31)) {
        // روز اول هره ما: 31 شهریور
        $tabari_month = $tabari_months[2];
        $tabari_day = 1;
    } else if ($jalali_month == 7 && $jalali_day > 1) {
        // هره ما: 1 مهر - 29 مهر
        $tabari_month = $tabari_months[2];
        $tabari_day = $jalali_day;
    } else if (($jalali_month == 8 && $jalali_day <= 29) || ($jalali_month == 7 && $jalali_day == 30)) {
        // تیر ما: 30 مهر - 29 آبان
        $tabari_month = $tabari_months[3];
        if ($jalali_month == 7) {
            $tabari_day = 1;
        } else {
            $tabari_day = $jalali_day + 1;
        }
    } else if (($jalali_month == 9 && $jalali_day <= 29) || ($jalali_month == 8 && $jalali_day == 30)) {
        // ملاره ما: 30 آبان - 29 آذر
        $tabari_month = $tabari_months[4];
        if ($jalali_month == 8) {
            $tabari_day = 1;
        } else {
            $tabari_day = $jalali_day + 1;
        }
    } else if (($jalali_month == 10 && $jalali_day <= 29) || ($jalali_month == 9 && $jalali_day == 30)) {
        // شروینه ما: 30 آذر - 29 دی
        $tabari_month = $tabari_months[5];
        if ($jalali_month == 9) {
            $tabari_day = 1;
        } else {
            $tabari_day = $jalali_day + 1;
        }
    } else if (($jalali_month == 11 && $jalali_day <= 29) || ($jalali_month == 10 && $jalali_day == 30)) {
        // میره ما: 30 دی - 29 بهمن
        $tabari_month = $tabari_months[6];
        if ($jalali_month == 10) {
            $tabari_day = 1;
        } else {
            $tabari_day = $jalali_day + 1;
        }
    } else if (($jalali_month == 12 && $jalali_day <= 29) || ($jalali_month == 11 && $jalali_day == 30)) {
        // اونه ما: 30 بهمن - 29 اسفند
        $tabari_month = $tabari_months[7];
        if ($jalali_month == 11) {
            $tabari_day = 1;
        } else {
            $tabari_day = $jalali_day + 1;
        }
    } else if ($jalali_month == 12 && $jalali_day == 30) {
        // شیشک (در سال کبیسه): 30 اسفند
        $tabari_month = 'شیشک';
        $tabari_day = 1;
    } else if ($jalali_month == 1 && $jalali_day <= 5) {
        // پیتک: 1-5 فروردین
        $tabari_month = 'پیتک';
        $tabari_day = $jalali_day;
    } else if (($jalali_month == 1 && $jalali_day >= 6) || ($jalali_month == 2 && $jalali_day <= 4)) {
        // ارکه ما: 6 فروردین - 4 اردیبهشت
        $tabari_month = $tabari_months[8];
        if ($jalali_month == 1) {
            $tabari_day = $jalali_day - 5;
        } else {
            $tabari_day = $jalali_day + 26;
        }
    } else if (($jalali_month == 2 && $jalali_day >= 5) || ($jalali_month == 3 && $jalali_day <= 3)) {
        // ده ما: 5 اردیبهشت - 3 خرداد
        $tabari_month = $tabari_months[9];
        if ($jalali_month == 2) {
            $tabari_day = $jalali_day - 4;
        } else {
            $tabari_day = $jalali_day + 27;
        }
    } else if (($jalali_month == 3 && $jalali_day >= 4) || ($jalali_month == 4 && $jalali_day <= 2)) {
        // وهمنه ما: 4 خرداد - 2 تیر
        $tabari_month = $tabari_months[10];
        if ($jalali_month == 3) {
            $tabari_day = $jalali_day - 3;
        } else {
            $tabari_day = $jalali_day + 28;
        }
    } else if (($jalali_month == 4 && $jalali_day >= 3) || ($jalali_month == 5 && $jalali_day <= 1)) {
        // نوروز ما: 3 تیر - 1 مرداد
        $tabari_month = $tabari_months[11];
        if ($jalali_month == 4) {
            $tabari_day = $jalali_day - 2;
        } else {
            $tabari_day = $jalali_day + 29;
        }
    }
    
    return [
        'year' => $tabari_year,
        'month' => $tabari_month,
        'day' => $tabari_day
    ];
    
    // اگر می‌خواهید تبدیل اعداد در سمت سرور انجام شود، کد زیر را جایگزین کد بالا کنید
    // return [
    //     'year' => convertNumbersToPersian($tabari_year),
    //     'month' => $tabari_month,
    //     'day' => convertNumbersToPersian($tabari_day)
    // ];
}


?>