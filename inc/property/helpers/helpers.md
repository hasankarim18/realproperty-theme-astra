Step 1 builds the archive page with its cards and pagination. There's no sidebar or hero search yet, so those come in later steps.

I'm assuming `header.php` ends before `<main>` and `footer.php` starts after `</main>`. If your Step 0 `header.php` already opens `<main>`, remove the `<main>` and `</main>` lines from the archive file.

## 1. New file: `inc/template-helpers.php`

```php
<?php
/**
 * Template helpers for the property archive and cards.
 */

defined( 'ABSPATH' ) || exit;

/**
 * Get a custom field. Uses ACF when active, falls back to post meta.
 */
function pd_field( $name, $post_id = null ) {
	$post_id = $post_id ? $post_id : get_the_ID();

	if ( function_exists( 'get_field' ) ) {
		$value = get_field( $name, $post_id );
		// Select fields can return ['value' => '', 'label' => ''].
		if ( is_array( $value ) && isset( $value['value'] ) ) {
			$value = $value['value'];
		}
		return $value;
	}

	return get_post_meta( $post_id, $name, true );
}

/**
 * Known listing statuses: value => label + CSS class.
 */
function pd_statuses() {
	return array(
		'for_sale' => array( 'label' => 'For Sale', 'class' => 'status-for-sale' ),
		'for_rent' => array( 'label' => 'For Rent', 'class' => 'status-for-rent' ),
		'sold'     => array( 'label' => 'Sold',     'class' => 'status-sold' ),
		'rented'   => array( 'label' => 'Rented',   'class' => 'status-rented' ),
	);
}

/**
 * Indian digit grouping: 18500000 => 1,85,00,000
 */
function pd_format_number_indian( $number ) {
	$number = (int) round( (float) $number );
	$digits = (string) abs( $number );

	if ( strlen( $digits ) > 3 ) {
		$last3  = substr( $digits, -3 );
		$rest   = substr( $digits, 0, -3 );
		$rest   = preg_replace( '/\B(?=(\d{2})+(?!\d))/', ',', $rest );
		$digits = $rest . ',' . $last3;
	}

	return ( $number < 0 ? '-' : '' ) . $digits;
}

/**
 * Price HTML for a card. Returns safe HTML.
 */
function pd_price_html( $post_id = null ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	$price   = pd_field( 'property_price', $post_id );
	$status  = pd_field( 'property_status', $post_id );

	if ( '' === $price || null === $price || (float) $price <= 0 ) {
		return esc_html__( 'Price on request', 'property-directory' );
	}

	$html = '৳ ' . esc_html( pd_format_number_indian( $price ) );

	if ( 'for_rent' === $status ) {
		$html .= ' <small>/ month</small>';
	}

	return $html;
}

/**
 * Area with its unit, e.g. "1,850 sq ft". Empty string if no area.
 */
function pd_area_text( $post_id = null ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	$area    = pd_field( 'property_area', $post_id );
	$unit    = pd_field( 'property_area_unit', $post_id );

	if ( '' === $area || null === $area || (float) $area <= 0 ) {
		return '';
	}

	$units = array(
		'sqft'    => 'sq ft',
		'sqm'     => 'sq m',
		'acre'    => 'acre',
		'decimal' => 'decimal',
	);

	$unit_label = isset( $units[ $unit ] ) ? $units[ $unit ] : '';

	return trim( number_format_i18n( (float) $area ) . ' ' . $unit_label );
}

/**
 * Build the list of short facts shown on a card (plain text, escaped later).
 */
function pd_card_facts( $post_id = null ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	$facts   = array();

	$beds  = (int) pd_field( 'property_bedrooms', $post_id );
	$baths = (int) pd_field( 'property_bathrooms', $post_id );

	// Land, offices etc. have no beds/baths: lead with the property type.
	if ( $beds <= 0 && $baths <= 0 ) {
		$types = get_the_terms( $post_id, 'property_type' );
		if ( $types && ! is_wp_error( $types ) ) {
			$facts[] = $types[0]->name;
		}
	}

	if ( $beds > 0 ) {
		$facts[] = '⌂ ' . sprintf( _n( '%d bed', '%d beds', $beds, 'property-directory' ), $beds );
	}

	if ( $baths > 0 ) {
		$facts[] = '◫ ' . sprintf( _n( '%d bath', '%d baths', $baths, 'property-directory' ), $baths );
	}

	$area = pd_area_text( $post_id );
	if ( $area ) {
		$facts[] = $area;
	}

	return $facts;
}

/**
 * Pagination markup built from the main query.
 * Keeps the current query string (get_pagenum_link does that).
 */
function pd_pagination( $query = null ) {
	global $wp_query;
	$query = $query ? $query : $wp_query;

	$total   = (int) $query->max_num_pages;
	$current = max( 1, (int) get_query_var( 'paged' ) );

	if ( $total < 2 ) {
		return '';
	}

	$links = paginate_links(
		array(
			'base'      => str_replace( 999999999, '%#%', esc_url( get_pagenum_link( 999999999 ) ) ),
			'format'    => '',
			'current'   => $current,
			'total'     => $total,
			'prev_next' => false,
			'type'      => 'array',
			'mid_size'  => 1,
			'end_size'  => 1,
		)
	);

	if ( ! $links ) {
		return '';
	}

	$out = '<nav class="pagination" aria-label="' . esc_attr__( 'Property pages', 'property-directory' ) . '">';

	// Previous arrow.
	if ( $current > 1 ) {
		$out .= '<a class="page-arrow" href="' . esc_url( get_pagenum_link( $current - 1 ) ) . '" aria-label="' . esc_attr__( 'Previous page', 'property-directory' ) . '">←</a>';
	} else {
		$out .= '<span class="page-arrow disabled" aria-hidden="true">←</span>';
	}

	// Page numbers: turn the current-page span into a styled link.
	foreach ( $links as $link ) {
		if ( false !== strpos( $link, 'current' ) ) {
			$out .= '<a class="current-page" href="' . esc_url( get_pagenum_link( $current ) ) . '" aria-current="page">' . esc_html( number_format_i18n( $current ) ) . '</a>';
		} else {
			$out .= $link;
		}
	}

	// Next arrow.
	if ( $current < $total ) {
		$out .= '<a class="page-arrow" href="' . esc_url( get_pagenum_link( $current + 1 ) ) . '" aria-label="' . esc_attr__( 'Next page', 'property-directory' ) . '">→</a>';
	} else {
		$out .= '<span class="page-arrow disabled" aria-hidden="true">→</span>';
	}

	$out .= '</nav>';

	return $out;
}
```

