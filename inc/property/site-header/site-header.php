<?php
function property_custom_header_callback()
{


    ?>
    <a class="skip-link" href="#main"><?php esc_html_e('Skip to content', 'property-directory'); ?></a>

    <header class="site-header">

        <div class="site-header-inner">
            <?php
            if (has_custom_logo()) {
                the_custom_logo();
            } elseif (function_exists('pd_brand_html')) {
                echo pd_brand_html(); // Escaped inside the helper.
            } else {
                ?>
                <a class="brand" href="<?php echo esc_url(home_url('/')); ?>">
                    <span class="brand-mark" aria-hidden="true">⌂</span>
                    <?php bloginfo('name'); ?>
                </a>
                <?php
            }
            ?>

            <nav class="main-nav" id="primary-nav" aria-label="<?php esc_attr_e('Main navigation', 'realproperty'); ?>">
                <?php
                wp_nav_menu(
                    array(
                        'theme_location' => 'primary',
                        'container'      => false,
                        'menu_class'     => 'menu',
                        'depth'          => 0,
                        'fallback_cb'    => function_exists('pd_primary_nav_fallback') ? 'pd_primary_nav_fallback' : 'wp_page_menu',
                    )
                );
                ?>
                <a class="nav-cta-mobile"
                    href="<?php echo esc_url(site_url('/contact-us')); ?>"><?php esc_html_e('List a property', 'property-directory'); ?></a>
            </nav>

            <nav class="main-nav mobile-nav" id="mobile-nav" aria-label="<?php esc_attr_e('Mobile navigation', 'realproperty'); ?>">
                <?php
                wp_nav_menu(
                    array(
                        'theme_location' => 'primary',
                        'container'      => false,
                        'menu_class'     => 'menu',
                        'depth'          => 0,
                        'fallback_cb'    => function_exists('pd_primary_nav_fallback') ? 'pd_primary_nav_fallback' : 'wp_page_menu',
                    )
                );
                ?>
                <a class="nav-cta-mobile"
                    href="<?php echo esc_url(site_url('/contact-us')); ?>"><?php esc_html_e('List a property', 'property-directory'); ?></a>
            </nav>

            <a class="header-cta"
                href="<?php echo esc_url(function_exists('pd_list_property_url') ? pd_list_property_url() : site_url('/contact-us')); ?>"><?php esc_html_e('List a property', 'property-directory'); ?></a>

            <button class="nav-toggle" type="button" aria-controls="primary-nav" aria-expanded="false">
                <span class="nav-toggle-bars" aria-hidden="true"></span>
                <span class="screen-reader-text"><?php esc_html_e('Menu', 'property-directory'); ?></span>
            </button>
        </div>
    </header>

<?php } ?>

<?php
add_action('property_custom_header', 'property_custom_header_callback');
?>