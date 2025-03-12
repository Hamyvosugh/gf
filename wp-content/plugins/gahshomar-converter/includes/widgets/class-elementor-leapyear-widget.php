<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

class Elementor_LeapYear_Widget extends Widget_Base {

    public function get_name() {
        return 'leap_year';
    }

    public function get_title() {
        return __('سال کبیسه', 'gahshomar-converter');
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

        $this->end_controls_section();
    }

    protected function render() {
        ?>
        <div id="leap-year-widget">
            <h2>سال کبیسه</h2>
            <div id="leap-year-result"></div>
        </div>
        <?php
    }
}