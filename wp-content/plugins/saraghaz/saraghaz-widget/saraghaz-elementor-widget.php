<?php
if (!defined('ABSPATH')) {
    exit;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Hekmatinasser\Verta\Verta;

class Saraghaz_Elementor_Persian_Date_Widget extends Widget_Base {
    public function get_name() {
        return 'saraghaz_persian_date_widget';
    }

    public function get_title() {
        return __('Persian Date Widget', 'text_domain');
    }

    public function get_icon() {
        return 'eicon-calendar';
    }

    public function get_categories() {
        return ['general'];
    }

    protected function _register_controls() {
        $this->start_controls_section(
            'content_section',
            [
                'label' => __('Content', 'text_domain'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'title',
            [
                'label' => __('Title', 'text_domain'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __('Persian Date', 'text_domain'),
            ]
        );

        $repeater = new \Elementor\Repeater();

        $repeater->add_control(
            'calendar_type',
            [
                'label' => __('Calendar Type', 'text_domain'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __('Calendar', 'text_domain'),
            ]
        );
    
        $repeater->add_responsive_control(
            'font_size',
            [
                'label' => __('Font Size (em)', 'text_domain'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['em'],
                'default' => [
                    'size' => 1.5,
                    'unit' => 'em',
                ],
                'range' => [
                    'em' => [
                        'min' => 0.5,
                        'max' => 5,
                        'step' => 0.1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}} p' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
    
        $this->add_control(
            'calendars',
            [
                'label' => __('Calendar Items', 'text_domain'),
                'type' => \Elementor\Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'calendar_type' => __('Calendar 1', 'text_domain'),
                        'font_size' => ['size' => 1.5, 'unit' => 'em'],
                    ],
                ],
                'title_field' => '{{{ calendar_type }}}',
            ]
        );
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        if (!class_exists('Hekmatinasser\Verta\Verta')) {
            echo 'Verta class not found';
            return;
        }

        echo '<div class="widget-content">';

        if (!empty($settings['title'])) {
            echo '<h2>' . $settings['title'] . '</h2>';
        }

        foreach ($settings['calendars'] as $index => $calendar) {
            $repeater_setting_key = $this->get_repeater_setting_key('font_size', 'calendars', $index);
            $this->add_render_attribute($repeater_setting_key, 'class', [
                'calendar-item',
                'elementor-repeater-item-' . $calendar['_id']
            ]);
            
            $this->add_render_attribute($repeater_setting_key, 'style', 
                'background-color:' . esc_attr($calendar['background_color']) . ';' .
                'color:' . esc_attr($calendar['font_color']) . ';' .
                'height:' . esc_attr($calendar['height']) . 'px;'
            );

            $link_start = !empty($calendar['calendar_link']['url']) ? '<a href="' . esc_url($calendar['calendar_link']['url']) . '"' . ($calendar['calendar_link']['is_external'] ? ' target="_blank"' : '') . '>' : '';
            $link_end = !empty($calendar['calendar_link']['url']) ? '</a>' : '';

            echo $link_start;
            echo '<div ' . $this->get_render_attribute_string($repeater_setting_key) . '>';
            echo '<p>' . esc_html($calendar['calendar_type']) . '</p>';
            echo '</div>';
            echo $link_end;
        }

        try {
            $v = new Verta();
            // Rest of your date conversion code...
            
            function convertToPersianNumerals($string) {
                $numMap = [
                    '0' => '۰', '1' => '۱', '2' => '۲', '3' => '۳', '4' => '۴',
                    '5' => '۵', '6' => '۶', '7' => '۷', '8' => '۸', '9' => '۹'
                ];
                return strtr($string, $numMap);
            }

            function adjustMonthName($month) {
                $monthMapping = ['مرداد' => 'اَمُرداد'];
                return isset($monthMapping[$month]) ? $monthMapping[$month] : $month;
            }

            $dayOfWeek = $v->formatWord('l');
            $dayMapping = [
                'شنبه' => 'کیوان شید (شنبه)',
                'یکشنبه' => 'مهر شید (یک‌شنبه)',
                'دو‌شنبه' => 'مَه شید (دو‌شنبه)',
                'سه‌شنبه' => 'بهرام شید (سه‌شنبه)',
                'چهارشنبه' => '(چهارشنبه) تیر شید ',
                'پنج‌شنبه' => 'هرمز شید (پنج‌شنبه)',
                'جمعه' => 'ناهید شید (آدینه)'
            ];

            $ancientDay = isset($dayMapping[$dayOfWeek]) ? $dayMapping[$dayOfWeek] : $dayOfWeek;
            $persianDate = $v->format('j F Y');
            $persianDateWithNumerals = convertToPersianNumerals($persianDate);
            $persianDateWithAdjustedMonth = preg_replace_callback(
                '/\b(مرداد)\b/u',
                function($matches) {
                    return adjustMonthName($matches[1]);
                },
                $persianDateWithNumerals
            );

            echo '<p>' . $ancientDay . ' ' . $persianDateWithAdjustedMonth . '</p>';
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }

        echo '</div>';
    }
}