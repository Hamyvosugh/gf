<?php

namespace Gahshomar;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

class Elementor_Test_Widget extends Widget_Base {

    public function get_name() {
        return 'test_widget';
    }

    public function get_title() {
        return __('Test Widget', 'gahshomar-converter');
    }

    public function get_icon() {
        return 'eicon-text';
    }

    public function get_categories() {
        return ['general'];
    }

    protected function register_controls() {
        $this->start_controls_section(
            'content_section',
            [
                'label' => __('Content', 'gahshomar-converter'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $current_date = date('Y-m-d H:i:s');
        ?>
        <div id="test-widget-wrapper">
            <h3><?php echo __('Current Date: ', 'gahshomar-converter') . $current_date; ?></h3>
        </div>
        <?php
    }
}