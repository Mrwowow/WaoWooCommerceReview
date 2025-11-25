<?php
if (!defined('ABSPATH')) {
    exit;
}

global $product;

$review_count = $product->get_review_count();
$average = $product->get_average_rating();
?>

<div class="wao-wcr-reviews-wrapper">
    <?php if (get_option('comments', 'open') == 'open') : ?>
        <div id="reviews" class="woocommerce-Reviews">
            <div id="comments">
                <h2 class="woocommerce-Reviews-title">
                    <?php
                    if ($review_count && wc_review_ratings_enabled()) {
                        printf(
                            esc_html(_n('%1$s review for %2$s', '%1$s reviews for %2$s', $review_count, 'wao-woocommerce-review')),
                            esc_html($review_count),
                            '<span>' . get_the_title() . '</span>'
                        );
                    } else {
                        _e('Reviews', 'wao-woocommerce-review');
                    }
                    ?>
                </h2>

                <?php if (have_comments()) : ?>
                    <ol class="commentlist">
                        <?php wp_list_comments(apply_filters('woocommerce_product_review_list_args', array('callback' => 'woocommerce_comments'))); ?>
                    </ol>

                    <?php
                    if (get_comment_pages_count() > 1 && get_option('page_comments')) :
                        echo '<nav class="woocommerce-pagination">';
                        paginate_comments_links(apply_filters('woocommerce_comment_pagination_args', array(
                            'prev_text' => '&larr;',
                            'next_text' => '&rarr;',
                            'type'      => 'list',
                        )));
                        echo '</nav>';
                    endif;
                    ?>
                <?php else : ?>
                    <p class="woocommerce-noreviews"><?php _e('There are no reviews yet.', 'wao-woocommerce-review'); ?></p>
                <?php endif; ?>
            </div>

            <?php if (get_option('woocommerce_review_rating_verification_required') === 'no' || wc_customer_bought_product('', get_current_user_id(), $product->get_id())) : ?>
                <div id="review_form_wrapper">
                    <div id="review_form">
                        <?php
                        $commenter = wp_get_current_commenter();

                        $comment_form = array(
                            'title_reply'          => have_comments() ? __('Add a review', 'wao-woocommerce-review') : sprintf(__('Be the first to review &ldquo;%s&rdquo;', 'wao-woocommerce-review'), get_the_title()),
                            'title_reply_to'       => __('Leave a Reply to %s', 'wao-woocommerce-review'),
                            'title_reply_before'   => '<span id="reply-title" class="comment-reply-title">',
                            'title_reply_after'    => '</span>',
                            'comment_notes_after'  => '',
                            'fields'               => array(
                                'author' => '<p class="comment-form-author">' . '<label for="author">' . esc_html__('Name', 'wao-woocommerce-review') . '&nbsp;<span class="required">*</span></label> ' .
                                            '<input id="author" name="author" type="text" value="' . esc_attr($commenter['comment_author']) . '" size="30" required /></p>',
                                'email'  => '<p class="comment-form-email"><label for="email">' . esc_html__('Email', 'wao-woocommerce-review') . '&nbsp;<span class="required">*</span></label> ' .
                                            '<input id="email" name="email" type="email" value="' . esc_attr($commenter['comment_author_email']) . '" size="30" required /></p>',
                            ),
                            'label_submit'  => __('Submit Review', 'wao-woocommerce-review'),
                            'logged_in_as'  => '',
                            'comment_field' => '',
                        );

                        $comment_form['comment_field'] = '<div class="comment-form-rating"><label for="rating">' . esc_html__('Your rating', 'wao-woocommerce-review') . (wc_review_ratings_required() ? '&nbsp;<span class="required">*</span>' : '') . '</label><select name="rating" id="rating" required>
                            <option value="">' . esc_html__('Rate&hellip;', 'wao-woocommerce-review') . '</option>
                            <option value="5">' . esc_html__('Perfect', 'wao-woocommerce-review') . '</option>
                            <option value="4">' . esc_html__('Good', 'wao-woocommerce-review') . '</option>
                            <option value="3">' . esc_html__('Average', 'wao-woocommerce-review') . '</option>
                            <option value="2">' . esc_html__('Not that bad', 'wao-woocommerce-review') . '</option>
                            <option value="1">' . esc_html__('Very poor', 'wao-woocommerce-review') . '</option>
                        </select></div>';

                        $comment_form['comment_field'] .= '<p class="comment-form-comment"><label for="comment">' . esc_html__('Your review', 'wao-woocommerce-review') . '&nbsp;<span class="required">*</span></label><textarea id="comment" name="comment" cols="45" rows="8" required></textarea></p>';

                        comment_form(apply_filters('woocommerce_product_review_comment_form_args', $comment_form));
                        ?>
                    </div>
                </div>
            <?php else : ?>
                <p class="woocommerce-verification-required"><?php _e('Only logged in customers who have purchased this product may leave a review.', 'wao-woocommerce-review'); ?></p>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
