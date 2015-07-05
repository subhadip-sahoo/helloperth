<?php
/* Template Name: Add Directory */
global $wp_query;
require_once( ABSPATH . '/wp-admin/includes/template.php' );
session_start();
if(isset($_SESSION['redirect_to'])){ unset($_SESSION['redirect_to']); }
global $user_ID;
if(!$user_ID){
    $_SESSION['redirect_to'] = href(ADD_DIRECTORY_PAGE);
    wp_safe_redirect(href(LOGIN_PAGE));
    exit();
}
$err_msg = '';
$war_msg = '';
$info_msg = '';
$suc_msg = '';
if(isset($_POST['submit_directory'])){
    if(empty($_POST['post_title'])){
        $err_msg = 'Title is required.';
    }else if(empty($_POST['tax_input']['directories-cat'])){
        $err_msg = 'Please select al least one business diretcory category.';
    }else if(empty($_POST['email_address'])){
        $err_msg = 'Email address is required.';
    }else if(filter_var($_POST['email_address'], FILTER_VALIDATE_EMAIL) == FALSE){
        $err_msg = 'Please enter a valid email address.';
    }else if(empty($_POST['phone'])){
        $err_msg = 'Phone is required.';
    }else if(empty($_POST['website'])){
        $err_msg = 'Website is required.';
    }else if(filter_var($_POST['website'], FILTER_VALIDATE_URL) == FALSE){
        $err_msg = 'Please enter a valid website.';
    }else if(empty($_POST['geo_location'])){
        $err_msg = 'Location is required.';
    }else if(empty($_POST['geo_route'])){
        $err_msg = 'Street route is required.';
    }else if(empty($_POST['geo_city'])){
        $err_msg = 'City is required.';
    }else if(empty($_POST['geo_state'])){
        $err_msg = 'State is required.';
    }else if(empty($_POST['geo_zip_code'])){
        $err_msg = 'Post code is required.';
    }else if(empty($_POST['geo_country'])){
        $err_msg = 'Country is required.';
    }
    
    if($err_msg == ''){
        $directory_info = array( 
            'post_title' => wp_strip_all_tags( $_POST['post_title'] ),
            'post_content' => $_POST['post_content'],
            'post_type' => 'directories',
            'post_status' => 'pending',
            'post_author' => $user_ID
        );

        if( $PID = wp_insert_post($directory_info)){
            set_post_thumbnail( $PID, $_POST['post_thumbnail'] );
            wp_set_post_terms($PID, $_POST['tax_input']['directories-cat'], 'directories-cat' );
            $keywords = trim($_POST['tags_input']);
            $keywords = rtrim($keywords,',');
            wp_set_post_terms($PID, $keywords, 'directories-tag', true );
            
            if(empty($_POST['geo_latlng'])){
                $results = parse_address_google(esc_sql($_POST['geo_location']), esc_sql($_POST['geo_zip_code']));

            }
            
            $directory_meta['company_name'] = esc_sql($_POST['company_name']);
            $directory_meta['contact_person'] = esc_sql($_POST['contact_person']);
            $directory_meta['email_address'] = esc_sql($_POST['email_address']);
            $directory_meta['phone'] = esc_sql($_POST['phone']);
            $directory_meta['website_title_dir'] = esc_sql($_POST['website_title_dir']);
            $directory_meta['website'] = esc_sql($_POST['website']);
            $directory_meta['geo_location'] = esc_sql($_POST['geo_location']);
            $directory_meta['geo_latlng'] = str_replace(array('(', ')'), array('',''), esc_sql($_POST['geo_latlng']));
            $directory_meta['geo_name'] = esc_sql($_POST['geo_name']);
            $directory_meta['geo_address'] = esc_sql($_POST['geo_address']);
            $directory_meta['geo_street_number'] = esc_sql($_POST['geo_street_number']);
            $directory_meta['geo_route'] = esc_sql($_POST['geo_route']);
            $directory_meta['geo_city'] = esc_sql($_POST['geo_city']);
            $directory_meta['geo_state'] = esc_sql($_POST['geo_state']);
            $directory_meta['geo_zip_code'] = esc_sql($_POST['geo_zip_code']);
            $directory_meta['geo_country'] = esc_sql($_POST['geo_country']);
            $directory_meta['_easy_image_gallery'] = esc_sql($_POST['img_gal']);

            foreach ($directory_meta as $key => $value) { 
                if( $post->post_type == 'revision' ) 
                    return; 
                update_post_meta($PID, $key, $value);
            }
            
            /* Email to administrator */
            
            $to = get_option('admin_email');
            $from = $user_email;
            $from_name = $display_name;
            $headers = "From: $from_name <$from>\r\n";
            $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
            $subject = "Please approve ".$_POST['post_title']." in ".get_option('blogname');
            $msg = "$display_name has added a business directory in ".  get_option('blogname')."<br/><br/>";
            $msg .= "To find the details and approve please click the link below. <br/>";
            $msg .= "<a href='".admin_url("post.php?post=$PID&action=edit")."'>Click here to approve this directory.</a><br/>";
            
            $suc_msg = 'Directory has been successfully updated.';
            
            unset($_POST);
        }
    }
}
get_header();
?>
<script type="text/javascript">
    (function($){
        function split( val ) {
            return val.split( /,\s*/ );
        }
        function extractLast( term ) {
            return split( term ).pop();
        }
        $(function(){
            var keywords = <?php echo json_encode(get_directory_tags());?>;
            $( "#tags_input" ).bind( "keydown", function( event ) {
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
        });
    })(jQuery);
</script>
<section class="main-container profile-container clearfix">
    <section class="main wrapper clearfix">
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <div class="page-inner-content-area clearfix">
                <?php
                while ( have_posts() ) : the_post();
                    get_template_part( 'content', 'page' );
                endwhile;
                ?>
                <?php if(!empty($err_msg)): echo '<p style="color: red;">'.$err_msg.'</p>'; endif;?>
                <?php if(!empty($war_msg)): echo '<p style="color: orange;">'.$war_msg.'</p>'; endif;?>
                <?php if(isset($_SESSION['session_msg']) && !empty($_SESSION['session_msg'])): echo '<p style="color: green;">'.$_SESSION['session_msg'].'</p>'; endif; unset($_SESSION['session_msg']);?>
                <form name="add_directory" id="add_directory" action="" method="POST" class="form_content" enctype="multipart/form-data">
                    <div class="form-group-lists">
                        <div class="form-group-lists-div">
                            <label>Title: </label>
                            <input type="text" name="post_title" id="post_title" value="<?php echo (isset($_POST['post_title'])) ? $_POST['post_title'] : ''; ?>" class="form-control validate[required]"/>
                        </div>
                        <div class="form-group-lists-div">
                            <label>Description: </label>
                            <?php $description = (isset($_POST['post_content'])) ? wp_richedit_pre($_POST['post_content']) : ''; ?>
                            <?php wp_editor($description, 'post_content', array('media_buttons' => false, 'wpautop' => false, 'quicktags' => false));?>
                        </div>
                        <div class="form-group-lists-div">
                            <label>Business Directory Categories: </label>
                            <ul class="directories-checkbox">
                                <?php 
                                    wp_terms_checklist('',
                                        array(
                                            'type' => 'directories', 
                                            'taxonomy' => 'directories-cat', 
                                            'checked_ontop' => false, 
                                            'descendants_and_self' => 0, 
                                            'selected_cats' => (isset($_POST['tax_input']) ? $_POST['tax_input']['directories-cat'] : false)
                                        )
                                    );  
                                ?>
                            </ul>
                        </div>
                        <div class="form-group-lists-div">
                            <label>Keywords: </label>
                            <input type="text" name="tags_input" id="tags_input" value="<?php echo (isset($_POST['tags_input'])) ? $_POST['tags_input'] : ''; ?>" class="form-control"/>
                        </div>
                        <div class="grid-row">
                            <div class="grid-row-2">
                                <div class="form-group-lists-div">
                                    <label>Company Name: </label>
                                    <input type="text" name="company_name" id="company_name" value="<?php echo (isset($_POST['company_name'])) ? $_POST['company_name'] : ''; ?>" class="form-control"/>
                                </div>
                            </div>
                            <div class="grid-row-2">
                                <div class="form-group-lists-div">
                                    <label>Contact Person: </label>
                                    <input type="text" name="contact_person" id="contact_person" value="<?php echo (isset($_POST['contact_person'])) ? $_POST['contact_person'] : ''; ?>" class="form-control"/>
                                </div>
                            </div>
                        </div>
                        <div class="grid-row">
                            <div class="grid-row-2">
                                <div class="form-group-lists-div">
                                    <label>Email Address: </label>
                                    <input type="email" name="email_address" id="email_address" value="<?php echo (isset($_POST['email_address'])) ? $_POST['email_address'] : ''; ?>" class="form-control validate[required, custom[email]]"/>
                                </div>
                            </div>
                            <div class="grid-row-2">
                                <div class="form-group-lists-div">
                                    <label>Phone: </label>
                                    <input type="text" name="phone" id="phone" value="<?php echo (isset($_POST['phone'])) ? $_POST['phone'] : ''; ?>" class="form-control validate[required, custom[number]]"/>
                                </div>
                            </div>
                        </div>
                        <div class="grid-row">
                            <div class="grid-row-2">
                                <div class="form-group-lists-div">
                                    <label>Website Title: </label>
                                    <input type="text" name="website_title_dir" id="website_title_dir" value="<?php echo (isset($_POST['website_title_dir'])) ? $_POST['website_title_dir'] : ''; ?>" class="form-control"/>
                                </div>
                            </div>
                            <div class="grid-row-2">
                                <div class="form-group-lists-div">
                                    <label>Web Site: </label>
                                    <input type="url" name="website" id="website" value="<?php echo (isset($_POST['website'])) ? $_POST['website'] : ''; ?>" class="form-control validate[required, custom[url]]"/>
                                </div>
                            </div>
                        </div>
                        <div class="form-group-lists-div">
                            <label for="upload_image">Directory Cover Image</label>
                            <div class="upload-image-block" id="figure_parent">
                                <figure class="upload-image-img" id="dir_feature_image">
                                    <?php if($isset_profile_pic == 1): ?>
                                    <img src="<?php echo $profile_pic[0]; ?>" width="200" height="150" alt="Profile picture"/>
                                    <?php endif; ?>
                                    <?php if($isset_profile_pic == 1) :?>
                                    <a href="javascript:void(0);" title="Remove image" id="remove_dir_feature_image" class="btn btn-remove-image"><i class="fa fa-times-circle"></i></a>
                                    <?php endif; ?>
                                </figure>
                                <div class="upload-btn-group" id="control-div">
                                    <?php if($isset_profile_pic == 0) :?>
                                    <button id="upload_image_button" class="button btn upload-btn" type="button" value="Upload Image" ><i class="fa fa-plus-circle"></i><span>Upload Image</span></button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group-lists-div">
                            <label for="Gallery">Image Gallery</label>
                            <div class="upload-image-block" id="bulk-image-box">
                                <button id="upload_gallery_image" class="button btn upload-btn" type="button" value="Upload Image" ><i class="fa fa-plus-circle"></i><span>Add Images</span></button>
                                <?php if(isset($_POST['img_gal']) && !empty($_POST['img_gal'])):?>
                                <?php $gImgArr = explode(',', $_POST['img_gal']);?>
                                <?php if(is_array($gImgArr) && !empty($gImgArr)) : ?>
                                <?php foreach($gImgArr as $img): ?>
                                <?php $gImg = wp_get_attachment_image_src($img, array(200, 150)); ?>
                                <figure class="upload-image-img">
                                    <img src="<?php echo $gImg[0]; ?>" width="200" height="150"/>
                                    <a href="javascript:void(0);" title="Remove image" class="remove-gal-image" data-attachment="<?php echo $img; ?>"><i class="fa fa-times-circle"></i></a>
                                </figure>
                                <?php endforeach; ?>
                                <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div id="locationField" class="form-group-lists-div">
                            <label>Location: </label>
                            <input id="autocomplete" autocomplete="off" name="geo_location" placeholder="Enter location" type="text" value="<?php echo (isset($_POST['geo_location'])) ? esc_attr($_POST['geo_location']) : ''; ?>" class="form-control validate[required]"/>
                            <input id="geo_latlng" name="geo_latlng" type="hidden" value="<?php echo (isset($_POST['geo_latlng'])) ? esc_attr($_POST['geo_latlng']) : ''; ?>">
                            <input id="geo_name" name="geo_name" type="hidden" value="<?php echo (isset($_POST['geo_name'])) ? esc_attr($_POST['geo_name']) : ''; ?>">
                            <input id="geo_address" name="geo_address" type="hidden" value="<?php echo (isset($_POST['geo_address'])) ? esc_attr($_POST['geo_address']) : ''; ?>">
                        </div>
                        <div class="form-group-lists-div">
                            <table id="address" class="table">
                              <tr>
                                <td class="label">Street address</td>
                                <td class="slimField"><input class="field form-control" id="street_number" placeholder="Street Number" name="geo_street_number" value="<?php echo (isset($_POST['geo_street_number'])) ? esc_attr($_POST['geo_street_number']) : ''; ?>"></td>
                                <td class="wideField" colspan="2"><input class="field form-control validate[required]" id="route" placeholder="Route" name="geo_route" value="<?php echo (isset($_POST['geo_route'])) ? esc_attr($_POST['geo_route']) : ''; ?>"></td>
                              </tr>
                              <tr>
                                <td class="label">City</td>
                                <td class="wideField" colspan="3"><input class="field form-control validate[required]" id="locality" name="geo_city" value="<?php echo (isset($_POST['geo_city'])) ? esc_attr($_POST['geo_city']) : ''; ?>"></td>
                              </tr>
                              <tr>
                                <td class="label">State</td>
                                <td class="slimField"><input class="field form-control validate[required]" id="administrative_area_level_1" name="geo_state" value="<?php echo (isset($_POST['geo_state'])) ? esc_attr($_POST['geo_state']) : ''; ?>"></td>
                                <td class="label">Zip code</td>
                                <td class="wideField"><input class="field form-control validate[required]" id="postal_code" name="geo_zip_code" value="<?php echo (isset($_POST['geo_zip_code'])) ? esc_attr($_POST['geo_zip_code']) : ''; ?>"></td>
                              </tr>
                              <tr>
                                <td class="label">Country</td>
                                <td class="wideField" colspan="3"><input class="field form-control validate[required]" name="geo_country" id="country" value="<?php echo (isset($_POST['geo_country'])) ? esc_attr($_POST['geo_country']) : ''; ?>"></td>
                              </tr>
                            </table>
                        </div>
                        <div class="form-group-lists-div">
                            <div id="map-canvas" style="height: 300px;"></div>
                        </div>
                        <div class="form-group-lists-div">
                            <input type="hidden" name="post_thumbnail" id="post_thumbnail" value="<?php echo(isset($_POST['post_thumbnail'])) ? $_POST['post_thumbnail'] : '' ?>" />
                            <input type="hidden" name="img_gal" id="img_gal" value="<?php echo(isset($_POST['img_gal'])) ? $_POST['img_gal'] : '' ?>" />
                            <input type="submit" name="submit_directory" value="Submit" class="signup btn btn-signup" />
                        </div>
                    </div>
                </form>
            </div>
        </article>
    </section>
</section>
<?php get_footer();?>