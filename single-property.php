<?php
/**
 * Template for displaying a single property (post_type = property).
 *
 * Uses the design from html/single-property/ with live ACF data.
 * Header/footer are loaded globally; this file only outputs <main>.
 *
 * @package Astra
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

get_header();

$post_id = get_the_ID();

// ---- ACF field values (pd_field() falls back to post meta if ACF is off). ----
$property_id   = pd_field('property_id', $post_id);
$price         = pd_field('property_price', $post_id);
$price_type    = pd_field('price_type', $post_id); // fixed | negotiable
$status_val    = pd_field('property_status', $post_id);
$address       = pd_field('propety_address', $post_id); // NB: misspelled in ACF
$area          = pd_field('property_area', $post_id);
$area_unit     = pd_field('area_unit', $post_id); // sqft | sqm | acre | decimal
$bedrooms      = pd_field('property_bedrooms', $post_id);
$bathrooms     = pd_field('property_bathrooms', $post_id);
$balconies     = pd_field('balconies', $post_id);
$garage        = pd_field('property_garage', $post_id);
$total_floors  = pd_field('total_floors', $post_id);
$floor_number  = pd_field('floor_number', $post_id);
$year_built    = pd_field('year_built', $post_id);
$furnishing    = pd_field('property_furnishing', $post_id);

// Features (true_false).
$features = array(
    'swimming_pool' => (bool) pd_field('property_swimming_pool', $post_id),
    'garden'        => (bool) pd_field('property_garden', $post_id),
    'gym'           => (bool) pd_field('property_gym', $post_id),
    'security'      => (bool) pd_field('property_security', $post_id),
    'elevator'      => (bool) pd_field('property_elevator', $post_id),
    'ac'            => (bool) pd_field('property_ac', $post_id),
    'internet'      => (bool) pd_field('property_internet', $post_id),
);

// Media.
$image_fields = array(
    'property_image_1',
    'property_image_2',
    'property_image_3',
    'property_image_4',
    'property_image_5',
    'property_image_6',
);
$video_url      = pd_field('property_video_url', $post_id);
$floor_plan     = pd_field('property_floor_plan', $post_id);
$virtual_tour   = pd_field('property_vertual_tour', $post_id); // NB: misspelled in ACF

// Agent relationship.
$agents = pd_field('property_agent', $post_id);
$agents = is_array($agents) ? $agents : array();

// ---- Derived display values. ----
$statuses  = function_exists('pd_statuses') ? pd_statuses() : array();
$status    = isset($statuses[$status_val]) ? $statuses[$status_val] : null;

$price_types = array(
    'fixed'      => __('Fixed price', 'property-directory'),
    'negotiable' => __('Negotiable', 'property-directory'),
);

$furnishing_labels = array(
    'fully'        => __('Fully furnished', 'property-directory'),
    'semi'         => __('Semi furnished', 'property-directory'),
    'unfurnished'  => __('Unfurnished', 'property-directory'),
);

$area_units = array(
    'sqft'    => __('sq ft', 'property-directory'),
    'sqm'     => __('sq m', 'property-directory'),
    'acre'    => __('acre', 'property-directory'),
    'decimal' => __('decimal', 'property-directory'),
);

// Featured image (main gallery image) + ACF images (side gallery).
$main_image_id  = get_post_thumbnail_id($post_id);
$main_image_url = $main_image_id ? wp_get_attachment_image_url($main_image_id, 'large') : '';
$main_image_alt = $main_image_id ? get_post_meta($main_image_id, '_wp_attachment_image_alt', true) : '';

$side_images = array();
foreach ($image_fields as $field) {
    $img = pd_field($field, $post_id);
    // ACF image returns an array; may also be an ID int.
    $img_id = is_array($img) ? (isset($img['ID']) ? (int) $img['ID'] : 0) : (int) $img;
    if (!$img_id) {
        continue;
    }
    $src = wp_get_attachment_image_url($img_id, 'medium_large');
    if (!$src) {
        continue;
    }
    $side_images[] = array(
        'url' => $src,
        'alt' => get_post_meta($img_id, '_wp_attachment_image_alt', true),
    );
}
// If the property has no featured image, promote the first ACF image to main.
if (!$main_image_url && $side_images) {
    $main_image_url = $side_images[0]['url'];
    $main_image_alt = $side_images[0]['alt'];
    array_shift($side_images);
}
$gallery_count = count($side_images) + ($main_image_url ? 1 : 0);

// Location line: taxonomy term + address.
$location_terms = get_the_terms($post_id, 'property_location');
$city = ($location_terms && !is_wp_error($location_terms)) ? $location_terms[0]->name : '';
$location_parts = array_filter(array($city, $address));
$location_text  = implode(', ', $location_parts);

// Details grid rows.
$details = array();

$types = get_the_terms($post_id, 'property_type');
if ($types && !is_wp_error($types)) {
    $details[] = array(__('Property type', 'property-directory'), $types[0]->name);
}
if ($year_built !== null && $year_built !== '') {
    $details[] = array(__('Year built', 'property-directory'), (int) $year_built);
}
if (!empty($furnishing_labels[$furnishing])) {
    $details[] = array(__('Furnishing', 'property-directory'), $furnishing_labels[$furnishing]);
}
if ($floor_number !== null && $floor_number !== '') {
    $floor_text = $total_floors !== null && $total_floors !== ''
        ? (int) $floor_number . ' ' . __('of', 'property-directory') . ' ' . (int) $total_floors
        : (int) $floor_number;
    $details[] = array(__('Floor', 'property-directory'), $floor_text);
}
if ($balconies !== null && $balconies !== '') {
    $details[] = array(__('Balconies', 'property-directory'), (int) $balconies);
}
if ($garage) {
    $details[] = array(__('Parking', 'property-directory'), __('Yes', 'property-directory'));
}
if ($area !== null && $area !== '') {
    $area_text = (float) $area . ' ' . (isset($area_units[$area_unit]) ? $area_units[$area_unit] : '');
    $details[] = array(__('Area', 'property-directory'), trim($area_text));
}
if ($status) {
    $details[] = array(__('Listing status', 'property-directory'), $status['label']);
}

// Media links (only when the value exists).
$media_links = array();
if ($floor_plan) {
    $floor_plan_url = is_array($floor_plan) ? (isset($floor_plan['url']) ? $floor_plan['url'] : '') : (int) $floor_plan;
    if (is_numeric($floor_plan_url)) {
        $floor_plan_url = wp_get_attachment_image_url((int) $floor_plan_url, 'full');
    }
    if ($floor_plan_url) {
        $media_links[] = array(__('View floor plan', 'property-directory'), $floor_plan_url, 'floor-plan');
    }
}
if ($video_url) {
    $media_links[] = array(__('Watch video tour', 'property-directory'), $video_url, 'video-tour');
}
if ($virtual_tour) {
    $media_links[] = array(__('Open virtual tour', 'property-directory'), $virtual_tour, 'virtual-tour');
}

// Feature checklist labels.
$feature_labels = array(
    'swimming_pool' => __('Swimming pool', 'property-directory'),
    'garden'        => __('Garden', 'property-directory'),
    'gym'           => __('Gym', 'property-directory'),
    'security'      => __('24-hour security', 'property-directory'),
    'elevator'      => __('Elevator', 'property-directory'),
    'ac'            => __('Air conditioning', 'property-directory'),
    'internet'      => __('High-speed internet', 'property-directory'),
);
$feature_items = array();
foreach ($feature_labels as $key => $label) {
    if ($features[$key]) {
        $feature_items[] = $label;
    }
}

// Agent display data.
$agent_items = array();
foreach ($agents as $agent) {
    $agent_id = is_object($agent) ? $agent->ID : (is_array($agent) ? (int) $agent['ID'] : (int) $agent);
    if (!$agent_id) {
        continue;
    }
    $name  = get_the_title($agent_id);
    $email = function_exists('get_field') ? get_field('agent_email', $agent_id) : '';
    $phone = function_exists('get_field') ? get_field('agent_phone', $agent_id) : '';
    $role  = function_exists('get_field') ? get_field('agent_role', $agent_id) : '';
    if (!$name) {
        continue;
    }
    $agent_items[] = array(
        'name'  => $name,
        'role'  => $role ? $role : '',
        'phone' => $phone ? $phone : '',
        'email' => $email ? $email : '',
        'initials' => function_exists('pd_initials') ? pd_initials($name) : mb_substr($name, 0, 2),
    );
}

// Request-a-viewing mailto (only when we have an agent email or a site contact).
$inquiry_email = '';
foreach ($agent_items as $agent) {
    if ($agent['email']) {
        $inquiry_email = $agent['email'];
        break;
    }
}
$inquiry_subject = $property_id
    ? sprintf(__('Property inquiry: %s (%s)', 'property-directory'), get_the_title(), $property_id)
    : sprintf(__('Property inquiry: %s', 'property-directory'), get_the_title());
$inquiry_url = $inquiry_email
    ? 'mailto:' . $inquiry_email . '?subject=' . rawurlencode($inquiry_subject)
    : '';
?>
<main class="page-shell" id="main">
    <nav class="breadcrumbs" aria-label="<?php esc_attr_e('Breadcrumb', 'property-directory'); ?>">
        <a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Home', 'property-directory'); ?></a><span
            aria-hidden="true">/</span>
        <a href="<?php echo esc_url(get_post_type_archive_link('property')); ?>"><?php esc_html_e('Properties', 'property-directory'); ?></a><span
            aria-hidden="true">/</span>
        <?php if ($city): ?>
            <a href="<?php echo esc_url(get_term_link($location_terms[0])); ?>"><?php echo esc_html($city); ?></a><span
                aria-hidden="true">/</span>
        <?php endif; ?>
        <span aria-current="page"><?php echo esc_html(get_the_title()); ?></span>
    </nav>

    <section class="listing-heading" aria-labelledby="property-title">
        <div class="listing-heading-copy">
            <?php if ($status): ?>
                <span class="status-badge <?php echo esc_attr($status['class']); ?>"><?php echo esc_html($status['label']); ?></span>
            <?php endif; ?>
            <h1 id="property-title"><?php echo esc_html(get_the_title()); ?></h1>
            <?php if ($location_text): ?>
                <p class="property-location"><span class="location-pin" aria-hidden="true">●</span><?php echo esc_html($location_text); ?></p>
            <?php endif; ?>
        </div>
        <?php if ($inquiry_url): ?>
            <div class="heading-actions">
                <a class="button button-secondary" href="<?php echo esc_url($inquiry_url); ?>">♡ <span><?php esc_html_e('Save', 'property-directory'); ?></span></a>
                <a class="button button-secondary" href="<?php echo esc_url($inquiry_url); ?>">↗ <span><?php esc_html_e('Share', 'property-directory'); ?></span></a>
            </div>
        <?php endif; ?>
    </section>

    <?php if ($main_image_url || $side_images): ?>
        <section class="gallery" aria-label="<?php esc_attr_e('Property photo gallery', 'property-directory'); ?>">
            <?php if ($main_image_url): ?>
                <div class="gallery-photo gallery-photo-main has-image">
                    <img src="<?php echo esc_url($main_image_url); ?>"
                        alt="<?php echo esc_attr($main_image_alt ? $main_image_alt : get_the_title()); ?>"
                        loading="lazy">
                    <span class="photo-tag"><?php esc_html_e('Featured photo', 'property-directory'); ?></span>
                </div>
            <?php endif; ?>

            <?php
            // Gallery classes two..five match the CSS placeholders' names.
            $side_classes = array('gallery-photo-two', 'gallery-photo-three', 'gallery-photo-four', 'gallery-photo-five');
            $shown = 0;
            foreach ($side_images as $img) {
                if ($shown >= 4) {
                    break;
                }
                echo '<div class="gallery-photo ' . esc_attr($side_classes[$shown]) . ' has-image">';
                echo '<img src="' . esc_url($img['url']) . '" alt="' . esc_attr($img['alt'] ? $img['alt'] : get_the_title()) . '" loading="lazy">';
                echo '</div>';
                $shown++;
            }
            ?>

            <?php if ($gallery_count > 1): ?>
                <button class="gallery-count" type="button">▦ <span><?php echo esc_html(sprintf(__('View all %d photos', 'property-directory'), $gallery_count)); ?></span></button>
            <?php endif; ?>
        </section>
    <?php endif; ?>

    <div class="content-layout">
        <div class="listing-content">
            <section class="quick-facts" aria-label="<?php esc_attr_e('Property highlights', 'property-directory'); ?>">
                <?php if ($bedrooms !== null && $bedrooms !== ''): ?>
                    <div class="fact"><span class="fact-icon" aria-hidden="true">⌂</span>
                        <div><strong><?php echo esc_html((int) $bedrooms); ?></strong><span><?php esc_html_e('Bedrooms', 'property-directory'); ?></span></div>
                    </div>
                <?php endif; ?>
                <?php if ($bathrooms !== null && $bathrooms !== ''): ?>
                    <div class="fact"><span class="fact-icon" aria-hidden="true">◫</span>
                        <div><strong><?php echo esc_html((int) $bathrooms); ?></strong><span><?php esc_html_e('Bathrooms', 'property-directory'); ?></span></div>
                    </div>
                <?php endif; ?>
                <?php if ($area !== null && $area !== ''): ?>
                    <div class="fact"><span class="fact-icon" aria-hidden="true">▱</span>
                        <div><strong><?php echo esc_html(number_format_i18n((float) $area)); ?> <small><?php echo esc_html(isset($area_units[$area_unit]) ? $area_units[$area_unit] : ''); ?></small></strong><span><?php esc_html_e('Area', 'property-directory'); ?></span></div>
                    </div>
                <?php endif; ?>
                <?php if ($garage): ?>
                    <div class="fact"><span class="fact-icon" aria-hidden="true">▰</span>
                        <div><strong><?php esc_html_e('Yes', 'property-directory'); ?></strong><span><?php esc_html_e('Parking', 'property-directory'); ?></span></div>
                    </div>
                <?php endif; ?>
            </section>

            <?php if ($price !== null && $price !== '' && (float) $price > 0): ?>
                <section class="price-block" aria-label="<?php esc_attr_e('Property price', 'property-directory'); ?>">
                    <div class="price">৳ <?php echo esc_html(pd_format_number_indian($price)); ?></div>
                    <?php if (isset($price_types[$price_type])): ?>
                        <span class="price-type"><?php echo esc_html($price_types[$price_type]); ?></span>
                    <?php endif; ?>
                    <?php if ($property_id): ?>
                        <span class="property-id"><?php esc_html_e('Property ID:', 'property-directory'); ?> <?php echo esc_html($property_id); ?></span>
                    <?php endif; ?>
                </section>
            <?php endif; ?>

            <?php
            // Overview = the post content. Skip if empty.
            $overview = get_the_content();
            if ('' !== trim((string) $overview)):
                ?>
                <section class="content-section" aria-labelledby="overview-title">
                    <h2 id="overview-title"><?php esc_html_e('Property overview', 'property-directory'); ?></h2>
                    <?php echo apply_filters('the_content', $overview); // WordPress core escaping. ?>
                </section>
            <?php endif; ?>

            <?php if ($feature_items): ?>
                <section class="content-section" aria-labelledby="features-title">
                    <h2 id="features-title"><?php esc_html_e('Features and amenities', 'property-directory'); ?></h2>
                    <ul class="feature-list">
                        <?php foreach ($feature_items as $item): ?>
                            <li><span aria-hidden="true">✓</span><?php echo esc_html($item); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </section>
            <?php endif; ?>

            <?php if ($details): ?>
                <section class="content-section detail-section" aria-labelledby="details-title">
                    <h2 id="details-title"><?php esc_html_e('Property details', 'property-directory'); ?></h2>
                    <dl class="details-grid">
                        <?php foreach ($details as $row): ?>
                            <div>
                                <dt><?php echo esc_html($row[0]); ?></dt>
                                <dd><?php echo esc_html($row[1]); ?></dd>
                            </div>
                        <?php endforeach; ?>
                    </dl>
                </section>
            <?php endif; ?>

            <section class="content-section location-section" aria-labelledby="location-title">
                <h2 id="location-title"><?php esc_html_e('Location', 'property-directory'); ?></h2>
                <?php if ($location_text): ?>
                    <p><?php echo esc_html($location_text); ?></p>
                <?php endif; ?>
                <div class="map-placeholder" role="img" aria-label="<?php esc_attr_e('Map preview placeholder', 'property-directory'); ?>"><span
                        class="map-pin">●</span><span><?php esc_html_e('Map preview', 'property-directory'); ?></span></div>
            </section>

            <?php if ($media_links): ?>
                <section class="content-section media-links" aria-labelledby="media-title">
                    <h2 id="media-title"><?php esc_html_e('More about this property', 'property-directory'); ?></h2>
                    <div class="media-actions">
                        <?php foreach ($media_links as $link): ?>
                            <a href="<?php echo esc_url($link[1]); ?>" <?php echo $link[2] ? 'id="' . esc_attr($link[2]) . '"' : ''; ?>> <?php echo esc_html($link[0]); ?><span aria-hidden="true">↗</span></a>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>
        </div>

        <aside class="contact-column" aria-label="<?php esc_attr_e('Property agents and contact options', 'property-directory'); ?>">
            <?php if ($agent_items): ?>
                <section class="contact-card" id="contact">
                    <p class="eyebrow"><?php esc_html_e('Listing team', 'property-directory'); ?></p>
                    <h2><?php esc_html_e('Interested in this property?', 'property-directory'); ?></h2>
                    <p class="contact-intro"><?php esc_html_e('Contact an agent to ask a question or arrange a viewing.', 'property-directory'); ?></p>
                    <div class="agent-list" id="agents">
                        <?php foreach ($agent_items as $agent): ?>
                            <article class="agent">
                                <div class="agent-avatar" aria-hidden="true"><?php echo esc_html(strtoupper($agent['initials'])); ?></div>
                                <div class="agent-info">
                                    <h3><?php echo esc_html($agent['name']); ?></h3>
                                    <?php if ($agent['role']): ?>
                                        <p><?php echo esc_html($agent['role']); ?></p>
                                    <?php endif; ?>
                                    <?php if ($agent['phone']): ?>
                                        <a href="tel:<?php echo esc_attr(preg_replace('/[^\d+]/', '', $agent['phone'])); ?>"><?php echo esc_html($agent['phone']); ?></a>
                                    <?php endif; ?>
                                    <?php if ($agent['email']): ?>
                                        <a href="mailto:<?php echo esc_attr($agent['email']); ?>"><?php echo esc_html($agent['email']); ?></a>
                                    <?php endif; ?>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                    <?php if ($inquiry_url): ?>
                        <a class="button button-primary button-full" href="<?php echo esc_url($inquiry_url); ?>"><?php esc_html_e('Request a viewing', 'property-directory'); ?></a>
                    <?php endif; ?>
                    <?php
                    $team_phone = $agent_items[0]['phone'];
                    if ($team_phone):
                        ?>
                        <a class="button button-outline button-full" href="tel:<?php echo esc_attr(preg_replace('/[^\d+]/', '', $team_phone)); ?>"><?php esc_html_e('Call the listing team', 'property-directory'); ?></a>
                    <?php endif; ?>
                    <p class="response-note"><?php esc_html_e('The agents will respond to your inquiry directly.', 'property-directory'); ?></p>
                </section>
            <?php endif; ?>

            <section class="help-card">
                <span class="help-mark" aria-hidden="true">?</span>
                <div>
                    <h2><?php esc_html_e('Need help?', 'property-directory'); ?></h2>
                    <p><?php esc_html_e('Our team can help you learn more about this listing.', 'property-directory'); ?></p>
                    <a href="#contact"><?php esc_html_e('Contact our office', 'property-directory'); ?> <span aria-hidden="true">→</span></a>
                </div>
            </section>
        </aside>
    </div>
</main>

<?php
get_footer();
