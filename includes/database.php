<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function wpwa_create_database_tables() {
	global $wpdb;
	$charset_collate = $wpdb->get_charset_collate();
	
	$table_orders = $wpdb->prefix . 'wa_orders';
	$table_items  = $wpdb->prefix . 'wa_order_items';
	
	$sql_orders = "CREATE TABLE $table_orders (
		id bigint(20) NOT NULL AUTO_INCREMENT,
		order_number varchar(50) NOT NULL,
		customer_name varchar(100) NOT NULL,
		customer_phone varchar(20) NOT NULL,
		customer_address text NOT NULL,
		customer_note text DEFAULT '',
		total_amount decimal(15,2) NOT NULL DEFAULT 0,
		status varchar(20) NOT NULL DEFAULT 'pending',
		created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
		PRIMARY KEY  (id)
	) $charset_collate;";
	
	$sql_items = "CREATE TABLE $table_items (
		id bigint(20) NOT NULL AUTO_INCREMENT,
		order_id bigint(20) NOT NULL,
		product_id bigint(20) NOT NULL,
		product_name varchar(255) NOT NULL,
		quantity int(11) NOT NULL,
		price decimal(15,2) NOT NULL,
		subtotal decimal(15,2) NOT NULL,
		PRIMARY KEY  (id)
	) $charset_collate;";
	
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	dbDelta( $sql_orders );
	dbDelta( $sql_items );
}
