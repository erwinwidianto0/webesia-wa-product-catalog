<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Product Tabs Elementor Widget
 */
class WPWA_Elementor_Product_Tabs extends \Elementor\Widget_Base {

	public function get_name() {
		return 'wpwa_product_tabs';
	}

	public function get_title() {
		return esc_html__( 'WA Tab Product', 'webesia-wa-product-catalog' );
	}

	public function get_icon() {
		return 'eicon-tabs';
	}

	public function get_categories() {
		return [ 'webesia-wa-catalog' ];
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_tabs_style',
			[
				'label' => esc_html__( 'Tabs Styling', 'webesia-wa-product-catalog' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'active_tab_color',
			[
				'label' => esc_html__( 'Active Tab Color', 'webesia-wa-product-catalog' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .wpwa-tab-nav-item.active' => 'color: {{VALUE}}; border-bottom-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'tab_text_color',
			[
				'label' => esc_html__( 'Normal Tab Color', 'webesia-wa-product-catalog' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .wpwa-tab-nav-item' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'nav_typography',
				'label' => esc_html__( 'Tab Navigation Typography', 'webesia-wa-product-catalog' ),
				'selector' => '{{WRAPPER}} .wpwa-tab-nav-item',
			]
		);

		$this->add_control(
			'content_color',
			[
				'label' => esc_html__( 'Content Color', 'webesia-wa-product-catalog' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .wpwa-tab-pane' => 'color: {{VALUE}};',
				],
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'content_typography',
				'label' => esc_html__( 'Tab Content Typography', 'webesia-wa-product-catalog' ),
				'selector' => '{{WRAPPER}} .wpwa-tab-pane',
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
                } else {
                    echo '<div class="wpwa-alert">' . esc_html__( 'Add a product first to preview tabs.', 'webesia-wa-product-catalog' ) . '</div>';
                    return;
                }
            }
		}

		$main_content = get_post_field( 'post_content', $product_id );
		$custom_tabs  = get_post_meta( $product_id, '_product_tabs', true ) ?: [];
		$comments_count = get_comments_number( $product_id );

		// Prepare tabs
		$tabs = [];
		
		// 1. Deskripsi Tab (Only if content exists)
		if ( ! empty( $main_content ) ) {
            // Check if content is Elementor
            if ( \Elementor\Plugin::$instance->db->is_built_with_elementor( $product_id ) ) {
                $rendered_content = \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $product_id );
            } else {
                $rendered_content = apply_filters( 'the_content', $main_content );
            }

			$tabs['description'] = [
				'title' => esc_html__( 'Deskripsi', 'webesia-wa-product-catalog' ),
				'content' => $rendered_content,
			];
		}

		// 2. Custom Tabs
		foreach ( $custom_tabs as $tab ) {
			if ( ! empty( $tab['title'] ) ) {
				$tabs[ sanitize_title( $tab['title'] ) ] = [
					'title' => $tab['title'],
					'content' => apply_filters( 'the_content', $tab['content'] ),
				];
			}
		}

		// 3. Reviews Tab
        ob_start();
        ?>
        <div class="wpwa-reviews-tab-container">
            <div class="wpwa-reviews-summary">
                <?php 
                $avg_rating = function_exists('wpwa_get_average_rating') ? wpwa_get_average_rating( $product_id ) : 0;
                ?>
                <div class="wpwa-avg-rating">
                    <span class="wpwa-avg-number"><?php echo number_format( (float)$avg_rating, 1 ); ?></span>
                    <?php if ( function_exists('wpwa_display_star_rating') ) echo wpwa_display_star_rating( round($avg_rating) ); ?>
                    <span class="wpwa-total-text"><?php printf( esc_html__( '%d Reviews', 'webesia-wa-product-catalog' ), intval( $comments_count ) ); ?></span>
                </div>
            </div>

            <div class="wpwa-reviews-list-container">
                <?php 
                $comments = get_comments( [ 'post_id' => $product_id, 'status'  => 'approve' ] );
                if ( ! empty( $comments ) && function_exists('wpwa_review_callback') ) : ?>
                    <ul class="wpwa-reviews-list">
                        <?php wp_list_comments( [ 'callback' => 'wpwa_review_callback', 'type' => 'comment', 'avatar_size' => 48, 'style' => 'ul' ], $comments ); ?>
                    </ul>
                <?php else : ?>
                    <p class="wpwa-no-reviews"><?php esc_html_e( 'No reviews yet.', 'webesia-wa-product-catalog' ); ?></p>
                <?php endif; ?>
            </div>

            <?php if ( ! \Elementor\Plugin::$instance->editor->is_edit_mode() ) : ?>
                <div class="wpwa-review-form-wrapper">
                    <?php comment_form( [ 'title_reply' => esc_html__( 'Write Your Review', 'webesia-wa-product-catalog' ), 'label_submit' => esc_html__( 'Submit Review', 'webesia-wa-product-catalog' ) ], $product_id ); ?>
                </div>
            <?php else : ?>
                <p class="wpwa-editor-info" style="font-style: italic; color: #94a3b8; margin-top: 15px;">
                    <?php esc_html_e( 'Review form is hidden in editor preview.', 'webesia-wa-product-catalog' ); ?>
                </p>
            <?php endif; ?>
        </div>
        <?php
        $reviews_html = ob_get_clean();

		$tabs['reviews'] = [
			'title' => sprintf( esc_html__( 'Reviews (%d)', 'webesia-wa-product-catalog' ), $comments_count ),
			'content' => $reviews_html,
		];

		if ( empty( $tabs ) && ! \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
			return;
		}
		?>
		<div class="wpwa-product-tabs" id="wpwa-tabs-<?php echo esc_attr( $this->get_id() ); ?>">
			<ul class="wpwa-tab-nav">
				<?php 
				$first = true;
				foreach ( $tabs as $id => $tab ) : ?>
					<li class="wpwa-tab-nav-item <?php echo $first ? 'active' : ''; ?>" data-target="#wpwa-tab-<?php echo esc_attr( $id ); ?>-<?php echo esc_attr( $this->get_id() ); ?>">
						<?php echo esc_html( $tab['title'] ); ?>
					</li>
				<?php 
				$first = false;
				endforeach; ?>
			</ul>
			<div class="wpwa-tab-content">
				<?php 
				$first = true;
				foreach ( $tabs as $id => $tab ) : ?>
					<div id="wpwa-tab-<?php echo esc_attr( $id ); ?>-<?php echo esc_attr( $this->get_id() ); ?>" class="wpwa-tab-pane <?php echo $first ? 'active' : ''; ?>">
						<?php echo $tab['content']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</div>
				<?php 
				$first = false;
				endforeach; ?>
			</div>
		</div>
		<script>
		(function() {
			const widgetId = '<?php echo esc_js( $this->get_id() ); ?>';
			const container = document.getElementById('wpwa-tabs-' + widgetId);
			if (!container) return;

			container.querySelectorAll('.wpwa-tab-nav-item').forEach(item => {
				item.addEventListener('click', function() {
					container.querySelectorAll('.wpwa-tab-nav-item').forEach(i => i.classList.remove('active'));
					container.querySelectorAll('.wpwa-tab-pane').forEach(i => i.classList.remove('active'));
					
					this.classList.add('active');
					const target = container.querySelector(this.dataset.target);
					if (target) target.classList.add('active');
				});
			});
		})();
		</script>
		<?php
	}
}
