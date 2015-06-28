<?php
/* 
 * Plugin Name: Payment Lists
 * Plugin URI: http://businessprodesigns.com
 * Description: Lists of users who have paid for certain package / packages
 * Version: 1.0
 * Author: Business Pro Designs
 * Author URI: http://businessprodesigns.com
 * Licence: GPL2
*/
global $wpdb, $table_name_users;
$table_name_users = $wpdb->prefix . 'user_payment_stripe';

if (!class_exists('WP_List_Table')) {
   require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

require_once dirname(__FILE__).'/class/user_payment_details_list_table.class.php';

function user_payment_details_admin_menu(){
    $hook = add_menu_page(__('Payment Details', 'user_payment_details'), __('Payments', 'user_payment_details'), 'activate_plugins', 'user-details', 'user_payment_details_main', plugins_url().'/user-payment-details/images/addEdit.png', 7);
    add_action('load-'.$hook, 'cct_add_option');
}

function cct_add_option() {
    $option = 'per_page';
    $args = array(
        'label' => 'Payment Lists',
        'default' => 10,
        'option' => 'users_per_page'
    );
    
    $screen = get_current_screen();
    add_filter( 'manage_'.$screen->id.'_columns', array( 'user_payment_details_list_table', 'get_columns' ));
    add_screen_option( $option, $args );
}
add_action('admin_menu', 'user_payment_details_admin_menu');
add_filter('set-screen-option', 'cct_set_option', 10, 3);

function cct_set_option($status, $option, $value) {
    if ( 'users_per_page' == $option ) return $value;
    return $status;
}

function user_payment_details_main($per_page){
    global $wpdb, $table_name_users;
    $table = new user_payment_details_list_table();
    $table->prepare_items();

    $message = '';
    if ('delete' === $table->current_action()) {
        $message = '<div class="updated below-h2" id="message"><p>' . sprintf(__('%d Items deleted.', 'user_payment_details'), count($_REQUEST['id'])) . '</p></div>';
    }
    ?>
<div class="wrap">
    <div class="icon32" id="icon-users"><br></div>
    <h2><?php _e('All Payments', 'user_payment_details')?> 
<?php
    if ( ! empty( $_REQUEST['s'] ) ) {
		echo sprintf( '<span class="subtitle">'
			. __( 'Search results for &#8220;%s&#8221;', 'user_payment_details' )
			. '</span>', esc_html( $_REQUEST['s'] ) );
	}
?>
    </h2>
    <?php echo $message; ?>

    <form method="get" action="">
        <input type="hidden" name="page" value="<?php echo esc_attr( $_REQUEST['page'] ); ?>"/>
        <?php $table->search_box( __( 'Search', 'user_payment_details' ), 'payments' ); ?>
        <?php $table->display(); ?>
    </form>
</div>
 <?php   
}

function user_payment_details_languages(){
    load_plugin_textdomain('user_payment_details', false, dirname(plugin_basename(__FILE__)));
}
add_action('init', 'user_payment_details_languages');
?>