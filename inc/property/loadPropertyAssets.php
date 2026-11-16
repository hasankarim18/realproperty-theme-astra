<?php

function property_enqueue_scripts()
{

    wp_enqueue_style(
        'astra_archive-property',
        ASTRA_THEME_URI . 'inc/property/css/archive-property-new.css',
        array(),
        '1.0.0'
    );
    wp_enqueue_style(
        'astra_archive-property-header-css',
        ASTRA_THEME_URI . 'inc/property/css/header.css',
        array(),
        '1.0.0'
    );
    wp_enqueue_style(
        'astra_single-property-css',
        ASTRA_THEME_URI . 'inc/property/css/single-property.css',
        array(),
        '1.0.2'
    );



}



add_action('wp_enqueue_scripts', 'property_enqueue_scripts');


/**
 * Archive script (only on the property listing) and a "js" class for no-JS fallbacks.
 */
function pd_enqueue_archive_script()
{
    if (!pd_is_property_listing()) {
        return;
    }

    $file = get_template_directory() . '/inc/property/js/archive.js';

    wp_enqueue_script(
        'pd-archive',
        get_template_directory_uri() . '/inc/property/js/archive.js',
        array(),
        file_exists($file) ? filemtime($file) : null,
        true
    );


    $drawer = get_template_directory() . '/inc/property/js/filter-drawer.js';

    wp_enqueue_script(
        'pd-filter-drawer',
        get_template_directory_uri() . '/inc/property/js/filter-drawer.js',
        array(),
        file_exists($drawer) ? filemtime($drawer) : '1.0',
        true
    );
    // $header = get_template_directory() . '/inc/property/js/header.js';
    // wp_enqueue_script(
    //     'property_header_custom',
    //     get_template_directory_uri() . '/inc/property/js/header.js',
    //     array(),
    //     file_exists($header) ? filemtime($header) : null,
    //     true
    // );

    // wp_enqueue_script( $handle:string, $src:string, $deps:array, $ver:string|boolean|null, $in_footer:boolean )
}
add_action('wp_enqueue_scripts', 'pd_enqueue_archive_script');

/**
 * Gallery lightbox script (single property only).
 */
function pd_enqueue_single_property_script()
{
    if (!is_singular('property')) {
        return;
    }

    $file = get_template_directory() . '/inc/property/js/single-property.js';

    wp_enqueue_script(
        'pd-single-property',
        get_template_directory_uri() . '/inc/property/js/single-property.js',
        array(),
        file_exists($file) ? filemtime($file) : '1.0',
        true
    );
}
add_action('wp_enqueue_scripts', 'pd_enqueue_single_property_script');

function pd_js_class()
{
    echo "<script>document.documentElement.className += ' js';</script>\n";
}
add_action('wp_head', 'pd_js_class', 1);

