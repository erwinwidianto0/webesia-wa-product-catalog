<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register Gutenberg Block for Related Products
 */
add_action( 'init', 'wpwa_register_related_blocks' );
function wpwa_register_related_blocks() {
	wp_register_script(
		'wpwa-related-blocks-js',
		WPWA_URL . 'assets/js/related-blocks.js',
		[ 'wp-blocks', 'wp-element', 'wp-components', 'wp-i18n', 'wp-editor', 'wp-server-side-render' ],
		'1.0.0'
	);

	register_block_type( 'webesia/related-products', [
		'editor_script' => 'wpwa-related-blocks-js',
		'render_callback' => 'wpwa_render_block_related_products',
		'attributes' => [
			'limit' => [
				'type' => 'number',
				'default' => 4,
			]
		]
	] );

	register_block_type( 'webesia/product-gallery', [
		'editor_script' => 'wpwa-related-blocks-js',
		'render_callback' => 'wpwa_render_block_product_gallery',
	] );
}

function wpwa_render_block_related_products( $attributes ) {
	if ( function_exists( 'wpwa_related_products_shortcode' ) ) {
		return wpwa_related_products_shortcode( $attributes );
	}
	return '';
}

function wpwa_render_block_product_gallery( $attributes ) {
	$product_id = get_the_ID();

	// If in editor context (which this callback is mainly for via ServerSideRender) or viewing a page
	// and not a product, try to fetch a random product for preview
	if ( ( defined( 'REST_REQUEST' ) && REST_REQUEST ) || get_post_type( $product_id ) !== 'simple_product' ) {
		// Only if we really want a fallback for preview
		// But in frontend, we might want it empty if not relevant.
		// However, for the Block Editor PREVIEW, we definitely want content.
		// ServerSideRender endpoint is a REST request.
		
		$random_product = get_posts([
			'post_type' => 'simple_product',
			'posts_per_page' => 1,
			'orderby' => 'rand'
		]);
		
		if ( ! empty( $random_product ) ) {
			$product_id = $random_product[0]->ID;
		}
	}

	if ( function_exists( 'wpwa_get_product_gallery_html' ) ) {
		return wpwa_get_product_gallery_html( $product_id );
	}
	return '';
}
