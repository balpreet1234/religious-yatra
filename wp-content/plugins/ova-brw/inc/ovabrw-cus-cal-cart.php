<?php defined( 'ABSPATH' ) || exit();

// Custom Calculate Total Add To Cart
add_action( 'woocommerce_before_calculate_totals',  'ovabrw_woocommerce_before_calculate_totals' , 10, 1 ); 
if ( ! function_exists( 'ovabrw_woocommerce_before_calculate_totals' ) ) {
    function ovabrw_woocommerce_before_calculate_totals( $cart_object ) {
        $remaining_amount   = $deposit_amount = $total_amount_insurance = $remaining_tax_total = 0;
        $has_deposit        = false;

        foreach ( $cart_object->get_cart() as $cart_item_key => $cart_item) {
            // Check custom product type is ovabrw_car_rental
            if ( !$cart_item['data']->is_type('ovabrw_car_rental') ) continue;

            $product_id = $cart_item['product_id'];

            // Quantity
            $quantity = 1;
            if ( ovabrw_check_array( $cart_item, 'ovabrw_quantity' ) ) {
                $quantity = absint( $cart_item['ovabrw_quantity'] );
            }

            // Check in
            $ovabrw_pickup_date = '';
            if ( ovabrw_check_array( $cart_item, 'ovabrw_pickup_date' ) ) {
                $ovabrw_pickup_date = strtotime( $cart_item['ovabrw_pickup_date'] );
            }

            // Check out
            $ovabrw_pickoff_date = '';
            if ( ovabrw_check_array( $cart_item, 'ovabrw_pickoff_date' ) ) {
                $ovabrw_pickoff_date = strtotime( $cart_item['ovabrw_pickoff_date'] );
            }

            // Adults Quantity
            $adults_quantity = absint( get_post_meta( $product_id, 'ovabrw_adults_min', true ) );
            if ( ovabrw_check_array( $cart_item, 'ovabrw_adults' ) ) {
                $adults_quantity = absint( $cart_item['ovabrw_adults'] );
            }

            // Childrens Quantity
            $childrens_quantity  = absint( get_post_meta( $product_id, 'ovabrw_childrens_min', true ) );
            if ( ovabrw_check_array( $cart_item, 'ovabrw_childrens' ) ) {
                $childrens_quantity = absint( $cart_item['ovabrw_childrens'] );
            }

            // Babies Quantity
            $babies_quantity  = absint( get_post_meta( $product_id, 'ovabrw_babies_min', true ) );
            if ( ovabrw_check_array( $cart_item, 'ovabrw_babies' ) ) {
                $babies_quantity = absint( $cart_item['ovabrw_babies'] );
            }

            $line_total = get_price_by_guests( $product_id, $ovabrw_pickup_date, $ovabrw_pickoff_date, $cart_item );
            $line_total = round( $line_total, wc_get_price_decimals() );

            // Amount Insurance 
            $amount_insurance = floatval( get_post_meta( $product_id, 'ovabrw_amount_insurance', true ) );
            $amount_insurance = $amount_insurance * ( $adults_quantity + $childrens_quantity + $babies_quantity ) * $quantity;

            if ( is_plugin_active( 'woocommerce-multilingual/wpml-woocommerce.php' ) ) {
                $line_total         = ovabrw_convert_price( $line_total );
                $amount_insurance   = ovabrw_convert_price( $amount_insurance );
            }

            $deposit_enable = get_post_meta ( $product_id, 'ovabrw_enable_deposit', true );
            $value_deposit  = get_post_meta ( $product_id, 'ovabrw_amount_deposit', true );
            $value_deposit  = $value_deposit ? floatval( $value_deposit ) : 0;
            $type_deposit   = get_post_meta ( $product_id, 'ovabrw_type_deposit', true );

            $sub_remaining_amount = 0;
            $sub_deposit_amount   = $line_total;

            $cart_type_deposit = 'full';
            if ( ovabrw_check_array( $cart_item, 'ova_type_deposit' ) ) {
                $cart_type_deposit = $cart_item['ova_type_deposit'];
            }

            WC()->cart->deposit_info = array();
            if ( 'yes' === $deposit_enable && $cart_type_deposit ) {
                $has_deposit = true;
                if ( 'full' === $cart_type_deposit ) {
                    $sub_deposit_amount     = $line_total;
                    $sub_remaining_amount   = 0;
                } else if ( 'deposit' === $cart_type_deposit ) {
                    if ( 'percent' === $type_deposit ) {
                        $sub_deposit_amount     = ($line_total * $value_deposit) / 100;
                        $sub_remaining_amount   = $line_total - $sub_deposit_amount;
                    } else if ( 'value' === $type_deposit ) {
                        $sub_deposit_amount     = $value_deposit;
                        $sub_remaining_amount   = $line_total - $sub_deposit_amount;
                    }
                }
            }

            $remaining_amount       += ovabrw_get_price_tax( $sub_remaining_amount, $cart_item );
            $deposit_amount         += $sub_deposit_amount;
            $total_amount_insurance += $amount_insurance;

            // Add tax total remanining
            if ( wc_tax_enabled() ) {
                $prices_include_tax   = wc_prices_include_tax() ? 'yes' : 'no';
                $remaining_tax_total += ovabrw_get_taxes_by_price( $sub_remaining_amount, $product_id, $prices_include_tax );
            }

            WC()->cart->deposit_info[ 'ova_deposit_amount' ]    = round( $deposit_amount, wc_get_price_decimals() );
            WC()->cart->deposit_info[ 'ova_remaining_amount' ]  = round( $remaining_amount, wc_get_price_decimals() );
            WC()->cart->deposit_info[ 'ova_type_deposit' ]      = $cart_type_deposit;
            WC()->cart->deposit_info[ 'ova_insurance_amount' ]  = round( $total_amount_insurance, wc_get_price_decimals() );
            WC()->cart->deposit_info[ 'ova_has_deposit' ]       = $has_deposit;
            WC()->cart->deposit_info[ 'ova_remaining_taxes' ]   = $remaining_tax_total;

            $cart_item['data']->set_price( round( $line_total / $quantity, wc_get_price_decimals() ) );
            $cart_item['data']->add_meta_data( 'pay_total', round( $line_total, wc_get_price_decimals() ), true );

            $cart_object->cart_contents[ $cart_item_key ]['quantity'] = $quantity;

            // Check deposit
            if ( $has_deposit && 'yes' === $deposit_enable ) {
                $cart_item['data']->set_price( $sub_deposit_amount / $quantity );
            }
        } // End foreach
    }
}

