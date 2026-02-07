<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WA WebEsia Catalog Widget
 *
 * Adds a widget to display the product grid in sidebars or page builders that support legacy widgets.
 */
class WPWA_Product_Catalog_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct(
			'wpwa_product_catalog_widget', // Base ID
			esc_html__( 'WA WebEsia Catalog', 'webesia-wa-product-catalog' ), // Name
			[
				'description' => esc_html__( 'Displays your product catalog grid.', 'webesia-wa-product-catalog' ),
			]
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		echo $args['before_widget'];

		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
		}

		$limit    = ! empty( $instance['limit'] ) ? intval( $instance['limit'] ) : 9;
		$category = ! empty( $instance['category'] ) ? sanitize_text_field( $instance['category'] ) : '';
		$filter   = ! empty( $instance['show_filter'] ) ? 'yes' : 'no';

		// Use the existing shortcode logic to render the grid
		echo do_shortcode( '[toko posts_per_page="' . $limit . '" category="' . $category . '" filter="' . $filter . '"]' );

		echo $args['after_widget'];
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		$title       = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'Our Products', 'webesia-wa-product-catalog' );
		$limit       = ! empty( $instance['limit'] ) ? intval( $instance['limit'] ) : 9;
		$category    = ! empty( $instance['category'] ) ? $instance['category'] : '';
		$show_filter = ! empty( $instance['show_filter'] ) ? (bool) $instance['show_filter'] : false;

		$categories = get_terms( [
			'taxonomy' => 'product_category',
			'hide_empty' => false,
		] );
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:', 'webesia-wa-product-catalog' ); ?></label> 
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'limit' ) ); ?>"><?php esc_attr_e( 'Number of Products:', 'webesia-wa-product-catalog' ); ?></label> 
			<input class="tiny-text" id="<?php echo esc_attr( $this->get_field_id( 'limit' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'limit' ) ); ?>" type="number" step="1" min="1" value="<?php echo esc_attr( $limit ); ?>" size="3">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'category' ) ); ?>"><?php esc_attr_e( 'Category:', 'webesia-wa-product-catalog' ); ?></label> 
			<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'category' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'category' ) ); ?>">
				<option value=""><?php esc_html_e( 'All Categories', 'webesia-wa-product-catalog' ); ?></option>
				<?php if ( ! is_wp_error( $categories ) && ! empty( $categories ) ) : ?>
					<?php foreach ( $categories as $cat ) : ?>
						<option value="<?php echo esc_attr( $cat->slug ); ?>" <?php selected( $category, $cat->slug ); ?>>
							<?php echo esc_html( $cat->name ); ?>
						</option>
					<?php endforeach; ?>
				<?php endif; ?>
			</select>
		</p>
		<p>
			<input class="checkbox" type="checkbox" <?php checked( $show_filter ); ?> id="<?php echo esc_attr( $this->get_field_id( 'show_filter' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_filter' ) ); ?>" /> 
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_filter' ) ); ?>"><?php esc_attr_e( 'Show Filter (Sidebar)', 'webesia-wa-product-catalog' ); ?></label>
		</p>
		<?php 
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = [];
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? sanitize_text_field( $new_instance['title'] ) : '';
		$instance['limit'] = ( ! empty( $new_instance['limit'] ) ) ? intval( $new_instance['limit'] ) : 9;
		$instance['category'] = ( ! empty( $new_instance['category'] ) ) ? sanitize_text_field( $new_instance['category'] ) : '';
		$instance['show_filter'] = ( ! empty( $new_instance['show_filter'] ) ) ? (bool) $new_instance['show_filter'] : false;

		return $instance;
	}

}
