<?php
/**
 * Single property card. Used inside the loop.
 */

$post_id = get_the_ID();
$permalink = get_permalink();
$title = get_the_title();

// Status badge.
$statuses = pd_statuses();
$status_val = pd_field('property_status', $post_id);
$status = isset($statuses[$status_val]) ? $statuses[$status_val] : null;

// Location line: term + address, skipping whatever is missing.
$locations = get_the_terms($post_id, 'property_location');
$city = ($locations && !is_wp_error($locations)) ? $locations[0]->name : '';
$address = pd_field('property_address', $post_id);

// Facts and image.
$facts = pd_card_facts($post_id);
$has_image = has_post_thumbnail();
?>
<article class="property-card">
    <a class="card-image<?php echo $has_image ? ' has-image' : ''; ?>" href="<?php echo esc_url($permalink); ?>"
        aria-label="<?php echo esc_attr(sprintf(__('View %s', 'property-directory'), $title)); ?>">
        <?php
        if ($has_image) {
            echo get_the_post_thumbnail($post_id, 'medium_large', array('alt' => '', 'loading' => 'lazy'));
        }
        ?>
        <?php if ($status): ?>
            <span
                class="status-badge <?php echo esc_attr($status['class']); ?>"><?php echo esc_html($status['label']); ?></span>
        <?php endif; ?>
    </a>

    <div class="card-body">
        <?php if ($city || $address): ?>
            <div class="card-location">
                <?php echo esc_html($city); ?>
                <?php if ($city && $address): ?>
                    <span>·</span>
                <?php endif; ?>
                <?php echo esc_html($address); ?>
            </div>
        <?php endif; ?>

        <h3><a href="<?php echo esc_url($permalink); ?>"><?php echo esc_html($title); ?></a></h3>

        <?php if ($facts): ?>
            <div class="card-facts">
                <?php foreach ($facts as $fact): ?>
                    <span><?php echo esc_html($fact); ?></span>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="card-footer">
            <strong><?php echo pd_price_html($post_id); // Escaped inside the helper. ?></strong>
            <a href="<?php echo esc_url($permalink); ?>"
                aria-label="<?php esc_attr_e('View property details', 'property-directory'); ?>">
                <?php esc_html_e('View details', 'property-directory'); ?> <span aria-hidden="true">→</span>
            </a>
        </div>
    </div>
</article>