/**
 * Get Price a product with Pick-up date, Drop-off date
 * @param  [type] $product_id
 * @param  [strtotime] $ovabrw_pickup_date
 * @param  [strtotime] $ovabrw_pickoff_date
 * @return $line_total
 */
if ( ! function_exists( 'get_price_by_guests' ) ) {
    function get_price_by_guests( $product_id = false, $ovabrw_pickup_date = '', $ovabrw_pickoff_date = '', $cart_item = [] ) {
        // Get New Date to match per product
        $date_format    = ovabrw_get_date_format();
        $new_date       = ovabrw_new_input_date( $product_id, $ovabrw_pickup_date, $ovabrw_pickoff_date, $date_format );
        $pickup_date    = $new_date['pickup_date_new'];
        $pickoff_date   = $new_date['pickoff_date_new'];

        // Adults Quantity
        $adults_quantity = absint( get_post_meta( $product_id, 'ovabrw_adults_min', true ) );
        if ( ovabrw_check_array( $cart_item, 'ovabrw_adults' ) ) {
            $adults_quantity = absint( $cart_item['ovabrw_adults'] );
        }

        // Childrens Quantity
        $childrens_quantity  = absint( get_post_meta( $product_id, 'ovabrw_childrens_min', true ) );
        if ( ovabrw_check_array( $cart_item, 'ovabrw_childrens' ) ) {
            $childrens_quantity = absint( $cart_item['ovabrw_childrens'] );
        }

        // Babies Quantity
        $babies_quantity  = absint( get_post_meta( $product_id, 'ovabrw_babies_min', true ) );
        if ( ovabrw_check_array( $cart_item, 'ovabrw_babies' ) ) {
            $babies_quantity = absint( $cart_item['ovabrw_babies'] );
        }

        // Time From
        $time_from = '';

        if ( ovabrw_check_array( $cart_item, 'ovabrw_time_from' ) ) {
            $time_from = $cart_item['ovabrw_time_from'];
        }

        // Global price
        $line_total = ovabrw_price_global( $product_id, $pickup_date, $pickoff_date, $adults_quantity, $childrens_quantity, $babies_quantity, $time_from );

        $ovabrw_amount_insurance = floatval( get_post_meta( $product_id, 'ovabrw_amount_insurance', true ) );
        $line_total += $ovabrw_amount_insurance * ( $adults_quantity + $childrens_quantity + $babies_quantity );

        // Resources
        if ( ovabrw_check_array( $cart_item, 'ovabrw_resources' ) ) {
            $resources          = $cart_item['ovabrw_resources'];
            $total_resources    = ovabrw_get_total_resoures( $product_id, $resources, $adults_quantity, $childrens_quantity, $babies_quantity );
            $line_total         += $total_resources;
        }

        // Services
        if ( ovabrw_check_array( $cart_item, 'ovabrw_services' ) ) {
            $services       = $cart_item['ovabrw_services'];
            $total_services = ovabrw_get_total_services( $product_id, $services, $adults_quantity, $childrens_quantity, $babies_quantity );
            $line_total     += $total_services;
        }

        // Quantity
        $ovabrw_quantity = 1;
        if ( ovabrw_check_array( $cart_item, 'ovabrw_quantity' ) ) {
            $ovabrw_quantity = absint( $cart_item['ovabrw_quantity'] );
        }

        $line_total *= $ovabrw_quantity;

        // Custom Checkout Fields
        if ( isset( $cart_item['custom_ckf'] ) ) {
            $price_ckf = ovabrw_get_price_ckf( $product_id, $cart_item['custom_ckf'] );

            if ( $price_ckf ) {
                $line_total += $price_ckf;
            }
        }

        return floatval( $line_total );
    }
}

