<?php
/**
 * Config-driven archive filters.
 * One array (pd_filters) drives: sidebar markup, URL whitelist, query, chips.
 */

defined('ABSPATH') || exit;

/**
 * Filter definitions. ui: checkbox | select | range | radio | toggle
 */
function pd_filters()
{
    return array(
        'status' => array(
            'label' => __('Listing status', 'property-directory'),
            'ui' => 'checkbox',
            'source' => 'meta',
            'meta_key' => 'property_status',
            'param' => 'status',
        ),
        'type' => array(
            'label' => __('Property type', 'property-directory'),
            'ui' => 'checkbox',
            'source' => 'taxonomy',
            'taxonomy' => 'property_type',
            'param' => 'type',
        ),
        'location' => array(
            'label' => __('Location', 'property-directory'),
            'ui' => 'select',
            'source' => 'taxonomy',
            'taxonomy' => 'property_location',
            'param' => 'location',
            'select_label' => __('City or area', 'property-directory'),
            'all_label' => __('All locations', 'property-directory'),
        ),
        'price' => array(
            'label' => __('Price range', 'property-directory'),
            'ui' => 'range',
            'source' => 'meta',
            'meta_key' => 'property_price',
            'params' => array('min_price', 'max_price'),
        ),
        'beds' => array(
            'label' => __('Bedrooms', 'property-directory'),
            'ui' => 'radio',
            'source' => 'meta',
            'meta_key' => 'property_bedrooms',
            'param' => 'beds',
            'chip' => '%s+ beds',
        ),
        'baths' => array(
            'label' => __('Bathrooms', 'property-directory'),
            'ui' => 'radio',
            'source' => 'meta',
            'meta_key' => 'property_bathrooms',
            'param' => 'baths',
            'chip' => '%s+ baths',
        ),
        'parking' => array(
            'label' => __('Parking', 'property-directory'),
            'ui' => 'toggle',
            'source' => 'meta',
            'meta_key' => 'property_garage',
            'param' => 'parking',
            'compare' => '>',
            'value' => 0,
        ),
        'pool' => array(
            'label' => __('Swimming pool', 'property-directory'),
            'ui' => 'toggle',
            'source' => 'meta',
            'meta_key' => 'property_swimming_pool',
            'param' => 'pool',
            'compare' => '=',
            'value' => 1,
        ),
    );
}

/**
 * Sort options: URL value => label ('' = newest first, the default).
 */
function pd_sort_options()
{
    return array(
        '' => __('Newest first', 'property-directory'),
        'price_asc' => __('Price: low to high', 'property-directory'),
        'price_desc' => __('Price: high to low', 'property-directory'),
    );
}

/**
 * Allowed values for a filter: value => label.
 * Note: PHP turns numeric-string array keys into integers, so always compare with isset() or (string).
 */
function pd_filter_options($key)
{
    static $cache = array();

    if (isset($cache[$key])) {
        return $cache[$key];
    }

    $filters = pd_filters();
    $options = array();

    if (!isset($filters[$key])) {
        return $options;
    }

    $f = $filters[$key];

    if ('taxonomy' === $f['source']) {
        $terms = get_terms(
            array(
                'taxonomy' => $f['taxonomy'],
                'hide_empty' => false,
                'orderby' => 'name',
            )
        );
        if (!is_wp_error($terms)) {
            foreach ($terms as $term) {
                $options[$term->slug] = $term->name;
            }
        }
    } elseif (isset($f['meta_key']) && 'property_status' === $f['meta_key']) {
        foreach (pd_statuses() as $value => $status) {
            $options[$value] = $status['label'];
        }
    } elseif ('radio' === $f['ui']) {
        $options = array(
            '' => __('Any', 'property-directory'),
            '1' => '1+',
            '2' => '2+',
            '3' => '3+',
            '4' => '4+',
        );
    }

    $cache[$key] = $options;

    return $options;
}

/**
 * Read one scalar value from $_GET, sanitized.
 */
function pd_get_scalar($param)
{
    if (!isset($_GET[$param]) || !is_scalar($_GET[$param])) {
        return '';
    }

    return trim(sanitize_text_field(wp_unslash($_GET[$param])));
}

