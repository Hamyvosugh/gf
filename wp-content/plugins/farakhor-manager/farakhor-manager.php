<?php
/**
 * Plugin Name: Farakhor Data Manager
 * Description: Manages and displays Farakhor data table
 * Version: 1.0
 * Author: Hamy
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Plugin Class
class Farakhor_Manager {
    private static $instance = null;

    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('admin_menu', array($this, 'add_menu_page'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        add_action('wp_ajax_save_farakhor_data', array($this, 'save_farakhor_data'));
        add_action('wp_ajax_delete_farakhor_data', array($this, 'delete_farakhor_data'));
    }
    
    /**
     * Delete farakhor data
     */
    public function delete_farakhor_data() {
        check_ajax_referer('farakhor_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized');
        }
        
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        
        if (!$id) {
            wp_send_json_error('Invalid ID');
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'farakhor';
        
        $result = $wpdb->delete(
            $table_name,
            array('id' => $id),
            array('%d')
        );
        
        if ($result) {
            wp_send_json_success();
        } else {
            wp_send_json_error('Failed to delete item');
        }
    }

    public function enqueue_admin_scripts($hook) {
        // Check if we're on any of our plugin pages
        if (!in_array($hook, array('toplevel_page_farakhor-data', 'farakhor-data_page_farakhor-data-new'))) {
            return;
        }

        // Enqueue required scripts and styles
        wp_enqueue_media();
        wp_enqueue_script('jquery');
        wp_enqueue_style('select2', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css');
        wp_enqueue_script('select2', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', array('jquery'));
        wp_enqueue_script('farakhor-admin', plugins_url('js/admin.js', __FILE__), array('jquery'), '1.0.1', true);
        
        wp_localize_script('farakhor-admin', 'farakhorAjax', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'admin_url' => admin_url(),
            'nonce' => wp_create_nonce('farakhor_nonce')
        ));
    }

    public function add_menu_page() {
        add_menu_page(
            'Farakhor Data',
            'Farakhor Data',
            'manage_options',
            'farakhor-data',
            array($this, 'display_page'),
            'dashicons-calendar-alt',
            30
        );
    
        // Add submenu for Create New
        add_submenu_page(
            'farakhor-data',
            'Add New Farakhor Entry',
            'Add New',
            'manage_options',
            'farakhor-data-new',
            array($this, 'display_create_page')
        );
    }

    public function display_page() {
        $action = isset($_GET['action']) ? sanitize_text_field($_GET['action']) : 'list';
        
        if ($action === 'edit' && isset($_GET['id'])) {
            $this->display_edit_form((int)$_GET['id']);
        } else {
            $this->display_list_page();
        }
    }

    public function display_create_page() {
        include plugin_dir_path(__FILE__) . 'templates/create-form.php';
    }

    private function display_list_page() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'farakhor';
        
        // Handle search
        $search = isset($_GET['search']) ? sanitize_text_field($_GET['search']) : '';
        $where = '';
        if (!empty($search)) {
            $where = $wpdb->prepare(
                "WHERE event_title LIKE '%%%s%%' OR event_text LIKE '%%%s%%'",
                $search,
                $search
            );
        }

        // Pagination
        $per_page = 20;
        $current_page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
        $offset = ($current_page - 1) * $per_page;

        // Get total items
        $total_items = $wpdb->get_var("SELECT COUNT(*) FROM $table_name $where");
        $total_pages = ceil($total_items / $per_page);

        // Get items for current page
        $items = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM $table_name $where ORDER BY id DESC LIMIT %d OFFSET %d",
                $per_page,
                $offset
            )
        );

        include plugin_dir_path(__FILE__) . 'templates/list-view.php';
    }

    private function display_edit_form($id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'farakhor';
        
        $item = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE id = %d",
            $id
        ));

        if (!$item) {
            wp_die('Item not found');
        }

        include plugin_dir_path(__FILE__) . 'templates/edit-form.php';
    }

    public function save_farakhor_data() {
        check_ajax_referer('farakhor_nonce', 'nonce');
    
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized');
        }
    
        global $wpdb;
        $table_name = $wpdb->prefix . 'farakhor';
    
        // Get categories and tags as strings
        $categories = isset($_POST['categories']) ? sanitize_text_field($_POST['categories']) : '';
        $tags = isset($_POST['tag']) ? sanitize_text_field($_POST['tag']) : '';
    
        $data = array(
            'persian_day' => sanitize_text_field($_POST['persian_day']),
            'gregorian_day' => sanitize_text_field($_POST['gregorian_day']),
            'event_title' => sanitize_text_field($_POST['event_title']),
            'event_text' => wp_kses_post($_POST['event_text']),
            'event_link' => isset($_POST['event_link']) ? esc_url_raw($_POST['event_link']) : null,
            'post_link' => isset($_POST['post_link']) ? esc_url_raw($_POST['post_link']) : null,
            'categories' => isset($_POST['categories']) ? sanitize_text_field($_POST['categories']) : null,
            'tag' => isset($_POST['tag']) ? sanitize_text_field($_POST['tag']) : null,
            'image' => isset($_POST['image']) ? esc_url_raw($_POST['image']) : null,
            'logo' => isset($_POST['logo']) ? esc_url_raw($_POST['logo']) : null,
            'module' => isset($_POST['module']) ? sanitize_text_field($_POST['module']) : null,
            'post_text' => isset($_POST['post_text']) ? wp_kses_post($_POST['post_text']) : null,
            'post_image1' => isset($_POST['post_image1']) ? esc_url_raw($_POST['post_image1']) : null,
            'post_image2' => isset($_POST['post_image2']) ? esc_url_raw($_POST['post_image2']) : null,
            'post_image3' => isset($_POST['post_image3']) ? esc_url_raw($_POST['post_image3']) : null,
            'post_video' => isset($_POST['post_video']) ? esc_url_raw($_POST['post_video']) : null,
            'post_attribute' => isset($_POST['post_attribute']) ? sanitize_text_field($_POST['post_attribute']) : null,
            'post_book_url' => isset($_POST['post_book_url']) ? esc_url_raw($_POST['post_book_url']) : null,
            'post_book_img' => isset($_POST['post_book_img']) ? esc_url_raw($_POST['post_book_img']) : null,
            'post_title_1' => isset($_POST['post_title_1']) ? sanitize_text_field($_POST['post_title_1']) : null,
            'post_title_2' => isset($_POST['post_title_2']) ? sanitize_text_field($_POST['post_title_2']) : null,
            'post_title_3' => isset($_POST['post_title_3']) ? sanitize_text_field($_POST['post_title_3']) : null,
            'post_title_4' => isset($_POST['post_title_4']) ? sanitize_text_field($_POST['post_title_4']) : null,
            'post_title_5' => isset($_POST['post_title_5']) ? sanitize_text_field($_POST['post_title_5']) : null,
            'post_title_6' => isset($_POST['post_title_6']) ? sanitize_text_field($_POST['post_title_6']) : null,
            'post_text_1' => isset($_POST['post_text_1']) ? wp_kses_post($_POST['post_text_1']) : null,
            'post_text_2' => isset($_POST['post_text_2']) ? wp_kses_post($_POST['post_text_2']) : null,
            'post_text_3' => isset($_POST['post_text_3']) ? wp_kses_post($_POST['post_text_3']) : null,
            'post_text_4' => isset($_POST['post_text_4']) ? wp_kses_post($_POST['post_text_4']) : null,
            'post_text_5' => isset($_POST['post_text_5']) ? wp_kses_post($_POST['post_text_5']) : null,
            'post_text_6' => isset($_POST['post_text_6']) ? wp_kses_post($_POST['post_text_6']) : null,
            'reference' => isset($_POST['reference']) ? wp_kses_post($_POST['reference']) : null,
            'image_desc_1' => isset($_POST['image_desc_1']) ? sanitize_text_field($_POST['image_desc_1']) : null,
            'image_desc_2' => isset($_POST['image_desc_2']) ? sanitize_text_field($_POST['image_desc_2']) : null,
            'image_desc_3' => isset($_POST['image_desc_3']) ? sanitize_text_field($_POST['image_desc_3']) : null,
            'image_desc_4' => isset($_POST['image_desc_4']) ? sanitize_text_field($_POST['image_desc_4']) : null,
            'image_desc_5' => isset($_POST['image_desc_5']) ? sanitize_text_field($_POST['image_desc_5']) : null,
        );

        // Create proper format array based on the data fields
        $format = array(
            '%s', // persian_day
            '%s', // gregorian_day
            '%s', // event_title
            '%s', // event_text
            '%s', // event_link
            '%s', // post_link
            '%s', // categories
            '%s', // tag
            '%s', // image
            '%s', // logo
            '%s', // module
            '%s', // post_text
            '%s', // post_image1
            '%s', // post_image2
            '%s', // post_image3
            '%s', // post_video
            '%s', // post_attribute
            '%s', // post_book_url
            '%s', // post_book_img
            '%s', // post_title_1
            '%s', // post_title_2
            '%s', // post_title_3
            '%s', // post_title_4
            '%s', // post_title_5
            '%s', // post_title_6
            '%s', // post_text_1
            '%s', // post_text_2
            '%s', // post_text_3
            '%s', // post_text_4
            '%s', // post_text_5
            '%s', // post_text_6
            '%s', // reference
            '%s', // image_desc_1
            '%s', // image_desc_2
            '%s', // image_desc_3
            '%s', // image_desc_4
            '%s', // image_desc_5
        );

        if (isset($_POST['id']) && !empty($_POST['id'])) {
            $wpdb->update(
                $table_name, 
                $data, 
                array('id' => (int)$_POST['id']), 
                $format, 
                array('%d')
            );
            $message = 'Entry updated successfully';
        } else {
            $wpdb->insert($table_name, $data, $format);
            $message = 'New entry created successfully';
        }
    
        wp_send_json_success(array('message' => $message));
    }
}

// Initialize plugin
Farakhor_Manager::getInstance();