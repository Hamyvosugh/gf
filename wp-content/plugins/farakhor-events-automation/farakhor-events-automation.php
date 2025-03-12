<?php
/**
 * Plugin Name: Farakhor Event Automation
 * Description: Automatically creates event posts from wp_farakhor table data
 * Version: 1.1
 */

if (!defined('ABSPATH')) {
    exit;
}

class Farakhor_Event_Automation {
    private $table_name = 'wp_farakhor';
    private $persian_months = array(
        '01' => 'فروردین',
        '02' => 'اردیبهشت',
        '03' => 'خرداد',
        '04' => 'تیر',
        '05' => 'مرداد',
        '06' => 'شهریور',
        '07' => 'مهر',
        '08' => 'آبان',
        '09' => 'آذر',
        '10' => 'دی',
        '11' => 'بهمن',
        '12' => 'اسپند'
    );

    public function __construct() {
        add_action('init', array($this, 'register_event_post_type'));
        add_action('init', array($this, 'register_event_taxonomies'));
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_notices', array($this, 'show_admin_notices'));
        add_action('admin_post_sync_farakhor_event', array($this, 'sync_event'));
    }

    public function register_event_post_type() {
        $labels = array(
            'name' => 'Events',
            'singular_name' => 'Event',
            'menu_name' => 'Events',
            'add_new' => 'Add New',
            'add_new_item' => 'Add New Event',
            'edit_item' => 'Edit Event',
            'new_item' => 'New Event',
            'view_item' => 'View Event',
            'search_items' => 'Search Events',
            'not_found' => 'No events found',
            'not_found_in_trash' => 'No events found in Trash'
        );

        $args = array(
            'labels' => $labels,
            'public' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'query_var' => true,
            'rewrite' => array('slug' => 'event'),
            'capability_type' => 'post',
            'has_archive' => true,
            'hierarchical' => false,
            'menu_position' => 5,
            'supports' => array('title', 'editor', 'thumbnail'), 
            'menu_icon' => 'dashicons-calendar-alt',
            'show_in_rest' => true
        );

        register_post_type('event', $args);
    }

    public function add_admin_menu() {
        add_menu_page(
            'همگام‌سازی رویدادهای فراخور',
            'Farakhor Sync',
            'manage_options',
            'sync-farakhor-event',
            array($this, 'render_admin_page'),
            'dashicons-update',
            31
        );
    }
    public function register_event_taxonomies() {
        // Register Event Category Taxonomy
        $cat_labels = array(
            'name'              => 'Event Categories',
            'singular_name'     => 'Event Category',
            'search_items'      => 'Search Event Categories',
            'all_items'         => 'All Event Categories',
            'parent_item'       => 'Parent Event Category',
            'parent_item_colon' => 'Parent Event Category:',
            'edit_item'         => 'Edit Event Category',
            'update_item'       => 'Update Event Category',
            'add_new_item'      => 'Add New Event Category',
            'new_item_name'     => 'New Event Category Name',
            'menu_name'         => 'Categories',
        );
    
        $cat_args = array(
            'hierarchical'      => true,
            'labels'            => $cat_labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array('slug' => 'event-category'),
            'show_in_rest'      => true,
        );
    
        register_taxonomy('event_category', array('event'), $cat_args);
    
        // Register Event Tag Taxonomy
        $tag_labels = array(
            'name'              => 'Event Tags',
            'singular_name'     => 'Event Tag',
            'search_items'      => 'Search Event Tags',
            'all_items'         => 'All Event Tags',
            'edit_item'         => 'Edit Event Tag',
            'update_item'       => 'Update Event Tag',
            'add_new_item'      => 'Add New Event Tag',
            'new_item_name'     => 'New Event Tag Name',
            'menu_name'         => 'Tags',
        );
    
        $tag_args = array(
            'hierarchical'      => false,
            'labels'            => $tag_labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array('slug' => 'event-tag'),
            'show_in_rest'      => true,
        );
    
        register_taxonomy('event_tag', array('event'), $tag_args);
    }
    public function show_admin_notices() {
        if (isset($_GET['farakhor_synced'])) {
            $count = intval($_GET['farakhor_synced']);
            ?>
            <div class="notice notice-success is-dismissible">
                <p><?php echo sprintf('Successfully synced %d events!', $count); ?></p>
            </div>
            <?php
        }

        if (isset($_GET['farakhor_debug'])) {
            ?>
            <div class="notice notice-info is-dismissible">
                <pre><?php echo $_GET['farakhor_debug']; ?></pre>
            </div>
            <?php
        }
    }

