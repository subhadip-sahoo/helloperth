<?php
/* 
 * Plugin Name: Subscriptions
 * Plugin URI: http://businessprodesigns.com
 * Description: Lists of users who have subscribed for certain package / packages
 * Version: 1.0
 * Author: Business Pro Designs
 * Author URI: http://businessprodesigns.com
 * Licence: GPL2
*/

global $wpdb, $table_name_users;
$table_name_users = $wpdb->prefix . 'user_payment_stripe';

require_once dirname(__FILE__) . '/class/wp-subscription-list-table.class.php';
require_once dirname(__FILE__).'/class/user_subscription_details_list_table.class.php';

function user_subscription_details_admin_menu(){
    $hook1 = add_menu_page(__('Subscription Details', 'user_subscription_details'), __('Subscriptions', 'user_subscription_details'), 'activate_plugins', 'subscription-details', 'user_subscription_details_main', plugins_url().'/user-subscription-details/images/addEdit.png', 6);
    add_action('load-'.$hook1, 'subscription_add_option');
}

function subscription_add_option() {
    $option = 'per_page';
    $args = array(
        'label' => 'Subscription Lists',
        'default' => 10,
        'option' => 'subscripbers_per_page'
    );
    
    $screen = get_current_screen();
    add_filter( 'manage_'.$screen->id.'_columns', array( 'user_subscription_details_list_table', 'get_columns' ));
    add_screen_option( $option, $args );
}
add_action('admin_menu', 'user_subscription_details_admin_menu');
add_filter('set-screen-option', 'subscription_set_option', 10, 3);

function subscription_set_option($status, $option, $value) {
    if ( 'subscripbers_per_page' == $option ) return $value;
    return $status;
}

function user_subscription_details_main($per_page){    
    global $wpdb, $table_name_users;
    require_once ABSPATH.'/wp-blog-header.php';
    if(isset($_REQUEST['cancel_subscr'])){
        $id = $_REQUEST['id'];
        $get_data = $wpdb->get_results("SELECT `id_user`, `subscription_id` FROM `{$table_name_users}` WHERE `id` = {$id}");
        foreach ($get_data as $data) {
            $stripe_cus_id = get_user_meta($data->id_user, 'stripe_cus_id', true);
            $subscription_id = $data->subscription_id;
        }
        if('canceled' == $response = cancel_stripe_subscription($stripe_cus_id, $subscription_id)){
            $api_message = '<div class="updated" id="message"><p>' . sprintf(__('Subscription has been canceled successfully.', 'user_subscription_details')) . '</p></div>';
        }else{
            $api_message = '<div id="notice" class="error"><p>' . sprintf(__('%s', 'user_subscription_details'), $response) . '</p></div>';
        }
    }
    $table = new user_subscription_details_list_table();
    $table->prepare_items();

    $message = '';
    if ('delete' === $table->current_action()) {
        $message = '<div class="updated below-h2" id="message"><p>' . sprintf(__('%d Items deleted.', 'user_subscription_details'), count($_REQUEST['id'])) . '</p></div>';
    }
    ?>
<div class="wrap">
    <div class="icon32" id="icon-users"><br></div>
    <h2><?php _e('All Subscriptions', 'user_subscription_details')?> 
<?php
    if ( ! empty( $_REQUEST['s'] ) ) {
		echo sprintf( '<span class="subtitle">'
			. __( 'Search results for &#8220;%s&#8221;', 'user_subscription_details' )
			. '</span>', esc_html( $_REQUEST['s'] ) );
	}
?>
    </h2>
    <?php echo $message; ?>
    <?php echo (isset($api_message)) ? $api_message : ''; ?>

    <form method="get" action="">
        <input type="hidden" name="page" value="<?php echo esc_attr( $_REQUEST['page'] ); ?>"/>
        <?php $table->search_box( __( 'Search', 'user_subscription_details' ), 'subscriptions' ); ?>
    </form>
    <form name="camcel_subscription" id="camcel_subscription" action="" method="POST">
        <input type="hidden" name="page" value="<?php echo esc_attr( $_REQUEST['page'] ); ?>"/>
        <?php $table->display(); ?>
    </form>
</div>
 <?php   
}

function user_subscription_details_languages(){
    load_plugin_textdomain('user_subscription_details', false, dirname(plugin_basename(__FILE__)));
}
add_action('init', 'user_subscription_details_languages');
?>