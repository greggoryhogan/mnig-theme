<?php
// Theme admin
require get_template_directory() . '/admin/index.php';

// Theme framework
require get_template_directory() . '/framework/framework.php';

// Plugin installation
require get_template_directory() . '/framework/helper/class-tgm-plugin-activation.php';
require get_template_directory() . '/framework/helper/required-plugins.php';

add_filter( 'auto_plugin_update_send_email', '__return_false' );

// Setup the theme

function cp_race_setup(){
  // Load text domain
  load_theme_textdomain('cp_race_theme', get_template_directory() . '/languages');

  // Register nav menu
  register_nav_menus(array('primary' => __('Header Navigation', 'cp_race_theme')));

  // Set content width 
  if (!isset($content_width)){ $content_width = 1170; }

  // Post thumbnails
  add_theme_support('post-thumbnails');

  // Image sizes
  add_image_size('team_member', 300, 300, true);
  add_image_size('portfolio_thumb', 640);
  add_image_size('portfolio_image', 725);
  add_image_size('blog_thumb', 320, 200, true);
  add_image_size('blog_image', 800);
  add_image_size('blog_header', 1300);

  // Post formats
  add_theme_support('post-formats', array('audio', 'video', 'quote', 'image', 'gallery', 'link'));

  add_theme_support( 'custom-header' );
  add_theme_support( 'custom-background' );
  add_theme_support( 'automatic-feed-links' );

  if ( function_exists( '_wp_render_title_tag' ) ) {
    add_theme_support( 'title-tag' );
  }
}

add_action('after_setup_theme', 'cp_race_setup');

//Filter title
if ( ! function_exists('cp_race_filter_title') && ! function_exists( '_wp_render_title_tag' ) ){
  function cp_race_filter_title( $title, $sep ) {
    global $paged, $page;
   
    if ( is_feed() ) {
      return $title;
    }
   
    $title .= get_bloginfo( 'name' );
   
    $site_description = get_bloginfo( 'description', 'display' );
    if ( $site_description && ( is_home() || is_front_page() ) ) {
      $title = "$title $sep $site_description";
    }
    if ( $paged >= 2 || $page >= 2 ) {
      $title = sprintf( __( 'Page %s', 'mayer' ), max( $paged, $page ) ) . " $sep $title";
    } 
    return $title;
   
  }
  add_filter( 'wp_title', 'cp_race_filter_title', 10, 2 );
}


// Enqueue scripts and styles

function cp_race_enqueue_scripts(){
  // Styles
  $theme_info = wp_get_theme();
  $version = $theme_info->get( 'Version' );
  wp_register_style('bootstrap', get_template_directory_uri() .'/css/bootstrap.min.css');  
  wp_register_style('flexslider', get_template_directory_uri() .'/css/flexslider.css');
  //wp_register_style('animate', get_template_directory_uri() .'/css/animate.css');
  wp_register_style('fontello', get_template_directory_uri() .'/css/fontello.css');
  wp_register_style('owl-carousel', get_template_directory_uri() .'/css/owl.carousel.css');
  wp_register_style('owl-theme', get_template_directory_uri() .'/css/owl.theme.css');
  
  wp_enqueue_style('bootstrap'); 
  wp_enqueue_style('flexslider');
  //wp_enqueue_style('animate');
  wp_enqueue_style('fontello');
  wp_enqueue_style('owl-carousel');
  wp_enqueue_style('owl-theme');
  wp_enqueue_style('theme', get_stylesheet_uri(), 'bootstrap');

  $protocol = is_ssl() ? 'https' : 'http';
  
  $primary_font = cp_race_primary_font(); 
  if (!empty($primary_font)) {
    wp_enqueue_style($primary_font, "$protocol://fonts.googleapis.com/css?family=".$primary_font);
  } else{
    wp_enqueue_style('lato', "$protocol://fonts.googleapis.com/css?family=Montserrat:400,700");
  }

  $secondary_font = cp_race_secondary_font();
  if (!empty($secondary_font)) {
    wp_enqueue_style($secondary_font, "$protocol://fonts.googleapis.com/css?family=".$secondary_font);
  } else{    
    wp_enqueue_style('montserrat', "$protocol://fonts.googleapis.com/css?family=PT+Serif:400italic");    
  }  

  // Scripts
  wp_register_script('plugins', get_template_directory_uri() .'/js/plugins.js', 'jquery', false, true);
  wp_register_script('main', get_template_directory_uri() .'/js/main.js', 'jquery', $version, true);  

  wp_enqueue_script('jquery');
  wp_enqueue_script('plugins');

  wp_enqueue_script('main');
  /*if (is_single()) {
    wp_enqueue_script('comment-reply');
  }*/
}