    public function render_admin_page() {
        // Get sync history
        $sync_history = get_option('farakhor_sync_history', array());
        ?>
        <div class="wrap farakhor-admin-container">
            <h1>همگام‌سازی رویدادهای فراخور</h1>
            
            <div class="farakhor-dashboard">
                <div class="farakhor-status-card">
                    <h2>وضعیت سیستم</h2>
                    <?php 
                    global $wpdb;
                    $total_records = $wpdb->get_var("SELECT COUNT(*) FROM {$this->table_name} WHERE module IN ('YES', 'POST')");
                    $pending_records = $wpdb->get_var("SELECT COUNT(*) FROM {$this->table_name} WHERE module IN ('YES', 'POST') AND (post_link IS NULL OR post_link = '')");
                    $existing_posts = wp_count_posts('event');
                    ?>
                    <div class="farakhor-stats">
                        <div class="stat-item">
                            <span class="stat-value"><?php echo number_format($total_records); ?></span>
                            <span class="stat-label">تعداد کل رکوردهای فعال</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-value"><?php echo number_format($pending_records); ?></span>
                            <span class="stat-label">رکوردهای در انتظار همگام‌سازی</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-value"><?php echo number_format($existing_posts->publish); ?></span>
                            <span class="stat-label">تعداد رویدادهای منتشر شده</span>
                        </div>
                    </div>
                </div>
                
                <div class="farakhor-action-card">
                    <h2>عملیات همگام‌سازی</h2>
                    <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
                        <input type="hidden" name="action" value="sync_farakhor_event">
                        <?php wp_nonce_field('sync_farakhor_event_nonce', 'sync_nonce'); ?>
                        <p>برای همگام‌سازی رویدادها از جدول فراخور، روی دکمه زیر کلیک کنید.</p>
                        <div class="sync-options">
                            <label>
                                <input type="checkbox" name="sync_pending_only" value="1">
                                فقط رکوردهای جدید همگام‌سازی شوند
                            </label>
                        </div>
                        <?php submit_button('شروع همگام‌سازی', 'primary', 'submit', true); ?>
                    </form>
                </div>
            </div>
            
            <?php if (!empty($sync_history)): ?>
            <div class="farakhor-report-container">
                <h2>گزارش عملیات همگام‌سازی</h2>
                <div class="farakhor-tabs">
                    <button class="tab-button active" data-tab="sync-history">تاریخچه همگام‌سازی</button>
                    <button class="tab-button" data-tab="last-sync">آخرین همگام‌سازی</button>
                </div>
                
                <div class="tab-content active" id="sync-history">
                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <th>تاریخ</th>
                                <th>تعداد</th>
                                <th>وضعیت</th>
                                <th>عملیات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $history_items = array_slice(array_reverse($sync_history), 0, 10);
                            foreach ($history_items as $index => $history): 
                                $date_format = new DateTime($history['date']);
                                $jalali_date = $this->gregorian_to_jalali($date_format->format('Y'), $date_format->format('m'), $date_format->format('d'));
                                $jalali_formatted = $jalali_date[2] . ' ' . $this->get_persian_month_name($jalali_date[1]) . ' ' . $jalali_date[0] . ' - ' . $date_format->format('H:i');
                            ?>
                                <tr>
                                    <td dir="rtl"><?php echo $jalali_formatted; ?></td>
                                    <td><?php echo number_format($history['count']); ?> رکورد</td>
                                    <td>
                                        <?php if ($history['count'] > 0): ?>
                                            <span class="status-success">موفق</span>
                                        <?php else: ?>
                                            <span class="status-warning">بدون تغییر</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <button type="button" class="button view-details-button" data-index="<?php echo $index; ?>">
                                            جزئیات
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="tab-content" id="last-sync">
                    <?php 
                    $last_sync = reset($history_items);
                    if ($last_sync):
                        $date_format = new DateTime($last_sync['date']);
                        $jalali_date = $this->gregorian_to_jalali($date_format->format('Y'), $date_format->format('m'), $date_format->format('d'));
                        $jalali_formatted = $jalali_date[2] . ' ' . $this->get_persian_month_name($jalali_date[1]) . ' ' . $jalali_date[0] . ' - ' . $date_format->format('H:i');
                    ?>
                        <div class="last-sync-summary">
                            <h3>خلاصه همگام‌سازی</h3>
                            <p><strong>تاریخ:</strong> <span dir="rtl"><?php echo $jalali_formatted; ?></span></p>
                            <p><strong>تعداد رکوردهای پردازش شده:</strong> <?php echo number_format($last_sync['count']); ?></p>
                        </div>
                        
                        <?php if (!empty($last_sync['details'])): ?>
                            <div class="last-sync-details">
                                <h3>جزئیات عملیات</h3>
                                <div class="details-container" dir="ltr">
                                    <?php 
                                    $operation_types = array(
                                        'create' => 'ایجاد',
                                        'update' => 'بروزرسانی',
                                        'error' => 'خطا'
                                    );
                                    
                                    $operations = array(
                                        'create' => array(),
                                        'update' => array(),
                                        'error' => array()
                                    );
                                    
                                    foreach ($last_sync['details'] as $detail) {
                                        if (strpos($detail, 'Creating') !== false) {
                                            $operations['create'][] = $detail;
                                        } elseif (strpos($detail, 'Updating') !== false) {
                                            $operations['update'][] = $detail;
                                        } elseif (strpos($detail, 'Error') !== false) {
                                            $operations['error'][] = $detail;
                                        }
                                    }
                                    
                                    foreach ($operations as $type => $msgs) {
                                        if (!empty($msgs)) {
                                            echo '<div class="operation-group operation-' . $type . '">';
                                            echo '<h4>' . $operation_types[$type] . ' (' . count($msgs) . ')</h4>';
                                            echo '<ul>';
                                            $display_limit = 5;
                                            $displayed = array_slice($msgs, 0, $display_limit);
                                            
                                            foreach ($displayed as $msg) {
                                                echo '<li>' . esc_html($msg) . '</li>';
                                            }
                                            
                                            if (count($msgs) > $display_limit) {
                                                echo '<li class="more-toggle"><a href="#">و ' . (count($msgs) - $display_limit) . ' مورد دیگر...</a></li>';
                                                echo '<div class="hidden-items" style="display:none;">';
                                                foreach (array_slice($msgs, $display_limit) as $msg) {
                                                    echo '<li>' . esc_html($msg) . '</li>';
                                                }
                                                echo '</div>';
                                            }
                                            
                                            echo '</ul>';
                                            echo '</div>';
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Modal for sync details -->
            <div id="sync-details-modal" class="modal">
                <div class="modal-content">
                    <span class="close">&times;</span>
                    <h2>جزئیات همگام‌سازی</h2>
                    <div id="modal-content"></div>
                </div>
            </div>
        </div>
        
        <style>
        .farakhor-admin-container {
            font-family: Tahoma, Arial, sans-serif;
        }
        .farakhor-dashboard {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 30px;
        }
        .farakhor-status-card, .farakhor-action-card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            padding: 20px;
            flex: 1;
            min-width: 300px;
        }
        .farakhor-stats {
            display: flex;
            gap: 20px;
            margin-top: 15px;
        }
        .stat-item {
            text-align: center;
            flex: 1;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
        }
        .stat-value {
            display: block;
            font-size: 24px;
            font-weight: bold;
            color: #2271b1;
            margin-bottom: 5px;
        }
        .stat-label {
            color: #50575e;
            font-size: 13px;
        }
        .sync-options {
            margin: 15px 0;
        }
        .farakhor-report-container {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            padding: 20px;
            margin-top: 20px;
        }
        .farakhor-tabs {
            display: flex;
            border-bottom: 1px solid #ddd;
            margin-bottom: 20px;
        }
        .tab-button {
            background: transparent;
            border: none;
            padding: 10px 15px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            color: #50575e;
        }
        .tab-button.active {
            color: #2271b1;
            border-bottom: 2px solid #2271b1;
        }
        .tab-content {
            display: none;
        }
        .tab-content.active {
            display: block;
        }
        .status-success {
            color: #46b450;
            font-weight: 500;
        }
        .status-warning {
            color: #ffb900;
            font-weight: 500;
        }
        .status-error {
            color: #dc3232;
            font-weight: 500;
        }
        .operation-group {
            margin-bottom: 20px;
            padding: 15px;
            border-radius: 6px;
        }
        .operation-create {
            background-color: #f0f6fc;
            border-left: 4px solid #2271b1;
        }
        .operation-update {
            background-color: #f0f6e8;
            border-left: 4px solid #46b450;
        }
        .operation-error {
            background-color: #fef7f1;
            border-left: 4px solid #dc3232;
        }
        .operation-group h4 {
            margin-top: 0;
            margin-bottom: 10px;
        }
        .operation-group ul {
            margin-top: 10px;
            max-height: 300px;
            overflow-y: auto;
        }
        .details-container {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            max-height: 500px;
            overflow-y: auto;
        }
        .more-toggle a {
            color: #2271b1;
            text-decoration: none;
        }
        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
        }
        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            width: 80%;
            max-width: 800px;
            max-height: 80vh;
            overflow-y: auto;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        </style>
        
        <script>
        jQuery(document).ready(function($) {
            // Tab switching
            $('.tab-button').on('click', function() {
                $('.tab-button').removeClass('active');
                $(this).addClass('active');
                
                const tabId = $(this).data('tab');
                $('.tab-content').removeClass('active');
                $('#' + tabId).addClass('active');
            });
            
            // Modal functionality
            const modal = $('#sync-details-modal');
            const modalContent = $('#modal-content');
            
            $('.view-details-button').on('click', function() {
                const index = $(this).data('index');
                const history = <?php echo json_encode($history_items); ?>;
                const item = history[index];
                
                let content = '<div class="sync-details">';
                content += '<p><strong>تاریخ:</strong> ' + item.date + '</p>';
                content += '<p><strong>تعداد:</strong> ' + item.count + ' رکورد</p>';
                
                if (item.details && item.details.length > 0) {
                    content += '<div class="details-container">';
                    content += '<h3>جزئیات عملیات</h3>';
                    content += '<pre>' + item.details.join("\n") + '</pre>';
                    content += '</div>';
                }
                
                content += '</div>';
                
                modalContent.html(content);
                modal.css('display', 'block');
            });
            
            $('.close').on('click', function() {
                modal.css('display', 'none');
            });
            
            $(window).on('click', function(event) {
                if (event.target == modal[0]) {
                    modal.css('display', 'none');
                }
            });
            
            // Toggle for "more items"
            $(document).on('click', '.more-toggle a', function(e) {
                e.preventDefault();
                $(this).parent().next('.hidden-items').toggle();
                return false;
            });
        });
        </script>
        <?php
    }