/**
 * Current archive params from the URL: whitelisted and sanitized.
 * Everything not recognised is dropped.
 */
function pd_current_args()
{
    $args = array();

    $s = pd_get_scalar('s');
    if ('' !== $s) {
        $args['s'] = $s;
    }

    foreach (pd_filters() as $key => $f) {
        switch ($f['ui']) {
            case 'checkbox':
                $param = $f['param'];
                $options = pd_filter_options($key);
                $values = array();

                if (isset($_GET[$param])) {
                    foreach ((array) wp_unslash($_GET[$param]) as $v) {
                        if (!is_scalar($v)) {
                            continue;
                        }
                        $v = sanitize_text_field($v);
                        if ('' !== $v && isset($options[$v])) {
                            $values[] = (string) $v;
                        }
                    }
                }

                $values = array_values(array_unique($values));
                if ($values) {
                    $args[$param] = $values;
                }
                break;

            case 'select':
            case 'radio':
                $param = $f['param'];
                $v = pd_get_scalar($param);
                $options = pd_filter_options($key);

                if ('' !== $v && isset($options[$v])) {
                    $args[$param] = (string) $v;
                }
                break;

            case 'toggle':
                if ('1' === pd_get_scalar($f['param'])) {
                    $args[$f['param']] = '1';
                }
                break;

            case 'range':
                $range = array();
                foreach ($f['params'] as $param) {
                    $v = pd_get_scalar($param);
                    if (preg_match('/^\d{1,12}$/', $v) && (int) $v > 0) {
                        $range[$param] = (string) (int) $v;
                    }
                }
                // Swap if the user typed min > max.
                if (2 === count($range) && (int) $range['min_price'] > (int) $range['max_price']) {
                    $tmp = $range['min_price'];
                    $range['min_price'] = $range['max_price'];
                    $range['max_price'] = $tmp;
                }
                $args = array_merge($args, $range);
                break;
        }
    }

    $sort = pd_get_scalar('sort');
    if ('' !== $sort && isset(pd_sort_options()[$sort])) {
        $args['sort'] = $sort;
    }

    return $args;
}

/**
 * True when at least one sidebar filter is active (keyword and sort don't count).
 */
function pd_has_filters()
{
    $args = pd_current_args();
    unset($args['s'], $args['sort']);

    return !empty($args);
}

/**
 * Archive URL for a set of params. post_type is only needed with a keyword.
 */
function pd_archive_url($args = array())
{
    $base = get_post_type_archive_link('property');

    if (!$base) {
        return home_url('/');
    }

    if (isset($args['s'])) {
        $args['post_type'] = 'property';
    }

    return $args ? add_query_arg(urlencode_deep($args), $base) : $base;
}

/**
 * Current URL with param(s) removed. With $value, removes only that value from a list param.
 * $keys can be a string or an array of param names.
 */
function pd_remove_url($keys, $value = null)
{
    $args = pd_current_args();

    foreach ((array) $keys as $key) {
        if (null !== $value && isset($args[$key]) && is_array($args[$key])) {
            $args[$key] = array_values(array_diff($args[$key], array((string) $value)));
            if (!$args[$key]) {
                unset($args[$key]);
            }
        } else {
            unset($args[$key]);
        }
    }

    return pd_archive_url($args);
}

/**
 * "Clear all" URL: archive root, keeping only the hero keyword.
 */
function pd_clear_url()
{
    $args = pd_current_args();

    return pd_archive_url(isset($args['s']) ? array('s' => $args['s']) : array());
}

/**
 * Active chips: array of [ 'label' => ..., 'url' => remove-link ].
 */
