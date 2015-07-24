<?php
add_action( 'init', 'helloperth_post_types' );
function helloperth_post_types() {    
    register_post_type('directories',
            array(
                'public' => true,
                'label' => 'Directories',
                'labels'  => array(
                    'name' => __('Directories'),
                    'add_new'  => __('Add New Directory'),
                    'all_items'  => __('All Directories'),
                    'add_new_item'  => __('Add New Directory'),
                    'edit_item'  => __('Edit Directory')
                ),
                'rewrite' => array("slug" => "directories"),
                'supports' => array( 'title', 'editor', 'thumbnail', 'author'),
                'menu_position' => 8,
                'menu_icon' => get_template_directory_uri().'/images/directory.png',
                'taxonomies' => array('directories-cat', 'directories_tag')
            )
    );
    flush_rewrite_rules();
    register_taxonomy(
            'directories-cat',
            'directories',
            array(
                'label' => __( 'Directory Categories' ),
                'rewrite' => array( 'slug' => 'directory-categories' ),
                'hierarchical' => true,
                'show_admin_column' => true,
                'sort' => true,
            )
    );
    flush_rewrite_rules();   
    register_taxonomy('directories-tag', 'directories', array(
            'hierarchical' => false, 
            'label' => __("Directory Tags"), 
            'singular_name' => __("Directory Tag"), 
            'show_admin_column' => true,
            'sort' => true,
        )
    );
    flush_rewrite_rules();
        
    register_post_type('packages',
            array(
                'public' => true,
                'label' => 'Pricing & Packages',
                'labels'  => array(
                    'name' => __('Pricing & Packages'),
                    'add_new'  => __('Add New Package'),
                    'all_items'  => __('All Packages'),
                    'add_new_item'  => __('Add New Package'),
                ),
                'rewrite' => array("slug" => "packages"),
                'supports' => array( 'title', 'editor'),
                'menu_position' => 8,
                'menu_icon' => get_template_directory_uri().'/images/cost.gif'
            )
    );
    flush_rewrite_rules();
    
    register_post_type('events',
            array(
                'public' => true,
                'label' => 'Events',
                'labels'  => array(
                    'name' => __('Events'),
                    'add_new'  => __('Add New Event'),
                    'all_items'  => __('All Events'),
                    'add_new_item'  => __('Add New Event'),
                ),
                'rewrite' => array("slug" => "event"),
                'supports' => array( 'title', 'editor', 'thumbnail'),
                'menu_position' => 83,
                'menu_icon' => get_template_directory_uri().'/images/events.png'
            )
    );
    flush_rewrite_rules();
    
    register_post_type('news',
            array(
                'public' => true,
                'label' => 'News',
                'labels'  => array(
                    'name' => __('News'),
                    'add_new'  => __('Add New News'),
                    'all_items'  => __('All News'),
                    'add_new_item'  => __('Add New News'),
                ),
                'rewrite' => array("slug" => "news"),
                'supports' => array( 'title', 'editor', 'thumbnail'),
                'menu_position' => 83,
                'menu_icon' => get_template_directory_uri().'/images/news.png'
            )
    );
    flush_rewrite_rules();
    
    register_post_type('sliders',
            array(
                'public' => true,
                'label' => 'Sliders',
                'labels'  => array(
                    'name' => __('Sliders'),
                    'add_new'  => __('Add New Slider'),
                    'all_items'  => __('All Sliders'),
                    'add_new_item'  => __('Add New Slider'),
                ),
                'rewrite' => array("slug" => "slider"),
                'supports' => array( 'title', 'editor', 'thumbnail'),
                'menu_position' => 82,
                'menu_icon' => get_template_directory_uri().'/images/slider.png'
            )
    );
    flush_rewrite_rules();
    
    register_post_type('maps',
            array(
                'public' => true,
                'label' => 'Maps',
                'labels'  => array(
                    'name' => __('Maps'),
                    'add_new'  => __('Add New Map'),
                    'all_items'  => __('All Maps'),
                    'add_new_item'  => __('Add New Map'),
                ),
                'rewrite' => array("slug" => "map"),
                'supports' => array( 'title'),
                'menu_position' => 84,
                'menu_icon' => get_template_directory_uri().'/images/maps.png'
            )
    );
    flush_rewrite_rules();
}

foreach( array( 'directories' ) as $hook )
    add_filter( "views_edit-$hook", 'hp_modified_post' );

function hp_modified_post( $views ){
//    $views['all'] = str_replace( 'All ', 'Tutti ', $views['all'] );

    if( isset( $views['publish'] ) )
        $views['publish'] = str_replace( 'Published ', 'Approved ', $views['publish'] );

//    if( isset( $views['future'] ) )
//        $views['future'] = str_replace( 'Scheduled ', 'Future ', $views['future'] );
//
//    if( isset( $views['draft'] ) )
//        $views['draft'] = str_replace( 'Drafts ', 'In progress ', $views['draft'] );
//
//    if( isset( $views['trash'] ) )
//        $views['trash'] = str_replace( 'Trash ', 'Dustbin ', $views['trash'] );

    return $views;
}