    /**
     * Sync events from farakhor table to WordPress posts
     */
    public function sync_event() {
        if (!current_user_can('manage_options')) {
            wp_die('دسترسی غیرمجاز');
        }

        check_admin_referer('sync_farakhor_event_nonce', 'sync_nonce');

        global $wpdb;
        $debug_messages = array();
        $sync_pending_only = isset($_POST['sync_pending_only']) && $_POST['sync_pending_only'] == '1';

        // Stats for this sync operation
        $stats = array(
            'created' => 0,
            'updated' => 0,
            'errors' => 0,
            'skipped' => 0
        );

        // Build the query based on sync options
        $where_clause = "WHERE module IN ('YES', 'POST')";
        if ($sync_pending_only) {
            $where_clause .= " AND (post_link IS NULL OR post_link = '')";
        }
        
        $records = $wpdb->get_results("SELECT * FROM {$this->table_name} {$where_clause}");
        
        if ($wpdb->last_error) {
            $debug_messages[] = 'خطای پایگاه داده: ' . $wpdb->last_error;
            $this->save_sync_history(0, $debug_messages, $stats);
            $this->redirect_with_messages($debug_messages);
            return;
        }

        $synced = 0;
        $start_time = microtime(true);
        
        foreach ($records as $record) {
            // Use event_title for the post title (that's the main title)
            $title = !empty($record->event_title) ? $record->event_title : '';
            
            // Use post_text for the post content
            $content = !empty($record->post_text) ? $record->post_text : $record->event_text;
            
            if (empty($title)) {
                $debug_messages[] = "رکورد {$record->id} بدون عنوان، نادیده گرفته شد";
                $stats['skipped']++;
                continue;
            }
            
            $post_data = array(
                'post_title' => $title,
                'post_content' => $content,
                'post_status' => 'publish',
                'post_type' => 'event'
            );

            // Check for existing post
            $existing_posts = get_posts(array(
                'post_type' => 'event',
                'meta_query' => array(
                    array(
                        'key' => 'farakhor_id',
                        'value' => $record->id,
                        'compare' => '='
                    )
                ),
                'posts_per_page' => 1
            ));

            if (empty($existing_posts)) {
                $post_id = wp_insert_post($post_data);
                $debug_messages[] = "ایجاد رویداد جدید برای رکورد شماره {$record->id}";
                $stats['created']++;
            } else {
                $post_data['ID'] = $existing_posts[0]->ID;
                $post_id = wp_update_post($post_data);
                $debug_messages[] = "بروزرسانی رویداد شماره {$existing_posts[0]->ID}";
                $stats['updated']++;
            }

            if (!is_wp_error($post_id)) {
                update_post_meta($post_id, 'farakhor_id', $record->id);
                
                // Generate and update post URL
                $post_url = $this->update_post_link_in_db($post_id, $record->id);
                
                // Process and assign categories
if (!empty($record->categories)) {
    $this->process_categories($post_id, $record->categories, $debug_messages);
}

// Process and assign tags
if (!empty($record->tag)) {
    $this->process_tags($post_id, $record->tag, $debug_messages);
}

                if (function_exists('update_field')) {
                    // Map fields according to ACF structure
                    // The field names on the left are your ACF field names
                    // The fields on the right are from your wp_farakhor table
                    $field_mappings = array(
                        // Basic fields
                        'persian_day' => $this->format_persian_date($record->persian_day),
                        'gregorian_day' => $this->format_gregorian_date($record->gregorian_day),
                        'event_title' => $record->event_title,
                        'event_text' => $record->event_text,
                        'event_link' => $record->event_link,
                        'post_link' => $post_url,
                        'categories' => $record->categories,
                        'tag' => $record->tag,
                        
                        // Post content fields
                        'post_title' => $record->event_title, // In ACF, this is "post_title" 
                        'post_text' => $record->post_text,
                        
                        // Paragraph sections
                        'post_title_1' => $record->post_title_1,
                        'post_title_2' => $record->post_title_2,
                        'post_title_3' => $record->post_title_3,
                        'post_title_4' => $record->post_title_4,
                        'post_title_5' => $record->post_title_5,
                        'post_title_6' => $record->post_title_6,
                        'post_text_1' => $record->post_text_1,
                        'post_text_2' => $record->post_text_2,
                        'post_text_3' => $record->post_text_3,
                        'post_text_4' => $record->post_text_4,
                        'post_text_5' => $record->post_text_5,
                        'post_text_6' => $record->post_text_6,
                        
                        // Additional content fields
                        'post_book_url' => $record->post_book_url,
                        'post_video' => $record->post_video,
                        'post_attribute' => $record->post_attribute,
                        'reference' => $record->reference,
                        
                        // Image descriptions
                        'image_desc_1' => $record->image_desc_1,
                        'image_desc_2' => $record->image_desc_2,
                        'image_desc_3' => $record->image_desc_3,
                        'image_desc_4' => $record->image_desc_4,
                        'image_desc_5' => $record->image_desc_5,
                        
                        // Post ID for reference
                        'id' => $record->id
                    );

                    foreach ($field_mappings as $acf_field => $value) {
                        if (!empty($value)) {
                            update_field($acf_field, $value, $post_id);
                            $debug_messages[] = "بروزرسانی فیلد {$acf_field}";
                        }
                    }

                    // Image fields mapped according to ACF structure
                    $image_mappings = array(
                        'post_img_1' => $record->post_image1,
                        'post_img_2' => $record->post_image2,
                        'post_img_3' => $record->post_image3,
                        'post_book_img' => $record->post_book_img,
                        'image' => $record->image, // Calendar Image in ACF
                        'logo' => $record->logo,
                        'post_banner' => $record->image // Post banner uses the main image
                    );
                    
                    // Handle post_img_1 separately for thumbnail
                    if (!empty($record->post_image1)) {
                        $thumbnail_success = $this->set_post_thumbnail($post_id, $record->post_image1);
                        if ($thumbnail_success) {
                            $debug_messages[] = "تصویر شاخص از post_img_1 برای رویداد {$post_id} تنظیم شد";
                            // Also update the ACF field if needed
                            update_field('post_img_1', get_post_thumbnail_id($post_id), $post_id);
                        } else {
                            $debug_messages[] = "خطا در تنظیم تصویر شاخص از post_img_1: {$record->post_image1}";
                        }
                    }
                    
                    // Handle other images
                    foreach ($image_mappings as $acf_field => $image_url) {
                        if (!empty($image_url) && $acf_field != 'post_img_1') { // Skip post_img_1 as we already handled it
                            $attach_id = $this->handle_image_upload($image_url, $post_id);
                            if ($attach_id) {
                                update_field($acf_field, $attach_id, $post_id);
                                $debug_messages[] = "بروزرسانی فیلد تصویر {$acf_field}";
                            } else {
                                $debug_messages[] = "خطا در پردازش تصویر برای {$acf_field}: {$image_url}";
                            }
                        }
                    }
                }
                
                $synced++;
            } else {
                $debug_messages[] = "خطا در رویداد {$record->id}: " . $post_id->get_error_message();
                $stats['errors']++;
            }
            
        }

        $end_time = microtime(true);
        $execution_time = round($end_time - $start_time, 2);
        
        // Add summary message
        if ($synced > 0) {
            $debug_messages[] = "عملیات همگام‌سازی در {$execution_time} ثانیه با موفقیت انجام شد. {$synced} رویداد پردازش شد.";
        } else {
            $debug_messages[] = "هیچ رویدادی برای همگام‌سازی یافت نشد.";
        }

        // Save sync history
        $this->save_sync_history($synced, $debug_messages, $stats, $execution_time);
        
        $this->redirect_with_messages($debug_messages, $synced);
    }