## 2. New file: `inc/query.php`

```php
<?php
/**
 * Main query changes for the property archive.
 * All filtering will be added here in Step 3.
 */

defined( 'ABSPATH' ) || exit;

function pd_property_archive_query( $query ) {
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}

	if ( $query->is_post_type_archive( 'property' ) ) {
		$query->set( 'posts_per_page', 6 );
	}
}
add_action( 'pre_get_posts', 'pd_property_archive_query' );
```

## 3. Edit `functions.php`

Add these two lines (anywhere after the opening `<?php`):

```php
require_once get_template_directory() . '/inc/template-helpers.php';
require_once get_template_directory() . '/inc/query.php';
```

## 4. New file: `template-parts/property-card.php`

```php
<?php
/**
 * Single property card. Used inside the loop.
 */

$post_id   = get_the_ID();
$permalink = get_permalink();
$title     = get_the_title();

// Status badge.
$statuses   = pd_statuses();
$status_val = pd_field( 'property_status', $post_id );
$status     = isset( $statuses[ $status_val ] ) ? $statuses[ $status_val ] : null;

// Location line: term + address, skipping whatever is missing.
$locations = get_the_terms( $post_id, 'property_location' );
$city      = ( $locations && ! is_wp_error( $locations ) ) ? $locations[0]->name : '';
$address   = pd_field( 'property_address', $post_id );

// Facts and image.
$facts     = pd_card_facts( $post_id );
$has_image = has_post_thumbnail();
?>
<article class="property-card">
	<a class="card-image<?php echo $has_image ? ' has-image' : ''; ?>" href="<?php echo esc_url( $permalink ); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'View %s', 'property-directory' ), $title ) ); ?>">
		<?php
		if ( $has_image ) {
			echo get_the_post_thumbnail( $post_id, 'medium_large', array( 'alt' => '', 'loading' => 'lazy' ) );
		}
		?>
		<?php if ( $status ) : ?>
			<span class="status-badge <?php echo esc_attr( $status['class'] ); ?>"><?php echo esc_html( $status['label'] ); ?></span>
		<?php endif; ?>
	</a>

	<div class="card-body">
		<?php if ( $city || $address ) : ?>
			<div class="card-location">
				<?php echo esc_html( $city ); ?>
				<?php if ( $city && $address ) : ?>
					<span>·</span>
				<?php endif; ?>
				<?php echo esc_html( $address ); ?>
			</div>
		<?php endif; ?>

		<h3><a href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( $title ); ?></a></h3>

		<?php if ( $facts ) : ?>
			<div class="card-facts">
				<?php foreach ( $facts as $fact ) : ?>
					<span><?php echo esc_html( $fact ); ?></span>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<div class="card-footer">
			<strong><?php echo pd_price_html( $post_id ); // Escaped inside the helper. ?></strong>
			<a href="<?php echo esc_url( $permalink ); ?>" aria-label="<?php esc_attr_e( 'View property details', 'property-directory' ); ?>">
				<?php esc_html_e( 'View details', 'property-directory' ); ?> <span aria-hidden="true">→</span>
			</a>
		</div>
	</div>
</article>
```

The `.featured-badge` and `.image-count` are left out for now: there's no featured field, and the gallery needs ACF PRO.

