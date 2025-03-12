<?php
// templates/list-view.php
?>
<div class="wrap">
    <h1>Farakhor Data</h1>

    <div class="tablenav top">
        <div class="alignleft actions">
            <a href="<?php echo admin_url('admin.php?page=farakhor-data-new'); ?>" class="button button-primary">Add New</a>
        </div>
        
        <form method="get" class="alignright">
            <input type="hidden" name="page" value="farakhor-data">
            <p class="search-box">
                <input type="search" name="search" value="<?php echo esc_attr($search); ?>" placeholder="Search entries...">
                <input type="submit" class="button" value="Search">
            </p>
        </form>
        <div class="clear"></div>
    </div>

    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th width="50">ID</th>
                <th width="80">Persian Day</th>
                <th width="100">Gregorian Day</th>
                <th width="150">Event Title</th>
                <th width="200">Event Text</th>
                <th width="100">Images</th>
                <th width="100">Categories</th>
                <th width="100">Module</th>
                <th width="120">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($items)): ?>
                <tr>
                    <td colspan="9">No items found.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?php echo esc_html($item->id); ?></td>
                        <td><?php echo esc_html($item->persian_day); ?></td>
                        <td><?php echo esc_html($item->gregorian_day); ?></td>
                        <td><?php echo esc_html($item->event_title); ?></td>
                        <td><?php echo wp_trim_words($item->event_text, 20); ?></td>
                        <td>
                            <?php if (!empty($item->image)): ?>
                                <img src="<?php echo esc_url($item->image); ?>" alt="Event Image" style="max-width: 50px; max-height: 50px;">
                            <?php endif; ?>
                        </td>
                        <td><?php echo esc_html($item->categories); ?></td>
                        <td><?php echo esc_html($item->module); ?></td>
                        <td>
                            <a href="<?php echo add_query_arg(['action' => 'edit', 'id' => $item->id]); ?>" 
                               class="button button-small">
                                <span class="dashicons dashicons-edit"></span> Edit
                            </a>
                            <a href="#" class="button button-small delete-item" data-id="<?php echo esc_attr($item->id); ?>">
                                <span class="dashicons dashicons-trash"></span> Delete
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <?php if ($total_pages > 1): ?>
        <div class="tablenav bottom">
            <div class="tablenav-pages">
                <span class="pagination-links">
                    <?php
                    echo paginate_links(array(
                        'base' => add_query_arg('paged', '%#%'),
                        'format' => '',
                        'prev_text' => __('&laquo;'),
                        'next_text' => __('&raquo;'),
                        'total' => $total_pages,
                        'current' => $current_page
                    ));
                    ?>
                </span>
            </div>
        </div>
    <?php endif; ?>
</div>

 