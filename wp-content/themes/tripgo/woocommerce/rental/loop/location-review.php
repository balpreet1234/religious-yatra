<?php
/**
 * @package    Tripgo by ovatheme
 * @author     Ovatheme
 * @copyright  Copyright (C) 2022 Ovatheme All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 */

if ( !defined( 'ABSPATH' ) ) exit();

$all_ids = ovabrw_get_all_id_product();

if( isset( $args['id'] ) && $args['id'] != '' ) {

    $product_id     = ( in_array( $args['id'], $all_ids ) == true ) ? $args['id'] : get_the_id();

} elseif( in_array( get_the_id(), $all_ids ) == false ) {

    $product_id     = $all_ids[0];

} else {
    $product_id     = get_the_id();
}

$product = wc_get_product($product_id);

if ( ! $product ) return;

$address        = get_post_meta( $product_id, 'ovabrw_address', true );
$review_count   = $product->get_review_count();
$rating         = $product->get_average_rating();

// Wishlist
$wishlist = do_shortcode('[yith_wcwl_add_to_wishlist]');

?>

<div class="ova-location-review">
    <?php if ( $address ): ?>
        <div class="ova-product-location">
            <i aria-hidden="true" class="icomoon icomoon-location-2"></i>
            <a href="#ova-tour-map">
                <?php echo esc_html( $address ); ?>
            </a>
        </div>
    <?php endif; ?>
    <?php if ( wc_review_ratings_enabled() && $rating > 0 ): ?>
        <div class="ova-product-review">
            <div class="star-rating" role="img" aria-label="<?php echo sprintf( __( 'Rated %s out of 5', 'tripgo' ), $rating ); ?>">
                <span class="rating-percent" style="width: <?php echo esc_attr( ( $rating / 5 ) * 100 ).'%'; ?>;"></span>
            </div>
            <a href="#reviews" class="woo-review-link" rel="nofollow">
                ( <?php printf( _n( '%s review', '%s reviews', $review_count, 'tripgo' ), esc_html( $review_count ) ); ?> )
            </a>
        </div>
    <?php endif; ?>
    <?php if ( '[yith_wcwl_add_to_wishlist]' != $wishlist ): ?>
        <div class="ova-single-product-wishlist">
            <?php echo do_shortcode('[yith_wcwl_add_to_wishlist]'); ?>
        </div>
    <?php endif; ?>
</div>