<?php 
/*
 *
 * Feather icon func
 *
 */
function featherIcon($icon,$classes = NULL, $size = NULL) {
    ob_start(); 
    include('feather-icons/'.$icon.'.svg');
    $icon_url = ob_get_clean();
    $icon_html = '<span class="feather-icon '.$classes.'"';
    if($size) {
        $icon_html .= ' style="width:'.$size.'px;height:'.$size.'px;padding-bottom:0;font-size:'.$size.'px;"';
    }
    $icon_html .= ' role="presentation">'.$icon_url.'</span>';
    return $icon_html;
} 

add_filter('wp_nav_menu_items', 'add_admin_link', 10, 2);
function add_admin_link($items, $args){
    if( $args->theme_location == 'main' ){
        $items .= '<li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-322"><button class="simple-button pl-0 pagejump orange-hover" data-scrollto="contact"">Contact</button></li>';
        $items .= '<li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-323"><a href="'.get_bloginfo('url').'/cart/">Cart</a></li>';
    }
    return $items;
}

/*
 *
 * Create custom post types for theme
 *
 */    
function create_site_custom_post_types() {
    
    //project cpt for portfolio
    register_post_type( 'project',
        array(
            'labels' => array(
                'name' => __( 'Projects' ),
                'singular_name' => __( 'Project' )
            ),
            'public' => true,
            'hierarchical'        => false,
            'show_in_menu'        => true,
            'has_archive' => true,
            'rewrite' => array('slug' => 'project'),
            'show_in_rest' => true,
            'supports' => array( 'title', 'editor', 'thumbnail',),
        )
    );

    //catergories for projects
    register_taxonomy('project-category',array('project'), array(
        'labels' => array(
            'name' => __( 'Project Categories' ),
            'singular_name' => __( 'Project Category' )
        ),
        'hierarchical' => false,
        'show_ui' => true,
        'show_in_rest' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => array( 'slug' => 'project-category' ),
      ));

}
add_action( 'init', 'create_site_custom_post_types', 10 );

add_action('template_redirect','no_single_cpts');
function no_single_cpts() {
    if(is_single() && get_post_type() == 'project') {
        $single_checked = get_post_meta(get_the_ID(),'project-single-view',true);
        if($single_checked != 1) {
            if(get_current_user_id() == 0) {
                wp_redirect(get_bloginfo('url').'/web-development/');
                exit;
            }
        }
    }
}

add_action('wp_footer','gregg_ga');
function gregg_ga() {
    ?>
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-129288316-1"></script>
    <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'UA-129288316-1');
    </script>
    <?php 
}

