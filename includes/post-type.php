<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function wpwa_register_post_type() {
	$labels = [
		'name'               => _x( 'Products', 'post type general name', 'webesia-wa-product-catalog' ),
		'singular_name'      => _x( 'Product', 'post type singular name', 'webesia-wa-product-catalog' ),
		'add_new'            => _x( 'Add New', 'product', 'webesia-wa-product-catalog' ),
		'add_new_item'       => esc_html__( 'Add New Product', 'webesia-wa-product-catalog' ),
		'edit_item'          => esc_html__( 'Edit Product', 'webesia-wa-product-catalog' ),
		'new_item'           => esc_html__( 'New Product', 'webesia-wa-product-catalog' ),
		'view_item'          => esc_html__( 'View Product', 'webesia-wa-product-catalog' ),
		'search_items'       => esc_html__( 'Search Products', 'webesia-wa-product-catalog' ),
		'not_found'          => esc_html__( 'No products found', 'webesia-wa-product-catalog' ),
		'not_found_in_trash' => esc_html__( 'No products found in Trash', 'webesia-wa-product-catalog' ),
		'menu_name'          => _x( 'WA Products', 'admin menu', 'webesia-wa-product-catalog' ),
		'featured_image'     => esc_html__( 'Gambar Utama', 'webesia-wa-product-catalog' ),
		'set_featured_image' => esc_html__( 'Atur Gambar Utama', 'webesia-wa-product-catalog' ),
		'remove_featured_image' => esc_html__( 'Hapus Gambar Utama', 'webesia-wa-product-catalog' ),
		'use_featured_image' => esc_html__( 'Gunakan sebagai Gambar Utama', 'webesia-wa-product-catalog' ),
	];

	$product_slug = get_option( 'wpwa_product_slug', 'produk' );
	$catalog_slug = get_option( 'wpwa_catalog_slug', 'toko' );

	$args = [
		'labels'              => $labels,
		'public'              => true,
		'has_archive'         => $catalog_slug,
		'publicly_queryable'  => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'query_var'           => true,
		'rewrite'             => [ 'slug' => $product_slug ],
		'capability_type'     => 'post',
		'hierarchical'        => false,
		'supports'            => [ 'title', 'editor', 'thumbnail', 'excerpt', 'comments' ],
		'menu_icon'           => 'dashicons-cart',
		'taxonomies'          => [ 'product_category' ],
		'show_in_rest'        => true,
	];

	register_post_type( 'simple_product', $args );

	// Register Taxonomy
	register_taxonomy( 'product_category', 'simple_product', [
		'label'        => esc_html__( 'Categories', 'webesia-wa-product-catalog' ),
		'rewrite'      => [ 'slug' => 'product-category' ],
		'hierarchical' => true,
		'show_in_rest' => true,
	] );
}
add_action( 'init', 'wpwa_register_post_type' );


// Meta Boxes
add_action( 'add_meta_boxes', 'wpwa_add_product_meta_boxes' );
function wpwa_add_product_meta_boxes() {
	add_meta_box(
		'wpwa_product_details',
		esc_html__( 'Product Details', 'webesia-wa-product-catalog' ),
		'wpwa_product_meta_box_html',
		'simple_product',
		'normal',
		'high'
	);

	add_meta_box(
		'wpwa_product_gallery',
		esc_html__( 'Product Gallery', 'webesia-wa-product-catalog' ),
		'wpwa_product_gallery_meta_box_html',
		'simple_product',
		'side',
		'default'
	);

	add_meta_box(
		'wpwa_product_excerpt',
		esc_html__( 'Product Short Description (Excerpt)', 'webesia-wa-product-catalog' ),
		'wpwa_product_excerpt_meta_box_html',
		'simple_product',
		'normal',
		'high'
	);

	add_meta_box(
		'wpwa_product_tabs',
		esc_html__( 'Custom Product Tabs', 'webesia-wa-product-catalog' ),
		'wpwa_product_tabs_meta_box_html',
		'simple_product',
		'normal',
		'low'
	);

	// Remove default excerpt box with late priority
	add_action( 'add_meta_boxes', function() {
		remove_meta_box( 'postexcerpt', 'simple_product', 'normal' );
	}, 999 );
}

