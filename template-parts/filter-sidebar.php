<?php
/**
 * Filter sidebar. Rendered from pd_filters(); checked state comes from the URL.
 * On mobile it becomes an off-canvas drawer (see filter-drawer.js).
 */

$current = pd_current_args();
?>
<aside class="filter-sidebar" id="filter-sidebar"
    aria-label="<?php esc_attr_e('Property filters', 'property-directory'); ?>">
    <div class="filter-title">
        <h2><?php esc_html_e('Filter properties', 'property-directory'); ?></h2>
        <div class="filter-title-actions">
            <a class="clear-filters"
                href="<?php echo esc_url(pd_clear_url()); ?>"><?php esc_html_e('Clear all', 'property-directory'); ?></a>
            <button class="drawer-close" type="button"
                aria-label="<?php esc_attr_e('Close filters', 'property-directory'); ?>">×</button>
        </div>
    </div>

    <form id="sidebar-form" action="<?php echo esc_url(get_post_type_archive_link('property')); ?>" method="get">
        <?php if (isset($current['s'])):  // The sidebar never drops the hero keyword. ?>
            <input type="hidden" name="s" value="<?php echo esc_attr($current['s']); ?>">
            <input type="hidden" name="post_type" value="property">
        <?php endif; ?>

        <?php
        $toggles = array();

        foreach (pd_filters() as $key => $filter) {
            if ('toggle' === $filter['ui']) {
                $toggles[$key] = $filter;
                continue;
            }

            get_template_part(
                'template-parts/filters/' . $filter['ui'],
                null,
                array(
                    'key' => $key,
                    'filter' => $filter,
                    'current' => $current,
                )
            );
        }
        ?>

        <?php if ($toggles): ?>
            <fieldset class="filter-group">
                <legend><?php esc_html_e('Features', 'property-directory'); ?></legend>
                <?php
                foreach ($toggles as $key => $filter) {
                    get_template_part(
                        'template-parts/filters/toggle',
                        null,
                        array(
                            'key' => $key,
                            'filter' => $filter,
                            'current' => $current,
                        )
                    );
                }
                ?>
            </fieldset>
        <?php endif; ?>

        <button class="apply-filters"
            type="submit"><?php esc_html_e('Show properties', 'property-directory'); ?></button>
    </form>

    <div class="drawer-footer">
        <button class="drawer-done" type="button"><?php esc_html_e('Show results', 'property-directory'); ?></button>
    </div>
</aside>
<div class="filter-overlay" aria-hidden="true"></div>