/* Get Price Per Guests */
if ( ! function_exists( 'ovabrw_price_per_guests' ) ) {
    function ovabrw_price_per_guests( $product_id = false, $pickup_date = '', $adults_quantity = 0, $childrens_quantity = 0, $babies_quantity = 0, $time_from = '' ) {
        // Adults price
        $adults_price       = ovabrw_regular_price_global( $product_id, $pickup_date );
        $childrens_price    = floatval( get_post_meta( $product_id, 'ovabrw_children_price', true ) );
        $babies_price       = floatval( get_post_meta( $product_id, 'ovabrw_baby_price', true ) );

        if ( ! $childrens_price ) {
            $childrens_price = 0;
        }

        if ( ! $babies_price ) {
            $babies_price = 0;
        }

        // Duration
        $duration = get_post_meta( $product_id, 'ovabrw_duration_checkbox', true );
        
        if ( $duration && $time_from ) {
            $weekday = ovabrw_get_weekday( $pickup_date );

            $schedule_price = ovabrw_get_price_from_schedule( $product_id, $weekday, $time_from );

            if ( $schedule_price ) {
                $adults_price       = $schedule_price['adults_price'];
                $childrens_price    = $schedule_price['childrens_price'];
                $babies_price       = $schedule_price['babies_price'];
            }
        }

        // Global Discount (GD)
        $gd_prices = ovabrw_get_price_by_global_discount( $product_id, $adults_quantity, $childrens_quantity, $babies_quantity );

        if ( $gd_prices && is_array( $gd_prices ) ) {
            $adults_price       = $gd_prices['adults_price'];
            $childrens_price    = $gd_prices['childrens_price'];
            $babies_price       = $gd_prices['babies_price'];
        }

        // Special Time (ST)
        $st_prices = ovabrw_get_price_by_special_time( $product_id, $pickup_date, $adults_quantity, $childrens_quantity, $babies_quantity );
        if ( $st_prices && is_array( $st_prices ) ) {
            $adults_price       = $st_prices['adults_price'];
            $childrens_price    = $st_prices['childrens_price'];
            $babies_price       = $st_prices['babies_price'];
        }

        $price_guests = array(
            'adults_price'      => $adults_price,
            'childrens_price'   => $childrens_price,
            'babies_price'      => $babies_price,
        );

        return $price_guests;
    }
}