// Force rename Featured Image metabox
add_action( 'do_meta_boxes', 'wpwa_rename_featured_image_box' );
function wpwa_rename_featured_image_box() {
	remove_meta_box( 'postimagediv', 'simple_product', 'side' );
	add_meta_box( 'postimagediv', esc_html__( 'Gambar Utama', 'webesia-wa-product-catalog' ), 'post_thumbnail_meta_box', 'simple_product', 'side', 'low' );
}

function wpwa_product_excerpt_meta_box_html( $post ) {
	?>
	<div class="wpwa-metabox-premium wpwa-excerpt-metabox">
		<div class="wpwa-premium-header">
			<div class="wpwa-header-left">
				<span class="dashicons dashicons-editor-alignleft"></span>
				<label for="wpwa_custom_excerpt"><?php esc_html_e( 'Kutipan / Deskripsi Singkat', 'webesia-wa-product-catalog' ); ?></label>
			</div>
			<div class="wpwa-header-right">
				<span class="wpwa-hint-text"><?php esc_html_e( 'Tampilkan ringkasan produk untuk memikat pembeli di halaman katalog.', 'webesia-wa-product-catalog' ); ?></span>
			</div>
		</div>
		<div class="wpwa-meta-field full-width no-label">
			<textarea id="wpwa_custom_excerpt" name="excerpt" rows="3" maxlength="90" placeholder="<?php esc_attr_e( 'Contoh: Layanan profesional terintegrasi untuk meningkatkan performa bisnis Anda...', 'webesia-wa-product-catalog' ); ?>"><?php echo esc_textarea( $post->post_excerpt ); ?></textarea>
			<div class="wpwa-field-info">
				<p><?php esc_html_e( 'Kutipan adalah teks ringkas operasional yang akan tampil di kartu produk.', 'webesia-wa-product-catalog' ); ?> <span class="wpwa-char-count-wrap">(<span id="wpwa-char-count">0</span>/90)</span></p>
			</div>
		</div>
		<div style="clear: both; display: block; height: 10px; width: 100%;"></div>
	</div>
	<div style="clear: both; display: block; height: 2px; width: 100%; margin-top: 20px;"></div>
	<?php
}

