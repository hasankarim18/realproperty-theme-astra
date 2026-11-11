<?php
/**
 * SEO for the property listing: robots, canonical, title, meta description.
 * Skipped when a dedicated SEO plugin is active, so the two never output duplicate tags.
 */

defined('ABSPATH') || exit;

function pd_seo_plugin_active()
{
    return defined('WPSEO_VERSION') || defined('RANK_MATH_VERSION') || defined('SEOPRESS_VERSION') || defined('AIOSEO_VERSION');
}

/**
 * True when the URL carries any filter, sort or keyword param (a "parameter URL").
 */
function pd_is_parameter_url()
{
    return (bool) pd_current_args();
}

/**
 * Canonical URL: filtered, sorted and keyword URLs point to the clean archive.
 * Clean paginated URLs point to themselves.
 */
function pd_canonical_url()
{
    $base = pd_archive_url();

    if (pd_is_parameter_url()) {
        return $base;
    }

    $paged = max(1, (int) get_query_var('paged'));

    return $paged > 1 ? trailingslashit($base) . 'page/' . $paged . '/' : $base;
}

/**
 * noindex, follow for parameter URLs.
 */
function pd_seo_robots($robots)
{
    if (pd_seo_plugin_active() || !pd_is_property_listing()) {
        return $robots;
    }

    if (pd_is_parameter_url()) {
        unset($robots['index']);
        $robots['noindex'] = true;
        $robots['follow'] = true;
    }

    return $robots;
}
add_filter('wp_robots', 'pd_seo_robots');

/**
 * Page title.
 */
function pd_seo_title_parts($parts)
{
    if (pd_seo_plugin_active() || !pd_is_property_listing()) {
        return $parts;
    }

    $args = pd_current_args();

    if (isset($args['s'])) {
        $parts['title'] = sprintf(__('Property search: “%s”', 'property-directory'), $args['s']);
    } elseif (pd_is_parameter_url()) {
        $parts['title'] = __('Filtered properties', 'property-directory');
    } else {
        $parts['title'] = __('Properties for Sale and Rent', 'property-directory');
    }

    return $parts;
}
add_filter('document_title_parts', 'pd_seo_title_parts');

/**
 * Canonical link and meta description.
 */
function pd_seo_head()
{
    if (pd_seo_plugin_active() || !pd_is_property_listing()) {
        return;
    }

    printf("<link rel=\"canonical\" href=\"%s\">\n", esc_url(pd_canonical_url()));

    $description = __('Browse property listings by location, type, price, and status.', 'property-directory');
    printf("<meta name=\"description\" content=\"%s\">\n", esc_attr($description));
}
add_action('wp_head', 'pd_seo_head', 1);