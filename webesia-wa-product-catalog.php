<?php
/**
 * Plugin Name: WebEsia Catalog
 * Project-Id-Version: WebEsia Catalog 1.0.0
 * Description: Catalog products with a seamless chat order system. Developed by Erwin Widianto (WebEsia).
 * Version: 1.0.0
 * Author: WebEsia
 * Author URI: https://webesia.com
 * Text Domain: webesia-wa-product-catalog
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define Constants
define( 'WPWA_PATH', plugin_dir_path( __FILE__ ) );
define( 'WPWA_URL', plugin_dir_url( __FILE__ ) );

// Include Files
require_once WPWA_PATH . 'includes/database.php';
require_once WPWA_PATH . 'includes/post-type.php';
require_once WPWA_PATH . 'includes/review-functions.php';
require_once WPWA_PATH . 'includes/i18n.php';
require_once WPWA_PATH . 'includes/ajax-handler.php';
require_once WPWA_PATH . 'includes/shortcode.php';
require_once WPWA_PATH . 'includes/template-tags.php';
require_once WPWA_PATH . 'admin/admin-settings.php';
require_once WPWA_PATH . 'admin/admin-orders.php';
require_once WPWA_PATH . 'admin/admin-reports.php';
require_once WPWA_PATH . 'admin/admin-dashboard.php';
require_once WPWA_PATH . 'includes/class-wpwa-widget.php';
require_once WPWA_PATH . 'includes/class-wpwa-related-widget.php';
require_once WPWA_PATH . 'includes/blocks.php';
require_once WPWA_PATH . 'includes/related-blocks.php';

// Register Widget
add_action( 'widgets_init', function() {
	register_widget( 'WPWA_Product_Catalog_Widget' );
	register_widget( 'WPWA_Related_Products_Widget' );
} );

// Register Elementor Widget Category
add_action( 'elementor/elements/categories_registered', function( $elements_manager ) {
	$elements_manager->add_category(
		'webesia-wa-catalog',
		[
			'title' => esc_html__( 'WA WebEsia Catalog', 'webesia-wa-product-catalog' ),
			'icon'  => 'fa fa-shopping-cart',
		]
	);
} );

// Register Elementor Widgets
add_action( 'elementor/widgets/register', function( $widgets_manager ) {
	require_once WPWA_PATH . 'includes/class-wpwa-elementor-widget.php';
	require_once WPWA_PATH . 'includes/class-wpwa-elementor-related-widget.php';
	require_once WPWA_PATH . 'includes/class-wpwa-elementor-gallery-widget.php';
	require_once WPWA_PATH . 'includes/class-wpwa-elementor-title-widget.php';
	require_once WPWA_PATH . 'includes/class-wpwa-elementor-breadcrumb-widget.php';
	require_once WPWA_PATH . 'includes/class-wpwa-elementor-meta-widget.php';
	require_once WPWA_PATH . 'includes/class-wpwa-elementor-pricing-widget.php';
	require_once WPWA_PATH . 'includes/class-wpwa-elementor-excerpt-widget.php';
	require_once WPWA_PATH . 'includes/class-wpwa-elementor-cta-widget.php';
	require_once WPWA_PATH . 'includes/class-wpwa-elementor-tabs-widget.php';
	$widgets_manager->register( new \WPWA_Elementor_Product_Catalog() );
	$widgets_manager->register( new \WPWA_Elementor_Related_Products() );
	$widgets_manager->register( new \WPWA_Elementor_Product_Gallery() );
	$widgets_manager->register( new \WPWA_Elementor_Product_Title() );
	$widgets_manager->register( new \WPWA_Elementor_Breadcrumb() );
	$widgets_manager->register( new \WPWA_Elementor_Product_Meta() );
	$widgets_manager->register( new \WPWA_Elementor_Product_Pricing() );
	$widgets_manager->register( new \WPWA_Elementor_Product_Excerpt() );
	$widgets_manager->register( new \WPWA_Elementor_Product_CTA() );
	$widgets_manager->register( new \WPWA_Elementor_Product_Tabs() );
} );

// Activation Hook
register_activation_hook( __FILE__, 'wpwa_plugin_activation' );

function wpwa_plugin_activation() {
	wpwa_create_database_tables();
	wpwa_register_post_type();
	wpwa_auto_generate_pages();
	flush_rewrite_rules();
}

// Allow manual setup via admin
add_action( 'admin_init', function() {
	if ( isset( $_GET['wpwa_run_setup'] ) && current_user_can( 'manage_options' ) ) {
		// Verify nonce
		if ( isset( $_GET['_wpnonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'wpwa_run_setup' ) ) {
			wpwa_auto_generate_pages();
			flush_rewrite_rules();
			wp_safe_redirect( admin_url( 'edit.php?post_type=simple_product&page=wpwa-settings&setup=done' ) );
			exit;
		}
	}
} );

function wpwa_auto_generate_pages() {
	$shop_page_id = get_option( 'wpwa_shop_page_id' );

	// Check if already set and page exists and is NOT in trash
	if ( $shop_page_id ) {
		$post = get_post( $shop_page_id );
		if ( $post && $post->post_status !== 'trash' ) {
			return;
		}
	}

	// Double check by path to avoid duplicates (prioritize 'toko' then 'product-catalog')
	// We check for published pages specifically
	$page_exists = get_pages( [
		'post_type'   => 'page',
		'post_status' => 'publish',
		'meta_key'    => '_wp_page_template',
		'hierarchical' => 0,
		'number'      => 1,
		'post_name__in' => [ 'shop', 'product-catalog' ]
	] );

	if ( ! empty( $page_exists ) ) {
		$found_page = $page_exists[0];
		update_option( 'wpwa_shop_page_id', $found_page->ID );
		update_option( 'wpwa_catalog_slug', $found_page->post_name );
		if ( ! get_option( 'wpwa_product_slug' ) ) {
			update_option( 'wpwa_product_slug', 'product' );
		}
		return;
	}

	// Create the "Shop" page with [toko] shortcode
	$page_id = wp_insert_post( [
		'post_title'   => 'Shop',
		'post_name'    => 'shop',
		'post_status'  => 'publish',
		'post_type'    => 'page',
		'post_content' => '[toko]',
	] );

	if ( ! is_wp_error( $page_id ) ) {
		update_option( 'wpwa_shop_page_id', $page_id );
		update_option( 'wpwa_catalog_slug', 'shop' );
		update_option( 'wpwa_product_slug', 'product' );
	}
}

// Enqueue Scripts & Styles
add_action( 'wp_enqueue_scripts', 'wpwa_enqueue_frontend_assets' );
function wpwa_enqueue_frontend_assets() {
	wp_enqueue_style( 'wpwa-frontend-css', WPWA_URL . 'assets/css/frontend.css', [], '1.1.9' );
	wp_enqueue_script( 'wpwa-frontend-js', WPWA_URL . 'assets/js/frontend.js', [ 'jquery' ], '1.0.0', true );
	
	wp_localize_script( 'wpwa-frontend-js', 'wpwa_ajax', [
		'ajax_url' => admin_url( 'admin-ajax.php' ),
		'nonce'    => wp_create_nonce( 'wpwa_order_nonce' ),
		'currency_symbol' => wpwa_get_currency(),
		'msg_processing'  => esc_html__( 'Processing...', 'webesia-wa-product-catalog' ),
		'msg_error'       => esc_html__( 'An error occurred. Please try again.', 'webesia-wa-product-catalog' ),
		'msg_send_btn'    => esc_html__( 'Send Order via WhatsApp', 'webesia-wa-product-catalog' ),
	] );

	// Remove <p> from excerpt inside our card
	remove_filter( 'the_excerpt', 'wpautop' );
}

/**
 * Render the mobile filter in the footer to escape theme containers
 */
add_action( 'wp_footer', function() {
	if ( function_exists( 'wpwa_render_product_filter' ) ) {
		echo '<aside class="wpwa-sidebar wpwa-mobile-only">';
		wpwa_render_product_filter();
		echo '</aside>';
	}
}, 100 ); // High priority to be at the very bottom

add_action( 'admin_enqueue_scripts', 'wpwa_enqueue_admin_assets', 999 );
function wpwa_enqueue_admin_assets( $hook ) {
	wp_enqueue_style( 'wpwa-admin-css', WPWA_URL . 'assets/css/admin.css', [], '4.0.1' );
	
	// Enqueue gallery script and editor on product edit pages
	global $post_type;
	if ( ( 'post.php' === $hook || 'post-new.php' === $hook ) && 'simple_product' === $post_type ) {
		wp_enqueue_media();
		wp_enqueue_editor();
		wp_enqueue_script( 'wpwa-gallery-js', WPWA_URL . 'admin/js/gallery.js', [ 'jquery' ], '1.0.2', true );
	}
}

