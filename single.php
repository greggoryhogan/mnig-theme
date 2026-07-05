<?php get_header(); ?>
<div class="white-bg mt-5 pt-4">
    <div class="container mt-3 pt-3">
        <div class="row">
            <div class="col-12 mt-1 mb-0 pt-0 pb-0 pr-sm-5 pl-sm-5 text-center">
                <div class="clip-text mb-3"><?php the_title(); ?></div>
            </div>
            <div class="col-lg-8 offset-lg-2 pt-3 pb-0 pr-5 pl-5"> <!--data-aos="fade-up" data-aos-offset="10"-->
                <div class="mb-4 border-bottom"></div>
            </div>

            <?php if(function_exists('get_field')) {
                $before_content = get_field('before_content');
                if($before_content != '') { 
                    $col = 'col-lg-8 offset-lg-2';
                    if(get_post_type() == 'project') {
                        $col = 'col-lg-12';
                    }
                    echo '<div class="'.$col.' mb-0 pb-1 pr-5 pl-5 text-center">';
                        echo $before_content;
                    echo '</div>';
                }
            } ?>

            <div class="col-12 pr-sm-5 pl-sm-5 main-content text-center" data-aos="fade-up" data-aos-offset="10">
                <?php if ( have_posts() ) : 
                    while ( have_posts() ) : the_post();
                        the_content();
                    endwhile;
                else :
                    _e( 'Hmmm, nothing here.', 'textdomain' );
                endif; ?>
            </div>

            

            <?php if(function_exists('get_field')) {
                $after_content = get_field('after_content');
                if($after_content != '') { ?>
                    <div class="col-lg-8 offset-lg-2 mb-3 pb-3 pr-5 pl-5 text-center">
                        <div class="mt-4 border-top"></div>
                    </div>
                <?php 
                $col = 'col-lg-8 offset-lg-2 pr-5 pl-5';
                if(get_post_type() == 'project') {
                    $col = 'col-lg-10 offset-lg-1 ';
                }
                    echo '<div class="'.$col.' mb-5 pb-5  after-content">';
                        echo $after_content;
                    echo '</div>';
                }
            } else { ?>
                <div class="col-lg-8 offset-lg-2 mb-5 pb-5 pr-5 pl-5 text-center" data-aos="fade-up" data-aos-offset="10">
                    <div class="mt-4 border-top"></div>
                </div>
            <?php } ?>
        
            <?php 
            if(is_single() && get_post_type() == 'product') {
                ?>
                <div class="col-lg-8 offset-lg-2 mt-5 pt-5 mb-5 pb-5 pr-5 pl-5 text-center">
                   
                </div><?php 
            }

            if(is_single() && get_post_type() == 'project') {

                $categories = get_the_terms(get_the_ID(),'project-category');
                if($categories) {
                    foreach($categories as $category) {
                        $primary = $category->slug;
                        $name = $category->name;
                        break;
                    }
                }
                ?>
                <div class="col-12 mt-5 mb-0 pt-0 pb-0 pr-5 pl-5 text-center">
                    <div class="clip-text smaller mb-3">More <?php echo $name; ?></div>
                </div>
                <div class="col-lg-8 offset-lg-2 mb-1 pt-0 pr-5 pl-5 text-center">
                    <div class="mb-4 border-bottom"></div>
                </div>
                <div class="col-12 mt-0 mb-4 pt-0 pb-5 mb-5 text-center">
                    <?php gregg_portfolio($primary,3); ?>
                </div><?php 
            }?>
            
        </div>
    </div>
</div>
<?php get_footer(); ?>