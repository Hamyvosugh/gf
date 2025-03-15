<?php
/**
 * Theme functions and definitions.
 *
 * For additional information on potential customization options,
 * read the developers' documentation:
 *
 * https://developers.elementor.com/docs/hello-elementor-theme/
 *
 * @package HelloElementorChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'HELLO_ELEMENTOR_CHILD_VERSION', '2.0.0' );

/**
 * Load child theme scripts & styles.
 *
 * @return void
 */
function hello_elementor_child_scripts_styles() {

	wp_enqueue_style(
		'hello-elementor-child-style',
		get_stylesheet_directory_uri() . '/style.css',
		[
			'hello-elementor-theme-style',
		],
		HELLO_ELEMENTOR_CHILD_VERSION
	);

}
add_action( 'wp_enqueue_scripts', 'hello_elementor_child_scripts_styles', 20 );

// Register custom page template
function register_event_cards_template($templates) {
    $templates['event-cards-template.php'] = 'Event Cards';
    return $templates;
}
add_filter('theme_page_templates', 'register_event_cards_template');

function load_event_cards_template($template) {
    if (is_page_template('event-cards-template.php')) {
        error_log('Event Cards template is being used.');
        $template = get_stylesheet_directory() . '/event-cards/templates/event-cards-template.php';
    }
    return $template;
}
add_filter('template_include', 'load_event_cards_template');

// Include the event cards functions file
require_once get_stylesheet_directory() . '/event-cards/includes/event-cards-functions.php';

function custom_scrollbar_styles() {
    wp_enqueue_style('custom-scrollbar', get_template_directory_uri() . '/css/custom-scrollbar.css');
}
add_action('wp_enqueue_scripts', 'custom_scrollbar_styles');

// Register header and Footer Menu
function gahshomar_child_setup() {
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'gahshomar-child'), // Register the primary menu
        'footer-menu' => __('Footer Menu', 'gahshomar-child'),
    ));
}
add_action('after_setup_theme', 'gahshomar_child_setup');




// hide header and footer for contact page mbile

function contact_page_mobile_header_footer_style() {
    if ( is_page_template( 'template-contact.php' ) ) { // Ensure this is your template name
        echo '<style>
        @media only screen and (max-width: 1080px) {
            header {
                display: none !important;
            }
            footer {
                display: none !important;
            }
        }
        </style>';
    }
}
add_action( 'wp_head', 'contact_page_mobile_header_footer_style' );



// تابعی برای بارگذاری محتوای تقویم ایزوله از فایل calendar-wrapper.php
function create_calendar_iframe() {
    $iframe_url = '/wp-content/themes/gahshomar-child/inc/jallali-calendar/calendar-wrapper.php';
    return '<div style="width: 100%; display: flex; justify-content: center;">
                <iframe src="' . esc_url($iframe_url) . '" width="170%" height="600px" frameborder="0" scrolling="yes" style="max-width: 100%; overflow: auto; margin-top:15px; "></iframe>
            </div>';
}
add_shortcode('calendar_iframe', 'create_calendar_iframe');
 





// اضافه کردن بخش فراخور //
function enqueue_farakhor_assets() {
    // Register CSS
    wp_register_style('farakhor-style', get_stylesheet_directory_uri() . '/farakhor/assets/css/farakhor.css');
    
    // Register JS
    wp_register_script('farakhor-script', get_stylesheet_directory_uri() . '/farakhor/assets/js/Farakhor.js', array('jquery'), null, true);
    wp_localize_script('farakhor-script', 'edpAjax', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('load_more_events_nonce')
    ));
}

// Enqueue the registered assets when needed
function load_farakhor_assets() {
    global $post;

    // Enqueue the CSS and JS if the shortcode is present on the page
    if (is_a($post, 'WP_Post') && has_shortcode($post->post_content, 'display_farakhor_events')) {
        wp_enqueue_style('farakhor-style');
        wp_enqueue_script('farakhor-script');
    }
}

add_action('wp_enqueue_scripts', 'enqueue_farakhor_assets');
add_action('wp_enqueue_scripts', 'load_farakhor_assets');

// Ensure the Farakhor shortcode is loaded from the child theme
require_once get_stylesheet_directory() . '/farakhor/farakhor.php';



