<?php


// Exit if accessed directly
if ( !defined('ABSPATH')) exit;


/**
 * Define image sizes
 */
function kt_woocommerce_image_dimensions() {
	global $pagenow;
 
	if ( ! isset( $_GET['activated'] ) || $pagenow != 'themes.php' ) {
		return;
	}

  	$catalog = array('width' => '500','height' => '600', 'crop' => 1 );
    $thumbnail = array('width' => '500', 'height' => '600', 'crop' => 1 );
	$single = array( 'width' => '1000','height' => '1200', 'crop' => 1);
	
	// Image sizes
	update_option( 'shop_catalog_image_size', $catalog ); 		// Product category thumbs
	update_option( 'shop_single_image_size', $single ); 		// Single product image
	update_option( 'shop_thumbnail_image_size', $thumbnail ); 	// Image gallery thumbs
}
add_action( 'after_switch_theme', 'kt_woocommerce_image_dimensions', 1 );


/**
 * Change placeholder for woocommerce
 * 
 */
add_filter('woocommerce_placeholder_img_src', 'custom_woocommerce_placeholder_img_src');

function custom_woocommerce_placeholder_img_src( $src ) {
	return THEME_IMG . 'placeholder.png';
}
/**
 * Enable support for woocommerce after setip theme
 * 
 */
add_action( 'after_setup_theme', 'woocommerce_theme_setup' );
if ( ! function_exists( 'woocommerce_theme_setup' ) ):
    function woocommerce_theme_setup() {
        /**
    	 * Disable Woo styles (will use customized compiled copy)
    	 */ 
    	add_filter( 'woocommerce_enqueue_styles', '__return_false' );
        
        /**
    	 * Enable support for woocommerce
    	 */
        add_theme_support( 'woocommerce' );
    }
endif;

/**
 * Woocommerce tool link in header
 * 
 * @since 1.0
 */
function woocommerce_get_tool($id = 'woocommerce-nav'){
    
    global $wpdb, $yith_wcwl, $woocommerce;
    if ( kt_is_wc() ) { ?>
        <nav class="woocommerce-nav-container" id="<?php echo $id; ?>">
            <ul class="menu">
                    <?php /* if ( is_user_logged_in() ) { ?>
                    <li class='logout-link'> 
                        <a href="<?php echo wp_logout_url(); ?>"><?php _e('Logout', THEME_LANG) ?></a>
                    </li>
                    <?php } */ ?>
                    <li class='my-account-link'>                        
                        <a href="<?php echo get_permalink( get_option('woocommerce_myaccount_page_id') ); ?>" title="<?php _e('My Account','woothemes'); ?>"><?php _e('My Account','woothemes'); ?></a>
                    </li>
                <?php
                    if ( sizeof( $woocommerce->cart->cart_contents) > 0 ) :
                        echo "<li class='checkout-link'>";
                    	echo '<a href="' . $woocommerce->cart->get_checkout_url() . '" title="' . __( 'Checkout' ) . '">' . __( 'Checkout' ) . '</a>';
                        echo "</li>";
                    endif;
                ?>
                <?php 
                    if(class_exists('YITH_WCWL_UI')){
                        $count = array();
            	       
                		if( is_user_logged_in() ) {
                		    $count = $wpdb->get_results( $wpdb->prepare( 'SELECT COUNT(*) as `cnt` FROM `' . YITH_WCWL_TABLE . '` WHERE `user_id` = %d', get_current_user_id()  ), ARRAY_A );
                		    $count = $count[0]['cnt'];
                		} elseif( yith_usecookies() ) {
                		    $count[0]['cnt'] = count( yith_getcookie( 'yith_wcwl_products' ) );
                		    $count = $count[0]['cnt'];
                		} else {
                		    $count[0]['cnt'] = count( $_SESSION['yith_wcwl_products'] );
                		    $count = $count[0]['cnt'];
                		}
                        
                		if (is_array($count)) {
                			$count = 0;
                		}
                        echo "<li class='wishlist-link'>";
                            echo '<a href="'.$yith_wcwl->get_wishlist_url('').'">'.__("My Wishlist ", THEME_LANG).'<span>('.$count.')</span></a>';
                        echo "</li>";
                    }
                ?>
                <?php
                    if(defined( 'YITH_WOOCOMPARE' )){
                        echo "<li class='woocompare-link'>";
                        echo '<a href="#" class="yith-woocompare-open">'.__("Compare", THEME_LANG).'</a>';
                        echo "</li>";
                    }
                ?>
                <?php
            	/**
            	 * @hooked 
            	 */
            	do_action( 'woocommerce_get_tool' ); ?>
                
            </ul>
        </nav>
    <?php }
}

