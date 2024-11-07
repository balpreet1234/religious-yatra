<?php
if ( ! defined( 'ABSPATH' ) ) exit;

// var_dump
if ( ! function_exists( 'dd' ) ) {
    function dd( ...$args ) {
        echo '<pre>';
        var_dump( ...$args );
        echo '</pre>';
        die;
    }
}

// Return value of setting
if ( ! function_exists( 'ovabrw_get_setting' ) ) {
	function ovabrw_get_setting( $setting ) {
		if ( trim( $setting ) == '' ) return;
		return esc_html__( $setting, 'BRW Admin Settings' , 'ova-brw' );
	}
}

// Get Date Format in Setting
if ( ! function_exists( 'ovabrw_get_date_format' ) ) {
	function ovabrw_get_date_format() {
		return apply_filters( 'ovabrw_get_date_format_hook', ovabrw_get_setting( get_option( 'ova_brw_booking_form_date_format', 'd-m-Y' ) ) );
	}
}

// Get Time Format in Setting
if ( ! function_exists( 'ovabrw_get_time_format' ) ) {
	function ovabrw_get_time_format() {
		return apply_filters( 'ovabrw_get_time_format_hook', ovabrw_get_setting( get_option( 'ova_brw_booking_form_time_format', 'H:i' ) ) );
	}
}

// Get Date Time Format
if ( ! function_exists( 'ovabrw_get_datetime_format' ) ) {
	function ovabrw_get_datetime_format() {
		return apply_filters( 'ovabrw_get_datetime_format_hook', ovabrw_get_date_format() . ' ' . ovabrw_get_time_format() );
	}
}

// Get Step Time in Setting
if ( ! function_exists( 'ovabrw_get_step_time' ) ) {
	function ovabrw_get_step_time() {
		return apply_filters( 'ovabrw_get_step_time_hook', ovabrw_get_setting( get_option( 'ova_brw_step_time', 5 ) ) );
	}
}

if ( ! function_exists('ovabrw_get_placeholder_date') ) {
	function ovabrw_get_placeholder_date() {
		$placeholder = '';
		$dateformat = ovabrw_get_date_format();

		if ( 'Y-m-d' === $dateformat ) {
			$placeholder = esc_html__( 'YYYY-MM-DD', 'ova-brw' );
		} elseif ( 'm/d/Y' === $dateformat ) {
			$placeholder = esc_html__( 'MM/DD/YYYY', 'ova-brw' );
		} elseif ( 'Y/m/d' === $dateformat ) {
			$placeholder = esc_html__( 'YYYY/MM/DD', 'ova-brw' );
		} else {
			$placeholder = esc_html__( 'DD-MM-YYYY', 'ova-brw' );
		}

		return $placeholder;
	}
}

// Return real path template in Plugin or Theme
if ( ! function_exists( 'ovabrw_locate_template' ) ) {
	function ovabrw_locate_template( $template_name = '', $template_path = '', $default_path = '' ) {
		// Set variable to search in ovabrw-templates folder of theme.
		if ( ! $template_path ) :
			$template_path = 'ovabrw-templates/';
		endif;

		// Set default plugin templates path.
		if ( ! $default_path ) :
			$default_path = OVABRW_PLUGIN_PATH . 'ovabrw-templates/'; // Path to the template folder
		endif;

		// Search template file in theme folder.
		$template = locate_template( array(
			$template_path . $template_name
			// ,$template_name
		));

		// Get plugins template file.
		if ( ! $template ) :
			$template = $default_path . $template_name;
		endif;

		return apply_filters( 'ovabrw_locate_template', $template, $template_name, $template_path, $default_path );
	}
}

// Include Template File
function ovabrw_get_template( $template_name = '', $args = array(), $tempate_path = '', $default_path = '' ) {
	if ( is_array( $args ) && isset( $args ) ) :
		extract( $args );
	endif;
	$template_file = ovabrw_locate_template( $template_name, $tempate_path, $default_path );
	if ( ! file_exists( $template_file ) ) :
		_doing_it_wrong( __FUNCTION__, sprintf( '<code>%s</code> does not exist.', $template_file ), '1.0.0' );
		return;
	endif;

	include $template_file;
}

