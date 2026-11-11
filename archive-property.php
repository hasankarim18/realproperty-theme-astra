<?php
/**
 * Properties archive (also used for property keyword searches).
 */

get_header();

global $wp_query;

$total = (int) $wp_query->found_posts;
$paged = max(1, (int) get_query_var('paged'));
$per_page = (int) $wp_query->get('posts_per_page');
$from = $total ? (($paged - 1) * $per_page) + 1 : 0;
$to = $total ? $from + (int) $wp_query->post_count - 1 : 0;

$args = pd_current_args();
$keyword = isset($args['s']) ? $args['s'] : trim(get_search_query(false));
$chips = pd_active_chips();
$has_filters = pd_has_filters();
$sort = isset($args['sort']) ? $args['sort'] : '';
?>
<main>
    <section class="archive-hero" aria-labelledby="archive-title">
        <div class="hero-inner">
            <p class="eyebrow"><?php esc_html_e('Find your next place', 'property-directory'); ?></p>
            <h1 id="archive-title"><?php esc_html_e('Explore properties', 'property-directory'); ?></h1>
            <p class="hero-copy">
                <?php esc_html_e('Browse homes and commercial properties across Bangladesh.', 'property-directory'); ?>
            </p>

            <form class="search-bar" role="search" method="get"
                action="<?php echo esc_url(get_post_type_archive_link('property')); ?>">
                <label class="search-field search-location">
                    <span><?php esc_html_e('Search', 'property-directory'); ?></span>
                    <input type="search" name="s" value="<?php echo esc_attr($keyword); ?>"
                        placeholder="<?php esc_attr_e('Keyword, e.g. villa, Gulshan, pool', 'property-directory'); ?>">
                </label>
                <input type="hidden" name="post_type" value="property">
                <button class="search-button" type="submit"><span aria-hidden="true">⌕</span>
                    <?php esc_html_e('Search', 'property-directory'); ?></button>
            </form>
        </div>
    </section>

    <div class="page-shell" id="properties">
        <nav class="breadcrumbs" aria-label="<?php esc_attr_e('Breadcrumb', 'property-directory'); ?>">
            <a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Home', 'property-directory'); ?></a>
            <span aria-hidden="true">/</span>
            <?php if ($keyword): ?>
                <a
                    href="<?php echo esc_url(pd_archive_url()); ?>"><?php esc_html_e('Properties', 'property-directory'); ?></a>
                <span aria-hidden="true">/</span>
                <span
                    aria-current="page"><?php echo esc_html(sprintf(__('Search: %s', 'property-directory'), $keyword)); ?></span>
            <?php else: ?>
                <span aria-current="page"><?php esc_html_e('Properties', 'property-directory'); ?></span>
            <?php endif; ?>
        </nav>

        <div class="archive-heading">
            <div>
                <h2><?php esc_html_e('Properties in Bangladesh', 'property-directory'); ?></h2>
                <p id="results-count" aria-live="polite">
                    <?php if ($total): ?>
                        Showing
                        <strong><?php echo esc_html(number_format_i18n($from) . '–' . number_format_i18n($to)); ?></strong>
                        of <strong><?php echo esc_html(number_format_i18n($total)); ?></strong> listings
                    <?php else: ?>
                        No listings found
                    <?php endif; ?>
                </p>
            </div>
            <div class="heading-controls">
                <button class="mobile-filter-button" type="button" aria-controls="filter-sidebar"
                    aria-expanded="false">☷
                    <span><?php esc_html_e('Filters', 'property-directory'); ?></span></button>
                <label class="sort-control">
                    <span><?php esc_html_e('Sort by', 'property-directory'); ?></span>
                    <select name="sort" form="sidebar-form">
                        <?php foreach (pd_sort_options() as $value => $label): ?>
                            <option value="<?php echo esc_attr($value); ?>" <?php selected((string) $value, $sort); ?>>
                                <?php echo esc_html($label); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <noscript><button class="sort-apply" type="submit"
                        form="sidebar-form"><?php esc_html_e('Apply', 'property-directory'); ?></button></noscript>
            </div>
        </div>

        <div class="archive-layout">
            <?php get_template_part('template-parts/filter-sidebar'); ?>

            <section class="results-area" id="results-area"
                aria-label="<?php esc_attr_e('Property search results', 'property-directory'); ?>">

                <?php if ($chips): ?>
                    <div class="active-filters">
                        <?php foreach ($chips as $chip): ?>
                            <span class="filter-chip">
                                <?php echo esc_html($chip['label']); ?>
                                <a href="<?php echo esc_url($chip['url']); ?>"
                                    aria-label="<?php echo esc_attr(sprintf(__('Remove filter: %s', 'property-directory'), $chip['label'])); ?>">×</a>
                            </span>
                        <?php endforeach; ?>
                        <?php if ($has_filters): ?>
                            <a class="remove-all"
                                href="<?php echo esc_url(pd_clear_url()); ?>"><?php esc_html_e('Clear filters', 'property-directory'); ?></a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php if (have_posts()): ?>
                    <div class="property-grid">
                        <?php
                        while (have_posts()):
                            the_post();
                            get_template_part('template-parts/property-card');
                        endwhile;
                        ?>
                    </div>

                    <?php echo pd_pagination(); // Escaped inside the helper. ?>
                <?php else: ?>
                    <div class="no-results">
                        <?php if ($keyword || $has_filters): ?>
                            <h3><?php esc_html_e('No properties match your search', 'property-directory'); ?></h3>
                            <p><a
                                    href="<?php echo esc_url(pd_clear_url()); ?>"><?php esc_html_e('Clear filters', 'property-directory'); ?></a>
                            </p>
                        <?php else: ?>
                            <h3><?php esc_html_e('No properties found', 'property-directory'); ?></h3>
                            <p><?php esc_html_e('There are no listings to show yet.', 'property-directory'); ?></p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </section>
        </div>
    </div>
</main>
<?php
get_footer();