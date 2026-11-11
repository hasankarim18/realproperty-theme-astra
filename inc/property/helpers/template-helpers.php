<?php

/**
 * Template helpers for the property archive and cards
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Get a custom field. Uses ACF when active, falls back to post meta
 * .
 */

error_log('Template helpers');

function pd_field($name, $post_id = null)
{
    $post_id = $post_id ? $post_id : get_the_ID();

    if (function_exists('get_field')) {
        $value = get_field($name, $post_id);
        // Select fields can return ['value'=> '', 'label'=> '']
        if (is_array($value) && isset($value['value'])) {
            $value = $value['value'];
        }
        return $value;
    }

    return get_post_meta($post_id, $name, true);

}

/**
 * Known listing statuses: value => label + css class
 */

function pd_statuses()
{
    return array(
        'for_sale' => array('label' => 'For Sale', 'class' => 'status-for-sale'),
        'for_rent' => array('label' => 'For Rent', 'class' => 'status-for-rent'),
        'sold' => array('label' => 'Sold', 'class' => 'status-sold'),
        'rented' => array('label' => 'Rented', 'class' => 'status-rented'),
    );
}

/**
 * Indian digit grouping: 18500000 => 1,85,00,000
 */

function pd_format_number_indian($number)
{
    $number = (int) round((float) $number);
    $digits = (string) abs($number);

    if (strlen($digits) > 3) {
        $last3 = substr($digits, -3);
        $rest = substr($digits, 0, -3);
        $rest = preg_replace('/\B(?=(\d{2})+(?!\d))/', ',', $rest);
        $digits = $rest . ',' . $last3;
    }

    return ($number < 0 ? '-' : '') . $digits;
}


/**
 *  Price HTML for a card, Returns safe HTML.
 */

function pd_price_html($post_id = null)
{
    $post_id = $post_id ? $post_id : get_the_ID();
    $price = pd_field('property_price', $post_id);
    $status = pd_field('property_status', $post_id);

    if ('' == $price || null == $price || (float) $price <= 0) {
        return esc_html__('Price on request');
    }

    $html = '৳ ' . esc_html(pd_format_number_indian($price));
    if ('for_rent' == $status) {
        return $html .= ' <small>/ month</small>';
    }

    return $html;
}

/**
 *  Area with its unit "1,850 sq ft". Empty string if no area.
 */

/**
 * Area with its unit, e.g. "1,850 sq ft". Empty string if no area.
 */
function pd_area_text($post_id = null)
{
    $post_id = $post_id ? $post_id : get_the_ID();
    $area = pd_field('property_area', $post_id);
    $unit = pd_field('property_area_unit', $post_id);

    if ('' === $area || null === $area || (float) $area <= 0) {
        return '';
    }

    $units = array(
        'sqft' => 'sq ft',
        'sqm' => 'sq m',
        'acre' => 'acre',
        'decimal' => 'decimal',
    );

    $units2 = get_field('unit');

    $unit_label = isset($units[$unit]) ? $units[$unit] : '';

    return trim(number_format_i18n((float) $area) . ' ' . $unit_label);
}


/**
 * Build the list of short facts shown on a card (plain text, escaped later).
 */
function pd_card_facts($post_id = null)
{
    $post_id = $post_id ? $post_id : get_the_ID();
    $facts = array();

    $beds = (int) pd_field('property_bedrooms', $post_id);
    $baths = (int) pd_field('property_bathrooms', $post_id);

    // Land, offices etc. have no beds/baths: lead with the property type.
    if ($beds <= 0 && $baths <= 0) {
        $types = get_the_terms($post_id, 'property_type');
        if ($types && !is_wp_error($types)) {
            $facts[] = $types[0]->name;
        }
    }

    if ($beds > 0) {
        $facts[] = '⌂ ' . sprintf(_n('%d bed', '%d beds', $beds, 'property-directory'), $beds);
    }

    if ($baths > 0) {
        $facts[] = '◫ ' . sprintf(_n('%d bath', '%d baths', $baths, 'property-directory'), $baths);
    }

    $area = pd_area_text($post_id);
    if ($area) {
        $facts[] = $area;
    }

    return $facts;
}

function pd_pagination($query = null)
{
    global $wp_query;
    $query = $query ? $query : $wp_query;

    $total = (int) $query->max_num_pages;
    $current = max(1, (int) get_query_var('paged'));

    if ($total < 2) {
        return '';
    }

    $links = paginate_links(
        array(
            'base' => str_replace(999999999, '%#%', esc_url(get_pagenum_link(999999999))),
            'format' => '',
            'current' => $current,
            'total' => $total,
            'prev_next' => false,
            'type' => 'array',
            'mid_size' => 1,
            'end_size' => 1,
        )
    );

    if (!$links) {
        return '';
    }

    $out = '<nav class="pagination" aria-label="' . esc_attr__('Property pages', 'property-directory') . '">';

    // Previous arrow.
    if ($current > 1) {
        $out .= '<a class="page-arrow" href="' . esc_url(get_pagenum_link($current - 1)) . '" aria-label="' . esc_attr__('Previous page', 'property-directory') . '">←</a>';
    } else {
        $out .= '<span class="page-arrow disabled" aria-hidden="true">←</span>';
    }

    // Page numbers: turn the current-page span into a styled link.
    foreach ($links as $link) {
        if (false !== strpos($link, 'current')) {
            $out .= '<a class="current-page" href="' . esc_url(get_pagenum_link($current)) . '" aria-current="page">' . esc_html(number_format_i18n($current)) . '</a>';
        } else {
            $out .= $link;
        }
    }

    // Next arrow.
    if ($current < $total) {
        $out .= '<a class="page-arrow" href="' . esc_url(get_pagenum_link($current + 1)) . '" aria-label="' . esc_attr__('Next page', 'property-directory') . '">→</a>';
    } else {
        $out .= '<span class="page-arrow disabled" aria-hidden="true">→</span>';
    }

    $out .= '</nav>';

    return $out;
}


/**
 *  Current URL params that belong to the archive (whitelisted and sanitize)
 * Step: 2 only the keywords. step 3 adds the sidebar params here.
 */


/**
 * Archive URL for a set of params. post_type is only needed with keyword
 */



/**
 * Current URL with one param removed (used by the chip "×" links).
 */
