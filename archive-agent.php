<?php
/**
 * The template for displaying agent archive pages.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Astra
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

get_header();

global $wp_query;

$total = (int) $wp_query->found_posts;
$paged = max(1, (int) get_query_var('paged'));
$per_page = (int) $wp_query->get('posts_per_page');
$from = $total ? (($paged - 1) * $per_page) + 1 : 0;
$to = $total ? $from + (int) $wp_query->post_count - 1 : 0;
?>

<main id="main">
    <div class="page-shell" id="properties">
        <nav class="breadcrumbs" aria-label="<?php esc_attr_e('Breadcrumb', 'property-directory'); ?>">
            <a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Home', 'property-directory'); ?></a>
            <span aria-hidden="true">/</span>
            <span aria-current="page"><?php esc_html_e('Agents', 'property-directory'); ?></span>
        </nav>

        <div class="archive-heading">
            <div>
                <h2><?php esc_html_e('Agents in Bangladesh', 'property-directory'); ?></h2>
                <p id="results-count" aria-live="polite">
                    <?php if ($total): ?>
                        <?php
                        printf(
                            /* translators: 1: first result number, 2: last result number, 3: total results */
                            esc_html__('Showing %1$s–%2$s of %3$s agents', 'property-directory'),
                            '<strong>' . esc_html(number_format_i18n($from)) . '</strong>',
                            '<strong>' . esc_html(number_format_i18n($to)) . '</strong>',
                            '<strong>' . esc_html(number_format_i18n($total)) . '</strong>'
                        );
                        ?>
                    <?php else: ?>
                        <?php esc_html_e('No agents found', 'property-directory'); ?>
                    <?php endif; ?>
                </p>
            </div>
        </div>

        <div class="archive-layout archive-agent-layout">
            <section class="results-area" id="results-area"
                aria-label="<?php esc_attr_e('Agent listings', 'property-directory'); ?>">
                <?php if (have_posts()): ?>
                    <div class="rp-agent-grid">
                        <?php
                        while (have_posts()):
                            the_post();

                            $agent_id = get_the_ID();
                            $job_title = function_exists('get_field') ? get_field('agent_job_title', $agent_id) : get_post_meta($agent_id, 'agent_job_title', true);
                            $phone = function_exists('get_field') ? get_field('agent_phone', $agent_id) : get_post_meta($agent_id, 'agent_phone', true);
                            $email = function_exists('get_field') ? get_field('agent_email', $agent_id) : get_post_meta($agent_id, 'agent_email', true);
                            $bio = function_exists('get_field') ? get_field('agent_bio', $agent_id) : get_post_meta($agent_id, 'agent_bio', true);
                            $license_number = function_exists('get_field') ? get_field('agent_license_number', $agent_id) : get_post_meta($agent_id, 'agent_license_number', true);

                            if (!$bio) {
                                $bio = has_excerpt() ? get_the_excerpt() : wp_strip_all_tags(get_the_content());
                            }

                            $phone_href = $phone ? preg_replace('/[^0-9+]/', '', (string) $phone) : '';
                            $title = get_the_title();
                            $permalink = get_permalink();
                            ?>
                            <article <?php post_class('rp-agent-card'); ?>>
                                <a class="rp-agent-card__image" href="<?php echo esc_url($permalink); ?>">
                                    <?php if (has_post_thumbnail()): ?>
                                        <?php
                                        the_post_thumbnail('large', array(
                                            'loading' => 'lazy',
                                            'decoding' => 'async',
                                            'alt' => esc_attr($title),
                                        ));
                                        ?>
                                    <?php else: ?>
                                        <span class="rp-agent-card__placeholder" aria-hidden="true">
                                            <span>👤</span>
                                        </span>
                                    <?php endif; ?>
                                    <span
                                        class="rp-agent-card__role"><?php esc_html_e('Real Estate Agent', 'property-directory'); ?></span>
                                </a>

                                <div class="rp-agent-card__body">
                                    <?php if (!empty($job_title)): ?>
                                        <div class="rp-agent-card__designation"><?php echo esc_html($job_title); ?></div>
                                    <?php endif; ?>

                                    <h3 class="rp-agent-card__name">
                                        <a href="<?php echo esc_url($permalink); ?>"><?php echo esc_html($title); ?></a>
                                    </h3>

                                    <?php if (!empty($license_number)): ?>
                                        <div class="rp-agent-card__license">
                                            <?php echo esc_html(sprintf(__('License: %s', 'property-directory'), $license_number)); ?>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (!empty($bio)): ?>
                                        <p class="rp-agent-card__description">
                                            <?php echo esc_html(wp_trim_words(wp_strip_all_tags((string) $bio), 28, '…')); ?>
                                        </p>
                                    <?php endif; ?>

                                    <?php if (!empty($phone) || !empty($email)): ?>
                                        <div class="rp-agent-card__contact">
                                            <?php if (!empty($phone)): ?>
                                                <a href="<?php echo esc_url('tel:' . $phone_href); ?>"
                                                    aria-label="<?php echo esc_attr(sprintf(__('Call %s', 'property-directory'), $title)); ?>">
                                                    <span aria-hidden="true">☎</span>
                                                    <?php echo esc_html($phone); ?>
                                                </a>
                                            <?php endif; ?>

                                            <?php if (!empty($email)): ?>
                                                <a href="<?php echo esc_url('mailto:' . antispambot((string) $email)); ?>"
                                                    aria-label="<?php echo esc_attr(sprintf(__('Email %s', 'property-directory'), $title)); ?>">
                                                    <span aria-hidden="true">✉</span>
                                                    <?php echo esc_html(antispambot((string) $email)); ?>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>

                                    <div class="rp-agent-card__footer">
                                        <a href="<?php echo esc_url($permalink); ?>" class="rp-agent-card__link">
                                            <?php esc_html_e('View Profile', 'property-directory'); ?>
                                            <span aria-hidden="true">→</span>
                                        </a>
                                    </div>
                                </div>
                            </article>
                        <?php endwhile; ?>
                    </div>

                    <?php echo pd_pagination(null, __('Agent pages', 'property-directory')); // Escaped inside the helper. ?>
                <?php else: ?>
                    <div class="no-results">
                        <h3><?php esc_html_e('No agents found', 'property-directory'); ?></h3>
                        <p><?php esc_html_e('There are no agent profiles to show yet.', 'property-directory'); ?></p>
                    </div>
                <?php endif; ?>
            </section>
        </div>
    </div>
</main>

<?php get_footer(); ?>