<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register Dashboard Widget
 */
add_action( 'wp_dashboard_setup', 'wpwa_add_dashboard_widgets' );
function wpwa_add_dashboard_widgets() {
	wp_add_dashboard_widget(
		'webesia_product_catalog_dashboard_shortcut_widget',
		esc_html__( 'Ringkasan Toko (WebEsia Product Catalog)', 'webesia-wa-product-catalog' ),
		'wpwa_dashboard_widget_render'
	);
}

/**
 * Render Dashboard Widget HTML
 */
function wpwa_dashboard_widget_render() {
	// Get Stats
	$stats = wpwa_get_order_stats('all');
	
	// Get total products
	$products_count = wp_count_posts( 'simple_product' )->publish;
	
	?>
	<div class="wpwa-dashboard-widget">
		<!-- Stats Row -->
		<div class="wpwa-db-stats-grid">
			<div class="wpwa-db-stat-item">
				<span class="dashicons dashicons-products"></span>
				<div class="db-stat-info">
					<span class="db-stat-value"><?php echo esc_html( number_format_i18n( $products_count ) ); ?></span>
					<span class="db-stat-label"><?php esc_html_e( 'Total Produk', 'webesia-wa-product-catalog' ); ?></span>
				</div>
			</div>
			<div class="wpwa-db-stat-item">
				<span class="dashicons dashicons-cart"></span>
				<div class="db-stat-info">
					<span class="db-stat-value"><?php echo esc_html( number_format_i18n( $stats['completed'] ) ); ?></span>
					<span class="db-stat-label"><?php esc_html_e( 'Pesanan Selesai', 'webesia-wa-product-catalog' ); ?></span>
				</div>
			</div>
			<div class="wpwa-db-stat-item">
				<span class="dashicons dashicons-chart-line"></span>
				<div class="db-stat-info">
					<span class="db-stat-value">Rp<?php echo esc_html( number_format( $stats['revenue'], 0, ',', '.' ) ); ?></span>
					<span class="db-stat-label"><?php esc_html_e( 'Total Omzet', 'webesia-wa-product-catalog' ); ?></span>
				</div>
			</div>
		</div>

		<!-- Shortcuts Grid -->
		<div class="wpwa-db-shortcuts">
			<h4><?php esc_html_e( 'Akses Cepat:', 'webesia-wa-product-catalog' ); ?></h4>
			<div class="wpwa-db-links-grid">
				<a href="<?php echo esc_url( admin_url('post-new.php?post_type=simple_product') ); ?>" class="wpwa-db-link">
					<span class="dashicons dashicons-plus"></span>
					<?php esc_html_e( 'Tambah Produk', 'webesia-wa-product-catalog' ); ?>
				</a>
				<a href="<?php echo esc_url( admin_url('edit.php?post_type=simple_product&page=wpwa-orders') ); ?>" class="wpwa-db-link">
					<span class="dashicons dashicons-list-view"></span>
					<?php esc_html_e( 'Daftar Pesanan', 'webesia-wa-product-catalog' ); ?>
				</a>
				<a href="<?php echo esc_url( admin_url('edit.php?post_type=simple_product&page=wpwa-reports') ); ?>" class="wpwa-db-link">
					<span class="dashicons dashicons-performance"></span>
					<?php esc_html_e( 'Laporan', 'webesia-wa-product-catalog' ); ?>
				</a>
				<a href="<?php echo esc_url( admin_url('edit.php?post_type=simple_product&page=wpwa-settings') ); ?>" class="wpwa-db-link">
					<span class="dashicons dashicons-admin-generic"></span>
					<?php esc_html_e( 'Pengaturan', 'webesia-wa-product-catalog' ); ?>
				</a>
			</div>
		</div>
	</div>

	<style>
		.wpwa-db-stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 20px; }
		.wpwa-db-stat-item { display: flex; align-items: center; gap: 8px; }
		.wpwa-db-stat-item .dashicons { font-size: 24px; width: 24px; height: 24px; color: #2271b1; }
		.db-stat-info { display: flex; flex-direction: column; }
		.db-stat-value { font-size: 16px; font-weight: 700; color: #1d2327; line-height: 1.2; }
		.db-stat-label { font-size: 11px; color: #646970; text-transform: uppercase; letter-spacing: 0.5px; }

		.wpwa-db-shortcuts h4 { margin: 0 0 12px 0; font-size: 13px; color: #1d2327; }
		.wpwa-db-links-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
		.wpwa-db-link { display: flex; align-items: center; gap: 8px; padding: 10px; background: #f6f7f7; border: 1px solid #dcdcde; border-radius: 6px; text-decoration: none; color: #2271b1; font-weight: 600; font-size: 13px; transition: all 0.2s; }
		.wpwa-db-link:hover { background: #f0f0f1; border-color: #2271b1; color: #135e96; transform: translateY(-1px); }
		.wpwa-db-link .dashicons { font-size: 18px; width: 18px; height: 18px; }
	</style>
	<?php
}
