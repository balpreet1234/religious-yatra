<?php defined( 'ABSPATH' ) || exit();

/* Send mail in Request for booking */
function ovabrw_request_booking( $data ) {
    // Get subject setting
    $subject = ovabrw_get_setting( get_option( 'ova_brw_request_booking_mail_subject', esc_html__('Request For Booking') ) );

    if ( empty( $subject ) ) {
        $subject = esc_html__('Request For Booking', 'ova-brw');
    }

    $subject = apply_filters( 'ovabrw_request_booking_subject', $subject, $data );

    // Get email setting
    $mail_to_setting = ovabrw_get_setting( get_option( 'ova_brw_request_booking_mail_from_email', get_option( 'admin_email' ) ) );

    if ( empty( $mail_to_setting ) ) {
        $mail_to_setting = get_option( 'admin_email' );
    }

    $mail_to = [];

    // Add $mail_to_settings to $mail_to
    if ( apply_filters( 'ovabrw_request_booking_add_mail_to_settings', true ) ) {
        array_push( $mail_to , $mail_to_setting );
    }
    
    // Add email customer
    array_push( $mail_to, $data['email'] );

    // Emails Cc
    $email_cc = ovabrw_get_setting( get_option( 'ova_brw_request_booking_mail_cc_email' ) );
    if ( $email_cc ) {
        $email_cc = explode( '|', $email_cc );
        $email_cc = array_map('trim', $email_cc);

        if ( $email_cc && is_array( $email_cc ) ) {
            $mail_to = array_unique( array_merge ( $mail_to, $email_cc ) );
        }
    }

    $mail_to = apply_filters( 'ovabrw_request_booking_mail_to', $mail_to, $data );

    $body = '';

    $product_name   = isset( $data['product_name'] ) ? $data['product_name'] : '';
    $product_id     = isset( $data['product_id'] ) ? $data['product_id'] : '';
    $time_from      = isset( $data['ovabrw_time_from'] ) ? sanitize_text_field( $data['ovabrw_time_from'] ) : '';
    $name           = isset( $data['name'] ) ? sanitize_text_field( $data['name'] ) : '';
    $email          = isset( $data['email'] ) ? sanitize_text_field( $data['email'] ) : '';
    $number         = isset( $data['phone'] ) ? sanitize_text_field( $data['phone'] ) : '';
    $address        = isset( $data['address'] ) ? sanitize_text_field( $data['address'] ) : '';
    $pickup_date    = isset( $data['ovabrw_request_pickup_date'] ) ? sanitize_text_field( $data['ovabrw_request_pickup_date'] ) : '';

    if ( $time_from ) {
        $pickup_date .= ' ' . $time_from;
    }

    $pickoff_date   = isset( $data['ovabrw_request_pickoff_date'] ) ? sanitize_text_field( $data['ovabrw_request_pickoff_date'] ) : '';
    $adults         = isset( $data['ovabrw_adults'] ) ? $data['ovabrw_adults'] : 1;
    $childrens      = isset( $data['ovabrw_childrens'] ) ? $data['ovabrw_childrens'] : 0;
    $babies         = isset( $data['ovabrw_babies'] ) ? $data['ovabrw_babies'] : 0;
    $quantity       = isset( $data['ovabrw_quantity'] ) ? $data['ovabrw_quantity'] : 1;
    $resources      = isset( $data['ovabrw_rs_checkboxs'] ) ? $data['ovabrw_rs_checkboxs'] : [];
    $services       = isset( $data['ovabrw_service'] ) ? $data['ovabrw_service'] : [];
    $extra          = isset( $data['extra'] ) ? $data['extra'] : '';
    
    $service_ids    = get_post_meta( $product_id, 'ovabrw_service_id', true );
    $service_name   = get_post_meta( $product_id, 'ovabrw_service_name', true );
    $arr_services   = array();

    if ( ! empty( $services ) && is_array( $services ) ) {
        foreach( $services as $s_id ){
            if( $s_id && $service_ids && is_array( $service_ids ) ){
                foreach( $service_ids as $key_id => $service_id_arr ) {
                    $key = array_search( $s_id, $service_id_arr );

                    if ( ! is_bool( $key ) ) {
                        $val_ser = '';
                        if ( ovabrw_check_array( $service_name, $key_id ) ) {
                            if ( ovabrw_check_array( $service_name[$key_id], $key ) ) {
                                $val_ser = $service_name[$key_id][$key];
                                if ( $val_ser ) {
                                    array_push( $arr_services , $val_ser );
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    // get order
    $order = $product_id ? '<h2>'.esc_html__( 'Order details: ', 'ova-brw' ).'</h2><table><tr><td>'.esc_html__( 'Tour: ', 'ova-brw' ).'</td><td><a href="'.get_permalink( $product_id ).'">'.$product_name.'</a><td></tr>' : '';

    $order .= $name ? '<tr><td>'.esc_html__( 'Name: ', 'ova-brw' ).'</td><td>'.$name.'</td></tr>' : '';
    $order .= $email ? '<tr><td>'.esc_html__( 'Email: ', 'ova-brw' ).'</td><td>'.$email.'</td></tr>' : '';

    if ( ovabrw_get_setting( get_option( 'ova_brw_request_booking_form_show_number', 'yes' ) ) == 'yes' ) {
        $order .= $number ? '<tr><td>'. esc_html__( 'Phone: ', 'ova-brw' ).'</td><td>'.$number.'</td></tr>' : '';
    }

    if ( ovabrw_get_setting( get_option( 'ova_brw_request_booking_form_show_address', 'yes' ) ) == 'yes' ) {
        $order .= $address ? '<tr><td>'.esc_html__( 'Address: ', 'ova-brw' ).'</td><td>'.$address.'</td></tr>' : '';
    }

    if ( ovabrw_get_setting( get_option( 'ova_brw_request_booking_form_show_dates', 'yes' ) ) == 'yes' ) {
        $order .= $pickup_date ? '<tr><td>'.esc_html__( 'Check-in: ', 'ova-brw' ).'</td><td>'.$pickup_date.'</td></tr>' : '';
    }

    if ( ovabrw_get_setting( get_option( 'ova_brw_request_booking_form_show_dates', 'yes' ) ) == 'yes' ) {
        $order .= $pickoff_date ? '<tr><td>'.esc_html__( 'Check-out: ', 'ova-brw' ).'</td><td>'.$pickoff_date.'</td></tr>' : '';
    }

    if ( ovabrw_get_setting( get_option( 'ova_brw_request_booking_form_show_guests', 'yes' ) ) == 'yes' ) {
        $order .= $adults ? '<tr><td>'. esc_html__( 'Adults: ', 'ova-brw' ).'</td><td>'.$adults.'</td></tr>' : '';
        $order .= $childrens ? '<tr><td>'.esc_html__( 'Children: ', 'ova-brw' ).'</td><td>'.$childrens.'</td></tr>' : '';
        $order .= $babies ? '<tr><td>'.esc_html__( 'Babies: ', 'ova-brw' ).'</td><td>'.$babies.'</td></tr>' : '';
    }

    // Custom Checkout Fields
    $list_fields        = ovabrw_get_list_field_checkout( $product_id );
    $custom_ckf         = array();
    $custom_ckf_save    = array();

    if ( ! empty( $list_fields ) && is_array( $list_fields ) ) {
        foreach ( $list_fields as $key => $field ) {
            if ( $field['type'] === 'file' ) {
                $files = isset( $_FILES[$key] ) ? $_FILES[$key] : '';

                if ( ! empty( $files ) ) {
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
                    
                    $order .= '<tr>
                                <td>'.sprintf( '%s: ', esc_html( $field['label'] ) ).'</td>
                                <td><a href="'.esc_url( $upload['url'] ).'" title="'.esc_attr( basename( $upload['file'] ) ).'" target="_blank">'.esc_attr( basename( $upload['file'] ) ).'</a></td>
                            </tr>';
                    $custom_ckf_save[$key] = '<a href="'.esc_url( $upload['url'] ).'" title="'.esc_attr( basename( $upload['file'] ) ).'" target="_blank">'.esc_attr( basename( $upload['file'] ) ).'</a>';
                }
            } else {
                $value = array_key_exists( $key, $data ) ? $data[$key] : '';

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

                    $order .= '<tr><td>'.sprintf( '%s: ', esc_html( $field['label'] ) ).'</td><td>'.esc_html( stripslashes( $value ) ).'</td></tr>';

                    $custom_ckf_save[$key] = $value;

                    if ( in_array( $field['type'], array( 'radio' ) ) ) {
                        $custom_ckf[$key] = sanitize_text_field( $value );
                    }
                }
            }
        }
    }

    $data['custom_ckf']         = $custom_ckf;
    $data['custom_ckf_save']    = $custom_ckf_save;

    if ( get_option( 'ova_brw_booking_form_show_quantity', 'no' ) == 'yes' ) {
        $order .= $quantity ? '<tr><td>'.esc_html__( 'Quantity: ', 'ova-brw' ).'</td><td>'.$quantity.'</td></tr>' : '';
    }
    
    if ( ovabrw_get_setting( get_option( 'ova_brw_request_booking_form_show_extra_service', 'yes' ) ) == 'yes' ) {
        if ( !empty( $resources ) && is_array( $resources ) ) {
            $order .= '<tr><td>'. esc_html__( 'Resource: ', 'ova-brw' );
            $resource = $resources ? implode(', ', $resources) : '';
            $order .= '</td><td>'. $resource.'</td></tr>';
        }
    }

    if ( ovabrw_get_setting( get_option( 'ova_brw_request_booking_form_show_service', 'yes' ) ) === 'yes' ) {
        if ( !empty( $arr_services ) && is_array( $arr_services ) ) {
            $order .= '<tr><td>'.esc_html__( 'Services: ', 'ova-brw' );
            $service = $arr_services ? implode(', ', $arr_services) : '';
            $order .= '</td><td>'.$service.'</td></tr>';
        }
    }
    
    if ( ovabrw_get_setting( get_option( 'ova_brw_request_booking_form_show_extra_info', 'yes' ) ) == 'yes' ) {
        $order .= $extra ? '<tr><td>'.esc_html__( 'Extra: ', 'ova-brw' ).'</td><td>'.stripslashes($extra).'</td></tr><table>' : '';
    }

    // Get Email Content
    $body = ovabrw_get_setting( get_option( 'ova_brw_request_booking_mail_content', esc_html__( 'You booked the tour: [product-name] from [check-in] to [check-out]. [order_details]', 'ova-brw' ) ) );

    if ( empty( $body ) ) {
        $body = esc_html__( 'You booked the tour: [product-name] from [check-in] to [check-out]. [order_details]', 'ova-brw' );
    }

    $body = str_replace('[br]', '<br/>', $body);
    $body = str_replace('[product-name]', '<a href="'.get_permalink($product_id).'" target="_blank">'.$product_name.'</a>', $body);

    // Replace body
    $body = str_replace('[check-in]', $pickup_date, $body);
    $body = str_replace('[check-out]', $pickoff_date, $body);
    $body = str_replace('[order_details]', $order, $body);
    $body = apply_filters( 'ovabrw_request_booking_content_mail', $body, $data );

    // Create Order
    $create_order = ovabrw_get_setting( get_option( 'ova_brw_request_booking_create_order', 'no' ) );

    if ( $create_order === 'yes' ) {
        $order_id = ovabrw_request_booking_create_new_order( $data );
    }

    // Before send email
    do_action( 'ovabrw_request_booking_before_send_mail', $mail_to, $subject, $body, $data );

    return ovabrw_sendmail( $mail_to, $subject, $body );
}

function ova_wp_mail_from() {
    $mail_to_setting = ovabrw_get_setting( get_option( 'ova_brw_request_booking_mail_from_email', get_option( 'admin_email' ) ) );

    return apply_filters( 'ovabrw_request_booking_mail_from', $mail_to_setting, $_REQUEST );
}

function ova_wp_mail_from_name() {
    $ova_wp_mail_from_name = ovabrw_get_setting( get_option( 'ova_brw_request_booking_mail_from_name', esc_html__( 'Request For Booking', 'ova-brw' ) ) );

    if ( empty( $ova_wp_mail_from_name ) ) {
        $ova_wp_mail_from_name = esc_html__( 'Request For Booking', 'ova-brw' );
    }

    return apply_filters( 'ovabrw_request_booking_mail_from_name', $ova_wp_mail_from_name, $_REQUEST );
}

function ovabrw_sendmail( $mail_to, $subject, $body ) {
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=".get_bloginfo( 'charset' )."\r\n";
    
    add_filter( 'wp_mail_from', 'ova_wp_mail_from' );
    add_filter( 'wp_mail_from_name', 'ova_wp_mail_from_name' );

    if ( wp_mail( $mail_to, $subject, $body, $headers ) ) {
        $result = true;
    } else {
        $result = false;
    }

    remove_filter( 'wp_mail_from', 'ova_wp_mail_from');
    remove_filter( 'wp_mail_from_name', 'ova_wp_mail_from_name' );

    return $result;
}

// Request for Booking create new order
if ( ! function_exists( 'ovabrw_request_booking_create_new_order' ) ) {
    function ovabrw_request_booking_create_new_order( $data ) {
        $product_id = isset( $data['product_id'] ) ? $data['product_id'] : '';

        if ( ! $product_id ) return false;

        $time_from      = isset( $data['ovabrw_time_from'] ) ? sanitize_text_field( $data['ovabrw_time_from'] ) : '';
        $name           = isset( $data['name'] ) ? sanitize_text_field( $data['name'] ) : '';
        $email          = isset( $data['email'] ) ? sanitize_text_field( $data['email'] ) : '';
        $phone          = isset( $data['phone'] ) ? sanitize_text_field( $data['phone'] ) : '';
        $address        = isset( $data['address'] ) ? sanitize_text_field( $data['address'] ) : '';
        $pickup_date    = isset( $data['ovabrw_request_pickup_date'] ) ? sanitize_text_field( $data['ovabrw_request_pickup_date'] ) : '';

        if ( $time_from ) {
            $pickup_date .= ' ' . $time_from;
        }

        $pickoff_date   = isset( $data['ovabrw_request_pickoff_date'] ) ? sanitize_text_field( $data['ovabrw_request_pickoff_date'] ) : '';
        $adults         = isset( $data['ovabrw_adults'] ) ? $data['ovabrw_adults'] : 1;
        $childrens      = isset( $data['ovabrw_childrens'] ) ? $data['ovabrw_childrens'] : 0;
        $babies         = isset( $data['ovabrw_babies'] ) ? $data['ovabrw_babies'] : 0;
        $custom_ckf     = isset( $data['custom_ckf'] ) ? $data['custom_ckf'] : [];
        $quantity       = isset( $data['ovabrw_quantity'] ) ? $data['ovabrw_quantity'] : 1;
        $resources      = isset( $data['ovabrw_rs_checkboxs'] ) ? $data['ovabrw_rs_checkboxs'] : [];
        $services       = isset( $data['ovabrw_service'] ) ? $data['ovabrw_service'] : [];
        $extra          = isset( $data['extra'] ) ? $data['extra'] : '';

        $cart_item['product_id']        = $product_id;
        $cart_item['ovabrw_adults']     = $adults;
        $cart_item['ovabrw_childrens']  = $childrens;
        $cart_item['ovabrw_babies']     = $babies;
        $cart_item['ovabrw_quantity']   = $quantity;
        $cart_item['custom_ckf']        = $custom_ckf;;
        $cart_item['ovabrw_resources']  = $resources;
        $cart_item['ovabrw_services']   = $services;
        $cart_item['ovabrw_time_from']  = $time_from;

        // Check-out
        if ( ! $pickoff_date ) {
            $pickoff_date = ovabrw_get_checkout_date( $product_id, strtotime( $pickup_date ) );
        }

        // Amount Insurance
        $amount_insurance = floatval( get_post_meta( $product_id, 'ovabrw_amount_insurance', true ) );
        $amount_insurance = $amount_insurance * ( $adults + $childrens + $babies );

        // Line Total
        $line_total = get_price_by_guests( $product_id, strtotime( $pickup_date ), strtotime( $pickoff_date ), $cart_item );

        // Multiple Currency
        $amount_insurance   = ovabrw_convert_price( $amount_insurance );
        $line_total         = ovabrw_convert_price( $line_total );

        // Billing
        $order_address = array(
            'billing' => array(
                'first_name' => $name,
                'last_name'  => '',
                'company'    => '',
                'email'      => $email,
                'phone'      => $phone,
                'address_1'  => $address,
                'address_2'  => '',
                'city'       => '',
                'country'    => '',
            ),
            'shipping' => array(),
        );

        $args = array(
            'status'        => '',
            'customer_note' => $extra,
        );
        
        $order      = wc_create_order( $args ); // Create new order
        $order_id   = $order->get_id(); // Get order id
        $products   = wc_get_product( $product_id );

        // Add product to order
        $order->add_product( wc_get_product( $product_id ), 1, array( 'total' => $line_total ) );

        // Order Item
        $order_items = $order->get_items(); 

        // Loop order items
        foreach ( $order_items as $item_id => $product ) {
            $item = WC_Order_Factory::get_order_item( absint( $item_id ) );

            if ( ! $item ) {
                continue;
            }

            $data_item  = array();
            $product_id = $product['product_id'];

            $data_item[ 'ovabrw_pickup_date' ]  = $pickup_date;
            $data_item[ 'ovabrw_pickoff_date' ] = $pickoff_date;
            $data_item[ 'ovabrw_adults' ]       = $adults;
            $data_item[ 'ovabrw_childrens' ]    = $childrens;
            $data_item[ 'ovabrw_babies' ]       = $babies;
            $data_item[ 'ovabrw_quantity' ]     = $quantity;

            if ( $amount_insurance ) {
                $data_item[ 'ovabrw_amount_insurance' ] = $amount_insurance;
            }

            // Custom Checkout Fields
            if ( $custom_ckf ) {
                $data_item[ 'ovabrw_custom_ckf' ] = $custom_ckf;
            }

            if ( isset( $data['custom_ckf_save'] ) && $data['custom_ckf_save'] ) {
                foreach ( $data['custom_ckf_save'] as $k => $val ) {
                    $data_item[$k] = $val;
                }
            }

            // Resources
            if ( ! empty( $resources ) ) {
                $data_item['ovabrw_resources'] = $resources;

                $res_name = [];

                foreach ( $resources as $res_id => $val ) {
                    array_push( $res_name, $val );
                }
                
                if ( count( $res_name ) == 1 ) {
                    $data_item[esc_html__( 'Resource', 'ova-brw' )] = join( ', ', $res_name );
                } else {
                    $data_item[esc_html__( 'Resources', 'ova-brw' )] = join( ', ', $res_name );
                }
            }

            // Services
            if ( ! empty( $services ) ) {
                $data_item['ovabrw_services'] = $services;

                $services_id      = get_post_meta( $product_id, 'ovabrw_service_id', true ); 
                $services_name    = get_post_meta( $product_id, 'ovabrw_service_name', true );
                $services_label   = get_post_meta( $product_id, 'ovabrw_label_service', true ); 

                foreach ( $services as $val_ser ) {
                    if ( ! empty ( $services_id ) && is_array( $services_id ) ) {
                        foreach ( $services_id as $key => $value ) {
                            if ( is_array( $value ) && ! empty( $value ) ) {
                                foreach ( $value as $k => $val ) {
                                    if ( $val_ser == $val && ! empty( $val ) ) {
                                        $service_name   = $services_name[$key][$k];
                                        $service_label  = $services_label[$key];
                                        $data_item[$service_label] = $service_name;
                                    }
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
                    'total'     => $line_total,
                    'subtotal'  => $line_total,
                )
            );
            $item->set_quantity( $quantity );

            $item->save();
        }

        $status_order = ovabrw_get_setting( get_option( 'ova_brw_request_booking_order_status', 'wc-on-hold' ) );
        wp_update_post( array( 'ID' => $order_id, 'post_status' => $status_order ) );

        $order->update_meta_data( '_ova_insurance_amount' , $amount_insurance );
        $order->set_address( $order_address['billing'], 'billing' );
        $order->set_date_created( date( 'Y-m-d H:i:s', current_time( 'timestamp' ) ) );
        $order->calculate_totals( wc_tax_enabled() );
        $order->save();
    }
}