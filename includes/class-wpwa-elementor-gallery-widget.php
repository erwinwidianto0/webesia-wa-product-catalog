<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Product Gallery Elementor Widget
 */
class WPWA_Elementor_Product_Gallery extends \Elementor\Widget_Base {

	public function get_name() {
		return 'wpwa_product_gallery';
	}

	public function get_title() {
		return esc_html__( 'WA Product Gallery', 'webesia-wa-product-catalog' );
	}

	public function get_icon() {
		return 'eicon-gallery-grid';
	}

	public function get_categories() {
		return [ 'general' ];
	}

	protected function register_controls() {
		// No controls needed for now as it pulls from product context
		$this->start_controls_section(
			'section_content',
			[
				'label' => esc_html__( 'Content', 'webesia-wa-product-catalog' ),
			]
		);

		$this->add_control(
			'info_msg',
			[
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw' => esc_html__( 'This widget displays the main image and gallery of the current product.', 'webesia-wa-product-catalog' ),
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		// Show preview in editor if needed
		if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
			$this->render_editor_preview();
			return;
		}

		$product_id = get_the_ID();
		
		// If not singular product, try to find one in loop or fallback
		if ( ! is_singular( 'simple_product' ) ) {
            // Optional: You could fetch a random product here too for frontend display on non-product pages
            // But usually gallery is context specific. 
            // For now, let's render empty or maybe random if user insists?
            // The previous requirement for "Related Products" was to show random.
            // Let's mirror that logic for consistency.
            $random_product = get_posts([
                'post_type' => 'simple_product',
                'posts_per_page' => 1,
                'orderby' => 'rand'
            ]);
            
            if ( ! empty( $random_product ) ) {
                $product_id = $random_product[0]->ID;
            } else {
                return;
            }
		}

		if ( function_exists( 'wpwa_get_product_gallery_html' ) ) {
			echo wpwa_get_product_gallery_html( $product_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}

	protected function render_editor_preview() {
		$product_id = get_the_ID();
        
        // If current page is not a product, fetch a random one for preview
        if ( get_post_type( $product_id ) !== 'simple_product' ) {
            $random_product = get_posts([
                'post_type' => 'simple_product',
                'posts_per_page' => 1,
                'orderby' => 'rand'
            ]);
            
            if ( ! empty( $random_product ) ) {
                $product_id = $random_product[0]->ID;
            } else {
                echo '<div class="wpwa-alert">' . esc_html__( 'No products found for preview.', 'webesia-wa-product-catalog' ) . '</div>';
                return;
            }
        }

		echo '<div class="wpwa-gallery-preview-wrapper" style="pointer-events: none;">'; // Disable interaction in editor to prevent lightbox issues
		if ( function_exists( 'wpwa_get_product_gallery_html' ) ) {
			echo wpwa_get_product_gallery_html( $product_id ); 
		}
		echo '</div>';
	}
}
