<?php
function helloperth_new_user_reg($user_id){
    global $wpdb;
    $key = wp_generate_password( 20, false );
    do_action( 'retrieve_password_key', $user_id, $key );
    $wpdb->update( $wpdb->users, array( 'user_activation_key' => $key ), array( 'ID' => $user_id ) );
    $userdata = get_userdata($user_id);
    $metadata = array(
        'system_id' => $user_id,
        'system_name' => $userdata->display_name,
        'system_username' => $userdata->user_login,
    );
    if($response = create_stripe_customer($userdata->user_email, $metadata)){
        update_user_meta($user_id, 'stripe_cus_id', $response);
    }
}
add_action('user_register', 'helloperth_new_user_reg', 10, 1);

function helloperth_login_auth($user, $password){
    $errors = new WP_Error();
    $userdata  = get_userdata( $user->ID );
    $disabled = get_user_meta( $user->ID, 'ja_disable_user', true );
    if(implode(', ', $userdata->roles) != 'administrator'){
        if(strtotime(get_user_meta($user->ID, 'account_expiry', TRUE)) < strtotime(date('Y-m-d'))){
            $errors->add('account_expired',  __('Your account has been expired. Please make your payment <a href="'.href(MAKE_PAYMENT_PAGE).'/'.$user->user_activation_key.'/'.$user->ID.'">here</a>.'));
            return $errors;
        }else if(get_user_meta( $user->ID, 'ja_disable_user', true ) == 1){
            wp_clear_auth_cookie();
            $errors->add('disabled_account', __('Your account has been disabled. Please contact to administrator.'));
            return $errors;
        }else if(get_user_meta($user->ID, 'account_status', true) == 0){
            $errors->add('verification_failed', __('Your account is not activated yet. Please make your payment <a href="'.href(MAKE_PAYMENT_PAGE).'/'.$user->user_activation_key.'/'.$user->ID.'">here</a>.'));
            $errors->add('user_email', $user->user_email);
            $errors->add('display_name', $user->display_name);
            $errors->add('ID', $user->ID);
            $errors->add('user_activation_key', $user->user_activation_key);
            return $errors;
        }else {
            return $user;
        }
    }else{
        return $user;
    }
}
add_filter('wp_authenticate_user', 'helloperth_login_auth',10,2);

function redirect_to_after_login( $redirect_to, $request, $user ) {
    if ( isset($user->roles) && is_array( $user->roles ) ) {
      if ( in_array( 'subscriber', $user->roles ) || in_array( 'siteuser', $user->roles ) || in_array( 'advertiser', $user->roles )) {
          return site_url();
      } 
      if ( in_array( 'administrator', $user->roles ) ) {
          return admin_url();
      }
    }
    return $redirect_to;
}
add_filter( 'login_redirect', 'redirect_to_after_login', 10, 3 );
/*
function restrict_users_dashboard(){
    global $user_ID;
    $userdata = get_userdata($user_ID);
    if ( isset($userdata->roles) && is_array( $userdata->roles ) ) {
      if ( in_array( 'subscriber', $userdata->roles ) || in_array( 'siteuser', $userdata->roles ) || in_array( 'advertiser', $userdata->roles )) {
          wp_safe_redirect(site_url());
          exit();
      }
      if ( in_array( 'administrator', $userdata->roles ) ) {
          wp_safe_redirect(admin_url());
          exit();
      }
    }
}
add_action( 'admin_init', 'restrict_users_dashboard', 1 );
*/
function helloperth_social_login_redirect(){
    $redirect_to = site_url();
    return $redirect_to;
}
add_filter('wsl_hook_process_login_alter_redirect_to', 'helloperth_social_login_redirect');

function helloperth_social_account_status($is_new_user, $user_id, $provider, $adapter, $hybridauth_user_profile, $wp_user){
    update_user_meta($user_id,'account_status', 1);
}
add_action('wsl_process_login_update_wsl_user_data_start', 'helloperth_social_account_status', 10, 6);

