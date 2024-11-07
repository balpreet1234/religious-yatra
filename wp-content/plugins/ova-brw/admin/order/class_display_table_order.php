<?php
// Display Manage Booking
function ovabrw_display_order(){
	//Create an instance of our package class...
    $manage_booking = new List_Booking();
    //Fetch, prepare, sort, and filter our data...
    $manage_booking->prepare_items();

    $all_rooms = get_all_rooms();

    $order_number = isset( $_POST['order_number'] ) ? sanitize_text_field( $_POST['order_number'] ) : '';
    if ( !$order_number && isset( $_GET['order_number'] ) && $_GET['order_number'] ) {
        $order_number = absint( $_GET['order_number'] );
    }

    $name_customer = isset( $_POST['name_customer'] ) ? sanitize_text_field( $_POST['name_customer'] ) : '';
    if ( !$name_customer && isset( $_GET['name_customer'] ) && $_GET['name_customer'] ) {
        $name_customer = $_GET['name_customer'];
    }

    $current_room_id = isset( $_POST['room_id'] ) ? sanitize_text_field( $_POST['room_id'] ) : '';
    if ( !$current_room_id && isset( $_GET['room_id'] ) && $_GET['room_id'] ) {
        $current_room_id = absint( $_GET['room_id'] );
    }
    
    $check_in_out = isset($_POST['check_in_out'] ) ? sanitize_text_field( $_POST['check_in_out'] ) : '';
    if ( !$check_in_out && isset( $_GET['check_in_out'] ) && $_GET['check_in_out'] ) {
        $check_in_out = $_GET['check_in_out'];
    }

    $filter_order_status = isset($_POST['filter_order_status'] ) ? sanitize_text_field( $_POST['filter_order_status'] ) : '';
    if ( !$filter_order_status && isset( $_GET['filter_order_status'] ) && $_GET['filter_order_status'] ) {
        $filter_order_status = $_GET['filter_order_status'];
    }
    
    $from_day = isset( $_POST['from_day'] ) ? sanitize_text_field( $_POST['from_day'] ) : '';
    if ( !$from_day && isset( $_GET['from_day'] ) && $_GET['from_day'] ) {
        $from_day = $_GET['from_day'];
    }

    $to_day = isset( $_POST['to_day'] ) ? sanitize_text_field( $_POST['to_day'] ) : '';
    if ( !$to_day && isset( $_GET['to_day'] ) && $_GET['to_day'] ) {
        $to_day = $_GET['to_day'];
    }

    $id         = get_option( 'admin_manage_order_show_id', 1 );
    $customer   = get_option( 'admin_manage_order_show_customer', 2 );
    $time       = get_option( 'admin_manage_order_show_time', 3 );
    $deposit    = get_option( 'admin_manage_order_show_deposit', 4 );
    $insurance  = get_option( 'admin_manage_order_show_insurance', 5 );
    $product    = get_option( 'admin_manage_order_show_product', 6 );
    $status     = get_option( 'admin_manage_order_show_order_status', 7 );

    ?>
    
    <div class="wrap">
        <form id="booking-filter" method="POST" action="<?php echo admin_url('/edit.php?post_type=product&page=ovabrw-manage-order'); ?>">
        	<h2><?php esc_html_e( 'Manage Order', 'ova-brw' ); ?></h2>
        	<div class="booking_filter">
            <?php if ( $id ): ?>
                <input type="text" name="order_number" value="<?php echo $order_number ?>" placeholder="<?php esc_html_e('Order ID', 'ova-brw'); ?>" class="" autocomplete="off"/>
            <?php endif; ?>

            <?php if ( $customer ): ?>
                <input type="text" name="name_customer" value="<?php echo $name_customer ?>" placeholder="<?php esc_html_e('Customer Name', 'ova-brw'); ?>" class="" autocomplete="off"/>
            <?php endif; ?>
            
            <?php if ( $time ): ?>
                <select name="check_in_out">
                    <option value=""><?php esc_html_e( 'All', 'ova-brw' ); ?></option>
                    <option value="check_in" <?php echo ( $check_in_out == 'check_in' ) ? 'selected' : ''; ?> >
                        <?php esc_html_e( 'Check-in date', 'ova-brw' ); ?>
                    </option>
                    <option value="check_out" <?php echo ( $check_in_out == 'check_out' ) ? 'selected' : ''; ?>>
                        <?php esc_html_e( 'Check-out date', 'ova-brw' ); ?>
                    </option>
                </select>
            <?php endif; ?>
        		<input 
                    type="text" 
                    name="from_day" 
                    value="<?php echo $from_day ?>" 
                    placeholder="<?php esc_html_e('From date', 'ova-brw'); ?>" 
                    class="ovabrw_datetimepicker ova-date-search date_book" 
                    autocomplete="off"/>
        		<input 
                    type="text" 
                    name="to_day" 
                    value="<?php echo $to_day ?>" 
                    placeholder="<?php esc_html_e('To date', 'ova-brw'); ?>" 
                    class="ovabrw_datetimepicker ova-date-search date_book" 
                    autocomplete="off" />
            <?php if ( $product ): ?>
        		<select name="room_id">
        			<option value="" <?php selected( '', $current_room_id, 'selected'); ?>>
                        <?php esc_html_e( 'Choose Product', 'ova-brw' ); ?>
                    </option>
        			<?php 
        				if ( $all_rooms->have_posts() ) : while ( $all_rooms->have_posts() ) : $all_rooms->the_post(); ?>
        					<option value="<?php the_id(); ?>" <?php selected( get_the_id(), $current_room_id, 'selected'); ?>>
                                <?php the_title(); ?>
                            </option>
        				<?php endwhile;endif;wp_reset_postdata();
        			?>
        		</select>
            <?php endif; ?>

            <?php if ( $status ): ?>
                <select name="filter_order_status" >
                    <option value="">
                        <?php esc_html_e( 'Order Status', 'ova-brw' ); ?>
                    </option>
                    <option value="wc-completed" <?php echo ( ( 'wc-completed' == $filter_order_status ) ) ? 'selected' : ''; ?>>
                        <?php esc_html_e( 'Completed', 'ova-brw' ); ?>
                    </option>
                    <option value="wc-processing" <?php echo ( ( 'wc-processing' == $filter_order_status ) ) ? 'selected' : ''; ?>>
                        <?php esc_html_e( 'Processing', 'ova-brw' ); ?>
                    </option>
                    <option value="wc-pending" <?php echo ( ( 'wc-pending' == $filter_order_status ) ) ? 'selected' : ''; ?>>
                        <?php esc_html_e( 'Pending payment', 'ova-brw' ); ?>
                    </option>
                    <option value="wc-on-hold" <?php echo ( ( 'wc-on-hold' == $filter_order_status ) ) ? 'selected' : ''; ?>>
                        <?php esc_html_e( 'On hold', 'ova-brw' ); ?>
                    </option>
                    <option value="wc-cancelled" <?php echo ( ( 'wc-cancelled' == $filter_order_status ) ) ? 'selected' : ''; ?>>
                        <?php esc_html_e( 'Cancel', 'ova-brw' ); ?>
                    </option>
                    <option value="wc-closed" <?php echo ( ( 'wc-closed' == $filter_order_status ) ) ? 'selected' : ''; ?>>
                        <?php esc_html_e( 'Closed', 'ova-brw' ); ?>
                    </option>
                </select>
            <?php endif; ?>
    			&nbsp;&nbsp;&nbsp;
    			<button type="submit" class="button">
                    <?php esc_html_e( 'Filter', 'ova-brw' ); ?>
                </button>
        	</div>
            <!-- For plugins, we also need to ensure that the form posts back to our current page -->
            <input type="hidden" name="post_type" value="product" />
            <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
            <!-- Now we can render the completed list table -->
            <?php $manage_booking->display() ?>
        </form>
    </div>

<?php }
