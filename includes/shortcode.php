<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Shortcode for Product Grid
add_shortcode( 'simple_products', 'wpwa_products_shortcode' );
add_shortcode( 'toko', 'wpwa_products_shortcode' );
function wpwa_products_shortcode( $atts ) {
	$atts = shortcode_atts( [
		'posts_per_page' => -1,
		'category'       => '',
		'filter'         => 'no', // New attribute: yes/no
        'columns'        => 3,
        'columns_tablet' => 2,
        'columns_mobile' => 1,
	], $atts );

	// Override with URL parameters if present
	// This allows the filter form to work with the shortcode
	if ( isset( $_GET['product_category'] ) ) {
		$atts['category'] = sanitize_text_field( $_GET['product_category'] );
	}
	
	// Prepare Query Args
	$args = [
		'post_type'      => 'simple_product',
		'posts_per_page' => $atts['posts_per_page'],
		'paged'          => get_query_var( 'paged' ) ? get_query_var( 'paged' ) : ( get_query_var( 'page' ) ? get_query_var( 'page' ) : ( isset( $_GET['paged'] ) ? intval( $_GET['paged'] ) : 1 ) ),
		'meta_query'     => [
			'relation' => 'OR',
			[
				'key'     => '_product_status',
				'value'   => 'yes',
				'compare' => '='
			],
			[
				'key'     => '_product_status',
				'compare' => 'NOT EXISTS'
			]
		]
	];

	if ( ! empty( $atts['category'] ) ) {
		$args['tax_query'] = [
			[
				'taxonomy' => 'product_category',
				'field'    => 'slug',
				'terms'    => $atts['category'],
			],
		];
	}

	// Handle Price Filter
	if ( isset( $_GET['min_price'] ) || isset( $_GET['max_price'] ) ) {
		$min = isset( $_GET['min_price'] ) && $_GET['min_price'] !== '' ? floatval( $_GET['min_price'] ) : 0;
		$max = isset( $_GET['max_price'] ) && $_GET['max_price'] !== '' ? floatval( $_GET['max_price'] ) : 9999999999;
		
		$args['meta_query'][] = [
			'key'     => '_product_price',
			'value'   => [ $min, $max ],
			'type'    => 'NUMERIC',
			'compare' => 'BETWEEN'
		];
	}

	// Handle Sorting
	if ( isset( $_GET['orderby'] ) ) {
		$orderby = sanitize_text_field( $_GET['orderby'] );
		switch ( $orderby ) {
			case 'price':
				$args['meta_key'] = '_product_price';
				$args['orderby']  = 'meta_value_num';
				$args['order']    = 'ASC';
				break;
			case 'price-desc':
				$args['meta_key'] = '_product_price';
				$args['orderby']  = 'meta_value_num';
				$args['order']    = 'DESC';
				break;
			case 'date':
			default:
				$args['orderby'] = 'date';
				$args['order']   = 'DESC';
				break;
		}
	}

	$atts['button_text'] = get_option( 'wpwa_button_text', esc_html__( 'Order via WhatsApp', 'webesia-wa-product-catalog' ) );
	$query = new WP_Query( $args );
	$button_text = $atts['button_text'];

	ob_start();
	
	// Wrapper for Layout
	if ( 'yes' === $atts['filter'] ) {
		echo '<div class="wpwa-catalog-layout">';
		
		// Sidebar
		echo '<aside class="wpwa-sidebar wpwa-desktop-only">';
		if ( function_exists( 'wpwa_render_product_filter' ) ) {
			// Pass current args to filter to maintain state
			wpwa_render_product_filter( ['product_category' => $atts['category']] );
		}
		echo '</aside>';

		echo '<div class="wpwa-main-content-area">';
		
		// Mobile Filter Trigger
		?>
		<div class="wpwa-mobile-filter-trigger">
			<span><?php esc_html_e( 'Product Filter', 'webesia-wa-product-catalog' ); ?></span>
			<svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>
		</div>
		<?php
	}

	if ( $query->have_posts() ) : 
        $grid_style = '';
        if ( ! empty( $atts['columns'] ) ) $grid_style .= '--wpwa-columns: ' . intval( $atts['columns'] ) . '; ';
        if ( ! empty( $atts['columns_tablet'] ) ) $grid_style .= '--wpwa-columns-tablet: ' . intval( $atts['columns_tablet'] ) . '; ';
        if ( ! empty( $atts['columns_mobile'] ) ) $grid_style .= '--wpwa-columns-mobile: ' . intval( $atts['columns_mobile'] ) . '; ';
        ?>
		<div class="wpwa-product-grid" style="<?php echo esc_attr( $grid_style ); ?>">
			<?php while ( $query->have_posts() ) : $query->the_post(); 
				$price = get_post_meta( get_the_ID(), '_product_price', true );
				$min_order = get_post_meta( get_the_ID(), '_product_min_order', true ) ?: 1;
				
				// Prepare WhatsApp URL for direct order
				$product_name = esc_attr( get_the_title() );
				$product_id = get_the_ID();
				$whatsapp_number = get_option( 'wpwa_phone' );
				/* translators: %1$s: product name, %2$d: product ID */
				$prefill_message = urlencode( sprintf( 
					esc_html__( 'Hello, I would like to order "%1$s" (ID: %2$d).', 'webesia-wa-product-catalog' ), 
					$product_name, 
					$product_id 
				) );
				$whatsapp_url = 'https://wa.me/' . preg_replace( '/[^0-9]/', '', $whatsapp_number ) . '?text=' . $prefill_message;
				?>
				<div class="wpwa-product-card">
					<div class="wpwa-product-image">
						<a href="<?php echo esc_url( get_permalink() ); ?>">
							<?php 
							$price = get_post_meta( get_the_ID(), '_product_price', true );
							$sale_price = get_post_meta( get_the_ID(), '_product_sale_price', true );
							$has_sale = !empty( $sale_price ) && floatval( $sale_price ) > 0;
							$display_price = $has_sale ? $sale_price : $price;
							
							if ( $has_sale ) : ?>
								<div class="wpwa-sale-badge"><?php esc_html_e( 'SALE', 'webesia-wa-product-catalog' ); ?></div>
							<?php endif; ?>
							<?php if ( has_post_thumbnail() ) : ?>
								<?php the_post_thumbnail( 'medium' ); ?>
							<?php else : ?>
								<img src="<?php echo esc_url( WPWA_URL . 'assets/images/placeholder.svg' ); ?>" alt="Placeholder">
							<?php endif; ?>
						</a>
					</div>
					<div class="wpwa-product-info">
						<h3><a href="<?php echo esc_url( get_permalink() ); ?>"><?php the_title(); ?></a></h3>
						
						<?php 
						$rating_summary = wpwa_get_rating_summary( get_the_ID() );
						if ( $rating_summary['count'] > 0 ) : ?>
							<div class="wpwa-card-rating">
								<?php echo wpwa_display_star_rating( $rating_summary['average'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								<span class="wpwa-rating-count">(<?php echo intval( get_comments_number() ); ?>)</span>
							</div>
						<?php endif; ?>

						<div class="wpwa-excerpt"><?php echo esc_html( mb_strimwidth( get_the_excerpt(), 0, 90, '...' ) ); ?></div>
						
						<div class="wpwa-card-price-row">
							<?php if ( $has_sale ) : ?>
								<span class="wpwa-price-regular strikethrough"><?php echo wpwa_format_price( $price ); ?></span>
								<span class="wpwa-price-value"><?php echo wpwa_format_price( $sale_price ); ?></span>
							<?php else : ?>
								<span class="wpwa-price-value"><?php echo wpwa_format_price( $price ); ?></span>
							<?php endif; ?>
						</div>
						
						<div class="wpwa-card-footer">
							<a href="<?php echo esc_url( get_permalink() ); ?>" class="wpwa-more-link">
								<?php esc_html_e( 'View Details', 'webesia-wa-product-catalog' ); ?> <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><polyline points="9 18 15 12 9 6"></polyline></svg>
							</a>
							<button class="wpwa-action-btn-wa wpwa-trigger-order" 
									data-id="<?php echo esc_attr( get_the_ID() ); ?>" 
									data-name="<?php echo esc_attr( get_the_title() ); ?>" 
									data-price="<?php echo esc_attr( number_format( (float)$display_price, 0, '', '' ) ); ?>"
									data-min="<?php echo esc_attr( $min_order ); ?>">
								<svg viewBox="0 0 24 24" width="22" height="22" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
							</button>
						</div>
					</div>
				</div>
			<?php endwhile; ?>
		</div>
		
		<?php
		// Only show pagination if there is more than 1 page
		if ( $query->max_num_pages > 1 ) {
			$pagination = paginate_links( [
				'base'      => str_replace( 999999999, '%#%', esc_url( get_pagenum_link( 999999999 ) ) ),
				'format'    => '?paged=%#%',
				'current'   => max( 1, get_query_var( 'paged' ), get_query_var( 'page' ), ( isset( $_GET['paged'] ) ? intval( $_GET['paged'] ) : 1 ) ),
				'total'     => $query->max_num_pages,
				'prev_text' => esc_html__( '« Previous', 'webesia-wa-product-catalog' ),
				'next_text' => esc_html__( 'Next »', 'webesia-wa-product-catalog' ),
				'type'      => 'list',
			] );

			if ( $pagination ) {
				echo '<div class="wpwa-pagination">' . $pagination . '</div>';
			}
		}
		
		wp_reset_postdata(); ?>
	<?php else : ?>
		<p class="wpwa-no-products"><?php esc_html_e( 'No products found.', 'webesia-wa-product-catalog' ); ?></p>
	<?php endif;

	// Close wrappers if filter was enabled
	if ( 'yes' === $atts['filter'] ) {
		echo '</div><!-- .wpwa-main-content-area -->';
		echo '</div><!-- .wpwa-catalog-layout -->';
	}

	return ob_get_clean();
}

// Order Modal HTML
function wpwa_order_modal_html() {
	$button_text = get_option( 'wpwa_button_text', 'Order via WhatsApp' );
	?>
	<!-- Order Popup Modal -->
	<div id="wpwa-order-modal" class="wpwa-modal">
		<div class="wpwa-modal-content">
			<div class="wpwa-close">
				<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
					<line x1="18" y1="6" x2="6" y2="18"></line>
					<line x1="6" y1="6" x2="18" y2="18"></line>
				</svg>
			</div>
			<div class="wpwa-modal-header">
				<h2><?php esc_html_e( 'Order Details', 'webesia-wa-product-catalog' ); ?></h2>
				<p id="wpwa-modal-welcome-msg"><?php echo esc_html( __( get_option('wpwa_welcome_msg', 'Please fill out the form below to order.'), 'webesia-wa-product-catalog' ) ); ?></p>
			</div>
			
			<form id="wpwa-order-form">
				<input type="hidden" id="wpwa-product-id" name="product_id">
				
				<?php 
				$default_form = [
					['id' => 'customer_name',    'label' => 'Your Name',         'type' => 'text',     'placeholder' => 'John Doe', 'required' => true, 'enabled' => true],
					['id' => 'customer_phone',   'label' => 'WhatsApp Number',    'type' => 'text',     'placeholder' => '628xxxxxxxxxx', 'required' => true, 'enabled' => true],
					['id' => 'customer_address', 'label' => 'Shipping Address', 'type' => 'textarea', 'placeholder' => 'Street Name, City, Postal Code', 'required' => true, 'enabled' => true],
					['id' => 'customer_note',    'label' => 'Note',           'type' => 'textarea', 'placeholder' => 'Delivery Date, Color, etc', 'required' => false, 'enabled' => true],
				];
				$custom_form = get_option( 'wpwa_custom_form', $default_form );
				?>

				<div class="wpwa-row-desktop">
					<!-- Left Column: Product Summary, Basic Info, Total -->
					<div class="wpwa-col-left">
						<div class="wpwa-product-summary-card">
							<h3 id="wpwa-modal-product-name"></h3>
							
							<div class="wpwa-form-row">
								<label for="wpwa-qty"><?php esc_html_e( 'Quantity', 'webesia-wa-product-catalog' ); ?></label>
								<input type="number" id="wpwa-qty" name="qty" min="1" value="1" required>
							</div>

							<?php 
							// Render First Field (usually Name) if enabled
							$name_field = array_values(array_filter($custom_form, function($f) { return $f['id'] === 'customer_name'; }))[0] ?? null;
							if ( $name_field && $name_field['enabled'] ) : ?>
								<div class="wpwa-form-row">
									<label for="wpwa-field-<?php echo esc_attr($name_field['id']); ?>"><?php echo esc_html( __($name_field['label'], 'webesia-wa-product-catalog') ); ?></label>
									<input type="<?php echo esc_attr($name_field['type']); ?>" 
										   id="wpwa-field-<?php echo esc_attr($name_field['id']); ?>" 
										   name="wpwa_field_<?php echo esc_attr($name_field['id']); ?>" 
										   placeholder="<?php echo esc_attr( __($name_field['placeholder'], 'webesia-wa-product-catalog') ); ?>"
										   <?php echo $name_field['required'] ? 'required' : ''; ?>>
								</div>
							<?php endif; ?>

							<div class="wpwa-form-total">
								<span><?php esc_html_e( 'ESTIMATED TOTAL:', 'webesia-wa-product-catalog' ); ?></span>
								<span id="wpwa-modal-total-display">$0</span>
							</div>
						</div>
					</div>
					
					<!-- Right Column: Other Fields -->
					<div class="wpwa-col-right">
						<?php 
						foreach ( $custom_form as $field ) : 
							if ( $field['id'] === 'customer_name' || !$field['enabled'] ) continue;
							?>
							<div class="wpwa-form-row">
								<label for="wpwa-field-<?php echo esc_attr($field['id']); ?>"><?php echo esc_html( __($field['label'], 'webesia-wa-product-catalog') ); ?></label>
								<?php if ( 'textarea' === $field['type'] ) : ?>
									<textarea id="wpwa-field-<?php echo esc_attr($field['id']); ?>" 
											  name="wpwa_field_<?php echo esc_attr($field['id']); ?>" 
											  rows="2" 
											  placeholder="<?php echo esc_attr( __($field['placeholder'], 'webesia-wa-product-catalog') ); ?>"
											  <?php echo $field['required'] ? 'required' : ''; ?>></textarea>
								<?php else : ?>
									<input type="text" 
										   id="wpwa-field-<?php echo esc_attr($field['id']); ?>" 
										   name="wpwa_field_<?php echo esc_attr($field['id']); ?>" 
										   placeholder="<?php echo esc_attr( __($field['placeholder'], 'webesia-wa-product-catalog') ); ?>"
										   <?php echo $field['required'] ? 'required' : ''; ?>>
								<?php endif; ?>
							</div>
						<?php endforeach; ?>
					</div>
				</div>

				<div class="wpwa-modal-footer">
					<button type="submit" class="wpwa-submit-order">
						<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="#ffffff"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
						<?php esc_html_e( 'Send Order via WhatsApp', 'webesia-wa-product-catalog' ); ?>
					</button>
				</div>
			</form>
		</div>
	</div>
	<?php
}

// Add modal to footer
add_action( 'wp_footer', 'wpwa_order_modal_html' );

/**
 * Shortcode for Related Products [related_products]
 */
add_shortcode( 'related_products', 'wpwa_related_products_shortcode' );
function wpwa_related_products_shortcode( $atts ) {
	$atts = shortcode_atts( [
		'limit' => 4,
		'id'    => get_the_ID(),
	], $atts, 'related_products' );

	if ( ! is_singular( 'simple_product' ) && empty( $atts['id'] ) ) {
		return '';
	}

	if ( function_exists( 'wpwa_get_related_products_html' ) ) {
		return wpwa_get_related_products_html( $atts['id'], $atts['limit'] );
	}
	
	return '';
}
