<?php
/* Template Name: Profile */
session_start();
if(isset($_SESSION['redirect_to'])){ unset($_SESSION['redirect_to']); }
global $user_ID, $wpdb;
if(!$user_ID){
    $_SESSION['redirect_to'] = href(PROFILE_PAGE);
    wp_safe_redirect(href(LOGIN_PAGE));
    exit();
}
$err_msg = '';
$war_msg = '';
$suc_msg = '';
if(isset($_POST['update_details'])){
    if(empty($_POST['first_name'])){
        $err_msg = 'First name is required.';
    }
    else if(empty($_POST['last_name'])){
        $err_msg = 'Last name is required.';
    }
    else if(empty($_POST['contact_number'])){
        $err_msg = 'Contact number is required.';
    }
    else if(empty($_POST['user_email'])){
        $err_msg = 'Email address is required.';
    }
    else if(!filter_var($_POST['user_email'], FILTER_VALIDATE_EMAIL) === TRUE){
        $err_msg = 'Please enter a valid email address';
    }
    else if(empty($_POST['user_pass']) && !empty($_POST['cuser_pass'])){
        $err_msg = 'New password is required.';
    }
    else if(!empty($_POST['user_pass']) && $_POST['user_pass'] <> $_POST['cuser_pass']){
        $err_msg = 'Password does not match.';
    }
    if($err_msg == ''){
        if($id = email_exists($_POST['user_email'])){
             if($id <> $user_ID){
                 $war_msg = 'This email is already registered, please choose another one.';
             }
        }
        if($war_msg == ''){
            $data = array(
                'ID' => $user_ID,
                'user_email' => esc_sql($_POST['user_email']),
                'first_name' => esc_sql($_POST['first_name']),
                'last_name' => esc_sql($_POST['last_name']),
                'display_name' => esc_sql($_POST['first_name']).' '.esc_sql($_POST['last_name']),
//                'description' => esc_sql($_POST['description']),
            );

            $UID = wp_update_user( $data );
            if(is_wp_error($UID)){
                $err_msg = 'There is an error occured during update. PLease try again later.';
            }
            if($err_msg == ''){
                update_user_meta($user_ID, 'contact_number', esc_sql($_POST['contact_number']));
                update_user_meta($user_ID, 'profile_pic', esc_sql($_POST['post_thumbnail']));
                if(!empty($_POST['user_pass'])){
                    wp_set_password($_POST['user_pass'], $user_ID);
                    $suc_msg = 'Profile has been successfully updated. Please <a href="'.href(LOGIN_PAGE).'">login</a> again with your updated password.';
                }else{
                    $suc_msg = 'Profile has been successfully updated.';
                }
            }
        }
    }
}
$attachment_id = get_the_author_meta( 'profile_pic', $user_ID );
$isset_profile_pic = 0;
if(is_numeric($attachment_id)){
    $profile_pic = wp_get_attachment_image_src($attachment_id, 'full');
    $isset_profile_pic = 1;
}
$query = array();
$query['post_type'] =  'directories';
$query['taxonomy'] =  'directories-cat';
$query['post_status'] =  'publish';
$query['author'] =  $user_ID;
$userdata = get_userdata($user_ID);
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
<section class="main-container profile-container clearfix">
    <section class="main wrapper clearfix">
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <div class="page-inner-content-area clearfix">
                <?php
                while ( have_posts() ) : the_post();
                    get_template_part( 'content', 'page' );
                endwhile;
                ?>
                <div class="registration-devider clearfix">
                    <div class="registration-block">