function check_valid_user($ID, $user_activation_key){
    global $wpdb;
    $row = $wpdb->get_row("SELECT * FROM `{$wpdb->users}` WHERE `ID` = {$ID} AND `user_activation_key` = '{$user_activation_key}'");
    if($row <> NULL)
        return TRUE;
    return FALSE;
}

function check_valid_user_post($post_id, $act_key){
    global $wpdb, $user_ID;
    if(!check_valid_user($user_ID, $act_key)){
        return false;
    }
    $post = get_post($post_id);
    if($post->post_author == $user_ID){
        return true;
    }
    return false;
}

function vt_resize( $attach_id = null, $img_url = null, $width, $height, $crop = false ) {
    if ( $attach_id ) {
        $image_src = wp_get_attachment_image_src( $attach_id, 'full' );
        $file_path = get_attached_file( $attach_id );
    } else if ( $img_url ) {
        $file_path = parse_url( $img_url );
        $file_path = $_SERVER['DOCUMENT_ROOT'] . $file_path['path'];
        if(file_exists($file_path) === false){
            global $blog_id;
            $file_path = parse_url( $img_url );
            if (preg_match("/files/", $file_path['path'])) {
                $path = explode('/',$file_path['path']);
                foreach($path as $k=>$v){
                    if($v == 'files'){
                        $path[$k-1] = 'wp-content/blogs.dir/'.$blog_id;
                    }
                }
                $path = implode('/',$path);
            }
            $file_path = $_SERVER['DOCUMENT_ROOT'].$path;
        }
        $orig_size = getimagesize( $file_path );
        $image_src[0] = $img_url;
        $image_src[1] = $orig_size[0];
        $image_src[2] = $orig_size[1];
    }
    $file_info = pathinfo( $file_path );
    $base_file = $file_info['dirname'].'/'.$file_info['filename'].'.'.$file_info['extension'];
    if ( !file_exists($base_file) )
    return;
    $extension = '.'. $file_info['extension'];
    $no_ext_path = $file_info['dirname'].'/'.$file_info['filename'];
    $cropped_img_path = $no_ext_path.'-'.$width.'x'.$height.$extension;
    if ( $image_src[1] > $width ) {
        if ( file_exists( $cropped_img_path ) ) {
            $cropped_img_url = str_replace( basename( $image_src[0] ), basename( $cropped_img_path ), $image_src[0] );
            $vt_image = array (
                'url' => $cropped_img_url,
                'width' => $width,
                'height' => $height
            );
            return $vt_image;
        }
        if ( $crop == false OR !$height ) {
            $proportional_size = wp_constrain_dimensions( $image_src[1], $image_src[2], $width, $height );
            $resized_img_path = $no_ext_path.'-'.$proportional_size[0].'x'.$proportional_size[1].$extension;
            if ( file_exists( $resized_img_path ) ) {
                $resized_img_url = str_replace( basename( $image_src[0] ), basename( $resized_img_path ), $image_src[0] );
                $vt_image = array (
                'url' => $resized_img_url,
                'width' => $proportional_size[0],
                'height' => $proportional_size[1]
                );
                return $vt_image;
            }
        }
        $img_size = getimagesize( $file_path );
        if ( $img_size[0] <= $width ) $width = $img_size[0];
        if (!function_exists ('imagecreatetruecolor')) {
            echo 'GD Library Error: imagecreatetruecolor does not exist - please contact your webhost and ask them to install the GD library';
            return;
        }
        $new_img_path = image_resize( $file_path, $width, $height, $crop );	
        $new_img_size = getimagesize( $new_img_path );
        $new_img = str_replace( basename( $image_src[0] ), basename( $new_img_path ), $image_src[0] );
        $vt_image = array (
            'url' => $new_img,
            'width' => $new_img_size[0],
            'height' => $new_img_size[1]
        );
        return $vt_image;
    }
    $vt_image = array (
        'url' => $image_src[0],
        'width' => $width,
        'height' => $height
    );
    return $vt_image;
}
function custom_numeric_posts_nav() {
    if( is_singular() )
        return;
    global $wp_query;
    if( $wp_query->max_num_pages <= 1 )
        return;
    $paged = get_query_var( 'paged' ) ? absint( get_query_var( 'paged' ) ) : 1;
    $max   = intval( $wp_query->max_num_pages );
    if ( $paged >= 1 )
        $links[] = $paged;
    if ( $paged >= 3 ) {
        $links[] = $paged - 1;
        $links[] = $paged - 2;
    }
    if ( ( $paged + 2 ) <= $max ) {
        $links[] = $paged + 2;
        $links[] = $paged + 1;
    }
    echo '<div class="navigation"><ul>' . "\n";
    if ( get_previous_posts_link() )
        printf( '<li>%s</li>' . "\n", get_previous_posts_link() );
    if ( ! in_array( 1, $links ) ) {
        $class = 1 == $paged ? ' class="active"' : '';
        printf( '<li%s><a href="%s">%s</a></li>' . "\n", $class, esc_url( get_pagenum_link( 1 ) ), '1' );
        if ( ! in_array( 2, $links ) )
                echo '<li>…</li>';
    }
    sort( $links );
    foreach ( (array) $links as $link ) {
        $class = $paged == $link ? ' class="active"' : '';
        printf( '<li%s><a href="%s">%s</a></li>' . "\n", $class, esc_url( get_pagenum_link( $link ) ), $link );
    }
    if ( ! in_array( $max, $links ) ) {
        if ( ! in_array( $max - 1, $links ) )
                echo '<li>…</li>' . "\n";

        $class = $paged == $max ? ' class="active"' : '';
        printf( '<li%s><a href="%s">%s</a></li>' . "\n", $class, esc_url( get_pagenum_link( $max ) ), $max );
    }
    if ( get_next_posts_link() )
        printf( '<li>%s</li>' . "\n", get_next_posts_link() );
    echo '</ul></div>' . "\n";
}

