<?php
/* Template Name: Forgot Password */

$err_msg = '';
$war_msg ='';
$suc_msg = '';
$info_msg = '';
global $wpdb ,$user_ID;
if($user_ID){
    wp_safe_redirect(href(LOGIN_PAGE));
    exit();
}

if(isset($_POST['get_new_password'])){								   
    $user_logs = esc_sql($_POST['user_log']);
    if(empty($user_logs)) { 
        $err_msg = "Username / Email address should not be empty.";										
    }
    else if ( strpos($user_log, '@') ) {
        if(filter_var($user_logs, FILTER_VALIDATE_EMAIL) === FALSE){
            $err_msg = "Please enter a valid email.";
        }
    }
     
    if($err_msg == '') {
        $user_log = $wpdb->escape(trim($_POST['user_log']));					

        if ( strpos($user_log, '@') ) {
            $user_data = get_user_by('email',$user_log);
            if(!$user_data || $user_data->caps[administrator] == 1) { 
                $err_msg ='Invalid Email Address';														
            }
        }else {
            $user_data = get_user_by('login',$user_log);
            if(empty($user_data) || $user_data->caps[administrator] == 1) { 
                $err_msg .='Invalid Username';														
            }
        }

        if($user_data){													
            $user_login = $user_data->user_login;
            $user_email = $user_data->user_email;													
            $id_user= $user_data->ID;
            $user_info = get_userdata( $id_user);
            $generate = $user_info->user_activation_key;
            $from = get_option('admin_email');
            $headers = "From: Hello Perth <$from>\r\n";
            $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
            $subject = "Resetting Password Request";
            $message = __('Someone requested that the password be reset for the following account:') . "\r\n\r\n"."<br/>";
            $message .= sprintf(__('Username: %s'), $user_login) . "\r\n\r\n"."<br/>";
            $message .= __('If this was a mistake, just ignore this email and nothing will happen.') . "\r\n\r\n"."<br/>";
            $home_url= home_url();
            $url = $home_url."/reset-password?exsiting_id=$id_user&activation_number=$generate";
            $message .=__('To reset your password, visit the following address:') . "\r\n\r\n";
            $message .= "<a href='$url'>Click here to reset password</a>\r\n";

            if ( $message && !wp_mail($user_email, $subject, $message,$headers) ) {
                $war_msg = "Email failed to send for some unknown reason";
            }
            else{
                $suc_msg = "Please check your email";
            }							
        }							
    }
}
get_header();
?>
<section class="main-container login-container clearfix" style="background-image:url(<?php bloginfo('template_directory'); ?>/images/login-background.jpg);">
    <section class="main wrapper clearfix">
        <article class="hentry" id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <div class="page-inner-content-area login-page-inner clearfix">
        <?php
        while ( have_posts() ) : the_post();
            get_template_part( 'content', 'page' );
        endwhile;
        ?>
        <?php if(!empty($err_msg)): echo message_alert($err_msg, 4); endif;?>
        <?php if(!empty($war_msg)): echo message_alert($war_msg, 3); endif;?>
        <?php if(!empty($suc_msg)): echo message_alert($suc_msg, 2); endif;?>
        <form name="forgot_pwd" action="" method="POST" class="form_content">
            <div class="login-form-grp">
                <dl>
                  <dt><label>Username / Email Address: </label></dt>
                  <dd></dd>
                </dl>
            </div>
            <p>
                <input type="text" name="user_log" id="user_log" value="" class="form-control" />
            </p>
            <p>
                <input type="submit" name="get_new_password" value="Submit" class="signup btn btn-signup" />
            </p>
        </form>
    </section>
</section>
<?php get_footer();?>