<!--                        <div class="form-group-lists-div">
                            <label for="upload_image">Profile Picture</label>
                            <div class="upload-image-block" id="figure_parent">
                                <figure class="upload-image-img" id="dir_feature_image">
                                    <?php //if($isset_profile_pic == 1): ?>
                                    <img src="<?php //echo $profile_pic[0]; ?>" width="200" height="150" alt="Profile picture"/>
                                    <?php //endif; ?>
                                    <?php //if($isset_profile_pic == 1) :?>
                                    <a href="javascript:void(0);" title="Remove image" id="remove_dir_feature_image" class="btn btn-remove-image"><i class="fa fa-times-circle"></i></a>
                                    <?php //endif; ?>
                                </figure>
                                <div class="upload-btn-group" id="control-div">
                                    <?php //if($isset_profile_pic == 0) :?>
                                    <button id="upload_image_button" class="button btn upload-btn" type="button" value="Upload Image" ><i class="fa fa-plus-circle"></i><span>Upload Image</span></button>
                                    <?php //endif; ?>                                     
                                </div>
                            </div>
                        </div>-->
                        <div class="view-profile-container clearfix">
                            <?php
                                $author_id = $user_ID; 
                                $author_posts = get_posts( array(
                                    'author' => $author_id,
                                    'post_type' => 'directories',
                                    'post_status' => 'publish',
                                    'posts_per_page' => -1,
                                ) );

                                $counter = 0;
                                if(!empty($author_posts)){
                                    echo '<h3 class="views-directory-title">Views by directories:</h3><div class="mCustomScrollbar mCustomScrollbar-view-directory"><ul class="views-directoryUl">'; 
                                    foreach ( $author_posts as $post ){
                                        $views = absint( get_post_meta( $post->ID, 'views', true ) );
                                        $counter += $views;
                                        echo "<li class='views-directoryLi'>{$post->post_title} <span>({$views})</span></li>";
                                    }
                                    echo "</ul></div><hr /><p>Total Number of views: <strong>{$counter}</strong></p>";
                                }else{
                                    echo '<h3 class="views-directory-title">Views by directories:</h3><div class="mCustomScrollbar mCustomScrollbar-view-directory"><ul class="views-directoryUl"><li>No directories found!<li></ul></div>'; 
                                }
                                wp_reset_query();
                            ?>
                        </div>
                        
                        <div class="user-menu-guide">
                            <a href="<?php echo href(USER_GUIDE); ?>" class="btn">User Guide</a>
                            <a href="<?php echo href(ADD_DIRECTORY_PAGE); ?>" class="btn">Add Listing <i class="fa fa-plus-circle"></i></a>
                            <a href="<?php echo href(MAKE_PAYMENT_PAGE).'/'.$userdata->user_activation_key.'/'.$user_ID; ?>" class="btn">Subscription Plans</a>
                        </div>
                        <div class="txn-log-container">
                            <div class="txn-log">
                                <h4>Last 10 transactions</h4>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Plan</th>
                                                <th>Transaction ID</th>
                                                <th>Transaction Amount</th>
                                                <th>Transaction Date</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php 
                                            $transaction_query = "SELECT u.display_name as username, p.post_title as package_name, up.id, up.transaction_id, up.transaction_amount, up.subscription_id, date_format(up.payment_date, '%D %b, %Y') as payment_date, (SELECT date_format(meta_value,'%D %b, %Y at %l:%i %p') FROM {$wpdb->prefix}usermeta WHERE meta_key = 'account_expiry' and user_id = up.id_user) AS expiry_date, up.id FROM $table_name_users as up LEFT JOIN {$wpdb->prefix}users as u on up.id_user = u.ID LEFT JOIN {$wpdb->prefix}posts as p on up.id_package = p.ID WHERE up.id_user = {$user_ID} ORDER BY up.id DESC LIMIT 0,10";
                                            $results = $wpdb->get_results($transaction_query, ARRAY_A);
                                            if(!empty($results) && is_array($results)):
                                                foreach ($results as $res) :
                                        ?>
                                            <tr>
                                                <td><?php echo $res['package_name']; ?></td>
                                                <td><?php echo $res['transaction_id']; ?></td>
                                                <td><?php echo $res['transaction_amount']; ?></td>
                                                <td><?php echo $res['payment_date']; ?></td>
                                                <td><a href="#txn-invoice" data-id="<?php echo $res['id']; ?>" class="btn txn-details">View</a></td>
                                            </tr>
                                        <?php
                                                endforeach;
                                            endif;
                                        ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div id="txn-invoice" class="zoom-anim-dialog mfp-hide perth-popup-container"></div>
                    </div>
                    <div class="registration-block">
                        <?php if(!empty($err_msg)): echo message_alert($err_msg, 4); endif;?>
                        <?php if(!empty($war_msg)): echo message_alert($war_msg, 3); endif;?>
                        <?php if(!empty($suc_msg)): echo message_alert($suc_msg, 2); endif;?>
                        <?php if(isset($_SESSION['session_msg']) && !empty($_SESSION['session_msg'])): echo message_alert($_SESSION['session_msg'], 2); endif; unset($_SESSION['session_msg']);?>
                        <form name="user_profile" action="" method="POST" class="form_content">
                            <div class="form-group-lists">
                                <div class="form-group-lists-div">
                                    <label>Username:</label>
                                    <input type="text" name="user_login" id="user_login" value="<?php echo $userdata->user_login; ?>" class="form-control" disabled/>
                                </div>
                                <div class="form-group-lists-div">
                                    <label>First Name: </label>
                                    <input type="text" name="first_name" id="first_name" value="<?php echo $userdata->first_name; ?>" class="form-control"/>
                                </div>
                                <div class="form-group-lists-div">
                                    <label>Last Name: </label>
                                    <input type="text" name="last_name" id="last_name" value="<?php echo $userdata->last_name; ?>" class="form-control"/>
                                </div>
                                <div class="form-group-lists-div">
                                    <label>Email Address: </label>
                                    <input type="text" name="user_email" id="user_email" value="<?php echo $userdata->user_email; ?>" class="form-control"/>
                                </div>
                                <div class="form-group-lists-div">
                                    <label>Contact Number: </label>
                                    <input type="text" name="contact_number" id="contact_number" value="<?php echo $userdata->contact_number; ?>" class="form-control"/>
                                </div>
<!--                                <div class="form-group-lists-div">
                                    <label>Biographical Info: </label>
                                    <?php //wp_editor(wp_richedit_pre($userdata->description), 'description', array('media_buttons' => false, 'wpautop' => false, 'quicktags' => false, 'textarea_rows' => 5));?>
                                </div>-->
                                <div class="form-group-lists-div">
                                    <label>New Password: </label>
                                    <input type="password" name="user_pass" id="user_pass" value="" class="form-control"/>
                                </div>
                                <div class="form-group-lists-div">
                                    <label>Confirm Password: </label>
                                    <input type="password" name="cuser_pass" id="cuser_pass" value="" class="form-control"/>
                                </div>
                                <div class="form-group-lists-div">
                                    <label>&nbsp;</label>
                                    <div id="psm-pass-strength-result" class="form-control"><p>Strength Indicator</p></div>
                                </div>
                                <div class="form-group-lists-div">
                                    <input type="hidden" name="post_thumbnail" id="post_thumbnail" value="<?php echo $userdata->profile_pic; ?>" />
                                    <input type="submit" name="update_details" value="Update" class="signup btn btn-signup" />
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </article>
        <!--<div class="loading-div"><img src="<?php //echo get_template_directory_uri(); ?>/images/ajax-loader.gif" /></div>-->
        <section class="inner-blocks-cts-area clearfix advance-search-lists" id="directorirs"></section>
    </section>
</section>
<?php get_footer(); ?>

