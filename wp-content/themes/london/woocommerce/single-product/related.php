<?php
/**
 * Related Products
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product, $woocommerce_loop;

if ( empty( $product ) || ! $product->exists() ) {
	return;
}

$posts_per_page = apply_filters('woocommerce_single_product_related', 12);

$related = $product->get_related( $posts_per_page );

if ( sizeof( $related ) == 0 ) return;

$args = apply_filters( 'woocommerce_related_products_args', array(
	'post_type'            => 'product',
	'ignore_sticky_posts'  => 1,
	'no_found_rows'        => 1,
	'posts_per_page'       => $posts_per_page,
	'orderby'              => $orderby,
	'post__in'             => $related,
	'post__not_in'         => array( $product->id )
) );

$products = new WP_Query( $args );

$woocommerce_loop['columns'] = apply_filters( 'loop_shop_columns_addons', 1 );
$woocommerce_loop['columns_tablet'] = apply_filters( 'loop_shop_columns_addons_tablet', 1 );

if ( $products->have_posts() ) : ?>

	<div class="related-products clearfix carousel-wrapper-top">
        <div class="block-heading">
            <h2><?php _e( 'Related Products', 'woocommerce' ); ?></h2>
        </div>
        <div class="woocommerce-carousel-wrapper" data-theme="style-navigation-top" data-itemscustom="<?php echo apply_filters( 'woocommerce_single_product_carousel', '[[992,4], [768, 3], [480, 1]]'); ?>">
            <?php woocommerce_product_loop_start(); ?>
    			<?php while ( $products->have_posts() ) : $products->the_post(); ?>
    				<?php wc_get_template_part( 'content', 'product' ); ?>
    			<?php endwhile; // end of the loop. ?>
            <?php woocommerce_product_loop_end(); ?>
        </div><!-- .woocommerce-carousel-wrapper -->
	</div><!-- .related-products -->

<?php endif;

wp_reset_postdata();
