<?php
namespace Farakhor;

require_once __DIR__ . '/../vendor/autoload.php';

use Hekmatinasser\Verta\Verta;

function fetch_event_data() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'Farakhor'; // use  the correct table name
    $results = $wpdb->get_results("SELECT * FROM $table_name", ARRAY_A);
    if (empty($results)) {
        error_log('No data fetched from the Farakhor table.');
    } else {
        error_log('Data fetched from the Farakhor table: ' . print_r($results, true));
    }
    return $results;
}

function render_event_cards() {
    $events = fetch_event_data();
    if (empty($events)) {
        return '<p>No events found.</p>';
    }
    ob_start();
    ?>
    <div class="container">
        <div class="filter-controls">
            <select id="month-filter">
                <option value="all">ماه ها</option>
                <?php
                $months = array_unique(array_column($events, 'Month'));
                foreach ($months as $month) {
                    echo "<option value='{$month}'>{$month}</option>";
                }
                ?>
            </select>
            <select id="important-filter">
                <option value="all">همه</option>
                <option value="yes">مهم ها</option>
            </select>
            <select id="view-filter">
                <option value="months">ماه ها</option>
                <option value="weeks">هفته ها</option>
                <option value="important">مهم ها</option>
            </select>
        </div>

        <div class="gallery-container">
            <div class="row" id="card-container">
                <?php foreach ($events as $event): ?>
                <?php
                    // Example of using Verta to convert a date to Persian date
                    $persianDate = new Verta($event['Georgian']);
                ?>
                <div class="col-md-3 card-item" data-month="<?php echo $event['Month']; ?>" data-important="<?php echo $event['Important']; ?>">
                    <div class="card farakhor-flip-cardd">
                        <div class="farakhor-flip-card-inner">
                            <div class="farakhor-flip-card-front">
                                <div class="card-body">
                                    <div class="card-text persian-day-event"><?php echo $event['PersianDayNumber']; ?></div>
                                    <div class="card-text month-event"><?php echo $event['Month']; ?></div>
                                    <div class="card-text event-title-event"><?php echo $event['EventTitle']; ?></div>
                                    <div class="card-text georgian-event"><?php echo $persianDate->format('Y/n/j'); ?></div>
                                </div>
                            </div>
                            <div class="farakhor-flip-card-back">
                                <?php if ($event['ModalStatus'] == 'true'): ?>
                                <img src="<?php echo $event['Logo']; ?>" class="logo-event" />
                                <?php endif; ?>
                                <div class="card-text text-event"><?php echo $event['Text']; ?></div>
                                <a href="#" class="btn btn-primary" target="_blank">بیشتر بدانید</a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
?>