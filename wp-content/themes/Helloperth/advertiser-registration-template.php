<?php
    /* Template Name: Advertiser Registration */
    session_start();
    global $user_ID;
    if($user_ID){
        $userdata = get_userdata($user_ID);
        $user_activation_key = $userdata->user_activation_key;
        wp_safe_redirect(href(MAKE_PAYMENT_PAGE)."/$user_activation_key/$user_ID");
        exit();
    }
    $err_msg = '';
    $war_msg = '';
    $info_msg = '';
    $suc_msg = '';
    if(isset($_POST['submit_reg'])){
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
        else if(empty($_POST['user_login'])){
            $err_msg = 'Username is required.';
        }
        else if(empty($_POST['user_pass'])){
            $err_msg = 'Password is required.';
        }
        else if($_POST['user_pass'] <> $_POST['con_password']){
            $err_msg = 'Password does not match.';
        }
        if($err_msg == ''){
            if( $_SESSION['security_code'] == $_POST['security_code'] && !empty($_SESSION['security_code'] ) ) {
                $userinfo = array(
                    'user_login' => esc_sql($_POST['user_login']),
                    'user_pass'  => esc_sql($_POST['user_pass']),
                    'user_email' => esc_sql($_POST['user_email']),
                    'display_name' => esc_sql($_POST['first_name'].' '.$_POST['last_name'])
                );
                $ID = wp_insert_user($userinfo);
                if ( is_wp_error($ID) ) {
                    if(array_key_exists('existing_user_email', $ID->errors)){
                        $war_msg = 'Sorry, email address already exists. Please try another one.';
                    }else if(array_key_exists('existing_user_login', $ID->errors)){
                        $war_msg = 'Sorry, username already exists. Please try another one.';
                    }else{
                        $war_msg = 'Sorry, username / email address already exists. Please try another one.';
                    }
                }
                if($war_msg == ''){
                    $userdata = array(
                        'ID' => $ID,
                        'user_pass' => esc_sql($_POST['user_pass']),
                    );
                    $new_user_id = wp_update_user( $userdata );
                    if ( is_wp_error($new_user_id) ) {
                        $err_msg = 'Registration failed. Please try again later.';
                    }else{
                        $role = new WP_User( $new_user_id );
                        $role->add_role( 'advertiser' );
                        $keys = $wpdb->get_results("SELECT * FROM $wpdb->users WHERE ID = $new_user_id", ARRAY_A);
                        if($wpdb->num_rows == 1){
                            foreach ($keys as $key) {
                                if(!empty($key['user_activation_key'])){
                                    $act_key = $key['user_activation_key'];
                                    $set_key = 1;
                                }else{
                                    $set_key = 0;
                                }
                            }
                        }
                        $from = get_option('admin_email');
                        $from_name = get_option('blogname');
                        $headers = "From: $from_name <$from>\r\n";
                        $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
                        $subject = "Welcome to Hello Perth. Your account has been created";
                        $msg = "Hi ".$_POST['first_name']." ".$_POST['last_name'].".<br/><br/>";
                        $msg .= "Thank you for registration.<br/>";
                        $msg .= "Your login details<br/>Username: ".$_POST['user_login']."<br/>Password: Your choosen password.<br/>";
                        $msg .= "To access you account please make sure that you have paid for any of the suscription packages.<br/>";
                        $msg .= "If you have not paid yet then please ";
                        if(isset($set_key) && $set_key == 1){
                            $msg .= "<a href='".href(MAKE_PAYMENT_PAGE)."/$act_key/$new_user_id'>click here</a><br/><br/>";
                        }else{
                            $msg .= "<a href='".href(MAKE_PAYMENT_PAGE)."/$new_user_id'>click here</a><br/><br/>";
                        }
                        $msg .= "If you want to login then please ";
                        $msg .= "<a href='".href(LOGIN_PAGE)."'>click here</a><br/><br/>";
                        $msg .= "Best regards<br/>$from_name Admin";

                        wp_mail( $_POST['user_email'], $subject, $msg, $headers );

                        update_user_meta($new_user_id,'account_status', 0);
                        update_user_meta($new_user_id,'first_name', esc_sql($_POST['first_name']));
                        update_user_meta($new_user_id,'last_name', esc_sql($_POST['last_name']));
                        update_user_meta($new_user_id,'contact_number', esc_sql($_POST['contact_number']));

                        $to = get_option('admin_email');

                        $from_name = esc_sql($_POST['first_name'].' '.$_POST['last_name']);
                        $headers = "From: $from_name <".$_POST['user_email'].">\r\n";
                        $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

                        $subject = 'New user registration details';

                        $msg = "An advertiser has registered in Hello Perth. Please find the details below.<br/><br/>";
                        $msg .= "Name: ".esc_sql($_POST['first_name'].' '.$_POST['last_name'])."<br/>";
                        $msg .= "Contact Number: ".esc_sql($_POST['contact_number'])."<br/>";
                        $msg .= "Email Address: ".esc_sql($_POST['user_email'])."<br/>";
                        $msg .= "Username: ".esc_sql($_POST['user_login'])."<br/>";

                        wp_mail($to, $subject, $msg, $headers);
                        
                        unset($_SESSION['security_code']);
                        unset($_POST);
                        $_SESSION['session_msg'] = 'Your registration has been successfully completed. Please select a plan.';
                        wp_safe_redirect(href(MAKE_PAYMENT_PAGE)."/$act_key/$new_user_id");
                        exit();
                    }
                }else{
                    $err_msg = 'Security code does not match';
                }
            }
        }
    }
    get_header();