// List custom checkout fields array
if ( ! function_exists( 'ovabrw_get_list_field_checkout' ) ) {
	function ovabrw_get_list_field_checkout( $post_id ) {
		if ( ! $post_id ) return [];

		$list_ckf_output = [];

		$ovabrw_manage_custom_checkout_field = get_post_meta( $post_id, 'ovabrw_manage_custom_checkout_field', true );

		$list_field_checkout = get_option( 'ovabrw_booking_form', array() );

		// Get custom checkout field by Category
		$product_cats = wp_get_post_terms( $post_id, 'product_cat' );
		$cat_id = isset( $product_cats[0] ) ? $product_cats[0]->term_id : '';
		$ovabrw_custom_checkout_field = $cat_id ? get_term_meta($cat_id, 'ovabrw_custom_checkout_field', true) : '';

		$ovabrw_choose_custom_checkout_field = $cat_id ? get_term_meta($cat_id, 'ovabrw_choose_custom_checkout_field', true) : '';
		
		if ( $ovabrw_manage_custom_checkout_field === 'new' ) {
			$list_field_checkout_in_product = get_post_meta( $post_id, 'ovabrw_product_custom_checkout_field', true );
			$list_field_checkout_in_product_arr = explode( ',', $list_field_checkout_in_product );
			$list_field_checkout_in_product_arr = array_map( 'trim', $list_field_checkout_in_product_arr );
			$list_ckf_output = [];

			if( ! empty( $list_field_checkout_in_product_arr ) && is_array( $list_field_checkout_in_product_arr ) ) {
				foreach( $list_field_checkout_in_product_arr as $field_name ) {
					if( array_key_exists( $field_name, $list_field_checkout ) ) {
						$list_ckf_output[$field_name] = $list_field_checkout[$field_name];
					}
				}
			} 
		} elseif ( $ovabrw_choose_custom_checkout_field == 'all' ) {
			$list_ckf_output = $list_field_checkout;
		} elseif ( $ovabrw_choose_custom_checkout_field == 'special' ) {
			if ( $ovabrw_custom_checkout_field ) {
				foreach( $ovabrw_custom_checkout_field as $field_name ) {
					if( array_key_exists( $field_name, $list_field_checkout ) ) {
						$list_ckf_output[$field_name] = $list_field_checkout[$field_name];
					}
				}
			} else {
				$list_ckf_output = [];
			}
		} else {
			$list_ckf_output = $list_field_checkout;
		}

		return $list_ckf_output;
	}
}

// List Order Status
if ( ! function_exists( 'brw_list_order_status' ) ) {
	function brw_list_order_status() {
		return apply_filters( 'brw_list_order_status', array( 'wc-completed', 'wc-processing' ) );
	}
}

// Stock Quantity Product
if ( ! function_exists( 'ovabrw_get_total_stock' ) ) {
	function ovabrw_get_total_stock( $product_id ) {
	    $stock_quantity = 1;
		$number_stock 	= get_post_meta( $product_id, 'ovabrw_stock_quantity', true );

		if ( $number_stock ) {
			$stock_quantity = absint( $number_stock );
		}

		return $stock_quantity;
	}
}

// Get dates between
if ( ! function_exists( 'ovabrw_createDatefull' ) ) {
	function ovabrw_createDatefull( $start = '', $end = '', $format = "Y-m-d" ){
	    $dates = array();

	    while( $start <= $end ) {
	        array_push( $dates, date( $format, $start) );
	        $start += 86400;
	    }

	    return $dates;
	} 
}

// Get number dates between
if ( ! function_exists( 'total_between_2_days' ) ) {
	function total_between_2_days( $start, $end ) {
    	return floor( abs( strtotime( $end ) - strtotime( $start ) ) / (60*60*24) );
	}
}

