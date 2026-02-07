<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Product CTA Elementor Widget
 */
class WPWA_Elementor_Product_CTA extends \Elementor\Widget_Base {

	public function get_name() {
		return 'wpwa_product_cta';
	}

	public function get_title() {
		return esc_html__( 'WA CTA Product', 'webesia-wa-product-catalog' );
	}

	public function get_icon() {
		return 'eicon-button';
	}

	public function get_categories() {
		return [ 'webesia-wa-catalog' ];
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_button',
			[
				'label' => esc_html__( 'Button', 'webesia-wa-product-catalog' ),
			]
		);

		$this->add_control(
			'button_text',
			[
				'label' => esc_html__( 'Button Text', 'webesia-wa-product-catalog' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => get_option( 'wpwa_button_text', 'Order via WhatsApp' ),
				'placeholder' => esc_html__( 'Order via WhatsApp', 'webesia-wa-product-catalog' ),
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
				'prefix_class' => 'elementor-align-',
				'default' => 'left',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style',
			[
				'label' => esc_html__( 'Style', 'webesia-wa-product-catalog' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'button_background_color',
			[
				'label' => esc_html__( 'Background Color', 'webesia-wa-product-catalog' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .wpwa-cta-btn' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_text_color',
			[
				'label' => esc_html__( 'Text Color', 'webesia-wa-product-catalog' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .wpwa-cta-btn' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'typography',
				'selector' => '{{WRAPPER}} .wpwa-cta-btn',
			]
		);

		$this->add_responsive_control(
			'padding',
			[
				'label' => esc_html__( 'Padding', 'webesia-wa-product-catalog' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .wpwa-cta-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'webesia-wa-product-catalog' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .wpwa-cta-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
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
        $display_price = $has_sale ? $sale_price : $price;
        $min_order = get_post_meta( $product_id, '_product_min_order', true ) ?: 1;
        $product_title = get_the_title( $product_id );

        if ( empty( $product_title ) && \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
            $product_title = 'Sample Product';
        }
        ?>
        <div class="wpwa-cta-wrapper">
            <button class="wpwa-cta-btn wpwa-trigger-order" 
                    data-id="<?php echo esc_attr( $product_id ); ?>" 
                    data-name="<?php echo esc_attr( $product_title ); ?>" 
                    data-price="<?php echo esc_attr( number_format( (float)$display_price, 0, '', '' ) ); ?>"
                    data-min="<?php echo esc_attr( $min_order ); ?>">
                <svg viewBox="0 0 24 24" width="22" height="22" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                <span><?php echo esc_html( __($settings['button_text'], 'webesia-wa-product-catalog') ); ?></span>
            </button>
        </div>
        <?php
	}
}
