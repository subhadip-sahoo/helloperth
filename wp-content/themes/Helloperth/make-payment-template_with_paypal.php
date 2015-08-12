<?php
/* Template Name: Make Payment With Paypal */
global $user_ID;
$user = $wp_query->query['auth'];
$key = $wp_query->query['key'];
if(!check_valid_user($user, $key)){
    $_SESSION['session_msg_error'] = 'Unauthorized user. Please login again.';
    wp_logout();
    wp_safe_redirect(href(LOGIN_PAGE));
    exit();
}
get_header();
?>
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
            <div class="payment-content-area clearfix">
                <div class="grid-row">
                    <?php $action = (get_field('paypal_environment', 'option') == 'sandbox')?'https://www.sandbox.paypal.com/cgi-bin/webscr':'https://www.paypal.com/cgi-bin/webscr'; ?>
                    <?php query_posts(array('post_type' => 'packages', 'posts_per_page' => -1, 'taxonomy' => array('ad-locations', 'subscription-durations'))); ?>
                    <?php if(have_posts()): ?>
                    <?php while(have_posts()): the_post(); ?>
                    <?php $terms = get_the_terms(get_the_ID(), array('ad-locations', 'subscription-durations') ); ?>
                        <div class="grid-row-4"> 
                            <section class="payment-table-block text-center">
                                <h2>Subscription</h2>
                                <h3><?php the_title(); ?></h3>
                                <div class="payment-table-price-area">
                                    <p class="price"><i>$</i><strong><?php echo get_field('price'); ?></strong></p>
                                </div>
                                <?php if(!empty($terms)): ?>
                                <?php foreach($terms as $term): ?>
                                <?php if($term->taxonomy == 'subscription-durations'):?>
                                    <div class="payment-table-month-area"><?php echo $term->name; ?></div>
                                <?php $p3 = intval($term->name); ?>
                                <?php endif; ?>
                                <?php if($term->taxonomy == 'ad-locations'):?>
                                    <div class="payment-table-description-area"><?php echo $term->name; ?></div>
                                <?php endif; ?>
                                
                                <?php endforeach; ?><div class="payment-table-lists-description-area">
                                    <ul>
                                        <li>Lorem ipsum dolor sit</li>
                                        <li>Lorem ipsum dolor</li>
                                        <li>Lorem ipsum sit</li>
                                    </ul>
                                </div>
                                <?php endif; ?>
                                <div class="payment-table-button-area">
                                    <div data-speed="2550" data-delay="750" class="lazy-load-box effect-zoomin">
                                        <div id="accept_paypal_payment_form">
                                            <form action="<?php echo $action;?>" method="POST" name="_xclick">
                                                <input type="hidden" name="business" value="<?php echo get_field('paypal_email', 'option');?>">
                                                <input type="hidden" name="cmd" value="_xclick-subscriptions">
                                                <input type="hidden" name="item_name" value="<?php the_title(); ?>">
                                                <input type="hidden" name="item_number" value="<?php echo get_the_ID(); ?>">
                                                <input type="hidden" name="return" value="<?php echo home_url().'/login/';?>">
                                                <input type="hidden" name="cancel_return" value="<?php echo home_url().'/make-payment/';?>">
                                                <input type="hidden" name="currency_code" value="AUD">
                                                <input type="hidden" name="notify_url" value="<?php echo get_template_directory_uri();?>/paypal/ipn_listner.php">
                                                <input type="hidden" name="a3" value="<?php echo get_field('price'); ?>">
                                                <input type="hidden" name="p3" value="<?php echo $p3; //p3?>">
                                                <input type="hidden" name="t3" value="D">
                                                <input type="hidden" name="src" value="1">
                                                <input type="hidden" name="custom" value="<?php echo $user;?>">
                                                <input type="submit" name="stripe_payment" id="stripe_payment" value="Subscribe"  class="btn subcribe-btn" />
                                                <input type="image" src="<?php bloginfo('template_directory'); ?>/images/payment-logo.jpg" name="paypal_payment" value="Subscribe With PayPal"/>
                                            </form>
                                        </div>															
                                    </div>
                                </div>
                            </section>
                        </div>
                    <?php endwhile;wp_reset_query(); ?>
                    <?php endif; ?>
                </div>
            </div>
        </article>
    </section>
</section>
<?php get_footer(); ?>

