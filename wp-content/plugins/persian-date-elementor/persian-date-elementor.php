<?php
/**
 * Plugin Name: Persian Date Elementor Widget
 * Description: A custom Elementor widget to display the Persian date.
 * Version: 1.0
 * Author: Hamy Vosugh
 */

if (!defined('ABSPATH')) exit;

// Register Widgets
function register_persian_date_elementor_widgets($widgets_manager) {
    require_once(__DIR__ . '/widgets/persian-date-widget.php');
    require_once(__DIR__ . '/widgets/persian-day-name-widget.php');
    $widgets_manager->register(new \Elementor\Persian_Date_Widget());
    $widgets_manager->register(new \Elementor\Persian_Day_Name_Widget());
}
add_action('elementor/widgets/register', 'register_persian_date_elementor_widgets');

// Enqueue Styles
function enqueue_persian_date_widget_styles() {
    wp_enqueue_style('persian-date-widget-css', plugins_url('widgets/persian-date-widget.css', __FILE__));
}
add_action('wp_enqueue_scripts', 'enqueue_persian_date_widget_styles');