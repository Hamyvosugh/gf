<?php
/*
Plugin Name: Dual Clock Widget for Elementor
Description: Adds a dual clock widget with local and international time
Version: 1.0
*/

if (!defined('ABSPATH')) exit;

class Dual_Clock_Widget_Elementor {
    private static $_instance = null;
    
    public static function instance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    public function __construct() {
        add_action('plugins_loaded', [$this, 'init']);
    }
    
    public function init() {
        if (!did_action('elementor/loaded')) {
            add_action('admin_notices', [$this, 'admin_notice_missing_elementor']);
            return;
        }
        
        add_action('elementor/widgets/register', [$this, 'register_widgets']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
    }

    public function admin_notice_missing_elementor() {
        echo '<div class="notice notice-warning is-dismissible"><p>Dual Clock Widget requires Elementor to be installed and activated.</p></div>';
    }
    
    public function register_widgets($widgets_manager) {
        require_once(__DIR__ . '/widgets/dual-clock-widget.php');
        require_once(__DIR__ . '/widgets/timezone-selector-widget.php');
        $widgets_manager->register(new \Dual_Clock_Widget());
        $widgets_manager->register(new \Timezone_Selector_Widget());
        
    }
    
    public function enqueue_scripts() {
        wp_enqueue_style('dual-clock-styles', plugins_url('assets/css/dual-clock.css', __FILE__));
        wp_enqueue_script('dual-clock-script', plugins_url('assets/js/dual-clock.js', __FILE__), ['jquery'], '1.0', true);
    }
}

Dual_Clock_Widget_Elementor::instance();