// loop page 
function filter_events_for_next_week($query) {
    // بررسی اینکه این کوئری مربوط به Query ID ما است
    if (isset($query->query_vars['post_type']) && $query->query_vars['post_type'] === 'event') {
        // تاریخ امروز
        $today = date('Y-m-d');
        // تاریخ هفت روز آینده
        $next_week = date('Y-m-d', strtotime('+7 days'));

        // فیلتر کردن پست‌ها بر اساس تاریخ میلادی
        $query->set('meta_query', array(
            array(
                'key' => 'gregorian_day', // کلید فیلد ACF برای تاریخ میلادی
                'value' => array($today, $next_week),
                'compare' => 'BETWEEN',
                'type' => 'DATE',
            )
        ));

        // مرتب‌سازی بر اساس تاریخ
        $query->set('orderby', 'meta_value');
        $query->set('meta_key', 'gregorian_day'); // کلید تاریخ میلادی
        $query->set('order', 'ASC');
    }
}
add_action('elementor/query/filter_events_7', 'filter_events_for_next_week');

function filter_events_for_next_month($query) {
    // بررسی اینکه این کوئری مربوط به Query ID ما است
    if (isset($query->query_vars['post_type']) && $query->query_vars['post_type'] === 'event') {
        // تاریخ امروز
        $today = date('Y-m-d');
        // تاریخ هفت روز آینده
        $next_month = date('Y-m-d', strtotime('+30 days'));

        // فیلتر کردن پست‌ها بر اساس تاریخ میلادی
        $query->set('meta_query', array(
            array(
                'key' => 'gregorian_day', // کلید فیلد ACF برای تاریخ میلادی
                'value' => array($today, $next_month),
                'compare' => 'BETWEEN',
                'type' => 'DATE',
            )
        ));

        // مرتب‌سازی بر اساس تاریخ
        $query->set('orderby', 'meta_value');
        $query->set('meta_key', 'gregorian_day'); // کلید تاریخ میلادی
        $query->set('order', 'ASC');
    }
}
add_action('elementor/query/filter_events_30', 'filter_events_for_next_month');



// form register
add_action('elementor_pro/forms/new_record', function( $record, $ajax_handler ) {
    $raw_fields = $record->get('fields');
    $fields = [];
    
    foreach ( $raw_fields as $id => $field ) {
        $fields[ $id ] = $field['value'];
    }

    global $wpdb;
    $output['success'] = $wpdb->insert('wp_farakhor_users', array(
        'username' => $fields['username'],
        'email' => $fields['email'],
        'password' => wp_hash_password($fields['password']),
		'day' => $fields['day'],
		'month' => $fields['month'],
        'created_at' => current_time('mysql')
    ));

    $ajax_handler->add_response_data(true, $output);
}, 10, 2);

// Dashboard assets
function enqueue_dashboard_assets() {
    if (is_page_template('page-dashboard.php')) {
        wp_enqueue_style('dashboard-style', get_template_directory_uri() . '/assets/css/dashboard-style.css', array(), '1.0', 'all');
        wp_enqueue_script('dashboard-script', get_template_directory_uri() . '/assets/js/dashboard.js', array('jquery'), '1.0', true);
    }
}
add_action('wp_enqueue_scripts', 'enqueue_dashboard_assets');


// copun 
function validate_email($email) {
    // Basic email validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return array(
            'valid' => false,
            'message' => 'لطفا یک ایمیل معتبر وارد کنید.'
        );
    }
    
    // Check for disposable email domains (you can expand this list)
    $disposable_domains = array(
        'tempmail.com',
        'temp-mail.org',
        'tempemail.com',
        // Add more disposable email domains as needed
    );
    
    $email_domain = substr(strrchr($email, "@"), 1);
    if (in_array($email_domain, $disposable_domains)) {
        return array(
            'valid' => false,
            'message' => 'لطفا از ایمیل‌های موقت استفاده نکنید.'
        );
    }
    
    return array('valid' => true);
}

function generate_random_coupon($length = 8) {
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $coupon_code = '';
    
    for ($i = 0; $i < $length; $i++) {
        $coupon_code .= $characters[rand(0, strlen($characters) - 1)];
    }
    
    return $coupon_code;
}

