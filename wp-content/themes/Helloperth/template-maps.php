

<?php
    /* Template Name: Maps */
    get_header();
?>
<?php echo banner_directory_listing(); ?>
<section class="main-container main-map-download-container clearfix">
    <section class="main wrapper clearfix">
        <div class="news-event-container clearfix">
            <header class="site-heading"><h2><?php the_title(); ?></h2></header>
            <article class="map-download-container clearfix">
                <?php query_posts(array('post_type' => 'maps', 'posts_per_page' => -1, 'order' => 'ASC'));  ?>
               <?php if(have_posts()) : ?>

                <ul class="grid-row download-list">
                    <?php while(have_posts()) : the_post(); ?>
                    <?php $pdf_id = get_field('map_pdf'); ?>
                    <?php $thumbnail_id = get_post_meta( $pdf_id, '_thumbnail_id', true ); ?>
                    <?php $thumb_src = wp_get_attachment_image_src ( $thumbnail_id, 'medium' ); ?>
                    <li class="grid-row-4 download-list-pdf">
                        <div class="map-download-list-inner">
                            <h2 class="map-download-title"><?php the_title(); ?></h2>
                            <p><a href="<?php echo wp_get_attachment_url( $pdf_id ); ?>" target="_blank" title="<?php echo esc_attr( get_the_title( $pdf_id ) ); ?>">Click to download</a> (<?php echo round(((filesize( get_attached_file( $pdf_id ) ) / 1024)), 2); ?> KB PDF file)</p>
                            <a href="<?php echo wp_get_attachment_url( $pdf_id ); ?>" target="_blank" title="<?php echo esc_attr( get_the_title( $pdf_id ) ); ?>">          <div class="product_img_part">
                                <?php if(!empty($thumb_src[0])): ?>
                                <img src="<?php echo $thumb_src[0]; ?>" width="<?php echo $thumb_src[1]; ?>" height="<?php echo $thumb_src[2]; ?>" alt="" />                        </div>
                                <?php endif; ?>
                                
                        </a>
                        </div> 
                    </li>
                    <?php endwhile; ?>
                    <?php wp_reset_query(); ?>
                    <?php endif; ?>
                    
                </ul>
            </article>
        </div>
    </section>
</section>
<!-- END -->
<?php get_footer(); ?>

