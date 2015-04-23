<?php

// Exit if accessed directly
if ( !defined('ABSPATH')) exit;

/*
 * Function check if WC Plugin installed
 */

function kt_is_wc(){
    return function_exists('is_woocommerce');
}

/**
 *  @true  if WPML installed.
 */
function  kt_is_wpml(){
    return function_exists('icl_get_languages');
}

/**
 * Get Page id - Supported WPML Plguin
 * @return page id
 */
function kt_get_page_id(  $ID , $post_type= 'page'){
    if(kt_is_wpml()){
        $ID =   icl_object_id($ID, $post_type , true) ;
    }
    return $ID;
}

/**
 *
 * Detect plugin.
 *
 * @param $plugin example: 'plugin-directory/plugin-file.php'
 */

function kt_is_active_plugin(   $plugin ){
    if(  !function_exists( 'is_plugin_active' ) ){
        include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
    }
    // check for plugin using plugin name
    return is_plugin_active( $plugin ) ;
}




/**
 * Add breadcrumb
 * 
 */
function kt_add_breadcrumb(){
    if( ! is_home() && ! is_front_page()  ){

        $show = true;
        if( is_page() || is_singular()  || is_front_page() ){
            $show  = rwmb_meta( '_kt_show_breadcrumb' );
        }
        if( $show ){
            ?>
                <?php if(function_exists('breadcrumb_trail')) { ?>
                <div class="breadcrumb-wrapper">
                    <div class="container">
                        <?php
                        if( is_woocommerce() ){
                            woocommerce_breadcrumb(
                                array(
                                    'delimiter' =>'<span class="sep navigation-pipe">&nbsp;</span>',
                                    'wrap_before' => '<nav class="woocommerce-breadcrumb breadcrumbs" ' . ( is_single() ? 'itemprop="breadcrumb"' : '' ) . '>',

                            ) );
                        }else{
                            breadcrumb_trail();
                        }

                        ?>
                    </div>
                </div>
                <?php } ?>
            <?php
        }
    }
}
add_action('theme_before_content', 'kt_add_breadcrumb');




/**
 * Extend the default WordPress body classes.
 *
 * @since 1.0
 *
 * @param array $classes A list of existing body class values.
 * @return array The filtered body class list.
 */
function theme_body_classes( $classes ) {
    global $post;
    
    $classes[] = 'theme-skin-'.kt_option('theme-skin', 'dark');
    
	if ( is_multi_author() ) {
		$classes[] = 'group-blog';
	}
    
    if( is_page() || is_singular('post')){
        $classes[] = 'layout-'.kt_getlayout($post->ID);
        $classes[] = rwmb_meta('_kt_extra_page_class');
    }else{
        $classes[] = 'layout-'.kt_option('layout');
    }

	return $classes;
}
add_filter( 'body_class', 'theme_body_classes' );

/**
 * Add class layout for main class
 *
 * @since 1.0
 *
 * @param string $classes current class
 * @param string $layout layout current of page 
 *  
 * @return array The filtered body class list.
 */
function kt_main_class_callback($classes, $layout){
    
    if($layout == 'left' || $layout == 'right'){
        $classes .= ' col-md-9 col-xs-12'; 
    }else{
        $classes .= ' col-md-12 col-xs-12';
    }
    
    if($layout == 'left'){
         $classes .= ' pull-right';
    }
    return $classes;
}
add_filter('kt_main_class', 'kt_main_class_callback', 10, 2);


/**
 * Add class layout for sidebar class
 *
 * @since 1.0
 *
 * @param string $classes current class
 * @param string $layout layout current of page 
 *  
 * @return array The filtered body class list.
 */
function kt_sidebar_class_callback($classes, $layout){
    if($layout == 'left' || $layout == 'right'){
        $classes .= ' col-md-3 col-xs-12'; 
    }
    return $classes;
}
add_filter('kt_sidebar_class', 'kt_sidebar_class_callback', 10, 2);



