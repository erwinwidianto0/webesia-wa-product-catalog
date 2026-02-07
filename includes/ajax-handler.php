<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'wp_ajax_wpwa_submit_order', 'wpwa_handle_ajax_order' );
add_action( 'wp_ajax_nopriv_wpwa_submit_order', 'wpwa_handle_ajax_order' );

function wpwa_handle_ajax_order() {
	check_ajax_referer( 'wpwa_order_nonce', 'nonce' );

	$posted     = wp_unslash( $_POST );
	$product_id = intval( $posted['product_id'] );
	$qty        = intval( $posted['qty'] );
	if ( $qty < 1 ) $qty = 1;

	$fields = [];
	foreach ( $posted as $key => $value ) {
		if ( strpos( $key, 'wpwa_field_' ) === 0 ) {
			$field_id            = str_replace( 'wpwa_field_', '', $key );
			$fields[ $field_id ] = sanitize_textarea_field( $value );
		}
	}

	$name    = $fields['customer_name'] ?? '';
	$phone   = $fields['customer_phone'] ?? '';
	$address = $fields['customer_address'] ?? '';
	$note    = $fields['customer_note'] ?? '';

	// Fetch Price & Name safely
	$price_raw = get_post_meta( $product_id, '_product_price', true );
	// Handle if price is stored as "20.000,00" or "IDR 20.000" or similar
	$price_cleaned = preg_replace( '/[^0-9]/', '', $price_raw ); 
	$price = floatval( $price_cleaned ); 

	$product_name = get_the_title( $product_id );
	if ( empty( $product_name ) ) {
		$product_name = '(Product ID: ' . $product_id . ')';
	}

	$total = $price * $qty;

	global $wpdb;
	$table_orders = $wpdb->prefix . 'wa_orders';
	$table_items  = $wpdb->prefix . 'wa_order_items';

	$order_number = 'ORD-' . strtoupper( wp_generate_password( 6, false ) );

	$wpdb->insert( $table_orders, [
		'order_number'    => $order_number,
		'customer_name'   => $name,
		'customer_phone'  => $phone,
		'customer_address' => $address,
		'customer_note'    => $note,
		'total_amount'    => $total,
		'status'          => 'pending',
		'created_at'      => current_time( 'mysql' )
	] );

	$order_id = $wpdb->insert_id;
	if ( ! $order_id ) {
		wp_send_json_error( esc_html__( 'Failed to save order to database.', 'webesia-wa-product-catalog' ) );
	}

	$wpdb->insert( $table_items, [
		'order_id'     => $order_id,
		'product_id'   => $product_id,
		'product_name' => $product_name,
		'quantity'     => $qty,
		'price'        => $price,
		'subtotal'     => $total
	] );

	// Generate WA Link
	$wa_phone = get_post_meta( $product_id, '_product_whatsapp', true );
	if ( empty( $wa_phone ) ) {
		$wa_phone = get_option( 'wpwa_phone', '' );
	}
	
	// Force the new template format requested by the user
	$default_template = __( "Hello Admin, I would like to order \"{product_name}\" with URL \"{product_url}\":\n\nQuantity: {qty}\nTotal: {total}\nName: {customer_name}\nPhone: {customer_phone}\nAddress: {address}\nNote: {note}\n\nThank you.", 'webesia-wa-product-catalog' );
	$template = get_option( 'wpwa_message_template', $default_template );
	
	// If the template is still using the old style (no product_url), force update it to the new style
	if ( strpos( $template, '{product_url}' ) === false ) {
		$template = $default_template;
		update_option( 'wpwa_message_template', $template );
	}

	$product_url   = get_permalink( $product_id );
	$order_details = "";
	
	// Get field labels for better display in WA
	$custom_form = get_option( 'wpwa_custom_form', [] );
	foreach ( $fields as $fid => $fval ) {
		$label = $fid;
		foreach ( $custom_form as $cf ) {
			if ( $cf['id'] === $fid ) {
				$label = esc_html( $cf['label'] );
				break;
			}
		}
		if ( ! empty( $fval ) ) {
			$order_details .= "\n" . $label . ": " . $fval;
		}
	}

	$message = str_replace(
		[ '{product_name}', '{product_url}', '{qty}', '{total}', '{customer_name}', '{customer_phone}', '{address}', '{note}', '{order_number}', '{details}' ],
		[ $product_name, $product_url, $qty, wpwa_format_price( $total ), $name, $phone, $address, $note, $order_number, $order_details ],
		$template
	);

	// If {details} tag is not used but they added custom fields, append them if not already there
	if ( strpos( $template, '{details}' ) === false && count($fields) > 4 ) {
		$message .= "\n\n" . __( "Additional Details:", 'webesia-wa-product-catalog' ) . $order_details;
	}

	$wa_url = "https://wa.me/" . preg_replace( '/[^0-9]/', '', $wa_phone ) . "?text=" . urlencode( $message );

	wp_send_json_success( [ 'wa_url' => $wa_url ] );
}
