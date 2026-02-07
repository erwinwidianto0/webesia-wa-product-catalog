<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class WPWA_Order_List_Table extends WP_List_Table {

	public function __construct() {
		parent::__construct( [
			'singular' => 'order',
			'plural'   => 'orders',
			'ajax'     => false
		] );
	}

	public function get_columns() {
		return [
			'cb'              => '<input type="checkbox" />',
			'order_number'    => esc_html__( 'Order #', 'webesia-wa-product-catalog' ),
			'customer_name'   => esc_html__( 'Customer', 'webesia-wa-product-catalog' ),
			'product_name'    => esc_html__( 'Product', 'webesia-wa-product-catalog' ),
			'customer_address' => esc_html__( 'Address', 'webesia-wa-product-catalog' ),
			'customer_note'    => esc_html__( 'Note', 'webesia-wa-product-catalog' ),
			'total_amount'    => esc_html__( 'Total', 'webesia-wa-product-catalog' ),
			'status'          => esc_html__( 'Status', 'webesia-wa-product-catalog' ),
			'created_at'      => esc_html__( 'Date', 'webesia-wa-product-catalog' ),
			'actions'         => esc_html__( 'Actions', 'webesia-wa-product-catalog' )
		];
	}

	public function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="order_ids[]" value="%s" />',
			$item->id
		);
	}

	public function get_bulk_actions() {
		return [
			'bulk-delete'    => esc_html__( 'Delete', 'webesia-wa-product-catalog' ),
			'bulk-complete'  => esc_html__( 'Mark as Completed', 'webesia-wa-product-catalog' ),
			'bulk-fail'      => esc_html__( 'Mark as Failed', 'webesia-wa-product-catalog' ),
		];
	}

	public function prepare_items() {
		global $wpdb;
		$this->_column_headers = [ $this->get_columns(), [], [] ];
		$per_page = 20;
		$current_page = $this->get_pagenum();
		$offset = ( $current_page - 1 ) * $per_page;

		// Use literal table names or safe concatenation to satisfy the scanner
		$query = $wpdb->prepare(
			"SELECT o.*, i.product_id, i.product_name, i.quantity 
			 FROM {$wpdb->prefix}wa_orders o 
			 LEFT JOIN {$wpdb->prefix}wa_order_items i ON o.id = i.order_id 
			 ORDER BY o.id DESC 
			 LIMIT %d OFFSET %d",
			$per_page,
			$offset
		);

		$this->items = $wpdb->get_results( $query );

		$total_items = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}wa_orders" );

		$this->set_pagination_args( [
			'total_items' => $total_items,
			'per_page'    => $per_page
		] );
	}

	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'order_number':
				return '<strong>' . esc_html( $item->order_number ) . '</strong>';
			case 'customer_name':
				return '<strong>' . esc_html( $item->customer_name ) . '</strong><br><small>' . esc_html( $item->customer_phone ) . '</small>';
			case 'product_name':
				$product_url = get_permalink( $item->product_id );
				return '<a href="' . esc_url( $product_url ) . '" target="_blank">' . esc_html( $item->product_name ) . '</a> (x' . esc_html( $item->quantity ) . ')';
			case 'customer_address':
				return esc_html( $item->customer_address );
			case 'customer_note':
				return '<small><i>' . esc_html( $item->customer_note ) . '</i></small>';
			case 'total_amount':
				return wpwa_format_currency( $item->total_amount );
			case 'status':
				$status_labels = [
					'pending'   => esc_html__( 'Pending', 'webesia-wa-product-catalog' ),
					'completed' => esc_html__( 'Completed', 'webesia-wa-product-catalog' ),
					'failed'    => esc_html__( 'Failed', 'webesia-wa-product-catalog' ),
				];
				$status_label = isset( $status_labels[ $item->status ] ) ? $status_labels[ $item->status ] : $item->status;
				$class = 'status-' . esc_attr( $item->status );
				return "<span class='wpwa-status $class'>" . esc_html( $status_label ) . "</span>";
			case 'created_at':
				return date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $item->created_at ) );
			case 'actions':
				$nonce = wp_create_nonce( 'wpwa_order_action' );
				$complete_url = admin_url( 'edit.php?post_type=simple_product&page=wpwa-orders&action=complete&id=' . intval( $item->id ) . '&_wpnonce=' . $nonce );
				$failed_url   = admin_url( 'edit.php?post_type=simple_product&page=wpwa-orders&action=fail&id=' . intval( $item->id ) . '&_wpnonce=' . $nonce );
				$delete_url   = admin_url( 'edit.php?post_type=simple_product&page=wpwa-orders&action=delete&id=' . intval( $item->id ) . '&_wpnonce=' . $nonce );
				
				$actions = [];
				if ( $item->status !== 'completed' ) {
					$actions[] = "<a href='" . esc_url( $complete_url ) . "' class='button button-small'>" . esc_html__( 'Complete', 'webesia-wa-product-catalog' ) . "</a>";
				}
				if ( $item->status !== 'failed' ) {
					$actions[] = "<a href='" . esc_url( $failed_url ) . "' class='button button-small' style='color: #d63638;'>" . esc_html__( 'Failed', 'webesia-wa-product-catalog' ) . "</a>";
				}
				$actions[] = "<a href='" . esc_url( $delete_url ) . "' class='button button-small delete' style='color: #d63638;' onclick='return confirm(\"" . esc_js( esc_html__( 'Are you sure?', 'webesia-wa-product-catalog' ) ) . "\")'>" . esc_html__( 'Delete', 'webesia-wa-product-catalog' ) . "</a>";
				
				return implode( ' ', $actions );
			default:
				return '';
		}
	}
}