add_action('wp_enqueue_scripts', 'cp_race_enqueue_scripts');


$cp_race_page_on_front = get_option('page_on_front');
$race_options = $global_theme_options;

// Search Filter
function cp_race_search_filter($query) {
  if ($query->is_search) {
    $query->set('post_type', 'post');
  }
  return $query;
}

add_filter('pre_get_posts','cp_race_search_filter');


// Comments template
function cp_race_comments( $comment, $args, $depth ) {
    $GLOBALS['comment'] = $comment; ?>
    <li id="comment-<?php comment_ID(); ?>" <?php comment_class(); ?>>
    <div <?php comment_class(); ?> class="comment">

      <div class="col-xs-2 author-avatar">
        <?php echo get_avatar( $comment, 80 ); ?>          
      </div>
      <div class="col-xs-10">
        <h5><?php echo get_comment_author_link(); ?></h5>
        <span class="comment-date"><?php _e('Posted on', 'cp_race_theme'); ?> <?php echo get_comment_date(); ?> <?php _e('at', 'cp_race_theme'); ?> <?php echo get_comment_time(); ?></span>
        <?php if ($comment->comment_approved == '0') : ?>
          <em><?php _e('Your comment is awaiting moderation.', 'cp_race_theme') ?></em>
        <?php endif; ?>
        <?php comment_text(); ?>
        <?php 
        comment_reply_link( array_merge( $args, array( 
          'reply_text' => __('Reply', 'cp_race_theme'),
          'depth' => $depth,
          'max_depth' => $args['max_depth'] 
        ) ) ); ?>
      </div>
    </div>
    <?php    
}


// Custom image size function
function cp_race_get_image_id_from_url( $attachment_url = '' ) { 
  global $wpdb;
  $query = "SELECT ID FROM {$wpdb->posts} WHERE guid='$attachment_url'";
  $id = $wpdb->get_var($query);
  return $id;
}

function modify_read_more_link() {
  return sprintf('<a class="more-link" href="%s">%s</a>', get_permalink(), __("Continue Reading..",'cp_race_theme'));
}

add_filter( 'the_content_more_link', 'modify_read_more_link' );

function cp_race_custom_pagination() {
  global $wp_query;
  $big = 999999999;
  $pages = paginate_links( array(
          'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
          'format' => '?paged=%#%',
          'current' => max( 1, get_query_var('paged') ),
          'total' => $wp_query->max_num_pages,
          'prev_next' => false,
          'type'  => 'array',
          'prev_next'   => TRUE,
          'prev_text'    => '&laquo;',
          'next_text'    => '&raquo;',
      ) );
  if( is_array( $pages ) ) {
    $paged = ( get_query_var('paged') == 0 ) ? 1 : get_query_var('paged');
    echo '<ul class="pagination">';
    foreach ( $pages as $page ) {
      echo "<li>$page</li>";
    }
    echo '</ul>';
  }
}

// Enable font size & font family selects in the editor
if ( ! function_exists( 'cp_race_wpex_mce_buttons' ) ) {
  function cp_race_wpex_mce_buttons( $buttons ) {
    array_unshift( $buttons, 'fontsizeselect' ); // Add Font Size Select
    return $buttons;
  }
}
add_filter( 'mce_buttons_2', 'cp_race_wpex_mce_buttons' );

// Customize mce editor font sizes
if ( ! function_exists( 'cp_race_wpex_mce_text_sizes' ) ) {
  function cp_race_wpex_mce_text_sizes( $initArray ){
    $initArray['fontsize_formats'] = "9px 10px 12px 13px 14px 16px 18px 20px 21px 24px 28px 32px 36px";
    return $initArray;
  }
}
add_filter( 'tiny_mce_before_init', 'cp_race_wpex_mce_text_sizes' );

// Custom Excerpt
function cp_race_preview_excerpt_length( $length ) {
  return 40;
}
add_filter( 'excerpt_length', 'cp_race_preview_excerpt_length', 999 );

function cp_race_new_excerpt_more( $more ) {
  return '...';
}
add_filter('excerpt_more', 'cp_race_new_excerpt_more');

//Remove Gutenberg Block Library CSS from loading on the frontend
function smartwp_remove_wp_block_library_css(){
  wp_dequeue_style( 'wp-block-library' );
  wp_dequeue_style( 'wp-block-library-theme' );
  wp_dequeue_style( 'wc-blocks-style' ); // Remove WooCommerce block CSS
 } 
 add_action( 'wp_enqueue_scripts', 'smartwp_remove_wp_block_library_css', 100 );

 /**
 * Disable the emoji's
 */
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
?>