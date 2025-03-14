<?php
/**
 * Farakhor Events in Child Theme
 * Description: Display events from the wp_farakhor table and private events in a grid card style.
 * Version: 1.1
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// کتابخانه را از دایرکتوری root فراخوانی کنید
require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';

use Hekmatinasser\Verta\Verta;

// ثبت و بارگذاری استایل‌ها و اسکریپت‌ها
function farakhor_load_assets() {
    wp_enqueue_style('farakhor-style', get_stylesheet_directory_uri() . '/farakhor/assets/css/farakhor.css');
    wp_enqueue_script('farakhor-script', get_stylesheet_directory_uri() . '/farakhor/assets/js/Farakhor.js', array('jquery'), null, true);
    
    // گرفتن ماه جاری به صورت پیش‌فرض
    $currentPersianMonth = (new Verta())->format('m');
    wp_localize_script('farakhor-script', 'farakhorAjax', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('load_more_events_nonce'),
        'currentMonth' => $currentPersianMonth
    ));
}
add_action('wp_enqueue_scripts', 'farakhor_load_assets');

// تابع برای دریافت رویدادهای عمومی و خصوصی
function farakhor_fetch_all_events() {
    global $wpdb;
    $public_table = $wpdb->prefix . 'farakhor';
    $private_table = $wpdb->prefix . 'user_events';
    
    $currentDate = new Verta();
    $currentYear = $currentDate->format('Y');
    $currentMonth = $currentDate->format('m');
    
    // دریافت رویدادهای عمومی
    $public_events = $wpdb->get_results($wpdb->prepare(
        "SELECT *, 'public' as event_type FROM $public_table 
        WHERE module = 'YES'
        AND YEAR(gregorian_day) = %d
        AND MONTH(gregorian_day) = %d",
        $currentYear,
        $currentMonth
    ));

    // دریافت رویدادهای خصوصی برای کاربر فعلی
   // $private_events = array();
    //if (is_user_logged_in()) {
      //  $current_user = wp_get_current_user();
       // $user_data = get_event_user_data($current_user->ID);
        
       //  if ($user_data) {
         //    $private_events = $wpdb->get_results($wpdb->prepare(
         //        "SELECT *, 'private' as event_type FROM $private_table 
         //        WHERE user_id = %d 
         //        AND SUBSTRING_INDEX(event_date, '-', 1) = %s",
        //         $user_data['user_id'],
        //         $currentMonth
        //     ));
      //   }
  //   }

    // ترکیب و مرتب‌سازی رویدادها
    $all_events = array_merge($public_events);
    //$all_events = array_merge($public_events, $private_events);
    usort($all_events, function($a, $b) {
        $a_day = $a->event_type === 'public' ? 
            substr($a->persian_day, -2) : 
            substr($a->event_date, -2);
        $b_day = $b->event_type === 'public' ? 
            substr($b->persian_day, -2) : 
            substr($b->event_date, -2);
        return intval($a_day) - intval($b_day);
    });

    return $all_events;
}

// تابع برای نمایش رویدادها
function farakhor_display_events() {
    ob_start();
    $events = farakhor_fetch_all_events();
    include get_stylesheet_directory() . '/farakhor/templates/farakhor-template.php';
    return ob_get_clean();
}

// ایجاد شورت‌کد برای نمایش رویدادها
add_shortcode('display_farakhor_events', 'farakhor_display_events');

// تابع کمکی برای تبدیل اعداد انگلیسی به فارسی
if (!function_exists('convertNumbersToPersian')) {
    function convertNumbersToPersian($string) {
        $persian_numbers = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        $english_numbers = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        return str_replace($english_numbers, $persian_numbers, $string);
    }
}

function include_verta_library() {
    try {
        $today = new Verta();
    } catch (Exception $e) {
        error_log('Verta library not loaded properly: ' . $e->getMessage());
    }
}
include_verta_library();