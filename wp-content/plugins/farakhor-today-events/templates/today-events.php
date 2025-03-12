<!-- templates/today-events.php -->
<div class="farakhor-today-events-wrapper">
    <div class="farakhor-today-header">
        <h3><?php echo esc_html($atts['title']); ?> (<?php echo $today_date; ?>)</h3>
    </div>
    
    <?php if (empty($events)): ?>
        <div class="farakhor-no-events">
            <p>هیچ رویدادی برای امروز وجود ندارد.</p>
        </div>
    <?php else: ?>
        <ul class="farakhor-today-events-list">
            <?php foreach ($events as $event): 
                $is_private = $event->event_type === 'private';
                $event_title = $is_private ? $event->event_title : $event->event_title;
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
                        <a href="<?php echo $link; ?>" class="farakhor-today-more-link">بیشتر بخوانید</a>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>