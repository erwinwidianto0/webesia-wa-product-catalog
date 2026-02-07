<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render the product filter form
 *
 * @param array $args Optional arguments to override current query state
 */
function wpwa_render_product_filter( $args = [] ) {
	global $wp;
	
	// Determine current URL for form action
	// If we are on a singular page/post using shortcode, we want to stay on this page
	// If we are on the archive page, we use the archive URL
	$shop_page_id = get_option( 'wpwa_shop_page_id' );
	
	// Check if current page is the designated shop page or product archive
	$is_shop_page = ( is_page() && get_the_ID() == $shop_page_id ) || is_post_type_archive( 'simple_product' ) || is_tax( 'product_category' );

	if ( $is_shop_page ) {
		$action_url = home_url( add_query_arg( [], $wp->request ) );
		// Ensure we don't duplicate query args that might be part of the request path in some permalink structures
	} else {
		// We are likely on a page with shortcode
		global $post;
		$action_url = get_permalink( $post->ID );
		$shop_page_id = $post->ID; // Reset to self for "Clear All" button
	}

	$current_cat  = isset( $_GET['product_category'] ) ? sanitize_text_field( $_GET['product_category'] ) : ( isset($args['product_category']) ? $args['product_category'] : '' );
	$current_sort = isset( $_GET['orderby'] ) ? sanitize_text_field( $_GET['orderby'] ) : 'date';
	$current_min  = isset( $_GET['min_price'] ) ? floatval( $_GET['min_price'] ) : '';
	$current_max  = isset( $_GET['max_price'] ) ? floatval( $_GET['max_price'] ) : '';
	
	$categories = get_terms( [
		'taxonomy' => 'product_category',
		'hide_empty' => false,
	] );
	?>
	<div class="wpwa-filter-widget">
		<h3 class="widget-title"><?php esc_html_e( 'Product Filter', 'webesia-wa-product-catalog' ); ?></h3>
		<form action="<?php echo esc_url( $action_url ); ?>" method="get" class="wpwa-sidebar-filter-form">
			<!-- Preserve existing query args if needed, but for now standard GET form is fine -->
			
			<!-- Category Filter -->
			<div class="wpwa-filter-row">
				<label><?php esc_html_e( 'Category', 'webesia-wa-product-catalog' ); ?></label>
				<select name="product_category" class="wpwa-sidebar-input">
					<option value=""><?php esc_html_e( 'All Categories', 'webesia-wa-product-catalog' ); ?></option>
					<?php foreach ( $categories as $cat ) : ?>
						<option value="<?php echo esc_attr( $cat->slug ); ?>" <?php selected( $current_cat, $cat->slug ); ?>>
							<?php echo esc_html( $cat->name ); ?>
						</option>
					<?php endforeach; ?>
				</select>
			</div>

			<!-- Price Filter -->
			<div class="wpwa-filter-row">
				<label><?php esc_html_e( 'Price Range', 'webesia-wa-product-catalog' ); ?></label>
				<div class="wpwa-price-fields">
					<input type="number" name="min_price" value="<?php echo esc_attr( $current_min ); ?>" placeholder="<?php esc_attr_e( 'Min Price', 'webesia-wa-product-catalog' ); ?>" class="wpwa-sidebar-input">
					<input type="number" name="max_price" value="<?php echo esc_attr( $current_max ); ?>" placeholder="<?php esc_attr_e( 'Max Price', 'webesia-wa-product-catalog' ); ?>" class="wpwa-sidebar-input">
				</div>
			</div>

			<!-- Sort Filter -->
			<div class="wpwa-filter-row">
				<label><?php esc_html_e( 'Sort By', 'webesia-wa-product-catalog' ); ?></label>
				<select name="orderby" class="wpwa-sidebar-input">
					<option value="date" <?php selected( $current_sort, 'date' ); ?>><?php esc_html_e( 'Latest', 'webesia-wa-product-catalog' ); ?></option>
					<option value="price" <?php selected( $current_sort, 'price' ); ?>><?php esc_html_e( 'Price: Low to High', 'webesia-wa-product-catalog' ); ?></option>
					<option value="price-desc" <?php selected( $current_sort, 'price-desc' ); ?>><?php esc_html_e( 'Price: High to Low', 'webesia-wa-product-catalog' ); ?></option>
				</select>
			</div>

			<div class="wpwa-sidebar-actions">
				<button type="submit" class="wpwa-btn-apply"><?php esc_html_e( 'Apply Filter', 'webesia-wa-product-catalog' ); ?></button>
				<?php if ( ! empty( $current_cat ) || ! empty( $current_min ) || ! empty( $current_max ) || $current_sort !== 'date' ) : ?>
					<a href="<?php echo esc_url( get_permalink( $shop_page_id ) ); ?>" class="wpwa-btn-clear"><?php esc_html_e( 'Clear All', 'webesia-wa-product-catalog' ); ?></a>
				<?php endif; ?>
			</div>
		</form>
	</div>
	<?php
}

