<?php
/**
 * Plugin Name: Farakhor Today Events
 * Plugin URI: https://yourwebsite.com
 * Description: Displays today's events from Farakhor system as a simple list for mobile
 * Version: 1.0
 * Author: Your Name
 * Text Domain: farakhor-today
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class Farakhor_Today_Events {
    
    /**
     * Constructor
     */
    public function __construct() {
        // Add shortcode
        add_shortcode('farakhor_today', array($this, 'render_today_events'));
        
        // Register scripts and styles
        add_action('wp_enqueue_scripts', array($this, 'register_assets'));
    }
    
    /**
     * Register scripts and styles
     */
    public function register_assets() {
        // Register styles
        wp_register_style(
            'farakhor-today-style',
            plugin_dir_url(__FILE__) . 'assets/css/farakhor-today.css',
            array(),
            '1.0'
        );
    }
    
    /**
     * Render the today events shortcode
     */
    public function render_today_events($atts) {
        // Extract attributes
        $atts = shortcode_atts(array(
            'title' => 'رویدادهای امروز',
            'limit' => 10,
        ), $atts);
        
        // Enqueue required assets
        wp_enqueue_style('farakhor-today-style');
        
        // Get today's events
        $events = $this->get_today_events($atts['limit']);
        
        // Get today's Persian date
        $today_date = $this->get_today_persian_date();
        
        // Start output buffering
        ob_start();
        
        // Include the template
        include plugin_dir_path(__FILE__) . 'templates/today-events.php';
        
        // Return the buffered content
        return ob_get_clean();
    }
    
    /**
     * Get today's Persian date formatted
     */
    private function get_today_persian_date() {
        // Make sure Verta library is loaded
        if (!class_exists('Hekmatinasser\Verta\Verta')) {
            require_once($_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php');
        }
        
        $today = new \Hekmatinasser\Verta\Verta();
        
        $persianMonths = [
            "01" => "فروردین",
            "02" => "اردیبهشت",
            "03" => "خرداد",
            "04" => "تیر", 
            "05" => "امرداد",
            "06" => "شهریور",
            "07" => "مهر",
            "08" => "آبان",
            "09" => "آذر",
            "10" => "دی",
            "11" => "بهمن", 
            "12" => "اسپند"
        ];
        
        $today_day = $this->convert_numbers_to_persian($today->format('d'));
        $today_month = $persianMonths[$today->format('m')];
        
        return "$today_day $today_month";
    }
    
    /**
     * Convert English numbers to Persian
     */
    private function convert_numbers_to_persian($string) {
        if (function_exists('convertNumbersToPersian')) {
            return convertNumbersToPersian($string);
        }
        
        $persian_numbers = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        $english_numbers = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        return str_replace($english_numbers, $persian_numbers, $string);
    }
    
    /**
     * Get today's events
     */
    private function get_today_events($limit = 10) {
        global $wpdb;
        
        // Make sure Verta library is loaded
        if (!class_exists('Hekmatinasser\Verta\Verta')) {
            require_once($_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php');
        }
        
        $public_table = $wpdb->prefix . 'farakhor';
        $private_table = $wpdb->prefix . 'user_events';
        
        // Get today's date in Persian (Jalali)
        $today = new \Hekmatinasser\Verta\Verta();
        $today_month = $today->format('m');
        $today_day = $today->format('d');
        $today_persian_format = sprintf("%02d-%02d", intval($today_month), intval($today_day));
        
        // Fetch public events (YES and POST) for today
        $public_events = $wpdb->get_results($wpdb->prepare(
            "SELECT *, 'public' as event_type FROM $public_table 
            WHERE (module = 'YES' OR module = 'POST') AND persian_day = %s
            LIMIT %d",
            $today_persian_format,
            $limit
        ));
        
        // Fetch private events for logged-in user
        $private_events = array();
        if (is_user_logged_in()) {
            $current_user = wp_get_current_user();
            
            // Check if get_event_user_data function exists
            if (function_exists('get_event_user_data')) {
                $user_data = get_event_user_data($current_user->ID);
                
                if ($user_data) {
                    $private_events = $wpdb->get_results($wpdb->prepare(
                        "SELECT *, 'private' as event_type FROM $private_table 
                        WHERE user_id = %d AND event_date = %s
                        LIMIT %d",
                        $user_data['user_id'],
                        $today_persian_format,
                        $limit
                    ));
                }
            }
        }
        
        // Merge public and private events
        $all_events = array_merge($public_events, $private_events);
        
        // Limit total events to specified limit
        if (count($all_events) > $limit) {
            $all_events = array_slice($all_events, 0, $limit);
        }
        
        return $all_events;
    }
}

// Initialize the plugin
$farakhor_today_events = new Farakhor_Today_Events();