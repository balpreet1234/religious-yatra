<?php if ( ! defined( 'ABSPATH' ) ) exit();

if ( ! class_exists( 'Ovabrw_Model' ) ) {
    class Ovabrw_Model {
        public function __construct(){
            // Create new order manually
            add_action( 'admin_init', array( $this, 'ovabrw_create_new_order_manully' ) );
        }

        public function ovabrw_get_address() {
            $first_name         = isset( $_POST['ovabrw_first_name'] )  ? sanitize_text_field( $_POST['ovabrw_first_name'] )    : '';
            $last_name          = isset( $_POST['ovabrw_last_name'] )   ? sanitize_text_field( $_POST['ovabrw_last_name'] )     : '';
            $company            = isset( $_POST['ovabrw_company'] )     ? sanitize_text_field( $_POST['ovabrw_company'] )       : '';
            $email              = isset( $_POST['ovabrw_email'] )       ? sanitize_text_field( $_POST['ovabrw_email'] )         : '';
            $phone              = isset( $_POST['ovabrw_phone'] )       ? sanitize_text_field( $_POST['ovabrw_phone'] )         : '';
            $address_1          = isset( $_POST['ovabrw_address_1'] )   ? sanitize_text_field( $_POST['ovabrw_address_1'] )     : '';
            $address_2          = isset( $_POST['ovabrw_address_2'] )   ? sanitize_text_field( $_POST['ovabrw_address_2'] )     : '';
            $city               = isset( $_POST['ovabrw_city'] )        ? sanitize_text_field( $_POST['ovabrw_city'] )          : '';
            $country_setting    = isset( $_POST['ovabrw_country'] )     ? sanitize_text_field( $_POST['ovabrw_country'] )       : 'US';

            if ( strstr( $country_setting, ':' ) ) {
                $country_setting = explode( ':', $country_setting );
                $country         = current( $country_setting );
                $state           = end( $country_setting );
            } else {
                $country = $country_setting;
                $state   = '*';
            }

            $data_address = array(
                'first_name' => $first_name,
                'last_name'  => $last_name,
                'company'    => $company,
                'email'      => $email,
                'phone'      => $phone,
                'address_1'  => $address_1,
                'address_2'  => $address_2,
                'city'       => $city,
                'country'    => $country,
            );

            return apply_filters( 'ovabrw_ft_get_address', $data_address );
        }

        public function ovabrw_get_tax_default() {

            $tax_rates = array();

            if ( wc_tax_enabled() ) {

                $country_setting = isset( $_POST['ovabrw_country'] ) ? sanitize_text_field( $_POST['ovabrw_country'] ) : 'US';

                if ( strstr( $country_setting, ':' ) ) {
                    $country_setting = explode( ':', $country_setting );
                    $country         = current( $country_setting );
                    $state           = end( $country_setting );
                } else {
                    $country = $country_setting;
                    $state   = '*';
                }

                $tax_rates = WC_Tax::find_rates(
                    array(
                        'country'   => $country,
                        'state'     => $state,
                        'postcode'  => '',
                        'city'      => '',
                        'tax_class' => '',
                    )
                );
            }

            return $tax_rates;
        }

        protected function ovabrw_get_total_taxed_enable( $products ,$price ) {

            if ( empty( $products ) || ! $price ) {
                return 0;
            }

            $tax_rates = $this->ovabrw_get_tax_default();

            if ( empty( $tax_rates ) ) {
                
                $tax_rates = WC_Tax::get_rates( $products->get_tax_class() );

                if ( empty( $tax_rates ) ) {
                    return apply_filters( 'ovabrw_ft_get_total_taxed_enable', $price );
                }
            }

            if ( wc_tax_enabled() ) {

                if ( ! wc_prices_include_tax() ) {

                    $excl_tax = WC_Tax::calc_exclusive_tax( $price, $tax_rates );
                    $price   += round( array_sum( $excl_tax ), wc_get_price_decimals() );

                }
            }

            return apply_filters( 'ovabrw_ft_get_total_taxed_enable', $price );
        }

        protected function ovabrw_get_item_total_taxed_enable( $products ,$price ) {

            if ( empty( $products ) || ! $price ) {
                return 0;
            }

            $tax_rates = $this->ovabrw_get_tax_default();

            if ( empty( $tax_rates ) ) {
                
                $tax_rates = WC_Tax::get_rates( $products->get_tax_class() );

                if ( empty( $tax_rates ) ) {
                    return apply_filters( 'ovabrw_ft_get_item_total_taxed_enable', $price );
                }
            }

            if ( wc_tax_enabled() ) {

                if ( wc_prices_include_tax() ) {

                    if ( get_option( 'woocommerce_tax_display_cart' ) != 'incl' ) {
                        $incl_tax = WC_Tax::calc_inclusive_tax( $price, $tax_rates );
                        $price   -= round( array_sum( $incl_tax ), wc_get_price_decimals() );
                    }

                } else {

                    if ( get_option( 'woocommerce_tax_display_cart' ) === 'incl' ) {
                        $excl_tax = WC_Tax::calc_exclusive_tax( $price, $tax_rates );
                        $price   += round( array_sum( $excl_tax ), wc_get_price_decimals() );
                    }

                }

            }

            return apply_filters( 'ovabrw_ft_get_item_total_taxed_enable', $price );
        }

        protected function ovabrw_get_product_price_taxed_enable( $products ,$price ) {

            if ( empty( $products ) || ! $price ) {
                return 0;
            }

            $tax_rates = $this->ovabrw_get_tax_default();

            if ( empty( $tax_rates ) ) {

                $tax_rates = WC_Tax::get_rates( $products->get_tax_class() );

                if ( empty( $tax_rates ) ) {
                    return apply_filters( 'ovabrw_ft_get_product_price_taxed_enable', $price );
                }
            }

            if ( wc_tax_enabled() ) {

                if ( wc_prices_include_tax() ) {

                    $incl_tax = WC_Tax::calc_inclusive_tax( $price, $tax_rates );
                    $price   -= round( array_sum( $incl_tax ), wc_get_price_decimals() );

                }
            }

            return apply_filters( 'ovabrw_ft_get_product_price_taxed_enable', $price );
        }

        protected function ovabrw_get_tax_amount_taxed_enable( $products ,$price ) {

            $tax_amount = 0;

            if ( ! $products || ! $price ) {
                return $tax_amount;
            }

            $tax_rates = $this->ovabrw_get_tax_default();

            if ( empty( $tax_rates ) ) {
                
                $tax_rates = WC_Tax::get_rates( $products->get_tax_class() );

                if ( empty( $tax_rates ) ) {
                    return apply_filters( 'ovabrw_ft_get_tax_amount_taxed_enable', $tax_amount );
                }
            }

            if ( wc_tax_enabled() ) {

                if ( wc_prices_include_tax() ) {

                    $incl_tax   = WC_Tax::calc_inclusive_tax( $price, $tax_rates );
                    $tax_amount = round( array_sum( $incl_tax ), wc_get_price_decimals() );

                } else {

                    $excl_tax   = WC_Tax::calc_exclusive_tax( $price, $tax_rates );
                    $tax_amount = round( array_sum( $excl_tax ), wc_get_price_decimals() );
                }

            }

            return apply_filters( 'ovabrw_ft_get_tax_amount_taxed_enable', $tax_amount );
        }

        public function ovabrw_add_order_item( $order_id ) {
            $data = $_POST;

            // order data
            $data_order = array();

            if ( ! $order_id ) {
                return $data_order;
            }

            $order = wc_get_order( $order_id ); // Get order

            // Get order items
            $has_deposit = $item_has_deposit = false;
            $order_items = $order->get_items(); 
            $order_total = $total_remaining = $total_deposite = $total_insurance = 0;
            $item_total_remaining = $item_total_deposite = $item_total_insurance = $deposit_full_amount = 0;

            // Tax
            $tax_display_cart   = false;
            $remaining_taxes    = 0;
            if ( wc_tax_enabled() && get_option( 'woocommerce_tax_display_cart' ) === 'incl' ) {
                $tax_display_cart = true;
            }

            // Init $i
            $i = 0;

            // Loop order items
            foreach ( $order_items as $item_id => $product ) {

                $item_total = 0;
                $line_tax   = $data_item = array();

                // Tax
                $tax_rate_id = '';
                $tax_amount  = 0;

                $item = WC_Order_Factory::get_order_item( absint( $item_id ) );

                if ( ! $item ) {
                    continue;
                }

                $product_id = $product['product_id'];
                $products   = wc_get_product( $product_id );

                $time_from = '';
                if ( isset( $data['ovabrw_time_from'] ) && ovabrw_check_array( $data['ovabrw_time_from'], $product_id ) ) {
                    $time_from = $data['ovabrw_time_from'][$product_id];
                }

                $ovabrw_pickup_date = '';
                if ( isset( $data['ovabrw_pickup_date'] ) && ovabrw_check_array( $data['ovabrw_pickup_date'], $i ) ) {
                    $ovabrw_pickup_date = $data['ovabrw_pickup_date'][$i];

                    if ( $time_from ) {
                        $ovabrw_pickup_date .= ' ' . $time_from;
                    }
                }

                $ovabrw_pickoff_date = '';
                if ( isset( $data['ovabrw_pickoff_date'] ) && ovabrw_check_array( $data['ovabrw_pickoff_date'], $i ) ) {
                    $ovabrw_pickoff_date = $data['ovabrw_pickoff_date'][$i];
                }

                $ovabrw_adults = 1;
                if ( isset( $data['ovabrw_adults'] ) && ovabrw_check_array( $data['ovabrw_adults'], $i ) ) {
                    $ovabrw_adults = absint( $data['ovabrw_adults'][$i] );
                }

                $ovabrw_childrens = 0;
                if ( isset( $data['ovabrw_childrens'] ) && ovabrw_check_array( $data['ovabrw_childrens'], $i ) ) {
                    $ovabrw_childrens = absint( $data['ovabrw_childrens'][$i] );
                }

                $ovabrw_babies = 0;
                if ( isset( $data['ovabrw_babies'] ) && ovabrw_check_array( $data['ovabrw_babies'], $i ) ) {
                    $ovabrw_babies = absint( $data['ovabrw_babies'][$i] );
                }

                $ovabrw_total_product = 0;
                if ( isset( $data['ovabrw-total-product'] ) && ovabrw_check_array( $data['ovabrw-total-product'], $i ) ) {
                    $ovabrw_total_product = $data['ovabrw-total-product'][$i];
                }

                $ovabrw_resources = [];
                if ( isset( $data['ovabrw_resource_checkboxs'] ) && ovabrw_check_array( $data['ovabrw_resource_checkboxs'], $product_id ) ) {
                    $ovabrw_resources = $data['ovabrw_resource_checkboxs'][$product_id];
                }

                $ovabrw_services = [];
                if ( isset( $data['ovabrw_service'] ) && ovabrw_check_array( $data['ovabrw_service'], $product_id ) ) {
                    $ovabrw_services = $data['ovabrw_service'][$product_id];
                }

                $item_total_insurance = 0;
                if ( isset( $data['ovabrw_amount_insurance'] ) && ovabrw_check_array( $data['ovabrw_amount_insurance'], $i ) ) {
                    $item_total_insurance = floatval( $data['ovabrw_amount_insurance'][$i] );
                }

                $item_total_deposite = 0;
                if ( isset( $data['ovabrw_amount_deposite'] ) && ovabrw_check_array( $data['ovabrw_amount_deposite'], $i ) ) {
                    $item_total_deposite = floatval( $data['ovabrw_amount_deposite'][$i] );
                }

                $item_total_remaining = 0;
                if ( isset( $data['ovabrw_amount_remaining'] ) && ovabrw_check_array( $data['ovabrw_amount_remaining'], $i ) ) {
                    $item_total_remaining = floatval( $data['ovabrw_amount_remaining'][$i] );
                }

                $total_insurance        += $item_total_insurance;
                $total_deposite         += $this->ovabrw_get_total_taxed_enable( $products, $item_total_deposite );
                $total_remaining        += $this->ovabrw_get_item_total_taxed_enable( $products, $item_total_remaining );

                if ( $item_total_deposite && floatval( $item_total_deposite ) > 0 ) {
                    $has_deposit = $item_has_deposit = true;
                } else {
                    $item_has_deposit = false;
                }

                if ( $item_has_deposit ) {
                    $order_total += $total_deposite;
                    $item_total   = $item_total_deposite;

                    // Deposit full amount
                    $deposit_full_amount = $this->ovabrw_get_item_total_taxed_enable( $products, $ovabrw_total_product );
                } else {
                    $order_total += $this->ovabrw_get_total_taxed_enable( $products, $ovabrw_total_product );
                    $item_total   = $ovabrw_total_product;
                    $deposit_full_amount = 0;
                    $no_has_deposite     = $ovabrw_total_product;
                }
                
                if ( isset( $no_has_deposite ) && $has_deposit ) {
                    $total_deposite += $no_has_deposite;
                }

                // Get remaining taxes
                if ( wc_tax_enabled() ) {
                    $remaining_taxes += $this->ovabrw_get_tax_amount_taxed_enable( $products, $item_total_remaining );

                    // item data
                    $item_total_deposite  = $this->ovabrw_get_item_total_taxed_enable( $products, $item_total_deposite );
                    $item_total_remaining = $this->ovabrw_get_item_total_taxed_enable( $products, $item_total_remaining );

                    $tax_rates = $this->ovabrw_get_tax_default();

                    if ( empty( $tax_rates ) ) {
                        $tax_rates = WC_Tax::get_rates( $products->get_tax_class() );
                    }

                    if ( ! empty( $tax_rates ) ) {
                        $tax_item_id = key($tax_rates);

                        if ( $tax_item_id ) {
                            $tax_amount_item        = $this->ovabrw_get_tax_amount_taxed_enable( $products, $item_total );
                            $line_tax[$tax_item_id] = $tax_amount_item;
                            $tax_rate_id            = $tax_item_id;
                            $tax_amount            += $tax_amount_item;
                            $item_total             = $this->ovabrw_get_product_price_taxed_enable( $products, $item_total );
                        }
                    }
                }
                
                if ( $time_from ) {
                    $data_item[ 'ovabrw_time_from' ] = $time_from;
                }

                $data_item[ 'ovabrw_pickup_date' ]  = $ovabrw_pickup_date;
                $data_item[ 'ovabrw_pickoff_date' ] = $ovabrw_pickoff_date;
                $data_item[ 'ovabrw_adults' ]       = $ovabrw_adults;
                $data_item[ 'ovabrw_childrens' ]    = $ovabrw_childrens;
                $data_item[ 'ovabrw_babies' ]       = $ovabrw_babies;
                $data_item[ 'ovabrw_quantity' ]     = 1;

                if ( $item_total_insurance ) {
                    $data_item[ 'ovabrw_amount_insurance' ] = $item_total_insurance;
                }

                if ( $item_has_deposit ) {
                    $data_item[ 'ovabrw_deposit_amount' ]   = $item_total_deposite;
                    $data_item[ 'ovabrw_remaining_amount' ] = $item_total_remaining;
                }

                if ( $deposit_full_amount ) {
                    $data_item['ovabrw_deposit_full_amount'] = $deposit_full_amount;
                }

                // Custom Checkout Fields
                $list_extra_fields  = ovabrw_get_list_field_checkout( $product_id );;
                $custom_ckf         = array();

                if ( ! empty( $list_extra_fields ) && is_array( $list_extra_fields ) ) {
                    foreach ( $list_extra_fields as $key => $field ) {
                        if ( $field['type'] === 'file' ) {
                            $data_file = isset( $_FILES[$key] ) ? $_FILES[$key] : '';

                            if ( $data_file ) {
                                $files = array();

                                if ( isset( $data_file['name'][$product_id] ) ) {
                                    $files['name'] = $data_file['name'][$product_id];
                                }
                                if ( isset( $data_file['full_path'][$product_id] ) ) {
                                    $files['full_path'] = $data_file['full_path'][$product_id];
                                }
                                if ( isset( $data_file['type'][$product_id] ) ) {
                                    $files['type'] = $data_file['type'][$product_id];
                                }
                                if ( isset( $data_file['tmp_name'][$product_id] ) ) {
                                    $files['tmp_name'] = $data_file['tmp_name'][$product_id];
                                }
                                if ( isset( $data_file['error'][$product_id] ) ) {
                                    $files['error'] = $data_file['error'][$product_id];
                                }
                                if ( isset( $data_file['size'][$product_id] ) ) {
                                    $files['size'] = $data_file['size'][$product_id];
                                }

                                if ( isset( $files['size'] ) && $files['size'] ) {
                                    $mb = absint( $files['size'] ) / 1048576;

                                    if ( $mb > $field['max_file_size'] ) {
                                        continue;
                                    }
                                }

                                $overrides = [
                                    'test_form' => false,
                                    'mimes'     => apply_filters( 'ovabrw_ft_file_mimes', [
                                        'jpg'   => 'image/jpeg',
                                        'jpeg'  => 'image/pjpeg',
                                        'png'   => 'image/png',
                                        'pdf'   => 'application/pdf',
                                        'doc'   => 'application/msword',
                                    ]),
                                ];

                                require_once( ABSPATH . 'wp-admin/includes/admin.php' );

                                $upload = wp_handle_upload( $files, $overrides );

                                if ( isset( $upload['error'] ) ) {
                                    continue;
                                }

                                $data_item[$key] = '<a href="'.esc_url( $upload['url'] ).'" title="'.esc_attr( basename( $upload['file'] ) ).'" target="_blank">'.esc_attr( basename( $upload['file'] ) ).'</a>';
                            }
                        } else {
                            $value = isset( $_POST[$key][$product_id] ) ? $_POST[$key][$product_id] : '';

                            if ( ! empty( $value ) && 'on' === $field['enabled'] ) {
                                if ( 'select' === $field['type'] ) {
                                    $custom_ckf[$key] = sanitize_text_field( $value );

                                    $options_key = $options_text = array();
                                    if ( ovabrw_check_array( $field, 'ova_options_key' ) ) {
                                        $options_key = $field['ova_options_key'];
                                    }

                                    if ( ovabrw_check_array( $field, 'ova_options_text' ) ) {
                                        $options_text = $field['ova_options_text'];
                                    }

                                    $key_op = array_search( $value, $options_key );

                                    if ( ! is_bool( $key_op ) ) {
                                        if ( ovabrw_check_array( $options_text, $key_op ) ) {
                                            $value = $options_text[$key_op];
                                        }
                                    }
                                }

                                if ( 'checkbox' === $field['type'] ) {
                                    $checkbox_val = $checkbox_key = $checkbox_text = array();

                                    if ( ! empty( $value ) && is_array( $value ) ) {
                                        $custom_ckf[$key] = $value;

                                        if ( ovabrw_check_array( $field, 'ova_checkbox_key' ) ) {
                                            $checkbox_key = $field['ova_checkbox_key'];
                                        }

                                        if ( ovabrw_check_array( $field, 'ova_checkbox_text' ) ) {
                                            $checkbox_text = $field['ova_checkbox_text'];
                                        }

                                        foreach ( $value as $val_cb ) {
                                            $key_cb = array_search( $val_cb, $checkbox_key );

                                            if ( ! is_bool( $key_cb ) ) {
                                                if ( ovabrw_check_array( $checkbox_text, $key_cb ) ) {
                                                    array_push( $checkbox_val , $checkbox_text[$key_cb] );
                                                }
                                            }
                                        }

                                        if ( ! empty( $checkbox_val ) && is_array( $checkbox_val ) ) {
                                            $value = join( ", ", $checkbox_val );
                                        }
                                    }
                                }

                                $data_item[$key] = $value;

                                if ( in_array( $field['type'], array( 'radio' ) ) ) {
                                    $custom_ckf[$key] = sanitize_text_field( $value );
                                }
                            }
                        }
                    }
                }

                if ( ! empty( $custom_ckf ) ) {
                    $data_item[ 'ovabrw_custom_ckf' ] = $custom_ckf;
                }

                // Add resource
                if ( ! empty( $ovabrw_resources ) && is_array( $ovabrw_resources ) ) {
                    $resource_ids   = get_post_meta( $product_id, 'ovabrw_rs_id', true );
                    $resource_name  = get_post_meta( $product_id, 'ovabrw_rs_name', true );
                    $data_resources = array();
                    $data_res_name  = array();

                    foreach ( $ovabrw_resources as $rs_id ) {
                        $rs_k = array_search( $rs_id, $resource_ids );

                        if ( ! is_bool( $rs_k ) ) {
                            if ( ovabrw_check_array( $resource_name, $rs_k ) ) {
                                array_push( $data_res_name, $resource_name[$rs_k] );
                                $data_resources[$rs_id] = $resource_name[$rs_k];
                            }
                        }
                    }

                    $data_item['ovabrw_resources'] = $data_resources;

                    if ( count( $data_res_name ) == 1 ) {
                        $data_item[ esc_html__( 'Resource', 'ova-brw' ) ] = join( ', ', $data_res_name );
                    } else {
                        $data_item[ esc_html__( 'Resources', 'ova-brw' ) ] = join( ', ', $data_res_name );
                    }
                }

                if ( $ovabrw_services && is_array( $ovabrw_services ) ) {
                    $data_item['ovabrw_services'] = $ovabrw_services;

                    $service_ids    = get_post_meta( $product_id, 'ovabrw_service_id', true );
                    $service_name   = get_post_meta( $product_id, 'ovabrw_service_name', true );
                    $service_label  = get_post_meta( $product_id, 'ovabrw_label_service', true );

                    foreach( $ovabrw_services as $ovabrw_s_id ) {
                        $label = $name = '';
                        if ( $ovabrw_s_id && $service_ids && is_array( $service_ids ) ) {
                            foreach( $service_ids as $key_id => $service_id_arr ) {
                                $key = array_search( $ovabrw_s_id, $service_id_arr );

                                if ( !is_bool( $key ) ) {
                                    if ( ovabrw_check_array( $service_label, $key_id ) ) {
                                        $label = $service_label[$key_id];
                                    }
                                    if ( ovabrw_check_array( $service_name, $key_id ) ) {
                                        if ( ovabrw_check_array( $service_name[$key_id], $key ) ) {
                                            $name = $service_name[$key_id][$key];
                                        }
                                    }

                                    if ( $label && $name ) {
                                        $data_item[$label] = $name;
                                    }
                                }
                            }
                        }
                    }
                }

                // Add item
                foreach ( $data_item as $meta_key => $meta_value ) {
                    wc_add_order_item_meta( $item_id, $meta_key, $meta_value );
                }

                // Update item meta
                $item->set_props(
                    array(
                        'total'     => $item_total,
                        'subtotal'  => $item_total,
                        'taxes'     => array(
                            'total'    => $line_tax,
                            'subtotal' => $line_tax,
                        ),
                    )
                );

                $item->save();
                // End update item meta

                $i++;
            }

            $data_order = [
                'order_total'       => $order_total,
                'total_remaining'   => $total_remaining,
                'total_deposit'     => $total_deposite,
                'total_insurance'   => $total_insurance,
                'has_deposit'       => $has_deposit,
                'tax_display_cart'  => $tax_display_cart,
                'remaining_taxes'   => $remaining_taxes,
                'tax_rate_id'       => $tax_rate_id,
                'tax_amount'        => $tax_amount,
            ];

            return apply_filters( 'ovabrw_ft_add_order_item', $data_order, $order_id );
        }

        public function ovabrw_create_new_order_manully() {
            $data = $_POST;

            if ( isset( $data['ovabrw_create_order'] ) && $data['ovabrw_create_order'] === 'create_order' ) {

                // Check Permission
                if ( ! current_user_can( apply_filters( 'ovabrw_create_order' ,'publish_posts' ) ) ) {

                    echo    '<div class="notice notice-error is-dismissible">
                                <h2>'.esc_html__( 'You don\'t have permission to create order', 'ova-brw' ).'</h2>
                            </div>';
                    return;
                }

                $data_order_keys = array();

                $order       = wc_create_order(); // Create new order
                $order_id    = $order->get_id(); // Get order id

                // Add product -> Get Total
                // Get array product ids
                $products = isset( $data['ovabrw-data-product'] ) ? $data['ovabrw-data-product'] : [];
                $currency = isset( $_POST['currency'] ) && $_POST['currency'] ? $_POST['currency'] : '';

                if ( $currency ) $order->set_currency( $currency );

                // Check order deposit
                $has_deposit = false;

                if ( ! empty( $products ) ) {
                    foreach( $products as $key => $product ) {
                        $product    = trim( sanitize_text_field( $product ) );
                        $product_id = substr( $product, strpos( $product, '(#' ) );
                        $product_id = str_replace('(#', '', $product_id);
                        $product_id = str_replace(')', '', $product_id);

                        $item_total = 0;
                        if ( ovabrw_check_array( $data['ovabrw-total-product'], $key ) ) {
                            $item_total = floatval( $data['ovabrw-total-product'][$key] );
                        }

                        $item_deposite = 0;
                        if ( ovabrw_check_array( $data['ovabrw_amount_deposite'], $key ) ) {
                            $item_deposite = floatval( $_POST['ovabrw_amount_deposite'][$key] );
                        }

                        if ( $item_deposite && floatval( $item_deposite ) > 0 ) {
                            $item_total = $item_deposite;
                        }

                        if ( wc_tax_enabled() ) {
                            $obj_products   = wc_get_product( $product_id );
                            $item_total     = $this->ovabrw_get_product_price_taxed_enable( $obj_products, $item_total );
                        }

                        $order->add_product( wc_get_product( $product_id ), 1, array( 'total' => $item_total, 'subtotal' => $item_total ) );
                    }
                }

                $order_data = $this->ovabrw_add_order_item( $order_id ); // Get order data

                // Get data total
                $order_total = $order_data['order_total'];
                
                if ( $order_data['has_deposit'] ) {
                    $data_order_keys['_ova_remaining_amount'] = $order_data['total_remaining'];
                    $data_order_keys['_ova_deposit_amount']   = $order_data['total_deposit'];
                    $data_order_keys['_ova_has_deposit']      = 1;
                    $data_order_keys['_ova_remaining_taxes']  = 0;
                }

                if ( $order_data['tax_display_cart'] ) {
                    $data_order_keys['_ova_tax_display_cart'] = 1;
                }

                if ( $order_data['remaining_taxes'] ) {
                    $data_order_keys['_ova_remaining_taxes']  = $order_data['remaining_taxes'];
                }

                $data_order_keys['_ova_insurance_amount'] = $order_data['total_insurance'];
                $data_order_keys['_ova_original_total']   = $order_total;

                foreach ( $data_order_keys as $key => $update ) {
                    $order->update_meta_data( $key , $update );
                }

                $order->set_total( $order_total );
                $order->set_address( $this->ovabrw_get_address(), 'billing' );
                $order->set_address( $this->ovabrw_get_address(), 'shipping' );

                $status_order = isset( $_POST['status_order'] ) ? sanitize_text_field( $_POST['status_order'] ) : '';

                // Tax
                if ( wc_tax_enabled() && $order_data['tax_rate_id'] && $order_data['tax_amount'] ) {

                    $tax_rate_id = $order_data['tax_rate_id'];
                    $tax_amount  = $order_data['tax_amount'];

                    $item = new WC_Order_Item_Tax();
                    $item->set_props(
                        array(
                            'rate_id'            => $tax_rate_id,
                            'tax_total'          => $tax_amount,
                            'shipping_tax_total' => 0,
                            'rate_code'          => WC_Tax::get_rate_code( $tax_rate_id ),
                            'label'              => WC_Tax::get_rate_label( $tax_rate_id ),
                            'compound'           => WC_Tax::is_compound( $tax_rate_id ),
                            'rate_percent'       => WC_Tax::get_rate_percent_value( $tax_rate_id ),
                        )
                    );

                    // Add item to order and save.
                    $order->add_item( $item );
                }

                if ( $order_data['tax_amount'] ) {
                    $order->set_cart_tax( $order_data['tax_amount'] );
                }

                if ( $status_order ) {
                    $order->update_status( $status_order );
                }

                $order->save();
            }
        }
    }
}

new Ovabrw_Model();