// Get Array Product ID with WPML
if ( ! function_exists( 'ovabrw_get_wpml_product_ids' ) ) {
	function ovabrw_get_wpml_product_ids( $product_id_original ) {
		$translated_ids = array();

		// get plugin active
		$active_plugins = get_option('active_plugins');

		if ( in_array ( 'polylang/polylang.php', $active_plugins ) || in_array ( 'polylang-pro/polylang.php', $active_plugins ) ) {
				$languages = pll_languages_list();
				if ( !isset( $languages ) ) return;
				foreach ($languages as $lang) {
					$translated_ids[] = pll_get_post($product_id_original, $lang);
				}
		} elseif ( in_array ( 'sitepress-multilingual-cms/sitepress.php', $active_plugins ) ) {
			global $sitepress;
		
			if(!isset($sitepress)) return;
			
			$trid = $sitepress->get_element_trid($product_id_original, 'post_product');
			$translations = $sitepress->get_element_translations($trid, 'product');
			foreach( $translations as $lang=>$translation){
			    $translated_ids[] = $translation->element_id;
			}

		} else {
			$translated_ids[] = $product_id_original;
		}

		return apply_filters( 'ovabrw_multiple_languages', $translated_ids );
	}
}

// Get Pick up date from URL in Product detail
if ( ! function_exists( 'ovabrw_get_current_date_from_search' ) ) {
	function ovabrw_get_current_date_from_search( $type = 'pickup_date', $product_id = false ) {
		// Get date from URL
		if ( $type == 'pickup_date'  ){
			$time = ( isset( $_GET['pickup_date'] ) ) ? strtotime( $_GET['pickup_date'] ) : '';
		} else if ( $type == 'dropoff_date' ) {
			$time = ( isset( $_GET['dropoff_date'] ) ) ? strtotime( $_GET['dropoff_date'] ) : '';
		}

		$dateformat = ovabrw_get_date_format();

		if ( $time ) {
			return date( $dateformat, $time );
		}

		return '';
	}
}

// Get All custom taxonomy display in listing of product
if ( ! function_exists( 'get_all_cus_tax_dis_listing' ) ) {
	function get_all_cus_tax_dis_listing( $pid ) {
		$all_cus_choosed 		= array();
		$all_cus_choosed_tmp 	= array();

		// Get All Categories of this product
		$categories = get_the_terms( $pid, 'product_cat' );
		if ( $categories ) {
			foreach ($categories as $key => $value) {
				$cat_id = $value->term_id;

				// Get custom tax display in category
				$ovabrw_custom_tax = get_term_meta($cat_id, 'ovabrw_custom_tax', true);

				if ( $ovabrw_custom_tax ) {
					foreach ($ovabrw_custom_tax as $slug_tax) {
						// Get value of terms in product
						$terms = get_the_terms( $pid, $slug_tax );

						// Get option: custom taxonomy
						$ovabrw_custom_taxonomy =  get_option( 'ovabrw_custom_taxonomy', '' );
						$show_listing_status = 'no';

						if ( $ovabrw_custom_taxonomy ) {
							foreach ( $ovabrw_custom_taxonomy as $slug => $value ) {
								if ( $slug_tax == $slug && isset( $value['show_listing'] ) && $value['show_listing'] == 'on' ) {
									$show_listing_status = 'yes';
									break;
								}
							}
						}

						if ( $terms && $show_listing_status == 'yes' ) {
							foreach ( $terms as $term ) {
								if ( ! in_array( $slug_tax, $all_cus_choosed_tmp ) ) {
									// Assign array temp to check exist
									array_push($all_cus_choosed_tmp, $slug_tax);
									array_push($all_cus_choosed, array( 'slug' => $slug_tax, 'name' => $term->name) );
								}
							}
						}
					}
				}
			}
		}

		return $all_cus_choosed;
	}
}

