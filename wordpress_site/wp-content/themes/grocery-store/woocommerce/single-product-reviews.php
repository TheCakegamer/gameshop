<?php
/**
 * Display single product reviews (comments)
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product-reviews.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 4.6.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( ! comments_open() ) {
	return;
}

?>
<div id="reviews" class="woocommerce-Reviews">
	<div id="comments" class="comments-area clearfix details-page-inner-box comment-meta" >
 
		<h3 class="comments-title">
			<?php
			$count = $product->get_review_count();
			if ( $count && wc_review_ratings_enabled() ) {
				/* translators: 1: reviews count 2: product name */
				$reviews_title = sprintf( esc_html( _n( '%1$s review for %2$s', '%1$s reviews for %2$s', $count, 'grocery-store' ) ), esc_html( $count ), '<span>' . get_the_title() . '</span>' );
				echo apply_filters( 'woocommerce_reviews_title', $reviews_title, $count, $product ); // WPCS: XSS ok.
			} else {
				esc_html_e( 'Reviews', 'grocery-store' );
			}
			?>
		</h3>

		<?php if ( have_comments() ) : ?>
        
			<ul class="comment-list">
				<?php wp_list_comments( apply_filters( 'woocommerce_product_review_list_args', array( 'callback' => 'woocommerce_comments' ) ) ); ?>
			</ul>

			<?php
			if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) :
				echo '<nav class="woocommerce-pagination">';
				paginate_comments_links(
					apply_filters(
						'woocommerce_comment_pagination_args',
						array(
							'prev_text' => '&larr;',
							'next_text' => '&rarr;',
							'type'      => 'list',
						)
					)
				);
				echo '</nav>';
			endif;
			?>
			<div class="space-margin"></div>
		<?php else : ?>
			<div class="woocommerce-message alert alert-info"><?php esc_html_e( 'There are no reviews yet.', 'grocery-store' ); ?></div>
		<?php endif; ?>
	</div>

	<?php if ( get_option( 'woocommerce_review_rating_verification_required' ) === 'no' || wc_customer_bought_product( '', get_current_user_id(), $product->get_id() ) ) : ?>
		<div id="review_form_wrapper">
			<div id="review_form" class="comment-form row">
				<?php
					
				$commenter    = wp_get_current_commenter();
				$comment_form = array(
					/* translators: %s is product title */
					'title_reply'         => '',
					/* translators: %s is product title */
					'title_reply_to'      => '',
					'title_reply_before'  => '',
					'title_reply_after'   => '',
					'comment_notes_after' => '',
					'logged_in_as'        => '',
					'comment_field'       => '',
					'submit_button' => '<button type="submit" class="theme-btn inverted" id="submit-new"><span>'. esc_html__( 'Submit', 'grocery-store' ) .'</span></button>',
					'class_form'      => 'row col-12',
				);

				$name_email_required = (bool) get_option( 'require_name_email', 1 );
				$fields              = array(
					'author' => array(
						'label'    => __( 'Name', 'grocery-store' ),
						'type'     => 'text',
						'value'    => $commenter['comment_author'],
						'required' => $name_email_required,
					),
					'email' => array(
						'label'    => __( 'Email', 'grocery-store' ),
						'type'     => 'email',
						'value'    => $commenter['comment_author_email'],
						'required' => $name_email_required,
					),
					
					
				);

				$comment_form['fields'] = array();

				foreach ( $fields as $key => $field ) {
					$field_html  = '<div class="col-xl-6 col-lg-6 col-md-4 col-12 comment-form-' . esc_attr( $key ) . '"> ';
					

					if ( $field['required'] ) {
						$field_html .= '<span class="required">*</span>';
					}

					$field_html .= '<input placeholder="'.esc_html( $field['label'] ).'" id="' . esc_attr( $key ) . '" name="' . esc_attr( $key ) . '" type="' . esc_attr( $field['type'] ) . '" value="' . esc_attr( $field['value'] ) . '" size="30" ' . ( $field['required'] ? 'required' : '' ) . ' /></div>';

					$comment_form['fields'][ $key ] = $field_html;
				}

				$account_page_url = wc_get_page_permalink( 'myaccount' );
				if ( $account_page_url ) {
					/* translators: %s opening and closing link tags respectively */
					$comment_form['must_log_in'] = '<p class="must-log-in">' . sprintf( esc_html__( 'You must be %1$slogged in%2$s to post a review.', 'grocery-store' ), '<a href="' . esc_url( $account_page_url ) . '">', '</a>' ) . '</p>';
				}

				if ( wc_review_ratings_enabled() ) {
					$comment_form['comment_field'] = '<div class="comment-form-rating col-12"><label for="rating">' . esc_html__( 'Your rating', 'grocery-store' ) . '</label><select name="rating" id="rating" required>
						<option value="">' . esc_html__( 'Rate&hellip;', 'grocery-store' ) . '</option>
						<option value="5">' . esc_html__( 'Perfect', 'grocery-store' ) . '</option>
						<option value="4">' . esc_html__( 'Good', 'grocery-store' ) . '</option>
						<option value="3">' . esc_html__( 'Average', 'grocery-store' ) . '</option>
						<option value="2">' . esc_html__( 'Not that bad', 'grocery-store' ) . '</option>
						<option value="1">' . esc_html__( 'Very poor', 'grocery-store' ) . '</option>
					</select><div class="clearfix"></div></div>';
				}

				$comment_form['comment_field'] .= '<div class="comment-form-comment col-12"><span class="required">*</span><textarea id="comment" name="comment" cols="45" rows="8" required placeholder="'.esc_html__( 'Your review', 'grocery-store' ) .'"></textarea></div>';
				
			
				
				comment_form( apply_filters( 'woocommerce_product_review_comment_form_args', $comment_form ) );
				?>
			</div>
		</div>
	<?php else : ?>
		<div class="woocommerce-message alert alert-danger"><?php esc_html_e( 'Only logged in customers who have purchased this product may leave a review.', 'grocery-store' ); ?></div>
	<?php endif; ?>

	<div class="clear"></div>
</div>