add_filter( 'gettext', 'change_publish_button', 10, 2 );

function change_publish_button( $translation, $text ) {
    if(get_post_type() === 'directories'){
        if ( $text == 'Publish' )
        return 'Approve';
    }
    return $translation;
}

add_action( 'restrict_manage_posts', 'my_restrict_manage_posts' );
function my_restrict_manage_posts() {
    global $typenow;
    $taxonomy = 'directories-cat';
    if( $typenow == "directories" ){
        $filters = array($taxonomy);
        foreach ($filters as $tax_slug) {
            $tax_obj = get_taxonomy($tax_slug);
            $tax_name = $tax_obj->labels->name;
            $terms = get_terms($tax_slug, array('hide_empty' => false));
            echo "<select name='$tax_slug' id='$tax_slug' class='postform'>";
            echo "<option value=''>All $tax_name</option>";
            foreach ($terms as $term) { echo '<option value='. $term->slug, $_GET[$tax_slug] == $term->slug ? ' selected="selected"' : '','>' . $term->name. '</option>'; }
            echo "</select>";
        }
    }
}

function add_metaboxes() {
    add_meta_box('street_metabox', 'Directory Info', 'street_section', 'directories', 'normal', 'default');
    add_meta_box('promotion', 'Promote Directory to Home Page', 'promote_directory', 'directories', 'side', 'default');
    add_meta_box('promote_to_banner', 'Promote Directory to Banner Listing', 'promote_directory_banner', 'directories', 'side', 'default');
    add_meta_box('package_price', 'Package Price', 'package_price', 'packages', 'side', 'default');
    add_meta_box('period', 'Subscription Billing Period', 'subscription_billing_period', 'packages', 'side', 'default');
}
add_action( 'add_meta_boxes', 'add_metaboxes' );
function street_section() {
    global $post, $wpdb;
?>
    <input type="hidden" name="eventmeta_noncename" id="eventmeta_noncename" value="<?php echo  wp_create_nonce( plugin_basename(__FILE__) );?>" />
    <p>
        <label>Company Name: </label>
        <input type="text" name="company_name" id="company_name" value="<?php echo get_post_meta($post->ID, 'company_name', true) ?>" style="width: 100%; top: 0; left: 0; margin: 0 0 20px 0"/>
    </p>
    <p>
        <label>Contact Person: </label>
        <input type="text" name="contact_person" id="contact_person" value="<?php echo get_post_meta($post->ID, 'contact_person', true) ?>" style="width: 100%; top: 0; left: 0; margin: 0 0 20px 0"/>
    </p>
    <p>
        <label>Email Address: </label>
        <input type="email" name="email_address" id="email_address" value="<?php echo get_post_meta($post->ID, 'email_address', true) ?>" style="width: 100%; top: 0; left: 0; margin: 0 0 20px 0">
    </p>
    <p>
        <label>Phone: </label>
        <input type="text" name="phone" id="phone" value="<?php echo get_post_meta($post->ID, 'phone', true) ?>" style="width: 100%; top: 0; left: 0; margin: 0 0 20px 0"/>
    </p>
    <p>
        <label>Website Title: </label>
        <input type="text" name="website_title_dir" id="website_title_dir" value="<?php echo get_post_meta($post->ID, 'website_title_dir', true) ?>" style="width: 100%; top: 0; left: 0; margin: 0 0 20px 0">
    </p>
    <p>
        <label>Website: </label>
        <input type="url" name="website" id="website" value="<?php echo get_post_meta($post->ID, 'website', true) ?>" style="width: 100%; top: 0; left: 0; margin: 0 0 20px 0">
    </p>
    <div id="locationField">
        <label>Location: </label>
        <input id="autocomplete" autocomplete="off" name="geo_location" placeholder="Enter location" type="text" value="<?php echo get_post_meta($post->ID, 'geo_location', true); ?>">
        <input id="geo_latlng" name="geo_latlng" type="hidden" value="<?php echo get_post_meta($post->ID, 'geo_latlng', true); ?>">
        <input id="geo_name" name="geo_name" type="hidden" value="<?php echo get_post_meta($post->ID, 'geo_name', true); ?>">
        <input id="geo_address" name="geo_address" type="hidden" value="<?php echo get_post_meta($post->ID, 'geo_address', true); ?>">
    </div>
    <table id="address">
      <tr>
        <td class="label">Street address</td>
        <td class="slimField"><input class="field" id="street_number" placeholder="Street Number" name="geo_street_number" value="<?php echo get_post_meta($post->ID, 'geo_street_number', true); ?>"></td>
        <td class="wideField" colspan="2"><input class="field" id="route" placeholder="Route" name="geo_route" value="<?php echo get_post_meta($post->ID, 'geo_route', true); ?>"></td>
      </tr>
      <tr>
        <td class="label">City</td>
        <td class="wideField" colspan="3"><input class="field" id="locality" name="geo_city" value="<?php echo get_post_meta($post->ID, 'geo_city', true); ?>"></td>
      </tr>
      <tr>
        <td class="label">State</td>
        <td class="slimField"><input class="field" id="administrative_area_level_1" name="geo_state" value="<?php echo get_post_meta($post->ID, 'geo_state', true); ?>"></td>
        <td class="label">Zip code</td>
        <td class="wideField"><input class="field" id="postal_code" name="geo_zip_code" value="<?php echo get_post_meta($post->ID, 'geo_zip_code', true); ?>"></td>
      </tr>
      <tr>
        <td class="label">Country</td>
        <td class="wideField" colspan="3"><input class="field" name="geo_country" id="country" value="<?php echo get_post_meta($post->ID, 'geo_country', true); ?>"></td>
      </tr>
    </table>
    <div id="map-canvas" style="height: 300px;"></div>   
<?php
}