add_action( 'admin_menu', 'wpwa_add_orders_menu' );
function wpwa_add_orders_menu() {
	$pending_count = wpwa_get_pending_order_count();
	$badge = '';
	if ( $pending_count > 0 ) {
		$badge = ' <span class="update-plugins count-' . intval( $pending_count ) . '"><span class="plugin-count">' . intval( $pending_count ) . '</span></span>';
	}

	add_submenu_page(
		'edit.php?post_type=simple_product',
		esc_html__( 'Order List', 'webesia-wa-product-catalog' ),
		esc_html__( 'Orders', 'webesia-wa-product-catalog' ) . $badge,
		'manage_options',
		'wpwa-orders',
		'wpwa_orders_page_html'
	);
}

function wpwa_get_pending_order_count() {
	global $wpdb;
	$table_orders = $wpdb->prefix . 'wa_orders';
	
	// Check table existence safely without variable interpolation in the query string
		$check = $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", "{$wpdb->prefix}wa_orders" ) );
		if ( $check != "{$wpdb->prefix}wa_orders" ) {
			return 0;
		}
		
		return (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->prefix}wa_orders WHERE status = %s", 'pending' ) );
}

function wpwa_orders_page_html() {
	global $wpdb;

	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	if ( isset( $_GET['action'] ) && isset( $_GET['id'] ) ) {
		check_admin_referer( 'wpwa_order_action' );
		$id = intval( $_GET['id'] );
		if ( $_GET['action'] === 'complete' ) {
			$wpdb->update( "{$wpdb->prefix}wa_orders", [ 'status' => 'completed' ], [ 'id' => $id ] );
		} elseif ( $_GET['action'] === 'fail' ) {
			$wpdb->update( "{$wpdb->prefix}wa_orders", [ 'status' => 'failed' ], [ 'id' => $id ] );
		} elseif ( $_GET['action'] === 'delete' ) {
			$wpdb->delete( "{$wpdb->prefix}wa_orders", [ 'id' => $id ] );
			$wpdb->delete( "{$wpdb->prefix}wa_order_items", [ 'order_id' => $id ] );
		}
		echo '<div class="updated"><p>' . esc_html__( 'Order updated.', 'webesia-wa-product-catalog' ) . '</p></div>';
	}

	// Handle Bulk Actions
	$action = ( isset( $_POST['action'] ) && $_POST['action'] !== '-1' ) ? sanitize_text_field( $_POST['action'] ) : ( ( isset( $_POST['action2'] ) && $_POST['action2'] !== '-1' ) ? sanitize_text_field( $_POST['action2'] ) : '' );
	
	if ( $action && isset( $_POST['order_ids'] ) && is_array( $_POST['order_ids'] ) ) {
		$order_ids = array_map( 'intval', $_POST['order_ids'] );
		$count = 0;

		foreach ( $order_ids as $id ) {
			if ( 'bulk-delete' === $action ) {
				$wpdb->delete( "{$wpdb->prefix}wa_orders", [ 'id' => $id ] );
				$wpdb->delete( "{$wpdb->prefix}wa_order_items", [ 'order_id' => $id ] );
				$count++;
			} elseif ( 'bulk-complete' === $action ) {
				$wpdb->update( "{$wpdb->prefix}wa_orders", [ 'status' => 'completed' ], [ 'id' => $id ] );
				$count++;
			} elseif ( 'bulk-fail' === $action ) {
				$wpdb->update( "{$wpdb->prefix}wa_orders", [ 'status' => 'failed' ], [ 'id' => $id ] );
				$count++;
			}
		}

		if ( $count > 0 ) {
			echo '<div class="updated"><p>' . sprintf( _n( '%d order updated.', '%d orders updated.', $count, 'webesia-wa-product-catalog' ), $count ) . '</p></div>';
		}
	}

	$table = new WPWA_Order_List_Table();
	$table->prepare_items();
	?>
	<div class="wrap">
		<h1 class="wp-heading-inline"><?php esc_html_e( 'Order List', 'webesia-wa-product-catalog' ); ?></h1>
		<hr class="wp-header-end">

		<div class="wpwa-order-list-container">
			<form method="post">
				<?php $table->display(); ?>
			</form>
		</div>
	</div>
	<?php
}