function gregg_portfolio($category,$max, $view_all = true) {
    
    if(is_single()) {
        $the_query = new WP_Query( array(
            'post_type' => 'project',
            'post__not_in' => array(get_the_ID()),
            'tax_query' => array(
                array (
                    'taxonomy' => 'project-category',
                    'field' => 'slug',
                    'terms' => $category,
                )
            ),
            'posts_per_page' => $max,
            'no_found_rows' => true, // counts posts, remove if you need pagination
            'update_post_term_cache' => false, // queries terms, remove if you need categories or tags
        ) );
    } else {
        $the_query = new WP_Query( array(
            'post_type' => 'project',
            'tax_query' => array(
                array (
                    'taxonomy' => 'project-category',
                    'field' => 'slug',
                    'terms' => $category,
                )
            ),
            'posts_per_page' => $max,
            'no_found_rows' => true, // counts posts, remove if you need pagination
	        'update_post_term_cache' => false, // queries terms, remove if you need categories or tags
        ) );
    }
    if($the_query->have_posts()) {
        echo '<div class="ml-3 mr-3">';
            echo '<div class="portfolio-display row gx-3 gy-3 justify-content-center">';
                while ( $the_query->have_posts() ) :
                    $the_query->the_post();
                    $post_id = get_the_ID();
                    $bg = get_post_meta($post_id,'project_background_image_id',true);
                    $bg_image = wp_get_attachment_image_src($bg,'full');
                    echo '<div class="portfolio-container text-center col-sm-12 col-md-6 col-lg-3">';
                        echo '<div data-background-image="'.$bg_image[0].'" class="lozad project '.get_post_meta($post_id,'project-classes',true).'">';
                            echo '<div class="brand">';
                                if(has_post_thumbnail()) {
                                    echo get_the_post_thumbnail( $page->ID, 'full',array( 'class' => 'lozad' ) );
                                } else {
                                    echo '<h3>'.get_the_title().'</h3>';
                                }
                            echo '</div>';
                            echo '<div class="overlay" style="background-color:'.get_post_meta($post_id,'client-background-color',true).'"></div>';
                            echo '<div class="content pl-4 pb-2 pr-4 pt-2 d-flex align-items-center flex-column justify-content-center desktop-only">';
                                echo apply_filters('the_content',get_post_meta($post_id,'project-teaser',true));
                                $single_checked = get_post_meta($post_id,'project-single-view',true);
                                if($single_checked == 1) {
                                    echo '<a href="'.get_permalink().'" aria-label="View Project" class="btn btn-primary mt-2 pt-2 pl-3 pr-2 pt-1 pb-1">View Project'. featherIcon('chevron-right','hover-ml-2').'</a>';
                                }
                                $link = get_post_meta($post_id,'project-link',true);
                                if($link) {
                                    echo '<a href="'.$link.'" rel="nofollow" target="_blank" aria-label="Open project in new window" class="btn btn-primary mt-2 pt-2 pl-3 pr-2 pt-1 pb-1">Visit Website'. featherIcon('chevron-right','hover-ml-2').'</a>';
                                }
                                

                            echo '</div>';

                            
                        echo '</div>';
                    echo '</div>';

                    echo '<div class="mobile-only mobile-links w-100">';
                        echo '<p>'.get_post_meta($post_id,'project-teaser',true).'</p>';
                        if($single_checked == 1) {
                            echo '<a href="'.get_permalink().'" aria-label="View Project" class="btn btn-primary mt-2 pt-2 pl-3 pr-2 pt-1 pb-1 mr-1 mb-4">View Project'. featherIcon('chevron-right','hover-ml-2').'</a>';
                        }
                        if($link) {
                            echo '<a href="'.$link.'" rel="nofollow" target="_blank" aria-label="Open project in new window" class="btn btn-primary ml-1 mt-2 pt-2 pl-3 pr-2 pt-1 pb-1 mb-4">Visit Website'. featherIcon('chevron-right','hover-ml-2').'</a>';
                        }
                    echo '</div>';
                endwhile;
            echo '</div>';
        echo '</div>';
        if($view_all) {
            $term_link = get_term_link($category,'project-category');
            if(!is_wp_error($term_link)) {
                echo '<a href="'.$term_link.'" title="View all" class="portfolio-link">View all</a>';
            }
        }
    } else {
        echo '<p class="font-italic">Portfolio under development</p>';
    }
    wp_reset_postdata();
}

//Add client logo meta box
add_action( 'add_meta_boxes', 'client_logo_add_metabox' );
function client_logo_add_metabox () {
	add_meta_box( 'clientclientlistingimagediv', __( 'Project Atts', 'text-domain' ), 'client_logo_metabox', 'project', 'side', 'low');

    add_meta_box( 'pageintro', __( 'Page Atts', 'text-domain' ), 'page_metabox', 'page', 'side', 'low');
}

