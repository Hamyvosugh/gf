<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

class Elementor_Hindu_Year_Widget extends Widget_Base {

    public function get_name() {
        return 'hindu_year';
    }

    public function get_title() {
        return __('نام سال هندو', 'gahshomar-converter');
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
                'label' => __('محتوا', 'gahshomar-converter'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'hindu_year',
            [
                'label' => __('نام سال هندو', 'gahshomar-converter'),
                'type' => Controls_Manager::TEXT,
                'default' => '',
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $hindu_year = $this->get_settings_for_display('hindu_year');

        echo '<div class="gahshomar-hindu-year-widget">';
        echo '<h3>' . __('نام سال هندو', 'gahshomar-converter') . '</h3>';
        echo '<p>' . $hindu_year . '</p>';
        echo '</div>';
    }

    protected function content_template() {}
}