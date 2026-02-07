<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'admin_menu', 'wpwa_add_settings_menu' );
function wpwa_add_settings_menu() {
	add_submenu_page(
		'edit.php?post_type=simple_product',
		esc_html__( 'WhatsApp Settings', 'webesia-wa-product-catalog' ),
		esc_html__( 'Settings', 'webesia-wa-product-catalog' ),
		'manage_options',
		'wpwa-settings',
		'wpwa_settings_page_html'
	);
}

function wpwa_settings_page_html() {
	if ( ! current_user_can( 'manage_options' ) ) return;

	$active_tab = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : 'general';

	if ( isset( $_POST['wpwa_save_settings'] ) ) {
		check_admin_referer( 'wpwa_settings_nonce' );
		
		if ( $active_tab === 'general' ) {
			if ( isset( $_POST['wpwa_phone'] ) ) {
				update_option( 'wpwa_phone', sanitize_text_field( wp_unslash( $_POST['wpwa_phone'] ) ) );
			}
			if ( isset( $_POST['wpwa_button_text'] ) ) {
				update_option( 'wpwa_button_text', sanitize_text_field( wp_unslash( $_POST['wpwa_button_text'] ) ) );
			}
			if ( isset( $_POST['wpwa_message_template'] ) ) {
				update_option( 'wpwa_message_template', sanitize_textarea_field( wp_unslash( $_POST['wpwa_message_template'] ) ) );
			}
			if ( isset( $_POST['wpwa_welcome_msg'] ) ) {
				update_option( 'wpwa_welcome_msg', sanitize_textarea_field( wp_unslash( $_POST['wpwa_welcome_msg'] ) ) );
			}
			if ( isset( $_POST['wpwa_shop_page_id'] ) ) {
				update_option( 'wpwa_shop_page_id', intval( wp_unslash( $_POST['wpwa_shop_page_id'] ) ) );
			}
			if ( isset( $_POST['wpwa_product_slug'] ) ) {
				update_option( 'wpwa_product_slug', sanitize_title( wp_unslash( $_POST['wpwa_product_slug'] ) ) );
			}
			if ( isset( $_POST['wpwa_catalog_slug'] ) ) {
				update_option( 'wpwa_catalog_slug', sanitize_title( wp_unslash( $_POST['wpwa_catalog_slug'] ) ) );
			}
			if ( isset( $_POST['wpwa_currency'] ) ) {
				update_option( 'wpwa_currency', sanitize_text_field( wp_unslash( $_POST['wpwa_currency'] ) ) );
			}
			flush_rewrite_rules();
		} elseif ( $active_tab === 'form' ) {
			$custom_fields = [];
			if ( isset( $_POST['form_fields'] ) && is_array( $_POST['form_fields'] ) ) {
				$posted_fields = array_map( 'wp_unslash', $_POST['form_fields'] );
				foreach ( $posted_fields as $field ) {
					$custom_fields[] = [
						'id'          => sanitize_title( $field['id'] ),
						'label'       => sanitize_text_field( $field['label'] ),
						'placeholder' => sanitize_text_field( $field['placeholder'] ),
						'type'        => sanitize_text_field( $field['type'] ),
						'required'    => isset( $field['required'] ) ? true : false,
						'enabled'     => isset( $field['enabled'] ) ? true : false,
					];
				}
			}
			update_option( 'wpwa_custom_form', $custom_fields );
		} elseif ( $active_tab === 'language' ) {
			if ( isset( $_POST['wpwa_plugin_language'] ) ) {
				update_option( 'wpwa_plugin_language', sanitize_text_field( wp_unslash( $_POST['wpwa_plugin_language'] ) ) );
			}
			if ( isset( $_POST['wpwa_custom_translations'] ) && is_array( $_POST['wpwa_custom_translations'] ) ) {
				$translations = [];
				foreach ( $_POST['wpwa_custom_translations'] as $key => $value ) {
					$translations[sanitize_text_field( wp_unslash( $key ) )] = sanitize_text_field( wp_unslash( $value ) );
				}
				update_option( 'wpwa_custom_translations', $translations );
			}
		}
		
		echo '<div class="updated"><p>' . esc_html__( 'Settings saved successfully.', 'webesia-wa-product-catalog' ) . '</p></div>';
	}

	if ( isset( $_GET['setup'] ) && 'done' === $_GET['setup'] ) {
		echo '<div class="updated"><p>' . esc_html__( 'Auto-setup completed. "Shop" page has been verified/created.', 'webesia-wa-product-catalog' ) . '</p></div>';
	}

	// Transition "Silahkan isi form di bawah untuk memesan." to English if it's the current value
	$current_welcome = get_option( 'wpwa_welcome_msg' );
	if ( $current_welcome === 'Silahkan isi form di bawah untuk memesan.' ) {
		update_option( 'wpwa_welcome_msg', 'Please fill out the form below to order.' );
	}

	// Transition labels
	$custom_form = get_option( 'wpwa_custom_form' );
	if ( is_array( $custom_form ) ) {
		$updated = false;
		foreach ( $custom_form as &$field ) {
			if ( $field['label'] === 'Nama' ) { $field['label'] = 'Your Name'; $updated = true; }
			if ( $field['label'] === 'Nomor WhatsApp' ) { $field['label'] = 'WhatsApp Number'; $updated = true; }
			if ( $field['label'] === 'Alamat Pengiriman' ) { $field['label'] = 'Shipping Address'; $updated = true; }
			if ( $field['label'] === 'Catatan' ) { $field['label'] = 'Note'; $updated = true; }
			
			// Placeholder migrations
			if ( $field['placeholder'] === 'Nama Lengkap' ) { $field['placeholder'] = 'John Doe'; $updated = true; }
			if ( $field['placeholder'] === 'Contoh: 08123456789' ) { $field['placeholder'] = '628xxxxxxxxxx'; $updated = true; }
			if ( $field['placeholder'] === 'Nama Jalan, Kota, Kode Pos' ) { $field['placeholder'] = 'Street Name, City, Postal Code'; $updated = true; }
			if ( $field['placeholder'] === 'Street Name, City, Postcode' ) { $field['placeholder'] = 'Street Name, City, Postal Code'; $updated = true; }
			if ( $field['placeholder'] === 'Tanggal pengiriman, warna, dll.' ) { $field['placeholder'] = 'Delivery Date, Color, etc'; $updated = true; }
			if ( $field['placeholder'] === 'Delivery date, color, etc.' ) { $field['placeholder'] = 'Delivery Date, Color, etc'; $updated = true; }
		}
		if ( $updated ) {
			update_option( 'wpwa_custom_form', $custom_form );
		}
	}

	$phone           = get_option( 'wpwa_phone', '' );
	$button_text     = get_option( 'wpwa_button_text', esc_html__( 'Order via WhatsApp', 'webesia-wa-product-catalog' ) );
	$template        = get_option( 'wpwa_message_template', __( "Hello Admin, I would like to order \"{product_name}\" from your website \"{product_url}\":\n\nQuantity: {qty}\nTotal: {total}\nName: {customer_name}\nPhone: {customer_phone}\nAddress: {address}\nNote: {note}\n\nThank you.", 'webesia-wa-product-catalog' ) );
	$welcome_msg     = get_option( 'wpwa_welcome_msg', esc_html__( 'Please fill out the form below to order.', 'webesia-wa-product-catalog' ) );
	$shop_page_id    = get_option( 'wpwa_shop_page_id', 0 );
	$product_slug    = get_option( 'wpwa_product_slug', 'product' ); 
	$catalog_slug    = get_option( 'wpwa_catalog_slug', 'shop' );   

	// Default Form Configuration
	$default_form = [
		['id' => 'customer_name',    'label' => 'Your Name',         'type' => 'text',     'placeholder' => 'John Doe', 'required' => true, 'enabled' => true],
		['id' => 'customer_phone',   'label' => 'WhatsApp Number',    'type' => 'text',     'placeholder' => '628xxxxxxxxxx', 'required' => true, 'enabled' => true],
		['id' => 'customer_address', 'label' => 'Shipping Address', 'type' => 'textarea', 'placeholder' => 'Street Name, City, Postal Code', 'required' => true, 'enabled' => true],
		['id' => 'customer_note',    'label' => 'Note',           'type' => 'textarea', 'placeholder' => 'Delivery Date, Color, etc', 'required' => false, 'enabled' => true],
	];
	$custom_form = get_option( 'wpwa_custom_form', $default_form );

	?>
	<div class="wrap wpwa-settings-wrap">
		<h1 class="wp-heading-inline"><?php esc_html_e( 'WhatsApp Catalog Settings', 'webesia-wa-product-catalog' ); ?></h1>
		<hr class="wp-header-end">

		<h2 class="nav-tab-wrapper">
			<a href="?post_type=simple_product&page=wpwa-settings&tab=general" class="nav-tab <?php echo $active_tab == 'general' ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'General', 'webesia-wa-product-catalog' ); ?></a>
			<a href="?post_type=simple_product&page=wpwa-settings&tab=form" class="nav-tab <?php echo $active_tab == 'form' ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'Order Form', 'webesia-wa-product-catalog' ); ?></a>
			<a href="?post_type=simple_product&page=wpwa-settings&tab=language" class="nav-tab <?php echo $active_tab == 'language' ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'Language', 'webesia-wa-product-catalog' ); ?></a>
		</h2>

		<form method="post" action="" style="background: #fff; padding: 20px; border: 1px solid #ccd0d4; border-top: none; max-width: 1000px;">
			<?php wp_nonce_field( 'wpwa_settings_nonce' ); ?>

			<?php if ( 'general' === $active_tab ) : ?>
			<table class="form-table">
				<tr>
					<th><label for="wpwa_phone"><?php esc_html_e( 'WhatsApp Number (e.g. 628xxx)', 'webesia-wa-product-catalog' ); ?></label></th>
					<td><input type="text" name="wpwa_phone" id="wpwa_phone" value="<?php echo esc_attr( $phone ); ?>" class="regular-text"></td>
				</tr>
				<tr>
					<th><label for="wpwa_shop_page_id"><?php esc_html_e( 'Shop Page (Catalog)', 'webesia-wa-product-catalog' ); ?></label></th>
					<td>
						<?php 
						wp_dropdown_pages( [
							'name'             => 'wpwa_shop_page_id',
							'selected'         => absint( $shop_page_id ),
							'show_option_none' => esc_html__( 'Select Page', 'webesia-wa-product-catalog' ),
							'class'            => 'regular-text'
						] ); 
						?>
						<p class="description">
							<?php esc_html_e( 'Select a page to display your product catalog.', 'webesia-wa-product-catalog' ); ?>
							<br><strong><?php esc_html_e( 'Alternative:', 'webesia-wa-product-catalog' ); ?></strong> <?php esc_html_e( 'You can also use the shortcode', 'webesia-wa-product-catalog' ); ?> <code>[toko]</code> <?php esc_html_e( 'in Elementor, Gutenberg, or any page builder.', 'webesia-wa-product-catalog' ); ?>
						</p>
					</td>
				</tr>
				<tr>
					<th><label for="wpwa_product_slug"><?php esc_html_e( 'Single Product Slug (Permalinks)', 'webesia-wa-product-catalog' ); ?></label></th>
					<td>
						<input type="text" name="wpwa_product_slug" id="wpwa_product_slug" value="<?php echo esc_attr( $product_slug ); ?>" class="regular-text">
						<?php /* translators: %s: default slug value */ ?>
						<p class="description"><?php printf( esc_html__( 'Default: %s. (Example: domain.com/produk/bag-name)', 'webesia-wa-product-catalog' ), '<code>produk</code>' ); ?></p>
					</td>
				</tr>
				<tr>
					<th><label for="wpwa_catalog_slug"><?php esc_html_e( 'Archive Catalog Slug', 'webesia-wa-product-catalog' ); ?></label></th>
					<td>
						<input type="text" name="wpwa_catalog_slug" id="wpwa_catalog_slug" value="<?php echo esc_attr( $catalog_slug ); ?>" class="regular-text">
						<?php /* translators: %s: default slug value */ ?>
						<p class="description"><?php printf( esc_html__( 'Default: %s. (Example: domain.com/shop/)', 'webesia-wa-product-catalog' ), '<code>shop</code>' ); ?></p>
					</td>
				</tr>
				<tr>
					<th><label for="wpwa_currency"><?php esc_html_e( 'Currency Symbol (e.g. Rp, USD)', 'webesia-wa-product-catalog' ); ?></label></th>
					<td>
						<input type="text" name="wpwa_currency" id="wpwa_currency" value="<?php echo esc_attr( get_option( 'wpwa_currency', '$' ) ); ?>" class="regular-text">
						<p class="description"><?php esc_html_e( 'Enter your desired currency symbol or code.', 'webesia-wa-product-catalog' ); ?></p>
					</td>
				</tr>
				<tr>
					<th><label for="wpwa_button_text"><?php esc_html_e( 'Buy Button Text', 'webesia-wa-product-catalog' ); ?></label></th>
					<td><input type="text" name="wpwa_button_text" id="wpwa_button_text" value="<?php echo esc_attr( $button_text ); ?>" class="regular-text"></td>
				</tr>
				<tr>
					<th><label for="wpwa_welcome_msg"><?php esc_html_e( 'Popup Welcome Message', 'webesia-wa-product-catalog' ); ?></label></th>
					<td><textarea name="wpwa_welcome_msg" id="wpwa_welcome_msg" rows="3" class="large-text"><?php echo esc_textarea( $welcome_msg ); ?></textarea></td>
				</tr>
				<tr>
					<th><label for="wpwa_message_template"><?php esc_html_e( 'WhatsApp Message Template', 'webesia-wa-product-catalog' ); ?></label></th>
					<td>
						<textarea name="wpwa_message_template" id="wpwa_message_template" rows="8" class="large-text"><?php echo esc_textarea( $template ); ?></textarea>
						<p class="description">
							<?php esc_html_e( 'Available Tags:', 'webesia-wa-product-catalog' ); ?><br>
							<code>{product_name}</code>, <code>{product_url}</code>, <code>{qty}</code>, <code>{total}</code>, <code>{customer_name}</code>, <code>{customer_phone}</code>, <code>{address}</code>, <code>{note}</code>, <code>{order_number}</code>, <code>{details}</code><br>
							<br>
							<strong><?php esc_html_e( 'Tips:', 'webesia-wa-product-catalog' ); ?></strong> <?php esc_html_e( 'Use {details} to automatically list all fields from the "Order Form" tab. If you don\'t use {details}, custom fields will be appended at the end of the message.', 'webesia-wa-product-catalog' ); ?>
						</p>
					</td>
				</tr>
			</table>
			<?php endif; ?>

			<?php if ( 'language' === $active_tab ) : ?>
			<?php 
			$plugin_lang = get_option( 'wpwa_plugin_language', 'en' );
			$custom_translations = get_option( 'wpwa_custom_translations', [] );
			$core_maps = wpwa_get_translation_maps();
			?>
			<table class="form-table">
				<tr>
					<th><label for="wpwa_plugin_language"><?php esc_html_e( 'Plugin Language', 'webesia-wa-product-catalog' ); ?></label></th>
					<td>
						<select name="wpwa_plugin_language" id="wpwa_plugin_language" class="regular-text">
							<option value="en" <?php selected( $plugin_lang, 'en' ); ?>><?php esc_html_e( 'English (Default)', 'webesia-wa-product-catalog' ); ?></option>
							<option value="id" <?php selected( $plugin_lang, 'id' ); ?>><?php esc_html_e( 'Bahasa Indonesia (Bundled)', 'webesia-wa-product-catalog' ); ?></option>
							<option value="custom" <?php selected( $plugin_lang, 'custom' ); ?>><?php esc_html_e( 'Custom Translate', 'webesia-wa-product-catalog' ); ?></option>
						</select>
						<p class="description"><?php esc_html_e( 'Choose the primary language for your catalog.', 'webesia-wa-product-catalog' ); ?></p>
					</td>
				</tr>
			</table>

			<div id="wpwa-custom-translate-section" style="<?php echo $plugin_lang === 'custom' ? '' : 'display: none;'; ?> margin-top: 30px;">
				<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
					<h3 style="margin: 0;"><?php esc_html_e( 'Custom Translations', 'webesia-wa-product-catalog' ); ?></h3>
					<input type="text" id="wpwa-translation-search" placeholder="<?php esc_attr_e( 'Search keywords...', 'webesia-wa-product-catalog' ); ?>" class="regular-text" style="width: 300px;">
				</div>
				<p class="description" style="margin-bottom: 15px;"><?php esc_html_e( 'Translate or modify any string below. Leave empty to use the original English text.', 'webesia-wa-product-catalog' ); ?></p>
				
				<div style="max-height: 500px; overflow-y: auto; border: 1px solid #ccd0d4;">
					<table class="widefat striped">
						<thead>
							<tr>
								<th style="width: 45%;"><?php esc_html_e( 'Original String (English)', 'webesia-wa-product-catalog' ); ?></th>
								<th><?php esc_html_e( 'Your Translation', 'webesia-wa-product-catalog' ); ?></th>
							</tr>
						</thead>
						<tbody id="wpwa-translation-table-body">
							<?php foreach ( $core_maps as $original => $indonesian ) : ?>
							<tr class="wpwa-translation-row">
								<td class="wpwa-original-text"><strong><?php echo esc_html( $original ); ?></strong></td>
								<td>
									<input type="text" name="wpwa_custom_translations[<?php echo esc_attr( $original ); ?>]" 
										   value="<?php echo esc_attr( isset( $custom_translations[$original] ) ? $custom_translations[$original] : '' ); ?>" 
										   placeholder="<?php echo esc_attr( $indonesian ); ?>" 
										   class="widefat">
								</td>
							</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			</div>

			<script>
			jQuery(document).ready(function($) {
				// Toggle custom section
				$('#wpwa_plugin_language').on('change', function() {
					if ($(this).val() === 'custom') {
						$('#wpwa-custom-translate-section').slideDown();
					} else {
						$('#wpwa-custom-translate-section').slideUp();
					}
				});

				// Search logic
				$('#wpwa-translation-search').on('keyup', function() {
					const value = $(this).val().toLowerCase();
					$('.wpwa-translation-row').filter(function() {
						$(this).toggle($(this).find('.wpwa-original-text').text().toLowerCase().indexOf(value) > -1);
					});
				});
			});
			</script>
			<?php endif; ?>

			<?php if ( 'form' === $active_tab ) : ?>
			<div class="wpwa-form-builder">
				<p class="description" style="margin-bottom: 20px;"><?php esc_html_e( 'Enable, change labels, or set required fields for the WhatsApp order form. You can also add new fields as needed.', 'webesia-wa-product-catalog' ); ?></p>
				<table class="widefat striped wpwa-fields-table" style="margin-bottom: 20px;">
					<thead>
						<tr>
							<th style="width: 50px; text-align: center;"><?php esc_html_e( 'Active', 'webesia-wa-product-catalog' ); ?></th>
							<th><?php esc_html_e( 'Field ID', 'webesia-wa-product-catalog' ); ?></th>
							<th><?php esc_html_e( 'Label', 'webesia-wa-product-catalog' ); ?></th>
							<th><?php esc_html_e( 'Placeholder', 'webesia-wa-product-catalog' ); ?></th>
							<th><?php esc_html_e( 'Type', 'webesia-wa-product-catalog' ); ?></th>
							<th style="width: 50px; text-align: center;"><?php esc_html_e( 'Required', 'webesia-wa-product-catalog' ); ?></th>
							<th style="width: 50px; text-align: center;"></th>
						</tr>
					</thead>
					<tbody id="wpwa-fields-body">
						<?php foreach ( $custom_form as $index => $field ) : ?>
						<tr class="wpwa-field-row" data-index="<?php echo intval( $index ); ?>">
							<td style="text-align: center; vertical-align: middle;">
								<input type="checkbox" name="form_fields[<?php echo intval( $index ); ?>][enabled]" value="1" <?php checked( $field['enabled'], true ); ?>>
							</td>
							<td>
								<input type="text" name="form_fields[<?php echo intval( $index ); ?>][id]" value="<?php echo esc_attr( $field['id'] ); ?>" class="code" style="width: 100%;" <?php echo in_array($field['id'], ['customer_name', 'customer_phone', 'customer_address', 'customer_note']) ? 'readonly' : ''; ?>>
							</td>
							<td>
								<input type="text" name="form_fields[<?php echo intval( $index ); ?>][label]" value="<?php echo esc_attr( $field['label'] ); ?>" class="regular-text" style="width: 100%;">
							</td>
							<td>
								<input type="text" name="form_fields[<?php echo intval( $index ); ?>][placeholder]" value="<?php echo esc_attr( $field['placeholder'] ); ?>" class="regular-text" style="width: 100%;">
							</td>
							<td>
								<select name="form_fields[<?php echo intval( $index ); ?>][type]" style="width: 100%;">
									<option value="text" <?php selected( $field['type'], 'text' ); ?>><?php esc_html_e( 'Text', 'webesia-wa-product-catalog' ); ?></option>
									<option value="textarea" <?php selected( $field['type'], 'textarea' ); ?>><?php esc_html_e( 'Textarea', 'webesia-wa-product-catalog' ); ?></option>
								</select>
							</td>
							<td style="text-align: center; vertical-align: middle;">
								<input type="checkbox" name="form_fields[<?php echo intval( $index ); ?>][required]" value="1" <?php checked( $field['required'], true ); ?>>
							</td>
							<td style="text-align: center; vertical-align: middle;">
								<?php if ( !in_array($field['id'], ['customer_name', 'customer_phone', 'customer_address', 'customer_note']) ) : ?>
									<button type="button" class="wpwa-remove-field-btn" title="<?php esc_attr_e('Delete', 'webesia-wa-product-catalog'); ?>">
										<span class="dashicons dashicons-trash"></span>
									</button>
								<?php endif; ?>
							</td>
						</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
				<button type="button" id="wpwa-add-field" class="button button-secondary">
					<span class="dashicons dashicons-plus-alt" style="margin-top: 4px;"></span> <?php esc_html_e( 'Add New Field', 'webesia-wa-product-catalog' ); ?>
				</button>
				<p class="description" style="margin-top: 20px;">
					<?php 
					/* translators: %1$s: Note label, %2$s: {details} tag */ 
					printf( esc_html__( '%1$s The %2$s tag in the WhatsApp message template will automatically display all the fields above. Default fields (ID: customer_*) cannot have their IDs deleted but can be disabled.', 'webesia-wa-product-catalog' ), '<strong>' . esc_html__( 'Note:', 'webesia-wa-product-catalog' ) . '</strong>', '<code>{details}</code>' ); ?>
				</p>
			</div>

			<script>
			jQuery(document).ready(function($) {
				let fieldIndex = <?php echo intval( count($custom_form) ); ?>;
				
				$('#wpwa-add-field').on('click', function() {
					const row = `
					<tr class="wpwa-field-row" data-index="${fieldIndex}">
						<td style="text-align: center; vertical-align: middle;">
							<input type="checkbox" name="form_fields[${fieldIndex}][enabled]" value="1" checked>
						</td>
						<td>
							<input type="text" name="form_fields[${fieldIndex}][id]" value="field_${fieldIndex}" class="code" style="width: 100%;">
						</td>
						<td>
							<input type="text" name="form_fields[${fieldIndex}][label]" value="New Label" class="regular-text" style="width: 100%;">
						</td>
						<td>
							<input type="text" name="form_fields[${fieldIndex}][placeholder]" value="Example..." class="regular-text" style="width: 100%;">
						</td>
						<td>
							<select name="form_fields[${fieldIndex}][type]" style="width: 100%;">
								<option value="text">Text</option>
								<option value="textarea">Textarea</option>
							</select>
						</td>
						<td style="text-align: center; vertical-align: middle;">
							<input type="checkbox" name="form_fields[${fieldIndex}][required]" value="1">
						</td>
						<td style="text-align: center; vertical-align: middle;">
							<button type="button" class="wpwa-remove-field-btn" title="<?php esc_attr_e('Delete', 'webesia-wa-product-catalog'); ?>">
								<span class="dashicons dashicons-trash"></span>
							</button>
						</td>
					</tr>`;
					$('#wpwa-fields-body').append(row);
					fieldIndex++;
				});

				$(document).on('click', '.wpwa-remove-field-btn', function() {
					if (confirm('<?php echo esc_js( esc_html__( 'Delete this field?', 'webesia-wa-product-catalog' ) ); ?>')) {
						$(this).closest('tr').remove();
					}
				});
			});
			</script>
			<?php endif; ?>

			<p class="submit">
				<input type="submit" name="wpwa_save_settings" class="button-primary" value="<?php esc_attr_e( 'Save Settings', 'webesia-wa-product-catalog' ); ?>">
				<?php if ( 'general' === $active_tab ) : ?>
				<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=simple_product&page=wpwa-settings&wpwa_run_setup=1' ) ); ?>" class="button-secondary" style="margin-left: 10px;"><?php esc_html_e( 'Run Auto-Setup (Shop Page)', 'webesia-wa-product-catalog' ); ?></a>
				<?php endif; ?>
			</p>
		</form>
		<div class="wpwa-admin-footer" style="margin-top: 40px; padding-top: 20px; border-top: 1px solid #ddd; color: #777;">
			<p>Design & Build by <a href="https://webesia.com" target="_blank" style="color: #2271b1; text-decoration: none; font-weight: 600;">WebEsia</a> | Erwin Widianto</p>
		</div>
	</div>
	<?php
}
