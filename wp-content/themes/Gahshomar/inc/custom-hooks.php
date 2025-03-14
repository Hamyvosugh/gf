<?php
// Custom hooks and filters

add_filter('the_content', 'prepend_custom_greeting');

function prepend_custom_greeting($content) {
    if (is_single()) {
        $content = custom_greeting_message('درود بر شما' ) . $content;
    }
    return $content;
}
?>