    private function set_post_thumbnail($post_id, $image_url) {
        if (empty($image_url)) return false;
    
        // First try to handle the image upload
        $attach_id = $this->handle_image_upload($image_url, $post_id);
        
        if ($attach_id) {
            // Set as post thumbnail
            set_post_thumbnail($post_id, $attach_id);
            return true;
        }
        
        return false;
    }

    private function handle_image_upload($image_url, $post_id) {
        if (empty($image_url)) return false;
    
        require_once(ABSPATH . 'wp-admin/includes/media.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');
    
        // Check if image exists in media library
        $query = array(
            'post_type' => 'attachment',
            'meta_query' => array(
                array(
                    'key' => '_wp_attached_file',
                    'value' => basename($image_url),
                    'compare' => 'LIKE'
                )
            )
        );
    
        $existing_attachment = get_posts($query);
        
        if (!empty($existing_attachment)) {
            return $existing_attachment[0]->ID;
        }
    
        // Download and attach new image
        $tmp = download_url($image_url);
        if (is_wp_error($tmp)) {
            return false;
        }
    
        $file_array = array(
            'name' => basename($image_url),
            'tmp_name' => $tmp
        );
    
        $attach_id = media_handle_sideload($file_array, $post_id);
        
        @unlink($tmp);
    
        if (is_wp_error($attach_id)) {
            return false;
        }
    
        return $attach_id;
    }
    
