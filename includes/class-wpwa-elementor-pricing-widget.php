<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Product Pricing Elementor Widget
 */
class WPWA_Elementor_Product_Pricing extends \Elementor\Widget_Base {

	public function get_name() {
		return 'wpwa_product_pricing';
	}

	public function get_title() {
		return esc_html__( 'WA Pricing Product', 'webesia-wa-product-catalog' );
	}

	public function get_icon() {
		return 'eicon-product-price';
	}

	public function get_categories() {
		return [ 'webesia-wa-catalog' ];
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_price_style',
			[
				'label' => esc_html__( 'Price', 'webesia-wa-product-catalog' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'price_color',
			[
				'label' => esc_html__( 'Main Price Color', 'webesia-wa-product-catalog' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .wpwa-price-value' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'price_typography',
				'label' => esc_html__( 'Main Price Typography', 'webesia-wa-product-catalog' ),
				'selector' => '{{WRAPPER}} .wpwa-price-value',
			]
		);

		$this->add_control(
			'heading_sale_price',
			[
				'label' => esc_html__( 'Sale Price (Regular)', 'webesia-wa-product-catalog' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'sale_price_color',
			[
				'label' => esc_html__( 'Old Price Color', 'webesia-wa-product-catalog' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .wpwa-price-regular' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'sale_price_typography',
				'label' => esc_html__( 'Old Price Typography', 'webesia-wa-product-catalog' ),
				'selector' => '{{WRAPPER}} .wpwa-price-regular',
			]
		);

		$this->add_responsive_control(
			'align',
			[
				'label' => esc_html__( 'Alignment', 'webesia-wa-product-catalog' ),
				'type' => \Elementor\Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'webesia-wa-product-catalog' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'webesia-wa-product-catalog' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'webesia-wa-product-catalog' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}}' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'spacing',
			[
				'label' => esc_html__( 'Items Spacing', 'webesia-wa-product-catalog' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .wpwa-pricing-wrapper' => 'gap: {{SIZE}}{{UNIT}};',
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

        $price = get_post_meta( $product_id, '_product_price', true );
        $sale_price = get_post_meta( $product_id, '_product_sale_price', true );
        $has_sale = ! empty( $sale_price ) && floatval( $sale_price ) > 0;

        if ( empty( $price ) && empty( $sale_price ) && ! \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
            return;
        }

        echo '<div class="wpwa-pricing-wrapper">';
        
        if ( $has_sale ) {
            echo '<span class="wpwa-price-regular strikethrough">' . wpwa_format_price( $price ) . '</span> ';
            echo '<span class="wpwa-price-value">' . wpwa_format_price( $sale_price ) . '</span>';
        } else {
            // Placeholder if no price set in editor
            if ( empty( $price ) && \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
                echo '<span class="wpwa-price-value">' . wpwa_format_price( 150000 ) . '</span>';
            } else {
                echo '<span class="wpwa-price-value">' . wpwa_format_price( $price ) . '</span>';
            }
        }

        echo '</div>';
	}
}