?>
<section class="main-container clearfix">
    <section class="main wrapper clearfix">
        <article class="hentry" id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <div class="page-inner-content-areaa clearfix">
                <?php
                while ( have_posts() ) : the_post();
                    get_template_part( 'content', 'page' );
                endwhile;
                ?>
            </div>
            <div class="page-inner-content-area registration-container clearfix">
                
                <header class="site-heading">
                    <h2>Advertiser Account Registration</h2>
                </header>
                <?php if(!empty($err_msg)): echo '<p style="color: red;">'.$err_msg.'</p>'; endif;?>
                <?php if(!empty($war_msg)): echo '<p style="color: orange;">'.$war_msg.'</p>'; endif;?>
                <?php if(!empty($suc_msg)): echo '<p style="color: green;">'.$suc_msg.'</p>'; endif;?>
                <form name="registration" action="" method="POST" class="form_content" id="advertise-with-us">
                    <div class="registration-devider clearfix">
                        <div class="registration-block">
                            <dl>
                                <dt><label>First Name: </label></dt>
                                <dd><input type="text" name="first_name" id="first_name" value="<?php echo ($_POST['first_name']) ? $_POST['first_name'] : ''?>" class="form-control validate[required, minSize[2]]"/></dd>
                                <dt><label>Last Name: </label></dt>
                                <dd><input type="text" name="last_name" id="last_name" value="<?php echo ($_POST['last_name']) ? $_POST['last_name'] : ''?>" class="form-control validate[required, minSize[2]]"/></dd>
                                <dt><label>Contact Number: </label></dt>
                                <dd><input type="text" name="contact_number" id="contact_number" value="<?php echo ($_POST['contact_number']) ? $_POST['contact_number'] : ''?>" class="form-control validate[required, custom[phone]]"/></dd>
                                <dt><label>Email Address: </label></dt>
                                <dd><input type="text" name="user_email" id="user_email" value="<?php echo ($_POST['user_email']) ? $_POST['user_email'] : ''?>" class="form-control validate[required, custom[email]]"/></dd>
                            </dl>
                        </div>
                        <div class="registration-block">
                            <dl>
                                <dt><label>Username: </label></dt>
                                <dd><input type="text" name="user_login" id="user_login" value="<?php echo ($_POST['user_login']) ? $_POST['user_login'] : ''?>" class="form-control validate[required]"/></dd>
                                <dt><label>Password: </label></dt>
                                <dd><input type="password" name="user_pass" id="user_pass" value="" class="form-control validate[required]"/></dd>
                                <dt><label>Confirm Password: </label></dt>
                                <dd><input type="password" name="con_password" id="con_password" value="" class="form-control validate[required, equals[user_pass]]"/></dd>
                                <dt><label>Security Code:</label></dt>
                                <dd class="captcha-code-area">
                                <img src="<?php echo get_template_directory_uri();?>/captcha/CaptchaSecurityImages.php?width=100&height=40&characters=5" />
                                <div><input id="security_code" name="security_code" type="text" class="form-control validate[required]"/></div></dd>
                            </dl>
                        </div>
                    </div>
                    <div class="registration-btn-group">
                        <input type="submit" name="submit_reg" value="Register" class="signup btn btn-signup" />
                    </div>
                </form>
            </div>
        </article>
    </section>
</section>
<?php get_footer();?>