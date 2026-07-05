<?php
define( 'THEME_DIR', get_template_directory() );
define( 'INC_DIR', THEME_DIR. '/inc' );

add_filter( 'auto_plugin_update_send_email', '__return_false' );

/*
 *
 * Enqueue theme styles
 * 
 */ 
function enqueue_theme_scripts() {
    $version = wp_get_theme()->get('Version');
    

    wp_register_style( 'bootstrap-styles', get_template_directory_uri() .'/assets/bootstrap.min.css', array(), $version);
    wp_enqueue_style( 'bootstrap-styles' );  

    //wp_register_style( 'google-font', 'https://fonts.googleapis.com/css2?family=Montserrat:wght@100;200;300;400;600&display=swap', array(), $version);
    wp_register_style( 'google-font', 'https://fonts.googleapis.com/css2?family=Hind:wght@300;400;500;600;700&display=swap', array(), $version);

    wp_enqueue_style( 'google-font' );  
    

        
    
    wp_register_script('boostrap-js', get_template_directory_uri() .'/assets/bootstrap.min.js', array('jquery'),$version, true);
    wp_enqueue_script('boostrap-js');

    //AOS
    wp_register_style( 'aos-styles', get_template_directory_uri() .'/assets/aos.css', array(), $version);
    wp_enqueue_style( 'aos-styles' );  
    wp_register_script('aos-js', get_template_directory_uri() .'/assets/aos.js', array('jquery'),$version, true);
    wp_enqueue_script('aos-js');

    //theme
    wp_register_style( 'theme-styles', get_template_directory_uri() .'/style.css', array(), $version);
    wp_enqueue_style( 'theme-styles' );

    //lozad
		wp_register_script('lozad', get_template_directory_uri() .'/assets/js/lozad.min.js', array('jquery'),$version,true);
		wp_enqueue_script('lozad');

    //theme scripts
    wp_register_script('theme-js', get_template_directory_uri() .'/assets/js/scripts.js', array('jquery'),$version, true);
    wp_enqueue_script('theme-js');

    
    //remove classic wp theme
    wp_deregister_style('classic-theme-styles');
    wp_dequeue_style('classic-theme-styles');

    wp_dequeue_style( 'wp-block-library' ); // WordPress core
    wp_dequeue_style( 'wp-block-library-theme' ); // WordPress core
    wp_dequeue_style( 'wc-block-style' ); // WooCommerce
    wp_dequeue_style( 'wc-blocks-style' );
    //wp_dequeue_style( 'storefront-gutenberg-blocks' ); /

} 
add_action( 'wp_enqueue_scripts', 'enqueue_theme_scripts' );

//theme functions 
require( INC_DIR .'/theme-functions.php' );

add_theme_support( 'menus' );
add_theme_support( 'title-tag' );
add_theme_support( 'post-thumbnails' );
add_theme_support( 'responsive-embeds' );
remove_action('wp_head', 'wp_generator');

function remove_image_zoom_support() {
  add_theme_support( 'wc-product-gallery-zoom' );
}
add_action( 'wp', 'remove_image_zoom_support', 100 );

function greggs_menus() {
    register_nav_menu('main',__( 'Main' ));
  }
  add_action( 'init', 'greggs_menus' );

  function testimonials() {
	$testimonials = '<div class="testimonials">';

	$testimonials .= '<div class="content-column one_half"><i class="fas fa-quote-left"></i><p>Gregg was exceptional.  He completed the work timely and to my specifications.  He listened well and took the time to understand my requirements.  I would not hesitate to use him in the future.</p></div>';//<span>John Stack, 3 Big Heads</span>
	$testimonials .=  '<div class="content-column one_half last_column"><i class="fas fa-quote-left"></i><p>Gregg has been once again very fast in both his work and communication. This is the second time we have used him, and we will again!</p></div>';	

		$testimonials .= '</div>';

	return $testimonials;
}
add_shortcode('testimonials', 'testimonials');

