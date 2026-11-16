<?php
/**
 * Gallery lightbox for a single property.
 *
 * Passed via get_template_part() $args: gallery_items — array of
 * ['id' => attachment ID, 'alt' => string]. Hidden by default;
 * single-property.js toggles the `hidden` attribute.
 *
 * @package Astra
 */

if (!defined('ABSPATH')) {
    return;
}

$gallery_items = isset($args['gallery_items']) ? (array) $args['gallery_items'] : array();

if (count($gallery_items) < 2) {
    return; // Nothing to slide through.
}
?>
<div class="pd-lightbox" id="property-lightbox" hidden aria-hidden="true">
    <div class="pd-lightbox-backdrop" data-pd-lightbox-close></div>

    <div class="pd-lightbox-dialog" role="dialog" aria-modal="true"
        aria-label="<?php esc_attr_e('Property photos', 'property-directory'); ?>">

        <button class="pd-lightbox-close" type="button" data-pd-lightbox-close
            aria-label="<?php esc_attr_e('Close gallery', 'property-directory'); ?>">×</button>

        <button class="pd-lightbox-nav pd-lightbox-prev" type="button" data-pd-lightbox-prev
            aria-label="<?php esc_attr_e('Previous photo', 'property-directory'); ?>">‹</button>

        <div class="pd-lightbox-viewport">
            <div class="pd-lightbox-track">
                <?php foreach ($gallery_items as $i => $item): ?>
                    <figure class="pd-lightbox-slide" data-index="<?php echo esc_attr($i); ?>"
                        <?php echo 0 === $i ? '' : 'aria-hidden="true"'; ?>>
                        <img src="<?php echo esc_url(wp_get_attachment_image_url($item['id'], 'full')); ?>"
                            alt="<?php echo esc_attr($item['alt'] ? $item['alt'] : get_the_title()); ?>"
                            <?php echo 0 === $i ? '' : 'loading="lazy"'; ?>>
                    </figure>
                <?php endforeach; ?>
            </div>
        </div>

        <button class="pd-lightbox-nav pd-lightbox-next" type="button" data-pd-lightbox-next
            aria-label="<?php esc_attr_e('Next photo', 'property-directory'); ?>">›</button>

        <p class="pd-lightbox-counter" aria-live="polite">
            <span data-pd-lightbox-current>1</span> /
            <?php echo esc_html(count($gallery_items)); ?>
        </p>
    </div>
</div>
