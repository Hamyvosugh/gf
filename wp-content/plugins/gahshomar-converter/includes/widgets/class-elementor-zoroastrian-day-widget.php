<?php
namespace Gahshomar;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

class Elementor_Zoroastrian_Day_Widget extends Widget_Base {

    public function get_name() {
        return 'gahshomar_zoroastrian_day';
    }

    public function get_title() {
        return __('نام روز زرتشتی', 'gahshomar-converter');
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
        <div id="gahshomar-zoroastrian-day-wrapper">
            <h3>نام روز زرتشتی:</h3>
            <p id="gahshomar-zoroastrian-day">نامشخص</p>
        </div>
        <?php
    }
}
?>