/* Get Price in Global */
if ( ! function_exists( 'ovabrw_price_global' ) ) {
    function ovabrw_price_global( $product_id = false, $pickup_date = '', $pickoff_date = '', $adults_quantity = 0, $childrens_quantity = 0, $babies_quantity = 0, $time_from = '' ) {
        // Adults price
        $adults_price       = ovabrw_regular_price_global( $product_id, $pickup_date );
        $childrens_price    = floatval( get_post_meta( $product_id, 'ovabrw_children_price', true ) );
        $babies_price       = floatval( get_post_meta( $product_id, 'ovabrw_baby_price', true ) );

        if ( ! $childrens_price ) {
            $childrens_price = 0;
        }

        if ( ! $babies_price ) {
            $babies_price = 0;
        }

        // Duration
        $duration   = get_post_meta( $product_id, 'ovabrw_duration_checkbox', true );
        $type_price = '';
        
        if ( $duration && $time_from ) {
            $weekday = ovabrw_get_weekday( $pickup_date );

            $schedule_price = ovabrw_get_price_from_schedule( $product_id, $weekday, $time_from );

            if ( $schedule_price ) {
                $adults_price       = $schedule_price['adults_price'];
                $childrens_price    = $schedule_price['childrens_price'];
                $babies_price       = $schedule_price['babies_price'];
                $type_price         = $schedule_price['type_price'];
            }
        }

        // Global Discount (GD)
        $gd_prices = ovabrw_get_price_by_global_discount( $product_id, $adults_quantity, $childrens_quantity, $babies_quantity );
        if ( $gd_prices && is_array( $gd_prices ) ) {
            $adults_price       = $gd_prices['adults_price'];
            $childrens_price    = $gd_prices['childrens_price'];
            $babies_price       = $gd_prices['babies_price'];
        }

        // Special Time (ST)
        $st_prices = ovabrw_get_price_by_special_time( $product_id, $pickup_date, $adults_quantity, $childrens_quantity, $babies_quantity );
        if ( $st_prices && is_array( $st_prices ) ) {
            $adults_price       = $st_prices['adults_price'];
            $childrens_price    = $st_prices['childrens_price'];
            $babies_price       = $st_prices['babies_price'];
        }

        $total = $adults_price * $adults_quantity + $childrens_price * $childrens_quantity + $babies_price * $babies_quantity;

        if ( $type_price === 'total' ) {
            $total = 0;

            if ( $adults_quantity ) {
                $total += $adults_price;
            }

            if ( $childrens_quantity ) {
                $total += $childrens_price;
            }

            if ( $babies_quantity ) {
                $total += $babies_price;
            }
        }

        return floatval( $total );
    }
}

/* Get Sale Price in Global */
if ( ! function_exists( 'ovabrw_regular_price_global' ) ) {
    function ovabrw_regular_price_global( $product_id, $pickup_date ) {
        // Regular Price
        $regular_price = get_post_meta( $product_id, '_regular_price', true );

        if ( ovabrw_wcml_get_product_price( $product_id, '_regular_price' ) ) {
            $regular_price = ovabrw_wcml_get_product_price( $product_id, '_regular_price' );
        }

        // Sale Price
        $sale_price = get_post_meta( $product_id, '_sale_price', true );

        if ( ovabrw_wcml_get_product_price( $product_id, '_sale_price' ) ) {
            $sale_price = ovabrw_wcml_get_product_price( $product_id, '_sale_price' );
        }
        
        if ( $sale_price ) {
            // Sale date
            $sale_from  = absint( get_post_meta( $product_id, '_sale_price_dates_from', true ) );
            $sale_to    = absint( get_post_meta( $product_id, '_sale_price_dates_to', true ) );

            if ( $sale_from && $sale_to ) {
                if ( $sale_from <= $pickup_date && $pickup_date <= $sale_to ) {
                    $regular_price = $sale_price;
                }
            } else if ( $sale_from && !$sale_to ) {
                if ( $sale_from <= $pickup_date ) {
                    $regular_price = $sale_price;
                }
            } else if ( !$sale_from && $sale_to ) {
                if ( $pickup_date <= $sale_to ) {
                    $regular_price = $sale_price;
                }
            } else {
                $regular_price = $sale_price;
            }
        }

        if ( ! $regular_price ) {
            $regular_price = 0;
        }

        return floatval( $regular_price );
    }
}

