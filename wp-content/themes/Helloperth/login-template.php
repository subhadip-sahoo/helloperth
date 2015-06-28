<?php
    /* Template Name: Login */
    session_start();
    $redirect_to = (isset($_SESSION['redirect_to'])) ? $_SESSION['redirect_to'] : href(PROFILE_PAGE);
    global $user_ID;
    if($user_ID){
        wp_safe_redirect(href(PROFILE_PAGE));
        exit();
    }
    $err_msg = '';
    $war_msg = '';
    $info_msg = '';
    $suc_msg = '';
    if(isset($_POST['submit_login'])){
        if(empty($_POST['user_login'])){
            $err_msg = 'Username is required.';
        }
        else if(empty($_POST['user_pass'])){
            $err_msg = 'Password is required.';
        }
        $remember = (isset($_POST['rememberme']) && $_POST['rememberme'] == 'on') ? TRUE : FALSE;
        if($err_msg == ''){
            $creds = array();
            $creds['user_login'] =  esc_sql($_POST['user_login']);
            $creds['user_password'] =  esc_sql($_POST['user_pass']);
            $creds['remember'] =  $remember;
            $user = wp_signon( $creds, FALSE);
            if ( is_wp_error($user) ) {
                if(isset($user->errors['invalid_username'])){
                    $war_msg = "Invalid username. ";
                }else if(isset($user->errors['incorrect_password'])){
                    $war_msg = "Incorrect password. <a href='".href(FORGOT_PASSWORD_PAGE)."' title='Lost your password'>Lost your password ?</a>";
                }
                else if(isset($user->errors['verification_failed'])){
                    $from = get_option('admin_email');
                    $from_name = get_option('blogname');
                    $headers = "From: $from_name <$from>\r\n";
                    $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
                    $subject = "To access your account please subscribe one of our membership package.";
                    $msg = "Hi ".$user->errors['display_name'][0].".<br/><br/>";
                    $msg .= "Your login attempt was failed and that is because you have not subscribed one of our membership packages yet.<br/>";
                    $msg .= "To access you account please make sure that you have paid for any of the suscription packages.<br/>";
                    $msg .= "Plase make your payment ";
                    $msg .= "<a href='".href(MAKE_PAYMENT_PAGE)."/".$user->errors['user_activation_key'][0]."/".$user->errors['ID'][0]."'>here</a><br/><br/>";
                    $msg .= "If you have any query or issue, please contact to administrator.<br/><br/>";
                    $msg .= "Best regards,<br/>$from_name Admin";
                    wp_mail( $user->errors['user_email'][0], $subject, $msg, $headers );
                    $war_msg = $user->errors['verification_failed'][0];
                }else if(isset($user->errors['account_expired'])){
                    $war_msg = $user->errors['account_expired'][0];
                }else if(isset($user->errors['disabled_account'])){
                    $war_msg = $user->errors['disabled_account'][0];
                }else{
                    $war_msg = 'Username / password does not match.';
                }
            }
            else {
                if ( isset($user->roles) && is_array( $user->roles ) ) {
                    if ( in_array( 'subscriber', $user->roles ) || in_array( 'siteuser', $user->roles ) || in_array( 'advertiser', $user->roles )) {
                        wp_safe_redirect($redirect_to);
                        exit();
                    } 
                    if ( in_array( 'administrator', $user->roles ) ) {
                        wp_safe_redirect(admin_url());
                        exit();
                    }
                }
                wp_safe_redirect($redirect_to);
                exit();
            }
        }
    }
    get_header();
?>
<section class="main-container login-container clearfix" style="background-image:url(<?php bloginfo('template_directory'); ?>/images/login-background.jpg);">
    <section class="main wrapper clearfix">
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <div class="page-inner-content-area login-page-inner clearfix">
                <?php
                while ( have_posts() ) : the_post();
                    get_template_part( 'content', 'page' );
                endwhile; wp_reset_query();
                ?>
                <?php if(!empty($err_msg)): echo '<p style="color: red;">'.$err_msg.'</p>'; endif;?>
                <?php if(!empty($war_msg)): echo '<p style="color: orange;">'.$war_msg.'</p>'; endif;?>
                <?php if(isset($_SESSION['session_msg']) && !empty($_SESSION['session_msg'])): echo '<p style="color: green;">'.$_SESSION['session_msg'].'</p>'; endif; unset($_SESSION['session_msg']);?>
                <?php if(isset($_SESSION['session_msg_error']) && !empty($_SESSION['session_msg_error'])): echo '<p style="color: red;">'.$_SESSION['session_msg_error'].'</p>'; endif; unset($_SESSION['session_msg_error']);?>
                <form name="login" action="" method="POST" class="form_content">
                    <div class="login-form-grp">
                        <dl>
                          <dt><label>Username: </label></dt>
                          <dd><input type="text" name="user_login" id="user_login" value="" class="form-control" /></dd>
                          <dt><label>Password: </label></dt>
                          <dd><input type="password" name="user_pass" id="user_pass" value="" class="form-control" /></dd>
                          <dt></dt>
                          <dd><label for="rememberme"><input type="checkbox" name="rememberme" value="on"> Remember me</label></dd>
                        </dl>
                    </div>
                    <?php //do_action('login_form');?>
                    <div class="login-form-extra">
                        <p class="forget-p">
                            <input type="submit" name="submit_login" value="Login" class="signup btn btn-signup" />
                            <a href="<?php echo href(FORGOT_PASSWORD_PAGE);?>" title="Forgot Password">Forgot password ?</a>
                        </p>
                        <p>
                            Don't have an account? Create an account <a href="<?php echo href(REGISTRATION_PAGE);?>" title="Register">here</a>
                        </p>
                    </div>
                </form>
            </div>
        </article>
    </section>
</section>
<?php get_footer();?>