function pd_active_chips()
{
    $args = pd_current_args();
    $chips = array();

    if (isset($args['s'])) {
        $chips[] = array(
            'label' => sprintf(__('Keyword: %s', 'property-directory'), $args['s']),
            'url' => pd_remove_url('s'),
        );
    }

    foreach (pd_filters() as $key => $f) {
        switch ($f['ui']) {
            case 'checkbox':
            case 'select':
                if (empty($args[$f['param']])) {
                    break;
                }
                $options = pd_filter_options($key);
                foreach ((array) $args[$f['param']] as $value) {
                    $chips[] = array(
                        'label' => $options[$value],
                        'url' => pd_remove_url($f['param'], $value),
                    );
                }
                break;

            case 'radio':
                if (!empty($args[$f['param']])) {
                    $chips[] = array(
                        'label' => sprintf($f['chip'], $args[$f['param']]),
                        'url' => pd_remove_url($f['param']),
                    );
                }
                break;

            case 'toggle':
                if (!empty($args[$f['param']])) {
                    $chips[] = array(
                        'label' => $f['label'],
                        'url' => pd_remove_url($f['param']),
                    );
                }
                break;

            case 'range':
                $min = isset($args['min_price']) ? '৳ ' . pd_format_number_indian($args['min_price']) : '';
                $max = isset($args['max_price']) ? '৳ ' . pd_format_number_indian($args['max_price']) : '';

                if ($min && $max) {
                    $label = $min . ' – ' . $max;
                } elseif ($min) {
                    $label = sprintf(__('From %s', 'property-directory'), $min);
                } elseif ($max) {
                    $label = sprintf(__('Up to %s', 'property-directory'), $max);
                } else {
                    break;
                }

                $chips[] = array(
                    'label' => $label,
                    'url' => pd_remove_url($f['params']),
                );
                break;
        }
    }

    return $chips;
}

/**
 * Apply the URL filters and sort to the main query.
 */
function pd_apply_filters($query)
{
    $args = pd_current_args();
    $tax_query = array();
    $meta_query = array();

    foreach (pd_filters() as $key => $f) {
        switch ($f['ui']) {
            case 'checkbox':
            case 'select':
                if (empty($args[$f['param']])) {
                    break;
                }
                if ('taxonomy' === $f['source']) {
                    $tax_query[] = array(
                        'taxonomy' => $f['taxonomy'],
                        'field' => 'slug',
                        'terms' => (array) $args[$f['param']],
                        'operator' => 'IN',
                    );
                } else {
                    $meta_query[] = array(
                        'key' => $f['meta_key'],
                        'value' => (array) $args[$f['param']],
                        'compare' => 'IN',
                    );
                }
                break;

            case 'radio':
                if (!empty($args[$f['param']])) {
                    $meta_query[] = array(
                        'key' => $f['meta_key'],
                        'value' => (int) $args[$f['param']],
                        'type' => 'NUMERIC',
                        'compare' => '>=',
                    );
                }
                break;

            case 'toggle':
                if (!empty($args[$f['param']])) {
                    $meta_query[] = array(
                        'key' => $f['meta_key'],
                        'value' => $f['value'],
                        'type' => 'NUMERIC',
                        'compare' => $f['compare'],
                    );
                }
                break;

            case 'range':
                $min = isset($args['min_price']) ? (int) $args['min_price'] : 0;
                $max = isset($args['max_price']) ? (int) $args['max_price'] : 0;

                if ($min && $max) {
                    $meta_query[] = array(
                        'key' => $f['meta_key'],
                        'value' => array($min, $max),
                        'type' => 'NUMERIC',
                        'compare' => 'BETWEEN',
                    );
                } elseif ($min) {
                    $meta_query[] = array(
                        'key' => $f['meta_key'],
                        'value' => $min,
                        'type' => 'NUMERIC',
                        'compare' => '>=',
                    );
                } elseif ($max) {
                    $meta_query[] = array(
                        'key' => $f['meta_key'],
                        'value' => $max,
                        'type' => 'NUMERIC',
                        'compare' => '<=',
                    );
                }
                break;
        }
    }

    if ($tax_query) {
        $tax_query['relation'] = 'AND';
        $query->set('tax_query', $tax_query);
    }

    if ($meta_query) {
        $meta_query['relation'] = 'AND';
        $query->set('meta_query', $meta_query);
    }

    // Sort. Newest first is WordPress's default, so nothing to set.
    if (isset($args['sort'])) {
        $query->set('meta_key', 'property_price');
        $query->set(
            'orderby',
            array(
                'meta_value_num' => 'price_asc' === $args['sort'] ? 'ASC' : 'DESC',
                'date' => 'DESC',
            )
        );
    }
}