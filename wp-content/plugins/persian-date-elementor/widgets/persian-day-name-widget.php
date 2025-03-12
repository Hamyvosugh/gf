<?php
namespace Elementor;

if (!defined('ABSPATH')) exit;

require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
use Hekmatinasser\Verta\Verta;

class Persian_Day_Name_Widget extends Widget_Base {
    
    public function get_name() {
        return 'persian_day_name';
    }

    public function get_title() {
        return __('Persian Day Name', 'elementor');
    }

    public function get_icon() {
        return 'eicon-number-field';
    }

    public function get_categories() {
        return ['general'];
    }

    protected function register_controls() {
        // Prefix & Suffix Section
        $this->start_controls_section(
            'prefix_suffix_section',
            [
                'label' => __('Prefix & Suffix', 'elementor'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'prefix_text',
            [
                'label' => __('Before Text', 'elementor'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => 'امروز روز',
                'placeholder' => __('Text before day name', 'elementor'),
            ]
        );

        $this->add_control(
            'suffix_text',
            [
                'label' => __('After Text', 'elementor'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => 'است',
                'placeholder' => __('Text after day name', 'elementor'),
            ]
        );

        $this->end_controls_section();

        // Day Names Section
        $this->start_controls_section(
            'content_section',
            [
                'label' => __('Day Names', 'elementor'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        for($i = 1; $i <= 31; $i++) {
            $this->add_control(
                'day_' . $i,
                [
                    'label' => __('Day ' . $i, 'elementor'),
                    'type' => \Elementor\Controls_Manager::TEXT,
                    'default' => '',
                    'placeholder' => __('Enter name for day ' . $i, 'elementor'),
                ]
            );
        }

        $this->end_controls_section();

        // Style Section - Container
        $this->start_controls_section(
            'style_section_container',
            [
                'label' => __('Container Style', 'elementor'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'align',
            [
                'label' => __('Alignment', 'elementor'),
                'type' => \Elementor\Controls_Manager::CHOOSE,
                'options' => [
                    'right' => [
                        'title' => __('Right', 'elementor'),
                        'icon' => 'eicon-text-align-right',
                    ],
                    'center' => [
                        'title' => __('Center', 'elementor'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'left' => [
                        'title' => __('Left', 'elementor'),
                        'icon' => 'eicon-text-align-left',
                    ],
                ],
                'default' => 'center',
                'selectors' => [
                    '{{WRAPPER}} .persian-day-name-container' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Section - Prefix
        $this->start_controls_section(
            'style_section_prefix',
            [
                'label' => __('Before Text Style', 'elementor'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'prefix_typography',
                'selector' => '{{WRAPPER}} .prefix-text',
            ]
        );

        $this->add_control(
            'prefix_color',
            [
                'label' => __('Text Color', 'elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#333863',
                'selectors' => [
                    '{{WRAPPER}} .prefix-text' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Section - Day Name
        $this->start_controls_section(
            'style_section_day',
            [
                'label' => __('Day Name Style', 'elementor'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'day_typography',
                'selector' => '{{WRAPPER}} .day-name',
            ]
        );

        $this->add_control(
            'day_color',
            [
                'label' => __('Text Color', 'elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#333863',
                'selectors' => [
                    '{{WRAPPER}} .day-name' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Section - Suffix
        $this->start_controls_section(
            'style_section_suffix',
            [
                'label' => __('After Text Style', 'elementor'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'suffix_typography',
                'selector' => '{{WRAPPER}} .suffix-text',
            ]
        );

        $this->add_control(
            'suffix_color',
            [
                'label' => __('Text Color', 'elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#333863',
                'selectors' => [
                    '{{WRAPPER}} .suffix-text' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        
        // Get current date in Persian calendar
        $v = Verta::now();
        $current_day = $v->day;
        
        // Get the custom name for current day
        $day_name = $settings['day_' . $current_day];
        
        if (!empty($day_name)) {
            echo '<div class="persian-day-name-container">';
            echo '<span class="prefix-text">' . $settings['prefix_text'] . ' </span>';
            echo '<span class="day-name">' . $day_name . '</span>';
            echo '<span class="suffix-text"> ' . $settings['suffix_text']  . '</span>';
            echo '</div>';
        }
    }

    private function convert_digits_to_persian($number) {
        $persian_digits = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        $latin_digits = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        return str_replace($latin_digits, $persian_digits, $number);
    }
}