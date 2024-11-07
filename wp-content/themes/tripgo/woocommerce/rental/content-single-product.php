<?php
/**
 * @package    Tripgo by ovatheme
 * @author     Ovatheme
 * @copyright  Copyright (C) 2022 Ovatheme All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 */

if ( !defined( 'ABSPATH' ) ) exit();

global $product;

/**
 * Hook: woocommerce_before_single_product.
 *
 * @hooked woocommerce_output_all_notices - 10
 */
do_action( 'woocommerce_before_single_product' );

if ( post_password_required() ) {
	echo get_the_password_form(); // WPCS: XSS ok.
	return;
}
?>
<div id="product-<?php the_ID(); ?>" <?php wc_product_class( '', $product ); ?>>

	<?php
	/**
	 * Hook: tripgo_wc_before_single_product_summary.
	 */
	do_action( 'tripgo_wc_before_single_product_summary' );
	?>

	<div class="ova-content-single-product">
		<div class="single-product-header">
			<?php
				/**
				 * Hook: tripgo_wc_before_single_product_header.
				 */
				do_action( 'tripgo_wc_before_single_product_header' );
			?>

				<?php
					/**
					 * Hook: tripgo_wc_before_single_product_top_header.
					 */
					do_action( 'tripgo_wc_before_single_product_top_header' );
				?>

					<?php
						/**
						 * Hook: tripgo_wc_single_product_top_header.
						 *
						 * @hooked tripgo_wc_template_single_title - 5
						 * @hooked tripgo_wc_template_single_video_gallery - 10
						 */
						do_action( 'tripgo_wc_single_product_top_header' );
					?>

				<?php
					/**
					 * Hook: tripgo_wc_after_single_product_top_header.
					 */
					do_action( 'tripgo_wc_after_single_product_top_header' );
				?>

				<?php
					/**
					 * Hook: tripgo_wc_single_product_header.
					 *
					 * @hooked tripgo_wc_template_single_location - 10
					 * @hooked tripgo_wc_template_single_slideshow - 10
					 * @hooked tripgo_wc_template_single_features - 10
					 */
					do_action( 'tripgo_wc_single_product_header' );
				?>

			<?php
				/**
				 * Hook: tripgo_wc_after_single_product_header.
				 */
				do_action( 'tripgo_wc_after_single_product_header' );
			?>
		</div>

		<div class="single-product-summary">
			<?php
				/**
				 * Hook: tripgo_wc_before_single_product_content.
				 */
				do_action( 'tripgo_wc_before_single_product_content' );
			?>

			    <?php
					/**
					 * Hook: tripgo_wc_before_single_product_summary_left.
					 */
					do_action( 'tripgo_wc_before_single_product_summary_left' );
				?>

					<?php
						/**
						 * Hook: tripgo_wc_single_product_summary_left.
						 *
						 * @hooked tripgo_wc_template_single_content - 10
						 * @hooked tripgo_wc_template_single_included-excluded - 10
						 * @hooked tripgo_wc_template_single_plan - 10
						 * @hooked tripgo_wc_template_single_map - 10
						 * @hooked tripgo_wc_template_single_review - 10
						 */

						do_action( 'tripgo_wc_single_product_summary_left' );
					?>

				<?php
					/**
					 * Hook: tripgo_wc_after_single_product_summary_left.
					 */
					do_action( 'tripgo_wc_after_single_product_summary_left' );
				?>

				<?php
					/**
					 * Hook: tripgo_wc_before_single_product_summary_right.
					 */
					do_action( 'tripgo_wc_before_single_product_summary_right' );
				?>

					<?php
						/**
						 * Hook: tripgo_wc_single_product_summary_right.
						 *
						 * @hooked tripgo_wc_template_single_forms - 10
						 * @hooked tripgo_wc_template_single_price_table - 10
						 * @hooked tripgo_wc_template_single_unavailable_time - 10
						 */

						do_action( 'tripgo_wc_single_product_summary_right' );
					?>

				<?php
					/**
					 * Hook: tripgo_wc_after_single_product_summary_right.
					 */
					do_action( 'tripgo_wc_after_single_product_summary_right' );
				?>

			<?php
				/**
				 * Hook: tripgo_wc_after_single_product_content.
				 */
				do_action( 'tripgo_wc_after_single_product_content' );
			?>
		</div>

		<div class="single-product-related">
			<?php
				/**
				 * Hook: tripgo_wc_before_single_product_content.
				 */
				do_action( 'tripgo_wc_before_single_product_content' );
			?>

			<?php
				/**
				 * Hook: tripgo_wc_single_product_related.
				 *
				 * @hooked tripgo_wc_template_single_product_related - 10
				 */
				do_action( 'tripgo_wc_single_product_related' );
			?>

			<?php
				/**
				 * Hook: tripgo_wc_after_single_product_content.
				 */
				do_action( 'tripgo_wc_after_single_product_content' );
			?>
		</div>
	</div>

	<?php
	/**
	 * Hook: tripgo_wc_after_single_product_summary.
	 *
	 * @hooked woocommerce_output_product_data_tabs - 10
	 * @hooked woocommerce_upsell_display - 15
	 * @hooked woocommerce_output_related_products - 20
	 */
	do_action( 'tripgo_wc_after_single_product_summary' );
	?>
</div>

<?php do_action( 'woocommerce_after_single_product' ); ?>
