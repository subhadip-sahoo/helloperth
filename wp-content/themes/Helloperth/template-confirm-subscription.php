<?php
/* Template Name: Confirm Subscription */
session_start();
if(isset($_SESSION['subscription'])){unset($_SESSION['subscription']);}
require_once dirname(__FILE__).'/stripe/init.php';
$user = $wp_query->query['auth'];
$key = $wp_query->query['key'];
$userdata = get_userdata($user);
if(!check_valid_user($user, $key)){
    $_SESSION['session_msg_error'] = 'Unauthorized user. Please login again.';
    wp_logout();
    wp_safe_redirect(href(LOGIN_PAGE));
    exit();
}
$err_msg = '';
$war_msg = '';
$suc_msg = '';
if(isset($_POST['stripe-subscribe'])){
    $token = $_POST['stripeToken'];
    $cus_id = get_user_meta($user, 'stripe_cus_id', TRUE);
    \Stripe\Stripe::setApiKey(stripe_api_key('secret'));
    $response = create_stripe_subscription($token, $cus_id, $_POST['item']);
    if($response[0] == 1){
        $subscription = $response[1];
        
        $_SESSION['subscription']['subscription_id'] = $subscription->id;
        $_SESSION['subscription']['name'] = $subscription->plan->name;
        $_SESSION['subscription']['interval'] = $subscription->plan->interval;
        $_SESSION['subscription']['interval_count'] = $subscription->plan->interval_count;
        $_SESSION['subscription']['amount'] = $subscription->plan->amount;
        $_SESSION['subscription']['currency'] = $subscription->plan->currency;
        $_SESSION['subscription']['customer'] = $subscription->customer;
        $_SESSION['subscription']['status'] = $subscription->status;
        $_SESSION['subscription']['current_period_start'] = $subscription->current_period_start;
        $_SESSION['subscription']['current_period_end'] = $subscription->current_period_end;
        
        unset($_POST);
        
        wp_safe_redirect(href(SUBSCRIPTION_COMPLETE_PAGE));
        exit();
    }else{
        $error = explode(':',$response[1]);
        $err_msg = $error[1];
    }
}
get_header();
?>
<section class="main-container clearfix">
    <section class="main wrapper clearfix">
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <div class="page-inner-content-area registration-container clearfix">
                <?php
                while ( have_posts() ) : the_post();
                    get_template_part( 'content', 'page' );
                endwhile; wp_reset_query();
                ?>
                <?php if(!empty($err_msg)): echo '<p style="color: red;">'.$err_msg.'</p>'; endif;?>
                <?php if(!empty($war_msg)): echo '<p style="color: orange;">'.$war_msg.'</p>'; endif;?>
                <?php if(!empty($suc_msg)): echo '<p style="color: green;">'.$suc_msg.'</p>'; endif;?>
                <form name="checkout-confirm" id="checkout-confirm" action="" method="POST" class="form_content">
                    <div class="registration-devider clearfix">
                        <div class="registration-block">
                            <dl>
                                <?php 
                                    $post = get_post($_POST['item']);
                                    $terms = get_the_terms($_POST['item'], array('ad-locations', 'subscription-durations') ); 
                                ?>
                                <dt><label>Package Name: </label></dt>
                                <dd><?php echo $post->post_title; ?></dd>
                                <dt><label>Price: </label></dt>
                                <dd><?php echo get_post_meta($_POST['item'], 'package_price', TRUE).' '.strtoupper($_POST['currency']); ?></dd>
                                <dt><label>Period: </label></dt>
                                <dd><?php echo get_post_meta($_POST['item'], 'interval_count', true).' '.ucwords(get_post_meta($_POST['item'], 'interval', TRUE)); ?></dd>
                            </dl>
                        </div>
                        <div class="registration-block">
                            <dl>
                                <dt><label>Cardholders' Name: </label></dt>
                                <dd><?php echo $_POST['cus_name']; ?></dd>
                                <dt><label>Card Number: </label></dt>
                                <dd>
                                    <p><?php echo generate_asterisk_value($_POST['number']); ?></p>
                                    <a href="javascript:void(0);" data-status="0" data-show="<?php echo $_POST['number']; ?>" data-hide="<?php echo generate_asterisk_value($_POST['number']); ?>" class="asterik-toggle">Toggle Card Number</a>
                                </dd>
                                <dt><label>CVC: </label></dt>
                                <dd>
                                    <p><?php echo generate_asterisk_value($_POST['cvc']); ?></p> 
                                    <a href="javascript:void(0);" data-status="0" data-show="<?php echo $_POST['cvc']; ?>" data-hide="<?php echo generate_asterisk_value($_POST['cvc']); ?>" class="asterik-toggle">Toggle CVC</a>
                                </dd>
                                <dt><label>Expiration: <br/>(MM/YYYY)</label></dt>
                                <dd><?php echo $_POST['exp-month'].' / '.$_POST['exp-year']; ?></dd>
                            </dl>
                        </div>
                    </div>
                    <div class="registration-btn-group">
                        <input type="hidden" name="stripeToken" value="<?php echo $_POST['stripeToken']; ?>" />
                        <input type="hidden" name="item" value="<?php echo $_POST['item']; ?>" />
                        <input type="hidden" name="currency" value="<?php echo $_POST['currency']; ?>" />
                        <input type="hidden" name="cus_name" value="<?php echo $_POST['cus_name']; ?>" />
                        <input type="hidden" name="cvc" value="<?php echo $_POST['cvc']; ?>" />
                        <input type="hidden" name="exp-month" value="<?php echo $_POST['exp-month']; ?>" />
                        <input type="hidden" name="exp-year" value="<?php echo $_POST['exp-year']; ?>" />
                        <input type="hidden" name="number" value="<?php echo $_POST['number']; ?>" />
                        <input type="hidden" name="interval_count" value="<?php echo $p3; ?>" />
                        <input type="button" name="back" value="Back" class="signup btn btn-signup" onClick="window.history.back();"/>
                        <input type="submit" name="stripe-subscribe" value="Confirm & Subscribe" class="signup btn btn-signup" />
                    </div>
                </form>
            </div>
        </article>
    </section>
</section>
<?php get_footer(); ?>