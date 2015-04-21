<?php

// Exit if accessed directly
if ( !defined('ABSPATH')) exit;

/** 
 * Widget content
 * 
 */

if ( function_exists('register_sidebar')) {
	register_sidebar( array(
		'name' => __( 'Primary Widget Area', THEME_LANG),
		'id' => 'primary-widget-area',
		'description' => __( 'The primary widget area', THEME_LANG),
		'before_widget' => '<section id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</section>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );
    
    register_sidebar( array(
		'name' => __( 'Shop Widget Area', THEME_LANG),
		'id' => 'shop-widget-area',
		'description' => __( 'The shop widget area', THEME_LANG),
		'before_widget' => '<section id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</section>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );
    
    register_sidebar( array(
		'name' => __( 'Blog Widget Area', THEME_LANG),
		'id' => 'blog-widget-area',
		'description' => __( 'The blog widget area', THEME_LANG),
		'before_widget' => '<section id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</section>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );
    
    $count = 4;
    
    for($i=1; $i<=$count;$i++){
        register_sidebar( array(
    		'name' => __( 'Sidebar '.$i, THEME_LANG),
    		'id' => 'sidebar-column-'.$i,
    		'description' => __( 'The sidebar column '.$i.' widget area', THEME_LANG),
    		'before_widget' => '<section id="%1$s" class="widget-container %2$s">',
    		'after_widget' => '</section>',
    		'before_title' => '<h3 class="widget-title">',
    		'after_title' => '</h3>',
    	) );
    }
    
    register_sidebar( array(
		'name' => __( 'Footer top', THEME_LANG),
		'id' => 'footer-top',
		'description' => __( 'The footer top widget area', THEME_LANG),
		'before_widget' => '<section id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</section>',
		'before_title' => '<h4 class="widget-title">',
		'after_title' => '</h4>',
	) );
    
    $count = 4;
    
    for($i=1; $i<=$count;$i++){
        register_sidebar( array(
    		'name' => __( 'Footer column '.$i, THEME_LANG),
    		'id' => 'footer-column-'.$i,
    		'description' => __( 'The footer column '.$i.' widget area', THEME_LANG),
    		'before_widget' => '<div id="%1$s" class="widget-container %2$s">',
    		'after_widget' => '</div>',
    		'before_title' => '<h3 class="widget-title">',
    		'after_title' => '</h3>',
    	) );
    }

}
/**
 * This code filters the categories widget to include the post count inside the link
 */
add_filter('wp_list_categories', 'cat_count_span');
function cat_count_span($links) {
    $links = str_replace('</a> (', ' (', $links);
    $links = str_replace(')', ')</a>', $links);
    return $links;
}

/**
 * This code filters the Archive widget to include the post count inside the link
 */
add_filter('get_archives_link', 'archive_count_span');
    function archive_count_span($links) {
    $links = str_replace('</a>&nbsp;(', ' (', $links);
    $links = str_replace(')', ')</a>', $links);
    return $links;
}



/**
 * Include widgets.
 *
 */

/* Widgets list */
/*
$kt_widgets = array(
	'widget_mailchimp.php',
    'widget_facebooklike.php',
    'widget_socials.php',
    'widget_article.php'
);

foreach ( $kt_widgets as $kt_widget ) {
	if ( $file_path = locate_template( THEME_WIDGETS . $kt_widget ) ) {
		require_once( $file_path );
	}
}
*/