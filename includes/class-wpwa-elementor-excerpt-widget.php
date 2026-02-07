<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Product Short Description Elementor Widget
 */
class WPWA_Elementor_Product_Excerpt extends \Elementor\Widget_Base {

	public function get_name() {
		return 'wpwa_product_excerpt';
	}

	public function get_title() {
		return esc_html__( 'WA Short Description Product', 'webesia-wa-product-catalog' );
	}

	public function get_icon() {
		return 'eicon-post-excerpt';
	}

	public function get_categories() {
		return [ 'webesia-wa-catalog' ];
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_excerpt_style',
			[
				'label' => esc_html__( 'Description', 'webesia-wa-product-catalog' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'text_color',
			[
				'label' => esc_html__( 'Text Color', 'webesia-wa-product-catalog' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .wpwa-product-excerpt' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'typography',
				'selector' => '{{WRAPPER}} .wpwa-product-excerpt',
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
					'justify' => [
						'title' => esc_html__( 'Justified', 'webesia-wa-product-catalog' ),
						'icon' => 'eicon-text-align-justify',
					],
				],
				'selectors' => [
					'{{WRAPPER}}' => 'text-align: {{VALUE}};',
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

        // Get manual excerpt only to avoid messy auto-generated content from the_content
        $excerpt = get_post_field( 'post_excerpt', $product_id );

        if ( empty( $excerpt ) && \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
            $excerpt = 'Layanan desain kreatif, modern, dan sesuai identitas brand atau komunitas Anda. Dengan desain rapi dan penuh karakter, pakaian Anda tampil lebih unik...';
        }

        if ( empty( $excerpt ) ) {
            return;
        }

        echo '<div class="wpwa-product-excerpt">' . wp_kses_post( $excerpt ) . '</div>';
	}
}