/**
 * Add class remove top or bottom padding
 *
 * @since 1.0
 */
function kt_content_class_callback($classes){
    global $post;
    if(is_page()){
        if(rwmb_meta('_kt_remove_top')){
            $classes .= ' remove_top_padding';
        }
        if(rwmb_meta('_kt_remove_bottom')){
            $classes .= ' remove_bottom_padding';
        }
    }
    return $classes;
} 
add_filter('kt_content_class', 'kt_content_class_callback');

/**
 * Add class sticky to header
 */
function theme_header_content_class_callback($classes){
    $sticky = kt_option('fixed_header', 1);
    if($sticky){
        $classes .= ' sticky-header';
    }
    return $classes;
}

add_filter('theme_header_content_class', 'theme_header_content_class_callback');


/**
 * Add slideshow header
 *
 * @since 1.0
 */
add_action( 'theme_slideshows_position', 'theme_slideshows_position_callback' );
function theme_slideshows_position_callback(){
    global $post;
    if(is_page() || is_singular('post')){
        
        $slideshow = rwmb_meta('_kt_slideshow_source');
        if($slideshow == 'revslider'){
            $revslider = rwmb_meta('_kt_rev_slider');
            if($revslider && class_exists( 'RevSlider' )){
                echo putRevSlider($revslider);
            }
        }elseif($slideshow == 'layerslider'){
            $layerslider = rwmb_meta('_kt_layerslider');
            if($layerslider && is_plugin_active( 'LayerSlider/layerslider.php' )){
                echo do_shortcode('[layerslider id="'.$layerslider.'"]');
            }
        }elseif($slideshow == 'custom_bg'){
            $img = rwmb_meta('_kt_custom_bg');
            $image = wp_get_attachment_url( $img );

            if ( $image ) {
                echo '<div class="page-bg-cover category-slide-container"><div class="container"><div class="cover-img" style="background-image: url(\''.esc_url( $image ).'\');"></div></div></div>';
            }
        }


    }elseif ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
        if(is_product_category()){
            
            	global $wp_query;
                $cat = $wp_query->get_queried_object();
                $thumbnail_id = get_woocommerce_term_meta( $cat->term_id, 'thumbnail_id', true );
                $image = wp_get_attachment_url( $thumbnail_id );
                if ( $image ) {
                    echo '<div class="page-bg-cover category-slide-container"><div class="container"><div class="cover-img" style="background-image: url(\''.esc_url( $image ).'\');"></div></div></div>';
                } 
        }else{

            // shop page
            if ( is_post_type_archive( 'product' ) && get_query_var( 'paged' ) == 0 ) {
                $shop_page   = wc_get_page_id( 'shop' ) ;

                if ( $shop_page ) {
                    $slideshow = rwmb_meta('_kt_slideshow_source', false, $shop_page);
                    if($slideshow == 'revslider'){
                        $revslider = rwmb_meta('_kt_rev_slider',  false, $shop_page);
                        if($revslider && class_exists( 'RevSlider' )){
                            echo putRevSlider($revslider);
                        }
                    }elseif($slideshow == 'layerslider'){
                        $layerslider = rwmb_meta('_kt_layerslider', false, $shop_page );
                        if($layerslider && is_plugin_active( 'LayerSlider/layerslider.php' )){
                            echo do_shortcode('[layerslider id="'.$layerslider.'"]');
                        }
                    }elseif($slideshow == 'custom_bg'){
                        $img = rwmb_meta('_kt_custom_bg', false, $shop_page );
                        $image = wp_get_attachment_url( $img );

                        if ( $image ) {
                            echo '<div class="page-bg-cover category-slide-container"><div class="container"><div class="cover-img" style="background-image: url(\''.esc_url( $image ).'\');"></div></div></div>';
                        }
                    }

                }
            }

        }

        /*

        */

    }
}

/**
 * Add title 
 *
 * @since 1.0
 */
add_action( 'theme_before_content', 'theme_before_content_add_title', 20 );
function theme_before_content_add_title(){

}