// Get custom taxonomy of an product
if ( ! function_exists( 'ovabrw_get_taxonomy_choosed_product' ) ) {
	function ovabrw_get_taxonomy_choosed_product( $pid ) {
		// Custom taxonomies choosed in post
		$all_cus_tax 	= array();
		$exist_cus_tax 	= array();
		
		// Get Category of product
		$cats = get_the_terms( $pid, 'product_cat' );
		$show_taxonomy_depend_category = ovabrw_get_setting( get_option( 'ova_brw_search_show_tax_depend_cat', 'yes' ) );

		if ( 'yes' == $show_taxonomy_depend_category ) {
			if ( $cats ) {
				foreach ( $cats as $key => $cat ) {
					// Get custom taxonomy display in category
					$ovabrw_custom_tax = get_term_meta($cat->term_id, 'ovabrw_custom_tax', true);	
					
					if ( $ovabrw_custom_tax ){
						foreach ( $ovabrw_custom_tax as $key => $value ) {
							array_push( $exist_cus_tax, $value );
						}	
					}
				}
			}

			if ( $exist_cus_tax ) {
				foreach ( $exist_cus_tax as $key => $value ) {
					$cus_tax_terms = get_the_terms( $pid, $value );

					if ( $cus_tax_terms ) {
						foreach ( $cus_tax_terms as $key => $value ) {
							$list_fields = get_option( 'ovabrw_custom_taxonomy', array() );

							if ( ! empty( $list_fields ) ) :
			                    foreach ( $list_fields as $key => $field ) : 
			                    	if ( is_object($value) && $value->taxonomy == $key ) {
			                    		if ( array_key_exists($key, $all_cus_tax) ) {
			                    			if ( !in_array( $value->name, $all_cus_tax[$key]['value'] ) ) {
			                    				array_push($all_cus_tax[$key]['value'], $value->name);	
			                    			}
			                    		} else {
		                    				if ( isset( $field['label_frontend'] ) && $field['label_frontend'] ) {
		                    					$all_cus_tax[$key]['name'] = $field['label_frontend'];	
		                    				} else {
		                    					$all_cus_tax[$key]['name'] = $field['name'];	
		                    				}
		                    				$all_cus_tax[$key]['value'] = array( $value->name );
			                    		}
			                    		break;
			                    	}
			                    endforeach;
			                endif;
						}
					}
				}
			}
		} else {
			$list_fields = get_option( 'ovabrw_custom_taxonomy', array() );

			if ( ! empty( $list_fields ) ) {
				foreach ( $list_fields as $key => $field ) {
					$terms = get_the_terms( $pid, $key );
					if ( $terms && ! isset( $terms->errors ) ) {
						foreach ( $terms as $value ) {
							if ( is_object( $value ) ) {
								if ( array_key_exists( $key, $all_cus_tax ) ) {
									if ( ! in_array( $value->name, $all_cus_tax[$key]['value'] ) ) {
			            				array_push($all_cus_tax[$key]['value'], $value->name);	
			            			}
								} else {
									if ( isset( $field['label_frontend'] ) && $field['label_frontend'] ) {
			        					$all_cus_tax[$key]['name'] = $field['label_frontend'];	
			        				} else {
			        					$all_cus_tax[$key]['name'] = $field['name'];
			        				}

									$all_cus_tax[$key]['value'] = array( $value->name );
								}
							}
						}
					}
				}
			}
		}

		return $all_cus_tax;
	}
}

// Get product template
if ( ! function_exists( 'ovabrw_get_product_template' ) ) {
	function ovabrw_get_product_template( $id ) {
		$template = get_option( 'ova_brw_template_elementor_template', 'default' );

		if ( empty( $id ) ) {
			return $template;
		}

		$product_template = get_post_meta( $id, 'ovabrw_product_template', true );

		if ( absint( $product_template ) ) {
			return absint( $product_template );
		}

		$products 	= wc_get_product( $id );
		$categories = $products->get_category_ids();

		if ( ! empty( $categories ) ) {
	        $term_id 	= reset( $categories );
	        $template_by_category = get_term_meta( $term_id, 'ovabrw_product_templates', true );

	        if ( $template_by_category && $template_by_category !== 'global' ) {
	        	$template = $template_by_category;
	        }
	    }

		return $template;
	}
}

// Check key in array
if ( ! function_exists( 'ovabrw_check_array' ) ) {
	function ovabrw_check_array( $args, $key ) {
		if ( ! empty( $args ) && is_array( $args ) ) {
			if ( isset( $args[$key] ) && $args[$key] != '' ) {
				return true;
			}
		}

		return false;
	}
}

