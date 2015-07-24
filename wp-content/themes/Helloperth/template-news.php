<?php
    /* Template Name: News*/
    get_header();
?>
<!-- HTML CODE START -->
<?php echo mini_banner_listing(); ?>
<section class="main-container clearfix">
    <section class="main wrapper clearfix">
        <div class="news-event-container clearfix">
            <header class="site-heading"><h2><?php the_title(); ?></h2></header>
            <?php 
                query_posts(array('post_type' => 'news', 'posts_per_page' => -1, 'post_status' => 'publish'));
                if(have_posts()) :
                    while(have_posts()) : the_post();
                        $event_image = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'list-events');
            ?>
            <article id="post-<?php echo get_the_ID(); ?>" <?php post_class(); ?>>
                <?php if(has_post_thumbnail()) : ?>
                <figure class="news-event-figure">
                    <a href="javascript:void(0);"><img src="<?php echo $event_image[0]; ?>" alt="" width="227" height="154"></a>
                </figure>
                <?php endif; ?>
                <div class="news-event-content">
                    <h4 class="news-event-title"><a href="javascript:void(0);"><span class="news-event-date"></span> <span class="news-event-title-t"><?php the_title(); ?></span></a></h4>
                    <div class="news-event-text">
                        <p><?php the_content(); ?></p>
                    </div>
                </div>
            </article>
<?php endwhile; ?>
<?php wp_reset_query(); ?>
<?php else: ?>            
            <article id="post-<?php echo the_ID(); ?>" <?php post_class(); ?>>
                <div class="news-event-content">
                    <h4 class="news-event-title"><a href="javascript:void(0);"><span class="news-event-date"></span> <span class="news-event-title-t">Currently no news to display!</span></a></h4>
                </div>
            </article>
<?php endif; ?>
        </div>
    </section>
</section>
<!-- END -->
<?php get_footer(); ?>