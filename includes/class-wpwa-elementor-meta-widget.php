<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Product Meta Elementor Widget
 */
class WPWA_Elementor_Product_Meta extends \Elementor\Widget_Base {

	public function get_name() {
		return 'wpwa_product_meta';
	}

	public function get_title() {
		return esc_html__( 'WA Meta Product', 'webesia-wa-product-catalog' );
	}

	public function get_icon() {
		return 'eicon-product-meta';
	}

	public function get_categories() {
		return [ 'webesia-wa-catalog' ];
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_meta_style',
			[
				'label' => esc_html__( 'Meta Data', 'webesia-wa-product-catalog' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'label_color',
			[
				'label' => esc_html__( 'Label Color', 'webesia-wa-product-catalog' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .wpwa-meta-label' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'value_color',
			[
				'label' => esc_html__( 'Value Color', 'webesia-wa-product-catalog' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .wpwa-meta-value' => 'color: {{VALUE}};',
					'{{WRAPPER}} .wpwa-meta-value a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'typography',
				'selector' => '{{WRAPPER}} .wpwa-product-meta-item',
			]
		);

		$this->add_control(
			'spacing',
			[
				'label' => esc_html__( 'Spacing', 'webesia-wa-product-catalog' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .wpwa-product-meta-item:not(:last-child)' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$product_id = get_the_ID();
		
		if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
            if ( get_post_type( $product_id ) !== 'simple_product' ) {
                $random_product = get_posts([
                    'post_type' => 'simple_product',
                    'posts_per_page' => 1,
                    'orderby' => 'rand'
                ]);
                if ( ! empty( $random_product ) ) {
                    $product_id = $random_product[0]->ID;
                }
            }
		}

        $sku = get_post_meta( $product_id, '_product_sku', true );
        $terms = get_the_terms( $product_id, 'product_category' );
        
        echo '<div class="wpwa-product-meta-wrapper">';
        
        // Category
        if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
            $cat_links = [];
            foreach ( $terms as $term ) {
                $cat_links[] = '<a href="' . esc_url( get_term_link( $term ) ) . '">' . esc_html( $term->name ) . '</a>';
            }
            echo '<div class="wpwa-product-meta-item">';
            echo '<span class="wpwa-meta-label">' . esc_html__( 'Category:', 'webesia-wa-product-catalog' ) . '</span> ';
            echo '<span class="wpwa-meta-value">' . implode( ', ', $cat_links ) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            echo '</div>';
        }

        // SKU
        if ( ! empty( $sku ) ) {
            echo '<div class="wpwa-product-meta-item">';
            echo '<span class="wpwa-meta-label">' . esc_html__( 'SKU:', 'webesia-wa-product-catalog' ) . '</span> ';
            echo '<span class="wpwa-meta-value">' . esc_html( $sku ) . '</span>';
            echo '</div>';
        }

        if ( empty( $sku ) && empty( $terms ) && \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
            echo '<div class="wpwa-alert">' . esc_html__( 'No meta data found.', 'webesia-wa-product-catalog' ) . '</div>';
        }

        echo '</div>';
	}
}
