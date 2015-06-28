<?php
/* Template Name: Subscription Complete */
session_start();
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
                <div class="subscription-details">
                    <p>Subscription has been successfully completed.</p>
                    <dl>
                        <dt><label>Plan:</label></dt>
                        <dd><?php echo ucwords($_SESSION['subscription']['name']);?></dd>
                        <dt><label>Amount:</label></dt>
                        <dd><?php echo ($_SESSION['subscription']['amount'] / 100);?>&nbsp;<?php echo strtoupper($_SESSION['subscription']['currency']);?></dd>
                        <dt><label>Interval:</label></dt>
                        <dd><?php echo $_SESSION['subscription']['interval_count'];?>&nbsp;<?php echo $_SESSION['subscription']['interval'];?></dd>
                        <dt><label>Subscription ID:</label></dt>
                        <dd><?php echo $_SESSION['subscription']['subscription_id'];?></dd>
                        <dt><label>Customer ID:</label></dt>
                        <dd><?php echo $_SESSION['subscription']['customer'];?></dd>
                        <dt><label>Status:</label></dt>
                        <dd><?php echo strtoupper(strtolower($_SESSION['subscription']['status']));?></dd>
                        <dt><label>Paid for the period:</label></dt>
                        <dd><?php echo date(DATE_DISPLAY_FORMAT,$_SESSION['subscription']['current_period_start']);?>&nbsp;-&nbsp;<?php echo date(DATE_DISPLAY_FORMAT,$_SESSION['subscription']['current_period_end']);?></dd>
                    </dl>
                    <p class="subscription-details"><strong>Note:</strong> Please make sure that you have note down the <strong>Subscription ID</strong> for future communication. You can also get this from your profile.</p>
                </div>
            </div>
        </article>
    </section>
</section>
<?php get_footer(); ?>