function add_admin_script_for_directories(){?>
    <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&signed_in=true&libraries=places"></script>
<?php
}
add_action('admin_head', 'add_admin_script_for_directories');

add_action('admin_footer', 'enquque_script_in_footer');
function enquque_script_in_footer(){?>
    <script src="<?php echo get_template_directory_uri();?>/js/google-map.js"></script>
<?php
}

function save_directories_meta($post_id, $post) {
    if ( !wp_verify_nonce( $_POST['eventmeta_noncename'], plugin_basename(__FILE__) )) {
        return $post->ID;
    }

    if ( !current_user_can( 'edit_post', $post->ID ))
        return $post->ID;
    
    $geo_name = esc_sql($_POST['geo_name']);
    $geo_address = esc_sql($_POST['geo_address']);
    $geo_latlng = str_replace(array('(', ')'), array('',''), esc_sql($_POST['geo_latlng']));

    if(empty($_POST['geo_latlng'])){
        $results = parse_address_google(esc_sql($_POST['geo_location']), esc_sql($_POST['geo_zip_code']));
        $geo_name = $results['address_components'][0]['long_name'];
        $geo_address = $geo_name.', '.$results['address_components'][1]['long_name'];
        $geo_latlng = $results['geometry']['location']['lat'].','.$results['geometry']['location']['lng'];
    }
    
    $directory_meta['company_name'] = esc_sql($_POST['company_name']);
    $directory_meta['contact_person'] = esc_sql($_POST['contact_person']);
    $directory_meta['email_address'] = esc_sql($_POST['email_address']);
    $directory_meta['phone'] = esc_sql($_POST['phone']);
    $directory_meta['website_title_dir'] = esc_sql($_POST['website_title_dir']);
    $directory_meta['website'] = esc_sql($_POST['website']);
    $directory_meta['geo_location'] = esc_sql($_POST['geo_location']);
    $directory_meta['geo_latlng'] = $geo_latlng;
    $directory_meta['geo_name'] = $geo_name;
    $directory_meta['geo_address'] = $geo_address;
    $directory_meta['geo_street_number'] = esc_sql($_POST['geo_street_number']);
    $directory_meta['geo_route'] = esc_sql($_POST['geo_route']);
    $directory_meta['geo_city'] = esc_sql($_POST['geo_city']);
    $directory_meta['geo_state'] = esc_sql($_POST['geo_state']);
    $directory_meta['geo_zip_code'] = esc_sql($_POST['geo_zip_code']);
    $directory_meta['geo_country'] = esc_sql($_POST['geo_country']);
    $directory_meta['promoted_to_home'] = (isset($_POST['promoted_to_home'])) ? esc_sql($_POST['promoted_to_home']) : 0;
    $directory_meta['promoted_to_banner'] = (isset($_POST['promoted_to_banner'])) ? esc_sql($_POST['promoted_to_banner']) : 0;

    foreach ($directory_meta as $key => $value) { 
        if( $post->post_type == 'revision' ) 
            return; 
        update_post_meta($post->ID, $key, $value);
    }
}
add_action('save_post', 'save_directories_meta', 1, 2);

function promote_directory(){
    global $post, $wpdb;
?>  
    <label for="promote_directory">
        <input type="checkbox" name="promoted_to_home" id="promoted_to_home" value="1" <?php echo (get_post_meta($post->ID, 'promoted_to_home', true) == 1) ? 'checked="checked"' : ''; ?>>&nbsp;Promote this directory to home page
    </label>
<?php }

