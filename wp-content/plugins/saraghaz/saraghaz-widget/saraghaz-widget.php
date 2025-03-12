<?php
/*
Plugin Name: Saraghaz Gahshomar
Description: All in one Iranian Calendars with modern Design / Created as Widget for Elementor Wordpress
Version: 1.0
Author: Hamy Vosugh
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

require_once __DIR__ . '/../vendor/autoload.php';

use Hekmatinasser\Verta\Verta;

class Saraghaz_Persian_Date_Widget extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'saraghaz_persian_date_widget',
            __('Persian Date Widget', 'text_domain'),
            array('description' => __('A Widget to display the current Persian date', 'text_domain'))
        );
    }

    public function widget($args, $instance) {
        echo $args['before_widget'];
        if (!empty($instance['title'])) {
            echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
        }

        // Display the current Persian date
        $v = new Verta();
        echo '<p>' . $v->format('l j F Y') . '</p>';

        echo $args['after_widget'];
    }

    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __('Persian Date', 'text_domain');
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <?php 
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';

        return $instance;
    }
}

// Register and load the widget
function saraghaz_load_widget() {
    register_widget('Saraghaz_Persian_Date_Widget');
}
add_action('widgets_init', 'saraghaz_load_widget');

function saraghaz_enqueue_styles() {
    wp_enqueue_style('saraghaz-styles', plugins_url('/css/saraghaz-styles.css', __FILE__));
}
add_action('wp_enqueue_scripts', 'saraghaz_enqueue_styles');