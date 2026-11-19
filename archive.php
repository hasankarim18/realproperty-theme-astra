<?php
/**
 * The template for displaying archive pages.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Astra
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

get_header(); ?>

<main>
	<div class="page-shell" id="properties">
		<nav class="breadcrumbs" aria-label="Breadcrumb"><a href="#home">Home</a><span aria-hidden="true">/</span><span
				aria-current="page">Properties</span></nav>
		<div class="archive-heading">
			<div>
				<h2>
					<?php
					if (is_tax('property_type')) {
						single_term_title('', true);
					} else {
						echo esc_html__('Properties', 'property-directory');
					}
					?>
					in Bangladesh
				</h2>
				<p>Showing <strong>1–6</strong> of <strong>48</strong> listings</p>
			</div>
			<div class="heading-controls">
				<button class="mobile-filter-button" type="button">☷ <span>Filters</span></button>
				<label class="sort-control"><span>Sort by</span><select name="sort">
						<option>Newest first</option>
						<option>Price: low to high</option>
						<option>Price: high to low</option>
					</select></label>
			</div>
		</div>

		<div style="display: flex;justify-content:space-around;flex-wrap:wrap;" class="archive-layout">
			<?php // get_template_part('template-parts/filter-sidebar'); ?>

			<section class="results-area" id="results-area"
				aria-label="<?php esc_attr_e('Property search results', 'property-directory'); ?>">



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
					<?php

				endif; ?>
			</section>
		</div>
	</div>
</main>

<?php get_footer(); ?>