// Get Price - Multi Currency
if ( ! function_exists( 'ovabrw_wc_price' ) ) {
	function ovabrw_wc_price( $price = null, $args = array(), $convert = true ) {
		$new_price = $price;

		if ( ! $price ) $new_price = 0;

		do_action( 'ovabrw_wc_price_before', $price, $args, $convert );

		$current_currency = isset( $args['currency'] ) && $args['currency'] ? $args['currency'] : false;

		// CURCY - Multi Currency for WooCommerce
		// WooCommerce Multilingual & Multicurrency
		if ( is_plugin_active( 'woo-multi-currency/woo-multi-currency.php' ) || is_plugin_active( 'woocommerce-multi-currency/woocommerce-multi-currency.php' ) ) {
			$new_price = wmc_get_price( $price, $current_currency );
		} elseif ( is_plugin_active( 'woocommerce-multilingual/wpml-woocommerce.php' ) ) {
			if ( $convert ) {
				// WPML multi currency
	    		global $woocommerce_wpml;

	    		if ( $woocommerce_wpml && is_object( $woocommerce_wpml ) ) {
	    			if ( wp_doing_ajax() ) add_filter( 'wcml_load_multi_currency_in_ajax', '__return_true' );

			        $multi_currency     = $woocommerce_wpml->get_multi_currency();
			        $currency_options   = $woocommerce_wpml->get_setting( 'currency_options' );
			        $WMCP   			= new WCML_Multi_Currency_Prices( $multi_currency, $currency_options );
			        $new_price  		= $WMCP->convert_price_amount( $price, $current_currency );
			    }
			}
		} else {
			// nothing
		}

		do_action( 'ovabrw_wc_price_after', $price, $args, $convert );
		
		return apply_filters( 'ovabrw_wc_price', wc_price( $new_price, $args ), $price, $args, $convert );
	}
}

// Convert Price - Multi Currency
if ( ! function_exists( 'ovabrw_convert_price' ) ) {
	function ovabrw_convert_price( $price = null, $args = array(), $convert = true ) {
		$new_price = $price;

		if ( ! $price ) $new_price = 0;

		do_action( 'ovabrw_convert_price_before', $price, $args, $convert );

		$current_currency = isset( $args['currency'] ) && $args['currency'] ? $args['currency'] : false;

		// CURCY - Multi Currency for WooCommerce
		// WooCommerce Multilingual & Multicurrency
		if ( is_plugin_active( 'woo-multi-currency/woo-multi-currency.php' ) || is_plugin_active( 'woocommerce-multi-currency/woocommerce-multi-currency.php' ) ) {
			$new_price = wmc_get_price( $price, $current_currency );
		} elseif ( is_plugin_active( 'woocommerce-multilingual/wpml-woocommerce.php' ) ) {
			if ( $convert ) {
				// WPML multi currency
	    		global $woocommerce_wpml;

	    		if ( $woocommerce_wpml && is_object( $woocommerce_wpml ) ) {
	    			if ( wp_doing_ajax() ) add_filter( 'wcml_load_multi_currency_in_ajax', '__return_true' );

			        $multi_currency     = $woocommerce_wpml->get_multi_currency();
			        $currency_options   = $woocommerce_wpml->get_setting( 'currency_options' );
			        $WMCP   			= new WCML_Multi_Currency_Prices( $multi_currency, $currency_options );
			        $new_price  		= $WMCP->convert_price_amount( $price, $current_currency );
			    }
			}
		} else {
			// nothing
		}

		do_action( 'ovabrw_convert_price_after', $price, $args, $convert );
		
		return apply_filters( 'ovabrw_convert_price', $new_price, $price, $args, $convert );
	}
}

// Convert Price in Admin - Multi Currency
if ( ! function_exists( 'ovabrw_convert_price_in_admin' ) ) {
	function ovabrw_convert_price_in_admin( $price = null, $currency_code = '' ) {
		$new_price = $price;

		if ( ! $price ) $new_price = 0;

		if ( is_admin() && ( is_plugin_active( 'woo-multi-currency/woo-multi-currency.php' ) || is_plugin_active( 'woocommerce-multi-currency/woocommerce-multi-currency.php' ) ) ) {
			$setting = '';
			
			if ( is_plugin_active( 'woo-multi-currency/woo-multi-currency.php' ) ) {
				$setting = WOOMULTI_CURRENCY_F_Data::get_ins();
			}

			if ( is_plugin_active( 'woocommerce-multi-currency/woocommerce-multi-currency.php' ) ) {
				$setting = WOOMULTI_CURRENCY_Data::get_ins();
			}

			if ( ! empty( $setting ) && is_object( $setting ) ) {
				/*Check currency*/
				$selected_currencies = $setting->get_list_currencies();
				$current_currency    = $setting->get_current_currency();

				if ( ! $currency_code || $currency_code === $current_currency ) {
					return $new_price;
				}

				if ( $new_price ) {
					if ( $currency_code && isset( $selected_currencies[ $currency_code ] ) ) {
						$new_price = $price * (float) $selected_currencies[ $currency_code ]['rate'];
					} else {
						$new_price = $price * (float) $selected_currencies[ $current_currency ]['rate'];
					}
				}
			}
		}

		return apply_filters( 'ovabrw_convert_price_in_admin', $new_price, $price, $currency_code );
	}
}

