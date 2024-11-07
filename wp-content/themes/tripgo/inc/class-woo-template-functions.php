<?php
/**
 * @package    Tripgo by ovatheme
 * @author     Ovatheme
 * @copyright  Copyright (C) 2022 Ovatheme All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 */

if ( !defined( 'ABSPATH' ) ) exit();

/* Single Product */
if ( ! function_exists( 'tripgo_wc_before_main_content' ) ) {
    function tripgo_wc_before_main_content() {
        echo '<div class="ova-single-product">';
    }
}

if ( ! function_exists( 'tripgo_wc_after_main_content' ) ) {
    function tripgo_wc_after_main_content() {
        echo '</div>';
    }
}

/* Header */
if ( ! function_exists( 'tripgo_wc_before_single_product_header' ) ) {
    function tripgo_wc_before_single_product_header() {
        echo '<div class="row_site">
                <div class="container_site">';
    }
}

if ( ! function_exists( 'tripgo_wc_after_single_product_header' ) ) {
    function tripgo_wc_after_single_product_header() {
        echo '</div>
                </div>';
    }
}

/* Top Header */
if ( ! function_exists( 'tripgo_wc_before_single_product_top_header' ) ) {
    function tripgo_wc_before_single_product_top_header() {
        echo '<div class="single-product-top-header">';
    }
}

if ( ! function_exists( 'tripgo_wc_after_single_product_top_header' ) ) {
    function tripgo_wc_after_single_product_top_header() {
        echo '</div>';
    }
}

/* Product Summary Left */
if ( ! function_exists( 'tripgo_wc_before_single_product_summary_left' ) ) {
    function tripgo_wc_before_single_product_summary_left() {
        echo '<div class="ova-single-product-summary-left">';
    }
}

if ( ! function_exists( 'tripgo_wc_after_single_product_summary_left' ) ) {
    function tripgo_wc_after_single_product_summary_left() {
        echo '</div>';
    }
}

/* Product Summary Right */
if ( ! function_exists( 'tripgo_wc_before_single_product_summary_right' ) ) {
    function tripgo_wc_before_single_product_summary_right() {
        echo '<div class="ova-single-product-summary-right">';
    }
}

if ( ! function_exists( 'tripgo_wc_after_single_product_summary_right' ) ) {
    function tripgo_wc_after_single_product_summary_right() {
        echo '</div>';
    }
}

/* Title */
if ( ! function_exists( 'tripgo_wc_template_single_title' ) ) {
    function tripgo_wc_template_single_title() {
        wc_get_template( 'rental/loop/title.php' );
    }
}

/* Location and Review */
if ( ! function_exists( 'tripgo_wc_template_single_location_review' ) ) {
    function tripgo_wc_template_single_location_review() {
        wc_get_template( 'rental/loop/location-review.php' );
    }
}

/* Video */
if ( ! function_exists( 'tripgo_wc_template_single_video_gallery' ) ) {
    function tripgo_wc_template_single_video_gallery() {
        wc_get_template( 'rental/loop/video-gallery.php' );
    }
}

/* Gallery Slideshow */
if ( ! function_exists( 'tripgo_wc_template_single_slideshow' ) ) {
    function tripgo_wc_template_single_slideshow() {
        wc_get_template( 'rental/loop/gallery-slideshow.php' );
    }
}

/* Features */
if ( ! function_exists( 'tripgo_wc_template_single_features' ) ) {
    function tripgo_wc_template_single_features() {
        wc_get_template( 'rental/loop/features.php' );
    }
}

/* Content */
if ( ! function_exists( 'tripgo_wc_before_single_product_content' ) ) {
    function tripgo_wc_before_single_product_content() {
        echo '<div class="row_site">
                <div class="container_site">';
    }
}

if ( ! function_exists( 'tripgo_wc_after_single_product_content' ) ) {
    function tripgo_wc_after_single_product_content() {
        echo '</div>
                </div>';
    }
}

/* Related */
if ( ! function_exists( 'tripgo_wc_template_single_product_related' ) ) {
    function tripgo_wc_template_single_product_related() {
        wc_get_template( 'rental/loop/related.php' );
    }
}


