<?php
/**
 * Plugin Name: Gregorian Date Display
 * Plugin URI: https://example.com/gregorian-date-display
 * Description: A simple plugin to display the current Gregorian date with a shortcode [gregorian_date].
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: https://example.com
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: gregorian-date-display
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// No font enqueuing needed as the font is already installed on the website

/**
 * Function to display the Gregorian date
 *
 * @param array $atts Shortcode attributes
 * @return string Formatted date string
 */
function gregorian_date_shortcode($atts) {
    // Extract and merge attributes
    $atts = shortcode_atts(
        array(
            'format' => 'l j F Y', // Default format: "Monday 13 July 2025"
            'locale' => '',        // Locale can be specified, or auto-detected
            'country' => '',       // Country can be specified, or auto-detected
        ),
        $atts,
        'gregorian_date'
    );
    
    // Days and months translations for specific countries
    $translations = array(
        // German (Germany)
        'de' => array(
            'days' => array(
                'Monday' => 'Montag',
                'Tuesday' => 'Dienstag',
                'Wednesday' => 'Mittwoch',
                'Thursday' => 'Donnerstag',
                'Friday' => 'Freitag',
                'Saturday' => 'Samstag',
                'Sunday' => 'Sonntag'
            ),
            'months' => array(
                'January' => 'Januar',
                'February' => 'Februar',
                'March' => 'März',
                'April' => 'April',
                'May' => 'Mai',
                'June' => 'Juni',
                'July' => 'Juli',
                'August' => 'August',
                'September' => 'September',
                'October' => 'Oktober',
                'November' => 'November',
                'December' => 'Dezember'
            )
        ),
        // Italian (Italy)
        'it' => array(
            'days' => array(
                'Monday' => 'Lunedì',
                'Tuesday' => 'Martedì',
                'Wednesday' => 'Mercoledì',
                'Thursday' => 'Giovedì',
                'Friday' => 'Venerdì',
                'Saturday' => 'Sabato',
                'Sunday' => 'Domenica'
            ),
            'months' => array(
                'January' => 'Gennaio',
                'February' => 'Febbraio',
                'March' => 'Marzo',
                'April' => 'Aprile',
                'May' => 'Maggio',
                'June' => 'Giugno',
                'July' => 'Luglio',
                'August' => 'Agosto',
                'September' => 'Settembre',
                'October' => 'Ottobre',
                'November' => 'Novembre',
                'December' => 'Dicembre'
            )
        ),
        // French (France)
        'fr' => array(
            'days' => array(
                'Monday' => 'Lundi',
                'Tuesday' => 'Mardi',
                'Wednesday' => 'Mercredi',
                'Thursday' => 'Jeudi',
                'Friday' => 'Vendredi',
                'Saturday' => 'Samedi',
                'Sunday' => 'Dimanche'
            ),
            'months' => array(
                'January' => 'Janvier',
                'February' => 'Février',
                'March' => 'Mars',
                'April' => 'Avril',
                'May' => 'Mai',
                'June' => 'Juin',
                'July' => 'Juillet',
                'August' => 'Août',
                'September' => 'Septembre',
                'October' => 'Octobre',
                'November' => 'Novembre',
                'December' => 'Décembre'
            )
        ),
        // Dutch (Netherlands, Belgium)
        'nl' => array(
            'days' => array(
                'Monday' => 'Maandag',
                'Tuesday' => 'Dinsdag',
                'Wednesday' => 'Woensdag',
                'Thursday' => 'Donderdag',
                'Friday' => 'Vrijdag',
                'Saturday' => 'Zaterdag',
                'Sunday' => 'Zondag'
            ),
            'months' => array(
                'January' => 'Januari',
                'February' => 'Februari',
                'March' => 'Maart',
                'April' => 'April',
                'May' => 'Mei',
                'June' => 'Juni',
                'July' => 'Juli',
                'August' => 'Augustus',
                'September' => 'September',
                'October' => 'Oktober',
                'November' => 'November',
                'December' => 'December'
            )
        ),
        // Swedish (Sweden)
        'sv' => array(
            'days' => array(
                'Monday' => 'Måndag',
                'Tuesday' => 'Tisdag',
                'Wednesday' => 'Onsdag',
                'Thursday' => 'Torsdag',
                'Friday' => 'Fredag',
                'Saturday' => 'Lördag',
                'Sunday' => 'Söndag'
            ),
            'months' => array(
                'January' => 'Januari',
                'February' => 'Februari',
                'March' => 'Mars',
                'April' => 'April',
                'May' => 'Maj',
                'June' => 'Juni',
                'July' => 'Juli',
                'August' => 'Augusti',
                'September' => 'September',
                'October' => 'Oktober',
                'November' => 'November',
                'December' => 'December'
            )
        ),
        // Turkish (Turkey)
        'tr' => array(
            'days' => array(
                'Monday' => 'Pazartesi',
                'Tuesday' => 'Salı',
                'Wednesday' => 'Çarşamba',
                'Thursday' => 'Perşembe',
                'Friday' => 'Cuma',
                'Saturday' => 'Cumartesi',
                'Sunday' => 'Pazar'
            ),
            'months' => array(
                'January' => 'Ocak',
                'February' => 'Şubat',
                'March' => 'Mart',
                'April' => 'Nisan',
                'May' => 'Mayıs',
                'June' => 'Haziran',
                'July' => 'Temmuz',
                'August' => 'Ağustos',
                'September' => 'Eylül',
                'October' => 'Ekim',
                'November' => 'Kasım',
                'December' => 'Aralık'
            )
        ),
        // Hindi (India)
        'hi' => array(
            'days' => array(
                'Monday' => 'सोमवार',
                'Tuesday' => 'मंगलवार',
                'Wednesday' => 'बुधवार',
                'Thursday' => 'गुरुवार',
                'Friday' => 'शुक्रवार',
                'Saturday' => 'शनिवार',
                'Sunday' => 'रविवार'
            ),
            'months' => array(
                'January' => 'जनवरी',
                'February' => 'फरवरी',
                'March' => 'मार्च',
                'April' => 'अप्रैल',
                'May' => 'मई',
                'June' => 'जून',
                'July' => 'जुलाई',
                'August' => 'अगस्त',
                'September' => 'सितंबर',
                'October' => 'अक्टूबर',
                'November' => 'नवंबर',
                'December' => 'दिसंबर'
            )
        ),
        // Chinese (China)
        'zh' => array(
            'days' => array(
                'Monday' => '星期一',
                'Tuesday' => '星期二',
                'Wednesday' => '星期三',
                'Thursday' => '星期四',
                'Friday' => '星期五',
                'Saturday' => '星期六',
                'Sunday' => '星期日'
            ),
            'months' => array(
                'January' => '一月',
                'February' => '二月',
                'March' => '三月',
                'April' => '四月',
                'May' => '五月',
                'June' => '六月',
                'July' => '七月',
                'August' => '八月',
                'September' => '九月',
                'October' => '十月',
                'November' => '十一月',
                'December' => '十二月'
            )
        )
    );
    
    // Country to language code mapping
    $country_to_lang = array(
        'DE' => 'de', // Germany
        'AT' => 'de', // Austria (German)
        'CH' => 'de', // Switzerland (using German, though they have multiple languages)
        'IT' => 'it', // Italy
        'FR' => 'fr', // France
        'BE' => 'nl', // Belgium (using Dutch, though they have multiple languages)
        'NL' => 'nl', // Netherlands
        'SE' => 'sv', // Sweden
        'TR' => 'tr', // Turkey
        'IN' => 'hi', // India (using Hindi, though they have multiple languages)
        'CN' => 'zh', // China
    );
    
    // Get current date in English format first
    $day_name = date('l');
    $day_num = date('j');
    $month_name = date('F');
    $year = date('Y');
    
    // Format the date in English by default
    $date_en = "$day_name $day_num $month_name $year";
    
    // For testing/debugging purposes - force a specific country code
    // IMPORTANT: Set this to your desired country code for testing
    $debug_country = 'DE'; // Example: Germany
    
    // Determine country code - either from parameter, locale, IP detection, or debug override
    $country_code = '';
    
    if (!empty($atts['country'])) {
        // Use specified country
        $country_code = strtoupper($atts['country']);
    } else if (!empty($atts['locale'])) {
        // Try to extract country from locale
        $parts = explode('_', $atts['locale']);
        if (count($parts) > 1) {
            $country_code = strtoupper($parts[1]);
        }
    } else {
        // For immediate testing, use the debug country code
        // In production, you would use the IP detection instead
        $country_code = $debug_country;
        
        // IP detection code (commented out for now)
        // $country_code = gregorian_date_get_country_from_ip();
    }
    
    // Get language code based on country
    $lang_code = isset($country_to_lang[$country_code]) ? $country_to_lang[$country_code] : '';
    
    // If we have translations for this language, use them
    if (!empty($lang_code) && isset($translations[$lang_code])) {
        $day_name = date('l');
        $month_name = date('F');
        
        // Translate day and month
        if (isset($translations[$lang_code]['days'][$day_name])) {
            $date_en = str_replace($day_name, $translations[$lang_code]['days'][$day_name], $date_en);
        }
        
        if (isset($translations[$lang_code]['months'][$month_name])) {
            $date_en = str_replace($month_name, $translations[$lang_code]['months'][$month_name], $date_en);
        }
    }
    
    // Return the formatted date with custom styling
    return '<span class="gregorian-date" style="font-family: Quicksand, sans-serif; font-weight: bold; font-size: 1.8rem; color: white;">' . esc_html($date_en) . '</span>';
}

