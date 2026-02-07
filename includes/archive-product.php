<?php
/**
 * The template for displaying product archives (catalog)
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header(); ?>

<div id="wpwa-primary-content" class="wpwa-page-wrapper">
	<div class="ct-container container site-content wpwa-container">
		<div class="wpwa-single-product-container">

		<?php
		// Pre-fetch current values
		$current_cat     = get_query_var( 'product_category' );
		$current_sort    = isset( $_GET['orderby'] ) ? sanitize_text_field( $_GET['orderby'] ) : 'date';
		$current_min     = isset( $_GET['min_price'] ) ? floatval( $_GET['min_price'] ) : '';
		$current_max     = isset( $_GET['max_price'] ) ? floatval( $_GET['max_price'] ) : '';
		
		// Category List
		$categories = get_terms( [
			'taxonomy' => 'product_category',
			'hide_empty' => false,
		] );
		?>

		<div class="wpwa-catalog-layout">
			<!-- Sidebar Filters (Desktop Only) -->
			<aside class="wpwa-sidebar wpwa-desktop-only">
				<?php wpwa_render_product_filter(); ?>
			</aside>

			<!-- Main Content -->
		<div class="wpwa-main-content-area">
			<!-- Mobile Filter Trigger (Clickable Nav Bar) -->
			<div class="wpwa-mobile-filter-trigger">
				<span><?php esc_html_e( 'Product Filter', 'webesia-wa-product-catalog' ); ?></span>
				<svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>
			</div>

			<?php if ( have_posts() ) : ?>
					<div class="wpwa-product-grid">
						<?php
						while ( have_posts() ) :
							the_post();
							include WPWA_PATH . 'includes/archive-card.php';
						endwhile;
						?>
					</div>
					
					<div class="wpwa-pagination">
						<?php
						global $wp_query;
						$pagination = paginate_links( [
							'base'      => str_replace( 999999999, '%#%', esc_url( get_pagenum_link( 999999999 ) ) ),
							'format'    => '?paged=%#%',
							'current'   => max( 1, get_query_var( 'paged' ) ),
							'total'     => $wp_query->max_num_pages,
							'prev_text' => esc_html__( '« Sebelumnya', 'webesia-wa-product-catalog' ),
							'next_text' => esc_html__( 'Selanjutnya »', 'webesia-wa-product-catalog' ),
							'type'      => 'list',
						] );

						if ( $pagination ) {
							echo $pagination;
						} else {
							// Force display for single page
							echo '<ul class="page-numbers">';
							echo '<li><span aria-current="page" class="page-numbers current">1</span></li>';
							echo '<li><span class="page-numbers next disabled" style="opacity:0.5; cursor:not-allowed;">' . esc_html__( 'Selanjutnya »', 'webesia-wa-product-catalog' ) . '</span></li>'; 
							echo '</ul>';
						}
						?>
					</div>

				<?php else : ?>
					<div class="wpwa-no-products">
						<p><?php esc_html_e( 'No products found with those criteria.', 'webesia-wa-product-catalog' ); ?></p>
						<a href="<?php echo esc_url( get_permalink( $shop_page_id ) ); ?>" class="btn-reset"><?php esc_html_e( 'Reset Filter', 'webesia-wa-product-catalog' ); ?></a>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</main>

<?php
get_footer();