function promote_directory_banner(){
    global $post, $wpdb;
?>  
    <label for="promote_directory_banner">
        <input type="checkbox" name="promoted_to_banner" id="promoted_to_banner" value="1" <?php echo (get_post_meta($post->ID, 'promoted_to_banner', true) == 1) ? 'checked="checked"' : ''; ?>>&nbsp;Promote this directory to banner listing
    </label>
<?php }

add_action('admin_enqueue_scripts', 'hp_admin_scripts');

function hp_admin_scripts(){
    wp_register_style('cutsom-admin-style', get_template_directory_uri().'/css/admin-style.css');
    wp_register_script('cutsom-admin-script', get_template_directory_uri().'/js/custom-admin-script.js', array('jquery'));
    wp_enqueue_style('cutsom-admin-style');
    wp_enqueue_script('cutsom-admin-script');
}

function get_meta_values( $key = '', $type = 'directories', $status = 'publish' ) {
    global $wpdb;
    $return = array();
    if( empty( $key ) )
        return;
    $r = $wpdb->get_col( $wpdb->prepare( "SELECT DISTINCT pm.meta_value FROM {$wpdb->postmeta} pm LEFT JOIN {$wpdb->posts} p ON p.ID = pm.post_id WHERE pm.meta_key = '%s' AND p.post_status = '%s' AND p.post_type = '%s'", $key, $status, $type ) );
    foreach ($r as $re) {
        if($re == '' || $re == NULL){
            continue;
        }
        $return[] = $re;
    }
    return $return;
}

function get_directory_tags(){
    $directories_tag = get_categories(array('post_type' => 'directories', 'post_status' => 'publish', 'taxonomy' => 'directories-tag', 'hide_empty' => 1));
    $tags = array();
    if(is_array($directories_tag) && !empty($directories_tag)){
        foreach ($directories_tag as $tag) {
            array_push($tags, array('id' => htmlspecialchars_decode($tag->term_id), 'label' => htmlspecialchars_decode($tag->name), 'value' => $tag->slug));
        }
    }
    return $tags;
}

function paginate_function($item_per_page, $current_page, $total_records, $total_pages){
    $pagination = '';
    if($total_pages > 0 && $total_pages != 1 && $current_page <= $total_pages){
        $pagination .= '<ul class="pagination">';
        
        $right_links    = $current_page + 3; 
        $previous       = $current_page - 1;
        $next           = $current_page + 1;
        $first_link     = true;
        
        if($current_page > 1){
            $previous_link = ($previous==0)?1:$previous;
            $pagination .= '<li class="first"><a href="#" data-page="1" title="First">&laquo;</a></li>'; 
            $pagination .= '<li><a href="#" data-page="'.$previous_link.'" title="Previous">&lt;</a></li>';
            for($i = ($current_page-2); $i < $current_page; $i++){
                if($i > 0){
                    $pagination .= '<li><a href="#" data-page="'.$i.'" title="Page'.$i.'">'.$i.'</a></li>';
                }
            }   
            $first_link = false;
        }
        
        if($first_link){ 
            $pagination .= '<li class="first active">'.$current_page.'</li>';
        }elseif($current_page == $total_pages){ 
            $pagination .= '<li class="last active">'.$current_page.'</li>';
        }else{ 
            $pagination .= '<li class="active">'.$current_page.'</li>';
        }
                
        for($i = $current_page+1; $i < $right_links ; $i++){ 
            if($i<=$total_pages){
                $pagination .= '<li><a href="#" data-page="'.$i.'" title="Page '.$i.'">'.$i.'</a></li>';
            }
        }
        if($current_page < $total_pages){ 
            $next_link = ($i > $total_pages)? $total_pages : $next;
            $pagination .= '<li><a href="#" data-page="'.$next_link.'" title="Next">&gt;</a></li>'; 
            $pagination .= '<li class="last"><a href="#" data-page="'.$total_pages.'" title="Last">&raquo;</a></li>';
        }
        
        $pagination .= '</ul>'; 
    }
    return $pagination;
}

function custom_excerpt_more( $more ) {
    return $more;
}
add_filter( 'excerpt_more', 'custom_excerpt_more' );