// Get Price in Global Discount (GD)
if ( ! function_exists( 'ovabrw_get_price_by_global_discount' ) ) {
    function ovabrw_get_price_by_global_discount( $product_id = false, $adults_quantity = 0, $children_quantity = 0, $babies_quantity = 0 ) {
        // Before
        add_action( 'ovabrw_before_get_total_guests_by_global_discount', $product_id, $adults_quantity, $children_quantity, $babies_quantity );

        $ovabrw_gd_duration_min = get_post_meta( $product_id, 'ovabrw_gd_duration_min', true );

        if ( $ovabrw_gd_duration_min && is_array( $ovabrw_gd_duration_min ) ) {
            asort( $ovabrw_gd_duration_min );

            // Guests Total
            $guests_total = $adults_quantity + $children_quantity + $babies_quantity;
            $guests_total = apply_filters( 'ovabrw_get_total_guests_by_global_discount', $guests_total, $product_id, $adults_quantity, $children_quantity, $babies_quantity );

            foreach ( $ovabrw_gd_duration_min as $key => $duration_min ) {
                $ovabrw_gd_duration_max      = get_post_meta( $product_id, 'ovabrw_gd_duration_max', true );
                $ovabrw_gd_adult_price       = get_post_meta( $product_id, 'ovabrw_gd_adult_price', true );
                $ovabrw_gd_children_price    = get_post_meta( $product_id, 'ovabrw_gd_children_price', true );
                $ovabrw_gd_baby_price        = get_post_meta( $product_id, 'ovabrw_gd_baby_price', true );

                // Duration Max Number
                $gd_duration_max = 0;
                if ( isset( $ovabrw_gd_duration_max[$key] ) && $ovabrw_gd_duration_max[$key] ) {
                    $gd_duration_max = floatval( $ovabrw_gd_duration_max[$key] );
                }

                // Discount Adult Price
                $gd_adult_price = 0;
                if ( isset( $ovabrw_gd_adult_price[$key] ) && $ovabrw_gd_adult_price[$key] ) {
                    $gd_adult_price = floatval( $ovabrw_gd_adult_price[$key] );
                }

                // Discount Children Price
                $gd_children_price = 0;
                if ( isset( $ovabrw_gd_children_price[$key] ) && $ovabrw_gd_children_price[$key] ) {
                    $gd_children_price = floatval( $ovabrw_gd_children_price[$key] );
                }

                // Discount Baby Price
                $gd_baby_price = 0;
                if ( isset( $ovabrw_gd_baby_price[$key] ) && $ovabrw_gd_baby_price[$key] ) {
                    $gd_baby_price = floatval( $ovabrw_gd_baby_price[$key] );
                }

                if ( $guests_total >= $duration_min && $guests_total <= $gd_duration_max ){
                    $gd_prices = array(
                        'adults_price'      => $gd_adult_price,
                        'childrens_price'   => $gd_children_price,
                        'babies_price'      => $gd_baby_price,
                    );

                    return $gd_prices;
                }
            }
        }

        // After
        add_action( 'ovabrw_after_get_total_guests_by_global_discount', $product_id, $adults_quantity, $children_quantity, $babies_quantity );

        return false;
    }
}

/* Get Price Product */
if ( ! function_exists( 'ovabrw_get_price_product' ) ) {
    function ovabrw_get_price_product( $product_id ) {
        $product        = wc_get_product( $product_id );
        $regular_price  = 0;

        if ( $product->is_on_sale() && $product->get_sale_price() ) {
            $regular_price  = $product->get_sale_price();
            $sale_price     = $product->get_regular_price();
        } else {
            $regular_price = $product->get_regular_price();
        }

        return $regular_price;
    }
}