## 5. New file: `archive-property.php`

```php
<?php
/**
 * Properties archive.
 */

get_header();

global $wp_query;

$total    = (int) $wp_query->found_posts;
$paged    = max( 1, (int) get_query_var( 'paged' ) );
$per_page = (int) $wp_query->get( 'posts_per_page' );
$from     = $total ? ( ( $paged - 1 ) * $per_page ) + 1 : 0;
$to       = $total ? $from + (int) $wp_query->post_count - 1 : 0;
?>
<main>
	<section class="archive-hero" aria-labelledby="archive-title">
		<div class="hero-inner">
			<p class="eyebrow"><?php esc_html_e( 'Find your next place', 'property-directory' ); ?></p>
			<h1 id="archive-title"><?php esc_html_e( 'Explore properties', 'property-directory' ); ?></h1>
			<p class="hero-copy"><?php esc_html_e( 'Browse homes and commercial properties across Bangladesh.', 'property-directory' ); ?></p>
			<?php // Hero search form comes in Step 2. ?>
		</div>
	</section>

	<div class="page-shell" id="properties">
		<nav class="breadcrumbs" aria-label="<?php esc_attr_e( 'Breadcrumb', 'property-directory' ); ?>">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'property-directory' ); ?></a>
			<span aria-hidden="true">/</span>
			<span aria-current="page"><?php esc_html_e( 'Properties', 'property-directory' ); ?></span>
		</nav>

		<div class="archive-heading">
			<div>
				<h2><?php esc_html_e( 'Properties in Bangladesh', 'property-directory' ); ?></h2>
				<p id="results-count" aria-live="polite">
					<?php if ( $total ) : ?>
						Showing <strong><?php echo esc_html( number_format_i18n( $from ) . '–' . number_format_i18n( $to ) ); ?></strong>
						of <strong><?php echo esc_html( number_format_i18n( $total ) ); ?></strong> listings
					<?php else : ?>
						No listings found
					<?php endif; ?>
				</p>
			</div>
			<?php // Sort control comes in Step 3. ?>
		</div>

		<div class="archive-layout no-sidebar">
			<?php // Filter sidebar comes in Step 3. ?>

			<section class="results-area" id="results-area" aria-label="<?php esc_attr_e( 'Property search results', 'property-directory' ); ?>">
				<?php if ( have_posts() ) : ?>
					<div class="property-grid">
						<?php
						while ( have_posts() ) :
							the_post();
							get_template_part( 'template-parts/property-card' );
						endwhile;
						?>
					</div>

					<?php echo pd_pagination(); // Escaped inside the helper. ?>
				<?php else : ?>
					<div class="no-results">
						<h3><?php esc_html_e( 'No properties found', 'property-directory' ); ?></h3>
						<p><?php esc_html_e( 'There are no listings to show yet.', 'property-directory' ); ?></p>
					</div>
				<?php endif; ?>
			</section>
		</div>
	</div>
</main>
<?php
get_footer();
```

## 6. Append to your theme CSS

Add this at the end of the CSS file you enqueued (the design CSS). It supports real featured images, the temporary no-sidebar layout, and the empty state.

```css
/* ---- Step 1 additions ---- */

/* Temporary: no sidebar yet. Remove in Step 3. */
.archive-layout.no-sidebar {
  display: block;
}

/* Real featured image inside a card. */
.card-image.has-image::before {
  display: none;
}
.card-image.has-image::after {
  position: absolute;
  inset: 0;
  width: auto;
  height: auto;
  border: 0;
  background: linear-gradient(180deg, #0e2a3500 50%, #0e2a354a);
}
.card-image.has-image img {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}

/* Empty state. */
.no-results {
  padding: 48px 20px;
  text-align: center;
  border: 1px dashed var(--line);
  border-radius: 9px;
  background: var(--white);
}
.no-results h3 {
  margin: 0 0 6px;
  color: var(--midnight);
  font-size: 16px;
}
.no-results p {
  margin: 0;
  color: var(--slate);
}
```

## What to test

1. `/properties/` shows 6 cards, the count reads "Showing 1–6 of N listings", and the pagination links appear.
2. `/properties/page/2/` shows the next listings, with "Showing 7–12" and a working ← arrow.
3. The last page has a disabled → arrow. For N = 13, page 3 should show "13–13 of 13" with a single card.
4. `/properties/page/99/` returns a 404, not an empty page.
5. A listing with no featured image (PR-0003) shows the placeholder house drawing. One with an image shows the photo with the badge on top.
6. Prices show Indian grouping (PR-0001 as ৳ 1,85,00,000), and rent listings show "/ month".
7. PR-0003 shows "Land · 5 decimal" with no bed or bath items, and nothing breaks for missing fields.
8. Turn on `WP_DEBUG` and confirm there are no PHP notices.

Tell me what you see. If something looks off, send a screenshot or the error text. If everything passes, Step 2 is the hero search.