function clients() {
	$clients = '<div class="clients">';

	$clients .= '<a href="https://henryusa.com" target="_blank" rel="nofollow"><img class="lozad" data-src="https://mynameisgregg.com/wp-content/uploads/2018/11/henry.jpg" /></a>';

	$clients .= '<a href="http://playtime.pem.org/" target="_blank" rel="nofollow"><img class="lozad" data-src="https://mynameisgregg.com/wp-content/uploads/2018/11/playtime-1.jpg" /></a>';

	$clients .= '<a href="https://projectmepro.com/" target="_blank" rel="nofollow"><img class="lozad" data-src="https://mynameisgregg.com/wp-content/uploads/2018/11/pmp-1.jpg" /></a>';

	$clients .= '<a href="https://www.margebar.com/" target="_blank" rel="nofollow"><img class="lozad" data-src="https://mynameisgregg.com/wp-content/uploads/2018/11/marge-2.jpg" /></a>';

	$clients .= '<a href="https://southshorerealtors.com/" target="_blank" rel="nofollow"><img class="lozad" data-src="https://mynameisgregg.com/wp-content/uploads/2018/11/ssr.jpg" /></a>';
	
	

	

	$clients .= '</div>';

	return $clients;
}
add_shortcode('clients', 'clients');

function disable_emojis() {
  remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
  remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
  remove_action( 'wp_print_styles', 'print_emoji_styles' );
  remove_action( 'admin_print_styles', 'print_emoji_styles' ); 
  remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
  remove_filter( 'comment_text_rss', 'wp_staticize_emoji' ); 
  remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
  add_filter( 'tiny_mce_plugins', 'disable_emojis_tinymce' );
  add_filter( 'wp_resource_hints', 'disable_emojis_remove_dns_prefetch', 10, 2 );
 }
 add_action( 'init', 'disable_emojis' );
 
 /**
  * Filter function used to remove the tinymce emoji plugin.
  * 
  * @param array $plugins 
  * @return array Difference betwen the two arrays
  */
 function disable_emojis_tinymce( $plugins ) {
  if ( is_array( $plugins ) ) {
  return array_diff( $plugins, array( 'wpemoji' ) );
  } else {
  return array();
  }
 }
 
 /**
  * Remove emoji CDN hostname from DNS prefetching hints.
  *
  * @param array $urls URLs to print for resource hints.
  * @param string $relation_type The relation type the URLs are printed for.
  * @return array Difference betwen the two arrays.
  */
 function disable_emojis_remove_dns_prefetch( $urls, $relation_type ) {
  if ( 'dns-prefetch' == $relation_type ) {
  /** This filter is documented in wp-includes/formatting.php */
  $emoji_svg_url = apply_filters( 'emoji_svg_url', 'https://s.w.org/images/core/emoji/2/svg/' );
 
 $urls = array_diff( $urls, array( $emoji_svg_url ) );
  }
 
 return $urls;
 }

 add_filter('woocommerce_admin_meta_boxes_variations_per_page', function() {
  return PHP_INT_MAX;
});


// Remove product images from the shop loop
//remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10 );

add_filter( 'woocommerce_thankyou_order_received_text', 'misha_thank_you_subtitle', 20, 2 );

function misha_thank_you_subtitle( $thank_you_title, $order ){

	return 'Your order has been received! If you orded a custom book <strong>MARK</strong> you should expect an email shortly to confirm your details. <br>Thank you for supporting my weird hobbies.<br>-Gregg';

}
add_filter('woocommerce_single_product_zoom_options', 'custom_single_product_zoom_options', 10, 3 );
function custom_single_product_zoom_options( $zoom_options ) {
// Disable zoom magnify:
  $zoom_options['magnify'] = 0;
  return $zoom_options;
}

add_action('template_redirect','send_shop_to_single');
function send_shop_to_single() {
  if(function_exists('is_shop')) {
    if(is_shop()) {
      wp_redirect( get_bloginfo('url').'/product/book-mark-marks/', 302 );
    }
  }
}