<?php
/* Template Name: Event  Cards */

get_header();
require_once get_stylesheet_directory() . '/event-cards/includes/event-cards-functions.php';

$events = Farakhor\fetch_event_data();
?>

<div class="container">
    <?php
    while ( have_posts() ) : the_post();
        the_content();
    endwhile;
    ?>
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
            <div class="card-item" data-month="<?php echo $event['Month']; ?>" data-important="<?php echo $event['Important']; ?>">
                <div class="card farakhor-flip-card">
                    <div class="farakhor-farakhor-flip-card -inner">
                        <div class="farakhor-farakhor-flip-card -front">
                            <!-- Persian day number at the top center -->
                            <div class="persian-day-event">
                                <?php echo $event['PersianDayNumber']; ?>
                            </div>
                            <!-- Month -->
                            <div class="card-text month-event">
                                <?php echo $event['Month']; ?>
                            </div>
                            <!-- Event title -->
                            <div class="card-text event-title-event">
                                <?php echo $event['EventTitle']; ?>
                            </div>
                            <!-- Georgian date -->
                            <div class="card-text georgian-event">
                                <?php echo $persianDate->format('Y/n/j'); ?>
                            </div>
                        </div>
                        <div class="farakhor-farakhor-flip-card -back">
                            <?php if ($event['ModalStatus'] == 'true'): ?>
                            <!-- Logo -->
                            <img src="<?php echo $event['Logo']; ?>" class="logo-event" />
                            <?php endif; ?>
                            <!-- Text -->
                            <div class="card-text text-event">
                                <?php echo $event['Text']; ?>
                            </div>
                            <!-- More info button -->
                            <a href="#" class="btn btn-primary" target="_blank">بیشتر بدانید</a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php get_footer(); ?>