/* Get Price in Special Time (ST) */
if ( ! function_exists( 'ovabrw_get_price_by_special_time' ) ) {
    function ovabrw_get_price_by_special_time( $product_id = false, $pickup_date = '', $adults_quantity = 0, $childrens_quantity = 0, $babies_quantity = 0 ) {

        // Before
        add_action( 'ovabrw_before_get_price_by_special_time', $product_id, $pickup_date, $adults_quantity, $childrens_quantity, $babies_quantity );

        $date_format = ovabrw_get_date_format();
        $pickup_date = strtotime( date_i18n( $date_format, $pickup_date ) );

        if ( ! $pickup_date ) return false;

        $st_prices = array();
        $ovabrw_st_startdate = get_post_meta( $product_id, 'ovabrw_st_startdate', true );

        if ( $ovabrw_st_startdate && is_array( $ovabrw_st_startdate ) ) {
            // Guests Total
            $guests_total               = $adults_quantity + $childrens_quantity + $babies_quantity;

            // ST
            $ovabrw_st_enddate          = get_post_meta( $product_id, 'ovabrw_st_enddate', true );
            $ovabrw_st_adult_price      = get_post_meta( $product_id, 'ovabrw_st_adult_price', true );
            $ovabrw_st_children_price   = get_post_meta( $product_id, 'ovabrw_st_children_price', true );
            $ovabrw_st_baby_price       = get_post_meta( $product_id, 'ovabrw_st_baby_price', true );
            $ovabrw_st_discount         = get_post_meta( $product_id, 'ovabrw_st_discount', true );

            foreach( $ovabrw_st_startdate as $key => $start_date ) {
                // Start date
                if ( ovabrw_check_array( $ovabrw_st_startdate, $key ) ) {
                    $start_date = strtotime( $ovabrw_st_startdate[$key] );
                }

                // End date
                $end_date = '';
                if ( ovabrw_check_array( $ovabrw_st_enddate, $key ) ) {
                    $end_date = strtotime( $ovabrw_st_enddate[$key] );
                }

                // Adult Price
                $adults_price = 0;
                if ( ovabrw_check_array( $ovabrw_st_adult_price, $key ) ) {
                    $adults_price = floatval( $ovabrw_st_adult_price[$key] );
                }

                // Children Price
                $childrens_price = 0;
                if ( ovabrw_check_array( $ovabrw_st_children_price, $key ) ) {
                    $childrens_price = floatval( $ovabrw_st_children_price[$key] );
                }

                // Baby Price
                $babies_price = 0;
                if ( ovabrw_check_array( $ovabrw_st_baby_price, $key ) ) {
                    $babies_price = floatval( $ovabrw_st_baby_price[$key] );
                }

                // Discounts
                $discount = array();
                if ( ovabrw_check_array( $ovabrw_st_discount, $key ) ) {
                    $discount = $ovabrw_st_discount[$key];
                }

                if ( $start_date && $end_date ) {
                    if ( $pickup_date >= $start_date && $pickup_date <= $end_date ) {
                        $st_prices = array(
                            'adults_price'      => $adults_price,
                            'childrens_price'   => $childrens_price,
                            'babies_price'      => $babies_price,
                        );

                        if ( $discount && is_array( $discount ) ) {
                            $dsc_min = $dsc_max = $dsc_adult_price = $dsc_children_price = $dsc_baby_price = array();
                            if ( ovabrw_check_array( $discount, 'min' ) ) {
                                $dsc_min = $discount['min'];
                            }

                            if ( ovabrw_check_array( $discount, 'max' ) ) {
                                $dsc_max = $discount['max'];
                            }

                            if ( ovabrw_check_array( $discount, 'adult_price' ) ) {
                                $dsc_adult_price = $discount['adult_price'];
                            }

                            if ( ovabrw_check_array( $discount, 'children_price' ) ) {
                                $dsc_children_price = $discount['children_price'];
                            }

                            if ( ovabrw_check_array( $discount, 'baby_price' ) ) {
                                $dsc_baby_price = $discount['baby_price'];
                            }

                            if ( $dsc_min && is_array( $dsc_min ) ) {
                                foreach( $dsc_min as $dsc_key => $dsc_min_number ) {
                                    $dsc_min_number = absint( $dsc_min_number );

                                    $dsc_max_number = 0;
                                    if ( ovabrw_check_array( $dsc_max, $dsc_key ) ) {
                                        $dsc_max_number = absint( $dsc_max[$dsc_key] );
                                    }

                                    $dsc_adult_amount = 0;
                                    if ( ovabrw_check_array( $dsc_adult_price, $dsc_key ) ) {
                                        $dsc_adult_amount = floatval( $dsc_adult_price[$dsc_key] );
                                    }

                                    $dsc_children_amount = 0;
                                    if ( ovabrw_check_array( $dsc_children_price, $dsc_key ) ) {
                                        $dsc_children_amount = floatval( $dsc_children_price[$dsc_key] );
                                    }

                                    $dsc_baby_amount = 0;
                                    if ( ovabrw_check_array( $dsc_baby_price, $dsc_key ) ) {
                                        $dsc_baby_amount = floatval( $dsc_baby_price[$dsc_key] );
                                    }

                                    if ( $guests_total >= $dsc_min_number && $guests_total <= $dsc_max_number  ) {
                                        $st_prices = array(
                                            'adults_price'      => $dsc_adult_amount,
                                            'childrens_price'   => $dsc_children_amount,
                                            'babies_price'      => $dsc_baby_amount,
                                        );
                                    }
                                }
                            }
                        }

                        return $st_prices;
                    }
                }
            }
        }

        // After
        add_action( 'ovabrw_after_get_price_by_special_time', $product_id, $pickup_date, $adults_quantity, $childrens_quantity, $babies_quantity );

        return false;
    }
}

