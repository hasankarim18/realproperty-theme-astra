<?php

/**
 * Main query changes for the property archive.
 * All filtering will be added here in Step 3.
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 *  True when the main query is a keyword search inside the property post type
 */
function pd_is_property_search()
{
    if (!is_search()) {
        return false;
    }

    return in_array('property', (array) get_query_var('post_type'), true);
}

/**
 * True for the property archive or a property search 
 * 
 */

function pd_is_property_listing()
{
    return is_post_type_archive('property') || pd_is_property_search();
}

function pd_property_archive_query($query)
{
    if (is_admin() || !$query->is_main_query()) {
        return;
    }

    if ($query->is_post_type_archive('agent')) {
        $query->set('posts_per_page', 6);
        return;
    }

    $is_archive = $query->is_post_type_archive('property');
    $is_search = $query->is_search() && in_array('property', (array) $query->get('post_type'), true);

    if ($is_archive || $is_search) {
        $query->set('posts_per_page', 6);
        pd_apply_filters($query);
    }
}
add_action('pre_get_posts', 'pd_property_archive_query');

/**
 * wordpress loads search.php for ?s=. froce our archive template for property searches.
 */


function pd_property_search_template($template)
{
    if (pd_is_property_search()) {
        $archive_template = locate_template('archive-property.php');
        if ($archive_template) {
            return $archive_template;
        }
    }
    return $template;
}

add_filter('template_include', 'pd_property_search_template', 99);