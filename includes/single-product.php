<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Check if theme has header, otherwise provide fallback
if ( function_exists( 'get_header' ) ) {
	get_header();
} else {
	?><!DOCTYPE html><html <?php language_attributes(); ?>><head><meta charset="<?php bloginfo( 'charset' ); ?>"><meta name="viewport" content="width=device-width, initial-scale=1"><?php wp_head(); ?></head><body <?php body_class(); ?>><?php
}
?>

<div id="wpwa-primary-content" class="wpwa-page-wrapper">
	<div class="ct-container container site-content wpwa-container">
		<div class="wpwa-single-product-container">
		<?php while ( have_posts() ) : the_post(); 
			$product_id = get_the_ID();
			$price      = get_post_meta( $product_id, '_product_price', true );
			$sale_price = get_post_meta( $product_id, '_product_sale_price', true );
			$has_sale   = !empty( $sale_price ) && floatval( $sale_price ) > 0;
			$display_price = $has_sale ? $sale_price : $price;
			
			$min_order  = get_post_meta( $product_id, '_product_min_order', true ) ?: 1;
			$gallery    = get_post_meta( $product_id, '_product_gallery', true );
			$image_ids  = ! empty( $gallery ) ? explode( ',', $gallery ) : [];
			$button_text = get_option( 'wpwa_button_text', 'Order via WhatsApp' );
			
			// Prepare WhatsApp URL
			$product_name = esc_attr( get_the_title() );
			$whatsapp_number = get_option( 'wpwa_phone' );
			/* translators: %1$s: product name, %2$d: product ID */
			$prefill_message = urlencode( sprintf( 
				esc_html__( 'Hello, I would like to order "%1$s" (ID: %2$d).', 'webesia-wa-product-catalog' ), 
				$product_name, 
				$product_id 
			) );
			$whatsapp_url = 'https://wa.me/' . preg_replace( '/[^0-9]/', '', $whatsapp_number ) . '?text=' . $prefill_message;
		?>
			<article id="post-<?php the_ID(); ?>" <?php post_class( 'wpwa-single-product-content' ); ?>>
				<!-- Product Gallery -->
				<?php 
				if ( function_exists( 'wpwa_get_product_gallery_html' ) ) {
					echo wpwa_get_product_gallery_html( $product_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				}
				?>

				<div class="wpwa-product-details-section">
						<div class="wpwa-sticky-info">
							<header class="entry-header">
								<div class="wpwa-breadcrumb">
									<a href="<?php echo esc_url( home_url('/') ); ?>"><?php esc_html_e( 'Home', 'webesia-wa-product-catalog' ); ?></a>
									<?php 
									$terms = get_the_terms( $product_id, 'product_category' );
									if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) : 
										$term = reset( $terms ); ?>
										<span class="sep">/</span>
										<a href="<?php echo esc_url( get_term_link( $term ) ); ?>"><?php echo esc_html( $term->name ); ?></a>
									<?php endif; ?>
									<span class="sep">/</span>
									<span class="current"><?php the_title(); ?></span>
								</div>

								<h1 class="wpwa-single-title entry-title"><?php the_title(); ?></h1>
								
								<?php 
								$rating_summary = wpwa_get_rating_summary( $product_id );
								if ( $rating_summary['count'] > 0 ) : ?>
									<div class="wpwa-card-rating">
										<?php echo wpwa_display_star_rating( $rating_summary['average'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
										<span class="wpwa-rating-count">(<?php echo intval( get_comments_number() ); ?>)</span>
									</div>
								<?php endif; ?>
								
								<div class="wpwa-product-meta">
									<?php if ( ! empty( $terms ) ) : ?>
										<span class="wpwa-meta-item">
											<strong><?php esc_html_e( 'Category:', 'webesia-wa-product-catalog' ); ?></strong> 
											<?php the_terms( $product_id, 'product_category', '', ', ' ); ?>
										</span>
									<?php endif; ?>
									
									<?php 
									$sku = get_post_meta( $product_id, '_product_sku', true );
									if ( $sku ) : ?>
										<span class="wpwa-meta-item">
											<strong><?php esc_html_e( 'SKU:', 'webesia-wa-product-catalog' ); ?></strong> 
											<?php echo esc_html( $sku ); ?>
										</span>
									<?php endif; ?>
								</div>

								<div class="wpwa-single-price-wrapper">
									<?php if ( $has_sale ) : ?>
										<span class="wpwa-single-price-regular strikethrough"><?php echo wpwa_format_price( $price ); ?></span>
										<span class="wpwa-single-price"><?php echo wpwa_format_price( $sale_price ); ?></span>
									<?php else : ?>
										<p class="wpwa-single-price"><?php echo wpwa_format_price( $price ); ?></p>
									<?php endif; ?>
								</div>
							</header>

							<div class="wpwa-single-description entry-content">
								<?php the_content(); ?>
							</div>

							<div class="wpwa-single-actions">
								<div class="wpwa-actions">
									<?php 
									$button_text = get_option( 'wpwa_whatsapp_button_text', esc_html__( 'Order via WhatsApp', 'webesia-wa-product-catalog' ) );
									?>
									<a href="#" class="wpwa-trigger-order button wpwa-single-buy-btn" 
									data-id="<?php echo esc_attr( $product_id ); ?>" 
									data-name="<?php echo esc_attr( get_the_title() ); ?>" 
									data-price="<?php echo esc_attr( number_format( (float)$display_price, 0, '', '' ) ); ?>"
									data-min="<?php echo esc_attr( $min_order ); ?>">
										<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
										<span><?php echo esc_html( $button_text ); ?></span>
									</a>
								</div>
							</div>
						</div>
					</div>
					<!-- Schema.org Structured Data -->
				<?php
				$schema_image = has_post_thumbnail( $product_id ) ? get_the_post_thumbnail_url( $product_id, 'full' ) : '';
				$schema_desc = get_the_excerpt() ? get_the_excerpt() : get_the_title();
				$schema_data = [
					'@context'    => 'https://schema.org/',
					'@type'       => 'Product',
					'name'        => get_the_title(),
					'description' => wp_strip_all_tags( $schema_desc ),
					'image'       => $schema_image,
					'sku'         => get_post_meta( $product_id, '_product_sku', true ),
					'offers'      => [
						'@type'         => 'Offer',
						'url'           => get_permalink(),
						'priceCurrency' => 'IDR',
						'price'         => $display_price,
						'availability'  => 'https://schema.org/InStock',
					],
				];

				if ( $rating_summary['count'] > 0 ) {
					$schema_data['aggregateRating'] = [
						'@type'       => 'AggregateRating',
						'ratingValue' => $rating_summary['average'],
						'reviewCount' => intval( get_comments_number() ),
						'bestRating'  => '5',
						'worstRating' => '1'
					];
				}
				?>
				<script type="application/ld+json">
					<?php echo wp_json_encode( $schema_data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ); ?>
				</script>
			</article>

				<div class="wpwa-product-tabs">
					<div class="wpwa-tab-nav">
						<?php 
						$custom_tabs = get_post_meta( $product_id, '_product_tabs', true );
						$has_custom = ! empty( $custom_tabs ) && is_array( $custom_tabs );
						
						if ( $has_custom ) : 
							foreach ( $custom_tabs as $index => $tab ) : ?>
								<button class="wpwa-tab-trigger <?php echo $index === 0 ? 'active' : ''; ?>" data-tab="wpwa-tab-custom-<?php echo esc_attr( $index ); ?>">
									<?php echo esc_html( $tab['title'] ); ?>
								</button>
							<?php endforeach; 
						endif; ?>

						<button class="wpwa-tab-trigger <?php echo ! $has_custom ? 'active' : ''; ?>" data-tab="wpwa-tab-reviews">
							<?php 
							/* translators: %d: number of reviews */
							printf( esc_html__( 'Reviews (%d)', 'webesia-wa-product-catalog' ), intval( get_comments_number() ) ); 
							?>
						</button>
					</div>

					<div class="wpwa-tab-content-wrapper">
						<!-- Custom Tabs -->
						<?php if ( $has_custom ) : 
							foreach ( $custom_tabs as $index => $tab ) : ?>
								<div class="wpwa-tab-panel <?php echo $index === 0 ? 'active' : ''; ?>" id="wpwa-tab-custom-<?php echo esc_attr( $index ); ?>">
									<?php echo wp_kses_post( wpautop( $tab['content'] ) ); ?>
								</div>
							<?php endforeach; 
						endif; ?>

						<!-- Reviews Tab -->
						<div class="wpwa-tab-panel <?php echo ! $has_custom ? 'active' : ''; ?>" id="wpwa-tab-reviews">
							<div class="wpwa-reviews-summary">
								<?php 
								$avg_rating = wpwa_get_average_rating( $product_id );
								$total_reviews = get_comments_number();
								?>
								<div class="wpwa-avg-rating">
									<span class="wpwa-avg-number"><?php echo number_format( $avg_rating, 1 ); ?></span>
									<?php echo wpwa_display_star_rating( round($avg_rating) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
									<span class="wpwa-total-text"><?php 
									/* translators: %d: number of reviews */
									printf( esc_html__( '%d Reviews', 'webesia-wa-product-catalog' ), intval( $total_reviews ) ); 
									?></span>
								</div>
							</div>

							<div class="wpwa-reviews-list-container">
								<?php 
								// IMPORTANT: Force comments query for this post
								$comments = get_comments( array(
									'post_id' => $product_id,
									'status'  => 'approve',
								) );
								
								if ( ! empty( $comments ) ) :
									?>
									<ul class="wpwa-reviews-list">
										<?php 
										wp_list_comments( array(
											'callback'     => 'wpwa_review_callback',
											'type'         => 'comment',
											'avatar_size'  => 48,
											'style'        => 'ul',
											'short_ping'   => true,
										), $comments );
										?>
									</ul>
									<?php
								else :
									?>
									<p class="wpwa-no-reviews"><?php esc_html_e( 'No reviews yet. Be the first to review!', 'webesia-wa-product-catalog' ); ?></p>
									<?php
								endif;
								?>
							</div>

							<div class="wpwa-review-form-wrapper">
								<?php 
								$commenter = wp_get_current_commenter();
								$req = get_option( 'require_name_email' );
								$aria_req = ( $req ? " aria-required='true'" : '' );

								comment_form( array(
									'title_reply'          => esc_html__( 'Write Your Review', 'webesia-wa-product-catalog' ),
									'title_reply_to'       => /* translators: %s: commenter name */ esc_html__( 'Reply to %s', 'webesia-wa-product-catalog' ),
									'comment_notes_before' => '',
									'label_submit'         => esc_html__( 'Submit Review', 'webesia-wa-product-catalog' ),
									'class_submit'         => 'wpwa-submit-order button',
									'id_form'              => 'wpwa-review-form',
									'comment_field'        => '<p class="comment-form-comment"><label for="comment">' . _x( 'Review', 'noun', 'webesia-wa-product-catalog' ) . '</label><textarea id="comment" name="comment" cols="45" rows="5" aria-required="true" class="widefat" placeholder="' . esc_attr__( 'Tulis pengalaman Anda menggunakan produk ini...', 'webesia-wa-product-catalog' ) . '"></textarea></p>',
									'fields'               => array(
										'author' => '<div class="wpwa-form-row' . ( $req ? ' required' : '' ) . '"><label for="author">' . esc_html__( 'Name', 'webesia-wa-product-catalog' ) . '</label> <input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30"' . $aria_req . ' class="widefat" placeholder="' . esc_attr__( 'Nama Anda', 'webesia-wa-product-catalog' ) . '" /></div>',
										'email'  => '<div class="wpwa-form-row' . ( $req ? ' required' : '' ) . '"><label for="email">' . esc_html__( 'Email', 'webesia-wa-product-catalog' ) . '</label> <input id="email" name="email" type="email" value="' . esc_attr(  $commenter['comment_author_email'] ) . '" size="30"' . $aria_req . ' class="widefat" placeholder="' . esc_attr__( 'Alamat Email', 'webesia-wa-product-catalog' ) . '" /></div>',
									),
								) );
								?>
							</div>
						</div>

					</div>
				</div>
				
				<?php 
				if ( function_exists( 'wpwa_get_related_products_html' ) ) {
					echo wpwa_get_related_products_html( get_the_ID() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				}
				?>
		<?php endwhile; ?>
	</div>
</main>



<?php 
// Check if theme has footer, otherwise provide fallback
if ( function_exists( 'get_footer' ) ) {
	get_footer();
} else {
	?><?php wp_footer(); ?></body></html><?php
}
?>
