<?php if ( !defined( 'ABSPATH' ) ) exit();

if ( ! class_exists('WP_List_Table') ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class List_Booking extends WP_List_Table {
    function __construct() {

        global $page;

        //Set parent defaults
        parent::__construct( array(
            'singular'  => 'bookings',     //singular name of the listed records
            'plural'    => 'bookings',    //plural name of the listed records
            'ajax'      => false        //does this table support ajax?
        ) );
        
    }

    function column_default( $item, $column_name ) {
        switch($column_name){
            case 'id':
            case 'customer':
            case 'check-in-check-out':
            case 'status-deposit':
            case 'status-insurance':
            case 'room-name':
            case 'order_status':
            case 'order_id':
                return $item[$column_name];
            default:
                return print_r($item,true); //Show the whole array for troubleshooting purposes
        }
    }

    function column_order_status( $item ) {
        switch ( $item['order_status'] ) {
            case 'processing':
                $order_status_text = esc_html__( 'Processing', 'ova-brw' );
                break;
            case 'completed':
                $order_status_text = esc_html__( 'Completed', 'ova-brw' );
                break;
            case 'on-hold':
                $order_status_text = esc_html__( 'On hold', 'ova-brw' );
                break;
            case 'pending':
                $order_status_text = esc_html__( 'Pending payment', 'ova-brw' );
                break;
            case 'cancelled':
                $order_status_text = esc_html__( 'Cancel', 'ova-brw' );
                break;

            case 'closed':
                $order_status_text = esc_html__( 'Closed', 'ova-brw' );
                break;    
            
            default:
                $order_status_text = esc_html__( 'Update Order', 'ova-brw' );
                break;
        }
        
        //Build row actions
        $selected_action = sprintf( '<select name="update_order_status" class="update_order_status" data-order_id="'.$item['id'].'" data-error-per-msg="'.esc_html__( 'You don\'t have permission to update', 'ova-brw' ).'" data-error-update-msg="'.esc_html__( 'Error Update', 'ova-brw' ).'" >
            <option value="">'.esc_html__( 'Update Status', 'ova-brw' ).'</option>
            <option value="wc-completed">'.esc_html__( 'Completed', 'ova-brw' ).'</option>
            <option value="wc-processing">'.esc_html__( 'Processing', 'ova-brw' ).'</option>
            <option value="wc-on-hold">'.esc_html__( 'On hold', 'ova-brw' ).'</option>
            <option value="wc-pending">'.esc_html__( 'Pending payment', 'ova-brw' ).'</option>
            <option value="wc-cancelled">'.esc_html__( 'Cancel', 'ova-brw' ).'</option>
            <option value="wc-closed">'.esc_html__( 'Closed', 'ova-brw' ).'</option>
        </select>' );

        return sprintf('<span>%1$s</span>%2$s',
            '<mark class="order-status status-'.$item['order_status'].' tips"><span>'.$order_status_text.'</span></mark>',
            $selected_action
        );
    }

    function column_id( $item ) {
    	//Build row actions
        if( current_user_can( apply_filters( 'ovabrw_edit_order_woo_cap' ,'manage_options' ) ) ){
            return sprintf('<span>%1$s</span>',
                '<a target="_blank" href="'.admin_url('/post.php?post='.$item['id'].'&action=edit').'">'.$item['id'].'</a>'
            );
        }else{
            return sprintf( '<span>%1$s</span>', $item['id'] );
        }
    	
    }

    function get_columns() {
        $options = $columns = array();

        $id = get_option( 'admin_manage_order_show_id', 1 );
        if ( $id ) {
            $options['id'] = $id;
        }

        $customer = get_option( 'admin_manage_order_show_customer', 2 );
        if ( $customer ) {
            $options['customer'] = $customer;
        }

        $time = get_option( 'admin_manage_order_show_time', 3 );
        if ( $time ) {
            $options['check-in-check-out'] = $time;
        }

        $deposit = get_option( 'admin_manage_order_show_deposit', 5 );
        if ( $deposit ) {
            $options['status-deposit'] = $deposit;
        }

        $insurance = get_option( 'admin_manage_order_show_insurance', 6 );
        if ( $insurance ) {
            $options['status-insurance'] = $insurance;
        }

        $product = get_option( 'admin_manage_order_show_product', 8 );
        if ( $product ) {
            $options['room-name'] = $product;
        }

        $status = get_option( 'admin_manage_order_show_order_status', 9 );
        if ( $status ) {
            $options['order_status'] = $status;
        }

        if ( $options ) {
            asort( $options );
        }

        foreach ( $options as $key => $value ) {
            switch ( $key ) {
                case 'id':
                    $columns[$key] = esc_html__( 'Order ID','ova-brw' );
                    break;

                case 'customer':
                    $columns[$key] = esc_html__( 'Customer Name','ova-brw' );
                    break;

                case 'check-in-check-out':
                    $columns[$key] = esc_html__( 'Check-in - Check-out','ova-brw' );
                    break;

                case 'status-deposit':
                    $columns[$key] = esc_html__( 'Deposit Status','ova-brw' );
                    break;

                case 'status-insurance':
                    $columns[$key] = esc_html__( 'Insurance Status','ova-brw' );
                    break;

                case 'room-name':
                    $columns[$key] = esc_html__( 'Product','ova-brw' );
                    break;

                case 'order_status':
                    $columns[$key] = esc_html__( 'Order Status','ova-brw' );
                    break;
                
                default:
                    break;
            }
        }

        return $columns;
    }

    function get_sortable_columns() {
        $sortable_columns = array(
            'id'                    => array('id',true),
            'customer'              => array('customer',false),
            'check-in-check-out'    => array('check-in-check-out',false),
            'status-deposit'        => array('status-deposit',false),
            'status-insurance'      => array('status-insurance',false),
            'room-name'             => array('room-name',false),
            'order_status'          => array('order_status',false),
            
        );

        return $sortable_columns;
    }

    function pagination( $which ) {
        if ( empty( $this->_pagination_args ) ) {
            return;
        }

        $parameters     = array();
        $fields_data    = $_REQUEST;

        // order_number
        if ( isset( $fields_data['order_number'] ) && $fields_data['order_number'] ) {
            $parameters['order_number'] = $fields_data['order_number'];
        }

        // name_customer
        if ( isset( $fields_data['name_customer'] ) && $fields_data['name_customer'] ) {
            $parameters['name_customer'] = $fields_data['name_customer'];
        }

        // check_in_out
        if ( isset( $fields_data['check_in_out'] ) && $fields_data['check_in_out'] ) {
            $parameters['check_in_out'] = $fields_data['check_in_out'];
        }

        // from_day
        if ( isset( $fields_data['from_day'] ) && $fields_data['from_day'] ) {
            $parameters['from_day'] = $fields_data['from_day'];
        }

        // to_day
        if ( isset( $fields_data['to_day'] ) && $fields_data['to_day'] ) {
            $parameters['to_day'] = $fields_data['to_day'];
        }

        // room_id
        if ( isset( $fields_data['room_id'] ) && $fields_data['room_id'] ) {
            $parameters['room_id'] = $fields_data['room_id'];
        }

        // filter_order_status
        if ( isset( $fields_data['filter_order_status'] ) && $fields_data['filter_order_status'] ) {
            $parameters['filter_order_status'] = $fields_data['filter_order_status'];
        }

        $total_items     = $this->_pagination_args['total_items'];
        $total_pages     = $this->_pagination_args['total_pages'];
        $infinite_scroll = false;
        if ( isset( $this->_pagination_args['infinite_scroll'] ) ) {
            $infinite_scroll = $this->_pagination_args['infinite_scroll'];
        }

        if ( 'top' === $which && $total_pages > 1 ) {
            $this->screen->render_screen_reader_content( 'heading_pagination' );
        }

        $output = '<span class="displaying-num">' . sprintf(
            /* translators: %s: Number of items. */
            _n( '%s item', '%s items', $total_items ),
            number_format_i18n( $total_items )
        ) . '</span>';

        $current              = $this->get_pagenum();
        $removable_query_args = wp_removable_query_args();

        $current_url = set_url_scheme( 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );

        $current_url = remove_query_arg( $removable_query_args, $current_url );

        $page_links = array();

        $total_pages_before = '<span class="paging-input">';
        $total_pages_after  = '</span></span>';

        $disable_first = false;
        $disable_last  = false;
        $disable_prev  = false;
        $disable_next  = false;

        if ( 1 == $current ) {
            $disable_first = true;
            $disable_prev  = true;
        }
        if ( $total_pages == $current ) {
            $disable_last = true;
            $disable_next = true;
        }

        if ( $disable_first ) {
            $page_links[] = '<span class="tablenav-pages-navspan button disabled" aria-hidden="true">&laquo;</span>';
        } else {
            $page_links[] = sprintf(
                "<a class='first-page button' href='%s'><span class='screen-reader-text'>%s</span><span aria-hidden='true'>%s</span></a>",
                esc_url( remove_query_arg( 'paged', $current_url ) ),
                __( 'First page' ),
                '&laquo;'
            );
        }

        if ( $disable_prev ) {
            $page_links[] = '<span class="tablenav-pages-navspan button disabled" aria-hidden="true">&lsaquo;</span>';
        } else {
            $parameters['paged'] = max( 1, $current - 1 );
            $page_links[] = sprintf(
                "<a class='prev-page button' href='%s'><span class='screen-reader-text'>%s</span><span aria-hidden='true'>%s</span></a>",
                esc_url( add_query_arg( $parameters, $current_url ) ),
                __( 'Previous page' ),
                '&lsaquo;'
            );
        }

        if ( 'bottom' === $which ) {
            $html_current_page  = $current;
            $total_pages_before = '<span class="screen-reader-text">' . __( 'Current Page' ) . '</span><span id="table-paging" class="paging-input"><span class="tablenav-paging-text">';
        } else {
            $html_current_page = sprintf(
                "%s<input class='current-page' id='current-page-selector' type='text' name='paged' value='%s' size='%d' aria-describedby='table-paging' /><span class='tablenav-paging-text'>",
                '<label for="current-page-selector" class="screen-reader-text">' . __( 'Current Page' ) . '</label>',
                $current,
                strlen( $total_pages )
            );
        }
        $html_total_pages = sprintf( "<span class='total-pages'>%s</span>", number_format_i18n( $total_pages ) );
        $page_links[]     = $total_pages_before . sprintf(
            /* translators: 1: Current page, 2: Total pages. */
            _x( '%1$s of %2$s', 'paging' ),
            $html_current_page,
            $html_total_pages
        ) . $total_pages_after;

        if ( $disable_next ) {
            $page_links[] = '<span class="tablenav-pages-navspan button disabled" aria-hidden="true">&rsaquo;</span>';
        } else {
            $parameters['paged'] = min( $total_pages, $current + 1 );
            $page_links[] = sprintf(
                "<a class='next-page button' href='%s'><span class='screen-reader-text'>%s</span><span aria-hidden='true'>%s</span></a>",
                esc_url( add_query_arg( $parameters, $current_url ) ),
                __( 'Next page' ),
                '&rsaquo;'
            );
        }

        if ( $disable_last ) {
            $page_links[] = '<span class="tablenav-pages-navspan button disabled" aria-hidden="true">&raquo;</span>';
        } else {
            $parameters['paged'] = $total_pages;
            $page_links[] = sprintf(
                "<a class='last-page button' href='%s'><span class='screen-reader-text'>%s</span><span aria-hidden='true'>%s</span></a>",
                esc_url( add_query_arg( $parameters, $current_url ) ),
                __( 'Last page' ),
                '&raquo;'
            );
        }

        $pagination_links_class = 'pagination-links';
        if ( ! empty( $infinite_scroll ) ) {
            $pagination_links_class .= ' hide-if-js';
        }
        $output .= "\n<span class='$pagination_links_class'>" . implode( "\n", $page_links ) . '</span>';

        if ( $total_pages ) {
            $page_class = $total_pages < 2 ? ' one-page' : '';
        } else {
            $page_class = ' no-pages';
        }
        $this->_pagination = "<div class='tablenav-pages{$page_class}'>$output</div>";

        echo $this->_pagination;
    }

    function prepare_items() {
        global $wpdb; //This is used only if making any database queries

        /**
         * First, lets decide how many records per page to show
         */
        $per_page = apply_filters( 'ovabrw_ft_manager_order_per_page', 20 );
        
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        
        $this->_column_headers = array($columns, $hidden, $sortable);
        
        $data = array();

        $filter_order_status =  isset( $_POST['filter_order_status'] ) ? sanitize_text_field( $_POST['filter_order_status'] ) : '';
        $filter_order_status =  isset( $_POST['filter_order_status'] ) ? $_POST['filter_order_status'] : '';
        if ( !$filter_order_status && isset( $_GET['filter_order_status'] ) && $_GET['filter_order_status'] ) {
            $filter_order_status = $_GET['filter_order_status'];
        }

        if ( !empty( $filter_order_status ) ) {
            $order_status = array($filter_order_status);
        } else {
            $order_status = array( 'wc-pending', 'wc-processing','wc-completed', 'wc-half-completed', 'wc-on-hold', 'wc-cancelled', 'wc-closed' );
        }

        $room_id = isset( $_POST['room_id'] ) ? $_POST['room_id'] : '';
        if ( !$room_id && isset( $_GET['room_id'] ) && $_GET['room_id'] ) {
            $room_id = $_GET['room_id'];
        }

        $order_number   = isset( $_POST['order_number'] ) ? absint( $_POST['order_number'] ) : '';
        if ( !$order_number && isset( $_GET['order_number'] ) && $_GET['order_number'] ) {
            $order_number = absint( $_GET['room_id'] );
        }

        $name_customer  = isset( $_POST['name_customer'] ) ? sanitize_text_field( $_POST['name_customer'] ) : '';
        if ( !$name_customer && isset( $_GET['name_customer'] ) && $_GET['name_customer'] ) {
            $name_customer = $_GET['name_customer'];
        }

        if ( ovabrw_wc_custom_orders_table_enabled() ) {
            $room_query = $room_id ? 'AND oitem_meta.meta_value = '. sanitize_text_field( $room_id ) : '';

            $result = $wpdb->get_col("
                SELECT DISTINCT o.id
                FROM {$wpdb->prefix}wc_orders AS o
                LEFT JOIN {$wpdb->prefix}woocommerce_order_items AS oitems
                ON o.id = oitems.order_id
                LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS oitem_meta
                ON oitems.order_item_id = oitem_meta.order_item_id
                WHERE oitems.order_item_type = 'line_item'
                AND oitem_meta.meta_key = '_product_id'
                AND o.status IN ( '" . implode( "','", $order_status ) . "' )
                $room_query
            ");
        } else {
            $room_query = $room_id ? 'AND oitem_meta.meta_value = '. sanitize_text_field( $room_id ) : '';

            $result = $wpdb->get_col("
                SELECT DISTINCT oitems.order_id
                FROM {$wpdb->prefix}woocommerce_order_items AS oitems
                LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS oitem_meta
                ON oitems.order_item_id = oitem_meta.order_item_id
                LEFT JOIN {$wpdb->posts} AS posts ON oitems.order_id = posts.ID
                WHERE posts.post_type = 'shop_order'
                AND oitems.order_item_type = 'line_item'
                AND oitem_meta.meta_key = '_product_id'
                AND posts.post_status IN ( '" . implode( "','", $order_status ) . "' )
                $room_query
            ");
        }

	    $from_day = isset( $_POST['from_day'] ) ? strtotime( $_POST['from_day'] ) : '';
        if ( !$from_day && isset( $_GET['from_day'] ) && $_GET['from_day'] ) {
            $from_day = strtotime( $_GET['from_day'] );
        }

        $to_day = isset( $_POST['to_day'] ) ? strtotime( $_POST['to_day'] ) : '';
        if ( !$to_day && isset( $_GET['to_day'] ) && $_GET['to_day'] ) {
            $to_day = strtotime( $_GET['to_day'] );
        }
        
        $check_in_out = isset( $_POST['check_in_out'] ) ? $_POST['check_in_out'] : '';
        if ( !$check_in_out && isset( $_GET['check_in_out'] ) && $_GET['check_in_out'] ) {
            $check_in_out = $_GET['check_in_out'];
        }

        $check = $check_in_flag = $check_out_flag = true;

        $ovabrw_date_format = ovabrw_get_date_format();

	    foreach ( $result as $key => $value ) {
            $order      = wc_get_order($value);
            $fullname   = $order->get_formatted_billing_full_name();

            if ( ( empty( $order_number ) || ( ! empty( $order_number ) && $order_number == $value  ) ) && ( empty( $name_customer ) || ( strpos($fullname, $name_customer) ) !== false ) ) {

    	        // Get Meta Data type line_item of Order
    	        $order_items = $order->get_items( apply_filters( 'woocommerce_purchase_order_item_types', 'line_item' ) );

    	        // For Meta Data
    	        foreach ( $order_items as $item_id => $item ) {
    	            $room_check_in = $room_check_out = $room_code = '';
    	            $room_name     = $item->get_name();
   	            
                    // Get value of check-in, check-out
                    $room_check_in  = $item->get_meta( 'ovabrw_pickup_date' );
                    $room_check_out = $item->get_meta( 'ovabrw_pickoff_date' );

                    $stt_check_in   = date_i18n( $ovabrw_date_format, strtotime( $room_check_in ) );
                    $stt_check_out  = date_i18n( $ovabrw_date_format, strtotime( $room_check_out ) );

                    $room_check_in_timep    = strtotime( $stt_check_in );
                    $room_check_out_timep   = strtotime( $stt_check_out );

                    if( $check_in_out == 'check_in' && $from_day != '' && $to_day != '' ){
                        $check = ( $from_day <=  $room_check_in_timep && $room_check_in_timep <= $to_day ) ? true : false;
                    }else if( $check_in_out == 'check_out' ){
                        $check = ( $from_day <=  $room_check_out_timep &&  $room_check_out_timep <= $to_day ) ? true : false;
                    }else if( $from_day != '' && $to_day != '' ){
                        $check_in_flag = ( $from_day <=  $room_check_in_timep && $room_check_in_timep <= $to_day ) ? true : false;
                        $check_out_flag = ( $from_day <=  $room_check_out_timep &&  $room_check_out_timep <= $to_day ) ? true : false;
                    } else if ( $from_day != '' && $to_day == '' ) {
                        $check_in_flag = ( $from_day <=  $room_check_out_timep && $room_check_in_timep <= $from_day ) ? true : false;
                    } else if ( $from_day == '' && $to_day != '' ) {
                        $check_out_flag = ( $to_day <=  $room_check_out_timep &&  $room_check_in_timep <= $to_day ) ? true : false;
                    }

                    $order_amount_insurance = $item['ovabrw_amount_insurance'];

                    if ( $order_amount_insurance == 0 ) {
                        $status_insurance = '<span class="ova_amount_status_insurance_column ova_paid">'.esc_html__("Paid for Customers", "ova-brw").'</span>';
                    } else {
                        $status_insurance = '<span class="ova_amount_status_insurance_column ova_pending">'.esc_html__("Received", "ova-brw").'</span>';
                    }

                    $order_amount_remaining = $item['ovabrw_remaining_amount'];

                    if ( $order_amount_remaining == 0 ) {
                        $status_deposit = '<span class="ova_amount_status_insurance_column ova_paid">'.esc_html__("Full Payment", "ova-brw").'</span>';
                    } else {
                        $ovabrw_remaining_balance_order_id = $item['ovabrw_remaining_balance_order_id'];
                        if ( $ovabrw_remaining_balance_order_id ) {
                            $status_deposit = '<span class="ova_amount_status_insurance_column ova_paid">'.esc_html__("Original Payment", "ova-brw").'</span>';
                        } else {
                            $status_deposit = '<span class="ova_amount_status_insurance_column ova_pending">'.esc_html__("Partial Payment", "ova-brw").'</span>';
                        }
                    }

                    $date_time = $room_check_in . ' @ ' . $room_check_out;

                    if ( ! $room_check_out ) {
                        $date_time = $room_check_in;
                    }

                    $ovabrw_original_order_id = $item->get_meta('ovabrw_original_order_id');

                    if ( absint( $ovabrw_original_order_id ) ) {
                        $date_time = sprintf( 'Original Order: %s', '<a href="'.get_edit_post_link($ovabrw_original_order_id).'" target="_blank"><strong>#'.$ovabrw_original_order_id.'</strong></a>' );
                    }

    	            if ( $check && $check_in_flag && $check_out_flag ) {
    	            	$data[] = array(
    		                'id'                  => $order->get_ID(),
    		                'customer'            => $order->get_formatted_billing_full_name(),
    		                'check-in-check-out'  => $date_time,
                            'status-deposit'      => $status_deposit,
                            'status-insurance'    => $status_insurance,
    		                'room-name'           => $room_name,
    		                'order_status'        => $order->get_status(),
    		            );
    	            }
    	        }
    	    }
        }

        function usort_reorder($a,$b){
            $orderby = (!empty($_REQUEST['orderby'])) ? sanitize_text_field( $_REQUEST['orderby'] ) : 'id'; //If no sort, default to title
            $order = (!empty($_REQUEST['order'])) ? sanitize_text_field( $_REQUEST['order'] ) : 'asc'; //If no order, default to asc
            $result = strcmp($a[$orderby], $b[$orderby]); //Determine sort order
            return ($order==='asc') ? -$result : $result; //Send final sort direction to usort
        }    
        usort($data, 'usort_reorder');

        $current_page = $this->get_pagenum();
        $total_items = count($data);
        $data = array_slice($data,(($current_page-1)*$per_page),$per_page);
       
        $this->items = $data;
        $this->set_pagination_args( array(
            'total_items' => $total_items,                  //WE have to calculate the total number of items
            'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
            'total_pages' => ceil($total_items/$per_page)   //WE have to calculate the total number of pages
        ));
    }
}