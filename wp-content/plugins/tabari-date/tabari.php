<?php
/**
 * Plugin Name: Tabari Calendar Date
 * Description: Plugin to show current date in Tabari calendar
 * Version: 1.0
 * Author: Hamy Vosugh
 * Text Domain: tabari-date
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Tabari_Calendar_Date {
    /**
     * Constructor
     */
    public function __construct() {
        // Register shortcode
        add_shortcode('tabari_date', array($this, 'tabari_date_shortcode'));
        
        // Register widget
        add_action('widgets_init', array($this, 'register_tabari_widget'));
        
        // Add CSS
        add_action('wp_enqueue_scripts', array($this, 'enqueue_styles'));
    }
    
    /**
     * Enqueue styles
     */
    public function enqueue_styles() {
        wp_enqueue_style(
            'tabari-date-style',
            plugin_dir_url(__FILE__) . 'css/tabari-date.css',
            array(),
            '1.0'
        );
    }
    
    /**
     * Convert Jalali to Tabari date
     */
    public function convert_jalali_to_tabari($jalali_year, $jalali_month, $jalali_day) {
        // Calculate Tabari year (132 years difference)
        $tabari_year = $jalali_year + 132;
        
        // Define Tabari month names
        $tabari_months = [
            'فردینه ما', // 2 مرداد - 31 مرداد
            'کرچه ما',   // 1 شهریور - 30 شهریور
            'هره ما',    // 31 شهریور - 29 مهر
            'تیر ما',    // 30 مهر - 29 آبان
            'ملاره ما',  // 30 آبان - 29 آذر
            'شروینه ما', // 30 آذر - 29 دی
            'میره ما',   // 30 دی - 29 بهمن
            'اونه ما',   // 30 بهمن - 29 اسفند
            'ارکه ما',   // 6 فروردین - 4 اردیبهشت (بعد از پیتک)
            'ده ما',     // 5 اردیبهشت - 3 خرداد
            'وهمنه ما',  // 4 خرداد - 2 تیر
            'نوروز ما'   // 3 تیر - 1 مرداد
        ];
        
        // Tabari date conversion based on mapping table
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
    }
    
    /**
     * Convert numbers to Persian
     */
    public function convert_numbers_to_persian($input) {
        $persian_digits = array('۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹');
        $english_digits = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
        
        return str_replace($english_digits, $persian_digits, $input);
    }
    
    /**
     * Get current Jalali date
     */
    public function get_current_jalali_date() {
        if (class_exists('Hekmatinasser\Verta\Verta')) {
            // Use Verta if available
            $verta = new \Hekmatinasser\Verta\Verta();
            return array(
                'year' => $verta->year,
                'month' => $verta->month,
                'day' => $verta->day
            );
        } else {
            // Fallback to jDateTime if available
            if (function_exists('jdate')) {
                $date = jdate('Y/n/j');
                list($year, $month, $day) = explode('/', $date);
                return array(
                    'year' => intval($year),
                    'month' => intval($month),
                    'day' => intval($day)
                );
            } else {
                // Basic Gregorian to Jalali conversion if no library is available
                $gregorian_date = date('Y-m-d');
                list($gy, $gm, $gd) = explode('-', $gregorian_date);
                
                // Simple Gregorian to Jalali conversion function
                $g_days_in_month = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
                $j_days_in_month = array(31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29);
                
                $gy = intval($gy);
                $gm = intval($gm);
                $gd = intval($gd);
                
                $g_day_no = 0;
                
                for ($i = 0; $i < $gm - 1; ++$i) {
                    $g_day_no += $g_days_in_month[$i];
                }
                
                $leap = (($gy % 4 == 0) && ($gy % 100 != 0)) || ($gy % 400 == 0);
                
                if ($leap && $gm > 2) {
                    ++$g_day_no;
                }
                
                $g_day_no += $gd;
                
                $j_day_no = $g_day_no - 79;
                
                $j_np = intval($j_day_no / 12053);
                $j_day_no %= 12053;
                
                $jy = 979 + 33 * $j_np + 4 * intval($j_day_no / 1461);
                
                $j_day_no %= 1461;
                
                if ($j_day_no >= 366) {
                    $jy += intval(($j_day_no - 1) / 365);
                    $j_day_no = ($j_day_no - 1) % 365;
                }
                
                for ($i = 0; $i < 11 && $j_day_no >= $j_days_in_month[$i]; ++$i) {
                    $j_day_no -= $j_days_in_month[$i];
                }
                
                $jm = $i + 1;
                $jd = $j_day_no + 1;
                
                return array(
                    'year' => intval($jy),
                    'month' => intval($jm),
                    'day' => intval($jd)
                );
            }
        }
    }
    
    /**
     * Get current Tabari date
     */
    public function get_current_tabari_date() {
        $jalali_date = $this->get_current_jalali_date();
        $tabari_date = $this->convert_jalali_to_tabari(
            $jalali_date['year'],
            $jalali_date['month'],
            $jalali_date['day']
        );
        
        return $tabari_date;
    }
    
    /**
     * Format Tabari date
     */
    public function format_tabari_date($tabari_date, $format = 'full') {
        $day = $this->convert_numbers_to_persian($tabari_date['day']);
        $month = $tabari_date['month'];
        $year = $this->convert_numbers_to_persian($tabari_date['year']);
        
        switch ($format) {
            case 'full':
                return "{$day} {$month} {$year} تبری";
            case 'short':
                return "{$day} {$month} {$year}";
            case 'day_month':
                return "{$day} {$month}";
            default:
                return "{$day} {$month} {$year} تبری";
        }
    }
    
    /**
     * Shortcode for displaying Tabari date
     */
    public function tabari_date_shortcode($atts) {
        $atts = shortcode_atts(
            array(
                'format' => 'full',
                'before' => '',
                'after' => '',
                'class' => 'tabari-date'
            ),
            $atts,
            'tabari_date'
        );
        
        $tabari_date = $this->get_current_tabari_date();
        $formatted_date = $this->format_tabari_date($tabari_date, $atts['format']);
        
        return '<span class="' . esc_attr($atts['class']) . '">' 
             . esc_html($atts['before'] ) 
             . $formatted_date 
             . esc_html($atts['after']) 
             . '</span>';
    }
    
    /**
     * Register the Tabari date widget
     */
    public function register_tabari_widget() {
        register_widget('Tabari_Date_Widget');
    }
}

