<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @package Gahshomar_Child
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}
?>

</div><!-- #content -->

<?php
// Only show desktop footer for desktop screens (will be hidden via CSS for mobile/tablet)
?>
<footer id="site-footer" class="gahshomar-footer desktop-only">
    <div class="gahshomar-footer-container">
        <div class="gahshomar-footer-main">
            <!-- Footer Left Section: Logo and Menu -->
            <div class="gahshomar-footer-left">
                <?php if (has_custom_logo()) : ?>
                    <div class="gahshomar-footer-logo">
                        <a href="<?php echo esc_url(home_url('/')); ?>">
                            <?php the_custom_logo(); ?>
                        </a>
                    </div>
                <?php else : ?>
                    <div class="gahshomar-footer-logo">
                        <a href="<?php echo esc_url(home_url('/')); ?>">
                            <img src="<?php echo esc_url(get_stylesheet_directory_uri() . '/assets/images/gahshomar.svg'); ?>" alt="<?php echo esc_attr(get_bloginfo('name')); ?>" class="gahshomar-logo-img">
                        </a>
                    </div>
                <?php endif; ?>
                
                <?php if (has_nav_menu('footer-menu')) : ?>
                    <nav class="gahshomar-footer-menu" aria-label="<?php esc_attr_e('Footer Menu', 'gahshomar-child'); ?>">
                        <?php
                        wp_nav_menu(array(
                            'theme_location' => 'footer-menu',
                            'menu_class'     => 'gahshomar-footer-menu-list',
                            'container'      => false,
                            'depth'          => 1,
                            'fallback_cb'    => false,
                        ));
                        ?>
                    </nav>
                <?php endif; ?>
            </div>
            
            <!-- Footer Right Section: Social Icons -->
            <div class="gahshomar-footer-right">
                <ul class="gahshomar-social-icons">
                    <?php 
                    // Define social media links
                    $social_links = array(
                        'telegram' => array(
                            'url' => 'https://www.instagram.com/gahshomar.iran/',
                            'icon' => get_stylesheet_directory_uri() . '/assets/images/telegram.svg',
                            'alt' => 'تلگرام'
                        ),
                        'instagram' => array(
                            'url' => 'https://www.instagram.com/gahshomar.iran/',
                            'icon' => get_stylesheet_directory_uri() . '/assets/images/instagram.svg',
                            'alt' => 'اینستاگرام'
                        ),
                        'youtube' => array(
                            'url' => 'https://www.youtube.com/@gahshomar.iranian',
                            'icon' => get_stylesheet_directory_uri() . '/assets/images/youtube.svg',
                            'alt' => 'یوتوب'
                        )
                    );
                    
                    // Loop through social links
                    foreach ($social_links as $platform => $data) :
                        if (!empty($data['url'])) : ?>
                            <li class="gahshomar-social-item">
                                <a href="<?php echo esc_url($data['url']); ?>" target="_blank" rel="noopener" aria-label="<?php echo esc_attr($data['alt']); ?>">
                                    <img src="<?php echo esc_url($data['icon']); ?>" alt="<?php echo esc_attr($data['alt']); ?>" class="gahshomar-social-icon gahshomar-<?php echo esc_attr($platform); ?>-icon" loading="lazy" width="24" height="24">
                                </a>
                            </li>
                        <?php endif;
                    endforeach; ?>
                </ul>
            </div>
        </div>
        
        <!-- Footer Bottom: Text and Copyright -->
        <div class="gahshomar-footer-bottom">
            <p class="gahshomar-footer-branding">گاه شمار<br>𐎥𐎠𐏃𐏁𐎢𐎷𐎠𐎼</p>
            <p class="gahshomar-footer-copyright">&copy; <?php echo esc_html(date("Y")); ?> Gahshomar.com <?php esc_html_e('All rights reserved', 'gahshomar-child'); ?></p>
        </div>
    </div>
</footer>

<?php
// Mobile navigation (will be hidden via CSS for desktop)
?>
<div class="gahshomar-mobile-nav mobile-only" id="gahshomar-mobile-nav">
    <div class="gahshomar-mobile-secondary">
        <a href="<?php echo esc_url(home_url('/contact')); ?>" class="gahshomar-mobile-item"><?php esc_html_e('ارتباط با ما', 'gahshomar-child'); ?></a>
        <button type="button" class="gahshomar-mobile-close" aria-label="<?php esc_attr_e('Close menu', 'gahshomar-child'); ?>">✕</button>
    </div>
    
    <div class="gahshomar-mobile-primary">
        <a href="#saraghaz" class="gahshomar-mobile-item"><?php esc_html_e('سرآغازها', 'gahshomar-child'); ?></a>
        <a href="#convert" class="gahshomar-mobile-item"><?php esc_html_e('تبدیل', 'gahshomar-child'); ?></a>
        <a href="#gahshomar" class="gahshomar-mobile-item"><?php esc_html_e('تقویم', 'gahshomar-child'); ?></a>
        <a href="#farakhor" class="gahshomar-mobile-item"><?php esc_html_e('مناسبتها', 'gahshomar-child'); ?></a>
        <a href="#headtoday" class="gahshomar-mobile-item"><?php esc_html_e('امروز', 'gahshomar-child'); ?></a>
        <button type="button" class="gahshomar-mobile-toggle" aria-expanded="false" aria-label="<?php esc_attr_e('Toggle menu', 'gahshomar-child'); ?>">☰</button>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Mobile menu functionality
    const menuToggle = document.querySelector('.gahshomar-mobile-toggle');
    const menuClose = document.querySelector('.gahshomar-mobile-close');
    const secondaryMenu = document.querySelector('.gahshomar-mobile-secondary');
    const mobileItems = document.querySelectorAll('.gahshomar-mobile-item');
    
    if (menuToggle && menuClose && secondaryMenu) {
        // Toggle menu open
        menuToggle.addEventListener('click', function() {
            secondaryMenu.classList.add('gahshomar-show');
            menuToggle.setAttribute('aria-expanded', 'true');
        });
        
        // Close menu
        menuClose.addEventListener('click', function() {
            secondaryMenu.classList.remove('gahshomar-show');
            menuToggle.setAttribute('aria-expanded', 'false');
        });
        
        // Handle menu item clicks
        mobileItems.forEach(function(item) {
            item.addEventListener('click', function(e) {
                // Remove active class from all items
                mobileItems.forEach(function(el) {
                    el.classList.remove('gahshomar-active');
                });
                
                // Add active class to clicked item
                this.classList.add('gahshomar-active');
                
                // Handle anchor links for smooth scrolling
                const href = this.getAttribute('href');
                if (href && href.startsWith('#') && href !== '#') {
                    e.preventDefault();
                    const targetElement = document.querySelector(href);
                    if (targetElement) {
                        // Close secondary menu
                        secondaryMenu.classList.remove('gahshomar-show');
                        menuToggle.setAttribute('aria-expanded', 'false');
                        
                        // Smooth scroll to target
                        targetElement.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                }
            });
        });
    }
});
</script>

<?php wp_footer(); ?>

</body>
</html>