function get_directory_latlng($term = ''){
    global $wp_query;
    query_posts(array('post_type' => 'directories', 'post_status' => 'publish', 'taxonomy' => 'directories-cat', 'hide_empty' => 0, 'posts_per_page' => -1));
    if(!empty($term)){
        query_posts(array('post_type' => 'directories', 'post_status' => 'publish', 'taxonomy' => 'directories-cat', 'hide_empty' => 0, 'posts_per_page' => -1, 'directories-cat' => $term));
    }
    $latlng = array();
    $latlng_ar = array();
    if(have_posts()){
        while(have_posts()) {
            the_post();
            $geo_latlng = get_post_meta(get_the_ID(), 'geo_latlng', true);
            $geo_latlng = explode(',', $geo_latlng);
            $latlng['lat'] = $geo_latlng[0];
            $latlng['lng'] = $geo_latlng[1];
            $latlng['title'] = get_the_title();
            $latlng['content'] = mb_strimwidth(get_the_content(), 0, 300, '[...]');
            if(!empty($latlng['lat']) && !empty($latlng['lng'])){
                if($latlng['lat'] === '-25.274398' && $latlng['lng'] === '133.775136'){
                    continue;
                }
                array_push($latlng_ar, $latlng);
            }
        }
        wp_reset_query();
    }
    return $latlng_ar;
}

function get_single_directory_latlng($post_id){
    $latlng = array();
    $latlng_ar = array();
    $geo_latlng = get_post_meta($post_id, 'geo_latlng', true);
    $geo_latlng = explode(',', $geo_latlng);
    $latlng['lat'] = $geo_latlng[0];
    $latlng['lng'] = $geo_latlng[1];
    if($latlng['lat'] === '-25.274398' && $latlng['lng'] === '133.775136'){
        $latlng['lat'] = '-31.9528536';
        $latlng['lng'] = '115.8573389';
        array_push($latlng_ar, $latlng);
        return $latlng_ar;
    }
    $latlng['title'] = get_the_title($post_id);
    $latlng['content'] = mb_strimwidth(get_the_content($post_id), 0, 300, '[...]');
    array_push($latlng_ar, $latlng);
    return $latlng_ar;
}

function get_center_latlng($term = ''){
    $latlngs = get_directory_latlng();
    if(!empty($term))
        $latlngs = get_directory_latlng($term);
    $tot_lat = 0;
    $tot_lng = 0;
    $average_lat = 0;
    $average_lng = 0;
    if(is_array($latlngs) && !empty($latlngs)){
        foreach($latlngs as $ln){
            if($ln['lat'] === '-25.274398' && $ln['lng'] === '133.775136'){
                continue;
            }
            $tot_lat += $ln['lat']; 
            $tot_lng += $ln['lng']; 
        }
        $average_lat = $tot_lat / count($latlngs);
        $average_lng = $tot_lng / count($latlngs);
    }
    return array(
        array(
            'lat' => $average_lat,
            'lng' => $average_lng,
        )
    );
}
remove_filter( 'the_content', 'easy_image_gallery_append_to_content' ); 

function event_sorting_by_date(){
    query_posts(array(
        'post_type' => 'events',
        'post_status' => 'publish' 
    ));
    $dates_array = array();
    if(have_posts()){
        while(have_posts()){
            the_post();
            $data = array(
                'post_id' => get_the_ID(),
                'start_date' => get_field('start_date'),
                'end_date' => get_field('end_date')
            );
            array_push($dates_array, $data);
        }
        wp_reset_query();
        aasort($dates_array, 'start_date');
        return $dates_array;
    }
    return 0;
}

function get_the_slug( $id = null ){
    if( empty($id) )
        return '';
    $slug = basename( get_permalink($id) );
    return $slug;
}

function add_query_vars($query_vars) {
    $query_vars[] = "key"; 
    $query_vars[] = "auth";
    return $query_vars;
}

add_filter('query_vars', 'add_query_vars');

function add_rewrite_rules($rewrite) {
    $rewrite_rule1 = array(get_the_slug(EDIT_DIRECTORY_PAGE).'/([^/]+)/([^/]+)/?$' => 'index.php?pagename='.get_the_slug(EDIT_DIRECTORY_PAGE).'&key=$matches[1]&auth=$matches[2]');
    $rewrite_rule2 = array(get_the_slug(MAKE_PAYMENT_PAGE).'/([^/]+)/([^/]+)/?$' => 'index.php?pagename='.get_the_slug(MAKE_PAYMENT_PAGE).'&key=$matches[1]&auth=$matches[2]');
    $rewrite_rule3 = array(get_the_slug(CHECKOUT_PAGE).'/([^/]+)/([^/]+)/?$' => 'index.php?pagename='.get_the_slug(CHECKOUT_PAGE).'&key=$matches[1]&auth=$matches[2]');
    $rewrite_rule4 = array(get_the_slug(CONFIRM_SUBSCRIPTION_PAGE).'/([^/]+)/([^/]+)/?$' => 'index.php?pagename='.get_the_slug(CONFIRM_SUBSCRIPTION_PAGE).'&key=$matches[1]&auth=$matches[2]');
    $rewrite = $rewrite_rule1 + $rewrite_rule2 + $rewrite_rule3 + $rewrite_rule4 + $rewrite;
    return $rewrite;
}