function client_logo_metabox ( $post ) {
	global $content_width, $_wp_additional_image_sizes;

	$image_id = get_post_meta( $post->ID, 'project_background_image_id', true );

	$old_content_width = $content_width;
	$content_width = 254;
    echo '<label>Background</label>';
	if ( $image_id && get_post( $image_id ) ) {

		if ( ! isset( $_wp_additional_image_sizes['post-thumbnail'] ) ) {
			$thumbnail_html = wp_get_attachment_image( $image_id, array( $content_width, $content_width ) );
		} else {
			$thumbnail_html = wp_get_attachment_image( $image_id, 'post-thumbnail' );
		}

		if ( ! empty( $thumbnail_html ) ) {
			$content = $thumbnail_html;
			$content .= '<p class="hide-if-no-js"><a href="javascript:;" id="remove_client_logo_button" >' . esc_html__( 'Remove background image', 'text-domain' ) . '</a></p>';
			$content .= '<input type="hidden" id="upload_client_logo" name="project_background_image" value="' . esc_attr( $image_id ) . '" />';
		}

		$content_width = $old_content_width;
	} else {

		$content = '<img src="" style="width:' . esc_attr( $content_width ) . 'px;height:auto;border:0;display:none;" />';
		$content .= '<p class="hide-if-no-js"><a title="' . esc_attr__( 'Set background image', 'text-domain' ) . '" href="javascript:;" id="upload_client_logo_button" id="set-listing-image" data-uploader_title="' . esc_attr__( 'Choose an image', 'text-domain' ) . '" data-uploader_button_text="' . esc_attr__( 'Set background image', 'text-domain' ) . '">' . esc_html__( 'Set background image', 'text-domain' ) . '</a></p>';
		$content .= '<input type="hidden" id="upload_client_logo" name="project_background_image" value="" />';

	}

    $content .= '<br><label>Background Color</label><input type="text" name="client-background-color" value="'.get_post_meta($post->ID,'client-background-color',true).'" />';
    $content .= '<br><label>Link</label><input type="text" name="project-link" value="'.get_post_meta($post->ID,'project-link',true).'" />';
    $content .= '<br><label>Classes</label><input type="text" name="project-classes" value="'.get_post_meta($post->ID,'project-classes',true).'" />';
    $content .= '<br><label>Teaser</label><textarea name="project-teaser" rows="10">'.get_post_meta($post->ID,'project-teaser',true).'</textarea>';
    $checked = get_post_meta($post->ID,'project-single-view',true);
    echo $checked;
    if($checked == 1) {
        $checkmark = ' checked="checked"';
    }
    $content .= '<br><label for="project-single-view">Link to Single View?</label><input type="checkbox" name="project-single-view" autocomplete="false" value="1" '.$checkmark.' />';
	echo $content;
}

add_action( 'save_post', 'client_logo_save', 10, 1 );
function client_logo_save ( $post_id ) {
	if( isset( $_POST['project_background_image'] ) ) {
		$image_id = (int) $_POST['project_background_image'];
		update_post_meta( $post_id, 'project_background_image_id', $image_id );
	}
    if( isset( $_POST['client-background-color'] ) ) {
		$color = $_POST['client-background-color'];
		update_post_meta( $post_id, 'client-background-color', $color );
	}
    if( isset( $_POST['project-link'] ) ) {
		$link = $_POST['project-link'];
		update_post_meta( $post_id, 'project-link', $link );
	}
    if( isset( $_POST['project-classes'] ) ) {
		$link = $_POST['project-classes'];
		update_post_meta( $post_id, 'project-classes', $link );
	}
    if( isset( $_POST['project-teaser'] ) ) {
		$link = $_POST['project-teaser'];
		update_post_meta( $post_id, 'project-teaser', $link );
	}
    $checked = 0;
    if( isset( $_POST['project-single-view'] ) ) {
        $checked = $_POST['project-single-view'];
    } 
	update_post_meta( $post_id, 'project-single-view', $checked );

    //pages
    if( isset( $_POST['post-teaser'] ) ) {
		$link = $_POST['post-teaser'];
		update_post_meta( $post_id, 'post-teaser', $link );
	}
}