/**
 * Woocommerce cart in header
 * 
 * @since 1.0
 */
function woocommerce_get_cart(){
    $output = '';
    if ( kt_is_wc() ) {
        // Put your plugin code here
        global $woocommerce;
        $cart_total = $woocommerce->cart->get_cart_total();
		$cart_count = $woocommerce->cart->cart_contents_count;
        
        $output .= '<div class="shopping_cart">';
            $output .= '<a class="cart-contents" href="'.$woocommerce->cart->get_cart_url().'" title="'.__("View my shopping cart", THEME_LANG).'"><span class="cart-content-text">'.__('My Cart', THEME_LANG).'</span><span class="cart-content-total">'.$cart_total.'</span></a>';
            
            $output .= '<div class="shopping-bag">';
            $output .= '<div class="shopping-bag-wrapper mCustomScrollbar">';
            $output .= '<div class="shopping-bag-content">';
                if ( sizeof($woocommerce->cart->cart_contents)>0 ) {
                    $output .= '<div class="bag-products">';
                    foreach ($woocommerce->cart->cart_contents as $cart_item_key => $cart_item) {
                        $bag_product = $cart_item['data']; 
                        
                        if ($bag_product->exists() && $cart_item['quantity']>0) {
                            $output .= '<div class="bag-product clearfix">';
        					$output .= '<figure><a class="bag-product-img" href="'.get_permalink($cart_item['product_id']).'">'.$bag_product->get_image().'</a></figure>';                      
        					$output .= '<div class="bag-product-details">';
            					$output .= '<div class="bag-product-title"><a href="'.get_permalink($cart_item['product_id']).'">' . apply_filters('woocommerce_cart_widget_product_title', $bag_product->get_title(), $bag_product) . '</a></div>';
            					$output .= '<div class="bag-product-price">'.woocommerce_price($bag_product->get_price()).'</div>';
                                $output .= '<div class="bag-product-qty">'.__('Qty: ', THEME_LANG).$cart_item['quantity'].'</div>';
                                
        					$output .= '</div>';
        					$output .= apply_filters( 'woocommerce_cart_item_remove_link', sprintf('<a href="%s" class="remove" title="%s"></a>', esc_url( $woocommerce->cart->get_remove_url( $cart_item_key ) ), __('Remove this item', 'woocommerce') ), $cart_item_key );
        					
        					$output .= '</div>';
                        }
                    }
                    $output .= '</div>';
                }else{
                   $output .=  "<p class='cart_block_no_products'>".__('No products', THEME_LANG)."</p>"; 
                }
                
                $output .= '<div class="bag-total">'.__('Cart subtotal: ', THEME_LANG).$cart_total.'</div><!-- .bag-total -->';
                
                $output .= '<div class="bag-buttons clearfix">';
                    $output .= '<a href="'.esc_url( $woocommerce->cart->get_cart_url() ).'" class="btn btn-default btn-round pull-left">'.__('View cart', THEME_LANG).'</a>';
                    $output .= '<a href="'.esc_url( $woocommerce->cart->get_checkout_url() ).'" class="btn btn-default btn-round pull-left">'.__('Checkout', THEME_LANG).'</a>';
                $output .= '</div><!-- .bag-buttons -->';
            
            $output .= '</div><!-- .shopping-bag-content -->';
            $output .= '</div><!-- .shopping-bag-wrapper -->';
            $output .= '</div><!-- .shopping-bag -->';
           //$output .= "<script type='text/javascript'>jQuery('.mCustomScrollbar').mCustomScrollbar();</script>";
        $output .= '</div><!-- .shopping_cart -->';
        
        
        
    }
    return $output;
}