/* Get Price in Resources */
if ( ! function_exists( 'ovabrw_get_total_resoures' ) ) {
    function ovabrw_get_total_resoures( $product_id = false, $ovabrw_resources = array(), $adults_quantity = 0, $childrens_quantity = 0, $babies_quantity = 0 ) {
        $total_resources = 0;

        if ( $ovabrw_resources && is_array( $ovabrw_resources ) ) {
            $rs_ids             = get_post_meta( $product_id, 'ovabrw_rs_id', true );
            $rs_names           = get_post_meta( $product_id, 'ovabrw_rs_name', true );
            $rs_adult_price     = get_post_meta( $product_id, 'ovabrw_rs_adult_price', true );
            $rs_children_price  = get_post_meta( $product_id, 'ovabrw_rs_children_price', true );
            $rs_baby_price      = get_post_meta( $product_id, 'ovabrw_rs_baby_price', true );
            $rs_duration_type   = get_post_meta( $product_id, 'ovabrw_rs_duration_type', true );

            foreach( $ovabrw_resources as $rs_id => $rs_name ) {
                $key = array_search( $rs_id, $rs_ids );

                if ( !is_bool( $key ) ) {
                    $adult_price = 0;
                    if ( ovabrw_check_array( $rs_adult_price, $key ) ) {
                        $adult_price = $rs_adult_price[$key];
                    }

                    $children_price = 0;
                    if ( ovabrw_check_array( $rs_children_price, $key ) ) {
                        $children_price = $rs_children_price[$key];
                    }

                    $baby_price = 0;
                    if ( ovabrw_check_array( $rs_baby_price, $key ) ) {
                        $baby_price = $rs_baby_price[$key];
                    }

                    $duration_type = 'person';
                    if ( ovabrw_check_array( $rs_duration_type, $key ) ) {
                        $duration_type = $rs_duration_type[$key];
                    }

                    if ( 'person' === $duration_type ) {
                        $total_resources += $adult_price * $adults_quantity + $children_price * $childrens_quantity + $baby_price * $babies_quantity;
                    } else {
                        $total_resources += $adult_price + $children_price + $baby_price;
                    }
                }
            }
        }

        return floatval( $total_resources );
    }
}

/* Get Price in Services */
if ( ! function_exists( 'ovabrw_get_total_services' ) ) {
    function ovabrw_get_total_services( $product_id = false, $ovabrw_services = array(), $adults_quantity = 0, $childrens_quantity = 0, $babies_quantity = 0 ) {
        $total_service = 0;

        if ( $ovabrw_services && is_array( $ovabrw_services ) ) {
            $service_ids            = get_post_meta( $product_id, 'ovabrw_service_id', true );
            $service_adult_price    = get_post_meta( $product_id, 'ovabrw_service_adult_price', true );
            $service_children_price = get_post_meta( $product_id, 'ovabrw_service_children_price', true );
            $service_baby_price     = get_post_meta( $product_id, 'ovabrw_service_baby_price', true );
            $service_duration_type  = get_post_meta( $product_id, 'ovabrw_service_duration_type', true );

            foreach( $ovabrw_services as $ovabrw_s_id ) {
                if ( $ovabrw_s_id && $service_ids && is_array( $service_ids ) ) {
                    foreach( $service_ids as $key_id => $service_id_arr ) {
                        $key = array_search( $ovabrw_s_id, $service_id_arr );

                        if ( !is_bool( $key ) ) {
                            $adult_price = 0;
                            if ( ovabrw_check_array( $service_adult_price, $key_id ) ) {
                                if ( ovabrw_check_array( $service_adult_price[$key_id], $key ) ) {
                                    $adult_price = $service_adult_price[$key_id][$key];
                                }
                            }

                            $children_price = 0;
                            if ( ovabrw_check_array( $service_children_price, $key_id ) ) {
                                if ( ovabrw_check_array( $service_children_price[$key_id], $key ) ) {
                                    $children_price = $service_children_price[$key_id][$key];
                                }
                            }

                            $baby_price = 0;
                            if ( ovabrw_check_array( $service_baby_price, $key_id ) ) {
                                if ( ovabrw_check_array( $service_baby_price[$key_id], $key ) ) {
                                    $baby_price = $service_baby_price[$key_id][$key];
                                }
                            }

                            $duration_type = 'person';
                            if ( ovabrw_check_array( $service_duration_type, $key_id ) ) {
                                if ( ovabrw_check_array( $service_duration_type[$key_id], $key ) ) {
                                    $duration_type = $service_duration_type[$key_id][$key];
                                }
                            }

                            if ( 'person' === $duration_type ) {
                                $total_service += $adult_price * $adults_quantity + $children_price * $childrens_quantity + $baby_price * $babies_quantity;
                            } else {
                                $total_service += $adult_price + $children_price + $baby_price;
                            }
                        }
                    }
                }
            }
        }

        return $total_service;
    }
}

