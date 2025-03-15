<?php
/**
 * Plugin Name: Farakhor Events
 * Plugin URI: #
 * Description: Display event cards from farakhor table with filtering and search capabilities
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: #
 * Text Domain: farakhor-events
 * Domain Path: /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Define plugin constants
define('FARAKHOR_EVENTS_VERSION', '1.0.0');
define('FARAKHOR_EVENTS_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('FARAKHOR_EVENTS_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * The class that handles all plugin functionality.
 */
class Farakhor_Events {

    /**
     * Initialize the plugin.
     */
    public function __construct() {
        // Register activation and deactivation hooks
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));

        // Load required files
        $this->load_dependencies();

        // Register shortcode
        add_shortcode('farakhor_events', array($this, 'shortcode_callback'));

        // Register assets
        add_action('wp_enqueue_scripts', array($this, 'register_assets'));

        // Add Ajax actions
        add_action('wp_ajax_farakhor_filter_events', array($this, 'filter_events'));
        add_action('wp_ajax_nopriv_farakhor_filter_events', array($this, 'filter_events'));
    }

    /**
     * Load dependencies.
     */
    private function load_dependencies() {
        // Include helper functions
        require_once FARAKHOR_EVENTS_PLUGIN_DIR . 'includes/helpers.php';
        
        // Check if Verta library is available and autoload it if needed
        if (!class_exists('Hekmatinasser\Verta\Verta') && file_exists(ABSPATH . 'vendor/autoload.php')) {
            require_once ABSPATH . 'vendor/autoload.php';
        }
    }

    /**
     * Plugin activation.
     */
    public function activate() {
        // Activation tasks if needed
        flush_rewrite_rules();
    }

    /**
     * Plugin deactivation.
     */
    public function deactivate() {
        // Deactivation tasks if needed
        flush_rewrite_rules();
    }

    /**
     * Register plugin assets.
     */
    public function register_assets() {
        // Register styles
        wp_register_style(
            'farakhor-events-style',
            FARAKHOR_EVENTS_PLUGIN_URL . 'assets/css/farakhor-events.css',
            array(),
            FARAKHOR_EVENTS_VERSION
        );

        // Register scripts
        wp_register_script(
            'farakhor-events-script',
            FARAKHOR_EVENTS_PLUGIN_URL . 'assets/js/farakhor-events.js',
            array('jquery'),
            FARAKHOR_EVENTS_VERSION,
            true
        );

        // Localize script with Ajax URL and nonce
        wp_localize_script(
            'farakhor-events-script',
            'farakhorEvents',
            array(
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('farakhor-events-nonce'),
            )
        );
    }

    /**
     * Shortcode callback function.
     *
     * @param array $atts Shortcode attributes.
     * @return string HTML output.
     */
    public function shortcode_callback($atts) {
        // Enqueue assets
        wp_enqueue_style('farakhor-events-style');
        wp_enqueue_script('farakhor-events-script');

        // Parse shortcode attributes
        $atts = shortcode_atts(
            array(
                'limit' => 12, // Default number of events to display
                'order' => 'ASC', // Default order
                'category' => '', // Default category filter
            ),
            $atts,
            'farakhor_events'
        );

        // Get events from database
        $events = $this->get_events($atts);

        // Start output buffering
        ob_start();

        // Include template
        include FARAKHOR_EVENTS_PLUGIN_DIR . 'templates/card-template.php';

        // Return buffered content
        return ob_get_clean();
    }

    /**
     * Get events from database.
     *
     * @param array $args Query arguments.
     * @return array Events data.
     */
    private function get_events($args) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'farakhor';
        $limit = intval($args['limit']);
        $order = $args['order'] === 'DESC' ? 'DESC' : 'ASC';
        $category_filter = '';

        if (!empty($args['category'])) {
            $category_filter = $wpdb->prepare(" AND categories LIKE %s", '%' . $wpdb->esc_like($args['category']) . '%');
        }

        // Get current date for comparison
        $current_date = current_time('Y-m-d');

        // Query to get events
        $query = $wpdb->prepare(
            "SELECT * FROM {$table_name} 
            WHERE 1=1 {$category_filter} 
            ORDER BY gregorian_day {$order} 
            LIMIT %d",
            $limit
        );

        $results = $wpdb->get_results($query, ARRAY_A);

        // Process each event to add additional data
        foreach ($results as &$event) {
            // Convert persian_day to full persian date string
            $event['persian_full_date'] = farakhor_get_persian_full_date($event['persian_day']);
            
            // Convert gregorian_day to formatted date
            $event['gregorian_formatted'] = date_i18n(get_option('date_format'), strtotime($event['gregorian_day']));
            
            // Calculate remaining days
            $days_diff = farakhor_calculate_days_difference($event['gregorian_day']);
            if ($days_diff > 0) {
                $event['remaining_days'] = farakhor_convert_to_persian_numeral($days_diff) . ' روز مانده';
            } else if ($days_diff < 0) {
                $event['remaining_days'] = farakhor_convert_to_persian_numeral(abs($days_diff)) . ' روز گذشته';
            } else {
                $event['remaining_days'] = 'امروز';
            }
            
            // Convert tags and categories to arrays
            $event['tags_array'] = !empty($event['tag']) ? explode(',', $event['tag']) : array();
            $event['categories_array'] = !empty($event['categories']) ? explode(',', $event['categories']) : array();
            
            // Filter categories to only show: ملی، جهانی، محلی، فرهنگی
            $allowed_categories = array('ملی', 'جهانی', 'محلی', 'فرهنگی');
            $event['filtered_categories'] = array_intersect($event['categories_array'], $allowed_categories);
        }

        return $results;
    }

    /**
     * Ajax callback for filtering events.
     */
    public function filter_events() {
        // Check nonce for security
        check_ajax_referer('farakhor-events-nonce', 'nonce');

        // Get filter parameters
        $month = isset($_POST['month']) ? sanitize_text_field($_POST['month']) : '';
        $search = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';
        $limit = isset($_POST['limit']) ? intval($_POST['limit']) : 12;

        // Query database with filters
        global $wpdb;
        $table_name = $wpdb->prefix . 'farakhor';
        $where_clauses = array('1=1');
        
        // Month filter
        if (!empty($month) && $month !== 'all') {
            $where_clauses[] = $wpdb->prepare("persian_day LIKE %s", $month . '-%');
        }
        
        // Only include rows where module = 'YES'
        $where_clauses[] = "module = 'YES'";
        
        // Search filter
        if (!empty($search)) {
            $where_clauses[] = $wpdb->prepare(
                "(event_title LIKE %s OR event_text LIKE %s OR tag LIKE %s OR categories LIKE %s)",
                '%' . $wpdb->esc_like($search) . '%',
                '%' . $wpdb->esc_like($search) . '%',
                '%' . $wpdb->esc_like($search) . '%',
                '%' . $wpdb->esc_like($search) . '%'
            );
        }
        
        // Build and execute query
        $where_clause = implode(' AND ', $where_clauses);
        $query = $wpdb->prepare(
            "SELECT * FROM {$table_name} 
            WHERE {$where_clause} 
            ORDER BY gregorian_day ASC 
            LIMIT %d",
            $limit
        );
        
        $results = $wpdb->get_results($query, ARRAY_A);
        
        // Process events like in get_events method
        foreach ($results as &$event) {
            $event['persian_full_date'] = farakhor_get_persian_full_date($event['persian_day']);
            $event['gregorian_formatted'] = date_i18n(get_option('date_format'), strtotime($event['gregorian_day']));
            
            $days_diff = farakhor_calculate_days_difference($event['gregorian_day']);
            if ($days_diff > 0) {
                $event['remaining_days'] = farakhor_convert_to_persian_numeral($days_diff) . ' روز مانده';
            } else if ($days_diff < 0) {
                $event['remaining_days'] = farakhor_convert_to_persian_numeral(abs($days_diff)) . ' روز گذشته';
            } else {
                $event['remaining_days'] = 'امروز';
            }
            
            $event['tags_array'] = !empty($event['tag']) ? explode(',', $event['tag']) : array();
            $event['categories_array'] = !empty($event['categories']) ? explode(',', $event['categories']) : array();
            
            $allowed_categories = array('ملی', 'جهانی', 'محلی', 'فرهنگی');
            $event['filtered_categories'] = array_intersect($event['categories_array'], $allowed_categories);
        }
        
        // Prepare response
        $response = array(
            'success' => true,
            'events' => $results,
            'count' => count($results)
        );
        
        // Return response as JSON
        wp_send_json($response);
    }
}

// Initialize the plugin
$farakhor_events = new Farakhor_Events();