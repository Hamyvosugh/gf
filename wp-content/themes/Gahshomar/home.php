<?php
/**
 * Template Name: Home Page
 */

get_header(); ?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">
        <?php
        if (class_exists('\Elementor\Plugin')) {
            // Get the page content
            while (have_posts()) :
                the_post();
                the_content();
            endwhile;
        } else {
            echo 'Elementor is not installed or activated.';
        }
        ?>
    </main>
</div>

<?php get_footer(); ?>