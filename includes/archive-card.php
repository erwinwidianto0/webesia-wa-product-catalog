<?php
/**
 * Product Card template part for the archive (catalog)
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$price = get_post_meta( get_the_ID(), '_product_price', true );
$sale_price = get_post_meta( get_the_ID(), '_product_sale_price', true );
$has_sale = !empty( $sale_price ) && floatval( $sale_price ) > 0;
$display_price = $has_sale ? $sale_price : $price;

$min_order = get_post_meta( get_the_ID(), '_product_min_order', true ) ?: 1;
$button_text = get_option( 'wpwa_button_text', esc_html__( 'Order via WhatsApp', 'webesia-wa-product-catalog' ) );
?>

<div class="wpwa-product-card">
	<div class="wpwa-product-image">
		<a href="<?php echo esc_url( get_permalink() ); ?>">
			<?php if ( $has_sale ) : ?>
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