/**
 * Get related products HTML
 *
 * @param int $product_id The current product ID
 * @param int $limit Number of products to show
 * @return string HTML output
 */
function wpwa_get_related_products_html( $product_id, $limit = 4 ) {
	$terms = get_the_terms( $product_id, 'product_category' );
	
	$args = [
		'post_type'      => 'simple_product',
		'posts_per_page' => $limit,
		'post__not_in'   => [ $product_id ],
		'orderby'        => 'rand',
	];

	// If has terms, filter by them. Otherwise (fallback), just show random/latest.
	if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
		$term_ids = wp_list_pluck( $terms, 'term_id' );
		$args['tax_query'] = [
			[
				'taxonomy' => 'product_category',
				'field'    => 'term_id',
				'terms'    => $term_ids,
			],
		];
	}

	$query = new WP_Query( $args );
	
	if ( ! $query->have_posts() ) {
		return '';
	}

	ob_start();
	?>
	<div class="wpwa-related-products">
		<h2 class="wpwa-section-title"><?php esc_html_e( 'Related Products', 'webesia-wa-product-catalog' ); ?></h2>
		<div class="wpwa-product-grid">
			<?php 
			while ( $query->have_posts() ) : $query->the_post();
				include WPWA_PATH . 'includes/archive-card.php';
			endwhile; 
			wp_reset_postdata();
			?>
		</div>
	</div>
	<?php
	return ob_get_clean();
}

/**
 * Get product gallery HTML
 *
 * @param int $product_id Product ID
 * @return string HTML output
 */
