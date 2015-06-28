<?php
global $wp_query;
$query = $wp_query->query;
$query['post_type'] =  'directories';
$query['taxonomy'] =  'directories-cat';
$query['post_status'] =  'publish';
get_header();
?>
<script type="text/javascript">
    (function($){
        $(function(){
            $('#directorirs' ).load('<?php echo admin_url('admin-ajax.php'); ?>', {action: 'directories', args:<?php echo json_encode($query); ?>}, function(res){
                if(res == '' || typeof res === 'undefined'){
                    $(this).html('<p>No directories found!</p>');
                }
            });
	
            $('#directorirs').on( 'click', '.pagination a', function (e){
                e.preventDefault();
                $('.loading-div').show();
                var page = $(this).attr('data-page');
                $('#directorirs').load('<?php echo admin_url('admin-ajax.php'); ?>',{action: 'directories', page: page, args:<?php echo json_encode($query); ?>}, function(res){
                    
                    $('.loading-div').hide();
                });
            });
        });
    })(jQuery);
</script>
<section class="main-container clearfix">
    <section class="main wrapper clearfix">
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <div class="page-inner-content-area clearfix">
                <div class="inner-full-container">
                    <header class="site-heading">
                        <h2><?php printf( __( 'Search Results for: %s', 'twentyfifteen' ), get_search_query() ); ?></h2>
                    </header>
                </div>
            </div>
        </article>
        <div class="loading-div"><img src="<?php echo get_template_directory_uri(); ?>/images/ajax-loader.gif" ></div>
        <section class="inner-blocks-cts-area clearfix advance-search-lists" id="directorirs"></section>
    </section>
</section>
<?php get_footer(); ?>