/**
 * Helper function to get country code from IP address
 * This is a placeholder - you'll need to implement this with a geolocation service
 * 
 * @return string Two-letter country code or empty string if not detected
 */
function gregorian_date_get_country_from_ip() {
    // For a simple implementation, you can use a free geolocation API like ipinfo.io
    // Example (you'd need to add proper error handling):
    // $ip = $_SERVER['REMOTE_ADDR'];
    // $details = json_decode(file_get_contents("http://ipinfo.io/{$ip}/json"));
    // return isset($details->country) ? $details->country : '';
    
    // For now, return empty string as placeholder
    return '';
}

// Register the shortcode
add_shortcode('gregorian_date', 'gregorian_date_shortcode');

/**
 * Add settings page for the plugin
 */
function gregorian_date_menu() {
    add_options_page(
        'Gregorian Date Display Settings',
        'Gregorian Date',
        'manage_options',
        'gregorian-date-display',
        'gregorian_date_settings_page'
    );
}
add_action('admin_menu', 'gregorian_date_menu');

/**
 * Settings page callback
 */
function gregorian_date_settings_page() {
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <div class="card">
            <h2>How to Use</h2>
            <p>Use the shortcode <code>[gregorian_date]</code> to display the current date in the format "Monday 13 July 2025".</p>
            <p>You can customize the format with additional parameters:</p>
            <ul>
                <li><code>[gregorian_date format="l j F Y"]</code> - Custom date format (default)</li>
                <li><code>[gregorian_date locale="en_US"]</code> - Set the locale for translation</li>
            </ul>
            <p>Example with all parameters: <code>[gregorian_date format="l j F Y" locale="fr_FR"]</code></p>
            <h3>Current Date Preview:</h3>
            <p><?php echo gregorian_date_shortcode(array()); ?></p>
        </div>
    </div>
    <?php
}

