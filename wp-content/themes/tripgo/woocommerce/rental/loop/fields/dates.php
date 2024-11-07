<?php
/**
 * @package    Tripgo by ovatheme
 * @author     Ovatheme
 * @copyright  Copyright (C) 2022 Ovatheme All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 */

if ( !defined( 'ABSPATH' ) ) exit();

$product_id = isset( $args['id'] ) && $args['id'] ? $args['id'] : get_the_id();

$product = wc_get_product( $product_id );

if ( !$product || !$product->is_type('ovabrw_car_rental') ) return;

// Get first day in week
$first_day = get_option( 'ova_brw_calendar_first_day', '0' );

if ( empty( $first_day ) ) {
    $first_day = 0;
}

$disable_week_day = get_post_meta( $product_id, 'ovabrw_product_disable_week_day', true );

// Total Day
$total_day = get_post_meta( $product_id, 'ovabrw_number_days', true );
if ( ! $total_day ) {
    $total_day = 1;
}

// Get booked time
$statuses   = brw_list_order_status();
$order_time = ovabrw_get_disabled_dates( $product_id, $statuses );

$placeholder_date   = ovabrw_get_placeholder_date();
$label_time         = esc_html__( 'Choose time', 'tripgo' );
$label_check_in     = esc_html__( 'Check in', 'tripgo' );
$label_check_out    = esc_html__( 'Check out', 'tripgo' );

$check_in_default   = ovabrw_get_current_date_from_search( 'pickup_date', $product_id );
$check_out_default  = ovabrw_get_current_date_from_search( 'dropoff_date', $product_id );

$fixed_time_check_in    = get_post_meta( $product_id, 'ovabrw_fixed_time_check_in', true );
$fixed_time_check_out   = get_post_meta( $product_id, 'ovabrw_fixed_time_check_out', true );

$duration = get_post_meta( $product_id, 'ovabrw_duration_checkbox', true );

$readonly = $check_in_class = '';
if ( ! empty( $fixed_time_check_in ) && ! empty( $fixed_time_check_out ) && ! $duration ) {
    $readonly = 'readonly';
    $check_in_class = ' ovabrw_readonly';
}

// Show/Hide checkout field
$hide_checkout  = '';
$checkout_field = get_post_meta( $product_id, 'ovabrw_manage_checkout_field', true );
$show_checkout  = get_option( 'ova_brw_booking_form_show_checkout', 'yes' );

if ( ! $checkout_field ) $checkout_field = 'global';

if ( $checkout_field === 'global' ) {
    if ( $show_checkout != 'yes' ) {
        $hide_checkout = ' ovabrw-hide-field';
    }
} elseif ( $checkout_field === 'hide' ) {
    $hide_checkout = ' ovabrw-hide-field';
} else {
    $hide_checkout = '';
}

?>

<?php if ( ! $duration && ! empty( $fixed_time_check_in ) && ! empty( $fixed_time_check_out ) ):
    $date_format    = ovabrw_get_date_format();
    $had_time       = false;

    // Preparation Time
    $preparation_time = get_post_meta( $product_id, 'ovabrw_preparation_time', true );
?>
    <div class="rental_item ovabrw_fixed_time_field">
        <label>
            <?php echo esc_html( $label_time ); ?>
        </label>
        <select name="ovabrw_fixed_time" class="ovabrw_fixed_time">
            <?php $flag = 0; foreach ( $fixed_time_check_in as $k => $check_in ):
                $check_out = isset( $fixed_time_check_out[$k] ) ? $fixed_time_check_out[$k] : '';

                if ( $check_in && $check_out ):
                    if ( strtotime( $check_in ) < current_time('timestamp') ) continue;

                    if ( $preparation_time ) {
                        $new_input_date = ovabrw_new_input_date( $product_id, strtotime( $check_in ), strtotime( $fixed_time_check_out[$k] ), $date_format );

                        if ( $new_input_date['pickup_date_new'] < ( current_time( 'timestamp' ) + $preparation_time*86400 - 86400 ) ) continue;
                    }

                    if ( ovabrw_qty_by_guests( $product_id ) ) {
                        $guests_available = ovabrw_validate_guests_available( $product_id, strtotime( $check_in ), strtotime( $check_out ), [], 'search' );

                        if ( ! $guests_available ) {
                            continue;
                        }
                    } else {
                        $ovabrw_quantity    = sanitize_text_field( filter_input( INPUT_POST, 'ovabrw_quantity' ) );
                        $quantity           = ! empty( $ovabrw_quantity ) ? absint( $ovabrw_quantity ) : 1;

                        $qty_available = ova_validate_manage_store( $product_id, strtotime( $check_in ), strtotime( $check_out ), false, 'search', $quantity );

                        if ( ! $qty_available ) continue;
                    }

                    $had_time = true;
                    $txt_time = sprintf( esc_html__( 'From %s to %s', 'tripgo' ), $check_in, $check_out );
            ?>
                    <option value="<?php echo esc_html( $check_in.'|'.$check_out ); ?>"<?php selected( $flag, 0 ); ?>>
                        <?php echo esc_html( $txt_time ); ?>
                    </option>
            <?php $flag++; endif; endforeach; ?>
            <?php if ( ! $had_time ): ?>
                <option value=""><?php esc_html_e( 'No time', 'tripgo' ); ?></option>
            <?php endif; ?>
        </select>
    </div>
<?php endif; ?>
<div class="rental_item ovabrw_checkin_field">
    <label>
        <?php echo esc_html( $label_check_in ); ?>
    </label>
    <input
        type="text"
        name="ovabrw_pickup_date" 
        class="required ovabrw_datetimepicker ovabrw_start_date<?php echo esc_attr( $check_in_class ); ?>"
        placeholder="<?php echo esc_attr( $placeholder_date ); ?>"
        value="<?php echo esc_attr( $check_in_default ); ?>"
        autocomplete="off"
        data-firstday="<?php echo esc_attr( $first_day ); ?>"
        data-disable-week-day="<?php echo esc_attr( $disable_week_day ); ?>"
        data-total-day="<?php echo esc_attr( $total_day ); ?>"
        data-order-time="<?php echo esc_attr( $order_time ); ?>"
        data-error="<?php esc_html_e( 'Check-in is required.', 'tripgo' ); ?>"
        data-readonly="<?php echo esc_attr( $readonly ); ?>"
        <?php echo esc_html( $readonly ); ?>
    />
    <span class="ovabrw-date-loading">
        <i aria-hidden="true" class="flaticon flaticon-spinner-of-dots"></i>
    </span>
</div>
<div class="rental_item ovabrw_checkout_field<?php echo esc_attr( $hide_checkout ); ?>">
    <label>
        <?php echo esc_html( $label_check_out ); ?>
    </label>
    <input
        type="text"
        name="ovabrw_pickoff_date"
        class="required ovabrw_end_date"
        placeholder="<?php echo esc_attr( $placeholder_date ); ?>"
        value="<?php echo esc_attr( $check_out_default ); ?>"
        autocomplete="off"
        data-error="<?php esc_html_e( 'Check-out is required.', 'tripgo' ); ?>"
        readonly
    />
    <span class="ovabrw-date-loading">
        <i aria-hidden="true" class="flaticon flaticon-spinner-of-dots"></i>
    </span>
</div>