    /**
     * Process and assign categories to event
     * 
     * @param int $post_id The post ID
     * @param string $categories Comma-separated category names
     * @param array &$debug_messages Reference to debug messages array
     */
    private function process_categories($post_id, $categories, &$debug_messages) {
        if (empty($categories)) {
            return;
        }
        
        // Remove current categories to avoid duplicates
        wp_set_object_terms($post_id, array(), 'event_category');
        
        // Split the categories string into an array
        $category_names = array_map('trim', explode(',', $categories));
        $assigned_categories = array();
        
        foreach ($category_names as $category_name) {
            if (empty($category_name)) {
                continue;
            }
            
            // Check if category exists, if not create it
            $term = term_exists($category_name, 'event_category');
            if (!$term) {
                $term = wp_insert_term($category_name, 'event_category');
                if (is_wp_error($term)) {
                    $debug_messages[] = "Error creating category: " . $term->get_error_message();
                    continue;
                }
                $debug_messages[] = "Created new category: {$category_name}";
            }
            
            if (is_array($term)) {
                $assigned_categories[] = (int)$term['term_id'];
            } else {
                $assigned_categories[] = (int)$term;
            }
        }
        
        if (!empty($assigned_categories)) {
            wp_set_object_terms($post_id, $assigned_categories, 'event_category');
            $debug_messages[] = "Assigned categories to event: " . implode(', ', $category_names);
        }
    }
    
