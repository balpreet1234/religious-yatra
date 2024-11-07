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

$amount_insurance = get_post_meta( $product_id, 'ovabrw_amount_insurance', true );

?>

<div class="ajax-show-total">
	<?php if ( get_option( 'ova_brw_booking_form_show_quantity_availables', 'yes' ) === 'yes' ): ?>
		<div class="ovabrw-ajax-availables ovabrw-show-amount">
			<span class="availables-label label">
				<?php esc_html_e( 'Available: ', 'tripgo' ); ?>
			</span>
			<span class="show-availables-number show-amount"></span>
			<span class="ajax-loading-total">
				<i aria-hidden="true" class="flaticon flaticon-spinner-of-dots"></i>
			</span>
		</div>
	<?php endif; ?>
	<div class="ovabrw-ajax-total ovabrw-show-amount">
		<span class="show-total label">
			<?php esc_html_e( 'Total:', 'tripgo' ); ?>
		</span>
		<span class="show-total-number show-amount"></span>
		<span class="ajax-loading-total">
			<i aria-hidden="true" class="flaticon flaticon-spinner-of-dots"></i>
		</span>
	</div>
	<?php if ( get_option( 'ova_brw_booking_form_show_amount_insurance', 'yes' ) === 'yes' && $amount_insurance ): ?>
		<div class="ovabrw-ajax-amount-insurance">
			<span class="amount-insurance-label">
				<?php esc_html_e( 'Insurance included: ', 'tripgo' ); ?>
			</span>
			<span class="show-amount-insurance"></span>
		</div>
	<?php endif; ?>
</div>