function page_metabox ( $post ) {
    $content = '<br><label>Teaser</label><textarea name="post-teaser" rows="10">'.get_post_meta($post->ID,'post-teaser',true).'</textarea>';
    echo $content;
}
//js for custom images
add_action('admin_footer','save_client_logo_js');
function save_client_logo_js() {
    ?>
    <script>
    jQuery(document).ready(function($) {
    // Uploading files
    var file_frame;

    jQuery.fn.upload_listing_image = function( button ) {
        var button_id = button.attr('id');
        var field_id = button_id.replace( '_button', '' );

        // If the media frame already exists, reopen it.
        if ( file_frame ) {
        file_frame.open();
        return;
        }

        // Create the media frame.
        file_frame = wp.media.frames.file_frame = wp.media({
        title: jQuery( this ).data( 'uploader_title' ),
        button: {
            text: jQuery( this ).data( 'uploader_button_text' ),
        },
        multiple: false
        });

        // When an image is selected, run a callback.
        file_frame.on( 'select', function() {
        var attachment = file_frame.state().get('selection').first().toJSON();
        jQuery("#"+field_id).val(attachment.id);
        jQuery("#clientclientlistingimagediv img").attr('src',attachment.url);
        jQuery( '#clientclientlistingimagediv img' ).show();
        jQuery( '#' + button_id ).attr( 'id', 'remove_client_logo_button' );
        jQuery( '#remove_client_logo_button' ).text( 'Remove listing image' );
        });

        // Finally, open the modal
        file_frame.open();
    };

    jQuery('#clientclientlistingimagediv').on( 'click', '#upload_client_logo_button', function( event ) {
        event.preventDefault();
        jQuery.fn.upload_listing_image( jQuery(this) );
    });

    jQuery('#clientclientlistingimagediv').on( 'click', '#remove_client_logo_button', function( event ) {
        event.preventDefault();
        jQuery( '#upload_listing_image' ).val( '' );
        jQuery( '#clientclientlistingimagediv img' ).attr( 'src', '' );
        jQuery( '#clientclientlistingimagediv img' ).hide();
        jQuery( this ).attr( 'id', 'upload_client_logo_button' );
        jQuery( '#upload_client_logo_button' ).text( 'Set listing image' );
    });

    });</script><?php 
}

/*
 *
 * Make featured images lazy load
 * 
 */ 
add_filter( 'post_thumbnail_html', 'add_image_placeholders', 11 );
add_filter( 'the_content', 'add_image_placeholders', 99 );
function add_image_placeholders( $content ) {
	// Don't lazyload for feeds, previews, mobile
	if( is_feed() || is_preview())
		return $content;
    if(function_exists('is_shop')) {
        if(is_shop()) {
            return $content;
        }
    }

	// Don't lazy-load if the content has already been run through previously
	if ( false !== strpos( $content, 'data-src' ) )
		return $content;

	// In case you want to change the placeholder image
	$placeholder_image = apply_filters( 'lazyload_images_placeholder_image', get_template_directory_uri() .'/assets/img/pixel.gif' );

	// This is a pretty simple regex, but it works
	$content = preg_replace( '#<img([^>]+?)src=[\'"]?([^\'"\s>]+)[\'"]?([^>]*)>#', sprintf( '<img${1}src="%s" data-src="${2}"${3}><noscript><img${1}src="${2}"${3}></noscript>', $placeholder_image ), $content );

	return $content;
}

/*
 * 
 * Add lozad class to all images in the content filter
 * 
 */
function add_responsive_class($content){
    if(!empty($content)) {
        $content = mb_convert_encoding($content, 'HTML-ENTITIES', "UTF-8");
        $document = new DOMDocument();
        libxml_use_internal_errors(true);
        $document->loadHTML(utf8_decode($content));

        $imgs = $document->getElementsByTagName('img');
        foreach ($imgs as $img) {
            $existing_class = $img->getAttribute('class');
            $img->setAttribute('class', "lozad $existing_class");
        }

        $html = $document->saveHTML();
        return $html;
    }
}
add_filter('the_content', 'add_responsive_class');
/* 
 *
 * remvoe src sets
 * 
 */ 
add_filter( 'wp_calculate_image_srcset', 'meks_disable_srcset' );
function meks_disable_srcset( $sources ) {
    return false;
}
remove_filter( 'the_content', 'wp_make_content_images_responsive' );

/*
 * Remove default wp lazy loading since we already sue lozad
 */
add_filter( 'wp_lazy_loading_enabled', '__return_false' ); 