    /**
     * Process and assign tags to event
     * 
     * @param int $post_id The post ID
     * @param string $tags Comma-separated tag names
     * @param array &$debug_messages Reference to debug messages array
     */
    private function process_tags($post_id, $tags, &$debug_messages) {
        if (empty($tags)) {
            return;
        }
        
        // Split the tags string into an array
        $tag_names = array_map('trim', explode(',', $tags));
        
        if (!empty($tag_names)) {
            wp_set_object_terms($post_id, $tag_names, 'event_tag');
            $debug_messages[] = "Assigned tags to event: " . implode(', ', $tag_names);
        }
    }
    
    private function update_post_link_in_db($post_id, $record_id) {
        global $wpdb;
        $post = get_post($post_id);
        $post_url = 'https://gahshomar.com/' . $post->post_name;
        
        $wpdb->update(
            $this->table_name,
            array('post_link' => $post_url),
            array('id' => $record_id),
            array('%s'),
            array('%d')
        );
    
        return $post_url;
    }
    
    private function format_persian_date($date_str) {
        if (empty($date_str)) return '';
        
        $parts = explode('-', $date_str);
        if (count($parts) !== 2) return $date_str;
        
        $month = $parts[0];
        $day = $parts[1];
        
        // Convert numbers to Persian digits
        $persian_day = strtr($day, array('0'=>'۰','1'=>'۱','2'=>'۲','3'=>'۳','4'=>'۴','5'=>'۵','6'=>'۶','7'=>'۷','8'=>'۸','9'=>'۹'));
        
        if (isset($this->persian_months[$month])) {
            return $persian_day . ' ' . $this->persian_months[$month];
        }
        
        return $date_str;
    }
    
