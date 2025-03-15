<?php
/**
 * Template for Farakhor Events cards.
 *
 * @var array $events Array of events.
 * @var array $atts Shortcode attributes.
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}
?>

<div class="farakhor-container" data-limit="<?php echo esc_attr($atts['limit']); ?>">
    <div class="farakhor-header">
        <h2 class="farakhor-title"><?php esc_html_e('رویدادهای تقویم', 'farakhor-events'); ?></h2>
        
        <div class="farakhor-filters">
            <div class="farakhor-search">
                <input type="text" id="farakhor-search-input" placeholder="<?php esc_attr_e('جستجو در رویدادها...', 'farakhor-events'); ?>">
                <div class="farakhor-search-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                </div>
            </div>
            
            <div class="farakhor-month-filter">
                <select id="farakhor-month-select">
                    <option value="all"><?php esc_html_e('همه ماه‌ها', 'farakhor-events'); ?></option>
                    <?php 
                    $current_month = farakhor_get_current_jalali_month();
                    for ($i = 1; $i <= 12; $i++) : 
                        $selected = ($i == $current_month) ? 'selected' : '';
                    ?>
                        <option value="<?php echo esc_attr($i); ?>" <?php echo $selected; ?>><?php echo esc_html(farakhor_get_persian_month_name($i)); ?></option>
                    <?php endfor; ?>
                </select>
                <div class="farakhor-arrow-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </div>
            </div>
        </div>
    </div>
    
    <div id="farakhor-cards-container" class="farakhor-cards">
        <?php if (!empty($events)) : ?>
            <?php foreach ($events as $event) : ?>
                <a href="<?php echo esc_url($event['post_link']); ?>" class="farakhor-card">
                    <div class="farakhor-card-bg" style="background-image: url('<?php echo esc_url($event['image']); ?>')"></div>
                    <div class="farakhor-card-overlay"></div>
                    <div class="farakhor-card-content">
                        <div class="farakhor-card-top">
                            <div class="farakhor-card-date">
                                <span class="farakhor-persian-date"><?php echo esc_html($event['persian_full_date']); ?></span>
                                <span class="farakhor-gregorian-date"><?php echo esc_html($event['gregorian_formatted']); ?></span>
                            </div>
                            <div class="farakhor-card-remain"><?php echo esc_html($event['remaining_days']); ?></div>
                        </div>
                        
                        <h3 class="farakhor-card-title"><?php echo esc_html($event['event_title']); ?></h3>
                        
                        <div class="farakhor-card-bottom">
                            <?php if (!empty($event['filtered_categories'])) : ?>
                                <?php foreach ($event['filtered_categories'] as $category) : ?>
                                    <span class="farakhor-category"><?php echo esc_html($category); ?></span>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            
                            <?php if (!empty($event['tags_array'])) : ?>
                                <?php foreach ($event['tags_array'] as $tag) : ?>
                                    <span class="farakhor-tag"><?php echo esc_html($tag); ?></span>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        <?php else : ?>
            <div class="farakhor-no-result">
                <?php esc_html_e('هیچ رویدادی یافت نشد.', 'farakhor-events'); ?>
            </div>
        <?php endif; ?>
    </div>
    
    <div id="farakhor-loading" class="farakhor-loading" style="display: none;">
        <svg xmlns="http://www.w3.org/2000/svg" width="38" height="38" viewBox="0 0 38 38">
            <defs>
                <linearGradient x1="8.042%" y1="0%" x2="65.682%" y2="23.865%" id="spinner-gradient">
                    <stop stop-color="#3498db" stop-opacity="0" offset="0%"/>
                    <stop stop-color="#3498db" stop-opacity=".631" offset="63.146%"/>
                    <stop stop-color="#3498db" offset="100%"/>
                </linearGradient>
            </defs>
            <g fill="none" fill-rule="evenodd">
                <g transform="translate(1 1)">
                    <path d="M36 18c0-9.94-8.06-18-18-18" stroke="url(#spinner-gradient)" stroke-width="2">
                        <animateTransform attributeName="transform" type="rotate" from="0 18 18" to="360 18 18" dur="0.9s" repeatCount="indefinite"/>
                    </path>
                </g>
            </g>
        </svg>
    </div>

    <!-- Template for use with JavaScript when updating cards -->
    <template id="farakhor-card-template">
        <a href="" class="farakhor-card">
            <div class="farakhor-card-bg"></div>
            <div class="farakhor-card-overlay"></div>
            <div class="farakhor-card-content">
                <div class="farakhor-card-top">
                    <div class="farakhor-card-date">
                        <span class="farakhor-persian-date"></span>
                        <span class="farakhor-gregorian-date"></span>
                    </div>
                    <div class="farakhor-card-remain"></div>
                </div>
                <h3 class="farakhor-card-title"></h3>
                <div class="farakhor-card-bottom"></div>
            </div>
        </a>
    </template>
</div>