function wpwa_product_meta_box_html( $post ) {
	$sku           = get_post_meta( $post->ID, '_product_sku', true );
	$price         = get_post_meta( $post->ID, '_product_price', true );
	$stock         = get_post_meta( $post->ID, '_product_stock', true );
	$min_order     = get_post_meta( $post->ID, '_product_min_order', true ) ?: 1;
	$status_active = get_post_meta( $post->ID, '_product_status', true ) !== 'no';
	?>
	<div class="wpwa-metabox-premium">
		<div class="wpwa-meta-row">
			<div class="wpwa-meta-field">
				<label for="wpwa_sku">
					<span class="dashicons dashicons-barcode"></span>
					<?php esc_html_e( 'SKU:', 'webesia-wa-product-catalog' ); ?>
				</label>
				<input type="text" id="wpwa_sku" name="wpwa_sku" value="<?php echo esc_attr( $sku ); ?>" placeholder="e.g. PROD-001">
			</div>
			
			<div class="wpwa-meta-field">
				<label for="wpwa_price">
					<span class="dashicons dashicons-money-alt"></span>
					<?php printf( esc_html__( 'Regular Price (%s):', 'webesia-wa-product-catalog' ), wpwa_get_currency() ); ?>
				</label>
				<input type="number" id="wpwa_price" name="wpwa_price" value="<?php echo esc_attr( $price ); ?>" placeholder="0">
			</div>

			<div class="wpwa-meta-field">
				<label for="wpwa_sale_price">
					<span class="dashicons dashicons-tag"></span>
					<?php esc_html_e( 'Sale Price (Optional):', 'webesia-wa-product-catalog' ); ?>
				</label>
				<input type="number" id="wpwa_sale_price" name="wpwa_sale_price" value="<?php echo esc_attr( get_post_meta( $post->ID, '_product_sale_price', true ) ); ?>" placeholder="0">
			</div>
		</div>

		<div class="wpwa-meta-row">
			<div class="wpwa-meta-field">
				<label for="wpwa_stock">
					<span class="dashicons dashicons-archive"></span>
					<?php esc_html_e( 'Stock (Optional):', 'webesia-wa-product-catalog' ); ?>
				</label>
				<input type="number" id="wpwa_stock" name="wpwa_stock" value="<?php echo esc_attr( $stock ); ?>" placeholder="<?php esc_attr_e( 'Unlimited', 'webesia-wa-product-catalog' ); ?>">
			</div>

			<div class="wpwa-meta-field">
				<label for="wpwa_min_order">
					<span class="dashicons dashicons-cart"></span>
					<?php esc_html_e( 'Minimal Order:', 'webesia-wa-product-catalog' ); ?>
				</label>
				<input type="number" id="wpwa_min_order" name="wpwa_min_order" value="<?php echo esc_attr( $min_order ); ?>">
			</div>
		</div>

		<div class="wpwa-meta-field full-width">
			<label for="wpwa_whatsapp">
				<span class="dashicons dashicons-whatsapp"></span>
				<?php esc_html_e( 'WhatsApp Number (Override):', 'webesia-wa-product-catalog' ); ?>
			</label>
			<input type="text" id="wpwa_whatsapp" name="wpwa_whatsapp" value="<?php echo esc_attr( get_post_meta( $post->ID, '_product_whatsapp', true ) ); ?>" placeholder="<?php esc_attr_e( 'Leave empty for global number (e.g. 628xxx)', 'webesia-wa-product-catalog' ); ?>">
			<p class="description"><?php esc_html_e( 'Fill this if you want orders for this product to go to a different WhatsApp number.', 'webesia-wa-product-catalog' ); ?></p>
		</div>

		<div class="wpwa-meta-field checkbox-field">
			<label>
				<input type="checkbox" name="wpwa_status" value="yes" <?php checked( $status_active ); ?>>
				<span class="status-label"><?php esc_html_e( 'Product is Active (Display in Catalog)', 'webesia-wa-product-catalog' ); ?></span>
			</label>
		</div>
	</div>

	<style>
		.wpwa-metabox-premium { padding: 10px; }
		.wpwa-meta-row { display: flex; gap: 20px; margin-bottom: 15px; }
		.wpwa-meta-field { flex: 1; display: flex; flex-direction: column; gap: 8px; }
		.wpwa-meta-field.full-width { margin-bottom: 15px; }
		.wpwa-meta-field label { font-weight: 600; color: #1d2327; display: flex; align-items: center; gap: 6px; font-size: 13px; }
		.wpwa-meta-field label .dashicons { font-size: 17px; width: 17px; height: 17px; color: #2271b1; }
		.wpwa-meta-field input[type="text"], 
		.wpwa-meta-field input[type="number"] { padding: 8px 12px; border: 1px solid #8c8f94; border-radius: 4px; font-size: 14px; width: 100%; transition: all 0.2s; }
		.wpwa-meta-field input:focus { border-color: #2271b1; box-shadow: 0 0 0 1px #2271b1; outline: none; }
		.wpwa-meta-field.checkbox-field { background: #f6f7f7; padding: 12px; border-radius: 6px; border: 1px dashed #c3c4c7; }
		.wpwa-meta-field.checkbox-field label { cursor: pointer; }
		.wpwa-meta-field.checkbox-field input { margin-right: 8px; }
		.wpwa-meta-field.checkbox-field .status-label { font-weight: 600; color: #1d2327; }
		
		@media (max-width: 782px) {
			.wpwa-meta-row { flex-direction: column; gap: 15px; }
		}
	</style>
	<?php
}

function wpwa_product_gallery_meta_box_html( $post ) {
	wp_nonce_field( 'wpwa_gallery_nonce_action', 'wpwa_gallery_nonce' );
	$gallery_data = get_post_meta( $post->ID, '_product_gallery', true );
	$image_ids = ! empty( $gallery_data ) ? explode( ',', $gallery_data ) : [];
	?>
	<div id="wpwa-gallery-container">
		<ul class="wpwa-gallery-images">
			<?php if ( ! empty( $image_ids ) ) : ?>
				<?php foreach ( $image_ids as $attachment_id ) : ?>
					<li class="image" data-attachment_id="<?php echo esc_attr( $attachment_id ); ?>">
						<?php echo wp_get_attachment_image( $attachment_id, 'thumbnail' ); ?>
						<a href="#" class="delete" title="<?php esc_attr_e( 'Delete image', 'webesia-wa-product-catalog' ); ?>"></a>
					</li>
				<?php endforeach; ?>
			<?php endif; ?>
		</ul>
		<input type="hidden" id="wpwa_product_gallery_ids" name="wpwa_product_gallery" value="<?php echo esc_attr( $gallery_data ); ?>" data-post-id="<?php echo esc_attr( $post->ID ); ?>">
		<p class="add_product_images hide-if-no-js">
			<a href="#" class="button" id="wpwa-add-gallery-images"><?php esc_html_e( 'Add product gallery images', 'webesia-wa-product-catalog' ); ?></a>
		</p>
		<p class="wpwa-gallery-status" style="color: #46b450; display: none;"></p>
	</div>
	<?php
}

/**
 * Custom Tabs Metabox HTML
 */
function wpwa_product_tabs_meta_box_html( $post ) {
	wp_nonce_field( 'wpwa_tabs_nonce_action', 'wpwa_tabs_nonce' );
	$tabs = get_post_meta( $post->ID, '_product_tabs', true );
	if ( ! is_array( $tabs ) ) {
		$tabs = [];
	}
	?>
	<div id="wpwa-tabs-repeater" class="wpwa-metabox-premium">
		<div class="wpwa-tabs-list">
			<?php if ( ! empty( $tabs ) ) : ?>
				<?php foreach ( $tabs as $index => $tab ) : ?>
						<div class="wpwa-tab-header">
							<div class="wpwa-tab-header-left">
								<span class="dashicons dashicons-menu sort-handle"></span>
								<input type="text" name="wpwa_tabs[<?php echo esc_attr( $index ); ?>][title]" value="<?php echo esc_attr( $tab['title'] ); ?>" class="widefat" placeholder="<?php esc_attr_e( 'Judul Tab (misal: Fitur)', 'webesia-wa-product-catalog' ); ?>">
							</div>
							<a href="#" class="wpwa-remove-tab" title="<?php esc_attr_e( 'Hapus Tab', 'webesia-wa-product-catalog' ); ?>">
								<svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
							</a>
						</div>
						<div class="wpwa-tab-content">
							<?php 
							$editor_id = "wpwatabcontent_" . $index;
							$settings = array(
								'textarea_name' => "wpwa_tabs[$index][content]",
								'media_buttons' => true,
								'textarea_rows' => 8,
								'quicktags'     => true,
								'tinymce'       => array(
									'toolbar1' => 'formatselect,bold,italic,bullist,numlist,blockquote,alignleft,aligncenter,alignright,link,unlink,wp_more,fullscreen,wp_adv',
									'toolbar2' => 'strikethrough,hr,forecolor,pastetext,removeformat,charmap,outdent,indent,undo,redo',
									'setup' => 'function(ed) { ed.on("change", function() { ed.save(); }); }'
								)
							);
							wp_editor( $tab['content'], $editor_id, $settings );
							?>
						</div>
					</div>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>
		<div class="wpwa-tabs-footer">
			<button type="button" class="button button-secondary" id="wpwa-add-tab">
				<span class="dashicons dashicons-plus-alt2"></span> <?php esc_html_e( 'Tambah Tab Baru', 'webesia-wa-product-catalog' ); ?>
			</button>
		</div>

		<!-- Dummy editor to ensure WP Editor scripts/styles are loaded when page is empty -->
		<div style="display:none;">
			<?php wp_editor( '', 'wpwa_dummy_editor', array( 'media_buttons' => true ) ); ?>
		</div>
	</div>
	
	<!-- Template for new tabs -->
	<script type="text/template" id="wpwa-tab-template">
		<div class="wpwa-tab-item" data-index="{index}">
			<div class="wpwa-tab-header">
				<div class="wpwa-tab-header-left">
					<span class="dashicons dashicons-menu sort-handle"></span>
					<input type="text" name="wpwa_tabs[{index}][title]" value="" class="widefat" placeholder="<?php esc_attr_e( 'Judul Tab', 'webesia-wa-product-catalog' ); ?>">
				</div>
				<a href="#" class="wpwa-remove-tab" title="<?php esc_attr_e( 'Hapus Tab', 'webesia-wa-product-catalog' ); ?>">
					<svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
				</a>
			</div>
			<div class="wpwa-tab-content">
				<textarea id="wpwatabcontent_{unique}" name="wpwa_tabs[{index}][content]" class="wpwa-tab-editor-placeholder" rows="8" style="width:100%"></textarea>
			</div>
		</div>
	</script>

	<style>
		.wpwa-tab-item { background: #fff; border: 1px solid #e2e8f0; margin-bottom: 20px; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
		.wpwa-tab-header { background: #f8fafc; padding: 10px 15px; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between; }
		.wpwa-tab-header-left { display: flex; align-items: center; gap: 12px; flex: 1; }
		.wpwa-tab-header .sort-handle { color: #94a3b8; cursor: move; font-size: 20px; }
		.wpwa-tab-header input { flex: 1; font-weight: 600; font-size: 14px; height: 36px; border-radius: 6px; border: 1px solid #cbd5e1; }
		.wpwa-tab-header input:focus { border-color: #2271b1; box-shadow: 0 0 0 1px #2271b1; }
		.wpwa-remove-tab { color: #94a3b8; transition: color 0.2s; display: flex; align-items: center; }
		.wpwa-remove-tab:hover { color: #ef4444; }
		.wpwa-tab-content { padding: 15px; background: #fff; }
		.wpwa-tabs-footer { margin-top: 15px; border-top: 1px dashed #cbd5e1; padding-top: 15px; }
		#wpwa-add-tab { display: flex; align-items: center; gap: 6px; padding: 0 15px 0 12px; height: 36px; }
		#wpwa-tabs-repeater .wp-editor-wrap { margin-bottom: 0; }
	</style>
	<script>
	jQuery(document).ready(function($) {
		var $wrapper = $('#wpwa-tabs-repeater');
		var $list = $wrapper.find('.wpwa-tabs-list');
		var template = $('#wpwa-tab-template').html();

		$('#wpwa-add-tab').on('click', function(e) {
			e.preventDefault();
			var index = $list.find('.wpwa-tab-item').length;
			var unique = Date.now();
			var html = template.replace(/{index}/g, index).replace(/{unique}/g, unique);
			
			var $newItem = $(html);
			$list.append($newItem);

			var editorId = 'wpwatabcontent_' + unique;

			// Small delay to ensure DOM is ready
			setTimeout(function() {
				if (typeof wp !== 'undefined' && wp.editor && wp.editor.initialize) {
					wp.editor.initialize(editorId, {
						tinymce: {
							wpautop: true,
							plugins: 'charmap colorpicker hr image link lists paste tabfocus textcolor wordpress wpautoresize wpdialogs wpeditimage wpgallery wplink wpview',
							toolbar1: 'formatselect bold italic bullist numlist blockquote alignleft aligncenter alignright link wp_more spellchecker fullscreen wp_adv',
							toolbar2: 'strikethrough hr forecolor pastetext removeformat charmap outdent indent undo redo wp_help',
							statusbar: true,
							setup: function(ed) {
								ed.on('change', function() {
									ed.save();
								});
							}
						},
						quicktags: true,
						mediaButtons: true
					});
				} else {
					console.error('WP Editor initialization failed: wp.editor.initialize not found');
				}
			}, 100);
		});

		$list.on('click', '.wpwa-remove-tab', function(e) {
			e.preventDefault();
			if (confirm('Apakah Anda yakin ingin menghapus tab ini?')) {
				var $item = $(this).closest('.wpwa-tab-item');
				var editorId = $item.find('textarea').attr('id') || $item.find('.wp-editor-area').attr('id');
				
				if (editorId && typeof tinymce !== 'undefined' && tinymce.get(editorId)) {
					tinymce.remove('#' + editorId);
				}
				$item.remove();
			}
		});
	});
	</script>
	<?php
}

// AJAX handler to save gallery
add_action( 'wp_ajax_wpwa_save_gallery', 'wpwa_ajax_save_gallery' );
function wpwa_ajax_save_gallery() {
	// Verify nonce
	if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'wpwa_gallery_nonce_action' ) ) {
		wp_send_json_error( 'Security check failed' );
	}
	
	// Check if user is logged in
	if ( ! is_user_logged_in() ) {
		wp_send_json_error( 'Unauthorized' );
	}
	
	// Get post ID
	$post_id = isset( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : 0;
	if ( ! $post_id ) {
		wp_send_json_error( 'Invalid post ID' );
	}
	
	// Check permissions
	if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_post', $post_id ) ) {
		wp_send_json_error( 'Insufficient permissions' );
	}
	
	// Save gallery
	$gallery_ids = isset( $_POST['gallery_ids'] ) ? sanitize_text_field( wp_unslash( $_POST['gallery_ids'] ) ) : '';
	update_post_meta( $post_id, '_product_gallery', $gallery_ids );
	
	wp_send_json_success( esc_html__( 'Gallery saved successfully', 'webesia-wa-product-catalog' ) );
}

add_action( 'save_post_simple_product', 'wpwa_save_product_meta', 10, 2 );
function wpwa_save_product_meta( $post_id, $post ) {
	// Check autosave
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	
	// Check permissions
	if ( ! current_user_can( 'edit_post', $post_id ) ) return;
	
	// Check if this is the right post type
	if ( 'simple_product' !== $post->post_type ) return;

	// Use static variable to prevent recursion
	static $is_saving = false;
	if ( $is_saving ) return;
	
	// Verify nonce
	if ( ! isset( $_POST['wpwa_product_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['wpwa_product_nonce'] ) ), 'wpwa_save_product_data' ) ) {
		return;
	}

	// Save price
	if ( isset( $_POST['wpwa_sku'] ) ) {
		update_post_meta( $post_id, '_product_sku', sanitize_text_field( wp_unslash( $_POST['wpwa_sku'] ) ) );
	}

	if ( isset( $_POST['wpwa_price'] ) ) {
		update_post_meta( $post_id, '_product_price', sanitize_text_field( wp_unslash( $_POST['wpwa_price'] ) ) );
	}

	if ( isset( $_POST['wpwa_sale_price'] ) ) {
		update_post_meta( $post_id, '_product_sale_price', sanitize_text_field( wp_unslash( $_POST['wpwa_sale_price'] ) ) );
	}

	// Save excerpt manually to ensure it works with our custom redesigned box
	if ( isset( $_POST['excerpt'] ) ) {
		$is_saving = true; // Set guard
		
		// Remove the filter to avoid infinite loop
		remove_action( 'save_post_simple_product', 'wpwa_save_product_meta', 10 );
		
		wp_update_post( [
			'ID'           => $post_id,
			'post_excerpt' => sanitize_textarea_field( wp_unslash( $_POST['excerpt'] ) ) 
		] );
		
		add_action( 'save_post_simple_product', 'wpwa_save_product_meta', 10, 2 );
		$is_saving = false; // Reset guard
	}
	
	// Save stock
	if ( isset( $_POST['wpwa_stock'] ) ) {
		update_post_meta( $post_id, '_product_stock', sanitize_text_field( wp_unslash( $_POST['wpwa_stock'] ) ) );
	}
	
	// Save min order
	if ( isset( $_POST['wpwa_min_order'] ) ) {
		update_post_meta( $post_id, '_product_min_order', sanitize_text_field( wp_unslash( $_POST['wpwa_min_order'] ) ) );
	}

	// Save WhatsApp override
	if ( isset( $_POST['wpwa_whatsapp'] ) ) {
		update_post_meta( $post_id, '_product_whatsapp', sanitize_text_field( wp_unslash( $_POST['wpwa_whatsapp'] ) ) );
	}
	
	
	// Save gallery fallback (for new products or cases where AJAX wasn't triggered)
	if ( isset( $_POST['wpwa_product_gallery'] ) ) {
		update_post_meta( $post_id, '_product_gallery', sanitize_text_field( wp_unslash( $_POST['wpwa_product_gallery'] ) ) );
	}
	
	// Save status
	$status = isset( $_POST['wpwa_status'] ) ? 'yes' : 'no';
	update_post_meta( $post_id, '_product_status', $status );

	// Save Custom Tabs
	if ( isset( $_POST['wpwa_tabs'] ) && is_array( $_POST['wpwa_tabs'] ) ) {
		$tabs          = [];
		$posted_tabs   = array_map( 'wp_unslash', $_POST['wpwa_tabs'] );
		foreach ( $posted_tabs as $tab ) {
			if ( ! empty( $tab['title'] ) ) {
				$tabs[] = [
					'title'   => sanitize_text_field( $tab['title'] ),
					'content' => wp_kses_post( $tab['content'] ),
				];

			}
		}
		update_post_meta( $post_id, '_product_tabs', $tabs );
	} else {
		delete_post_meta( $post_id, '_product_tabs' );
	}
}

// Filter to use custom template for single simple_product and archives
add_filter( 'template_include', 'wpwa_product_templates' );
function wpwa_product_templates( $template ) {
	if ( is_singular( 'simple_product' ) ) {
		$new_template = WPWA_PATH . 'includes/single-product.php';
		if ( file_exists( $new_template ) ) {
			return $new_template;
		}
	}
	
	if ( is_post_type_archive( 'simple_product' ) || is_tax( 'product_category' ) ) {
		$new_template = WPWA_PATH . 'includes/archive-product.php';
		if ( file_exists( $new_template ) ) {
			return $new_template;
		}
	}
	
	return $template;
}

// Ensure the Shop Page shows products
add_action( 'pre_get_posts', 'wpwa_shop_page_query', 999 );
function wpwa_shop_page_query( $query ) {
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}

	$shop_page_id = get_option( 'wpwa_shop_page_id' );
	if ( ! $shop_page_id ) {
		return;
	}

	// Detect if this is the shop page
	$is_shop = false;
	
	if ( $query->get( 'page_id' ) == $shop_page_id || $query->get( 'p' ) == $shop_page_id ) {
		$is_shop = true;
	} elseif ( $query->get( 'pagename' ) ) {
		$shop_page = get_post( $shop_page_id );
		if ( $shop_page && $query->get( 'pagename' ) == $shop_page->post_name ) {
			$is_shop = true;
		}
	} elseif ( $query->is_front_page() && get_option( 'show_on_front' ) == 'page' && get_option( 'page_on_front' ) == $shop_page_id ) {
		$is_shop = true;
	}

	if ( $is_shop ) {
		$query->set( 'post_type', 'simple_product' );
		
		// Aggressively clear page/singular identifiers
		$query->set( 'page_id', '' );
		$query->set( 'p', '' );
		$query->set( 'pagename', '' );
		$query->set( 'name', '' );
		$query->set( 'post_parent', '' );
		$query->set( 'attachment', '' );
		$query->set( 'attachment_id', '' );
		$query->set( 'static', '' );
		
		// Fix pagination for static front page or custom page
		if ( get_query_var( 'paged' ) ) {
			$paged = get_query_var( 'paged' );
		} elseif ( get_query_var( 'page' ) ) {
			$paged = get_query_var( 'page' );
		} else {
			$paged = 1;
		}
		$query->set( 'paged', $paged );


		// Use WordPress default or custom limit if not set
		$limit = get_option( 'posts_per_page' );
		if ( ! $limit ) {
			$limit = 9;
		}
		
		// If specific query var is set (e.g. from shortcode on some contexts), use it
		if ( $query->get( 'posts_per_page' ) ) {
			// Do nothing, let it be
		} else {
			$query->set( 'posts_per_page', $limit );
		}
		
		// Handle Sorting
		$orderby = isset( $_GET['orderby'] ) ? sanitize_text_field( $_GET['orderby'] ) : 'date';
		if ( 'price' === $orderby ) {
			$query->set( 'meta_key', '_product_price' );
			$query->set( 'orderby', 'meta_value_num' );
			$query->set( 'order', 'ASC' );
		} elseif ( 'price-desc' === $orderby ) {
			$query->set( 'meta_key', '_product_price' );
			$query->set( 'orderby', 'meta_value_num' );
			$query->set( 'order', 'DESC' );
		} else {
			$query->set( 'orderby', 'date' );
			$query->set( 'order', 'DESC' );
		}

		// Handle Price Range
		$meta_query = [];
		$min_price = isset( $_GET['min_price'] ) ? floatval( $_GET['min_price'] ) : 0;
		$max_price = isset( $_GET['max_price'] ) ? floatval( $_GET['max_price'] ) : 0;

		if ( $min_price > 0 || $max_price > 0 ) {
			$price_clause = [
				'key'     => '_product_price',
				'type'    => 'NUMERIC',
				'compare' => 'BETWEEN',
			];

			if ( $min_price > 0 && $max_price > 0 ) {
				$price_clause['value'] = [ $min_price, $max_price ];
			} elseif ( $min_price > 0 ) {
				$price_clause['compare'] = '>=';
				$price_clause['value'] = $min_price;
			} elseif ( $max_price > 0 ) {
				$price_clause['compare'] = '<=';
				$price_clause['value'] = $max_price;
			}
			$meta_query[] = $price_clause;
		}

		if ( ! empty( $meta_query ) ) {
			$query->set( 'meta_query', $meta_query );
		} else {
			$query->set( 'meta_query', '' );
		}

		// Force archive status for template loader
		$query->is_page = false;
		$query->is_singular = false;
		$query->is_archive = true;
		$query->is_post_type_archive = true;
		$query->is_attachment = false;
		
		// Set a custom flag for title/template logic
		$query->set( 'wpwa_is_custom_shop', true );
	}
}
// Custom Post Updated Messages
add_filter( 'post_updated_messages', 'wpwa_product_updated_messages' );
function wpwa_product_updated_messages( $messages ) {
	$post             = get_post();
	$post_type        = get_post_type( $post );
	$post_type_object = get_post_type_object( $post_type );

	if ( 'simple_product' !== $post_type ) {
		return $messages;
	}

	$messages['simple_product'] = [
		0  => '', // Unused. Messages start at index 1.
		/* translators: %s: product permalink URL */
		1  => sprintf( esc_html__( 'Produk berhasil diperbarui. <a href="%s">Lihat produk</a>', 'webesia-wa-product-catalog' ), esc_url( get_permalink( $post->ID ) ) ),
		2  => esc_html__( 'Custom field diperbarui.', 'webesia-wa-product-catalog' ),
		3  => esc_html__( 'Custom field dihapus.', 'webesia-wa-product-catalog' ),
		4  => esc_html__( 'Produk diperbarui.', 'webesia-wa-product-catalog' ),
		/* translators: %s: revision date */
		5  => isset( $_GET['revision'] ) ? sprintf( esc_html__( 'Produk dikembalikan ke revisi dari %s', 'webesia-wa-product-catalog' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
		/* translators: %s: product permalink URL */
		6  => sprintf( esc_html__( 'Produk berhasil diterbitkan. <a href="%s">Lihat produk</a>', 'webesia-wa-product-catalog' ), esc_url( get_permalink( $post->ID ) ) ),
		7  => esc_html__( 'Produk disimpan.', 'webesia-wa-product-catalog' ),
		/* translators: %s: preview URL */
		8  => sprintf( esc_html__( 'Produk diajukan. <a target="_blank" href="%s">Pratinjau produk</a>', 'webesia-wa-product-catalog' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post->ID ) ) ) ),
		/* translators: %1$s: scheduled date, %2$s: preview URL */
		9  => sprintf( esc_html__( 'Produk dijadwalkan untuk: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Pratinjau produk</a>', 'webesia-wa-product-catalog' ), date_i18n( esc_html__( 'M j, Y @ G:i', 'webesia-wa-product-catalog' ), strtotime( $post->post_date ) ), esc_url( get_permalink( $post->ID ) ) ),
		/* translators: %s: preview URL */
		10 => sprintf( esc_html__( 'Draf produk diperbarui. <a target="_blank" href="%s">Pratinjau produk</a>', 'webesia-wa-product-catalog' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post->ID ) ) ) ),
	];

	return $messages;
}