/* Get Price in Schedule */
if ( ! function_exists( 'ovabrw_get_price_from_schedule' ) ) {
    function ovabrw_get_price_from_schedule( $product_id = false, $weekday = '', $time_from = '' ) {
        if ( ! $product_id || ! $weekday || ! $time_from ) return false;

        $price_guests = array(
            'adults_price'      => 0,
            'childrens_price'   => 0,
            'babies_price'      => 0,
            'type_price'        => 'person'
        );

        $ovabrw_schedule_time              = get_post_meta( $product_id, 'ovabrw_schedule_time', true );
        $ovabrw_schedule_adult_price       = get_post_meta( $product_id, 'ovabrw_schedule_adult_price', true );
        $ovabrw_schedule_children_price    = get_post_meta( $product_id, 'ovabrw_schedule_children_price', true );
        $ovabrw_schedule_baby_price        = get_post_meta( $product_id, 'ovabrw_schedule_baby_price', true );
        $ovabrw_schedule_type              = get_post_meta( $product_id, 'ovabrw_schedule_type', true );

        if ( ovabrw_check_array( $ovabrw_schedule_time, $weekday ) ) {
            $schedule_time              = $ovabrw_schedule_time[$weekday];
            $schedule_adult_price       = isset( $ovabrw_schedule_adult_price[$weekday] ) ? $ovabrw_schedule_adult_price[$weekday] : array();
            $schedule_children_price    = isset( $ovabrw_schedule_children_price[$weekday] ) ? $ovabrw_schedule_children_price[$weekday] : array();
            $schedule_baby_price        = isset( $ovabrw_schedule_baby_price[$weekday] ) ? $ovabrw_schedule_baby_price[$weekday] : array();
            $schedule_type              = isset( $ovabrw_schedule_type[$weekday] ) ? $ovabrw_schedule_type[$weekday] : array();

            if ( ! empty( $schedule_time ) && is_array( $schedule_time ) ) {
                foreach ( $schedule_time as $k => $time ) {
                    if ( $time === $time_from ) {
                        $price_guests['adults_price']       = isset( $schedule_adult_price[$k] ) ? floatval( $schedule_adult_price[$k] ) : 0;
                        $price_guests['childrens_price']    = isset( $schedule_children_price[$k] ) ? floatval( $schedule_children_price[$k] ) : 0;
                        $price_guests['babies_price']       = isset( $schedule_baby_price[$k] ) ? floatval( $schedule_baby_price[$k] ) : 0;
                        $price_guests['type_price']         = isset( $schedule_type[$k] ) ? $schedule_type[$k] : 'person';

                        break;
                    }
                }
            }
        }

        return $price_guests;
    }
}

// Get Price Custom Checkout Fields
if ( ! function_exists( 'ovabrw_get_price_ckf' ) ) {
    function ovabrw_get_price_ckf( $product_id, $args_ckf ) {
        if ( ! $product_id || empty( $args_ckf ) ) return 0;

        $price = 0;
        $list_custom_ckf = ovabrw_get_list_field_checkout( $product_id );

        foreach ( $args_ckf as $key => $val ) {
            if ( isset( $list_custom_ckf[$key] ) && ! empty( $list_custom_ckf[$key] ) ) {
                $type = $list_custom_ckf[$key]['type'];

                if ( ! $type || ! in_array( $type, array( 'radio', 'select', 'checkbox' ) ) ) continue;

                if ( $type === 'radio' && isset( $list_custom_ckf[$key]['ova_radio_values'] ) && $list_custom_ckf[$key]['ova_radio_values'] ) {
                    foreach ( $list_custom_ckf[$key]['ova_radio_values'] as $k => $v ) {
                        if ( $val === $v && isset( $list_custom_ckf[$key]['ova_radio_prices'][$k] ) ) {
                            $price += floatval( $list_custom_ckf[$key]['ova_radio_prices'][$k] );

                            break;
                        }
                    }
                }

                if ( $type === 'select' && isset( $list_custom_ckf[$key]['ova_options_key'] ) && $list_custom_ckf[$key]['ova_options_key'] ) {
                    foreach ( $list_custom_ckf[$key]['ova_options_key'] as $k => $v ) {
                        if ( $val === $v && isset( $list_custom_ckf[$key]['ova_options_price'][$k] ) ) {
                            $price += floatval( $list_custom_ckf[$key]['ova_options_price'][$k] );

                            break;
                        }
                    }
                }

                if ( $type === 'checkbox' && ! empty( $val ) && is_array( $val ) ) {
                    $checkbox_key   = isset( $list_custom_ckf[$key]['ova_checkbox_key'] ) ? $list_custom_ckf[$key]['ova_checkbox_key'] : '';
                    $checkbox_price = isset( $list_custom_ckf[$key]['ova_checkbox_price'] ) ? $list_custom_ckf[$key]['ova_checkbox_price'] : '';
                    if ( ! empty( $checkbox_key ) && ! empty( $checkbox_price ) ) {
                        foreach ( $val as $val_cb ) {
                            $key_cb = array_search( $val_cb, $checkbox_key );

                            if ( ! is_bool( $key_cb ) ) {
                                if ( ovabrw_check_array( $checkbox_price, $key_cb ) ) {
                                    $price += floatval( $checkbox_price[$key_cb] );
                                }
                            }
                        }
                    }
                }
            }
        }

        return floatval( $price );
    }
}