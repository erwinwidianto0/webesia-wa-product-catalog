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
		}
		
		echo '<div class="updated"><p>' . esc_html__( 'Settings saved successfully.', 'webesia-wa-product-catalog' ) . '</p></div>';
	}

	if ( isset( $_GET['setup'] ) && 'done' === $_GET['setup'] ) {
		echo '<div class="updated"><p>' . esc_html__( 'Auto-setup completed. "Toko" page has been verified/created.', 'webesia-wa-product-catalog' ) . '</p></div>';
	}

	$phone           = get_option( 'wpwa_phone', '' );
	$button_text     = get_option( 'wpwa_button_text', esc_html__( 'Order via WhatsApp', 'webesia-wa-product-catalog' ) );
	$template        = get_option( 'wpwa_message_template', __( "Halo Admin, saya ingin pesan \"{product_name}\" dengan URL \"{product_url}\":\n\nJumlah: {qty}\nTotal: {total}\nNama: {customer_name}\nNomor HP: {customer_phone}\nAlamat: {address}\nCatatan: {note}\n\nTerimakasih.", 'webesia-wa-product-catalog' ) );
	$welcome_msg     = get_option( 'wpwa_welcome_msg', esc_html__( 'Silahkan isi form di bawah untuk memesan.', 'webesia-wa-product-catalog' ) );
	$shop_page_id    = get_option( 'wpwa_shop_page_id', 0 );
	$product_slug    = get_option( 'wpwa_product_slug', 'produk' ); 
	$catalog_slug    = get_option( 'wpwa_catalog_slug', 'toko' );   

	// Default Form Configuration
	$default_form = [
		['id' => 'customer_name',    'label' => 'Nama Anda',         'type' => 'text',     'placeholder' => 'Budi Santoso', 'required' => true, 'enabled' => true],
		['id' => 'customer_phone',   'label' => 'Nomor WhatsApp',    'type' => 'text',     'placeholder' => '08xxxxxxxxxx', 'required' => true, 'enabled' => true],
		['id' => 'customer_address', 'label' => 'Alamat Pengiriman', 'type' => 'textarea', 'placeholder' => 'Nama Jalan, Kota, Kode Pos', 'required' => true, 'enabled' => true],
		['id' => 'customer_note',    'label' => 'Catatan',           'type' => 'textarea', 'placeholder' => 'Tanggal pengiriman, warna, dll.', 'required' => false, 'enabled' => true],
	];
	$custom_form = get_option( 'wpwa_custom_form', $default_form );

	?>
	<div class="wrap wpwa-settings-wrap">
		<h1 class="wp-heading-inline"><?php esc_html_e( 'WhatsApp Catalog Settings', 'webesia-wa-product-catalog' ); ?></h1>
		<hr class="wp-header-end">

		<h2 class="nav-tab-wrapper">
			<a href="?post_type=simple_product&page=wpwa-settings&tab=general" class="nav-tab <?php echo $active_tab == 'general' ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'Umum', 'webesia-wa-product-catalog' ); ?></a>
			<a href="?post_type=simple_product&page=wpwa-settings&tab=form" class="nav-tab <?php echo $active_tab == 'form' ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'Formulir Order', 'webesia-wa-product-catalog' ); ?></a>
			<a href="?post_type=simple_product&page=wpwa-settings&tab=language" class="nav-tab <?php echo $active_tab == 'language' ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'Bahasa', 'webesia-wa-product-catalog' ); ?></a>
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
						<p class="description"><?php printf( esc_html__( 'Default: %s. (Example: domain.com/toko/)', 'webesia-wa-product-catalog' ), '<code>toko</code>' ); ?></p>
					</td>
				</tr>
				<tr>
					<th><label for="wpwa_currency"><?php esc_html_e( 'Currency Symbol (e.g. Rp, USD)', 'webesia-wa-product-catalog' ); ?></label></th>
					<td>
						<input type="text" name="wpwa_currency" id="wpwa_currency" value="<?php echo esc_attr( get_option( 'wpwa_currency', 'Rp' ) ); ?>" class="regular-text">
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
			<table class="form-table">
				<tr>
					<th><?php esc_html_e( 'Language Updates', 'webesia-wa-product-catalog' ); ?></th>
					<td>
						<div id="wpwa-translation-status">
							<p><span class="dashicons dashicons-translation" style="margin-right: 5px; color: #555;"></span> <strong><?php esc_html_e( 'Current Language:', 'webesia-wa-product-catalog' ); ?></strong> <?php echo esc_html( get_locale() ); ?></p>
							<p class="description"><?php esc_html_e( 'Translations are handled automatically for Indonesian. For other languages, use Loco Translate.', 'webesia-wa-product-catalog' ); ?></p>
							<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=simple_product&page=wpwa-settings&tab=language&wpwa_update_lang=1' ) ); ?>" class="button button-small" id="wpwa-btn-update-lang"><?php esc_html_e( 'Cek Pembaruan Bahasa', 'webesia-wa-product-catalog' ); ?></a>
						</div>
						<?php 
						if ( isset( $_GET['wpwa_update_lang'] ) ) {
							echo '<div style="margin-top: 10px; padding: 10px; background: #fdfdfd; border-left: 4px solid #46b450; font-size: 13px;">✓ ' . esc_html__( 'Semua bahasa sudah versi terbaru.', 'webesia-wa-product-catalog' ) . '</div>';
						}
						?>
					</td>
				</tr>
			</table>
			<?php endif; ?>

			<?php if ( 'form' === $active_tab ) : ?>
			<div class="wpwa-form-builder">
				<p class="description" style="margin-bottom: 20px;"><?php esc_html_e( 'Aktifkan, ubah label, atau atur field wajib untuk formulir order WhatsApp. Bapak juga bisa menambah field baru sesuai kebutuhan.', 'webesia-wa-product-catalog' ); ?></p>
				<table class="widefat striped wpwa-fields-table" style="margin-bottom: 20px;">
					<thead>
						<tr>
							<th style="width: 50px; text-align: center;"><?php esc_html_e( 'Aktif', 'webesia-wa-product-catalog' ); ?></th>
							<th><?php esc_html_e( 'ID Field', 'webesia-wa-product-catalog' ); ?></th>
							<th><?php esc_html_e( 'Label', 'webesia-wa-product-catalog' ); ?></th>
							<th><?php esc_html_e( 'Placeholder', 'webesia-wa-product-catalog' ); ?></th>
							<th><?php esc_html_e( 'Tipe', 'webesia-wa-product-catalog' ); ?></th>
							<th style="width: 50px; text-align: center;"><?php esc_html_e( 'Wajib', 'webesia-wa-product-catalog' ); ?></th>
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
									<button type="button" class="wpwa-remove-field-btn" title="<?php esc_attr_e('Hapus', 'webesia-wa-product-catalog'); ?>">
										<span class="dashicons dashicons-trash"></span>
									</button>
								<?php endif; ?>
							</td>
						</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
				<button type="button" id="wpwa-add-field" class="button button-secondary">
					<span class="dashicons dashicons-plus-alt" style="margin-top: 4px;"></span> <?php esc_html_e( 'Tambah Field Baru', 'webesia-wa-product-catalog' ); ?>
				</button>
				<p class="description" style="margin-top: 20px;">
					<?php 
					/* translators: %1$s: Note label, %2$s: {details} tag */ 
					printf( esc_html__( '%1$s Tag %2$s di template pesan WhatsApp akan otomatis menampilkan semua field di atas. Field default (ID: customer_*) tidak bisa dihapus ID-nya tapi bisa di-nonaktifkan.', 'webesia-wa-product-catalog' ), '<strong>' . esc_html__( 'Catatan:', 'webesia-wa-product-catalog' ) . '</strong>', '<code>{details}</code>' ); ?>
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
							<input type="text" name="form_fields[${fieldIndex}][label]" value="Label Baru" class="regular-text" style="width: 100%;">
						</td>
						<td>
							<input type="text" name="form_fields[${fieldIndex}][placeholder]" value="Contoh..." class="regular-text" style="width: 100%;">
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
							<button type="button" class="wpwa-remove-field-btn" title="<?php esc_attr_e('Hapus', 'webesia-wa-product-catalog'); ?>">
								<span class="dashicons dashicons-trash"></span>
							</button>
						</td>
					</tr>`;
					$('#wpwa-fields-body').append(row);
					fieldIndex++;
				});

				$(document).on('click', '.wpwa-remove-field-btn', function() {
					if (confirm('<?php echo esc_js( esc_html__( 'Hapus field ini?', 'webesia-wa-product-catalog' ) ); ?>')) {
						$(this).closest('tr').remove();
					}
				});
			});
			</script>
			<?php endif; ?>

			<p class="submit">
				<input type="submit" name="wpwa_save_settings" class="button-primary" value="<?php esc_attr_e( 'Save Settings', 'webesia-wa-product-catalog' ); ?>">
				<?php if ( 'general' === $active_tab ) : ?>
				<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=simple_product&page=wpwa-settings&wpwa_run_setup=1' ) ); ?>" class="button-secondary" style="margin-left: 10px;"><?php esc_html_e( 'Run Auto-Setup (Toko Page)', 'webesia-wa-product-catalog' ); ?></a>
				<?php endif; ?>
			</p>
		</form>
		<div class="wpwa-admin-footer" style="margin-top: 40px; padding-top: 20px; border-top: 1px solid #ddd; color: #777;">
			<p>Design & Build by <a href="https://webesia.com" target="_blank" style="color: #2271b1; text-decoration: none; font-weight: 600;">WebEsia</a> | Erwin Widianto</p>
		</div>
	</div>
	<?php
}
