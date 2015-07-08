<?php
    session_start();
    global $post;
    $err_msg = '';
    $war_msg = '';
    $suc_msg = '';
    if(isset($_POST['quick_contact'])){
        if(empty($_POST['cus_name'])){
            $err_msg = 'Name is required.';
        }else if(empty($_POST['cus_email'])){
            $err_msg = 'Email address is required.';
        }else if(filter_var($_POST['cus_email'], FILTER_VALIDATE_EMAIL) === FALSE){
            $err_msg = 'Please enter a valid email address.';
        }else if(empty($_POST['cus_message'])){
            $err_msg = 'Message is required.';
        }
        
        if($err_msg == ''){
            if(isset($_SESSION['ver_code']) && $_SESSION['ver_code'] == $_POST['ver_code']){
                $to = get_the_author_meta('user_email', $post->post_author);
                $display_name = get_the_author_meta('display_name', $post->post_author);
                $from = esc_attr($_POST['cus_email']);
                $from_name = esc_attr($_POST['cus_name']);
                $message = $_POST['cus_message'];
                $directory_title = get_the_title($post->ID);
                $blogname = get_option('blogname');
                $headers = "From: $from_name <$from>\r\n";
                $headers .= "Reply-To: $display_name <$to>\r\n";
                $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
                $subject = "{$blogname} :: {$from_name} has made an enquiry from your {$directory_title} directory";
                $msg = "Dear $display_name,<br/><br/>";
                $msg .= "Someone has made an enquiry into your {$directory_title} directory.<br/> ";
                $msg .= "Please find the details below.<br/><br/>";
                $msg .= "Name: {$from_name} <br/>";
                $msg .= "Email Address: {$from} <br/>";
                $msg .= "Message: {$message} <br/><br/>";

                if(wp_mail( $to, $subject, $msg, $headers )){
                    unset($_POST);
                    $suc_msg = 'Your message has been successfully submitted.';
                }else{
                    $err_msg = 'Error occured! Please try again later.';
                }
            }else{
                $err_msg = 'Invalid captcha!';
            }
        }
    }
    get_header(); 
    banner_directory_listing(); 
    if(have_posts()):
        while(have_posts()) :
            the_post();
        
?>

<?php echo banner_directory_listing(); ?>

