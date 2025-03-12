<?php
/*
Plugin Name: Saraghaz
Description: All in one Iranian Calendars with modern Design / Created as Widget for Elementor Wordpress
Version: 1.0
Author: Hamy Vosugh
*/


// Include the Update Checker library


// Include the Update Checker library
require 'includes/plugin-update-checker/load-v5p4.php';

use YahnisElsts\PluginUpdateChecker\v5p4\PucFactory;

// Initialize the update checker
$updateChecker = PucFactory::buildUpdateChecker(
    'https://your-github-token:x-oauth-basic@github.com/Hamyvosugh/Gahshomar/raw/main/metadata.json', // URL with token for the metadata.json file
    __FILE__, // Full path to the main plugin file
    'saraghaz' // Plugin slug
);




/*    end of update checker    */


// Enqueue the CSS file
function saraghaz_enqueue_styles() {
    wp_enqueue_style('saraghaz-style', plugin_dir_url(__FILE__) . 'asset/css/saraghaz.css');
}
add_action('wp_enqueue_scripts', 'saraghaz_enqueue_styles');

// Check if Elementor is active
function saraghaz_is_elementor_active() {
    return defined('ELEMENTOR_VERSION');
}

// Register the Elementor widgets
function saraghaz_register_elementor_widgets() {
    // Load the widget files only if Elementor is active
    if (saraghaz_is_elementor_active()) {
        require_once plugin_dir_path(__FILE__) . 'saraghaz-widget/saraghaz-controls.php';
        require_once plugin_dir_path(__FILE__) . 'saraghaz-widget/saraghaz-main-widget.php';

        // Register the main widget
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new \Saraghaz_Main_Widget());
    }
}
add_action('elementor/widgets/widgets_registered', 'saraghaz_register_elementor_widgets');