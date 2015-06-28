<?php
get_header(); ?>
<section class="main-container clearfix">
    <section class="main wrapper clearfix">
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <div class="page-inner-content-area clearfix">
                <?php
                    while ( have_posts() ) : the_post();
                        get_template_part( 'content', 'page' );
                    endwhile;
                ?>
            </div>
        </article>
    </section>
</section>
<?php get_footer(); ?>
