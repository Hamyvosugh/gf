<?php
/**
 * Plugin Name: Gahshomar Converter
 * Description: A date converter plugin for various calendars.
 * Version: 1.0.0
 * Author: Hamy Vosugh
 * Text Domain: gahshomar-converter
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Autoloader for plugin classes
spl_autoload_register(function($class) {
    // Project-specific namespace prefix
    $prefix = 'Gahshomar\\';

    // Base directory for the namespace prefix
    $base_dir = __DIR__ . '/includes/widgets/';

    // Does the class use the namespace prefix?
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        // No, move to the next registered autoloader
        return;
    }

    // Get the relative class name
    $relative_class = substr($class, $len);

    // Replace the namespace prefix with the base directory,
    // replace namespace separators with directory separators
    // in the relative class name, append with .php
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    // If the file exists, require it
    if (file_exists($file)) {
        require $file;
    }
});

function gahshomar_converter_enqueue_styles() {
    wp_enqueue_style('gahshomar-converter-styles', plugins_url('assets/css/gahshomar-converter.css', __FILE__));
}
add_action('wp_enqueue_scripts', 'gahshomar_converter_enqueue_styles');

function register_gahshomar_converter_widgets($widgets_manager) {
    require_once __DIR__ . '/includes/widgets/class-elementor-gahshomar-widget.php';
    require_once __DIR__ . '/includes/widgets/class-elementor-conversion-result-widget.php';
    require_once __DIR__ . '/includes/widgets/class-elementor-gregorian-date-widget.php';
    require_once __DIR__ . '/includes/widgets/class-elementor-jalaali-date-widget.php';
    require_once __DIR__ . '/includes/widgets/class-elementor-birthstone-widget.php';
    require_once __DIR__ . '/includes/widgets/class-elementor-zoroastrian-day-widget.php';
    require_once __DIR__ . '/includes/widgets/class-elementor-converted-date-info-widget.php'; 
    require_once __DIR__ . '/includes/widgets/class-elementor-chinese-zodiac-widget.php';

    $widgets_manager->register(new \Gahshomar\Elementor_Gahshomar_Widget());
    $widgets_manager->register(new \Gahshomar\Elementor_Conversion_Result_Widget());
    $widgets_manager->register(new \Gahshomar\Elementor_Gregorian_Date_Widget());
    $widgets_manager->register(new \Gahshomar\Elementor_Jalaali_Date_Widget());
    $widgets_manager->register(new \Gahshomar\Elementor_Birthstone_Widget());
    $widgets_manager->register(new \Gahshomar\Elementor_Zoroastrian_Day_Widget());
    $widgets_manager->register(new \Gahshomar\Elementor_Converted_Date_Info_Widget());  
    $widgets_manager->register(new \Gahshomar\Elementor_Chinese_Zodiac_Widget());
}
add_action('elementor/widgets/register', 'register_gahshomar_converter_widgets');

// Load text domain for translations
function gahshomar_converter_load_textdomain() {
    load_plugin_textdomain('gahshomar-converter', false, basename(dirname(__FILE__)) . '/languages');
}
add_action('plugins_loaded', 'gahshomar_converter_load_textdomain');

function gahshomar_converter_enqueue_scripts() {
    wp_enqueue_script(
        'gahshomar-converter',
        plugins_url('assets/js/gahshomar-converter.js', __FILE__),
        array('jquery'),
        null,
        true
    );

    // Adding the AJAX URL using wp_add_inline_script
    $ajax_script = sprintf('var ajaxurl = "%s";', admin_url('admin-ajax.php'));
    wp_add_inline_script('gahshomar-converter', $ajax_script, 'before');
}
add_action('wp_enqueue_scripts', 'gahshomar_converter_enqueue_scripts');

// Define custom calendar offsets
const CUSTOM_CALENDARS = [
    'padeshahi' => 1180,
    'eilami' => 3821,
    'zartoshti' => 2359,
    'madi' => 1321,
    'iran_nov' => -1396
];

// Define custom calendar names in Persian
const CUSTOM_CALENDAR_NAMES = [
    'padeshahi' => 'شاهنشاهی',
    'eilami' => 'ایلامی',
    'zartoshti' => 'زرتشتی',
    'madi' => 'کردی',
    'iran_nov' => 'ایران نو'
];

// AJAX handler for date conversion
function gahshomar_converter_ajax_handler() {
    if (!isset($_POST['source_calendar'], $_POST['day'], $_POST['month'], $_POST['year'], $_POST['target_calendar'])) {
        wp_send_json_error(['message' => 'درخواست نامعتبر است. لطفاً تمام فیلدهای مورد نیاز را وارد کنید']);
    }

    $source_calendar = sanitize_text_field($_POST['source_calendar']);
    $day = intval($_POST['day']);
    $month = intval($_POST['month']);
    $year = intval($_POST['year']);
    $target_calendar = sanitize_text_field($_POST['target_calendar']);

    require_once __DIR__ . '/Calendar/Gahshomar/LeapYearChecker.php';
    require_once __DIR__ . '/Calendar/Gahshomar/src/GregorianToJalaali.php';
    require_once __DIR__ . '/Calendar/Gahshomar/src/JalaaliToGregorian.php';

    try {

        if ($source_calendar == 'gregorian' && $target_calendar == 'jalaali') {
            list($jy, $jm, $jd) = \Gahshomar\GregorianToJalaali::convert($year, $month, $day);
            $weekday = \Gahshomar\GregorianToJalaali::getWeekdayName($year, $month, $day);
            $formatted_year = $jy < 0 ? abs($jy) . 'سال پیش از سرآغاز گاهشمار هجری شمسی' : $jy;
            $output = \Gahshomar\GregorianToJalaali::formatJalaaliDate($formatted_year, $jm, $jd, $weekday);
            
            if ($jy >= 0) {
                $calendar_name = 'هجری شمسی';
                $output .= " " . $calendar_name;
            }
        } elseif ($source_calendar == 'jalaali' && $target_calendar == 'gregorian') {
            list($gy, $gm, $gd) = \Gahshomar\JalaaliToGregorian::convert($year, $month, $day);
            
            // بررسی سال منفی و فرمت صحیح آن
            $formatted_year = $gy < 0 ? abs($gy) . ' سال پیش از ' : $gy;
            
            // بررسی اینکه آیا تاریخ ساخته شده قابل اعتبار است
            if (checkdate($gm, $gd, $gy)) {
                // به‌دست آوردن نام روز هفته با استفاده از DateTime
                $date = \DateTime::createFromFormat('Y-m-d', sprintf('%04d-%02d-%02d', $gy, $gm, $gd));
                $english_weekday = $date->format('l'); // نام روز هفته به انگلیسی (مانند Monday)
            } else {
                $english_weekday = '';
            }
        
            // نگاشت روزهای هفته به زبان فارسی
            $weekdays_map = [
                'Saturday' => 'Saturday=کیوان شید(شنبه)',
                'Sunday' => 'Sunday= مهر شید (یکشنبه)',
                'Monday' => 'Monday=مه شید (دوشنبه)',
                'Tuesday' => 'Tuesday=بهرام شید (سه شنبه)',
                'Wednesday' => 'Wednesday=هرمزشید (پنج شنبه)',
                'Thursday' => 'Thursday=کیوان شید(شنبه)',
                'Friday' => 'Friday=ناهید شید(آدینه)'
            ];
        
            // تبدیل نام روز هفته به فارسی
            $persian_weekday = $english_weekday ? $weekdays_map[$english_weekday] : 'نامشخص';
        
            // ساختن خروجی
            $output = \Gahshomar\JalaaliToGregorian::formatGregorianDate($formatted_year, $gm, $gd);
            $output .= "میلادی" . CUSTOM_CALENDAR_NAMES[$target_calendar];
            $output = $output . " / " . $persian_weekday; // اضافه کردن نام روز هفته به سمت راست خروجی
        } elseif ($source_calendar == 'gregorian' && array_key_exists($target_calendar, CUSTOM_CALENDARS)) {
            list($jy, $jm, $jd) = \Gahshomar\GregorianToJalaali::convert($year, $month, $day);
            $jy += CUSTOM_CALENDARS[$target_calendar];
            $weekday = \Gahshomar\GregorianToJalaali::getWeekdayName($year, $month, $day);
            $month_name = \Gahshomar\GregorianToJalaali::getJalaaliMonthName($jm);
            $calendar_name = CUSTOM_CALENDAR_NAMES[$target_calendar];
            $formatted_year = $jy < 0 ? abs($jy) . ' سال پیش از ' : $jy;
            $output = "$weekday $jd $month_name $formatted_year $calendar_name";
        } elseif ($source_calendar == 'jalaali' && array_key_exists($target_calendar, CUSTOM_CALENDARS)) {
            $jy = $year + CUSTOM_CALENDARS[$target_calendar];
            $month_name = \Gahshomar\GregorianToJalaali::getJalaaliMonthName($month);
            $calendar_name = CUSTOM_CALENDAR_NAMES[$target_calendar];
            $weekday = \Gahshomar\GregorianToJalaali::getWeekdayName($year, $month, $day);
            $formatted_year = $jy < 0 ? abs($jy) . ' سال پیش از ' : $jy;
            $output = "$weekday $day $month_name $formatted_year $calendar_name";
        } elseif (array_key_exists($source_calendar, CUSTOM_CALENDARS) && $target_calendar == 'gregorian') {
            $jy = $year - CUSTOM_CALENDARS[$source_calendar];
            list($gy, $gm, $gd) = \Gahshomar\JalaaliToGregorian::convert($jy, $month, $day);
            
            // بررسی سال منفی و فرمت صحیح آن
            $formatted_year = $gy < 0 ? abs($gy) . ' سال پیش از سرآغاز گاهشمار میلادی ' : $gy;
            
            // بررسی اینکه آیا تاریخ ساخته شده قابل اعتبار است
            if (checkdate($gm, $gd, $gy)) {
                // به‌دست آوردن نام روز هفته با استفاده از DateTime
                $date = \DateTime::createFromFormat('Y-m-d', sprintf('%04d-%02d-%02d', $gy, $gm, $gd));
                $english_weekday = $date->format('l'); // نام روز هفته به انگلیسی (مانند Monday)
            } else {
                $english_weekday = '';
            }
        
            // نگاشت روزهای هفته به زبان فارسی
            $weekdays_map = [
                'Saturday' => 'Saturday=کیوان شید(شنبه)',
                'Sunday' => 'Sunday= مهر شید (یکشنبه)',
                'Monday' => 'Monday=مه شید (دوشنبه)',
                'Tuesday' => 'Tuesday=بهرام شید (سه شنبه)',
                'Wednesday' => 'Wednesday=هرمزشید (پنج شنبه)',
                'Thursday' => 'Thursday=کیوان شید(شنبه)',
                'Friday' => 'Friday=ناهید شید(آدینه)'
            ];
        
          
            // تبدیل نام روز هفته به فارسی
             $persian_weekday = $english_weekday ? $weekdays_map[$english_weekday] : 'نام روز هفته نامشخص است';

           // ساختن خروجی
             $output = \Gahshomar\JalaaliToGregorian::formatGregorianDate($formatted_year, $gm, $gd);
    
            // اگر سال منفی نبود، اضافه کردن نام تقویم مقصد
             if ($gy >= 0) {
             $output .= " میلادی " . CUSTOM_CALENDAR_NAMES[$target_calendar];
             }
    
            // اضافه کردن نام روز هفته به سمت راست خروجی
             $output .= " / " . $persian_weekday;

            } elseif (array_key_exists($source_calendar, CUSTOM_CALENDARS) && $target_calendar == 'jalaali') {
                $jy = $year - CUSTOM_CALENDARS[$source_calendar];
            
                // بررسی معتبر بودن سال جلالی پس از تبدیل
                if ($jy <= 0) {
                    // اگر سال منفی یا صفر است، می‌توانیم آن را به گونه‌ای مدیریت کنیم یا پیام خطا نمایش دهیم
                    $formatted_year = abs($jy) . 'سال پیش از سرآغاز گاهشمار هجری شمسی' ;
                    $month_name = \Gahshomar\GregorianToJalaali::getJalaaliMonthName($month);
                    $weekday = \Gahshomar\GregorianToJalaali::getWeekdayName(abs($jy), $month, $day);
                    $output = \Gahshomar\GregorianToJalaali::formatJalaaliDate($formatted_year, $month, $day, $weekday);
                    // در این حالت، نام تقویم مقصد نمایش داده نمی‌شود
                } else {
                    // اگر سال معتبر است، مانند قبل ادامه دهید
                    $month_name = \Gahshomar\GregorianToJalaali::getJalaaliMonthName($month);
                    $weekday = \Gahshomar\GregorianToJalaali::getWeekdayName($jy, $month, $day);
                    $output = \Gahshomar\GregorianToJalaali::formatJalaaliDate($jy, $month, $day, $weekday);
                    $output .= " هجری شمسی " . CUSTOM_CALENDAR_NAMES[$target_calendar];
                }
            } elseif (array_key_exists($source_calendar, CUSTOM_CALENDARS) && array_key_exists($target_calendar, CUSTOM_CALENDARS)) {
                // بررسی یکسان بودن تقویم‌های سفارشی
                if ($source_calendar === $target_calendar) {
                    $output = "شما گاهشماری های یکسانی را انتخاب کرده اید !";
                } else {
                    $jy = $year - CUSTOM_CALENDARS[$source_calendar] + CUSTOM_CALENDARS[$target_calendar];
                    
                    // بررسی معتبر بودن سال پس از تبدیل
                    if ($jy <= 0) {
                        // اگر سال منفی یا صفر است، مدیریت و فرمت سال به همراه تقویم مقصد
                        $formatted_year = abs($jy) . ' سال پیش از سرآغاز گاشمار ' . CUSTOM_CALENDAR_NAMES[$target_calendar];
                        $month_name = \Gahshomar\GregorianToJalaali::getJalaaliMonthName($month);
                        $weekday = \Gahshomar\GregorianToJalaali::getWeekdayName(abs($jy), $month, $day);
                        $output = "$weekday $day $month_name $formatted_year";
                        // در این حالت، نام تقویم مقصد نمایش داده نمی‌شود
                    } else {
                        // اگر سال معتبر است، مانند قبل ادامه دهید
                        $month_name = \Gahshomar\GregorianToJalaali::getJalaaliMonthName($month);
                        $weekday = \Gahshomar\GregorianToJalaali::getWeekdayName($jy, $month, $day);
                        $calendar_name = CUSTOM_CALENDAR_NAMES[$target_calendar];
                        $output = "$weekday $day $month_name $jy $calendar_name";
                    }
                }
            } else {
            $output = "شما گاهشماری های یکسانی را انتخاب کرده اید !";
        }
        wp_send_json_success(['output' => $output]);
    } catch (Exception $e) {
        wp_send_json_error(['message' => __('در فرآیند تبدیل خطایی رخ داده است: ', 'gahshomar.com') . $e->getMessage()]);
    } 



}
        add_action('wp_ajax_gahshomar_converter', 'gahshomar_converter_ajax_handler');
        add_action('wp_ajax_nopriv_gahshomar_converter', 'gahshomar_converter_ajax_handler');