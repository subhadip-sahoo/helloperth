<?php
/* Template Name: Make Payment */
session_start();
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
            <?php if(isset($_SESSION['session_msg']) && !empty($_SESSION['session_msg'])): echo message_alert($_SESSION['session_msg'], 2); endif; unset($_SESSION['session_msg']);?>
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
                        <div class="grid-row-4"> 
                            <section class="payment-table-block text-center">
                                <h2>Subscription</h2>
                                <h3><?php the_title(); ?></h3>
                                <div class="payment-table-price-area">
                                    <p class="price"><i>$</i><strong><?php echo get_post_meta(get_the_ID(), 'package_price', true); ?></strong></p>
                                </div>
                                <div class="payment-table-month-area"><?php echo get_post_meta(get_the_ID(), 'interval_count', true).' '.ucwords(get_post_meta(get_the_ID(), 'interval', TRUE)); ?></div>
                                <?php $package_features = get_field('package_features'); ?>
                                <?php if(is_array($package_features) && !empty($package_features)) : ?>
                                <div class="payment-table-lists-description-area">
                                    <ul>
                                        <?php foreach ($package_features as $feature) : ?>
                                        <li><?php echo $feature['features']; ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                                <?php endif; ?>
                                <div class="payment-table-button-area">
                                    <div class="lazy-load-box effect-zoomin">
                                        <div id="accept_paypal_payment_form">
                                            <form action="<?php echo href(CHECKOUT_PAGE).'/'.$key.'/'.$user;?>" method="POST" name="packages">
                                                <input type="hidden" name="item" value="<?php echo get_the_ID(); ?>">
                                                <input type="hidden" name="currency" value="aud">
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