    private function format_gregorian_date($date_str) {
        if (empty($date_str)) {
            return '';
        }
        
        // Handle YYYY-MM-DD format from database
        $date_parts = explode('-', $date_str);
        if (count($date_parts) === 3) {
            $year = $date_parts[0];
            $month = $date_parts[1];
            $day = $date_parts[2];
            
            // Convert day number to Persian digits
            $persian_day = strtr($day, array(
                '0'=>'۰', '1'=>'۱', '2'=>'۲', '3'=>'۳', '4'=>'۴', 
                '5'=>'۵', '6'=>'۶', '7'=>'۷', '8'=>'۸', '9'=>'۹'
            ));
            
            // Remove leading zero from day
            $persian_day = ltrim($persian_day, '۰');
            
            // Format as "DD Month" in Persian
            $date = new DateTime($date_str);
            $month_num = $date->format('m');
            
            if (isset($this->persian_months[$month_num])) {
                return $persian_day . ' ' . $this->persian_months[$month_num];
            }
        }
        
        return $date_str;
    }
    /**
     * Save sync history
     */
    private function save_sync_history($count, $details, $stats = array(), $execution_time = 0) {
        $history = get_option('farakhor_sync_history', array());
        
        // Add new sync record
        $history[] = array(
            'date' => current_time('mysql'),
            'count' => $count,
            'details' => $details,
            'stats' => $stats,
            'execution_time' => $execution_time
        );
        
        // Limit history to last 50 entries
        if (count($history) > 50) {
            $history = array_slice($history, -50);
        }
        
        update_option('farakhor_sync_history', $history);
    }
    
