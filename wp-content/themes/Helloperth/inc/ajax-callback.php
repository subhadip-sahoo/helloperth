<?php

add_action('wp_ajax_directories', 'directories');
add_action('wp_ajax_nopriv_directories', 'directories');
add_action('wp_ajax_getDraftDir_callback', 'getDraftDir_callback');
add_action('wp_ajax_txn_invoice_callback', 'txn_invoice_callback');
add_action('wp_ajax_check_captcha', 'check_captcha');
add_action('wp_ajax_nopriv_check_captcha', 'check_captcha');
add_action('wp_ajax_checkPromotionCount_callback', 'checkPromotionCount_callback');
add_action('wp_ajax_get_page_content', 'get_page_content');
add_action('wp_ajax_nopriv_get_page_content', 'get_page_content');
//add_action('wp_ajax_quick_contact', 'quick_contact');
//add_action('wp_ajax_nopriv_quick_contact', 'quick_contact');

function directories(){
    global $wp_query, $authordata;
    $directories = '';
    if(isset($_POST) && isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
        $item_per_page = 1;
	if(isset($_POST['page'])){
            $page_number = filter_var($_POST['page'], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH);
            if(!is_numeric($page_number)){die('Invalid page number!');}
	}else{
            $page_number = 1;
	}
        $offset = (($page_number-1) * $item_per_page);
        $args = $_POST['args'];
        $args['posts_per_page'] = $item_per_page;
        $args['offset'] = $offset;
	
        query_posts($args);
        
	$get_total_rows = $wp_query->found_posts;
	$total_pages = ceil($get_total_rows/$item_per_page);
        
        if(have_posts()){
            $directories .= '<div class="perth-tourist-map-lists clearfix">';
            $directories .= '<ul class="grid-row">';
            while(have_posts()){
                the_post();
                $desktop_image = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'list-desktop');
                $tablet_image = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'list-tablet');
                $mobile_image = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'list-mobile');
                $popup_image = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'list-popup');
                
                $term_list = wp_get_post_terms(get_the_ID(), 'directories-cat', array('fields' => 'all'));
                $terms = array();
                foreach ($term_list as $term) {
                    $terms[] = $term->name;
                }
                
                $location = get_post_meta(get_the_ID(), 'geo_location', true);
                $website = get_post_meta(get_the_ID(), 'website', true);
                
                $author_contact = get_user_meta($authordata->ID, 'contact_number', true );
                
                $authordata = get_userdata($authordata->ID);
                
                $directories .= '<li class="grid-row-4">';
                $directories .= '<div class="perth-slider-box">';
                $directories .= '<a href="#directory-popup'.  get_the_ID() .'">';
                $directories .= '<div class="perth-tourist-lists-image">';
                $directories .= '<figure class="parth-slider-image">';
                $directories .= '<img src="'.$desktop_image[0].'" width="274" height="204" alt="Image" class="hide-tablet">';
                $directories .= '<img src="'.$tablet_image[0].'" alt="Images" class="show-tablet" width="1024" height="289">';
                $directories .= '<img src="'.$mobile_image[0].'" alt="Images" class="hide-desktop-tablet" width="767" height="336">';
                $directories .= '</figure>';
                $directories .= '<h3 class="parth-slider-title">'.get_the_title().'<i class="icon icon-arrow"></i></h3>';
                $directories .= '</div>';
                $directories .= '</a>';
                $directories .= '</div>';
                $directories .= '<div id="directory-popup'.  get_the_ID() .'" class="zoom-anim-dialog mfp-hide perth-popup-container">';
                $directories .= '<div class="perth-popup-container-inner clearfix">';
                $directories .= '<h2 class="perth-popup-title">'.  get_the_title() .'</h2>';
                $directories .= '<div class="perth-popup-left-container">';
                $directories .= '<figure class="perth-popup-image">';
                $directories .= '<img src="'.$popup_image[0].'" alt="Images" width="429" height="290">';
                $directories .= '</figure>';
                $directories .= '</div>';
                $directories .= '<div class="perth-popup-right-container">';
                $directories .= '<ul class="perth-popup-list-address">';
                $directories .= '<li class="perth-popup-list"><i class="icon perth-popup-list-icon"></i> '.implode(', ', $terms).'</li>';
                $directories .= '<li class="perth-popup-list"><i class="icon perth-popup-list-icon1"></i> '.$location.'</li>';
                $directories .= '<li class="perth-popup-list"><i class="icon perth-popup-list-icon2"></i> '.$author_contact.'</li>';
                $directories .= '<li class="perth-popup-list"><i class="icon perth-popup-list-icon3"></i>';
                $directories .= '<a href="'.$website.'" target="_blank">'.$website.'</a>';
                $directories .= '</li>';
                $directories .= '</ul>';
                $directories .= '<p>'.  get_the_excerpt(get_the_ID()) .'</p>';
                $directories .= '<a href="'.  get_the_permalink(get_the_ID()) .'" class="btn btn-viewmore">View More</a>';
                if(check_valid_user_post(get_the_ID(), $authordata->user_activation_key)){
                    $directories .= '&nbsp;&nbsp;<a href="'.  href(EDIT_DIRECTORY_PAGE) .'/'.$authordata->user_activation_key.'/'.get_the_ID().'" class="btn btn-viewmore">Edit this directory</a>';
                }
                $directories .= '</div>';
                $directories .= '</div>';
                $directories .= '</div>';
                $directories .= '</li>';
            }
            wp_reset_query();
            $directories .= '</ul>';
            $directories .= '</div>';
            $directories .= '<div align="center">';
            $directories .= paginate_function($item_per_page, $page_number, $get_total_rows, $total_pages);;
            $directories .= '</div>';
        }
    }
    echo $directories;
    exit();
}