function currentPageURL() {
    $curpageURL = 'http';
    if ($_SERVER["HTTPS"] == "on") {$curpageURL.= "s";}
    $curpageURL.= "://";
    if ($_SERVER["SERVER_PORT"] != "80") {
        $curpageURL.= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
    } else {
        $curpageURL.= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
    }
    return untrailingslashit($curpageURL);
}

function href($page_id){
    $permalink = get_permalink( $page_id );
    if($permalink)
        return untrailingslashit($permalink);
}

function aasort (&$array, $key) {
    $sorter=array();
    $ret=array();
    reset($array);
    foreach ($array as $ii => $va) {
        $sorter[$ii]=$va[$key];
    }
    asort($sorter);
    foreach ($sorter as $ii => $va) {
        $ret[$ii]=$array[$ii];
    }
    $array=$ret;
}
add_filter( 'auto_core_update_send_email', '__return_false' );
add_filter( 'automatic_updater_disabled', '__return_true' );
add_filter( 'auto_update_core', '__return_false' );
add_filter('show_admin_bar', '__return_false');

function generate_asterisk_value($value){
    if(empty($value))
        return;
    $length = strlen(trim($value));
    $asterik = '';
    for($i = 0; $i < $length; $i++){
        $asterik .= '*';
    }
    return $asterik;
}

function generate_math_captcha(){
    session_start();
    $num1 = rand(1, 6);
    $num2 = rand(5, 9);
    $answer = $num1 + $num2;
    $captcha = "What is $num1 + $num2?";
    $_SESISON['ver_code'] = $answer;
    return $captcha;
}

function parse_address_google($location, $zipcode = '') {
    $url = "https://maps.googleapis.com/maps/api/geocode/json?components=country:AU|postal_code:$zipcode&sensor=false&address=$location";
    if(empty($zipcode)){
        $url = "https://maps.googleapis.com/maps/api/geocode/json?components=country:AU&sensor=false&address=$location";
    }
    $results = json_decode(file_get_contents($url),1);
    return $results['results'][0];
}