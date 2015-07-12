<?php 
    /* Template Name: Home */
    get_header();
?>
<section class="banner-container clearfix">
    <div class="banner-slider-container clearfix">
        <?php query_posts(array('post_type' => 'sliders', 'posts_per_page' => -1, 'order' => 'ASC')); ?>
        <?php if(have_posts()) : ?>
        <div class="flexslider banner-slider">
            <ul class="slides">
                <?php while(have_posts()) : the_post(); ?>
                <?php $slider = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'full') ?>
                <?php if(has_post_thumbnail()) : ?>
                <li style="background-image:url(<?php echo $slider[0]; ?>)">
                    <img src="<?php echo $slider[0]; ?>" width="2000" height="638" alt="<?php echo get_the_title(get_the_ID()); ?>" />
                </li>
                <?php endif; ?>
                <?php endwhile; ?>
                <?php wp_reset_query(); ?>
            </ul>
        </div>
        <?php endif; ?>
    </div>
    <div class="banner-search-area">
        <div class="wrapper">
            <div class="inner-banner-search-area">
                <h2>explore <br>indulge unwind</h2>
                <p>Hello Perth is the premier free visitor guide to our <br>beautiful capital city, Perth. </p>
                <form action="<?php echo home_url(); ?>" name="search" method="GET">
                    <div class="banner-search-form-area">
                        <input type="text" class="form-control" name="s">
                        <input type="hidden" class="form-control" name="post_type" value="directories">   
                        <input type="submit" class="btn btn-banner-search" value="explore perth">
                    </div>
                    <p class="advance-search-text"><a href="<?php echo href(ADVANCED_SEARCH_PAGE); ?>">Advanced Search</a></p>
                </form>
            </div>
        </div>
    </div>
</section>