add_filter('rewrite_rules_array', 'add_rewrite_rules');

function get_terms_by_post($post_id, $taxonomy, $name = FALSE){
    $terms = wp_get_post_terms( $post_id, $taxonomy);
    $terms_arr = array();
    if(is_array($terms) && !empty($terms)){
        foreach($terms as $term){
            if(!$name){
                array_push($terms_arr, $term->term_id);
            }else if($name){
                array_push($terms_arr, $term->slug);
            }
        }
    }
    return $terms_arr;
}

function sync_stripe_plan( $post_id, $post, $update ) {
    global $post_data;
    $post_type = 'packages';
    if( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
        return;
    }
    if ( $post->post_status === 'auto-draft' ) {
        return;
    }
    if ( $post->post_status === 'trash' ) {
        return;
    }
    if ( $post_type != $post->post_type ) {
        return;
    }
    if ( wp_is_post_revision( $post_id ) ){
        return;
    }
    
    if(is_stripe_plan_exists($post_id)){
        update_stripe_plan($post_id, $_REQUEST);
    }else{
        create_stripe_plan($post_id, $_REQUEST);
    }
}
add_action( 'save_post_packages', 'sync_stripe_plan', 10, 3 );

function disable_package_price_period_edit_option() {
    if(isset($_REQUEST['post']) && isset($_REQUEST['action']) && $_REQUEST['action'] == 'edit'){
        $package = get_post($_REQUEST['post']);
        if($package->post_type == 'packages'){?>
            <script type="text/javascript">
                (function($){
                    $(function(){
                        $(':radio:not(:checked)').attr('disabled', true);
                        $('input[type=number]').attr('readonly', 'readonly');
                    });
                })(jQuery);  
            </script>
<?php        }
    }
}
add_action('admin_head', 'disable_package_price_period_edit_option');

function subscription_billing_period(){
    global $post, $wpdb;
    $checked = (get_post_meta($post->ID, 'interval', true) <> '') ? get_post_meta($post->ID, 'interval', true) : 'month';
?>
    <input type="hidden" name="package_noncename" id="package_noncename" value="<?php echo  wp_create_nonce( plugin_basename(__FILE__) );?>" />
    <input required step="1" min="1" type="number" size="4" name="interval_count" id="interval_count" value="<?php echo (get_post_meta($post->ID, 'interval_count', true) <> '') ? get_post_meta($post->ID, 'interval_count', true) : 1;?>" style="width: 25%;"/>
    <input required type="radio" name="interval" id="interval" value="day" <?php echo ($checked == "day") ? "checked='checked'" : ""; ?> />D
    <input required type="radio" name="interval" id="interval" value="week" <?php echo ($checked == "week") ? "checked='checked'" : ""; ?> />W
    <input required type="radio" name="interval" id="interval" value="month" <?php echo ($checked == "month") ? "checked='checked'" : ""; ?> />M
    <input required type="radio" name="interval" id="interval" value="year" <?php echo ($checked == "year") ? "checked='checked'" : ""; ?> />Y<br/>
    <p class="description">D: Day, W: Week, M: Month, Y: Year</p>
<?php
}

function package_price(){
   global $post, $wpdb;
?>
   <input required min="0" type="number" step="0.01" value="<?php echo (get_post_meta($post->ID, 'package_price', true) <> '') ? get_post_meta($post->ID, 'package_price', true):'1';?>" id="package_price" class="number" name="package_price" style="width: 60%;" />&nbsp;AUD
<?php   
}
function save_package_meta_details($post_id, $post) {
    if ( !wp_verify_nonce( $_POST['package_noncename'], plugin_basename(__FILE__) )) {
        return $post->ID;
    }

    if ( !current_user_can( 'edit_post', $post->ID ))
        return $post->ID;

    $directory_meta['package_price'] = esc_sql($_POST['package_price']);
    $directory_meta['interval_count'] = esc_sql($_POST['interval_count']);
    $directory_meta['interval'] = esc_sql($_POST['interval']);

    foreach ($directory_meta as $key => $value) { 
        if( $post->post_type == 'revision' ) 
            return; 
        update_post_meta($post->ID, $key, $value);
    }
}
add_action('save_post', 'save_package_meta_details', 1, 2);

add_filter( 'post_row_actions', 'remove_row_actions', 10, 2 );
function remove_row_actions( $actions, $post ) {
    global $current_screen;
    if( $current_screen->post_type != 'directories' ) return $actions;
    unset( $actions['inline hide-if-no-js'] );
    //$actions['inline hide-if-no-js'] .= __( 'Quick&nbsp;Edit' );
    return $actions;
}

