<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

use Elementor\Widget_Base;
use Hekmatinasser\Verta\Verta;

class Saraghaz_Main_Widget extends Widget_Base {

    public function get_name() {
        return 'saraghaz_main_widget';
    }

    public function get_title() {
        return __('Saraghaz Calendar Widgets', 'text_domain');
    }

    public function get_icon() {
        return 'eicon-calendar';
    }

    public function get_categories() {
        return ['general'];
    }

    protected function _register_controls() {
        require_once plugin_dir_path(__FILE__) . 'saraghaz-controls.php';
        saraghaz_register_controls($this);
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $calendars = $settings['calendars'];
        $columns = $settings['columns'];
        $gap = $settings['gap'];

        $border_radius_top = isset($settings['border_radius']['top']) ? $settings['border_radius']['top'] : '12';
        $border_radius_right = isset($settings['border_radius']['right']) ? $settings['border_radius']['right'] : '12';
        $border_radius_bottom = isset($settings['border_radius']['bottom']) ? $settings['border_radius']['bottom'] : '12';
        $border_radius_left = isset($settings['border_radius']['left']) ? $settings['border_radius']['left'] : '12';
        $border_radius_unit = isset($settings['border_radius']['unit']) ? $settings['border_radius']['unit'] : 'px';

        $date_position = isset($settings['date_position']) ? $settings['date_position'] : 'center center';

        $position_styles = [
            'top left' => 'align-items: flex-start; justify-content: flex-start;',
            'top center' => 'align-items: flex-start; justify-content: center;',
            'top right' => 'align-items: flex-start; justify-content: flex-end;',
            'center left' => 'align-items: center; justify-content: flex-start;',
            'center center' => 'align-items: center; justify-content: center;',
            'center right' => 'align-items: center; justify-content: flex-end;',
            'bottom left' => 'align-items: flex-end; justify-content: flex-start;',
            'bottom center' => 'align-items: flex-end; justify-content: center;',
            'bottom right' => 'align-items: flex-end; justify-content: flex-end;',
        ];

        echo '<div class="widget-content" style="display: flex; flex-wrap: wrap; gap: ' . $gap . 'px; background: transparent;">';
        foreach ($calendars as $calendar) {
            $background_color = $calendar['background_color'] ? $calendar['background_color'] : '#333863';
            $font_color = $calendar['font_color'] ? $calendar['font_color'] : '#FFFFFF';
            $height = $calendar['height'] ? $calendar['height'] : 200;
            $background_image = $calendar['background_image']['url'];
            $background_image_opacity = $calendar['background_image_opacity']['size'];
            $background_image_position = $calendar['background_image_position'];
            $background_image_size = $calendar['background_image_size'];
            $font_family = $calendar['font_family'] ? $calendar['font_family'] : 'Digi Hamishe Bold';
            $font_size = $calendar['font_size']['size'] ? $calendar['font_size']['size'] : 1.5;

            // Check if a link is provided and wrap the content with it
            $link_start = !empty($calendar['calendar_link']['url']) ? '<a href="' . esc_url($calendar['calendar_link']['url']) . '"' . ($calendar['calendar_link']['is_external'] ? ' target="_blank"' : '') . ' style="display: block; height: 100%; text-decoration: none; color: inherit;">' : '';
            $link_end = !empty($calendar['calendar_link']['url']) ? '</a>' : '';

            echo '<div class="calendar-item" style="
            flex: 1 1 calc(' . (100 / $columns) . '% - ' . $gap . 'px); 
            box-sizing: border-box; 
            background-color: ' . $background_color . '; 
            color: ' . $font_color . '; 
            height: ' . $height . 'px; 
            font-family: ' . $font_family . '; 
            font-size: ' . $font_size . 'em; 
            position: relative; 
            border-radius: ' . $border_radius_top . $border_radius_unit . ' ' . $border_radius_right . $border_radius_unit . ' ' . $border_radius_bottom . $border_radius_unit . ' ' . $border_radius_left . $border_radius_unit . ';
            overflow: hidden; /* Add this to ensure the background respects the border-radius */
            display: flex; ' . $position_styles[$date_position] . '">';

            echo $link_start;

            if ($background_image) {
                echo '<div style="
                    background-image: url(' . $background_image . '); 
                    background-position: ' . $background_image_position . '; 
                    background-size: ' . $background_image_size . '; 
                    opacity: ' . $background_image_opacity . '; 
                    position: absolute; 
                    top: 0; 
                    left: 0; 
                    width: 100%; 
                    height: 100%;
                    border-radius: inherit; /* Ensures the border-radius is applied to this div as well */
                    "></div>';
            }

            echo '<div style="position: relative; z-index: 1; padding: 10px;">';

            $calendar_type = $calendar['calendar_type'];
            switch ($calendar_type) {
                case 'hejri_shamsi':
                    $this->render_hejri_shamsi_date();
                    break;
                case 'padeshahi':
                    $this->render_padeshahi_date();
                    break;
                case 'maadi':
                    $this->render_maadi_date();
                    break;
                case 'elami':
                    $this->render_elami_date();
                    break;
                case 'zartoshti':
                    $this->render_zartoshti_date();
                    break;
                case 'iran_nov':
                    $this->render_iran_nov_date();
                    break;
                case 'miladi':
                    $this->render_miladi_date();
                    break;
            }
            echo '</div>';
            echo $link_end;

            echo '</div>';
        }
        echo '</div>';

        // Add the user time fetching script here
        ?>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                // Get the local date and time of the user
                var userTime = new Date();
    
                // Extract the necessary date parts
                var year = userTime.getFullYear();
                var month = userTime.getMonth() + 1; // Months are zero-indexed
                var day = userTime.getDate();
                var hours = userTime.getHours();
                var minutes = userTime.getMinutes();
                var seconds = userTime.getSeconds();
    
                // Send the time to PHP via AJAX
                var xhr = new XMLHttpRequest();
                xhr.open("POST", "<?php echo admin_url('admin-ajax.php'); ?>", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.send("action=save_user_time&year=" + year + "&month=" + month + "&day=" + day + "&hours=" + hours + "&minutes=" + minutes + "&seconds=" + seconds);
            });
        </script>
        <?php
    }

    // Now you can continue using the session-based user time in these render methods.

    protected function render_hejri_shamsi_date() {
        if (isset($_SESSION['user_time'])) {
            // Create a DateTime object from the stored session time
            $v = new Verta(new DateTime($_SESSION['user_time']));
        } else {
            // Fallback to server time if user time is not available
            $v = new Verta();
        }
        $this->output_date($v, 0, 'هجری شمسی');
    }

    protected function render_padeshahi_date() {
        if (isset($_SESSION['user_time'])) {
            $v = new Verta($_SESSION['user_time']);
        } else {
            $v = new Verta();
        }
        $this->output_date($v, 1180, 'شاهنشاهی');
    }

    protected function render_maadi_date() {
        if (isset($_SESSION['user_time'])) {
            $v = new Verta($_SESSION['user_time']);
        } else {
            $v = new Verta();
        }

        // CORDI day names for the Madi calendar mapped to Persian days of the week
        $maadi_day_names = [
            "شه‌ممه",      // Saturday
            "یه‌کشه‌ممه",  // Sunday
            "دووشه‌ممه",   // Monday
            "سێشەممه",    // Tuesday
            "چوارشه‌ممه", // Wednesday
            "پێنجشه‌ممه",  // Thursday
            "هه‌ینی"       // Friday
        ];

        // Get the current Persian day of the week (Saturday = 0, ..., Friday = 6)
        $current_day_of_week = (int) $v->format('w'); // 'w' returns 0 for Saturday, 6 for Friday

        // Get the CORDI day name based on the current Persian day of the week
        $maadi_day_name = $maadi_day_names[$current_day_of_week];

        // Output the Madi calendar date with the day name appended
        $this->output_date_with_day_name($v, 1321, 'کردی', $maadi_day_name);
    }

    protected function render_elami_date() {
        if (isset($_SESSION['user_time'])) {
            $v = new Verta($_SESSION['user_time']);
        } else {
            $v = new Verta();
        }
        $this->output_date($v, 3821, 'ایلامی');
    }

    protected function render_zartoshti_date() {
        if (isset($_SESSION['user_time'])) {
            $v = new Verta($_SESSION['user_time']);
        } else {
            $v = new Verta();
        }

        // Get the current day of the Persian month (1-30)
        $current_day_of_month = (int) $v->format('j');

        // Zartoshti day names array
        $zartoshti_day_names = [
            "هرمزد", "بهمن", "اردیبهشت", "شهریور", "سپندارمذ", "خورداد", "امرداد",
            "دی به آذر", "آذر", "آبان", "خور", "ماه", "تیر", "گوش", "دی به مهر",
            "مهر", "سروش", "رشن", "فروردین", "بهرام", "رام", "باد", "دی به دین",
            "دین", "ارد", "اشتاد", "آسمان", "زامیاد", "مانتره سپند", "انارم"
        ];

        // Validate the day of the month to prevent out-of-bounds errors
        if ($current_day_of_month >= 1 && $current_day_of_month <= 30) {
            // Get the Zartoshti day name based on the current day
            $zartoshti_day_name = $zartoshti_day_names[$current_day_of_month - 1];
        } else {
            $zartoshti_day_name = ''; // Fallback for invalid days
        }

        // Output the Zartoshti calendar date with the day name appended in the same line
        $this->output_date_with_day_name($v, 2359, 'زرتشتی', $zartoshti_day_name);
    }

    protected function output_date_with_day_name($v, $year_difference, $calendar_name, $zartoshti_day_name) {
        $dayOfWeek = $v->formatWord('l');
        $dayMapping = [
            'شنبه' => 'کیوان شید (شنبه)',
            'یکشنبه' => 'مهر شید (یک‌شنبه)',
            'دوشنبه' => 'مَه شید (دو‌شنبه)',
            'سه شنبه' => 'بهرام شید (سه‌شنبه)',
            'چهارشنبه' => 'تیر شید (چهار‌شنبه)',
            'پنج شنبه' => 'هرمز شید (پنج‌شنبه)',
            'جمعه' => 'ناهید شید (آدینه)'
        ];

        $ancientDay = isset($dayMapping[$dayOfWeek]) ? $dayMapping[$dayOfWeek] : $dayOfWeek;

        $currentYear = $v->format('Y');
        $calendarYear = $currentYear + $year_difference;

        $persianDate = $v->format('j F ') . $calendarYear;
        $persianDateWithNumerals = $this->convertToPersianNumerals($persianDate);

        // Append Zartoshti day name inline
        echo '<p style="text-align: right; direction: rtl; white-space: nowrap;">' . $ancientDay . ' ' . $persianDateWithNumerals . ' ' . $calendar_name . ' - نام روز : ' . $zartoshti_day_name . '</p>';
    }

    protected function render_iran_nov_date() {
        if (isset($_SESSION['user_time'])) {
            $v = new Verta($_SESSION['user_time']);
        } else {
            $v = new Verta();
        }
        $this->output_date($v, -1396, 'ایران نو');
    }

    protected function render_miladi_date() {
        if (isset($_SESSION['user_time'])) {
            $date = $_SESSION['user_time'];
        } else {
            $date = new DateTime();
        }
        $dayOfWeek = $date->format('l');
        $dayMapping = [
            'Saturday' => 'کیوان شید (شنبه)',
            'Sunday' => 'مهر شید (یک‌شنبه)',
            'Monday' => 'مَه شید (دو‌شنبه)',
            'Tuesday' => 'بهرام شید (سه‌شنبه)',
            'Wednesday' => 'تیر شید (چهارشنبه)',
            'Thursday' => 'هرمز شید (پنج‌شنبه)',
            'Friday' => 'ناهید شید (آدینه)'
        ];

        $ancientDay = isset($dayMapping[$dayOfWeek]) ? $dayMapping[$dayOfWeek] : $dayOfWeek;

        $gregorianDate = $date->format('j F Y');
        $gregorianDateWithNumerals = $this->convertToPersianNumerals($gregorianDate);
        $gregorianDateWithPersianMonth = $this->mapEnglishMonthToPersian($gregorianDateWithNumerals);

        echo '<p>' . $ancientDay . ' ' . $gregorianDateWithPersianMonth . ' میلادی' . '</p>';
    }

    protected function output_date($v, $year_difference, $calendar_name) {
        $dayOfWeek = $v->formatWord('l');
        $dayMapping = [
            'شنبه' => 'کیوان شید (شنبه)',
            'یکشنبه' => 'مهر شید (یک‌شنبه)',
            'دوشنبه' => 'مَه شید (دو‌شنبه)',
            'سه شنبه' => 'بهرام شید (سه‌شنبه)',
            'چهارشنبه' => 'تیر شید (چهار‌شنبه)',
            'پنج شنبه' => 'هرمز شید (پنج‌شنبه)',
            'جمعه' => 'ناهید شید (آدینه)'
        ];

        $ancientDay = isset($dayMapping[$dayOfWeek]) ? $dayMapping[$dayOfWeek] : $dayOfWeek;

        $currentYear = $v->format('Y');
        $calendarYear = $currentYear + $year_difference;

        $persianDate = $v->format('j F ') . $calendarYear;
        $persianDateWithNumerals = $this->convertToPersianNumerals($persianDate);

        $persianDateWithAdjustedMonth = preg_replace_callback(
            '/\b(مرداد)\b/u',
            function($matches) {
                return $this->adjustMonthName($matches[1]);
            },
            $persianDateWithNumerals
        );

        echo '<p>' . $ancientDay . ' ' . $persianDateWithAdjustedMonth . ' ' . $calendar_name . '</p>';
    }

    protected function convertToPersianNumerals($string) {
        $numMap = [
            '0' => '۰',
            '1' => '۱',
            '2' => '۲',
            '3' => '۳',
            '4' => '۴',
            '5' => '۵',
            '6' => '۶',
            '7' => '۷',
            '8' => '۸',
            '9' => '۹'
        ];

        return strtr($string, $numMap);
    }

    protected function adjustMonthName($month) {
        $monthMapping = [
            'مرداد' => 'اَمُرداد'
        ];

        return isset($monthMapping[$month]) ? $monthMapping[$month] : $month;
    }

    protected function mapEnglishMonthToPersian($dateString) {
        $monthMapping = [
            'January' => 'ژانویه',
            'February' => 'فوریه',
            'March' => 'مارس',
            'April' => 'آوریل',
            'May' => 'مه',
            'June' => 'ژوئن',
            'July' => 'جولای',
            'August' => 'اوت',
            'September' => 'سپتامبر',
            'October' => 'اکتبر',
            'November' => 'نوامبر',
            'December' => 'دسامبر'
        ];

        return strtr($dateString, $monthMapping);
    }
}

// Add the PHP function to handle AJAX requests for saving user time
add_action('wp_ajax_save_user_time', 'save_user_time');
add_action('wp_ajax_nopriv_save_user_time', 'save_user_time');

function save_user_time() {
    if (isset($_POST['year'], $_POST['month'], $_POST['day'], $_POST['hours'], $_POST['minutes'], $_POST['seconds'])) {
        // Create a DateTime object based on the user's provided time
        $user_time = new DateTime();
        $user_time->setDate(intval($_POST['year']), intval($_POST['month']), intval($_POST['day']));
        $user_time->setTime(intval($_POST['hours']), intval($_POST['minutes']), intval($_POST['seconds']));

        // Store the user time in the session for later use
        $_SESSION['user_time'] = $user_time->format('Y-m-d H:i:s');

        wp_send_json_success('User time saved');
    } else {
        wp_send_json_error('Missing time data');
    }
}