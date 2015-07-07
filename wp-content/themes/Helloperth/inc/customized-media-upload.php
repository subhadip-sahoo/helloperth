<?php
add_action('wp_enqueue_scripts', 'helloperth_media_upload_scripts');
add_action('admin_enqueue_scripts', 'helloperth_media_upload_scripts');
 
function helloperth_media_upload_scripts() {
    wp_enqueue_media();
    wp_register_script('custom-media-upload-js', get_template_directory_uri().'/js/custom-media-upload.js', array('jquery'), true);
    wp_enqueue_script('custom-media-upload-js');
    $template = array(
        'uri' => get_template_directory_uri()
    );
    wp_localize_script( 'custom-media-upload-js', 'template', $template );
}

add_filter( 'ajax_query_attachments_args', 'show_current_user_attachments' );

function show_current_user_attachments( $query ) {
    global $user_ID;
    $userdata = get_userdata($user_ID);
    if(isset($userdata->roles) && is_array($userdata->roles)){
        if(in_array('subscriber', $userdata->roles) || in_array('siteuser', $userdata->roles) || in_array('advertiser', $userdata->roles)){
            $query['author'] = $user_ID;
        }
    }
    return $query;
}

add_filter('wp_handle_upload_prefilter','media_upload_prefilter');

function media_upload_prefilter($file){
    $filetype = wp_check_filetype($file['tmp_name']);
    $check_types = array('jpg', 'jpeg', 'jpe', 'gif', 'png');
    if(!in_array($filetype['ext'], $check_types)){
        return $file;
    }
    
    $img = getimagesize($file['tmp_name']);
    
    $width = $img[0];
    $height = $img[1];
    
    if (($width / $height) > 3.5){
        return array('error' => 'Invalid image. Width is too large than height. Please try another image.');
    }else if(($height / $width) > 3.5){
        return array('error' => 'Invalid image. Height is too large than width. Please try another image.');
    }else{
        return $file; 
    }
}

add_filter('upload_mimes', 'restrict_mime');

function restrict_mime($mimes) {
    global $user_ID;
    if($user_ID){
        $userdata = get_userdata($user_ID);
        if(!in_array('administrator', $userdata->roles)){
            $mimes = array(
                'jpg|jpeg|jpe' => 'image/jpeg',
                'gif' => 'image/gif',
                'png' => 'image/png'
            );
            return $mimes;
        }
        return $mimes;
    }
    return $mimes;
}