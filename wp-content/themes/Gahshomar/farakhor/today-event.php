<?php
/**
 * Today Events List
 * Description: Display today's events from the wp_farakhor table and private events in a simple list for mobile.
 */

// Function to get today's events (both public and private)
function farakhor_fetch_today_events() {
    global $wpdb;
    $public_table = $wpdb->prefix . 'farakhor';
    $private_table = $wpdb->prefix . 'user_events';
    
    // Get today's date in Persian (Jalali)
    $today = new Verta();
    $today_month = $today->format('m');
    $today_day = $today->format('d');
    $today_persian_format = sprintf("%02d-%02d", intval($today_month), intval($today_day));
    
    // Fetch public events for today
    $public_events = $wpdb->get_results($wpdb->prepare(
        "SELECT *, 'public' as event_type FROM $public_table 
        WHERE module = 'YES' AND persian_day = %s",
        $today_persian_format
    ));
    
    // Fetch private events for logged-in user
    $private_events = array();
    if (is_user_logged_in()) {
        $current_user = wp_get_current_user();
        $user_data = get_event_user_data($current_user->ID);
        
        if ($user_data) {
            $private_events = $wpdb->get_results($wpdb->prepare(
                "SELECT *, 'private' as event_type FROM $private_table 
                WHERE user_id = %d AND event_date = %s",
                $user_data['user_id'],
                $today_persian_format
            ));
        }
    }
    
    // Merge public and private events
    return array_merge($public_events, $private_events);
}

// Function to display today's events as a simple list
function farakhor_today_events_list() {
    ob_start();
    
    // Get today's events
    $today_events = farakhor_fetch_today_events();
    
    // Get today's Persian date
    $today = new Verta();
    $persianMonths = [
        "01" => "فروردین",
        "02" => "اردیبهشت",
        "03" => "خرداد",
        "04" => "تیر", 
        "05" => "امرداد",
        "06" => "شهریور",
        "07" => "مهر",
        "08" => "آبان",
        "09" => "آذر",
        "10" => "دی",
        "11" => "بهمن", 
        "12" => "اسپند"
    ];
    
    $today_day = convertNumbersToPersian($today->format('d'));
    $today_month = $persianMonths[$today->format('m')];
    $today_formatted = "$today_day $today_month";
    
    // Output HTML
    ?>
    <div class="farakhor-today-events-container">
        <div class="farakhor-today-header">
            <h3>رویدادهای امروز (<?php echo $today_formatted; ?>)</h3>
        </div>
        
        <?php if (empty($today_events)): ?>
            <div class="farakhor-no-events">
                <p>هیچ رویدادی برای امروز وجود ندارد.</p>
            </div>
        <?php else: ?>
            <ul class="farakhor-today-events-list">
                <?php foreach ($today_events as $event): 
                    $is_private = $event->event_type === 'private';
                    $event_title = $is_private ? $event->event_title : $event->event_title;
                    $event_text = $is_private ? $event->event_description : $event->event_text;
                    $link = $is_private ? '#' : (!empty($event->post_link) ? esc_url($event->post_link) : esc_url($event->event_link));
                ?>
                    <li class="farakhor-today-event-item <?php echo $is_private ? 'private-event' : ''; ?>">
                        <div class="farakhor-today-event-title">
                            <?php if ($is_private): ?>
                                <span class="farakhor-private-badge">خصوصی</span>
                            <?php endif; ?>
                            <h4><?php echo esc_html($event_title); ?></h4>
                        </div>
                        <?php if (!$is_private && !empty($link) && $link !== '#'): ?>
                            <a href="<?php echo $link; ?>" class="farakhor-today-more-link">بیشتر</a>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
    
    <style>
        .farakhor-today-events-container {
            font-family: 'Digi Hamishe Regular', sans-serif;
            direction: rtl;
            max-width: 100%;
            margin: 0 auto;
            padding: 10px;
            border-radius: 8px;
            background-color: #f5f5f5;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .farakhor-today-header {
            border-bottom: 2px solid #FF8200;
            margin-bottom: 15px;
            padding-bottom: 5px;
        }
        
        .farakhor-today-header h3 {
            color: #333;
            margin: 0;
            font-size: 16px;
        }
        
        .farakhor-today-events-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .farakhor-today-event-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            border-bottom: 1px solid #ddd;
            background-color: #fff;
            margin-bottom: 5px;
            border-radius: 4px;
        }
        
        .farakhor-today-event-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }
        
        .farakhor-today-event-title {
            flex: 1;
        }
        
        .farakhor-today-event-title h4 {
            margin: 0;
            font-size: 14px;
            color: #333;
        }
        
        .farakhor-private-badge {
            display: inline-block;
            background-color: #6c757d;
            color: white;
            font-size: 10px;
            padding: 2px 5px;
            border-radius: 3px;
            margin-left: 5px;
        }
        
        .farakhor-today-more-link {
            color: #FF8200;
            text-decoration: none;
            font-size: 12px;
            font-weight: bold;
        }
        
        .farakhor-no-events p {
            text-align: center;
            color: #666;
            font-size: 14px;
            margin: 20px 0;
        }
        
        @media only screen and (max-width: 767px) {
            .farakhor-today-events-container {
                padding: 8px;
            }
            
            .farakhor-today-event-item {
                padding: 8px;
            }
            
            .farakhor-today-event-title h4 {
                font-size: 13px;
            }
        }
    </style>
    <?php
    
    return ob_get_clean();
}

// Create shortcode for today's events list
add_shortcode('today_farakhor_events', 'farakhor_today_events_list');