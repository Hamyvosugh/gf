<?php
namespace Elementor;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
use Hekmatinasser\Verta\Verta;

class Persian_Date_Widget extends Widget_Base {

    public function get_name() {
        return 'persian_date';
    }

    public function get_title() {
        return __('Persian Date', 'elementor');
    }

    public function get_icon() {
        return 'eicon-calendar';
    }

    public function get_categories() {
        return ['general'];
    }

    protected function register_controls() {
        $this->start_controls_section(
            'content_section',
            [
                'label' => __('Content', 'elementor'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'title',
            [
                'label' => __('Title', 'elementor'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __('Persian Date', 'elementor'),
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        // Fetch the user's timezone
        $timezone = $this->get_user_timezone();

        // Create a new instance of Verta for the current date and time in the user's timezone
        $v = Verta::now($timezone);
        $day = $v->formatWord('l'); // Day of the week
        $dayOfMonth = $this->convert_digits_to_persian($v->day); // Day of the month in Persian digits
        $month = $v->formatWord('F'); // Month name

        // Define custom names for each day
        $custom_day_names = [
            'شنبه' => 'کیوان شید',
            'یکشنبه' => 'مهر شید',
            'دوشنبه' => 'مَه شید',
            'سه شنبه' => 'بهرام شید',
            'چهارشنبه' => 'تیر شید',
            'پنج شنبه' => 'اورمزد شید',
            'جمعه' => 'ناهید شید'
        ];

        // Check if the day is in the custom day names and replace the name
        $custom_day_name = isset($custom_day_names[$day]) ? $custom_day_names[$day] : $day;

        // If the day is Friday, change the displayed day name from 'جمعه' to 'آدینه'
        if ($day === 'جمعه') {
            $day = 'آدینه';
        }
        if ($month === 'مرداد') {
            $month = 'اَمُرداد';
        }

        // Inline CSS for dark and light modes support
        echo '<style>
        :root {
            --persian-date-color-light: #333863; /* Default color for light mode */
            --persian-date-color-dark: #ffffff;  /* Default color for dark mode */
            --persian-month-color-light: #333863;
            --persian-month-color-dark: #ffffff;
            --persian-day-color-light: #333863;
            --persian-day-color-dark: #ffffff;
            --persian-day-of-week-color-light: #dcdcdc;
            --persian-day-of-week-color-dark: #dcdcdc;
        }

        body.light-mode {
            --persian-date-color: var(--persian-date-color-light);
            --persian-month-color: var(--persian-month-color-light);
            --persian-day-color: var(--persian-day-color-light);
            --persian-day-of-week-color: var(--persian-day-of-week-color-light);
        }

        body.dark-mode {
            --persian-date-color: var(--persian-date-color-dark);
            --persian-month-color: var(--persian-month-color-dark);
            --persian-day-color: var(--persian-day-color-dark);
            --persian-day-of-week-color: var(--persian-day-of-week-color-dark);
        }

        .persian-date {
            text-align: center;
            font-size: 1.8em; /* Adjusted font size */
            color: var(--persian-date-color); /* Use variable color */
            font-family: "Digi Hamishe", sans-serif;
            width: 100%; /* Ensure it takes the full width */
            background-color: transparent; /* Transparent background */
        }

        .persian-date .first-row {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .persian-month {
            font-size: 2.2em; /* Adjusted font size */
            font-weight: bold;
            background-color: transparent; /* Transparent background */
            color: var(--persian-month-color); /* Use variable color */
            margin-right: 10px; /* Adjust margin between elements */
        }

        .persian-day {
            font-size: 60px; /* Adjusted font size */
            font-weight: bold;
            color: var(--persian-day-color); /* Use variable color */
            background-color: transparent; /* Transparent background */
        }

        .persian-day-of-week {
            font-size: 1.2em; /* Adjusted font size */
                        font-weight: bold;

            color: var(--persian-day-of-week-color); /* Use variable color */
            margin-top: -5px;
            background-color: transparent; /* Transparent background */
            text-align: center;
        }

        .persian-date span {
            display: inline-block;
            background-color: transparent; /* Transparent background */
        }
        </style>';

        echo '<div class="persian-date">';
        echo '<div class="first-row">';
        echo '<span class="persian-month">' . $month . '</span>';
        echo '<span class="persian-day">' . $dayOfMonth . '</span>';
        echo '</div>';
        echo '<div class="persian-day-of-week">' . $custom_day_name . ' (' . $day . ')</div>';
        echo '</div>';
    }

    private function convert_digits_to_persian($number) {
        $persian_digits = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        $latin_digits = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        return str_replace($latin_digits, $persian_digits, $number);
    }

    private function get_user_timezone() {
        try {
            $ip = $_SERVER['REMOTE_ADDR'];
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => "https://ipapi.co/{$ip}/json",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_TIMEOUT => 5,
                CURLOPT_HTTPHEADER => ['Accept: application/json']
            ]);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($response && $httpCode === 200 && ($data = json_decode($response, true))) {
                if (!empty($data['timezone'])) {
                    return $data['timezone'];
                }
            }
            
            // Fallback to JavaScript for client-side timezone
            echo "<script>
                document.cookie = 'user_timezone=' + Intl.DateTimeFormat().resolvedOptions().timeZone;
            </script>";
            
            if (isset($_COOKIE['user_timezone'])) {
                return $_COOKIE['user_timezone'];
            }
            
            // Last resort - get from browser
            return isset($_SERVER['HTTP_ACCEPT_TIMEZONE']) ? $_SERVER['HTTP_ACCEPT_TIMEZONE'] : date_default_timezone_get();
            
        } catch (\Exception $e) {
            return date_default_timezone_get();
        }
    }

    protected function _content_template() {
        ?>
        <#
        view.addInlineEditingAttributes( 'title', 'none' );
        #>
        <div class="persian-date">
            <div class="first-row">
                <span class="persian-month">{{{ settings.persian_month }}}</span>
                <span class="persian-day">{{{ settings.persian_day }}}</span>
            </div>
            <div class="persian-day-of-week">{{{ settings.persian_day_of_week }}}</div>
        </div>
        <?php
    }
}