/* Description */
if ( ! function_exists( 'tripgo_wc_template_single_content' ) ) {
    function tripgo_wc_template_single_content() {
        wc_get_template( 'rental/loop/content.php' );
    }
}

/* Included/Excluded */
if ( ! function_exists( 'tripgo_wc_template_single_included_excluded' ) ) {
    function tripgo_wc_template_single_included_excluded() {
        wc_get_template( 'rental/loop/included-excluded.php' );
    }
}

/* Plan */
if ( ! function_exists( 'tripgo_wc_template_single_plan' ) ) {
    function tripgo_wc_template_single_plan() {
        wc_get_template( 'rental/loop/plan.php' );
    }
}

/* Map */
if ( ! function_exists( 'tripgo_wc_template_single_map' ) ) {
    function tripgo_wc_template_single_map() {
        wc_get_template( 'rental/loop/map.php' );
    }
}

/* Map */
if ( ! function_exists( 'tripgo_wc_template_single_review' ) ) {
    function tripgo_wc_template_single_review() {
        wc_get_template( 'rental/loop/review.php' );
    }
}

/* Froms */
if ( ! function_exists( 'tripgo_wc_template_single_forms' ) ) {
    function tripgo_wc_template_single_forms() {
        wc_get_template( 'rental/loop/forms.php' );
    }
}

/* Table Price */
if ( ! function_exists( 'tripgo_wc_template_single_table_price' ) ) {
    function tripgo_wc_template_single_table_price() {
        wc_get_template( 'rental/loop/table_price.php' );
    }
}

/* Unavailable time */
if ( ! function_exists( 'tripgo_wc_template_single_unavailable_time' ) ) {
    function tripgo_wc_template_single_unavailable_time() {
        wc_get_template( 'rental/loop/unavailable_time.php' );
    }
}



/* Content Product */
if ( ! function_exists( 'tripgo_template_wc_loop_item' ) ) {
    /**
     * Get after head product price in the loop.
     */
    function tripgo_template_wc_loop_item() {
        wc_get_template( 'rental/content-item-product.php' );
    }
}

/* Booking form */
// Dates
if ( ! function_exists( 'tripgo_booking_form_dates' ) ) {
    function tripgo_booking_form_dates( $args ) {
        wc_get_template( 'rental/loop/fields/dates.php', $args );
    }
}

// Guests
if ( ! function_exists( 'tripgo_booking_form_guests' ) ) {
    function tripgo_booking_form_guests( $args ) {
        wc_get_template( 'rental/loop/fields/guests.php', $args );
    }
}

// Extra Fields
if ( ! function_exists( 'tripgo_booking_form_extra_fields' ) ) {
    function tripgo_booking_form_extra_fields( $args ) {
        wc_get_template( 'rental/loop/fields/extra_fields.php', $args );
    }
}

// Quantity
if ( ! function_exists( 'tripgo_booking_form_quantity' ) ) {
    function tripgo_booking_form_quantity( $args ) {
        wc_get_template( 'rental/loop/fields/quantity.php', $args );
    }
}

// Resources
if ( ! function_exists( 'tripgo_booking_form_resources' ) ) {
    function tripgo_booking_form_resources( $args ) {
        wc_get_template( 'rental/loop/fields/resources.php', $args );
    }
}

// Services
if ( ! function_exists( 'tripgo_booking_form_services' ) ) {
    function tripgo_booking_form_services( $args ) {
        wc_get_template( 'rental/loop/fields/services.php', $args );
    }
}

// Deposit
if ( ! function_exists( 'tripgo_booking_form_deposit' ) ) {
    function tripgo_booking_form_deposit( $args ) {
        wc_get_template( 'rental/loop/fields/deposit.php', $args );
    }
}

// Ajax Total
if ( ! function_exists( 'tripgo_booking_form_ajax_total' ) ) {
    function tripgo_booking_form_ajax_total( $args ) {
        wc_get_template( 'rental/loop/fields/ajax-total.php', $args );
    }
}