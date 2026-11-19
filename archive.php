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
				<h2>Properties in Bangladesh</h2>
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

		<div style="display: flex; justify-content: space-around;flex-wrap:wrap;" class="archive-layout">


			<section style="width: 100%;" class="results-area" aria-label="Property search results">

				<div class="property-grid">
					<article class="property-card">
						<a class="card-image image-one" href="#property-pr-0001"
							aria-label="View Luxury 3-Bedroom Apartment in Gulshan">
							<span class="status-badge status-for-sale">For Sale</span><span class="featured-badge">★
								Featured</span><span class="image-count">▧ 12</span>
						</a>
						<div class="card-body">
							<div class="card-location">Dhaka <span>·</span> Gulshan 2</div>
							<h3><a href="#property-pr-0001">Luxury 3-Bedroom Apartment in Gulshan</a></h3>
							<div class="card-facts"><span>⌂ 3 beds</span><span>◫ 3 baths</span><span>1,850 sq ft</span>
							</div>
							<div class="card-footer"><strong>৳ 1,85,00,000</strong><a href="#property-pr-0001"
									aria-label="View property details">View details <span
										aria-hidden="true">→</span></a></div>
						</div>
					</article>
					<article class="property-card">
						<a class="card-image image-two" href="#property-pr-0002"
							aria-label="View Spacious Villa with Private Pool in Sylhet">
							<span class="status-badge status-for-rent">For Rent</span><span class="image-count">▧
								8</span>
						</a>
						<div class="card-body">
							<div class="card-location">Sylhet <span>·</span> Shahjalal Upashahar</div>
							<h3><a href="#property-pr-0002">Spacious Villa with Private Pool in Sylhet</a></h3>
							<div class="card-facts"><span>⌂ 5 beds</span><span>◫ 4 baths</span><span>4,200 sq ft</span>
							</div>
							<div class="card-footer"><strong>৳ 1,20,000 <small>/ month</small></strong><a
									href="#property-pr-0002" aria-label="View property details">View details <span
										aria-hidden="true">→</span></a></div>
						</div>
					</article>
					<article class="property-card">
						<a class="card-image image-three" href="#property-pr-0003"
							aria-label="View Residential Plot Near Khulna City Bypass">
							<span class="status-badge status-for-sale">For Sale</span><span class="image-count">▧
								4</span>
						</a>
						<div class="card-body">
							<div class="card-location">Khulna <span>·</span> City Bypass</div>
							<h3><a href="#property-pr-0003">Residential Plot Near Khulna City Bypass</a></h3>
							<div class="card-facts"><span>Land</span><span>5 decimal</span><span>Road access</span>
							</div>
							<div class="card-footer"><strong>৳ 35,00,000</strong><a href="#property-pr-0003"
									aria-label="View property details">View details <span
										aria-hidden="true">→</span></a></div>
						</div>
					</article>
					<article class="property-card">
						<a class="card-image image-four" href="#property-pr-0004"
							aria-label="View family home in Dhanmondi">
							<span class="status-badge status-for-sale">For Sale</span><span class="image-count">▧
								10</span>
						</a>
						<div class="card-body">
							<div class="card-location">Dhaka <span>·</span> Dhanmondi</div>
							<h3><a href="#property-pr-0004">Bright Family Home near Dhanmondi Lake</a></h3>
							<div class="card-facts"><span>⌂ 4 beds</span><span>◫ 3 baths</span><span>2,400 sq ft</span>
							</div>
							<div class="card-footer"><strong>৳ 2,40,00,000</strong><a href="#property-pr-0004"
									aria-label="View property details">View details <span
										aria-hidden="true">→</span></a></div>
						</div>
					</article>
					<article class="property-card">
						<a class="card-image image-five" href="#property-pr-0005"
							aria-label="View modern office suite in Chittagong">
							<span class="status-badge status-for-rent">For Rent</span><span class="image-count">▧
								6</span>
						</a>
						<div class="card-body">
							<div class="card-location">Chittagong <span>·</span> Agrabad</div>
							<h3><a href="#property-pr-0005">Modern Office Suite in Agrabad Commercial Area</a></h3>
							<div class="card-facts"><span>Office</span><span>1,600 sq ft</span><span>Parking</span>
							</div>
							<div class="card-footer"><strong>৳ 85,000 <small>/ month</small></strong><a
									href="#property-pr-0005" aria-label="View property details">View details <span
										aria-hidden="true">→</span></a></div>
						</div>
					</article>
					<article class="property-card">
						<a class="card-image image-six" href="#property-pr-0006"
							aria-label="View family villa in Rajshahi">
							<span class="status-badge status-sold">Sold</span><span class="image-count">▧ 9</span>
						</a>
						<div class="card-body">
							<div class="card-location">Rajshahi <span>·</span> Padma Residential Area</div>
							<h3><a href="#property-pr-0006">Garden Villa in a Quiet Residential Area</a></h3>
							<div class="card-facts"><span>⌂ 4 beds</span><span>◫ 3 baths</span><span>3,100 sq ft</span>
							</div>
							<div class="card-footer"><strong>৳ 1,45,00,000</strong><a href="#property-pr-0006"
									aria-label="View property details">View details <span
										aria-hidden="true">→</span></a></div>
						</div>
					</article>
				</div>
				<nav class="pagination" aria-label="Property pages"><a class="page-arrow disabled" href="#previous"
						aria-label="Previous page">←</a><a class="current-page" href="#page-1"
						aria-current="page">1</a><a href="#page-2">2</a><a href="#page-3">3</a><span>…</span><a
						href="#page-8">8</a><a class="page-arrow" href="#next" aria-label="Next page">→</a></nav>
			</section>
		</div>
	</div>
</main>

<?php get_footer(); ?>