<!doctype html>
<html lang="en">
<head>
    <?php $filtered_title = str_replace('<span>','',str_replace('</span>','',get_the_title())); ?>
<?php $title = get_bloginfo('name'); if(is_home() || is_front_page()) { $title .=  ': '.get_bloginfo('description'); } elseif(is_archive()) { $term = get_queried_object(); $title .= ': '.$term->name; }else { $title .=  ': '.$filtered_title; } ?>
<title><?php echo $title; ?></title>
<meta content="width=device-width, initial-scale=1, shrink-to-fit=no" name="viewport">
<meta property="og:title" content="<?php echo $title; ?>" />
<meta property="og:image" content="" />
<meta property="og:image:secure" content="" />
<link rel="shortcut icon" type="image/png" href="<?php echo get_bloginfo('template_url'); ?>/assets/img/gregg.png"/>
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<header id="navbar_sticky" class="navbar is-sticky primary-bg <?php if(!is_home() && !is_front_page()) { echo ' slide-in'; } ?>"> 
    <div class="container position-relative hero-container">
        <div class="me mr-2">
            <a href="<?php echo get_bloginfo('url'); ?>" title="Home">
                <img class="has-shake" src="<?php echo get_bloginfo('template_url'); ?>/assets/img/gregg.svg" alt="Gregg" />
            </a>
        </div>
        <nav>
            <?php wp_nav_menu( array( 
                'theme_location' => 'main', 
                'container_class' => 'header-menu' ) ); 
            ?>
        </nav>
    </div>
</header>
<?php if(is_home() || is_front_page()) { ?>
    <section id="greggintro" class="primary-bg vh-100 d-flex align-items-center">
        <div class="container position-relative hero-container d-block d-sm-flex text-center text-sm-left mt--100">
            <div class="me mr-2 d-flex justify-content-center">
                <img class="mr-0 mr-sm-4 has-shake lozad" data-src="<?php echo get_bloginfo('template_url'); ?>/assets/img/gregg.svg" alt="Gregg" />
            </div>
            <div class="intro-text">
                <!-- <p class="d-block h4 mb-3">Hello</p>-->
                <p class="d-block h1 mt-2">Hi, my name is Gregg</p>
                <div class="d-block h4 qualifications">I&rsquo;m a <div id="text" class="orange"></div><div id="cursor"></div></div>
                
                <button class="simple-button pl-0 pagejump orange-hover mr-0 mr-sm-5" data-scrollto="contact" aria-label="Contact Me">Contact <?php echo featherIcon('phone-call'); ?></button>

                <button class="simple-button pl-0 pagejump orange-hover" data-scrollto="learnmore" aria-label="Continue to Content">Learn More <?php echo featherIcon('chevron-down'); ?></button>
            </div>
        </div>
        <button class="simple-button pl-0 pagejump orange-hover scroll-to-position-bottom" data-scrollto="learnmore" aria-label="Continue to Content"><?php echo featherIcon('chevron-down','','30'); ?></button>
    </section>   
<?php } ?> 
<main>
<div id="learnmore" class="mb-4"></div>