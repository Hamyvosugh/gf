<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>فهرست رویدادها</title>
    <!-- بارگذاری استایل‌ها -->
    <link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri(); ?>/farakhor/assets/css/farakhor.css">
</head>
<body>
<?php
global $wpdb;

// Load the Verta library from the root vendor directory
require_once($_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php');
use Hekmatinasser\Verta\Verta;

// Fetch public events
$results = $wpdb->get_results("SELECT *, 'public' as event_type FROM {$wpdb->prefix}farakhor WHERE module = 'YES'");

// Fetch private events for logged-in user
//$private_events = array();
//if (is_user_logged_in()) {
 //   $current_user = wp_get_current_user();
  //  $user_data = get_event_user_data($current_user->ID);
    
 //   if ($user_data) {
   //     $private_events = $wpdb->get_results($wpdb->prepare(
    //       "SELECT *, 'private' as event_type FROM {$wpdb->prefix}user_events WHERE user_id = %d",
    //        $user_data['user_id']
    //    ));
 //   }
//}

// Merge public and private events
$results = array_merge($results);
// $results = array_merge($results, $private_events);

if ($results) {
    // Get today's date in Persian (Jalali)
    $today = new Verta();
    $today->setTime(0, 0, 0); // Ensure we compare only the date part
    $currentYear = convertNumbersToPersian($today->year);
    
    // Sort all events by date
    usort($results, function($a, $b) {
        $dateA = $a->event_type === 'public' ? $a->persian_day : $a->event_date;
        $dateB = $b->event_type === 'public' ? $b->persian_day : $b->event_date;
        list($monthA, $dayA) = explode('-', $dateA);
        list($monthB, $dayB) = explode('-', $dateB);
        return (intval($monthA) * 31 + intval($dayA)) - (intval($monthB) * 31 + intval($dayB));
    });

    // Fixed header for search and sort buttons
    echo '<div class="farakhor-search-sort-header" >';

    // Search container
    echo '<div class="farakhor-search-container">';
    echo '<input type="text" id="search-input" placeholder="جستجو بر اساس عنوان، محتوا و یا تاریخ..." onkeyup="filterEvents()" >'; // Search Input
    echo '</div>';

    // Sort buttons container
    echo '<div class="farakhor-sort-buttons-container" >';

    // Dropdown for selecting months
    $currentPersianMonth = (new Verta())->month;
    $currentPersianMonthFormatted = sprintf("%02d", $currentPersianMonth);

    echo '<select id="month-filter" onchange="filterByMonth(this.value)">';

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

    foreach ($persianMonths as $key => $monthName) {
        $selected = ($key === $currentPersianMonthFormatted) ? 'selected' : '';
        echo '<option value="' . $key . '" ' . $selected . '>' . $monthName . '</option>';
    }

    echo '</select>';

    echo '<div class="farakhor-month-title">نمایش فراخورهای ماه:</div>';

    // Sort buttons
    echo '<button onclick="filterByTag(\'جشن\')" style="padding: 5px 10px; background-color: #FF8200; color: #ffffff; border: none; cursor: pointer; border-radius: 5px; font-family: \'Digi Hamishe Regular\', sans-serif;">جشن ها</button>';
    // Reset filters button
    echo '<button class="farakhor-refresh-button" onclick="resetToCurrentMonth()" title="بازگشت به حالت اولیه" style="background: none; border: none; cursor: pointer; display: flex; align-items: center;">';
    echo '<img src="https://gahshomar.com/wp-content/uploads/2025/01/refresh-cw-svgrepo-com.svg" alt="refresh icon" class="farakhor-refresh-icon" style="width: 32px; height: 32px;">';
    echo '</button>';

    echo '</div>'; // Close sort buttons container
    echo '</div>'; // Close farakhor-search-sort-header

    // Start events container for the grid layout
    echo '<div class="farakhor-events-wrapper">';
    foreach ($results as $event) {
        $is_private = $event->event_type === 'private';
        
        if ($is_private) {
            $persianDateFormatted = convert_persian_date($event->event_date);
            // Add this block to calculate Gregorian date from Persian date
            try {
                list($month, $day) = explode('-', $event->event_date);
                $verta = Verta::parse(sprintf("%s-%s-%s", $today->year, intval($month), intval($day)));
                $gregorian = $verta->DateTime();
                $gregorianDateFormatted = convert_gregorian_date($gregorian->format('Y-m-d'));
            } catch (Exception $e) {
                $gregorianDateFormatted = date('d F'); // Fallback to current date if conversion fails
            }
            $event_title = $event->event_title;
            $event_text = $event->event_description;
            $link = '#';
            $image = get_stylesheet_directory_uri() . '/farakhor/assets/images/private-event.jpg';
        } else {
            if ($event->module !== 'YES') {
                continue;
            }
            $persianDateFormatted = convert_persian_date($event->persian_day);
            $gregorianDateFormatted = convert_gregorian_date($event->gregorian_day);
            $event_title = $event->event_title;
            $event_text = $event->event_text;
            $link = !empty($event->post_link) ? esc_url($event->post_link) : esc_url($event->event_link);
            $image = esc_url($event->image);
        }

        // Calculate days difference
        try {
            if ($is_private) {
                list($eventMonth, $eventDay) = explode('-', $event->event_date);
            } else {
                list($eventMonth, $eventDay) = explode('-', $event->persian_day);
            }
            $eventDate = Verta::parse(sprintf("%s-%s-%s", $today->year, intval($eventMonth), intval($eventDay)));
            $daysDifference = $eventDate->diffDays($today);
            
            // Determine if this event is today
            $isToday = $daysDifference === 0;
            
            if ($eventDate->greaterThan($today)) {
                $dayStatus = convertNumbersToPersian($daysDifference) . ' روز مانده';
            } elseif ($eventDate->lessThan($today)) {
                $dayStatus = convertNumbersToPersian(abs($daysDifference)) . ' روز گذشته';
            } else {
                $dayStatus = 'امروز';
            }
        } catch (Exception $e) {
            $dayStatus = "تاریخ نامعتبر";
            $isToday = false;
        }

        // Create the flip card  layout
        echo '<div class="farakhor-flip-card clickable-card ' . 
     ($is_private ? 'private-event' : '') . 
     ($isToday ? ' today-highlight' : '') . 
     '" data-url="' . $link . '" 
     data-date="' . ($is_private ? $event->event_date : $event->persian_day) . '"
     data-tag="' . (
         strpos($event->event_title, 'تعطیل') !== false || 
         strpos($event->event_title, 'تعطیلات') !== false ? 'تعطیل ' : ''
     ) . (strpos($event->event_title, 'جشن') !== false ? 'جشن ' : '') . '">';
        echo '  <div class="farakhor-flip-card-inner">';
        echo '    <div class="farakhor-flip-card-front" style="--card-bg: url(\'' . $image . '\');">';
        echo '      <div class="overlay"></div>';
        if ($is_private) {
            echo '      <div class="private-badge">خصوصی</div>';
        }
        echo '      <div class="card-title">';
        echo '        <h4 class="farakhor-event-title">' . esc_html($event_title) . '</h4>';
        echo '      </div>';
        echo '      <div class="card-dates">';
        echo '        <p class="farakhor-persian-date">' . esc_html($persianDateFormatted) . '</p>';
        echo '        <div class="farakhor-bottom-info">';
        echo '          <p class="remaining-days">' . esc_html($dayStatus) . '</p>';
        echo '          <p class="farakhor-gregorian-date">' . esc_html($gregorianDateFormatted) . '</p>';
        echo '        </div>';
        echo '      </div>';
        echo '    </div>';
        echo '    <div class="farakhor-flip-card-back">';
        echo '      <span>' . esc_html($event_text) . '</span>';
        if (!$is_private) {
            echo '      <a href="' . $link . '" class="farakhor-read-more-btn">بیشتر بخوانید</a>';
        }
        echo '    </div>';
        echo '  </div>';
        echo '</div>';
    }
    echo '</div>';
} else {
    echo '<p>هیچ رویدادی پیدا نشد.</p>';
}

// Utility functions
function convert_persian_date($persian_day) {
    $months = ['فروردین', 'اردیبهشت', 'خرداد', 'تیر', 'امرداد', 'شهریور', 'مهر', 'آبان', 'آذر', 'دی', 'بهمن', 'اسپند'];
    list($month, $day) = explode('-', $persian_day);
    $day = convertNumbersToPersian(intval($day));
    $month = $months[intval($month) - 1];
    return "{$day} {$month}";
}

function convert_gregorian_date($gregorian_day) {
    $months = ['January' => 'ژانویه', 'February' => 'فوریه', 'March' => 'مارس', 'April' => 'آوریل', 'May' => 'مه', 'June' => 'ژوئن', 'July' => 'ژوئیه', 'August' => 'اوت', 'September' => 'سپتامبر', 'October' => 'اکتبر', 'November' => 'نوامبر', 'December' => 'دسامبر'];
    $date = new DateTime($gregorian_day);
    $day = convertNumbersToPersian($date->format('d'));
    $month = $months[$date->format('F')];
    return "{$day} {$month}";
}
?>



<script src="<?php echo get_stylesheet_directory_uri(); ?>/farakhor/assets/js/Farakhor.js"></script>
</body>
</html>