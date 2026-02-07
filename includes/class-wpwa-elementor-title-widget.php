<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Product Title Elementor Widget
 */
class WPWA_Elementor_Product_Title extends \Elementor\Widget_Base {

	public function get_name() {
		return 'wpwa_product_title';
	}

	public function get_title() {
		return esc_html__( 'WA Product Title', 'webesia-wa-product-catalog' );
	}

	public function get_icon() {
		return 'eicon-post-title';
	}

	public function get_categories() {
		return [ 'webesia-wa-catalog' ];
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_title_style',
			[
				'label' => esc_html__( 'Title', 'webesia-wa-product-catalog' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => esc_html__( 'Text Color', 'webesia-wa-product-catalog' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .wpwa-product-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'typography',
				'selector' => '{{WRAPPER}} .wpwa-product-title',
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
				'default' => '',
				'selectors' => [
					'{{WRAPPER}}' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'header_tag',
			[
				'label' => esc_html__( 'HTML Tag', 'webesia-wa-product-catalog' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
					'div' => 'div',
					'span' => 'span',
					'p' => 'p',
				],
				'default' => 'h1',
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$title_tag = $settings['header_tag'];

		$title = '';
		$product_id = get_the_ID();

		if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
			// Editor Preview Logic
			if ( get_post_type( $product_id ) !== 'simple_product' ) {
				// Try to find a random product for preview
				$random_product = get_posts([
					'post_type' => 'simple_product',
					'posts_per_page' => 1,
					'orderby' => 'rand'
				]);
				if ( ! empty( $random_product ) ) {
					$title = get_the_title( $random_product[0]->ID );
				} else {
					$title = 'Contoh Judul Produk';
				}
			} else {
				$title = get_the_title();
			}
		} else {
			// Frontend Logic
			if ( is_singular( 'simple_product' ) ) {
				$title = get_the_title();
			} else {
				// Similar fallback logic to gallery/related widget if placed on non-product page
				$random_product = get_posts([
					'post_type' => 'simple_product',
					'posts_per_page' => 1,
					'orderby' => 'rand'
				]);
				if ( ! empty( $random_product ) ) {
					$title = get_the_title( $random_product[0]->ID );
				}
			}
		}

		if ( empty( $title ) ) {
			return;
		}

		printf( '<%1$s class="wpwa-product-title">%2$s</%1$s>', tag_escape( $title_tag ), esc_html( $title ) );
	}
}
