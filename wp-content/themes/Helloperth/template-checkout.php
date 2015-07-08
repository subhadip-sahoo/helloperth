<?php
/* Template Name: Checkout */
session_start();
$user = $wp_query->query['auth'];
$key = $wp_query->query['key'];
//if(!isset($_POST)){
//    wp_safe_redirect(href(HOME_PAGE));
//    exit();
//}
if(!check_valid_user($user, $key)){
    $_SESSION['session_msg_error'] = 'Unauthorized user. Please login again.';
    wp_logout();
    wp_safe_redirect(href(LOGIN_PAGE));
    exit();
}
$err_msg = '';
$war_msg = '';
$suc_msg = '';
get_header();
?>
<section class="main-container clearfix">
    <section class="main wrapper clearfix">
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <div class="page-inner-content-area registration-container clearfix">
                <?php
                while ( have_posts() ) : the_post();
                    get_template_part( 'content', 'page' );
                endwhile;
                ?>
                <?php if(!empty($err_msg)): echo message_alert($err_msg, 4); endif;?>
                <?php if(!empty($war_msg)): echo message_alert($war_msg, 3); endif;?>
                <?php if(!empty($suc_msg)): echo message_alert($suc_msg, 2); endif;?>
                <form name="checkout-form" id="checkout-form" action="<?php echo href(CONFIRM_SUBSCRIPTION_PAGE).'/'.$key.'/'.$user; ?>" method="POST" class="form_content">
                    <span class="checkout-errors"></span>
                    <div class="registration-devider clearfix">
                        <div class="registration-block">
                            <dl>
                                <dt><label>Cardholders' Name: </label></dt>
                                <dd><input type="text" name="cus_name" id="cus_name" data-stripe="name" value="<?php echo ($_POST['cus_name']) ? $_POST['cus_name'] : ''?>" class="form-control"/></dd>
                                <dt><label>CVC: </label></dt>
                                <dd><input type="text" maxlength="4" name="cvc" id="cvc" data-stripe="cvc" value="<?php echo ($_POST['cvc']) ? $_POST['cvc'] : ''?>" class="form-control"/></dd>
                                <dt><label>Expiration: <br/>(MM/YYYY)</label></dt>
                                <dd>
                                    <select name="exp-month" id="exp-month" data-stripe="exp-month" class="form-control checkout-select">
                                        <option value="">-- Select Month --</option>
                                        <?php 
                                            for($i = 1; $i <= 12; $i++):
                                                $month = ($i < 10) ? '0'.$i : $i;
                                        ?>
                                        <option value="<?php echo $month; ?>"><?php echo $month; ?></option>
                                    <?php endfor; ?>
                                    </select>
                                    <select name="exp-year" id="exp-year" data-stripe="exp-year" class="form-control checkout-select">
                                        <option value="">-- Select Year --</option>
                                        <?php 
                                            for($year = 2015; $year <= 2070; $year++):
                                        ?>
                                        <option value="<?php echo $year; ?>"><?php echo $year; ?></option>
                                    <?php endfor; ?>
                                    </select>
                                </dd>
                            </dl>
                        </div>
                        <div class="registration-block">
                            <dl>  
                                <dt><label>Card Number: </label></dt>
                                <dd><input type="text" name="number" id="number" data-stripe="number" value="<?php echo ($_POST['number']) ? $_POST['number'] : '4012888888881881'?>" class="form-control"/></dd>
                                <dt><label>Security Code:</label></dt>
                                <dd class="captcha-code-area">
                                <img src="<?php echo get_template_directory_uri();?>/captcha/CaptchaSecurityImages.php?width=100&height=40&characters=5" />
                                <div><input id="security_code" name="security_code" type="text" class="form-control"/></div></dd>
                                <div class="registration-btn-group">
                                    <input type="hidden" name="item" value="<?php echo $_POST['item']; ?>"/>
                                    <input type="hidden" name="currency" value="<?php echo $_POST['currency']; ?>"/>
                                    <input type="button" name="back" value="Back" class="signup btn btn-signup" onClick="window.history.back();"/>
                                    <button type="submit" class="signup btn btn-signup" id="proceed-to-checkout">Proceed to Subscription</button>
                                </div>
                            </dl>
                        </div>
                    </div>
                </form>
            </div>
        </article>
    </section>
</section>
<?php get_footer(); ?>

