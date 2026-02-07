<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Related Products Elementor Widget
 */
class WPWA_Elementor_Related_Products extends \Elementor\Widget_Base {

	public function get_name() {
		return 'wpwa_related_products';
	}

	public function get_title() {
		return esc_html__( 'WA Related Products', 'webesia-wa-product-catalog' );
	}

	public function get_icon() {
		return 'eicon-post-list';
	}

	public function get_categories() {
		return [ 'general' ];
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_content',
			[
				'label' => esc_html__( 'Content', 'webesia-wa-product-catalog' ),
			]
		);

		$this->add_control(
			'limit',
			[
				'label' => esc_html__( 'Number of Products', 'webesia-wa-product-catalog' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 12,
				'default' => 4,
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		// Show placeholder in editor if not a single product page
		if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
			$this->render_editor_preview();
			return;
		}


		// Removed strict check to allow widget on any page (fallback to random)
		// if ( ! is_singular( 'simple_product' ) ) { return; }

		$settings = $this->get_settings_for_display();
		$limit = $settings['limit'];
		$product_id = get_the_ID();

		if ( function_exists( 'wpwa_get_related_products_html' ) ) {
			echo wpwa_get_related_products_html( $product_id, $limit ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}

	protected function render_editor_preview() {
		$settings = $this->get_settings_for_display();
		$limit = $settings['limit'];

		// Query random products for preview
		$args = [
			'post_type'      => 'simple_product',
			'posts_per_page' => $limit,
			'orderby'        => 'rand',
		];

		$query = new \WP_Query( $args );

		if ( ! $query->have_posts() ) {
			echo '<div class="wpwa-alert">' . esc_html__( 'No products found for preview.', 'webesia-wa-product-catalog' ) . '</div>';
			return;
		}

		?>
		<div class="wpwa-related-products-preview">
			<h2 class="wpwa-section-title"><?php esc_html_e( 'Produk Terkait (Preview)', 'webesia-wa-product-catalog' ); ?></h2>
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
	}
}
