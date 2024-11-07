<?php if ( ! defined( 'ABSPATH' ) ) exit;

    if ( isset( $args['id'] ) && $args['id'] ) {
        $id = $args['id'];
    } else {
        $id = get_the_id();
    }

    // products
    $show_on_sale     =  isset( $args['show_on_sale'] )    ? $args['show_on_sale']      : 'no' ;
    $posts_per_page   =  isset( $args['posts_per_page'] )  ? $args['posts_per_page']    : 4 ;
    $orderby          =  isset( $args['product_orderby'] ) ? $args['product_orderby']   : 'ID' ;
    $order            =  isset( $args['product_order'] )   ? $args['product_order']     : 'DESC' ;
    $filter_title	  =  isset( $args['filter_title'] )    ? $args['filter_title']      : '' ;
    
    // categories
    $args_categories  =  isset( $args['categories'] ) ? $args['categories'] : array('all') ;
    if ( $args_categories === 'all') {
        $args_categories = array('all');
    }

    $pro_args = array(
       'taxonomy' => 'product_cat',
       'orderby'  => $args['orderby'],
       'order'    => $args['order']
    );

    $catAll      = isset( $args['catAll'] )  ? $args['catAll']  : esc_html__('All','ova-brw');
    $categories  = get_categories($pro_args);

    // Additional Options Slider
    $data_options['items']              = isset( $args['item_number'] )         ? $args['item_number']      : 1 ;
    $data_options['slideBy']            = isset( $args['slides_to_scroll'] )    ? $args['slides_to_scroll'] : 1 ;
    $data_options['margin']             = isset( $args['margin_items'] )        ? $args['margin_items']     : 0 ;
    $data_options['autoplayTimeout']    = isset( $args['autoplay_speed'] )      ? $args['autoplay_speed']   : 3000;
    $data_options['smartSpeed']         = isset( $args['smartspeed'] )          ? $args['smartspeed']       : 500;
    $data_options['autoplayHoverPause'] = $args['pause_on_hover']   === 'yes'   ? true : false;
    $data_options['loop']               = $args['infinite']         === 'yes'   ? true : false;
    $data_options['autoplay']           = $args['autoplay']         === 'yes'   ? true : false;
    $data_options['nav']                = $args['nav_control']      === 'yes'   ? true : false;
    $data_options['dots']               = $args['dots_control']     === 'yes'   ? true : false;
    $data_options['rtl']                = is_rtl() ? true : false;


?>

<div class="ova-product-filter-ajax"
    data-show_on_sale = "<?php echo esc_attr( $show_on_sale ); ?>"
    data-posts_per_page = "<?php echo esc_attr( $posts_per_page ); ?>"
    data-orderby = "<?php echo esc_attr( $orderby ); ?>"
    data-order = "<?php echo esc_attr( $order ); ?>"
>

    <ul class="product-filter-category">

    	<li class="filter-title">
            <?php echo esc_html($filter_title);?>
        </li>
        
        <li class="product-filter-button active-category" data-slug="all">
            <span class="category"><?php echo esc_html( $catAll ); ?></span>
            <i aria-hidden="true" class="icomoon icomoon-angle-right"></i>
        </li>
        
        <?php  if( !empty( $categories ) && is_array( $categories ) ) : ?>

            <?php foreach ( $categories as $category ) : 
                $name  = $category->name;
                $slug  = $category->slug;
            ?>

                <?php if( in_array( $slug, $args_categories ) ) : ?>

                    <li class="product-filter-button" data-slug="<?php echo esc_attr($slug);?>">
                        <span class="category"><?php echo esc_html( $name ); ?></span>
                        <i aria-hidden="true" class="icomoon icomoon-angle-right"></i>
                    </li>

                <?php endif; ?>

            <?php endforeach; ?>

        <?php endif; ?>

    </ul>


   
    <div class="content-item slide-product owl-carousel owl-theme" data-options="<?php echo esc_attr(json_encode($data_options)); ?>">
        
    </div>  

</div>