function handle_generate_coupon() {
    global $wpdb;
    
    try {
        // Verify email was provided
        if (empty($_POST['email'])) {
            wp_send_json_error('لطفا ایمیل خود را وارد کنید.');
            return;
        }
        
        $email = sanitize_email($_POST['email']);
        
        // Validate email
        $email_validation = validate_email($email);
        if (!$email_validation['valid']) {
            wp_send_json_error($email_validation['message']);
            return;
        }
        
        // Check if email already used
        $existing_coupon = $wpdb->get_var($wpdb->prepare(
            "SELECT coupon_code FROM wp_coupons WHERE email = %s",
            $email
        ));
        
        if ($existing_coupon) {
            wp_send_json_error('این ایمیل قبلاً کد تخفیف دریافت کرده است.');
            return;
        }
        
        // Generate a unique coupon code
        do {
            $coupon_code = generate_random_coupon();
            $exists = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM wp_coupons WHERE coupon_code = %s",
                $coupon_code
            ));
        } while ($exists > 0);
        
        // Insert the new coupon
        $result = $wpdb->insert(
            'wp_coupons',
            array(
                'coupon_code' => $coupon_code,
                'email' => $email,
                'created_at' => current_time('mysql'),
                'is_used' => 0
            ),
            array('%s', '%s', '%s', '%d')
        );
        
        if ($result === false) {
            wp_send_json_error('خطا در ذخیره کد تخفیف');
            return;
        }
        
        // Send email notification
        $email_sent = send_coupon_notification($email, $coupon_code);
        
        wp_send_json_success(array(
            'coupon_code' => $coupon_code,
            'email_sent' => $email_sent
        ));
        
    } catch (Exception $e) {
        wp_send_json_error('خطای سیستمی');
    }
}

add_action('wp_ajax_generate_coupon_code', 'handle_generate_coupon');
add_action('wp_ajax_nopriv_generate_coupon_code', 'handle_generate_coupon');

// copun sending email for register form
function send_coupon_email($email, $coupon_code) {
    // Get the email template
    $template_path = get_template_directory() . '/email-templates/coupon-email.php';
    
    // Get register page URL
    $register_url = home_url('/register'); // Change this to your actual registration page URL
    
    // Load and process the email template
    ob_start();
    include $template_path;
    $email_content = ob_get_clean();
    
    // Replace placeholders
    $email_content = str_replace('%%COUPON_CODE%%', $coupon_code, $email_content);
    $email_content = str_replace('%%REGISTER_LINK%%', $register_url, $email_content);
    
    // Email headers
    $headers = array(
        'Content-Type: text/html; charset=UTF-8',
        'From: گاه‌شمار <info@gahshomar.com>',
        'Reply-To: info@gahshomar.com'
    );
    
    // Send email
    $subject = 'کد تخفیف شما در گاه‌شمار';
    
    return wp_mail($email, $subject, $email_content, $headers);
}

// Add this to your existing handle_generate_coupon function right after successful coupon creation
function send_coupon_notification($email, $coupon_code) {
    try {
        // Log email attempt
        error_log('Attempting to send coupon email to: ' . $email);
        
        $email_sent = send_coupon_email($email, $coupon_code);
        
        // Log email result
        if ($email_sent) {
            error_log('Successfully sent coupon email to: ' . $email);
        } else {
            error_log('Failed to send coupon email to: ' . $email);
        }
        
        return $email_sent;
    } catch (Exception $e) {
        error_log('Error sending coupon email: ' . $e->getMessage());
        return false;
    }
}

// Optional: Add this function to test SMTP configuration
function test_smtp_configuration() {
    $test_email = 'info@gahshomar.com';
    $subject = 'SMTP Test Email';
    $message = 'This is a test email to verify SMTP configuration.';
    $headers = array(
        'Content-Type: text/html; charset=UTF-8',
        'From: گاه‌شمار <info@gahshomar.com>'
    );
    
    $result = wp_mail($test_email, $subject, $message, $headers);
    
    if ($result) {
        error_log('SMTP Test: Email sent successfully');
    } else {
        error_log('SMTP Test: Email sending failed');
    }
    
    return $result;
}

//   ---------- فرم ثبت نام ------------- //

function register_registration_template($templates) {
    $templates['page-registration.php'] = 'Registration Form';
    return $templates;
}
add_filter('theme_page_templates', 'register_registration_template');

