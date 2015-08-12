<?php
require get_template_directory() . '/inc/wp_user_extends.class.php';

function helloperth_users(){
//    remove_role('siteuser');
//    remove_role('advertiser');
//    add_role('siteuser', 'Site User', 
//            array(
//                'read'=>true, 
//                'edit_posts'=>false, 
//                'upload_files'=>true, 
//                'delete_posts'=>false, 
//                'remove_users'=>false, 
//                'edit_users'=>false 
//            ));
//    remove_role('subscriber');
//    remove_role('contributor');
//    remove_role('author');
//    remove_role('editor');
    add_role('advertiser', 'Advertiser', 
            array(
                'read'=>true, 
                'edit_posts'=>true, 
                'edit_others_posts'=>true, 
                'edit_pages'=>true, 
                'edit_others_pages'=>true, 
                'edit_published_pages'=>true, 
                'upload_files'=>true, 
                'edit_files'=>false, 
                'delete_posts'=>true, 
                'remove_users'=>false, 
                'edit_users'=>false
            ));
}
add_action("init", "helloperth_users");

add_filter('pre_option_default_role', function($default_role){
    $default_role = 'advertiser'; 
    return $default_role; 
});

function hp_users_avatar(){
    global $wpdb;
    $users = $wpdb->get_results("SELECT * FROM {$wpdb->users}");
    foreach($users as $user){ 
        get_custom_avatar( $avatar, $user->ID, $size, $default, $alt);
        add_filter( 'get_avatar' , 'get_custom_avatar' , 1 , 5 );
    }
}
add_action('admin_init', 'hp_users_avatar');

function get_custom_avatar( $avatar, $id_or_email, $size, $default, $alt ) {    
    $avatar_new = get_user_meta($id_or_email, 'profile_pic', true);
    if(!is_numeric($avatar_new ))
        return $avatar;
    $profile_avatar = wp_get_attachment_image_src($avatar_new, 'full');
    $avatar_new = "<img alt='{$alt}' src='{$profile_avatar[0]}' class='avatar avatar-{$size} photo' height='{$size}' width='{$size}' />";
    return $avatar_new;
}