<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

class Elementor_Western_Zodiac_Widget extends Widget_Base {

    public function get_name() {
        return 'western_zodiac_widget';
    }

    public function get_title() {
        return __('علامت زودیاک غربی', 'gahshomar-converter');
    }

    public function get_icon() {
        return 'eicon-star';
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
        <div id="western-zodiac-wrapper">
            <h3>علامت زودیاک غربی</h3>
            <p id="western-zodiac"></p>
        </div>
        <?php
    }
}