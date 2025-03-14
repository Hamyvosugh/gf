<?php
/**
 * Plugin Name: Farakhor Calendar Events
 * Plugin URI: https://example.com/plugins/farakhor-calendar-events
 * Description: Display calendar events with a beautiful card design.
 * Version: 1.0
 * Author: Your Name
 * Author URI: https://example.com
 * Text Domain: farakhor-calendar-events
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class FarakhorCalendarEvents {
    
    /**
     * Constructor
     */
    public function __construct() {
        // Register activation hook
        register_activation_hook(__FILE__, array($this, 'plugin_activation'));
        
        // Load required files
        $this->load_dependencies();
        
        // Register shortcode
        add_shortcode('farakhor_events', array($this, 'events_shortcode'));
        
        // Enqueue styles
        add_action('wp_enqueue_scripts', array($this, 'enqueue_styles'));
    }
    
    /**
     * Plugin activation
     */
    public function plugin_activation() {
        // Check if the jdatetime library is installed
        if (!class_exists('jDateTime')) {
            // Create required directory
            $lib_dir = plugin_dir_path(__FILE__) . 'lib';
            if (!file_exists($lib_dir)) {
                mkdir($lib_dir, 0755, true);
            }
            
            // Download and extract the jDateTime library
            $this->download_jdatetime_library();
        }
    }
    
    /**
     * Download jDateTime library
     */
    private function download_jdatetime_library() {
        // For simplicity, we'll include the library directly in the plugin
        // In a real-world scenario, you might want to use wp_remote_get to download it
        
        $jdatetime_content = <<<'EOD'
<?php
/**
 * Jalali (Shamsi) DateTime Class, supports years higher than 2038
 * Copyright (c) 2012, Sallar Kaboli (sallar.kaboli@gmail.com)
 *
 * This class is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * @package jDateTime
 */
class jDateTime
{
    /**
     * Defaults
     */
    private static $jalali   = true; //Use Jalali Date, If set to false, falls back to gregorian
    private static $convert  = true; //Convert numbers to Farsi characters in utf-8
    private static $timezone = null; //Timezone String e.g Asia/Tehran, Defaults to Server Timezone Settings
    private static $temp = array();

    /**
     * Convert a Gregorian date to Jalali
     * @param int $g_y
     * @param int $g_m
     * @param int $g_d
     * @return array
     */
    public static function toJalali($g_y, $g_m, $g_d)
    {
        $g_days_in_month = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
        $j_days_in_month = array(31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29);

        $gy = $g_y-1600;
        $gm = $g_m-1;
        $gd = $g_d-1;

        $g_day_no = 365*$gy+self::div($gy+3, 4)-self::div($gy+99, 100)+self::div($gy+399, 400);

        for ($i=0; $i < $gm; ++$i)
            $g_day_no += $g_days_in_month[$i];
        if ($gm>1 && (($gy%4==0 && $gy%100!=0) || ($gy%400==0)))
            $g_day_no++;
        $g_day_no += $gd;

        $j_day_no = $g_day_no-79;

        $j_np = self::div($j_day_no, 12053);
        $j_day_no = $j_day_no % 12053;

        $jy = 979+33*$j_np+4*self::div($j_day_no, 1461);

        $j_day_no %= 1461;

        if ($j_day_no >= 366) {
            $jy += self::div($j_day_no-1, 365);
            $j_day_no = ($j_day_no-1)%365;
        }

        for ($i = 0; $i < 11 && $j_day_no >= $j_days_in_month[$i]; ++$i)
            $j_day_no -= $j_days_in_month[$i];
        $jm = $i+1;
        $jd = $j_day_no+1;

        return array($jy, $jm, $jd);
    }

    /**
     * Convert a Jalali date to Gregorian
     * @param int $j_y
     * @param int $j_m
     * @param int $j_d
     * @return array
     */
    public static function toGregorian($j_y, $j_m, $j_d)
    {
        $g_days_in_month = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
        $j_days_in_month = array(31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29);

        $jy = $j_y-979;
        $jm = $j_m-1;
        $jd = $j_d-1;

        $j_day_no = 365*$jy + self::div($jy, 33)*8 + self::div($jy%33+3, 4);
        for ($i=0; $i < $jm; ++$i)
            $j_day_no += $j_days_in_month[$i];

        $j_day_no += $jd;

        $g_day_no = $j_day_no+79;

        $gy = 1600 + 400*self::div($g_day_no, 146097);
        $g_day_no = $g_day_no % 146097;

        $leap = true;
        if ($g_day_no >= 36525) {
            $g_day_no--;
            $gy += 100*self::div($g_day_no,  36524);
            $g_day_no = $g_day_no % 36524;

            if ($g_day_no >= 365)
                $g_day_no++;
            else
                $leap = false;
        }

        $gy += 4*self::div($g_day_no, 1461);
        $g_day_no %= 1461;

        if ($g_day_no >= 366) {
            $leap = false;

            $g_day_no--;
            $gy += self::div($g_day_no, 365);
            $g_day_no = $g_day_no % 365;
        }

        for ($i = 0; $g_day_no >= $g_days_in_month[$i] + ($i == 1 && $leap); $i++)
            $g_day_no -= $g_days_in_month[$i] + ($i == 1 && $leap);
        $gm = $i+1;
        $gd = $g_day_no+1;

        return array($gy, $gm, $gd);
    }

    /**
     * Division
     * @param int $a
     * @param int $b
     * @return int
     */
    private static function div($a, $b)
    {
        return ~~($a / $b);
    }

    /**
     * Get day of the week
     * @param int $j_y
     * @param int $j_m
     * @param int $j_d
     * @return int
     */
    public static function getDayOfWeek($j_y, $j_m, $j_d)
    {
        list($g_y, $g_m, $g_d) = self::toGregorian($j_y, $j_m, $j_d);
        $dayOfWeek = date('w', mktime(0, 0, 0, $g_m, $g_d, $g_y));
        return $dayOfWeek;
    }

    /**
     * Get month name
     * @param int $month
     * @return string
     */
    public static function getMonthName($month)
    {
        $months = array(
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
        return $months[(int)$month];
    }

    /**
     * Get day name
     * @param int $day
     * @return string
     */
    public static function getDayName($day)
    {
        $days = array(
            0 => 'یکشنبه',
            1 => 'دوشنبه',
            2 => 'سه‌شنبه',
            3 => 'چهارشنبه',
            4 => 'پنج‌شنبه',
            5 => 'جمعه',
            6 => 'شنبه'
        );
        return $days[$day];
    }

    /**
     * Convert English numbers to Persian
     * @param string $str
     * @return string
     */
    public static function convertNumbers($str)
    {
        $farsi_array = array('۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹');
        $english_array = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
        return str_replace($english_array, $farsi_array, $str);
    }

    /**
     * Get current Jalali date
     * @return array
     */
    public static function getCurrentJalaliDate()
    {
        $date = date('Y-m-d');
        list($g_y, $g_m, $g_d) = explode('-', $date);
        return self::toJalali($g_y, $g_m, $g_d);
    }

    /**
     * Format Jalali date
     * @param int $j_y
     * @param int $j_m
     * @param int $j_d
     * @param bool $convert
     * @return string
     */
    public static function formatDate($j_y, $j_m, $j_d, $convert = true)
    {
        $dayOfWeek = self::getDayOfWeek($j_y, $j_m, $j_d);
        $monthName = self::getMonthName($j_m);
        $dayName = self::getDayName($dayOfWeek);
        
        $formatted = "$dayName $j_d $monthName";
        
        if ($convert) {
            $formatted = self::convertNumbers($formatted);
        }
        
        return $formatted;
    }
}
EOD;
        
        // Write the library to a file
        file_put_contents(plugin_dir_path(__FILE__) . 'lib/jDateTime.php', $jdatetime_content);
    }
    
    /**
     * Load dependencies
     */
    public function load_dependencies() {
        // Load jDateTime library
        require_once plugin_dir_path(__FILE__) . 'lib/jDateTime.php';
    }
    
    /**
     * Enqueue styles
     */
    public function enqueue_styles() {
        // Enqueue Vazirmatn font from Google Fonts
        wp_enqueue_style('vazirmatn-font', 'https://fonts.googleapis.com/css2?family=Vazirmatn:wght@300;400;500;700;900&display=swap', array(), '1.0.0');
        
        // Enqueue plugin styles
        wp_enqueue_style('farakhor-calendar-events', plugins_url('assets/css/style.css', __FILE__), array(), '1.0.0');
    }

    /**
     * Map Persian digits
     * 
     * @param string $number
     * @return string
     */
    public function to_persian_num($number) {
        $persian_digits = array('۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹');
        $english_digits = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
        
        return str_replace($english_digits, $persian_digits, $number);
    }
    
    /**
     * Calculate and format remaining days
     * 
     * @param array $current_date Current Jalali date (y, m, d)
     * @param int $event_month Event Jalali month
     * @param int $event_day Event Jalali day
     * @return string Formatted remaining days text
     */
    public function get_remaining_days($current_date, $event_month, $event_day) {
        $current_year = $current_date[0];
        $current_month = $current_date[1];
        $current_day = $current_date[2];
        
        // Create timestamps for comparison
        $event_timestamp = jDateTime::toGregorian($current_year, $event_month, $event_day);
        $event_timestamp = mktime(0, 0, 0, $event_timestamp[1], $event_timestamp[2], $event_timestamp[0]);
        
        $current_timestamp = jDateTime::toGregorian($current_year, $current_month, $current_day);
        $current_timestamp = mktime(0, 0, 0, $current_timestamp[1], $current_timestamp[2], $current_timestamp[0]);
        
        // Calculate difference in days
        $diff_days = round(($event_timestamp - $current_timestamp) / (60 * 60 * 24));
        
        // Format text
        if ($diff_days > 0) {
            return '<div class="event-remain">' . $this->to_persian_num($diff_days) . ' روز مانده</div>';
        } elseif ($diff_days < 0) {
            return '<div class="event-remain event-passed">' . $this->to_persian_num(abs($diff_days)) . ' روز گذشته</div>';
        } else {
            return '<div class="event-remain event-today">امروز</div>';
        }
    }
    
    /**
     * Format Persian date
     * 
     * @param string $persian_day Format: "m-d"
     * @return array [day, month]
     */
    public function format_persian_date($persian_day) {
        $parts = explode('-', $persian_day);
        if (count($parts) == 2) {
            $month = intval($parts[0]);
            $day = intval($parts[1]);
            
            $month_name = jDateTime::getMonthName($month);
            
            return array(
                'day' => $this->to_persian_num($day),
                'month' => $month_name,
                'month_num' => $month,
                'day_num' => $day
            );
        }
        
        return array(
            'day' => '',
            'month' => '',
            'month_num' => 0,
            'day_num' => 0
        );
    }
    
    /**
     * Format Gregorian date
     * 
     * @param string $gregorian_date Format: "Y-m-d"
     * @return string
     */
    public function format_gregorian_date($gregorian_date) {
        $date = new DateTime($gregorian_date);
        
        // Map month names
        $months = array(
            1 => 'ژانویه',
            2 => 'فوریه',
            3 => 'مارس',
            4 => 'آوریل',
            5 => 'مه',
            6 => 'ژوئن',
            7 => 'ژوئیه',
            8 => 'اوت',
            9 => 'سپتامبر',
            10 => 'اکتبر',
            11 => 'نوامبر',
            12 => 'دسامبر'
        );
        
        $month = $months[$date->format('n')];
        $day = $this->to_persian_num($date->format('j'));
        
        return $day . ' ' . $month;
    }
    
    /**
     * Events shortcode
     * 
     * @param array $atts Shortcode attributes
     * @return string HTML output
     */
    public function events_shortcode($atts) {
        global $wpdb;
        
        // Extract attributes
        $atts = shortcode_atts(array(
            'limit' => -1,
            'month' => '',
            'category' => '',
            'tag' => '',
        ), $atts);
        
        // Get current Jalali date
        $current_date = jDateTime::getCurrentJalaliDate();
        $current_month = $current_date[1];
        $current_day = $current_date[2];
        
        // If month attribute is not set, use current month
        $filter_month = !empty($atts['month']) ? intval($atts['month']) : $current_month;
        
        // Build query
        $table_name = $wpdb->prefix . 'farakhor';
        $query = "SELECT * FROM $table_name WHERE persian_day LIKE '$filter_month-%'";
        
        // Add category filter if set
        if (!empty($atts['category'])) {
            $query .= $wpdb->prepare(" AND categories LIKE %s", '%' . $atts['category'] . '%');
        }
        
        // Add tag filter if set
        if (!empty($atts['tag'])) {
            $query .= $wpdb->prepare(" AND tag LIKE %s", '%' . $atts['tag'] . '%');
        }
        
        // Add order
        $query .= " ORDER BY CAST(SUBSTRING_INDEX(persian_day, '-', -1) AS UNSIGNED)";
        
        // Add limit if set
        if ($atts['limit'] > 0) {
            $query .= $wpdb->prepare(" LIMIT %d", $atts['limit']);
        }
        
        // Get events
        $events = $wpdb->get_results($query);
        
        // Start output buffering
        ob_start();
        
        // Include filter form
        $this->render_filter_form($filter_month);
        
        // Display events
        echo '<div class="farakhor-events-container">';
        
        if (!empty($events)) {
            foreach ($events as $event) {
                // Parse persian date
                $persian_date = $this->format_persian_date($event->persian_day);
                
                // Check if this is current day event
                $is_current_day = ($persian_date['month_num'] == $current_month && $persian_date['day_num'] == $current_day);
                $card_class = $is_current_day ? 'event-card event-card-today' : 'event-card';
                
                // Display event card
                ?>
                <div class="<?php echo $card_class; ?>">
                    <div class="event-image-container">
                        <?php if (!empty($event->image)) : ?>
                        <img src="<?php echo esc_url($event->image); ?>" alt="<?php echo esc_attr($event->event_title); ?>" class="event-image">
                        <?php else : ?>
                        <img src="<?php echo plugins_url('assets/images/placeholder.jpg', __FILE__); ?>" alt="تصویر مناسبت" class="event-image">
                        <?php endif; ?>
                        
                        <?php
                        // Display category label if it's one of the specified categories
                        $categories = explode(',', $event->categories);
                        $display_categories = array('ملی', 'محلی', 'جهانی', 'خصوصی');
                        $category_displayed = false;
                        
                        foreach ($categories as $category) {
                            $category = trim($category);
                            if (in_array($category, $display_categories)) {
                                echo '<div class="event-category">' . esc_html($category) . '</div>';
                                $category_displayed = true;
                                break;
                            }
                        }
                        ?>
                    </div>
                    <div class="event-content">
                        <div class="event-date-container">
                            <div class="event-day"><?php echo $persian_date['day']; ?></div>
                            <div class="event-month"><?php echo $persian_date['month']; ?></div>
                            <div class="event-gregorian"><?php echo $this->format_gregorian_date($event->gregorian_day); ?></div>
                            <?php echo $this->get_remaining_days($current_date, $persian_date['month_num'], $persian_date['day_num']); ?>
                        </div>
                        <div class="event-main-content">
                            <div class="event-info">
                                <h3 class="event-title"><?php echo esc_html($event->event_title); ?></h3>
                                <p class="event-description"><?php echo wp_trim_words($event->event_text, 25, '...'); ?></p>
                            </div>
                            <div class="event-bottom">
                                <div class="event-occasion-type">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9" />
                                    </svg>
                                    <span><?php echo !empty($event->tag) ? esc_html($event->tag) : 'مناسبت'; ?></span>
                                </div>
                                <?php if (!empty($event->post_link)) : ?>
                                <a href="<?php echo esc_url($event->post_link); ?>" class="event-button">اطلاعات بیشتر</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }
        } else {
            echo '<div class="no-events-message">هیچ مناسبتی در این ماه یافت نشد.</div>';
        }
        
        echo '</div>';
        
        // Return buffered content
        return ob_get_clean();
    }
    
    /**
     * Render filter form
     * 
     * @param int $selected_month Currently selected month
     */
    private function render_filter_form($selected_month) {
        // Get all categories from database
        global $wpdb;
        $table_name = $wpdb->prefix . 'farakhor';
        $categories_query = "SELECT DISTINCT categories FROM $table_name WHERE categories IS NOT NULL AND categories != ''";
        $categories_results = $wpdb->get_results($categories_query);
        
        // Process categories
        $all_categories = array();
        foreach ($categories_results as $result) {
            $cats = explode(',', $result->categories);
            foreach ($cats as $cat) {
                $cat = trim($cat);
                if (!empty($cat) && !in_array($cat, $all_categories)) {
                    $all_categories[] = $cat;
                }
            }
        }
        
        // Get all tags from database
        $tags_query = "SELECT DISTINCT tag FROM $table_name WHERE tag IS NOT NULL AND tag != ''";
        $tags_results = $wpdb->get_results($tags_query);
        
        // Process tags
        $all_tags = array();
        foreach ($tags_results as $result) {
            $tag = trim($result->tag);
            if (!empty($tag) && !in_array($tag, $all_tags)) {
                $all_tags[] = $tag;
            }
        }
        
        // Render the form
        ?>
        <div class="farakhor-filter-form">
            <form method="get">
                <div class="filter-row">
                    <div class="filter-group">
                        <label for="farakhor-month">ماه:</label>
                        <select name="farakhor_month" id="farakhor-month">
                            <?php for ($i = 1; $i <= 12; $i++) : ?>
                                <option value="<?php echo $i; ?>" <?php selected($selected_month, $i); ?>>
                                    <?php echo jDateTime::getMonthName($i); ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    
                    <?php if (!empty($all_categories)) : ?>
                    <div class="filter-group">
                        <label for="farakhor-category">دسته بندی:</label>
                        <select name="farakhor_category" id="farakhor-category">
                            <option value="">همه</option>
                            <?php foreach ($all_categories as $category) : ?>
                                <option value="<?php echo esc_attr($category); ?>" <?php selected(isset($_GET['farakhor_category']) ? $_GET['farakhor_category'] : '', $category); ?>>
                                    <?php echo esc_html($category); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($all_tags)) : ?>
                    <div class="filter-group">
                        <label for="farakhor-tag">برچسب:</label>
                        <select name="farakhor_tag" id="farakhor-tag">
                            <option value="">همه</option>
                            <?php foreach ($all_tags as $tag) : ?>
                                <option value="<?php echo esc_attr($tag); ?>" <?php selected(isset($_GET['farakhor_tag']) ? $_GET['farakhor_tag'] : '', $tag); ?>>
                                    <?php echo esc_html($tag); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php endif; ?>
                    
                    <div class="filter-submit">
                        <button type="submit" class="filter-button">فیلتر</button>
                    </div>
                </div>
            </form>
        </div>
        <?php
    }
}

// Initialize the plugin
new FarakhorCalendarEvents();