function wpwa_get_product_gallery_html( $product_id ) {
	$product_id = intval( $product_id );
	if ( ! $product_id ) {
		return '';
	}

	$sale_price = get_post_meta( $product_id, '_product_sale_price', true );
	$has_sale   = !empty( $sale_price ) && floatval( $sale_price ) > 0;
	
	$gallery    = get_post_meta( $product_id, '_product_gallery', true );
	$image_ids  = ! empty( $gallery ) ? explode( ',', $gallery ) : [];
	
	ob_start();
	?>
	<div class="wpwa-product-gallery-wrapper">
		<div class="wpwa-tokped-gallery">
			<!-- Main Image (Top) -->
			<div class="wpwa-tokped-main-image">
				<?php if ( $has_sale ) : ?>
					<div class="wpwa-sale-badge"><?php esc_html_e( 'SALE', 'webesia-wa-product-catalog' ); ?></div>
				<?php endif; ?>
				<?php if ( has_post_thumbnail( $product_id ) ) : ?>
					<img class="wpwa-main-img" src="<?php echo esc_url( get_the_post_thumbnail_url( $product_id, 'large' ) ); ?>" alt="<?php echo esc_attr( get_the_title( $product_id ) ); ?>">
				<?php else : ?>
					<img class="wpwa-main-img" src="<?php echo esc_url( WPWA_URL . 'assets/images/placeholder.svg' ); ?>" alt="<?php echo esc_attr( get_the_title( $product_id ) ); ?>">
				<?php endif; ?>
				<div class="wpwa-zoom-icon">
					<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
						<circle cx="11" cy="11" r="8"></circle>
						<line x1="21" y1="21" x2="16.65" y2="16.65"></line>
					</svg>
				</div>
			</div>
			
			<!-- Thumbnails (Bottom) with Arrows -->
			<?php if ( ! empty( $image_ids ) ) : ?>
				<div class="wpwa-tokped-thumb-wrapper">
					<button class="wpwa-slider-arrow wpwa-prev" aria-label="Previous">‹</button>
					
					<div class="wpwa-tokped-thumbnails">
						<?php if ( has_post_thumbnail( $product_id ) ) : ?>
							<div class="wpwa-tokped-thumb active" data-image="<?php echo esc_url( get_the_post_thumbnail_url( $product_id, 'large' ) ); ?>">
								<?php echo wp_get_attachment_image( get_post_thumbnail_id( $product_id ), 'thumbnail' ); ?>
							</div>
						<?php endif; ?>
						<?php foreach ( $image_ids as $attachment_id ) : 
							$attachment_id = intval( $attachment_id );
							if ( wp_attachment_is_image( $attachment_id ) ) :
								$full_url = wp_get_attachment_image_url( $attachment_id, 'large' );
								if ( $full_url ) :
						?>
							<div class="wpwa-tokped-thumb" data-image="<?php echo esc_url( $full_url ); ?>">
								<?php echo wp_get_attachment_image( $attachment_id, 'thumbnail' ); ?>
							</div>
						<?php 
								endif;
							endif;
						endforeach; ?>
					</div>
					
					<button class="wpwa-slider-arrow wpwa-next" aria-label="Next">›</button>
				</div>
			<?php endif; ?>
		</div>

		<div id="wpwa-lightbox" class="wpwa-lightbox">
			<div class="wpwa-lightbox-wrapper">
				<div class="wpwa-lightbox-close">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
						<line x1="18" y1="6" x2="6" y2="18"></line>
						<line x1="6" y1="6" x2="18" y2="18"></line>
					</svg>
				</div>
				<img class="wpwa-lightbox-content" id="wpwa-lightbox-img">
			</div>
		</div>
	</div>
	<?php
	return ob_get_clean();
}

/**
 * Get product breadcrumb HTML
 *
 * @return string HTML output
 */
function wpwa_get_breadcrumb_html() {
	if ( ! is_singular( 'simple_product' ) ) {
		return '';
	}

	$delimiter = '<span class="wpwa-breadcrumb-separator">/</span>';
	$home_text = esc_html__( 'Home', 'webesia-wa-product-catalog' );
	$home_link = home_url( '/' );
	
	// Get Shop Page
	$shop_page_id = get_option( 'wpwa_shop_page_id' );
	$shop_link    = $shop_page_id ? get_permalink( $shop_page_id ) : home_url( '/shop/' );
	$shop_text    = $shop_page_id ? get_the_title( $shop_page_id ) : esc_html__( 'Shop', 'webesia-wa-product-catalog' );

	ob_start();
	?>
	<nav class="wpwa-breadcrumb">
		<a href="<?php echo esc_url( $home_link ); ?>"><?php echo esc_html( $home_text ); ?></a>
		<?php echo $delimiter; ?>
		
		<?php if ( $shop_page_id && ! is_page( $shop_page_id ) ) : ?>
			<a href="<?php echo esc_url( $shop_link ); ?>"><?php echo esc_html( $shop_text ); ?></a>
			<?php echo $delimiter; ?>
		<?php endif; ?>

		<?php
		$terms = get_the_terms( get_the_ID(), 'product_category' );
		if ( $terms && ! is_wp_error( $terms ) ) {
			// Sort by parent to get hierarchy (basic)
			// For simplicity take the first one and its parents
			$term = $terms[0];
			if ( $term->parent ) {
				$parent = get_term( $term->parent, 'product_category' );
				echo '<a href="' . esc_url( get_term_link( $parent ) ) . '">' . esc_html( $parent->name ) . '</a>';
				echo $delimiter;
			}
			echo '<a href="' . esc_url( get_term_link( $term ) ) . '">' . esc_html( $term->name ) . '</a>';
			echo $delimiter;
		}
		?>
		
		<span class="wpwa-breadcrumb-current"><?php the_title(); ?></span>
	</nav>
	<?php
	return ob_get_clean();
}
