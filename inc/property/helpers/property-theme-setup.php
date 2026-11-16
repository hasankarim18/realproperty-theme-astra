<?php

/**
 * Theme setup: logo and menus.
 */
function pd_theme_setup()
{
    add_theme_support(
        'custom-logo',
        array(
            'height' => 40,
            'width' => 180,
            'flex-width' => true,
            'flex-height' => true,
        )
    );

    register_nav_menus(
        array(
            'rp_header_menu' => __('Primary menu rp', 'realProperty'),
            'footer' => __('Footer menu rp', 'realProperty'),
        )
    );
}
add_action('after_setup_theme', 'pd_theme_setup');

/** Brand mark + name (used when no custom logo is set, and in the footer). */
function pd_brand_html($class = 'brand')
{
    return sprintf(
        '<a class="%s" href="%s" aria-label="%s"><span class="brand-mark">P</span>Property<span class="brand-light">Directory</span></a>',
        esc_attr($class),
        esc_url(home_url('/')),
        esc_attr(get_bloginfo('name'))
    );
}

/** Where the "List a property" buttons go. Change the page slug to yours. */
function pd_list_property_url()
{
    return apply_filters('pd_list_property_url', home_url('/contact/'));
}

/** Menu shown until you build a menu in Appearance > Menus. */
function pd_primary_nav_fallback()
{
    $items = array(array(__('Properties', 'property-directory'), get_post_type_archive_link('property') ? get_post_type_archive_link('property') : home_url('/properties/')));

    $agents = get_post_type_archive_link('agent');
    if ($agents) {
        $items[] = array(__('Agents', 'property-directory'), $agents);
    }

    $items[] = array(__('About', 'property-directory'), home_url('/about/'));
    $items[] = array(__('Contact', 'property-directory'), home_url('/contact/'));

    echo '<ul class="menu">';
    foreach ($items as $i => $item) {
        $current = (0 === $i && function_exists('pd_is_property_listing') && pd_is_property_listing()) ? ' aria-current="page"' : '';
        printf('<li><a href="%s"%s>%s</a></li>', esc_url($item[1]), $current, esc_html($item[0])); // phpcs:ignore WordPress.Security.EscapeOutput
    }
    echo '</ul>';
}

function pd_footer_nav_fallback()
{
    echo '<ul>';
    printf('<li><a href="%s">%s</a></li>', esc_url(home_url('/about/')), esc_html__('About us', 'property-directory'));
    printf('<li><a href="%s">%s</a></li>', esc_url(home_url('/contact/')), esc_html__('Contact', 'property-directory'));
    printf('<li><a href="%s">%s</a></li>', esc_url(pd_list_property_url()), esc_html__('List a property', 'property-directory'));
    echo '</ul>';
}

/** Header menu script (all pages). */
function pd_enqueue_header_script()
{
    $file = get_template_directory() . '/inc/property/js/header.js';

    wp_enqueue_script(
        'pd-header',
        get_template_directory_uri() . '/inc/property/js/header.js',
        array(),
        file_exists($file) ? filemtime($file) : '1.0',
        true
    );
}
add_action('wp_enqueue_scripts', 'pd_enqueue_header_script');