/**
 * Add class header
 *
 * @since 1.0
 * @return string
 */
add_filter('theme_header_class', 'theme_header_class_callback', 10, 2);

function theme_header_class_callback($class, $position){
    if($position == 'override'){
        $class .= ' header-absolute';
    }
    return $class;
}



/**
 * Add popup 
 *
 * @since 1.0
 */
add_action( 'theme_after_footer', 'theme_after_footer_add_popup', 20 );
function theme_after_footer_add_popup(){
    $enable_popup = kt_option( 'enable_popup' );
    $disable_popup_mobile = kt_option( 'disable_popup_mobile' );
    $content_popup = kt_option( 'content_popup' );
    $time_show = kt_option( 'time_show', 0 );
    
    if( $enable_popup == 1 ){ 
        if(!isset($_COOKIE['kt_popup'])){ ?>
            <div id="popup-wrap" data-mobile="<?php echo $disable_popup_mobile; ?>" data-timeshow="<?php echo $time_show; ?>">     
                <div class="white-popup-block">
                    <?php echo do_shortcode($content_popup); ?>
                </div>
            </div>
        <?php }
    }
}




function kt_blog_favicon() { 
    $custom_favicon = kt_option( 'custom_favicon' );
    $custom_favicon_iphone = kt_option( 'custom_favicon_iphone' );
    $custom_favicon_iphone_retina = kt_option( 'custom_favicon_iphone_retina' );
    $custom_favicon_ipad = kt_option( 'custom_favicon_ipad' );
    $custom_favicon_ipad_retina = kt_option( 'custom_favicon_ipad_retina' );
    
    ?>
    <!-- Favicons -->
    <?php if($custom_favicon['url']){ ?>
        <link rel="shortcut icon" href="<?php echo $custom_favicon['url'] ?>" />    
    <?php } ?>
	<?php if($custom_favicon_iphone['url']){ ?>
        <link rel="apple-touch-icon" href="<?php echo $custom_favicon_iphone['url'] ?>" />    
    <?php } ?>
    <?php if($custom_favicon_iphone_retina['url']){ ?>
        <link rel="apple-touch-icon" sizes="114x114" href="<?php echo $custom_favicon_iphone_retina['url'] ?>" />    
    <?php } ?>
    <?php if($custom_favicon_ipad['url']){ ?>
        <link rel="apple-touch-icon" sizes="72x72" href="<?php echo $custom_favicon_ipad['url'] ?>" />    
    <?php } ?>
    <?php if($custom_favicon_ipad_retina['url']){ ?>
        <link rel="apple-touch-icon" sizes="144x144" href="<?php echo $custom_favicon_ipad_retina['url'] ?>" />    
    <?php } ?>
<?php }
add_action('wp_head', 'kt_blog_favicon');


/**
 * Add share product 
 *
 * @since 1.0
 */
add_action( 'theme_head_bottom', 'theme_head_bottom_addthis_script', 50 );
function theme_head_bottom_addthis_script(){ 
    $addthis_id = kt_option('addthis_id');
    if($addthis_id){
        ?>
        <script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-<?php echo esc_attr( $addthis_id ); ?>" async="async"></script>
        <?php
    }
}


/**
 * Add search to header
 * 
 * 
 */
function kt_search_form(){
    if(kt_is_wc()){
        get_product_search_form();
    }else{
        get_search_form();
    }
}



/**
 * This code filters the categories widget to include the post count inside the link
 */
add_filter('wp_list_categories', 'kt_cat_count_span');
function kt_cat_count_span($links) {
    $links = str_replace('</a> (', ' (', $links);
    $links = str_replace(')', ')</a>', $links);
    return $links;
}

/**
 * This code filters the Archive widget to include the post count inside the link
 */
add_filter('get_archives_link', 'kt_archive_count_span');
function kt_archive_count_span($links) {
    $links = str_replace('</a>&nbsp;(', ' (', $links);
    $links = str_replace(')', ')</a>', $links);
    return $links;
}