<section class="main-container clearfix">
    <section class="main wrapper clearfix">
        <div class="homenews-event-container clearfix">
            <div id="tabs">
                <ul>
                    <li><a href="#tabs-events">Events</a></li>
                    <li><a href="#tabs-news">News</a></li>
                </ul>
                <div id="tabs-events">
                    <div class="news-events-container clearfix">
                        <!--<h3 class="news-event-heading">EVENTS</h3>-->
                        <?php 
                            $events = event_sorting_by_date(); 
                            if($events <> 0) :
                        ?>
                        <a href="<?php echo href(NEWS_N_EVENTS_PAGE); ?>" class="link-view-full hide-tablet">View full list <i class="fa fa-arrow-circle-o-right"></i></a>
                        <div class="news-events-slider clearfix">
                            <div class="flexslider news-event-slider">
                                <ul class="slides">
                                <?php 
                                foreach($events as $evt) : 
                                    if(strtotime(date('Y-m-d')) > strtotime($evt['end_date'])) : continue; endif; 
                                    $event = get_post($evt['post_id']);
                                    $event_image = wp_get_attachment_image_src(get_post_thumbnail_id($evt['post_id']), 'list-events');

                                ?>
                                    <li><a href="<?php echo href(NEWS_N_EVENTS_PAGE) ;?>">
                                        <figure class="news-event-image">
                                            <?php if(!empty($event_image[0])) : ?>
                                            <img src="<?php echo $event_image[0]; ?>" alt="Image" width="177" height="137">
                                            <?php else: ?>
                                            <img src="<?php echo get_template_directory_uri(); ?>/images/no-image-events.png" alt="Image" width="177" height="137">
                                            <?php endif; ?>
                                        </figure>
                                       
                                        <div class="news-events-content">
                                            <h2><?php echo date('d/m/Y', strtotime($evt['start_date'])); ?>  -  <?php echo date('d/m/Y', strtotime($evt['end_date'])); ?>&nbsp;<?php echo $event->post_title; ?></h2>
                                            <p><?php echo mb_strimwidth($event->post_content, 0, 200, '...'); ?></p>
                                            <p class="news-event-add"><?php echo get_field('location', $evt['post_id'], true); ?> <?php echo get_field('website', $evt['post_id'], true); ?></p>
                                        </div></a>   
                                    </li>
                                <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                        <a href="<?php echo href(NEWS_N_EVENTS_PAGE); ?>" class="link-view-full hide-desktop">View full list <i class="fa fa-arrow-circle-o-right"></i></a>
                        <?php else: ?>
                        <p>Currently there are no events to display!</p>
                        <?php endif; ?>
                    </div>
                </div>
                <div id="tabs-news">
                    <div class="news-events-container clearfix">
                        <!--<h3 class="news-event-heading">EVENTS</h3>-->
                        <?php 
                            query_posts(array('post_type' => 'news', 'posts_per_page' => -1, 'post_status' => 'publish'));
                            if(have_posts()) :
                        ?>
                        <a href="<?php echo href(NEWS_PAGE); ?>" class="link-view-full hide-tablet">View full list <i class="fa fa-arrow-circle-o-right"></i></a>
                        <div class="news-events-slider clearfix">
                            <div class="flexslider news-event-slider">
                                <ul class="slides">
                                    <?php 
                                        while(have_posts()) : the_post();
                                            $event_image = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'list-events');
                                    ?>
                                    <li><a href="<?php echo href(NEWS_PAGE) ;?>">
                                        <?php if(has_post_thumbnail()) : ?>
                                        <figure class="news-event-image">
                                            <img src="<?php echo $event_image[0]; ?>" alt="Image" width="177" height="137">
                                        </figure>
                                        <?php endif; ?>
                                        <div class="news-events-content">
                                            <h2><?php the_title(); ?></h2>
                                            <p><?php echo mb_strimwidth(get_the_content(get_the_ID()), 0, 200, '...'); ?></p>
                                            <p class="news-event-add"></p>
                                        </div></a>   
                                    </li>
                                    <?php endwhile; ?>
                                    <?php wp_reset_query(); ?>
                                </ul>
                            </div>
                        </div>
                        <a href="<?php echo href(NEWS_PAGE); ?>" class="link-view-full hide-desktop">View full list <i class="fa fa-arrow-circle-o-right"></i></a>
                        <?php else: ?>
                        <p>Currently there are no news to display!</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <?php echo best_of_perth(); ?>

        <section class="left-right-blocks-container clearfix">
            <div class="left-blocks-container">
                <section class="section-blocks perth-tourist-map-section clearfix">
                    <header class="section-blocks-header">
                        <h2><i class="icon icon-tourist-map"></i>Perth Tourist Maps</h2>
                        <a href="" class="btn btn-view-download hide-tablet">View Download PDF maps <i class="fa fa-arrow-circle-o-right"></i></a>
                        <p>We have the best tourist maps to help you make the most os your visit in Perth. <a href="">Click here</a> for Transperth <a href="">train</a> and bas maps, <a href="">Perth shopping guides,</a> <a href="">maps of Fremantle</a> and more</p>
                    </header>
                    <section class="section-blocks-contentarea">
                        <div class="perth-tourist-map clearfix">
                            <div id="map-home" style="width: 890px; height: 224px;"></div>
                        </div>
                        <a href="" class="btn btn-view-download hide-desktop">View Download PDF maps <i class="fa fa-arrow-circle-o-right"></i></a>
                        <?php $static_page_ids = array(EXCLUSIVE_DISCOUNT_PAGE, NEWS_N_EVENTS_PAGE, TOURIST_INFO_PAGE); ?>
                        <?php $button_names = array('Discounts', 'Events', 'Tourist Info'); ?>
                        <div class="perth-tourist-map-lists clearfix">
                            <ul class="grid-row">
                                <?php $i = 0; ?>
                                <?php foreach($static_page_ids as $id) : ?>
                                <?php $page = get_post($id); ?>
                                <li class="grid-row-3">
                                    <div class="perth-slider-box">
                                        <div class="perth-tourist-lists-image">
                                            <?php $img_desktop = wp_get_attachment_image_src(get_post_thumbnail_id($id), 'home_block_image_desktop'); ?>
                                            <?php $img_tablet = wp_get_attachment_image_src(get_post_thumbnail_id($id), 'home_block_image_tablet'); ?>
                                            <?php $img_mobile = wp_get_attachment_image_src(get_post_thumbnail_id($id), 'home_block_image_mobile'); ?>
                                            <?php if(!empty($img_desktop[0])) :?>
                                            <figure class="parth-slider-image">
                                                <img src="<?php echo $img_desktop[0];?>" width="274" height="230" alt="Image" class="hide-tablet">
                                                <img src="<?php echo $img_tablet[0];?>" alt="Images" class="show-tablet" width="1024" height="289">
                                                <img src="<?php echo $img_mobile[0];?>" alt="Images" class="hide-desktop-tablet" width="767" height="336">
                                            </figure>
                                            <?php endif; ?>
                                            <h3 class="parth-slider-title">
                                                <a href="<?php echo href($id); ?>"><?php echo ($id == NEWS_N_EVENTS_PAGE) ? 'Events in Perth' : $page->post_title; ?> <i class="icon icon-arrow"></i></a>
                                            </h3>
                                        </div>
                                        <div class="perth-tourist-lists-con">
                                            <div class="perth-tourist-lists-text">
                                                <p><?php echo mb_strimwidth($page->post_content, 0, 100, '...'); ?></p>
                                            </div>
                                            <p class="perth-tourist-lists-btn"><a href="<?php echo href($id); ?>" class="btn btn-perth-tourist"><?php echo $button_names[$i]; ?></a></p>
                                        </div>
                                    </div>
                                </li>
                                <?php $i++; endforeach; ?>
                            </ul>
                        </div>
                    </section>
                </section>
            </div>
            <div class="right-blocks-container">
                <?php if(is_numeric(get_field('advertise_image_1', 'option'))): ?>
                <?php $home_advertise_image_desktop = wp_get_attachment_image_src(get_field('advertise_image_1', 'option'), 'home_advertise_image_desktop') ?>
                <?php $home_advertise_image_tablet = wp_get_attachment_image_src(get_field('advertise_image_1', 'option'), 'home_advertise_image_tablet') ?>
                <?php $home_advertise_image_mobile = wp_get_attachment_image_src(get_field('advertise_image_1', 'option'), 'home_advertise_image_mobile') ?>
                <div class="click-extra-blocks clearfix">
                    <div class="inner-click-extra-blocks clearfix">
                        <a href="<?php echo(get_field('advertise_link_1','option')) ? get_field('advertise_link_1','option') : '#'; ?>">
                            <figure class="click-extra-blocks-image">
                                <img src="<?php echo $home_advertise_image_desktop[0]; ?>" alt="Image" width="426" height="331" class="hide-tablet">
                                <img src="<?php echo $home_advertise_image_tablet[0]; ?>" alt="Images" class="show-tablet" width="1024" height="312">
                                <img src="<?php echo $home_advertise_image_mobile[0]; ?>" alt="Images" class="hide-desktop-tablet" width="767" height="441">
                            </figure>
                            <h3 class="click-extra-blocks-title">
                                <span><?php echo get_field('advertise_title_1', 'option'); ?></span>
                                <i class="icon icon-arrow2"></i>
                            </h3>
                        </a>
                    </div>
                </div>
                <?php endif; ?>
                <?php if(is_numeric(get_field('advertise_image_2', 'option'))): ?>
                <?php $home_advertise_image_desktop = wp_get_attachment_image_src(get_field('advertise_image_2', 'option'), 'home_advertise_image_desktop') ?>
                <?php $home_advertise_image_tablet = wp_get_attachment_image_src(get_field('advertise_image_2', 'option'), 'home_advertise_image_tablet') ?>
                <?php $home_advertise_image_mobile = wp_get_attachment_image_src(get_field('advertise_image_2', 'option'), 'home_advertise_image_mobile') ?>
                <div class="click-extra-blocks clearfix">
                    <div class="inner-click-extra-blocks clearfix">
                        <a href="<?php echo(get_field('advertise_link_2','option')) ? get_field('advertise_link_2','option') : '#'; ?>">
                            <figure class="click-extra-blocks-image">
                                <img src="<?php echo $home_advertise_image_desktop[0]; ?>" alt="Image" width="426" height="331" class="hide-tablet">
                                <img src="<?php echo $home_advertise_image_tablet[0]; ?>" alt="Images" class="show-tablet" width="1024" height="312">
                                <img src="<?php echo $home_advertise_image_mobile[0]; ?>" alt="Images" class="hide-desktop-tablet" width="767" height="441">
                            </figure>
                            <h3 class="click-extra-blocks-title">
                                <span><?php echo get_field('advertise_title_2', 'option'); ?></span>
                                <i class="icon icon-arrow2"></i>
                            </h3>
                        </a>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </section>

    </section> <!-- #main -->