<div class="main-container clearfix">
    <section class="main wrapper clearfix">
        <div class="single-container clearfix">
            <?php if(isset($_SESSION['breadcrumb'])): ?>
            <div class="breadcrumbs">
                <a href="<?php echo $_SESSION['breadcrumb']['term_link']; ?>"><?php echo $_SESSION['breadcrumb']['term']; ?></a> <span class="active"><?php the_title(); ?></span>
            </div>
            <?php endif; ?>
            <div class="single-content clearfix">
                <div class="single-content-area clearfix">
                    <div class="grid-row">
                        <div class="grid-row-2 garry_left">
                            <div class="single-grid-block">
                                <header class="site-heading">
                                    <h2><?php the_title(); ?></h2>
                                </header>
                                <div class="single-loaction">
                                    <div class="grid-row">
                                        <div class="grid-row-2">
                                            <h4 class="location-title-icon">Location</h4>
                                            <p class="map_box"><a target="_blank" href="https://www.google.com/maps/place/<?php echo urlencode(get_post_meta($post->ID, 'geo_location', TRUE)); ?>"><?php echo get_post_meta($post->ID, 'geo_location', TRUE); ?></a></p>
                                        </div>
                                        <div class="grid-row-2">
                                            <h4>Contact Info</h4>
                                            <p class="tel-number-icon"><?php echo get_post_meta($post->ID, 'phone', TRUE); ?></p>
                                            <p class="website-icon">
                                                <a href="mailto:<?php echo get_post_meta($post->ID, 'email_address', TRUE); ?>">
                                                    <?php echo get_post_meta($post->ID, 'email_address', TRUE); ?>
                                                </a>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <?php the_content(); ?>
                                </div>
                            </div>
                        </div>
                        <?php $get_all_images = get_post_meta($post->ID, '_easy_image_gallery', TRUE); ?>
                        <?php if(!empty($get_all_images)): ?>
                        <div class="grid-row-2 garry_right">
                            <div class="single-grid-block">
                                <div class="single-slider-area">
                                    <div id="single-slider" class="single-slider-flex flexslider">
                                        <ul class="slides">
                                            <?php 
                                                $ids = explode(',', $get_all_images);
                                                foreach ($ids as $id) :
                                                    $image = wp_get_attachment_image_src($id, 'gallery-big');
                                                    if(!empty($image[0])) :
                                            ?>
                                            <li>
                                                <img src="<?php echo $image[0]; ?>" alt=""/>
                                            </li>
                                            <?php endif; ?>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                    <div id="single-carousel" class="single-carousel-flex flexslider">
                                        <ul class="slides">
                                            <?php 
                                                foreach ($ids as $id) :
                                                    $image = wp_get_attachment_image_src($id, 'gallery-big');
                                                    if(!empty($image[0])) :
                                            ?>
                                            <li>
                                                <img src="<?php echo $image[0]; ?>" />
                                            </li>
                                            <?php endif; ?>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                    <svg version="1.1" xmlns="http://www.w3.org/2000/svg">    
                                        <filter id="greyscale">
                                         <feColorMatrix type="matrix" values="0.3333 0.3333 0.3333 0 0
                                                                              0.3333 0.3333 0.3333 0 0
                                                                              0.3333 0.3333 0.3333 0 0
                                                                              0      0      0      1 0"/>
                                        </filter>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="single-contact-map-area clearfix">
                    <div class="grid-row">
                        <div class="grid-row-2">
                            <div class="single-grid-block">
                                <div class="single-quick-con-area">
                                    <h4>Quick Contact</h4>
                                    <?php if(!empty($err_msg)): echo message_alert($err_msg, 4); endif;?>
                                    <?php if(!empty($war_msg)): echo message_alert($war_msg, 3); endif;?>
                                    <?php if(!empty($suc_msg)): echo message_alert($suc_msg, 2); endif;?>
                                    <form name="quick_contatc" id="quick_contatc" action="" method="POST">
                                        <p><input type="text" class="form-control validate[required]" placeholder="Name" name="cus_name" id="cus_name" value="<?php echo (isset($_POST['cus_name'])) ? $_POST['cus_name'] : ''; ?>"></p>
                                        <p><input type="email" class="form-control validate[required, custom[email]]" placeholder="Email Address" name="cus_email" id="cus_email" value="<?php echo (isset($_POST['cus_email'])) ? $_POST['cus_email'] : ''; ?>"></p>
                                        <p><textarea id="" cols="30" rows="5" class="form-control validate[required]" placeholder="Message" name="cus_message" id="cus_message"><?php echo (isset($_POST['cus_message'])) ? $_POST['cus_message'] : ''; ?></textarea></p>
                                        <p><?php echo generate_math_captcha(); ?><input type="text" name="ver_code" id="ver_code" class="form-control validate[required]"/></p>
                                        <p><input type="submit" class="btn btn-submit" value="Submit" name="quick_contact"></p>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="grid-row-2">
                            <div class="single-grid-block">
                                <div class="single-map-area">
                                    <div id="map-single" style="width: 627px; height: 410px;"></div>
                                    <a href="#larger_map" class="click-large-view-map">Click to view larger map <i class="icon icon-search"></i></a>
                                </div>
                            </div>
                            <div id="larger_map" class="zoom-anim-dialog mfp-hide perth-popup-container">
                                <div id="map-single" style="width: 800px; height: 400px;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<?php
        endwhile;
    endif;
?>
<!-- END -->
<?php  get_footer(); ?>