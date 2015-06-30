<?php
/* Template Name: Reset Password */
session_start();
global $wpdb, $user_ID ;
$err_msg = '';
$war_msg = '';
$suc_msg = '';
$info_msg = '';
$number = $_GET['activation_number'];

if($number == '' || is_user_logged_in()){
    wp_safe_redirect(site_url());
    exit();
}

if(isset($_POST['reset_password'])){
    $new_pass= esc_sql($_POST['new_pass']);
    $con_pass= esc_sql($_POST['con_pass']);
    if(empty($new_pass)) { 
        $err_msg .= "Please Enter New Password.<br/>";
    }
    else if($new_pass != $con_pass){
        $war_msg .= "Password does not match";
    }
    if($err_msg == '' && $war_msg == ''){
        $us_id = esc_sql($_POST['us_id']);
        $numberss = $_POST['numbers'];
        $user_info = get_userdata( $us_id);
        $generate = $user_info->user_activation_key;	
        if($numberss <> $generate){
            $err_msg = 'Activation key is either invalid or expired. Please try again.';
        }else{
            $update = wp_update_user( array ( 'ID' => $us_id, 'user_pass' =>$new_pass) ) ;
            if($update){
                $_SESSION['session_msg'] = 'Your Password has been reset successfully. Please login.';
                wp_safe_redirect(href(LOGIN_PAGE));
                exit();
            }
        }
    }
}
get_header();
?>
<section class="main-container clearfix">
    <section class="main wrapper clearfix">
        <?php
        while ( have_posts() ) : the_post();
            get_template_part( 'content', 'page' );
        endwhile;
        ?>
        <?php if(!empty($err_msg)): echo '<p style="color: red;">'.$err_msg.'</p>'; endif;?>
        <?php if(!empty($war_msg)): echo '<p style="color: orange;">'.$war_msg.'</p>'; endif;?>
        <form name="reset_pwd" action="" method="POST" class="form_content">
            <p>
                <label>New Password: </label>
                <input type="password" name="new_pass" id="new_pass" value="" />
            </p>
            <p>
                <label>Confirm Password: </label>
                <input type="password" name="con_pass" id="con_pass" value="" />
            </p>
            <p>
                <input type="hidden" value="<?php $exsiting_user_id = $_GET['exsiting_id']; echo $exsiting_user_id;?>" name="us_id">
                <input type="hidden" value="<?php $numbers = $_GET['activation_number']; echo $numbers;?>" name="numbers">
                <input type="submit" name="reset_password" value="Reset Password" class="signup" />
            </p>
        </form>
    </section>
</section>
<?php get_footer();?>