/**
 * Tabari Date Widget
 */
class Tabari_Date_Widget extends WP_Widget {

    /**
     * Register widget with WordPress
     */
    public function __construct() {
        parent::__construct(
            'tabari_date_widget',
            __('Tabari Date', 'tabari-date'),
            array('description' => __('Displays the current date in Tabari calendar', 'tabari-date'))
        );
    }

    /**
     * Front-end display of widget
     */
    public function widget($args, $instance) {
        echo $args['before_widget'];
        

        if (!empty($instance['title'])) {
            echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
        }
        
        $tabari_plugin = new Tabari_Calendar_Date();
        $tabari_date = $tabari_plugin->get_current_tabari_date();
        $format = !empty($instance['format']) ? $instance['format'] : 'full';
        $formatted_date = $tabari_plugin->format_tabari_date($tabari_date, $format);
        
        echo '<div class="tabari-date-widget">';
        echo $formatted_date;
        echo '</div>';
        
        echo $args['after_widget'];
    }

    /**
     * Back-end widget form
     */
    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __('Tabari Date', 'tabari-date');
        $format = !empty($instance['format']) ? $instance['format'] : 'full';
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php _e('Title:', 'tabari-date'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('format')); ?>"><?php _e('Format:', 'tabari-date'); ?></label>
            <select class="widefat" id="<?php echo esc_attr($this->get_field_id('format')); ?>" name="<?php echo esc_attr($this->get_field_name('format')); ?>">
                <option value="full" <?php selected($format, 'full'); ?>><?php _e('Full (Day Month Year Tabari)', 'tabari-date'); ?></option>
                <option value="short" <?php selected($format, 'short'); ?>><?php _e('Short (Day Month Year)', 'tabari-date'); ?></option>
                <option value="day_month" <?php selected($format, 'day_month'); ?>><?php _e('Day Month', 'tabari-date'); ?></option>
            </select>
        </p>
        <?php
    }

    /**
     * Sanitize widget form values as they are saved
     */
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? sanitize_text_field($new_instance['title']) : '';
        $instance['format'] = (!empty($new_instance['format'])) ? sanitize_text_field($new_instance['format']) : 'full';
        
        return $instance;
    }
}

// Create CSS directory and file if they don't exist
function tabari_date_create_css_file() {
    $css_dir = plugin_dir_path(__FILE__) . 'css';
    
    if (!file_exists($css_dir)) {
        mkdir($css_dir, 0755, true);
    }
    
    $css_file = $css_dir . '/tabari-date.css';
    
    if (!file_exists($css_file)) {
        $css_content = '
/* Tabari Date Plugin Styles */
.tabari-date {
    font-family:Digi Hamishe Bold, sans-serif;
    font-size: 3rem;
    direction: rtl;
    border-radius: 4px;
    display: inline-block;
    background: url(\'https://gahshomar.com/wp-content/uploads/2024/10/ilami-gahshomar-1.webp\') no-repeat center center;
    background-size: cover;
    width: 60vw;
    height: 25vh;
    border-radius: 20px;
    color: white;
    padding-top: 3rem;
    padding-right: 1rem;
    
    
}


.tabari-date-widget {
    font-family:Digi Hamishe Bold, sans-serif;
    direction: rtl;
    text-align: center;
    padding: 10px;
    background-color: #f9f9f9;
    border-radius: 4px;
}

';
        file_put_contents($css_file, $css_content);
    }
}

// Run when plugin is activated
register_activation_hook(__FILE__, 'tabari_date_create_css_file');

// Initialize the plugin
$tabari_calendar_date = new Tabari_Calendar_Date();