function banner_directory_listing($post_type = 'directories', $taxonomy = 'directories-cat', $term = ''){
    global $user_ID, $post, $wp_query;
    $query['post_type'] = $post_type;
    $query['taxonomy'] = $taxonomy;
    $query['post_status'] = 'publish';
    $query['meta_query'] = array(
        array(
            'key' => 'promoted_to_banner',
            'value' => 1,
            'compare' => '='
        )
    );
    $banner_directory = '';
    query_posts($query);
    if(have_posts()):
        $banner_directory .= '<section class="banner-container clearfix">';
        $banner_directory .= '<div class="inner-banner-container clearfix">';
        $banner_directory .= '<div class="owl-carousel inner-banner-carousel">';
        while(have_posts()) : the_post();
            $banner_image = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'list-cat-slider');
            $banner_directory .= '<div class="item">';
            $banner_directory .= '<div class="perth-slider-box">';
            if(has_post_thumbnail()):
            $banner_directory .= '<figure class="parth-slider-image">';
            $banner_directory .= '<img src="'.$banner_image[0].'" alt="Directory banner image" width="332" height="222">';
            $banner_directory .= '</figure>';
            endif;
            $banner_directory .= '<h3 class="parth-slider-title">';
            $banner_directory .= '<a href="'.get_permalink(get_the_ID()).'">'.get_the_title(get_the_ID()).' <i class="icon icon-arrow"></i></a>';
            $banner_directory .= '</h3>';
            $banner_directory .= '</div>';
            $banner_directory .= '</div>';
        endwhile;   
        wp_reset_query();
        $banner_directory .= '</div>';
        $banner_directory .= '</div>';
        $banner_directory .= '</section>';
    endif;
    return $banner_directory;
}

function count_promote_directroies($location){
    global $wp_query;
    $query = array(
        'post_type' => 'directories',
        'post_status' => 'publish',
        'meta_query' => array(
            array(
                'key'     => $location,
                'value'   => 1,
                'compare' => '=',
            )
        )
    );
    query_posts($query);
    $found_directories = $wp_query->found_posts;
    wp_reset_query();
    return $found_directories;
}

function best_of_perth($post_type = 'directories', $taxonomy = 'directories-cat', $term = ''){
    global $user_ID, $post, $wp_query;
    $query['post_type'] = $post_type;
    $query['taxonomy'] = $taxonomy;
    $query['post_status'] = 'publish';
    $query['meta_query'] = array(
        array(
            'key' => 'promoted_to_home',
            'value' => 1,
            'compare' => '='
        )
    );
    $best_of_perth = '';
    query_posts($query);
    if(have_posts()):
        $best_of_perth .= '<section class="section-blocks best-perth-section clearfix">';
        $best_of_perth .= '<header class="section-blocks-header">';
        $best_of_perth .= '<h2><i class="icon icon-best-perth"></i>Best of Perth</h2>';
        $best_of_perth .= '</header>';
        $best_of_perth .= '<section class="section-blocks-contentarea">';
        $best_of_perth .= '<div class="owl-carousel perth-carousel">';
        while(have_posts()) : the_post();
            $best_perth_image = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'list-desktop');
            
            $best_of_perth .= '<div class="item">';
            $best_of_perth .= '<div class="perth-slider-box">';
            if(has_post_thumbnail()):
            $best_of_perth .= '<figure class="parth-slider-image">';
            $best_of_perth .= '<img src="'.$best_perth_image[0].'" width="274" height="204" alt="Business directory image">';
            $best_of_perth .= '</figure>';
            endif;
            $best_of_perth .= '<h3 class="parth-slider-title">';
            $best_of_perth .= '<a href="'.get_permalink(get_the_ID()).'">'.get_the_title(get_the_ID()).' <i class="icon icon-arrow"></i></a>';
            $best_of_perth .= '</h3>';
            $best_of_perth .= '</div>';
            $best_of_perth .= '</div>';
        endwhile;   
        wp_reset_query();
        $best_of_perth .= '</div>';
        $best_of_perth .= '</section>';
        $best_of_perth .= '</section>';
    endif;
    return $best_of_perth;
}

add_filter('acf/fields/relationship/query', 'tax_filter');

function tax_filter($options){
    session_start();
    global $user_ID, $post, $wp_query;
    if(isset($_SESSION['edit_tag_ID'])){
        $options['tax_query'][] = array(
            'taxonomy' => 'directories-cat',
            'field' => 'id',
            'terms' => array($_SESSION['edit_tag_ID']),
        );
        return $options;
    }
    return $options;
}

