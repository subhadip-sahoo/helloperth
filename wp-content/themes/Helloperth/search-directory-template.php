<?php
/* Template Name: Search Directory */
global $wp_query;
$query = array(
    'post_type' => 'directories',
    'post_status' => 'publish'
);
if(isset($_REQUEST['location']) && !empty($_REQUEST['location'])){
    $query['meta_query'] = array(
        array(
            'key' => 'geo_location',
            'value' => $_REQUEST['location'],
            'compare' => '='
        )
    );
}
if(isset($_REQUEST['postcode']) && !empty($_REQUEST['postcode'])){
    $query['meta_query'] = array(
        array(
            'key' => 'geo_zip_code',
            'value' => $_REQUEST['postcode'],
            'compare' => '='
        )
    );
}

if(isset($_REQUEST['location']) && !empty($_REQUEST['location']) && isset($_REQUEST['postcode']) && !empty($_REQUEST['postcode'])){
    $query['meta_query'] = array(
        'relation' => 'AND',
        array(
            'key' => 'geo_location',
            'value' => $_REQUEST['location'],
            'compare' => '='
        ),
        array(
            'key' => 'geo_zip_code',
            'value' => $_REQUEST['postcode'],
            'compare' => '='
        )
    );
}
if(isset($_REQUEST['category_name']) && !empty($_REQUEST['category_name']) && $_REQUEST['category_name'] <> 'all'){
    $query['tax_query'] = array(
        array(
            'taxonomy' => 'directories-cat',
            'field' => 'slug',
            'terms'    => array( $_REQUEST['category_name'] )
        )
    );
}
if(isset($_REQUEST['keywords']) && !empty($_REQUEST['keywords'])){
    $keywords = trim($_REQUEST['keywords']);
    $keywords = rtrim($keywords,',');
    $tags = explode(',', $keywords);
    $query['tax_query'] = array(
        array(
            'taxonomy' => 'directories-tag',
            'field' => 'slug',
            'terms'    => $tags
        )
    );
}
if(isset($_REQUEST['category_name']) && !empty($_REQUEST['category_name']) && $_REQUEST['category_name'] <> 'all' && isset($_REQUEST['keywords']) && !empty($_REQUEST['keywords'])){
    $keywords = trim($_REQUEST['keywords']);
    $keywords = rtrim($keywords,',');
    $tags = explode(',', $keywords);
    $query['tax_query'] = array(
        'relation' => 'AND',
        array(
            'taxonomy' => 'directories-cat',
            'field' => 'slug',
            'terms'    => array( $_REQUEST['category_name'] )
        ),
        array(
            'taxonomy' => 'directories-tag',
            'field' => 'slug',
            'terms'    => $tags
        )
    );
}
get_header();
?>
<script type="text/javascript">
    (function($){
        $.widget( "custom.combobox", {
            _create: function() {
                this.wrapper = $( "<span>" )
                .addClass( "custom-combobox" )
                .insertAfter( this.element );
                this.element.hide();
                this._createAutocomplete();
                this._createShowAllButton();
            },
            _createAutocomplete: function() {
                var selected = this.element.children( ":selected" ),
                value = selected.val() ? selected.text() : "";
                this.input = $( "<input>" )
                .appendTo( this.wrapper )
                .val( value )
                .attr( "title", "" )
                .addClass( "custom-combobox-input ui-widget ui-widget-content ui-state-default ui-corner-left" )
                .autocomplete({
                    delay: 0,
                    minLength: 0,
                    source: $.proxy( this, "_source" )
                })
                .tooltip({
                    tooltipClass: "ui-state-highlight"
                });
                this._on( this.input, {
                    autocompleteselect: function( event, ui ) {
                        ui.item.option.selected = true;
                        this._trigger( "select", event, {
                            item: ui.item.option
                        });
                    },
                    autocompletechange: "_removeIfInvalid"
                });
            },
            _createShowAllButton: function() {
                var input = this.input,
                wasOpen = false;
                $( "<a>" )
                .attr( "tabIndex", -1 )
                .attr( "title", "" )
                .tooltip()
                .appendTo( this.wrapper )
                .button({
                    icons: {
                        primary: "ui-icon-triangle-1-s"
                    },
                    text: false
                })
                .removeClass( "ui-corner-all" )
                .addClass( "custom-combobox-toggle ui-corner-right" )
                .mousedown(function() {
                    wasOpen = input.autocomplete( "widget" ).is( ":visible" );
                })
                .click(function() {
                    input.focus();
                    if ( wasOpen ) {
                        return;
                    }
                    input.autocomplete( "search", "" );
                });
            },
            _source: function( request, response ) {
                var matcher = new RegExp( $.ui.autocomplete.escapeRegex(request.term), "i" );
                response( this.element.children( "option" ).map(function() {
                    var text = $( this ).text();
                    if ( this.value && ( !request.term || matcher.test(text) ) )
                    return {
                        label: text,
                        value: text,
                        option: this
                    };
                }) );
            },
            _removeIfInvalid: function( event, ui ) {
                if ( ui.item ) {
                    return;
                }
                var value = this.input.val(),
                valueLowerCase = value.toLowerCase(),
                valid = false;
                this.element.children( "option" ).each(function() {
                    if ( $( this ).text().toLowerCase() === valueLowerCase ) {
                        this.selected = valid = true;
                        return false;
                    }
                });
                if ( valid ) {
                    return;
                }
                this.input
                .val( "" )
                .attr( "title", "" ) // value + " didn't match any item"
                .tooltip( "open" );
                this.element.val( "" );
                this._delay(function() {
                    this.input.tooltip( "close" ).attr( "title", "" );
                }, 2500 );
                this.input.autocomplete( "instance" ).term = "";
            },
            _destroy: function() {
                this.wrapper.remove();
                this.element.show();
            }
        });
        function split( val ) {
            return val.split( /,\s*/ );
        }
        function extractLast( term ) {
            return split( term ).pop();
        }
        $(function(){
            var locations = <?php echo json_encode(get_meta_values('geo_location'));?>;
            var postcodes = <?php echo json_encode(get_meta_values('geo_zip_code'));?>;
            var keywords = <?php echo json_encode(get_directory_tags());?>;
            $( "#location" ).autocomplete({
                source: locations
            });
            $( "#postcode" ).autocomplete({
                source: postcodes
            });
            $( "#keywords" ).bind( "keydown", function( event ) {
                if ( event.keyCode === $.ui.keyCode.TAB &&
                $( this ).autocomplete( "instance" ).menu.active ) {
                event.preventDefault();
                }
            })
            .autocomplete({
                minLength: 0,
                source: function( request, response ) {
                    response( $.ui.autocomplete.filter(
                    keywords, extractLast( request.term ) ) );
                },
                focus: function() {
                    return false;
                },
                select: function( event, ui ) {
                    var terms = split( this.value );
                    terms.pop();
                    terms.push( ui.item.value );
                    terms.push( "" );
                    this.value = terms.join( ", " );
                    return false;
                }
            });
            
            $( "#category_name" ).combobox();
            
            $('#directorirs' ).load('<?php echo admin_url('admin-ajax.php'); ?>', {action: 'directories', args:<?php echo json_encode($query); ?>}, function(res){
                if(res == '' || typeof res === 'undefined'){
                    $(this).html('<p>No directories found!</p>');
                }
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
<section class="banner-container clearfix">
    <div class="inner-banner-container clearfix">
        <div class="owl-carousel inner-banner-carousel">
            <div class="item">
                <div class="perth-slider-box">
                    <figure class="parth-slider-image">
                        <img src="<?php echo get_template_directory_uri();?>/images/inner-banner-slider.jpg" alt="Images" width="332" height="222">
                    </figure>
                    <h3 class="parth-slider-title">
                        <a href="">Crown Perth <i class="icon icon-arrow"></i></a>
                    </h3>
                </div>
            </div>
            <div class="item">
                <div class="perth-slider-box">
                    <figure class="parth-slider-image">
                        <img src="<?php echo get_template_directory_uri();?>/images/inner-banner-slider1.jpg" alt="Images" width="332" height="222">
                    </figure>
                    <h3 class="parth-slider-title">
                        <a href="">St George’s Cathedral <i class="icon icon-arrow"></i></a>
                    </h3>
                </div>
            </div>
            <div class="item">
                <div class="perth-slider-box">
                    <figure class="parth-slider-image">
                        <img src="<?php echo get_template_directory_uri();?>/images/inner-banner-slider2.jpg" alt="Images" width="332" height="222">
                    </figure>
                    <h3 class="parth-slider-title">
                        <a href="">Captain Cook Cruises <i class="icon icon-arrow"></i></a>
                    </h3>
                </div>
            </div>
            <div class="item">
                <div class="perth-slider-box">
                    <figure class="parth-slider-image">
                        <img src="<?php echo get_template_directory_uri();?>/images/inner-banner-slider3.jpg" alt="Images" width="332" height="222">
                    </figure>
                    <h3 class="parth-slider-title">
                        <a href="">City Sightseeing <i class="icon icon-arrow"></i></a>
                    </h3>
                </div>
            </div>
            <div class="item">
                <div class="perth-slider-box">
                    <figure class="parth-slider-image">
                        <img src="<?php echo get_template_directory_uri();?>/images/inner-banner-slider4.jpg" alt="Images" width="332" height="222">
                    </figure>
                    <h3 class="parth-slider-title">
                        <a href="">Perth Concert Hall <i class="icon icon-arrow"></i></a>
                    </h3>
                </div>
            </div>
            <div class="item">
                <div class="perth-slider-box">
                    <figure class="parth-slider-image">
                        <img src="<?php echo get_template_directory_uri();?>/images/inner-banner-slider5.jpg" alt="Images" width="332" height="222">
                    </figure>
                    <h3 class="parth-slider-title">
                        <a href="">the Bell Tower <i class="icon icon-arrow"></i></a>
                    </h3>
                </div>
            </div>
            <div class="item">
                <div class="perth-slider-box">
                    <figure class="parth-slider-image">
                        <img src="<?php echo get_template_directory_uri();?>/images/inner-banner-slider5.jpg" alt="Images" width="332" height="222">
                    </figure>
                    <h3 class="parth-slider-title">
                        <a href="">Captain Cook Cruises <i class="icon icon-arrow"></i></a>
                    </h3>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="main-container clearfix">
    <section class="main wrapper clearfix">
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <div class="page-inner-content-area clearfix">
                <?php
                    while ( have_posts() ) : the_post();
                        get_template_part( 'content', 'page' );
                    endwhile; wp_reset_query();
                ?>
            </div>

            <div class="advanced-search-container clearfix">
                <form name="search_directory" id="search_directory" action="" method="GET">
                    <div class="advance-search-field-group">
                        <div class="input-field-group">
                            <div class="input-field input-field1">
                                <input type="text" name="location" id="location" value="<?php echo (isset($_REQUEST['location'])) ? $_REQUEST['location'] : '';?>" placeholder="Location" class="form-control" />
                            </div>
                            <div class="input-field input-field2">
                                <select name="category_name" id="category_name" class="form-control">
                                    <option value="all">All Business Categories</option>
                                    <?php $directory_cat = get_categories(array('post_type' => 'directories', 'taxonomy' => 'directories-cat', 'hide_empty' => 0)); ?>
                                    <?php if(is_array($directory_cat) && !empty($directory_cat)): ?>
                                    <?php foreach ($directory_cat as $dir):?>
                                    <option value="<?php echo $dir->slug; ?>" <?php echo (isset($_REQUEST['category_name']) && $_REQUEST['category_name'] == $dir->slug) ? 'selected="selected"' : ''; ?>><?php echo $dir->name; ?></option>
                                    <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="input-field input-field3">
                                <input type="text" name="postcode" id="postcode" value="<?php echo (isset($_REQUEST['postcode'])) ? $_REQUEST['postcode'] : '';?>" placeholder="Post Code" class="form-control" />
                            </div>
                            <div class="input-field input-field4">
                                <input type="text" name="keywords" id="keywords" placeholder="keywords" value="<?php echo (isset($keywords)) ? $keywords : '';?>" class="form-control"/>
                            </div>
                        </div>
                        <div class="submit-field">
                            <!-- <input type="submit" name="search" value="Search" class="btn btn-ad-search" /> -->
                            <button type="submit" class="btn btn-ad-search"><i class="fa fa-search"></i> Search</button>
                        </div>
                    </div>
                </form>
            </div>
        </article>
        <!--<div class="loading-div"><img src="<?php //echo get_template_directory_uri(); ?>/images/ajax-loader.gif" ></div>-->
        <section class="inner-blocks-cts-area clearfix advance-search-lists" id="directorirs"></section>
    </section>
</section>
<?php get_footer(); ?>