/**
 * Add a widget to display the Gregorian date
 */
class Gregorian_Date_Widget extends WP_Widget {

    /**
     * Register widget with WordPress
     */
    public function __construct() {
        parent::__construct(
            'gregorian_date_widget', // Base ID
            'Gregorian Date', // Widget name
            array('description' => 'Displays the current Gregorian date.') // Args
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
        
        $format = !empty($instance['format']) ? $instance['format'] : 'l j F Y';
        $locale = !empty($instance['locale']) ? $instance['locale'] : 'en_US';
        
        echo gregorian_date_shortcode(array(
            'format' => $format,
            'locale' => $locale
        ));
        
        echo $args['after_widget'];
    }

    /**
     * Back-end widget form
     */
    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : 'Gregorian Date';
        $format = !empty($instance['format']) ? $instance['format'] : 'l j F Y';
        $locale = !empty($instance['locale']) ? $instance['locale'] : 'en_US';
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>">Title:</label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('format'); ?>">Date Format:</label>
            <input class="widefat" id="<?php echo $this->get_field_id('format'); ?>" name="<?php echo $this->get_field_name('format'); ?>" type="text" value="<?php echo esc_attr($format); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('locale'); ?>">Locale (e.g. en_US, fr_FR):</label>
            <input class="widefat" id="<?php echo $this->get_field_id('locale'); ?>" name="<?php echo $this->get_field_name('locale'); ?>" type="text" value="<?php echo esc_attr($locale); ?>">
        </p>
        <?php
    }

    /**
     * Sanitize widget form values as they are saved
     */
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? sanitize_text_field($new_instance['title']) : '';
        $instance['format'] = (!empty($new_instance['format'])) ? sanitize_text_field($new_instance['format']) : 'l j F Y';
        $instance['locale'] = (!empty($new_instance['locale'])) ? sanitize_text_field($new_instance['locale']) : 'en_US';
        
        return $instance;
    }
}

// Register the widget
function register_gregorian_date_widget() {
    register_widget('Gregorian_Date_Widget');
}
add_action('widgets_init', 'register_gregorian_date_widget');