// Get product price from database
if ( ! function_exists( 'ovabrw_mcml_get_product_price' ) ) {
	function ovabrw_wcml_get_product_price( $product_id, $meta_key ) {
		$price = 0;

		if ( ! $product_id || ! $meta_key ) return $price;

		if ( is_plugin_active( 'woocommerce-multilingual/wpml-woocommerce.php' ) ) {
			global $wpdb;

        	$price = $wpdb->get_var( "SELECT meta_value FROM $wpdb->postmeta WHERE post_id = $product_id AND meta_key = '$meta_key'" );
		}

		return floatval( $price );
	}
}

// Check High-Performance Order Storage for Woocommerce
if ( ! function_exists( 'ovabrw_wc_custom_orders_table_enabled' ) ) {
	function ovabrw_wc_custom_orders_table_enabled() {
		if ( get_option( 'woocommerce_custom_orders_table_enabled', 'no' ) === 'yes' ) {
			return true;
		}

		return false;
	}
}

// Loading reCAPTCHA
if ( ! function_exists( 'ovabrw_loading_reCAPTCHA' ) ) {
	function ovabrw_loading_reCAPTCHA() {
		// reCAPTCHA
		if ( get_option( 'ova_brw_recapcha_enable', 'no' ) === 'yes' && apply_filters( 'ovabrw_loading_reCAPTCHA', true ) ) {
			$recaptcha_type = ovabrw_get_recaptcha_type();
			$site_key 		= ovabrw_get_recaptcha_site_key();

			wp_enqueue_script( 'ovabrw_recapcha_loading', OVABRW_PLUGIN_URI.'assets/js/frontend/ova-brw-recaptcha.js', [], false, false );
			wp_localize_script( 'ovabrw_recapcha_loading', 'ovabrw_recaptcha', array( 'site_key' => $site_key, 'form' => get_option( 'ova_brw_recapcha_form', '' ) ) );

			if ( $recaptcha_type === 'v3' ) {
				wp_enqueue_script( 'ovabrw_recaptcha', 'https://www.google.com/recaptcha/api.js?onload=ovabrwLoadingReCAPTCHAv3&render='.$site_key, [], false, false );
			} else {
				wp_enqueue_script( 'ovabrw_recaptcha', 'https://www.google.com/recaptcha/api.js?onload=ovabrwLoadingReCAPTCHAv2&render=explicit', [], false, false );
			}
		}
	}
}

// reCAPTCHA type
if ( ! function_exists( 'ovabrw_get_recaptcha_type' ) ) {
	function ovabrw_get_recaptcha_type() {
		return get_option( 'ova_brw_recapcha_type', 'v3' );
	}
}

// reCAPTCHA site key
if ( ! function_exists( 'ovabrw_get_recaptcha_site_key' ) ) {
	function ovabrw_get_recaptcha_site_key() {
		if ( ovabrw_get_recaptcha_type() === 'v3' ) {
			return get_option( 'ova_brw_recapcha_v3_site_key', '' );
		} else {
			return get_option( 'ova_brw_recapcha_v2_site_key', '' );
		}
	}
}

// reCAPTCHA secret key
if ( ! function_exists( 'ovabrw_get_recaptcha_secret_key' ) ) {
	function ovabrw_get_recaptcha_secret_key() {
		if ( ovabrw_get_recaptcha_type() === 'v3' ) {
			return get_option( 'ova_brw_recapcha_v3_secret_key', '' );
		} else {
			return get_option( 'ova_brw_recapcha_v2_secret_key', '' );
		}
	}
}

// reCAPTCHA form
if ( ! function_exists( 'ovabrw_get_recaptcha_form' ) ) {
	function ovabrw_get_recaptcha_form( $form = '' ) {
		if ( get_option( 'ova_brw_recapcha_form', '' ) === 'both' ) return true;
		if ( get_option( 'ova_brw_recapcha_form', '' ) === $form ) return true;

		return false;
	}
}