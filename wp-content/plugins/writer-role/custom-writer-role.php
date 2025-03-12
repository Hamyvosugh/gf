<?php
/**
 * Plugin Name: Simple Farakhor Access
 * Description: Basic role with access to Farakhor plugins
 * Version: 0.2
 * Author: Your Name
 */

// Make sure this code isn't called directly
if (!defined('ABSPATH')) {
    exit;
}

// Simple function to create the writer role on activation
register_activation_hook(__FILE__, 'simple_farakhor_access_activate');

// Remove dashboard widgets
add_action('wp_dashboard_setup', 'simple_farakhor_remove_dashboard_widgets', 999);

function simple_farakhor_remove_dashboard_widgets() {
    // Only apply to writers
    $user = wp_get_current_user();
    if (!in_array('writer', (array) $user->roles)) {
        return;
    }
    
    // Remove default dashboard widgets
    remove_meta_box('dashboard_activity', 'dashboard', 'normal');
    remove_meta_box('dashboard_right_now', 'dashboard', 'normal');
    remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');
    remove_meta_box('dashboard_incoming_links', 'dashboard', 'normal');
    remove_meta_box('dashboard_plugins', 'dashboard', 'normal');
    remove_meta_box('dashboard_quick_press', 'dashboard', 'side');
    remove_meta_box('dashboard_recent_drafts', 'dashboard', 'side');
    remove_meta_box('dashboard_primary', 'dashboard', 'side');
    remove_meta_box('dashboard_secondary', 'dashboard', 'side');
    remove_meta_box('dashboard_site_health', 'dashboard', 'normal');
    remove_meta_box('e-dashboard-overview', 'dashboard', 'normal'); // Elementor widget
    remove_meta_box('wp_welcome_panel', 'dashboard', 'normal'); // Welcome panel
    
    // Remove WordPress Events and News widget
    remove_meta_box('dashboard_primary', 'dashboard', 'side');
    
    // Remove WordPress news widget
    remove_meta_box('dashboard_secondary', 'dashboard', 'side');
    
    // Remove Yoast SEO dashboard widget if exists
    remove_meta_box('wpseo-dashboard-overview', 'dashboard', 'normal');
}

// Remove welcome panel
add_action('admin_head', 'simple_farakhor_remove_welcome_panel');

function simple_farakhor_remove_welcome_panel() {
    // Only apply to writers
    $user = wp_get_current_user();
    if (!in_array('writer', (array) $user->roles)) {
        return;
    }
    
    remove_action('welcome_panel', 'wp_welcome_panel');
    
    // Also hide elements with CSS
    echo '<style>
        #welcome-panel, 
        .welcome-panel,
        .postbox-container .meta-box-sortables,
        #dashboard-widgets,
        #dashboard-widgets-wrap {
            display: none !important;
        }
        #dashboard-widgets .postbox-container {
            width: 0 !important;
        }
        #wpbody-content .metabox-holder {
            padding-top: 0 !important;
        }
    </style>';
}

function simple_farakhor_access_activate() {
    // Get or create writer role
    $role = get_role('writer');
    if (!$role) {
        // Create new role with administrator capabilities (for testing only)
        $admin_role = get_role('administrator');
        add_role('writer', 'Writer', $admin_role->capabilities);
    } else {
        // Add all admin capabilities
        $admin_role = get_role('administrator');
        foreach ($admin_role->capabilities as $cap => $grant) {
            $role->add_cap($cap);
        }
    }
}

// Register custom admin menu items
add_action('admin_menu', 'simple_farakhor_add_menus', 5); // early priority

function simple_farakhor_add_menus() {
    // Only for writers
    $user = wp_get_current_user();
    if (!in_array('writer', (array) $user->roles)) {
        return;
    }
    
    // Add custom Farakhor Data menu
    add_menu_page(
        'Farakhor Data',
        'Farakhor Data',
        'read', // Lower capability
        'writer-farakhor-data',
        'simple_farakhor_data_page',
        'dashicons-calendar-alt',
        30
    );
    
    // Add custom Farakhor Sync menu
    add_menu_page(
        'Farakhor Sync',
        'Farakhor Sync',
        'read', // Lower capability
        'writer-farakhor-sync',
        'simple_farakhor_sync_page',
        'dashicons-update',
        31
    );
    
    // Remove the duplicate Events menu later
    global $menu;
    $events_count = 0;
    
    // Count Events menu items
    foreach ($menu as $position => $item) {
        if (!empty($item[2]) && $item[2] == 'edit.php?post_type=event') {
            $events_count++;
            // If we have more than one, remove the later one
            if ($events_count > 1) {
                remove_menu_page('edit.php?post_type=event');
            }
        }
    }
}

// Filter admin menu to show only our items
add_action('admin_menu', 'simple_farakhor_filter_menu', 9999);

