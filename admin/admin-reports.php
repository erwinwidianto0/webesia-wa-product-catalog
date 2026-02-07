<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'admin_menu', 'wpwa_add_reports_menu' );
function wpwa_add_reports_menu() {
	add_submenu_page(
		'edit.php?post_type=simple_product',
		esc_html__( 'Reports', 'webesia-wa-product-catalog' ),
		esc_html__( 'Reports', 'webesia-wa-product-catalog' ),
		'manage_options',
		'wpwa-reports',
		'wpwa_reports_page_html'
	);
}

function wpwa_reports_page_html() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$timeframe  = isset($_GET['timeframe']) ? sanitize_text_field(wp_unslash($_GET['timeframe'])) : 'all';
	$start_date = isset($_GET['start_date']) ? sanitize_text_field(wp_unslash($_GET['start_date'])) : '';
	$end_date   = isset($_GET['end_date']) ? sanitize_text_field(wp_unslash($_GET['end_date'])) : '';
	
	// Nonce verification for filters
	if ( isset( $_GET['timeframe'] ) ) {
		// We use wpwa_reports_nonce for the filter form
		if ( ! isset( $_GET['wpwa_reports_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['wpwa_reports_nonce'] ) ), 'wpwa_filter_reports' ) ) {
			// If nonce fails, we revert to default view instead of die() for better UX
			$timeframe = 'all';
		}
	}

	$stats = wpwa_get_order_stats($timeframe, $start_date, $end_date);
	if ( ! $stats ) {
		echo '<div class="wrap"><h1>' . esc_html__( 'Reports', 'webesia-wa-product-catalog' ) . '</h1><p>' . esc_html__( 'No data available yet.', 'webesia-wa-product-catalog' ) . '</p></div>';
		return;
	}
	?>
	<div class="wrap wpwa-reports-wrap">
		<div class="wpwa-header-flex">
			<h1 class="wp-heading-inline"><?php esc_html_e( 'Laporan Penjualan', 'webesia-wa-product-catalog' ); ?></h1>
			<form method="get" class="wpwa-filter-form">
				<input type="hidden" name="post_type" value="simple_product">
				<input type="hidden" name="page" value="wpwa-reports">
				<?php wp_nonce_field( 'wpwa_filter_reports', 'wpwa_reports_nonce' ); ?>
				<div class="wpwa-filter-inputs">
					<select name="timeframe" id="wpwa-timeframe-select" onchange="this.form.submit()">
						<option value="all" <?php selected($timeframe, 'all'); ?>><?php esc_html_e( 'Semua', 'webesia-wa-product-catalog' ); ?></option>
						<option value="daily" <?php selected($timeframe, 'daily'); ?>><?php esc_html_e( 'Harian', 'webesia-wa-product-catalog' ); ?></option>
						<option value="weekly" <?php selected($timeframe, 'weekly'); ?>><?php esc_html_e( 'Mingguan', 'webesia-wa-product-catalog' ); ?></option>
						<option value="monthly" <?php selected($timeframe, 'monthly'); ?>><?php esc_html_e( 'Bulanan', 'webesia-wa-product-catalog' ); ?></option>
						<option value="yearly" <?php selected($timeframe, 'yearly'); ?>><?php esc_html_e( 'Tahunan', 'webesia-wa-product-catalog' ); ?></option>
						<option value="custom" <?php selected($timeframe, 'custom'); ?>><?php esc_html_e( 'Custom', 'webesia-wa-product-catalog' ); ?></option>
					</select>
					<div id="wpwa-custom-dates" style="display: <?php echo ($timeframe == 'custom') ? 'flex' : 'none'; ?>;">
						<input type="date" name="start_date" value="<?php echo esc_attr($start_date); ?>" placeholder="Dari">
						<input type="date" name="end_date" value="<?php echo esc_attr($end_date); ?>" placeholder="Sampai">
						<button type="submit" class="button button-primary"><?php esc_html_e( 'Filter', 'webesia-wa-product-catalog' ); ?></button>
					</div>
					<?php 
					$export_nonce = wp_create_nonce( 'wpwa_export_reports' );
					$query_args = $_GET;
					unset($query_args['wpwa_reports_nonce']);
					unset($query_args['_wp_http_referer']);
					$query_args['_wpnonce'] = $export_nonce;
					$export_url = add_query_arg( array_merge( ['action' => 'wpwa_export_reports'], $query_args ), admin_url('admin-ajax.php') );
					?>
					<button type="button" class="button wpwa-export-btn" onclick="window.location.href='<?php echo esc_url($export_url); ?>'">
						<span class="dashicons dashicons-download"></span> <?php esc_html_e( 'Export Excel', 'webesia-wa-product-catalog' ); ?>
					</button>
				</div>
			</form>
		</div>

		<script>
		document.getElementById('wpwa-timeframe-select').addEventListener('change', function() {
			var customDiv = document.getElementById('wpwa-custom-dates');
			if (this.value === 'custom') {
				customDiv.style.display = 'flex';
			} else {
				customDiv.style.display = 'none';
			}
		});
		</script>
		
		<div class="wpwa-dashboard-container">
			<!-- Primary Stats Row -->
			<div class="wpwa-hero-grid">
				<div class="wpwa-hero-main">
					<div class="wpwa-hero-bg-accent"></div>
					<div class="wpwa-hero-inner">
						<div class="wpwa-hero-text">
							<span class="wpwa-badge"><?php esc_html_e( 'Total Revenue', 'webesia-wa-product-catalog' ); ?></span>
							<h2 class="wpwa-main-revenue">Rp<?php echo esc_html( number_format( $stats['revenue'], 0, ',', '.' ) ); ?></h2>
						</div>
						<div class="wpwa-hero-mini">
							<div class="mini-item">
								<span class="m-label"><?php esc_html_e( 'Bulan Ini', 'webesia-wa-product-catalog' ); ?></span>
								<span class="m-value">Rp<?php echo esc_html( number_format( $stats['rev_this_month'], 0, ',', '.' ) ); ?></span>
							</div>
							<div class="mini-item">
								<span class="m-label"><?php esc_html_e( 'Bulan Lalu', 'webesia-wa-product-catalog' ); ?></span>
								<span class="m-value">Rp<?php echo esc_html( number_format( $stats['rev_last_month'], 0, ',', '.' ) ); ?></span>
							</div>
						</div>
					</div>
				</div>

				<div class="wpwa-hero-side">
					<div class="wpwa-status-card">
						<h3><?php esc_html_e( 'Status Pesanan', 'webesia-wa-product-catalog' ); ?></h3>
						<div class="status-summary">
							<div class="s-row completed">
								<span class="s-label"><?php esc_html_e( 'Selesai', 'webesia-wa-product-catalog' ); ?></span>
								<span class="s-count"><?php echo esc_html( number_format_i18n( $stats['completed'] ) ); ?></span>
							</div>
							<div class="s-row pending">
								<span class="s-label"><?php esc_html_e( 'Menunggu', 'webesia-wa-product-catalog' ); ?></span>
								<span class="s-count"><?php echo esc_html( number_format_i18n( $stats['pending'] ) ); ?></span>
							</div>
							<div class="s-row failed">
								<span class="s-label"><?php esc_html_e( 'Gagal', 'webesia-wa-product-catalog' ); ?></span>
								<span class="s-count"><?php echo esc_html( number_format_i18n( $stats['failed'] ) ); ?></span>
							</div>
						</div>
					</div>
				</div>
			</div>

			<!-- Visual Data Row -->
			<div class="wpwa-content-grid">
				<!-- Weekly Trend (Visual CSS Bar) -->
				<div class="wpwa-card chart-card">
						<div class="card-title">
							<span class="dashicons dashicons-chart-bar"></span>
							<h3><?php 
								if ($timeframe == 'all') esc_html_e( 'Tren Penjualan (6 Bulan Terakhir)', 'webesia-wa-product-catalog' );
								elseif ($timeframe == 'daily') esc_html_e( 'Tren Penjualan (Hari Ini)', 'webesia-wa-product-catalog' );
								elseif ($timeframe == 'weekly') esc_html_e( 'Tren Penjualan (Harian)', 'webesia-wa-product-catalog' );
								elseif ($timeframe == 'yearly') esc_html_e( 'Tren Penjualan (Bulanan)', 'webesia-wa-product-catalog' );
								else esc_html_e( 'Tren Penjualan (Mingguan)', 'webesia-wa-product-catalog' );
							?></h3>
						</div>
						<div class="wpwa-css-chart">
						<?php 
						$max_val = 0;
						foreach ($stats['daily_data'] as $d) {
							if ($d['value'] > $max_val) $max_val = $d['value'];
						}
						
						foreach ($stats['daily_data'] as $data) : 
							$percent = ($max_val > 0) ? ($data['value'] / $max_val) * 100 : 0;
						?>
						<div class="chart-bar-wrap">
							<div class="chart-bar" style="height: <?php echo esc_attr( max(5, $percent) ); ?>%;">
								<span class="bar-tooltip">Rp<?php echo esc_html( number_format($data['value'], 0, ',', '.') ); ?></span>
							</div>
							<span class="chart-day"><?php echo esc_html( $data['label'] ); ?></span>
						</div>
						<?php endforeach; ?>
					</div>
				</div>

				<!-- Top Products Leaderboard -->
				<div class="wpwa-card top-products-card">
					<div class="card-title">
						<span class="dashicons dashicons-star-filled"></span>
						<h3><?php esc_html_e( 'Produk Unggulan', 'webesia-wa-product-catalog' ); ?></h3>
					</div>
					<div class="top-products-list">
						<?php if ( ! empty( $stats['top_products'] ) ) : ?>
							<?php foreach ( $stats['top_products'] as $index => $product ) : ?>
							<div class="top-product-item">
								<div class="prod-rank"><?php echo esc_html( $index + 1 ); ?></div>
								<div class="prod-info">
									<a href="<?php echo esc_url( get_permalink( $product->product_id ) ); ?>" target="_blank" class="prod-name">
										<?php echo esc_html( wp_trim_words( $product->product_name, 4 ) ); ?>
									</a>
									<span class="prod-sales"><?php echo esc_html( number_format_i18n( $product->total_qty ) ); ?> unit terjual</span>
								</div>
								<div class="prod-amount">Rp<?php echo esc_html( number_format( $product->total_sales, 0, ',', '.' ) ); ?></div>
							</div>
							<?php endforeach; ?>
						<?php else : ?>
							<p class="empty-state"><?php esc_html_e( 'Belum ada transaksi produk.', 'webesia-wa-product-catalog' ); ?></p>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php
}

function wpwa_get_order_stats($timeframe = 'monthly', $start_custom = '', $end_custom = '') {
	global $wpdb;
	$table_orders = $wpdb->prefix . 'wa_orders';
	$table_items  = $wpdb->prefix . 'wa_order_items';
	
	// Securely check table exists without interpolation in the query string
	$check = $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", "{$wpdb->prefix}wa_orders" ) );
	if ( $check != "{$wpdb->prefix}wa_orders" ) {
		return false;
	}

	$start_date = '';
	$end_date = current_time('Y-m-d 23:59:59');
	
	$use_filter = true;
	if ($timeframe == 'all') {
		$use_filter = false;
	} elseif ($timeframe == 'custom' && !empty($start_custom) && !empty($end_custom)) {
		$start_date = $start_custom . ' 00:00:00';
		$end_date = $end_custom . ' 23:59:59';
	} else {
		switch ($timeframe) {
			case 'daily':
				$start_date = current_time('Y-m-d 00:00:00');
				$end_date = current_time('Y-m-d 23:59:59');
				break;
			case 'weekly':
				$start_date = gmdate('Y-m-d 00:00:00', strtotime('-7 days', current_time('timestamp')));
				$end_date = current_time('Y-m-d 23:59:59');
				break;
			case 'yearly':
				$start_date = gmdate('Y-01-01 00:00:00', current_time('timestamp'));
				$end_date = gmdate('Y-12-31 23:59:59', current_time('timestamp'));
				break;
			case 'monthly':
			default:
				$start_date = gmdate('Y-m-01 00:00:00', current_time('timestamp'));
				$end_date = gmdate('Y-m-t 23:59:59', current_time('timestamp'));
				break;
		}
	}

	$stats = [];
	
	if ($use_filter) {
		$stats['total']     = (int) $wpdb->get_var( $wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->prefix}wa_orders WHERE created_at >= %s AND created_at <= %s", $start_date, $end_date) );
		$stats['pending']   = (int) $wpdb->get_var( $wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->prefix}wa_orders WHERE status = 'pending' AND created_at >= %s AND created_at <= %s", $start_date, $end_date) );
		$stats['completed'] = (int) $wpdb->get_var( $wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->prefix}wa_orders WHERE status = 'completed' AND created_at >= %s AND created_at <= %s", $start_date, $end_date) );
		$stats['failed']    = (int) $wpdb->get_var( $wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->prefix}wa_orders WHERE status = 'failed' AND created_at >= %s AND created_at <= %s", $start_date, $end_date) );
		$stats['revenue']   = (float) $wpdb->get_var( $wpdb->prepare("SELECT SUM(total_amount) FROM {$wpdb->prefix}wa_orders WHERE status = 'completed' AND created_at >= %s AND created_at <= %s", $start_date, $end_date) );
		
		$stats['top_products'] = $wpdb->get_results( $wpdb->prepare("
			SELECT i.product_id, i.product_name, SUM(i.quantity) as total_qty, SUM(i.subtotal) as total_sales
			FROM {$wpdb->prefix}wa_order_items i
			JOIN {$wpdb->prefix}wa_orders o ON i.order_id = o.id
			WHERE o.status = 'completed' AND o.created_at >= %s AND o.created_at <= %s
			GROUP BY i.product_id
			ORDER BY total_qty DESC
			LIMIT 5
		", $start_date, $end_date) );
	} else {
		$stats['total']     = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}wa_orders" );
		$stats['pending']   = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->prefix}wa_orders WHERE status = %s", 'pending' ) );
		$stats['completed'] = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->prefix}wa_orders WHERE status = %s", 'completed' ) );
		$stats['failed']    = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->prefix}wa_orders WHERE status = %s", 'failed' ) );
		$stats['revenue']   = (float) $wpdb->get_var( $wpdb->prepare( "SELECT SUM(total_amount) FROM {$wpdb->prefix}wa_orders WHERE status = %s", 'completed' ) );
		
		$stats['top_products'] = $wpdb->get_results( $wpdb->prepare( "
			SELECT i.product_id, i.product_name, SUM(i.quantity) as total_qty, SUM(i.subtotal) as total_sales
			FROM {$wpdb->prefix}wa_order_items i
			JOIN {$wpdb->prefix}wa_orders o ON i.order_id = o.id
			WHERE o.status = %s
			GROUP BY i.product_id
			ORDER BY total_qty DESC
			LIMIT 5
		", 'completed' ) );
	}

	// Growth logic (Monthly context)
	// We keep these simple as they work in the screenshot
	$this_month_start = gmdate('Y-m-01 00:00:00', current_time('timestamp'));
	$last_month_start = gmdate('Y-m-01 00:00:00', strtotime('-1 month', current_time('timestamp')));
	$last_month_end = gmdate('Y-m-t 23:59:59', strtotime('-1 month', current_time('timestamp')));
	
	$stats['rev_this_month'] = (float) $wpdb->get_var( $wpdb->prepare(
		"SELECT SUM(total_amount) FROM {$wpdb->prefix}wa_orders WHERE status = 'completed' AND created_at >= %s", 
		$this_month_start
	) );
	
	$stats['rev_last_month'] = (float) $wpdb->get_var( $wpdb->prepare(
		"SELECT SUM(total_amount) FROM {$wpdb->prefix}wa_orders WHERE status = 'completed' AND created_at >= %s AND created_at <= %s", 
		$last_month_start, $last_month_end
	) );

	// Dynamic Chart Data
	$stats['daily_data'] = [];
	
	if ($timeframe == 'all') {
		$months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
		for ($i = 5; $i >= 0; $i--) {
			$m_offset = "-$i month";
			$date_start = gmdate('Y-m-01 00:00:00', strtotime($m_offset, current_time('timestamp')));
			$date_end = gmdate('Y-m-t 23:59:59', strtotime($m_offset, current_time('timestamp')));
			$total = (float) $wpdb->get_var( $wpdb->prepare("SELECT SUM(total_amount) FROM {$wpdb->prefix}wa_orders WHERE status = 'completed' AND created_at >= %s AND created_at <= %s", $date_start, $date_end) );
			$m_label = $months[(int)gmdate('m', strtotime($date_start)) - 1];
			$stats['daily_data'][] = ['label' => $m_label, 'value' => $total];
		}
	} elseif ($timeframe == 'daily') {
		$base_time = current_time('Y-m-d');
		for ($h = 0; $h < 24; $h += 3) {
			$h_str = sprintf("%02d", $h);
			$h_end = sprintf("%02d", $h + 2);
			$date_start = "$base_time $h_str:00:00";
			$date_end = "$base_time $h_end:59:59";
			$total = (float) $wpdb->get_var( $wpdb->prepare("SELECT SUM(total_amount) FROM {$wpdb->prefix}wa_orders WHERE status = 'completed' AND created_at >= %s AND created_at <= %s", $date_start, $date_end) );
			$stats['daily_data'][] = ['label' => "$h_str:00", 'value' => $total];
		}
	} elseif ($timeframe == 'weekly' || $timeframe == 'custom') {
		$days_of_week = ['Mon' => 'Sen', 'Tue' => 'Sel', 'Wed' => 'Rab', 'Thu' => 'Kam', 'Fri' => 'Jum', 'Sat' => 'Sab', 'Sun' => 'Min'];
		
		if ($timeframe == 'custom' && !empty($start_custom) && !empty($end_custom)) {
			$begin = new DateTime($start_custom);
			$end = new DateTime($end_custom);
			$end->modify('+1 day'); // Include end day
			$interval = new DateInterval('P1D');
			$daterange = new DatePeriod($begin, $interval ,$end);
			
			// Limit to 31 days for chart sanity
			$count = 0;
			foreach($daterange as $date){
				if ($count >= 31) break;
				$d_str = $date->format("Y-m-d");
				$total = (float) $wpdb->get_var( $wpdb->prepare("SELECT SUM(total_amount) FROM {$wpdb->prefix}wa_orders WHERE status = 'completed' AND DATE(created_at) = %s", $d_str) );
				$stats['daily_data'][] = ['label' => $date->format('d/m'), 'value' => $total];
				$count++;
			}
		} else {
			$current_day = gmdate('N', current_time('timestamp'));
			for ($i = 1; $i <= 7; $i++) {
				$diff = $i - $current_day;
				$date = gmdate('Y-m-d', strtotime(($diff > 0 ? $diff-7 : $diff) . ' days', current_time('timestamp')));
				$total = (float) $wpdb->get_var( $wpdb->prepare("SELECT SUM(total_amount) FROM {$wpdb->prefix}wa_orders WHERE status = 'completed' AND DATE(created_at) = %s", $date) );
				$stats['daily_data'][] = ['label' => $days_of_week[gmdate('D', strtotime($date))], 'value' => $total];
			}
		}
	} elseif ($timeframe == 'yearly') {
		$months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
		$curr_year = gmdate('Y', current_time('timestamp'));
		for ($m = 1; $m <= 12; $m++) {
			$date_start = "$curr_year-" . sprintf("%02d", $m) . "-01 00:00:00";
			$date_end = gmdate("Y-m-t 23:59:59", strtotime($date_start));
			$total = (float) $wpdb->get_var( $wpdb->prepare("SELECT SUM(total_amount) FROM {$wpdb->prefix}wa_orders WHERE status = 'completed' AND created_at >= %s AND created_at <= %s", $date_start, $date_end) );
			$stats['daily_data'][] = ['label' => $months[$m-1], 'value' => $total];
		}
	} else { // Monthly
		for ($w = 0; $w < 4; $w++) {
			$date_start = gmdate('Y-m-d 00:00:00', strtotime('monday this week - ' . (3-$w) . ' weeks', current_time('timestamp')));
			$date_end = gmdate('Y-m-d 23:59:59', strtotime('sunday this week - ' . (3-$w) . ' weeks', current_time('timestamp')));
			$total = (float) $wpdb->get_var( $wpdb->prepare("SELECT SUM(total_amount) FROM {$wpdb->prefix}wa_orders WHERE status = 'completed' AND created_at >= %s AND created_at <= %s", $date_start, $date_end) );
			$stats['daily_data'][] = ['label' => 'M' . ($w+1), 'value' => $total];
		}
	}

	return $stats;
}

/**
 * Handle CSV Export
 */
add_action( 'wp_ajax_wpwa_export_reports', 'wpwa_handle_reports_export' );
function wpwa_handle_reports_export() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( 'Forbidden' );
	}

	check_admin_referer( 'wpwa_export_reports' );

	$timeframe  = isset($_GET['timeframe']) ? sanitize_text_field(wp_unslash($_GET['timeframe'])) : 'all';
	$start_custom = isset($_GET['start_date']) ? sanitize_text_field(wp_unslash($_GET['start_date'])) : '';
	$end_custom   = isset($_GET['end_date']) ? sanitize_text_field(wp_unslash($_GET['end_date'])) : '';

	global $wpdb;
	$table_orders = $wpdb->prefix . 'wa_orders';

	$start_date = '';
	$end_date = current_time('Y-m-d 23:59:59');
	$use_filter = ($timeframe !== 'all');

	if ($timeframe == 'custom' && !empty($start_custom) && !empty($end_custom)) {
		$start_date = $start_custom . ' 00:00:00';
		$end_date = $end_custom . ' 23:59:59';
	} else {
		switch ($timeframe) {
			case 'daily':
				$start_date = current_time('Y-m-d 00:00:00');
				$end_date = current_time('Y-m-d 23:59:59');
				break;
			case 'weekly':
				$start_date = gmdate('Y-m-d 00:00:00', strtotime('-7 days', current_time('timestamp')));
				$end_date = current_time('Y-m-d 23:59:59');
				break;
			case 'yearly':
				$start_date = gmdate('Y-01-01 00:00:00', current_time('timestamp'));
				$end_date = gmdate('Y-12-31 23:59:59', current_time('timestamp'));
				break;
			case 'monthly':
			default:
				$start_date = gmdate('Y-m-01 00:00:00', current_time('timestamp'));
				$end_date = gmdate('Y-m-t 23:59:59', current_time('timestamp'));
				break;
		}
	}

	if ($use_filter) {
		$query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}wa_orders WHERE created_at >= %s AND created_at <= %s", $start_date, $end_date);
	} else {
		$query = "SELECT * FROM {$wpdb->prefix}wa_orders";
	}
	$query .= " ORDER BY id DESC";

	$orders = $wpdb->get_results( $query );

	$filename = 'Laporan_Penjualan_' . gmdate('Y-m-d') . '.csv';

	header('Content-Type: text/csv; charset=utf-8');
	header('Content-Disposition: attachment; filename=' . $filename);

	$output = fopen('php://output', 'w');
	
	// CSV Header
	fputcsv($output, [
		'Order ID', 
		'No. Pesanan', 
		'Nama Pelanggan', 
		'No. WhatsApp', 
		'Total Amount', 
		'Status', 
		'Tanggal'
	]);

	foreach ($orders as $order) {
		fputcsv($output, [
			$order->id,
			$order->order_number,
			$order->customer_name,
			$order->customer_phone,
			$order->total_amount,
			ucfirst($order->status),
			$order->created_at
		]);
	}

	fclose($output);
	exit;
}
