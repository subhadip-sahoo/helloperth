<?php 
session_start();
if(isset($_SESSION['breadcrumb'])){unset($_SESSION['breadcrumb']);}
global $wp_query;
$directory_name = $wp_query->queried_object->name;
$directory_slug = $wp_query->queried_object->slug;
$directory_id = $wp_query->queried_object->term_id;
$directory_taxonomy = $wp_query->queried_object->taxonomy;
$term_link = get_term_link( $directory_slug, $directory_taxonomy );
$_SESSION['breadcrumb']['term'] = $directory_name;
$_SESSION['breadcrumb']['term_link'] = $term_link;
$query = array();
$query['post_type'] =  'directories';
$query['taxonomy'] =  'directories-cat';
$query['post_status'] =  'publish';
$query['term'] =  $directory_slug;
get_header();
?>
<script type="text/javascript">    
    (function($){
        $(function(){
            $('#directorirs' ).load('<?php echo admin_url('admin-ajax.php'); ?>', {action: 'directories', args:<?php echo json_encode($query); ?>}, function(res){
//                if(res == '' || typeof res === 'undefined'){
//                    $(this).html('<p>No directories found!</p>');
//                }
            });
	
            $('#directorirs').on( 'click', '.pagination a', function (e){
                e.preventDefault();
//                $('.loading-div').show();
                var page = $(this).attr('data-page');
                $('#directorirs').load('<?php echo admin_url('admin-ajax.php'); ?>',{action: 'directories', page: page, args:<?php echo json_encode($query); ?>}, function(res){
                    
//                    $('.loading-div').hide();
                });
            });
        });
    })(jQuery);
</script>
<?php echo mini_banner_listing(); ?>

<section class="main-container clearfix">
    <section class="main wrapper clearfix">

        <section class="inner-explore-search-area">
            <div class="grid-row">
                <div class="grid-row-2">
                    <div class="explore-search-blocks">
                        <h2 class="explore-search-title">explore indulge unwind</h2>
                        <p>Hello Perth is the premier free visitor guide to our beautiful capital city, Perth.</p>
                    </div>
                </div>
                <div class="grid-row-2">
                    <div class="explore-search-form">
                        <form action="<?php echo home_url(); ?>" name="search" method="GET">
                            <input type="text" class="form-control" name="s">
                            <input type="hidden" class="form-control" name="taxonomy" value="directories-cat">
                            <input type="hidden" class="form-control" name="term" value="<?php echo $directory_slug; ?>">
                            <input type="submit" class="btn btn-banner-search" value="explore <?php echo $directory_name; ?>">
                        </form>
                        <p><a href="<?php echo href(ADVANCED_SEARCH_PAGE);?>">Advanced Search</a></p>
                    </div>
                </div>
            </div>
        </section>

        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <div class="page-inner-content-area clearfix">
                <div class="inner-left-container">
                    <?php 
                        $directories_cat_page = get_field('related_page', $directory_taxonomy.'_'.$directory_id); 
                        if($directories_cat_page):
                            $postid = url_to_postid( $directories_cat_page );
                            $post = get_post($postid); 
                            $content = apply_filters('the_content', $post->post_content); 
                    ?>
                    <header class="site-heading">
                        <h2><?php echo $post->post_title; ?></h2>
                    </header>
                    <div class="page-content" id="accordian-content">
                        <?php echo mb_strimwidth($content, 0, 1335, '...'); ?>
                        <p><a href="<?php echo 'javascript:void(0);'; //$directories_cat_page ?>" class="btn btn-more btn-view-download accord-more" data-postid="<?php echo $postid; ?>">More <i class="fa fa-arrow-circle-o-right"></i></a></p>
                    </div>
                    <?php else: ?>
                    <header class="site-heading">
                        <h2><?php echo $directory_name; ?></h2>
                    </header>
                    <div class="page-content">
                        <p>Content will be updated very soon. Keep visiting us. Thanks you.</p>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="inner-right-container">
                    <div class="inner-map-container">
                        <div id="map" style="height: 278px; width: 318px;"></div>
                        <a href="#larger_map" class="click-large-view-map">Click to view larger map <i class="icon icon-search"></i></a>
                    </div>
                </div>
                <!--<div id="map-large" style="width: 800px; height: 400px;"></div>-->
                <div id="larger_map" class="zoom-anim-dialog mfp-hide perth-popup-container">
                    <div id="map-large" style="width: 800px; height: 400px;"></div>
                </div>
            </div>
        </article>
        
        <!--<div class="loading-div"><img src="<?php //echo get_template_directory_uri(); ?>/images/ajax-loader.gif" /></div>-->
        <section class="inner-blocks-cts-area clearfix advance-search-lists" id="directorirs"></section>
        
    </section> <!-- #main -->
</section> <!-- #main-container -->
<?php get_footer(); ?>