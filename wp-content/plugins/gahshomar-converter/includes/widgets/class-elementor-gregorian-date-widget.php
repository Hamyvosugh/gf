<?php
namespace Gahshomar;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

class Elementor_Gregorian_Date_Widget extends Widget_Base {

    public function get_name() {
        return 'gregorian_date_widget';
    }

    public function get_title() {
        return __('Gregorian Date Widget', 'gahshomar-converter');
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
                'label' => __('Content', 'gahshomar-converter'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'day',
            [
                'label' => __('Day', 'gahshomar-converter'),
                'type' => Controls_Manager::NUMBER,
                'min' => 1,
                'max' => 31,
                'default' => 1,
            ]
        );

        $this->add_control(
            'month',
            [
                'label' => __('Month', 'gahshomar-converter'),
                'type' => Controls_Manager::NUMBER,
                'min' => 1,
                'max' => 12,
                'default' => 1,
            ]
        );

        $this->add_control(
            'year',
            [
                'label' => __('Year', 'gahshomar-converter'),
                'type' => Controls_Manager::NUMBER,
                'default' => 2023,
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        ?>
        <div id="gregorian-date-wrapper">
            <h3><?php _e('Gregorian Date', 'gahshomar-converter'); ?></h3>
            <form id="gahshomar-converter-form">
                <label for="day"><?php _e('Day:', 'gahshomar-converter'); ?></label>
                <input type="number" id="day" name="day" value="<?php echo $settings['day']; ?>" required>

                <label for="month"><?php _e('Month:', 'gahshomar-converter'); ?></label>
                <input type="number" id="month" name="month" value="<?php echo $settings['month']; ?>" required>

                <label for="year"><?php _e('Year:', 'gahshomar-converter'); ?></label>
                <input type="number" id="year" name="year" value="<?php echo $settings['year']; ?>" required>

                <input type="hidden" id="source_calendar" name="source_calendar" value="gregorian">
                <input type="hidden" id="target_calendar" name="target_calendar" value="jalaali">

                <button type="submit"><?php _e('Convert', 'gahshomar-converter'); ?></button>
            </form>
            <div id="gregorian-date-output"></div>
        </div>
        <?php
    }
}