function getDraftDir_callback(){
    echo json_encode(array('count' => wp_count_posts('directories')->pending));
    die();
}

function check_captcha(){
    session_start();
    $security_code = $_REQUEST['code'];
    if( $_SESSION['security_code'] == $security_code && !empty($_SESSION['security_code'] ) ) {
        echo 'verified';
    }else{
        echo 'Security code does not match';
    }
    exit();
}

function txn_invoice_callback(){
    global $wpdb, $user_ID;
    $id = $_REQUEST['id'];
    $stripe_cus_id = get_user_meta($user_ID, 'stripe_cus_id', true);
    $userdata = get_userdata($user_ID);
    $cus_name = $userdata->display_name;
    $HTML = '';
    $query = "SELECT * FROM `{$wpdb->prefix}user_payment_stripe` WHERE `id` = {$id}";
    $result = $wpdb->get_results($query, ARRAY_A);
    foreach($result as $res){
        $stripe_invoice_id = $res['invoice_id'];
        $stripe_transaction_id = $res['transaction_id'];
        $stripe_payment_date = $res['payment_date'];
        $plan_OBJ = get_post($res['id_package']);
        $plan = $plan_OBJ->post_title;
        $interval = get_post_meta($res['id_package'], 'interval_count', TRUE).' '.get_post_meta($res['id_package'], 'interval', TRUE);
        $currency = 'AUD';
        $status = $res['subscription_status'];
        $subscription_date = strtotime($res['subscription_date']);
    }
    if($invoice = get_stripe_invoice($stripe_invoice_id)){
        $subcription_id = $invoice->lines->data[0]->id;
        $period_start = $invoice->lines->data[0]->period->start;
        $period_end = $invoice->lines->data[0]->period->end;
        $subtotal = ($invoice->subtotal / 100);
        $total = ($invoice->total / 100);
        if($subscription = get_stripe_subscription($stripe_cus_id, $subcription_id)){
            $plan = $subscription->plan->name;
            $plan_id = $subscription->plan->id;
            $interval = $subscription->plan->interval_count.' '.$subscription->plan->interval;
            $amount = ($subscription->plan->amount / 100).' '.strtoupper($subscription->plan->currency);
            $currency = strtoupper($subscription->plan->currency);
            $status = strtoupper(strtolower($subscription->status));
            $actual_payment = ($subscription->plan->amount / 100);
            $subscription_date = $subscription->start;
        }
    }
    $HTML .= '<table>';
    $HTML .= '<tbody>';
    $HTML .= '<tr>';
    $HTML .= '<td colspan="3" align="center"><h2>Subscription details</h2></td>';
    $HTML .= '</tr>';
    $HTML .= '<tr>';
    //$HTML .= '<td></td>';
    $HTML .= '<td>Plan:</td>';
    $HTML .= '<td>'.$plan.'</td>';
    $HTML .= '</tr>';
    $HTML .= '<tr>';
    //$HTML .= '<td></td>';
    $HTML .= '<td>Period:</td>';
    $HTML .= '<td>'.$interval.'</td>';
    $HTML .= '</tr>';
    $HTML .= '<tr>';
    //$HTML .= '<td></td>';
    $HTML .= '<td>Subscriber Name:</td>';
    $HTML .= '<td>'.$cus_name.'</td>';
    $HTML .= '</tr>';
    $HTML .= '<tr>';
    //$HTML .= '<td></td>';
    $HTML .= '<td>Subscriber ID:</td>';
    $HTML .= '<td>'.$stripe_cus_id.'</td>';
    $HTML .= '</tr>';
    $HTML .= '<tr>';
    //$HTML .= '<td></td>';
    $HTML .= '<td>Subscription ID:</td>';
    $HTML .= '<td>'.$subcription_id.'</td>';
    //$HTML .= '<td></td>';
    $HTML .= '</tr>';
    $HTML .= '<tr>';
    //$HTML .= '<td></td>';
    $HTML .= '<td>Subscription Date:</td>';
    $HTML .= '<td>'.date(DATE_DISPLAY_FORMAT, $subscription_date).'</td>';
   // $HTML .= '<td></td>';
    $HTML .= '</tr>';
    $HTML .= '<tr>';
    //$HTML .= '<td></td>';
    $HTML .= '<td>Subscription Status:</td>';
    $HTML .= '<td>'.$status.'</td>';
    //$HTML .= '<td></td>';
    $HTML .= '</tr>';
    $HTML .= '<tr>';
    //$HTML .= '<td><hr/></td>';
    $HTML .= '<td>Tramsaction ID:</td>';
    $HTML .= '<td>'.$stripe_transaction_id.'</td>';
    //$HTML .= '<td></td>';
    $HTML .= '</tr>';
    $HTML .= '<tr>';
    //$HTML .= '<td></td>';
    $HTML .= '<td>Tramsaction Date:</td>';
    $HTML .= '<td>'.date(DATETIME_DISPLAY_FORMAT, strtotime($stripe_payment_date)).'</td>';
    //$HTML .= '<td></td>';
    $HTML .= '</tr>';
    $HTML .= '<tr>';
    //$HTML .= '<td><hr/></td>';
    $HTML .= '<td>Total:</td>';
    $HTML .= '<td>'.$total.' '.$currency.'</td>';
    //$HTML .= '<td></td>';
    $HTML .= '</tr>';
    $HTML .= '<tr>';
    //$HTML .= '<td></td>';
    $HTML .= '<td>Subtotal:</td>';
    $HTML .= '<td>'.$subtotal.' '.$currency.'</td>';
   // $HTML .= '<td></td>';
    $HTML .= '</tr>';
    $HTML .= '</tbody>';
    $HTML .= '</table>';
    echo $HTML;
    die();
}

function quick_contact(){
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
                $suc_msg = 'Your message has been successfully submitted.';
            }else{
                $err_msg = 'Error occured! Please try again later.';
            }
        }
    }
    if(!empty($err_msg)){
        echo json_encode(array('status' => 'error', 'message' => $err_msg));
    }
    if(!empty($suc_msg)){
        echo json_encode(array('status' => 'success', 'message' => $suc_msg));
    }
    exit();
}

function checkPromotionCount_callback(){
    $dir_count = count_promote_directroies($_REQUEST['meta_key']);
    $set_max_limit = 10; // set the limit of directories want to display
    $dir_count_include_current = $dir_count + 1;
    echo ($dir_count_include_current > $set_max_limit ) ? 1 : 0;
    exit();
}

function get_page_content(){
    $postid = $_REQUEST['postid'];
    $post = get_post($postid); 
    $content = apply_filters('the_content', $post->post_content); 
    echo $content;
    exit();
}