function mini_banner_listing(){
    global $user_ID, $post, $wp_query;
    
    $banner_directory = '';
    
    $mini_banners = (is_tax('directories-cat', $wp_query->queried_object->term_id)) ? get_field('mini_banner_listings', 'directories-cat'.'_'.$wp_query->queried_object->term_id) : get_field('mini_banner_listings') ;
    if(empty($mini_banners))
        return $banner_directory;
    $post_ids = array();
    foreach ($mini_banners as $banners) {
        array_push($post_ids, $banners->ID);
    }
    
    $query['post_type'] = 'directories';
    $query['taxonomy'] = 'directories-cat';
    $query['post_status'] = 'publish';
    $query['post__in'] = $post_ids;
    $query['orderby'] = 'post__in';
    $query['order'] = 'ASC';
    
    query_posts($query);
    
    if(have_posts()):
        $banner_directory .= '<section class="banner-container clearfix">';
        $banner_directory .= '<div class="inner-banner-container clearfix">';
        $banner_directory .= '<div class="owl-carousel inner-banner-carousel">';
        while(have_posts()) : the_post();
            $banner_image = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'list-cat-slider');
            $banner_directory .= '<div class="item">';
            $banner_directory .= '<div class="perth-slider-box">';
            if(has_post_thumbnail()):
            $banner_directory .= '<figure class="parth-slider-image">';
            $banner_directory .= '<img src="'.$banner_image[0].'" alt="Directory banner image" width="332" height="222">';
            $banner_directory .= '</figure>';
            endif;
            $banner_directory .= '<h3 class="parth-slider-title">';
            $banner_directory .= '<a href="'.get_permalink(get_the_ID()).'">'.get_the_title(get_the_ID()).' <i class="icon icon-arrow"></i></a>';
            $banner_directory .= '</h3>';
            $banner_directory .= '</div>';
            $banner_directory .= '</div>';
        endwhile;   
        wp_reset_query();
        $banner_directory .= '</div>';
        $banner_directory .= '</div>';
        $banner_directory .= '</section>';
    endif;
    return $banner_directory;
}

function best_of_perth_home(){
    global $user_ID, $post, $wp_query;
    $best_of_perth = '';
    $best_of_perth_banners = get_field('best_of_perth');
    
    if(empty($best_of_perth_banners))
        return $best_of_perth;
    
    $post_ids = array();
    
    foreach ($best_of_perth_banners as $banners) {
        array_push($post_ids, $banners->ID);
    }
    
    $query['post_type'] = 'directories';
    $query['taxonomy'] = 'directories-cat';
    $query['post_status'] = 'publish';
    $query['post__in'] = $post_ids;
    $query['orderby'] = 'post__in';
    $query['order'] = 'ASC';
    
    query_posts($query);
    
    if(have_posts()):
        $best_of_perth .= '<section class="section-blocks best-perth-section clearfix">';
        $best_of_perth .= '<header class="section-blocks-header">';
        $best_of_perth .= '<h2><i class="icon icon-best-perth"></i>Best of Perth</h2>';
        $best_of_perth .= '</header>';
        $best_of_perth .= '<section class="section-blocks-contentarea">';
        $best_of_perth .= '<div class="owl-carousel perth-carousel">';
        while(have_posts()) : the_post();
            $best_perth_image = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'list-desktop');
            
            $best_of_perth .= '<div class="item">';
            $best_of_perth .= '<div class="perth-slider-box">';
            if(has_post_thumbnail()):
            $best_of_perth .= '<figure class="parth-slider-image">';
            $best_of_perth .= '<img src="'.$best_perth_image[0].'" width="274" height="204" alt="Business directory image">';
            $best_of_perth .= '</figure>';
            endif;
            $best_of_perth .= '<h3 class="parth-slider-title">';
            $best_of_perth .= '<a href="'.get_permalink(get_the_ID()).'">'.get_the_title(get_the_ID()).' <i class="icon icon-arrow"></i></a>';
            $best_of_perth .= '</h3>';
            $best_of_perth .= '</div>';
            $best_of_perth .= '</div>';
        endwhile;   
        wp_reset_query();
        $best_of_perth .= '</div>';
        $best_of_perth .= '</section>';
        $best_of_perth .= '</section>';
    endif;
    return $best_of_perth;
}

add_filter('wp_dropdown_users', 'DisplayAdvertisers');
function DisplayAdvertisers($output){
    global $post, $user_ID;
    if($post->post_type == 'directories'){
        $users = get_users(); // 'role=advertiser'
        $output = "<select id=\"post_author_override\" name=\"post_author_override\" class=\"\">";
//        $select = ($post->post_author == 1) ? 'selected="selected"' : '';
//        $output .= "<option value=\"1\" ".$select.">".  get_the_author_meta('user_login', 1) ."</option>";
        foreach($users as $user){
            $sel = ($post->post_author == $user->ID)?"selected='selected'":'';
            $output .= '<option value="'.$user->ID.'"'.$sel.'>'.$user->user_login.'</option>';
        }
        $output .= "</select>";
        return $output;
    }
    return $output;
}