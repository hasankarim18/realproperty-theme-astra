<?php
function rp_site_footer_callback()
{
    ?>
    <?php
    // Edit these three lines.
    $contact = array(
        'address' => 'Dhaka, Bangladesh',
        'phone' => '+880 1XXX-XXXXXX',
        'email' => 'hello@example.com',
    );

    $types = get_terms(array('taxonomy' => 'property_type', 'hide_empty' => false, 'number' => 6));
    $locations = get_terms(array('taxonomy' => 'property_location', 'hide_empty' => false, 'number' => 6));
    $types = is_wp_error($types) ? array() : $types;
    $locations = is_wp_error($locations) ? array() : $locations;
    ?>
    <footer class="site-footer">
        <div class="footer-cta">
            <div class="footer-cta-box">
                <div>
                    <h2><?php esc_html_e('Have a property to sell or rent?', 'property-directory'); ?></h2>
                    <p><?php esc_html_e('List it with us and reach buyers and tenants across Bangladesh.', 'property-directory'); ?>
                    </p>
                </div>
                <a class="cta-button"
                    href="<?php echo esc_url(pd_list_property_url()); ?>"><?php esc_html_e('List a property', 'property-directory'); ?></a>
            </div>
        </div>

        <div class="footer-main">
            <div class="footer-about">
                <?php echo pd_brand_html('brand footer-brand'); // Escaped inside the helper. ?>
                <p><?php esc_html_e('Find a place to call home. Browse homes and commercial properties for sale and rent.', 'property-directory'); ?>
                </p>
                <ul class="footer-contact">
                    <li><b><?php esc_html_e('Office', 'property-directory'); ?></b><span><?php echo esc_html($contact['address']); ?></span>
                    </li>
                    <li><b><?php esc_html_e('Phone', 'property-directory'); ?></b><a
                            href="tel:<?php echo esc_attr(preg_replace('/[^\d+]/', '', $contact['phone'])); ?>"><?php echo esc_html($contact['phone']); ?></a>
                    </li>
                    <li><b><?php esc_html_e('Email', 'property-directory'); ?></b><a
                            href="mailto:<?php echo esc_attr($contact['email']); ?>"><?php echo esc_html($contact['email']); ?></a>
                    </li>
                </ul>
            </div>

            <?php if ($types): ?>
                <nav class="footer-col" aria-label="<?php esc_attr_e('Property types', 'property-directory'); ?>">
                    <h3><?php esc_html_e('Property types', 'property-directory'); ?></h3>
                    <ul>
                        <?php foreach ($types as $term): ?>
                            <li><a
                                    href="<?php echo esc_url(pd_archive_url(array('type' => array($term->slug)))); ?>"><?php echo esc_html($term->name); ?></a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </nav>
            <?php endif; ?>

            <?php if ($locations): ?>
                <nav class="footer-col" aria-label="<?php esc_attr_e('Cities', 'property-directory'); ?>">
                    <h3><?php esc_html_e('Cities', 'property-directory'); ?></h3>
                    <ul>
                        <?php foreach ($locations as $term): ?>
                            <li><a
                                    href="<?php echo esc_url(pd_archive_url(array('location' => $term->slug))); ?>"><?php echo esc_html($term->name); ?></a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </nav>
            <?php endif; ?>

            <nav class="footer-col" aria-label="<?php esc_attr_e('Company', 'property-directory'); ?>">
                <h3><?php esc_html_e('Company', 'property-directory'); ?></h3>
                <?php
                wp_nav_menu(
                    array(
                        'theme_location' => 'footer',
                        'container' => false,
                        'depth' => 1,
                        'items_wrap' => '<ul>%3$s</ul>',
                        'fallback_cb' => 'pd_footer_nav_fallback',
                    )
                );
                ?>
            </nav>
        </div>

        <div class="footer-bottom">
            <div class="footer-bottom-inner">
                <p>© <?php echo esc_html(wp_date('Y')); ?>     <?php echo esc_html(get_bloginfo('name')); ?>.
                    <?php esc_html_e('All rights reserved.', 'property-directory'); ?></p>
                <ul class="footer-legal">
                    <li><a href="#"><?php esc_html_e('Back to top', 'property-directory'); ?> ↑</a></li>
                </ul>
            </div>
        </div>
    </footer>

    <?php wp_footer(); ?>
    </body>

    </html>

    <?php
}

add_action('rp_site_footer', 'rp_site_footer_callback');
?>