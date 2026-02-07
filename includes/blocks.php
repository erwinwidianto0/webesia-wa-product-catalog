<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register Gutenberg Block
 */
add_action( 'init', 'wpwa_register_blocks' );
function wpwa_register_blocks() {
	// Register the block script
	wp_register_script(
		'wpwa-blocks-js',
		WPWA_URL . 'assets/js/blocks.js',
		[ 'wp-blocks', 'wp-element', 'wp-components', 'wp-i18n', 'wp-editor' ],
		'1.0.1'
	);

	// Register the block
	register_block_type( 'webesia/product-catalog', [
		'editor_script' => 'wpwa-blocks-js',
		'render_callback' => 'wpwa_render_block_product_catalog',
		'attributes' => [
			'posts_per_page' => [
				'type' => 'number',
				'default' => 9,
			],
			'posts_per_page_tablet' => [
				'type' => 'number',
				'default' => 9,
			],
			'posts_per_page_mobile' => [
				'type' => 'number',
				'default' => 9,
			],
			'category' => [
				'type' => 'string',
				'default' => '',
			],
			'filter' => [
				'type' => 'boolean',
				'default' => false,
			],
		]
	] );
}

/**
 * Render Callback for the Block
 */
function wpwa_render_block_product_catalog( $attributes ) {
	// Re-use the shortcode logic we already have
	// The shortcode function expects an array of attributes
	
	// Handle Responsive Limits
	if ( wp_is_mobile() ) {
		if ( ! empty( $attributes['posts_per_page_mobile'] ) ) {
			$attributes['posts_per_page'] = $attributes['posts_per_page_mobile'];
		} elseif ( ! empty( $attributes['posts_per_page_tablet'] ) ) {
			$attributes['posts_per_page'] = $attributes['posts_per_page_tablet'];
		}
	}

	// Convert boolean filter to string 'yes'/'no' for shortcode
	if ( isset( $attributes['filter'] ) ) {
		$attributes['filter'] = $attributes['filter'] ? 'yes' : 'no';
	}

	if ( function_exists( 'wpwa_products_shortcode' ) ) {
		return wpwa_products_shortcode( $attributes );
	}
	return '';
}
