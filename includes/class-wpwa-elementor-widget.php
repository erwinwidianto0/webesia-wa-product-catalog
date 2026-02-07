<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Elementor Widget for WA WebEsia Catalog
 */
class WPWA_Elementor_Product_Catalog extends \Elementor\Widget_Base {

	public function get_name() {
		return 'wpwa_product_catalog';
	}

	public function get_title() {
		return esc_html__( 'WA WebEsia Catalog', 'webesia-wa-product-catalog' );
	}

	public function get_icon() {
		return 'eicon-products';
	}

	public function get_categories() {
		return [ 'webesia-wa-catalog' ];
	}

	protected function register_controls() {

		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Settings', 'webesia-wa-product-catalog' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_responsive_control(
			'posts_per_page',
			[
				'label' => esc_html__( 'Product Limit', 'webesia-wa-product-catalog' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'min' => -1,
				'max' => 100,
				'step' => 1,
				'default' => 9,
			]
		);

        $this->add_responsive_control(
			'columns',
			[
				'label' => esc_html__( 'Columns', 'webesia-wa-product-catalog' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                    '5' => '5',
                    '6' => '6',
                ],
				'devices' => [ 'desktop', 'tablet', 'mobile' ],
                'desktop_default' => '3',
                'tablet_default' => '2',
                'mobile_default' => '1',
                'default' => '3',
			]
		);

		$categories = get_terms( [
			'taxonomy' => 'product_category',
			'hide_empty' => false,
		] );

		$cat_options = [ '' => esc_html__( 'All Categories', 'webesia-wa-product-catalog' ) ];
		if ( ! is_wp_error( $categories ) && ! empty( $categories ) ) {
			foreach ( $categories as $cat ) {
				$cat_options[ $cat->slug ] = $cat->name;
			}
		}

		$this->add_control(
			'category',
			[
				'label' => esc_html__( 'Category', 'webesia-wa-product-catalog' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => $cat_options,
				'default' => '',
			]
		);

		$this->add_control(
			'show_filter',
			[
				'label' => esc_html__( 'Show Filter (Sidebar)', 'webesia-wa-product-catalog' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'webesia-wa-product-catalog' ),
				'label_off' => esc_html__( 'Hide', 'webesia-wa-product-catalog' ),
				'return_value' => 'yes',
				'default' => '',
			]
		);

		$this->end_controls_section();

	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		// Determine Limit based on Responsive Settings and Device Context
		$limit = intval( $settings['posts_per_page'] ); // Desktop default

		if ( wp_is_mobile() ) {
			// Check Mobile first (most specific)
			if ( ! empty( $settings['posts_per_page_mobile'] ) ) {
				$limit = intval( $settings['posts_per_page_mobile'] );
			} 
			// Check Tablet fallback
			elseif ( ! empty( $settings['posts_per_page_tablet'] ) ) {
				$limit = intval( $settings['posts_per_page_tablet'] );
			}
		}

		$shortcode = sprintf(
			'[toko posts_per_page="%s" category="%s" filter="%s" columns="%s" columns_tablet="%s" columns_mobile="%s"]',
			$limit,
			esc_attr( $settings['category'] ),
			'yes' === $settings['show_filter'] ? 'yes' : 'no',
            $settings['columns'],
            ! empty( $settings['columns_tablet'] ) ? $settings['columns_tablet'] : $settings['columns'],
            ! empty( $settings['columns_mobile'] ) ? $settings['columns_mobile'] : ( ! empty( $settings['columns_tablet'] ) ? $settings['columns_tablet'] : $settings['columns'] )
		);

		echo do_shortcode( $shortcode );
	}

}