</section> <!-- #main-container -->

<section class="main-base-container clearfix">
    <div class="wrapper">
        <div class="inner-main-base-container">
            <div class="guides-available-image-area hide-tablet">
                <?php $home_guide_image = get_field('home_guide_image', 'option'); ?>
                <?php if(is_numeric($home_guide_image)) : ?>
                <?php $home_guide_image_src = wp_get_attachment_image_src($home_guide_image, 'home_guide_image'); ?>
                <figure class="guides-available-image">
                    <img src="<?php echo $home_guide_image_src[0]; ?>" alt="Image" width="270" height="381">
                </figure>
                <?php endif; ?>
            </div>
            <article class="guides-available-content">
                <h4 class="guides-available-title hide-tablet">Guides Available:</h4>
                <div class="guides-available-con hide-tablet">
                    <p>Hello Perth is the premier free visitor guide to our beautiful capital city, Perth. Let us guide you to discover and enjoy the very best Western Australia has to offer with the latest information, maps and exclusive offers at your fingertips, in print and online. Be fully informed with our free printed guide (available at over 250 locations throughout Perth), browse this website, or download our App or PDF guide.</p>
                </div>
                <div class="grid-row">
                    <div class="grid-row-3"><a href="<?php echo (get_field('app_store_link', 'option') <> '') ? get_field('app_store_link', 'option') : '#'; ?>" class="btn btn-guides-available" target="_blank"><i class="icon icon-download"></i><span>App Store Download for iOS</span></a></div>
                    <div class="grid-row-3"><a href="<?php echo get_field('pdf_guide_download', 'option'); ?>" class="btn btn-guides-available" target="_blank"><i class="icon icon-download1"></i><span>PDF Guide download (13mb)</span></a></div>
                    <div class="grid-row-3"><a href="<?php echo href(GUIDE_PICKUP_LOCATIONS_PAGE); ?>" class="btn btn-guides-available"><i class="icon icon-download2"></i><span>Printed guide pickup location map</span></a></div>
                </div>
            </article>
        </div>
    </div>
</section>
<?php get_footer(); ?>