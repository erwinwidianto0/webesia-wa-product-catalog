<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Breadcrumb Elementor Widget
 */
class WPWA_Elementor_Breadcrumb extends \Elementor\Widget_Base {

	public function get_name() {
		return 'wpwa_breadcrumb';
	}

	public function get_title() {
		return esc_html__( 'WA Breadcrumb', 'webesia-wa-product-catalog' );
	}

	public function get_icon() {
		return 'eicon-product-breadcrumbs';
	}

	public function get_categories() {
		return [ 'webesia-wa-catalog' ];
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_breadcrumb_style',
			[
				'label' => esc_html__( 'Breadcrumb', 'webesia-wa-product-catalog' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'text_color',
			[
				'label' => esc_html__( 'Text Color', 'webesia-wa-product-catalog' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .wpwa-breadcrumb' => 'color: {{VALUE}};',
					'{{WRAPPER}} .wpwa-breadcrumb a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'link_hover_color',
			[
				'label' => esc_html__( 'Link Hover Color', 'webesia-wa-product-catalog' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .wpwa-breadcrumb a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'typography',
				'selector' => '{{WRAPPER}} .wpwa-breadcrumb',
			]
		);

		$this->add_control(
			'separator_color',
			[
				'label' => esc_html__( 'Separator Color', 'webesia-wa-product-catalog' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .wpwa-breadcrumb-separator' => 'color: {{VALUE}};',
				],
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

		$this->end_controls_section();
	}

	protected function render() {
		if ( function_exists( 'wpwa_get_breadcrumb_html' ) ) {
            // If in editor and not on product page, it might return empty.
            // Let's provide a dummy preview if so.
            $html = wpwa_get_breadcrumb_html();
            
            if ( empty( $html ) && \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
                echo '<nav class="wpwa-breadcrumb preview-mode">';
                echo '<a>' . esc_html__( 'Home', 'webesia-wa-product-catalog' ) . '</a>';
                echo ' <span class="wpwa-breadcrumb-separator">/</span> ';
                echo '<a>' . esc_html__( 'Kategori', 'webesia-wa-product-catalog' ) . '</a>';
                echo ' <span class="wpwa-breadcrumb-separator">/</span> ';
                echo '<span class="wpwa-breadcrumb-current">' . esc_html__( 'Nama Produk', 'webesia-wa-product-catalog' ) . '</span>';
                echo '</nav>';
            } else {
                echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            }
		}
	}
}
