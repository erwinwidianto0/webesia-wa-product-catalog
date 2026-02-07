<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Force comments open for simple_product
 */
add_filter( 'comments_open', 'wpwa_force_comments_open', 10, 2 );
function wpwa_force_comments_open( $open, $post_id ) {
	if ( get_post_type( $post_id ) === 'simple_product' ) {
		return true;
	}
	return $open;
}

/**
 * Disable duplicate comment check for simple_product
 */
add_filter( 'duplicate_comment_id', 'wpwa_disable_duplicate_review_check', 10, 2 );
function wpwa_disable_duplicate_review_check( $dupe_id, $comment_data ) {
	if ( get_post_type( $comment_data['comment_post_ID'] ) === 'simple_product' ) {
		return false;
	}
	return $dupe_id;
}

/**
 * Save rating provided with comment
 */
add_action( 'comment_post', 'wpwa_save_comment_rating' );
function wpwa_save_comment_rating( $comment_id ) {
	// Note: WordPress already verifies nonce before calling comment_post action
	// So we don't need additional nonce verification here
	if ( ( isset( $_POST['rating'] ) ) && ( $_POST['rating'] !== '' ) ) {
		$rating = intval( wp_unslash( $_POST['rating'] ) );
		add_comment_meta( $comment_id, 'rating', $rating );
	}
}

/**
 * Add rating field to comment form
 */
add_filter( 'comment_form_logged_in_after', 'wpwa_add_rating_field' );
add_filter( 'comment_form_after_fields', 'wpwa_add_rating_field' );
function wpwa_add_rating_field() {
	if ( get_post_type() !== 'simple_product' ) {
		return;
	}
	?>
	<div class="wpwa-rating-selector-wrapper">
		<label for="rating"><?php esc_html_e( 'Your Rating', 'webesia-wa-product-catalog' ); ?><span class="required">*</span></label>
		<div class="wpwa-star-rating-selector">
			<?php for ( $i = 5; $i >= 1; $i-- ) : ?>
				<input type="radio" id="star<?php echo intval( $i ); ?>" name="rating" value="<?php echo intval( $i ); ?>" required />
				<label for="star<?php echo intval( $i ); ?>" title="<?php echo intval( $i ); ?> stars">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-star"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
				</label>
			<?php endfor; ?>
		</div>
	</div>
	<?php
}

/**
 * Average Rating Calculation
 */
function wpwa_get_average_rating( $post_id ) {
	$comments = get_comments( [
		'post_id' => $post_id,
		'status'  => 'approve'
	] );

	if ( empty( $comments ) ) {
		return 0;
	}

	$total_rating = 0;
	$rating_count = 0;

	foreach ( $comments as $comment ) {
		$rating = get_comment_meta( $comment->comment_ID, 'rating', true );
		if ( $rating ) {
			$total_rating += intval( $rating );
			$rating_count++;
		}
	}

	if ( $rating_count === 0 ) {
		return 0;
	}

	return round( $total_rating / $rating_count, 1 );
}

/**
 * Get Rating Summary (Average and Count)
 */
function wpwa_get_rating_summary( $post_id ) {
	$comments = get_comments( [
		'post_id' => $post_id,
		'status'  => 'approve'
	] );

	if ( empty( $comments ) ) {
		return [ 'average' => 0, 'count' => 0 ];
	}

	$total_rating = 0;
	$rating_count = 0;

	foreach ( $comments as $comment ) {
		$rating = get_comment_meta( $comment->comment_ID, 'rating', true );
		if ( $rating ) {
			$total_rating += intval( $rating );
			$rating_count++;
		}
	}

	if ( $rating_count === 0 ) {
		return [ 'average' => 0, 'count' => 0 ];
	}

	return [
		'average' => round( $total_rating / $rating_count, 1 ),
		'count'   => $rating_count
	];
}

/**
 * Display Star Rating HTML
 */
function wpwa_display_star_rating( $rating ) {
	$html = '<div class="wpwa-star-rating-display">';
	for ( $i = 1; $i <= 5; $i++ ) {
		$fill = $i <= $rating ? 'currentColor' : 'none';
		$class = $i <= $rating ? 'filled' : 'empty';
		$html .= '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="' . $fill . '" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-star ' . $class . '"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>';
	}
	$html .= '</div>';
	return $html;
}

/**
 * Custom Comment Callback for Reviews
 */
function wpwa_review_callback( $comment, $args, $depth ) {
	$rating = get_comment_meta( $comment->comment_ID, 'rating', true );
	?>
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
		<div id="comment-<?php comment_ID(); ?>" class="wpwa-review-item">
			<div class="wpwa-review-header">
				<div class="wpwa-review-avatar">
					<?php echo get_avatar( $comment, 48 ); ?>
				</div>
				<div class="wpwa-review-meta">
					<span class="wpwa-review-author"><?php echo esc_html( get_comment_author() ); ?></span>
					<span class="wpwa-review-date"><?php printf( /* translators: %s: time difference */ esc_html__( '%s ago', 'webesia-wa-product-catalog' ), esc_html( human_time_diff( get_comment_date( 'U' ), current_time( 'timestamp' ) ) ) ); ?></span>
					<?php if ( $rating ) : ?>
						<?php echo wpwa_display_star_rating( $rating ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<?php endif; ?>
				</div>
			</div>
			<div class="wpwa-review-content">
				<?php comment_text(); ?>
			</div>
		</div>
	<?php
}
