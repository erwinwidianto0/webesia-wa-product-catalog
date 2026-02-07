<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Related Products Widget
 */
class WPWA_Related_Products_Widget extends WP_Widget {

	public function __construct() {
		parent::__construct(
			'wpwa_related_products',
			esc_html__( 'WA Related Products', 'webesia-wa-product-catalog' ),
			[ 'description' => esc_html__( 'Menampilkan produk terkait berdasarkan kategori produk yang sedang dilihat.', 'webesia-wa-product-catalog' ) ]
		);
	}

	public function widget( $args, $instance ) {
		if ( ! is_singular( 'simple_product' ) ) {
			return;
		}

		$title = ! empty( $instance['title'] ) ? $instance['title'] : '';
		$limit = ! empty( $instance['limit'] ) ? absint( $instance['limit'] ) : 4;

		echo $args['before_widget'];

		if ( ! empty( $title ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $title ) . $args['after_title'];
		}

		if ( function_exists( 'wpwa_get_related_products_html' ) ) {
			// Remove the section title from helper because widget has its own title
			$html = wpwa_get_related_products_html( get_the_ID(), $limit );
			// Strip the h2 title from the helper output if it exists
			$html = preg_replace( '/<h2 class="wpwa-section-title">.*?<\/h2>/is', '', $html );
			echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		echo $args['after_widget'];
	}

	public function form( $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'Produk Terkait', 'webesia-wa-product-catalog' );
		$limit = ! empty( $instance['limit'] ) ? absint( $instance['limit'] ) : 4;
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Judul:', 'webesia-wa-product-catalog' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'limit' ) ); ?>"><?php esc_html_e( 'Jumlah Produk:', 'webesia-wa-product-catalog' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'limit' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'limit' ) ); ?>" type="number" step="1" min="1" value="<?php echo esc_attr( $limit ); ?>">
		</p>
		<?php
	}

	public function update( $new_instance, $old_instance ) {
		$instance = [];
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? sanitize_text_field( $new_instance['title'] ) : '';
		$instance['limit'] = ( ! empty( $new_instance['limit'] ) ) ? absint( $new_instance['limit'] ) : 4;
		return $instance;
	}
}
