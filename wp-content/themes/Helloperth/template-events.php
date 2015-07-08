<?php
    /* Template Name: Events */
    $events = event_sorting_by_date();
    get_header();
?>
<?php echo banner_directory_listing(); ?>
<section class="main-container clearfix">
    <section class="main wrapper clearfix">
        <div class="news-event-container clearfix">
            <header class="site-heading"><h2>Events</h2></header>
<?php if($events <> 0) :
        foreach($events as $evt) : 
            if(strtotime(date('Y-m-d')) > strtotime($evt['end_date'])) : continue; endif; 
            $event = get_post($evt['post_id']);
            $event_image = wp_get_attachment_image_src(get_post_thumbnail_id($evt['post_id']), 'list-events');
?>
            <article id="post-<?php echo $evt['post_id']; ?>" <?php post_class(); ?>>
                <figure class="news-event-figure">
                    <?php if(!empty($event_image[0])) : ?>
                    <a href="javascript:void(0);"><img src="<?php echo $event_image[0]; ?>" alt="" width="227" height="154"></a>
                    <?php else: ?>
                    <a href="javascript:void(0);"><img src="<?php echo get_template_directory_uri(); ?>/images/no-image-events-listing.png" alt="" width="227" height="154"></a>
                    <?php endif; ?>
                </figure>
               
                <div class="news-event-content">
                    <h4 class="news-event-title"><a href="javascript:void(0);"><span class="news-event-date"><?php echo date('d/m/Y', strtotime($evt['start_date'])); ?>  -  <?php echo date('d/m/Y', strtotime($evt['end_date'])); ?></span> <span class="news-event-title-t"><?php echo $event->post_title; ?></span></a></h4>
                    <div class="news-event-text">
                        <p><?php echo $event->post_content; ?></p>
                    </div>
                    <div class="news-event-footer">
                        <?php echo get_field('location', $evt['post_id'], true); ?> <a href="<?php echo get_field('website_url', $evt['post_id'], true); ?>" target="_blank"><?php echo get_field('website', $evt['post_id'], true); ?></a>
                    </div>
                </div>
            </article>
<?php endforeach; ?>
<?php else: ?>            
            <article id="post-<?php echo the_ID(); ?>" <?php post_class(); ?>>
                <div class="news-event-content">
                    <h4 class="news-event-title"><a href="javascript:void(0);"><span class="news-event-date"></span> <span class="news-event-title-t">Currently no events to display!</span></a></h4>
                </div>
            </article>
<?php endif; ?>
        </div>
    </section>
</section>
<!-- END -->
<?php get_footer(); ?>