// Support Persian URLs
function custom_persian_permalinks($permalink) {
    return urldecode($permalink);
}
add_filter('permalink_structure', 'custom_persian_permalinks');

// Enable Persian slug support
function enable_persian_slugs() {
    add_filter('sanitize_title', function($title) {
        return urldecode($title);
    }, 10, 3);
}
add_action('init', 'enable_persian_slugs');





function add_acf_to_rest_api($response, $post, $request) {
    $acf_fields = get_fields($post->ID);
    if ($acf_fields) {
        $response->data['acf'] = $acf_fields;
    }
    return $response;
}
add_filter('rest_prepare_post', 'add_acf_to_rest_api', 10, 3);
add_filter('rest_prepare_YOUR_CUSTOM_POST_TYPE', 'add_acf_to_rest_api', 10, 3);

// forced add category to event post type
function add_category_support_to_event_post_type() {
    register_taxonomy_for_object_type('category', 'event'); // Ensures 'event' supports categories
}
add_action('init', 'add_category_support_to_event_post_type');



//+++++++++++++++++++++++++++++++++++++++++ Add Jalali calendar
require_once get_stylesheet_directory() . '/inc/jallali-calendar/persian-jalali-calendar.php';

// Add action for logged-in users
add_action('wp_ajax_load_template', 'custom_load_template');
// Add action for non-logged-in users
add_action('wp_ajax_nopriv_load_template', 'custom_load_template');

// Enqueue Jalali Calendar assets
function gahshomar_jallali_calendar_assets() {
    wp_enqueue_style(
        'pjc-styles',
        get_stylesheet_directory_uri() . '/inc/jallali-calendar/assets/css/pjc-style.css',
        [],
        HELLO_ELEMENTOR_CHILD_VERSION
    );
    
    wp_enqueue_script(
        'pjc-script',
        get_stylesheet_directory_uri() . '/inc/jallali-calendar/assets/js/pjc-script.js',
        ['jquery'],  // Added jQuery dependency since the script uses it
        HELLO_ELEMENTOR_CHILD_VERSION,
        true
    );
    
    // Localize script to provide AJAX URL
    wp_localize_script('pjc-script', 'pjcAjax', array(
        'ajax_url' => admin_url('admin-ajax.php')
    ));
}
add_action('wp_enqueue_scripts', 'gahshomar_jallali_calendar_assets');

// Handle AJAX requests for calendar day events and updates
add_action('wp_ajax_pjc_get_day_events', 'pjc_get_day_events');
add_action('wp_ajax_nopriv_pjc_get_day_events', 'pjc_get_day_events');

add_action('wp_ajax_pjc_update_calendar', 'pjc_update_calendar');
add_action('wp_ajax_nopriv_pjc_update_calendar', 'pjc_update_calendar');

add_action('wp_ajax_get_current_jalali', 'get_current_jalali');
add_action('wp_ajax_nopriv_get_current_jalali', 'get_current_jalali');

add_action('wp_ajax_convert_to_tabari', 'convert_to_tabari_ajax');
add_action('wp_ajax_nopriv_convert_to_tabari', 'convert_to_tabari_ajax');


// Register Event Cards assets
function enqueue_event_cards_assets() {
	wp_enqueue_style(
		'event-cards-custom-style',
		get_stylesheet_directory_uri() . '/event-cards/css/custom.css',
		[],
		HELLO_ELEMENTOR_CHILD_VERSION
	);

	wp_enqueue_style(
		'event-cards-mode-style', 
		get_stylesheet_directory_uri() . '/event-cards/css/mode-event.css',
		[],
		HELLO_ELEMENTOR_CHILD_VERSION
	);

	wp_enqueue_script(
		'event-cards-custom-script',
		get_stylesheet_directory_uri() . '/event-cards/js/custom.js',
		['jquery'],
		HELLO_ELEMENTOR_CHILD_VERSION,
		true
	);
}
add_action('wp_enqueue_scripts', 'enqueue_event_cards_assets');


function enqueue_footer_styles() {
    wp_enqueue_style(
        'footer-style',
        get_stylesheet_directory_uri() . '/assets/css/footer.css',
        [],
        HELLO_ELEMENTOR_CHILD_VERSION
    );
}
add_action('wp_enqueue_scripts', 'enqueue_footer_styles');