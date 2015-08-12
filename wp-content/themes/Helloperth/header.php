<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <link rel="shortcut icon" href="<?php echo get_template_directory_uri();?>/favicon.ico" type="image/x-icon">
        <link rel="icon" href="<?php echo get_template_directory_uri();?>/favicon.ico" type="image/x-icon">
        
        <link href='http://fonts.googleapis.com/css?family=Roboto:500,900italic,900,400italic,100,700italic,300,700,500italic,100italic,300italic,400' rel='stylesheet' type='text/css'>
        <link rel="stylesheet" href="<?php echo get_template_directory_uri();?>/css/normalize.min.css">
        <link rel="stylesheet" href="<?php echo get_template_directory_uri();?>/css/flexslider.css" media="all" />
        <link rel="stylesheet" href="<?php echo get_template_directory_uri();?>/css/jquery.bxslider.css" />
        <link rel="stylesheet" href="<?php echo get_template_directory_uri();?>/css/meanmenu.css" media="all" />
        <link rel="stylesheet" href="<?php echo get_template_directory_uri();?>/css/owl.carousel.css">
        <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
        <link rel="stylesheet" href="<?php echo get_template_directory_uri();?>/css/magnific-popup.css">
        <link rel="stylesheet" href="<?php echo get_template_directory_uri();?>/jQueryUI/css/jquery-ui.css">
        <link rel="stylesheet" href="<?php echo get_template_directory_uri();?>/css/psm.css">
        <link rel="stylesheet" href="<?php echo get_template_directory_uri();?>/css/validationEngine.jquery.css">
        <link rel="stylesheet" href="<?php echo get_template_directory_uri();?>/css/jquery.mCustomScrollbar.css" />
        <link rel="stylesheet" href="<?php echo get_template_directory_uri();?>/css/main.css">

        <script src="<?php echo get_template_directory_uri();?>/js/vendor/modernizr-2.6.2-respond-1.1.0.min.js"></script>
        <script src="<?php echo get_template_directory_uri();?>/js/clock.js"></script>
        <?php wp_head(); ?>
        <script src="<?php echo get_template_directory_uri();?>/jQueryUI/js/jquery-ui.js"></script>
        <script src="<?php echo get_template_directory_uri();?>/js/jquery.simpleWeather.min.js"></script>
        <script src="<?php echo get_template_directory_uri();?>/js/weather-scripts.js"></script>
        <script type="text/javascript">!function(e){e(function(){e("#tabs").tabs()})}(jQuery);</script>
    </head>
    <body <?php body_class();?>>
        <header class="header-container clearfix">
            <section class="wrapper clearfix">
                <div class="inner-header-container clearfix">
                    <div class="logo-area">
                        <h1 class="site-logo">
                            <a href="<?php echo home_url();?>">
                                <img src="<?php header_image();?>" alt="Helloperth">
                            </a>
                        </h1>
                    </div>
                    <aside class="header-right-area">
                        <div class="header-right-top-area">
                            <h2 class="site-caption hide-tablet"><?php echo get_option('blogdescription');?></h2>
                            <div class="header-weather-time-area">
                                <div class="header-weather-area" id="weather">LOADING...</div>
                                <div class="header-time-area hide-tablet">
                                    <div class="current-time-area"> <i class="icon-time"><?php if(is_active_sidebar('sidebar-2')){ dynamic_sidebar('sidebar-2'); }?></i><span class="current-timing" id="showTime">Loading...</span><span class="pm-am" id="showAMPM"></span></div>
                                </div>
                            </div>
                            <?php if(is_user_logged_in()): ?>
                            <?php global $user_ID; ?>
                            <?php $userdetails = get_userdata($user_ID); ?>
                            <?php $avatar = wp_get_attachment_image_src($userdetails->profile_pic, 'full'); ?>
                            <div class="header-login-area">
                                <a href="<?php echo href(PROFILE_PAGE);?>" class="head-login" data-target="#" data-toggle="dropdown" aria-haspopup="true" role="button" aria-expanded="false">
                                    <?php //echo $userdetails->first_name; ?> 
                                    <!--<span style="text-transform: none;">Hi, <?php //echo ucfirst($userdetails->first_name); ?></span>?-->
                                    <span style="text-transform: none;">User Menu</span>
                                    <i class="icon icon-login"></i>
                                </a>
                                <div class="dropdown-menu" role="menu" aria-labelledby="dLabel">
                                    <div class="profile-dropdown-img">
                                        <a href="<?php echo href(PROFILE_PAGE);?>"><?php echo (!empty($userdetails->profile_pic)) ? '<img src="'.$avatar[0].'" width="92" height="92">' : get_avatar($user_ID, 92, get_option('avatar_default'), $userdetails->display_name); ?></a>
                                    </div>
                                    <ul class="dropdown-menu-ul">
                                        <li><a href="<?php echo href(PROFILE_PAGE);?>">Your Account</a></li>
                                        <li><a href="<?php echo href(ADD_DIRECTORY_PAGE); ?>">Add Listing</a></li>
                                        <li><a href="<?php echo href(ADVANCED_SEARCH_PAGE); ?>">Search directory</a></li>
                                        <li><a href="<?php echo href(MAKE_PAYMENT_PAGE).'/'.$userdetails->user_activation_key.'/'.$user_ID; ?>">Plans</a></li>
                                        <li><a href="<?php echo href(NEWS_PAGE); ?>">News</a></li>
                                        <li><a href="<?php echo href(USER_GUIDE); ?>">User Guide</a></li>
                                        <li><a href="<?php echo wp_logout_url(href(LOGIN_PAGE));?>">Logout</a></li>
                                    </ul>
                                </div>
                            </div>
                            <?php else: ?>
                            <div class="header-login-area"><a href="<?php echo href(LOGIN_PAGE);?>" class="head-login">Login <i class="icon-login"></i></a></div>
                            <?php endif; ?>
                        </div>
                        <div class="header-nav-area">
                            <nav class="heade-menu">
                                <?php
                                    $args_header = array(
                                            'theme_location'  => 'primary',
                                            'menu'            => '',
                                            'container'       => '',
                                            'container_class' => '',
                                            'container_id'    => '',
                                            'menu_class'      => 'menu',
                                            'menu_id'         => '',
                                            'echo'            => true,
                                            'fallback_cb'     => 'wp_page_menu',
                                            'before'          => '',
                                            'after'           => '',
                                            'link_before'     => '',
                                            'link_after'      => '',
                                            'items_wrap'      => '<ul>%3$s</ul>',
                                            'depth'           => 0,
                                            'walker'          => ''
                                    );

                                    wp_nav_menu( $args_header );

                                ?>
                            </nav>
                        </div>
                    </aside>
                </div>
            </section>
        </header>
        <h2 class="site-caption hide-desktop"><?php echo get_option('blogdescription');?></h2>