/**
 * Woocommerce replate cart in header
 * 
 */ 
function woocommerce_header_add_to_cart_fragment( $fragments ) {
	global $woocommerce;
	
    $fragments['.shopping_cart'] = woocommerce_get_cart();
	return $fragments;
}
add_filter('add_to_cart_fragments', 'woocommerce_header_add_to_cart_fragment');






/**
 * Woocommerce replace before main content and after main content
 * 
 */
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);

add_action('woocommerce_before_main_content', 'london_wrapper_start', 10);
add_action('woocommerce_after_main_content', 'london_wrapper_end', 10);

function london_wrapper_start() {
  echo '<div class="content-wrapper"><div class="container wc-container">';
}

function london_wrapper_end() {
  echo '</div><!-- .container --></div>';
}

/**
 * Add checkout button to cart page
 * 
 */
add_action('woocommerce_cart_actions', 'woocommerce_button_proceed_to_checkout');

/**
 * Woocommerce breadcrumb change order and navigation pipe
 * 
 */
remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0);
add_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 5, 0);

add_filter( 'woocommerce_breadcrumb_defaults', 'jk_woocommerce_breadcrumbs' );
function jk_woocommerce_breadcrumbs() {
    return array(
        'delimiter' => '<span class="navigation-pipe">&#47;</span>',
        'wrap_before' => '<div class="woocommerce-breadcrumb-wrapper"><div class="container"><nav class="woocommerce-breadcrumb" itemprop="breadcrumb">',
        'wrap_after' => '</nav></div></div>',
        'before' => '',
        'after' => '',
        'home' => _x( 'Home', 'breadcrumb', 'woocommerce' ),
    );
        
} 


/**
 * Change columns of shop
 * 
 */

add_filter( 'loop_shop_columns', 'london_woo_shop_columns' );
function london_woo_shop_columns( $columns ) {
    $layout = kt_option('shop_sidebar','full');
    if($layout == 'left' || $layout == 'right'){
        return 3;
    }else{
        return 4;
    }
    return $columns;
}





/**
 * Change layout of archive product
 * 
 */
add_filter( 'archive_product_layout', 'woocommerce_archive_product_layout' );
function woocommerce_archive_product_layout( $columns ) {
    $layout = kt_option('shop_sidebar', 'full');
    return $layout;
}

add_filter( 'woocommerce_product_loop_start', 'woocommerce_product_loop_start_callback' );
function woocommerce_product_loop_start_callback($classes){
    if(is_product_category() || is_shop() || is_product_tag()){
        $products_layout = kt_option('products-layout', 'grid');
        $classes .= ' '.$products_layout;
    }
    return $classes;
}
add_filter( 'woocommerce_gridlist_toggle', 'woocommerce_gridlist_toggle_callback' );
function woocommerce_gridlist_toggle_callback(){
    return kt_option('products-layout', 'grid');
}


/**
 * Change layout of single product
 * 
 */
add_filter( 'single_product_layout', 'london_single_product_layout' );
function london_single_product_layout( $columns ) {
    $layout = kt_option('product_sidebar', 'full');
    return $layout;
}

/**
 * Change layout of carousel single product
 * 
 */