    /**
     * Convert Gregorian date to Jalali
     */
    private function gregorian_to_jalali($g_y, $g_m, $g_d) {
        $g_days_in_month = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
        $j_days_in_month = array(31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 30, 29);
        
        $gy = $g_y-1600;
        $gm = $g_m-1;
        $gd = $g_d-1;
        
        $g_day_no = 365*$gy+intval(($gy+3)/4)-intval(($gy+99)/100)+intval(($gy+399)/400);
        
        for ($i=0; $i < $gm; ++$i)
            $g_day_no += $g_days_in_month[$i];
        if ($gm>1 && (($gy%4==0 && $gy%100!=0) || ($gy%400==0)))
            $g_day_no++;
        $g_day_no += $gd;
        
        $j_day_no = $g_day_no-79;
        
        $j_np = intval($j_day_no/12053);
        $j_day_no %= 12053;
        
        $jy = 979+33*$j_np+4*intval($j_day_no/1461);
        
        $j_day_no %= 1461;
        
        if ($j_day_no >= 366) {
            $jy += intval(($j_day_no-1)/365);
            $j_day_no = ($j_day_no-1)%365;
        }
        
        for ($i = 0; $i < 11 && $j_day_no >= $j_days_in_month[$i]; ++$i)
            $j_day_no -= $j_days_in_month[$i];
        $jm = $i+1;
        $jd = $j_day_no+1;
        
        return array($jy, $jm, $jd);
    }
    
    /**
     * Get Persian month name
     */
    private function get_persian_month_name($month_num) {
        $persian_months = array(
            1 => 'فروردین',
            2 => 'اردیبهشت',
            3 => 'خرداد',
            4 => 'تیر',
            5 => 'مرداد',
            6 => 'شهریور',
            7 => 'مهر',
            8 => 'آبان',
            9 => 'آذر',
            10 => 'دی',
            11 => 'بهمن',
            12 => 'اسپند'
        );
        
        return isset($persian_months[$month_num]) ? $persian_months[$month_num] : '';
    }
    
    private function redirect_with_messages($debug_messages, $synced = 0) {
        wp_redirect(add_query_arg(
            array(
                'page' => 'sync-farakhor-event',
                'farakhor_synced' => $synced,
                'sync_complete' => 'true'
            ),
            admin_url('admin.php')
        ));
        exit;
    }
}

// Initialize plugin
add_action('plugins_loaded', function() {
    new Farakhor_Event_Automation();


    
});