function simple_farakhor_filter_menu() {
    // Only for writers
    $user = wp_get_current_user();
    if (!in_array('writer', (array) $user->roles)) {
        return;
    }
    
    global $menu;
    
    // Keep only these menu items
    $keep_menu = array(
        'index.php', // Dashboard
        'writer-farakhor-data', // Our custom Farakhor Data
        'writer-farakhor-sync', // Our custom Farakhor Sync
        'edit.php?post_type=event', // Events
        'profile.php' // Profile
    );
    
    // Remove all others
    foreach ($menu as $position => $item) {
        if (empty($item[2])) {
            continue; // Skip separators
        }
        
        if (!in_array($item[2], $keep_menu)) {
            remove_menu_page($item[2]);
        }
    }
}

// Override capabilities check for writers to bypass all restrictions
add_filter('user_has_cap', 'simple_farakhor_override_caps', 10, 3);

function simple_farakhor_override_caps($allcaps, $caps, $args) {
    // Only for writers
    $user = wp_get_current_user();
    if (!in_array('writer', (array) $user->roles)) {
        return $allcaps;
    }
    
    // Always grant these critical capabilities
    $critical_caps = array(
        'manage_options',
        'upload_files',
        'edit_posts',
        'edit_published_posts',
        'publish_posts',
        'edit_events',
        'edit_published_events',
        'publish_events',
        'read_private_events'
    );
    
    foreach ($critical_caps as $cap) {
        $allcaps[$cap] = true;
    }
    
    // Check current request action
    $current_action = '';
    if (isset($_POST['action'])) {
        $current_action = $_POST['action'];
    } elseif (isset($_GET['action'])) {
        $current_action = $_GET['action'];
    }
    
    // For any Farakhor-related AJAX actions, grant all capabilities
    if (strpos($current_action, 'farakhor') !== false || 
        strpos($current_action, 'save_farakhor') !== false ||
        strpos($current_action, 'sync_farakhor') !== false ||
        strpos($current_action, 'delete_farakhor') !== false) {
        
        // Grant all capabilities for this request
        foreach ($caps as $cap) {
            $allcaps[$cap] = true;
        }
    }
    
    // Check if we're on a Farakhor page
    $is_farakhor_page = false;
    if (isset($_GET['page']) && (strpos($_GET['page'], 'farakhor') !== false || strpos($_GET['page'], 'sync-farakhor-event') !== false)) {
        $is_farakhor_page = true;
    }
    
    // Check if we're on an Events page
    $is_events_page = false;
    if (isset($_GET['post_type']) && $_GET['post_type'] === 'event') {
        $is_events_page = true;
    }
    
    // If on Farakhor or Events page, grant all capabilities
    if ($is_farakhor_page || $is_events_page) {
        foreach ($caps as $cap) {
            $allcaps[$cap] = true;
        }
    }
    
    return $allcaps;
}

// Allow AJAX actions for farakhor
add_action('admin_init', 'simple_farakhor_allow_ajax');

function simple_farakhor_allow_ajax() {
    // Make sure Writers can perform AJAX actions
    if (defined('DOING_AJAX') && DOING_AJAX) {
        // Get current action
        $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
        
        // Check if it's a Farakhor action
        if (strpos($action, 'farakhor') !== false || 
            strpos($action, 'save_farakhor') !== false ||
            strpos($action, 'sync_farakhor') !== false) {
            
            // Add capability filters with high priority
            add_filter('user_has_cap', 'simple_farakhor_force_ajax_caps', 9999, 4);
        }
    }
}

function simple_farakhor_force_ajax_caps($allcaps, $caps, $args, $user) {
    // Only apply to Writers
    if (!in_array('writer', (array) $user->roles)) {
        return $allcaps;
    }
    
    // Force all capabilities to true
    foreach ($caps as $cap) {
        $allcaps[$cap] = true;
    }
    
    // Explicitly grant manage_options
    $allcaps['manage_options'] = true;
    
    return $allcaps;
}

// Redirect pages to actual plugin pages
function simple_farakhor_data_page() {
    // Force capability for this page
    current_user_can('manage_options', true);
    
    echo '<script>window.location.href = "' . admin_url('admin.php?page=farakhor-data') . '";</script>';
    echo '<p>Redirecting to Farakhor Data...</p>';
    echo '<p><a href="' . admin_url('admin.php?page=farakhor-data') . '">Click here if not redirected</a></p>';
}

function simple_farakhor_sync_page() {
    // Force capability for this page
    current_user_can('manage_options', true);
    
    echo '<script>window.location.href = "' . admin_url('admin.php?page=sync-farakhor-event') . '";</script>';
    echo '<p>Redirecting to Farakhor Sync...</p>';
    echo '<p><a href="' . admin_url('admin.php?page=sync-farakhor-event') . '">Click here if not redirected</a></p>';
}