add_filter( 'woocommerce_single_product_carousel', 'woocommerce_single_product_carousel_callback' );
function woocommerce_single_product_carousel_callback( $columns ) {
    $layout = kt_option('product_sidebar', 'full');
    if($layout == 'left' || $layout == 'right'){
        return '[[992,3], [768, 3], [480, 2]]';
    }else{
        return '[[992,4], [768, 3], [480, 2]]';
    }
    
}

/**
 * Change hook of archive-product.php
 * 
 */
function woocommerce_shop_loop_item_action_action_add(){
    echo "<div class='functional-buttons'>";
    echo '<a href="#" class="product-quick-view" data-id="'.get_the_ID().'"><span></span><i class="fa fa-spinner fa-spin"></i></a>';
    if(class_exists('YITH_WCWL_UI')){
        echo do_shortcode('[yith_wcwl_add_to_wishlist]');    
    }
    if(defined( 'YITH_WOOCOMPARE' )){
        echo do_shortcode('[yith_compare_button]');
    }
    echo "</div>";
}

/**
 * Change hook of archive-product.php
 * 
 */

remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20);
add_action( 'woocommerce_after_shop_loop', 'woocommerce_result_count', 20);

remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30);
add_action( 'woocommerce_after_shop_loop', 'woocommerce_catalog_ordering', 12);

add_action( 'woocommerce_before_shop_loop', 'woocommerce_gridlist_toggle', 40);



remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5);
remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10);
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);


add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_rating', 5);
add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_single_excerpt', 7);

add_action( 'woocommerce_shop_loop_item_image', 'woocommerce_template_loop_product_thumbnail', 5);
add_action( 'woocommerce_shop_loop_item_after_image', 'woocommerce_template_loop_add_to_cart', 5);

add_action( 'woocommerce_shop_loop_item_tools', 'woocommerce_template_loop_add_to_cart', 10);
add_action( 'woocommerce_shop_loop_item_tools', 'woocommerce_shop_loop_item_action_action_add', 10);

remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10);
add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_price', 20);

remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10);
add_action( 'woocommerce_shop_loop_item_after_image', 'woocommerce_show_product_loop_sale_flash', 10);

add_action( 'woocommerce_shop_loop_item_after_image', 'woocommerce_shop_loop_item_action_action_add', 10);


add_action( 'woocommerce_after_shop_loop_item_sale', 'woocommerce_after_shop_loop_item_sale_sale_price', 10, 2);
function woocommerce_after_shop_loop_item_sale_sale_price($product, $post){
    $sale_price_dates_to 	= ( $date = get_post_meta( $product->id, '_sale_price_dates_to', true ) ) ? date_i18n( 'Y-m-d', $date ) : '';
    if($sale_price_dates_to){
        echo '<div class="woocommerce-countdown clearfix" data-time="'.$sale_price_dates_to.'"></div>';
    }
}
add_action( 'woocommerce_after_shop_loop_item_sale', 'woocommerce_after_shop_loop_item_sale_rating', 20, 2);
function woocommerce_after_shop_loop_item_sale_rating($product, $post){
    echo "<div class='woocommerce-countdown-rating'>".$product->get_rating_html()."</div>"; 
}
add_action( 'woocommerce_after_shop_loop_item_sale', 'woocommerce_after_shop_loop_item_sale_short_description', 30, 2);
function woocommerce_after_shop_loop_item_sale_short_description($product, $post){
    echo apply_filters( 'woocommerce_short_description', $post->post_excerpt );
}
function woocommerce_gridlist_toggle(){ ?>
    <?php $gridlist = apply_filters('woocommerce_gridlist_toggle', 'grid') ?>
    <ul class="gridlist-toggle hidden-xs">
        <li><span><?php _e('View as:', THEME_LANG) ?></span></li>
		<li>
			<a <?php if($gridlist == 'lists'){ ?>class="active"<?php } ?> href="#" title="<?php _e('List view', THEME_LANG) ?>" data-layout="lists" data-remove="grid"><i class="fa fa-th-list"></i></a>
		</li>
		<li>
			<a <?php if($gridlist == 'grid'){ ?>class="active"<?php } ?> href="#" title="<?php _e('Grid view', THEME_LANG) ?>" data-layout="grid" data-remove="lists"><i class="fa fa-th-large"></i></a>
		</li>
	</ul>
<?php }
/**
 * Change hook of single-product.php
 * 
 */



remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10, 0);
add_action( 'woocommerce_after_single_product_content', 'woocommerce_output_product_data_tabs', 10, 0);

add_filter('woocommerce_product_description_heading', 'london_woocommerce_product_description_heading');
function london_woocommerce_product_description_heading(){
    return "";
}

remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40, 0);
add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 15);

remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10, 0);
add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 17, 0);



if(defined( 'YITH_WOOCOMPARE' )){
    global $yith_woocompare;
    remove_action( 'woocommerce_single_product_summary', array( $yith_woocompare->obj, 'add_compare_link' ), 35 );
}

add_filter('yith_wcwl_positions', 'yith_wcwl_positions_callback');
function yith_wcwl_positions_callback($positions){
    unset($positions['add-to-cart']);
    return $positions;
}


function woocommerce_shop_loop_item_action_action_product(){
    if(class_exists('YITH_WCWL_UI') || defined( 'YITH_WOOCOMPARE' )){
        echo "<div class='functional-buttons-product clearfix'>";
        echo "<div class='functional-buttons'>";
        if(class_exists('YITH_WCWL_UI')){
            echo do_shortcode('[yith_wcwl_add_to_wishlist]');    
        }
        if(defined( 'YITH_WOOCOMPARE' )){
            echo do_shortcode('[yith_compare_button]');
        }
        echo "</div>";
        echo "</div>";
    }
}
add_action( 'woocommerce_after_add_to_cart_button', 'woocommerce_shop_loop_item_action_action_product', 50);


function custom_stock_totals($availability_html, $availability_text, $variation) {
    $availability         = $variation->get_availability();
	$availability_html = '<p class="stock ' . esc_attr( $availability['class'] ) . '"><span>' . esc_html( $availability_text ) . '</span></p>';
	return 	$availability_html;
}
add_filter('woocommerce_stock_html', 'custom_stock_totals', 20, 3);


/**
 * Add share product 
 *
 * @since 1.0
 */
add_action( 'woocommerce_single_product_summary', 'theme_share_product_add_share', 50 );
function theme_share_product_add_share(){ 
    global $post;
    $addthis_id = kt_option('addthis_id');
    ?>
    <div class="clearfix"></div>
    <div class="product-details-share clearfix">
        <ul class="share clearfix">
            <li><a href="mailto:?subject=<?php echo get_the_title($post->ID); ?>&body=<?php echo get_permalink($post->ID); ?>"><i class="fa fa-envelope"></i></a></li>
            <li><a href="javascript:print();"><i class="fa fa-print"></i></a></li>
        </ul>
        <?php if($addthis_id){ ?>
            <div class="addthis_native_toolbox"></div>
        <?php } ?>
    </div><?php
}



/* cart hooks */
add_action('woocommerce_before_cart_table', 'kt_woocommerce_before_cart_table', 20);
function kt_woocommerce_before_cart_table( $args )
{
	global $woocommerce;

	$html = '<h3>' . sprintf( __( 'You Have %d Items In Your Cart', 'Avada' ), $woocommerce->cart->cart_contents_count ) . '</h3>';

	echo $html;
}

function kt_compare_css() {
     
    ?>
    <style type="text/css">
        table.compare-list .add-to-cart td a{
            font-family: Dosis;
            line-height: 1.42857;
            padding: 9px 30px;
            text-transform: none;
        }
        table.compare-list thead th, h1{
            font-family:Dosis;
            font-weight:300;
        }
    </style>
    <?php

}
add_action('wp_head', 'kt_compare_css');