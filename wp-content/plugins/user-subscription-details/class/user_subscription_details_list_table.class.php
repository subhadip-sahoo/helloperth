<?php
class user_subscription_details_list_table extends WP_Subscription_List_Table {
    function __construct(){
        global $status, $page;
        parent::__construct(array(
            'singular' => '',
            'plural' => '',
            'ajax' => false
        ));
    }
    function column_default($item, $column_name){
        return $item[$column_name];
    }

    function get_columns(){
        $columns = array(
            'username' => __('Name', 'user_subscription_details'),
            'package_name' => __('Plan', 'user_subscription_details'),
            'subscription_id' => __('Subscription ID', 'user_subscription_details'),
            'subscription_date' => __('Subscription Date', 'user_subscription_details'),
            'subscription_status' => __('Status', 'user_subscription_details'),
            'action' => __('Action', 'user_subscription_details'),
        );
        return $columns;
    }

    function get_sortable_columns(){
        $sortable_columns = array(
            'username' => array('username', true),
            'package_name' => array('package_name', false),
            'subscription_id' => array('subscription_id', false),
            'subscription_date' => array('subscription_date', false),
            'subscription_status' => array('subscription_status', false)
        );
        return $sortable_columns;
    }

    function prepare_items(){
        global $wpdb, $table_name_users;
        $user = get_current_user_id();
        $screen = get_current_screen();
        $option = $screen->get_option('per_page', 'option');
        $per_page = get_user_meta($user, $option, true);

        if ( empty ( $per_page) || $per_page < 1 ) {
            $per_page = $screen->get_option( 'per_page', 'default');
        }
        $this->_column_headers = $this->get_column_info();
        //$this->process_bulk_action();
        $args = array(
                'posts_per_page' => $per_page,
                'orderby' => 'id',
                'order' => 'DESC',
                'offset' => ( $this->get_pagenum() - 1 ) * $per_page );
        $having = '';
        $where = '';
        if (isset($_REQUEST['s']) && ! empty( $_REQUEST['s'] ) ){
            $having = " HAVING display_name LIKE '%%".$_REQUEST['s']."%%' OR post_title LIKE '%%".$_REQUEST['s']."%%' OR subscription_id LIKE '%%".$_REQUEST['s']."%%' OR subscription_status LIKE '%%".$_REQUEST['s']."%%' OR date_format(subscription_date, '%D %b, %Y at %l:%i %p') LIKE '%%".$_REQUEST['s']."%%'";
            $where = " WHERE u.display_name LIKE '%%".$_REQUEST['s']."%%' OR p.post_title LIKE '%%".$_REQUEST['s']."%%' OR up.subscription_id LIKE '%%".$_REQUEST['s']."%%' OR up.subscription_status LIKE '%%".$_REQUEST['s']."%%' OR date_format(up.subscription_date, '%D %b, %Y at %l:%i %p') LIKE '%%".$_REQUEST['s']."%%'";
        }
        if ( ! empty( $_REQUEST['orderby'] ) ) {
            if ( 'username' == $_REQUEST['orderby'] ){
                $args['orderby'] = 'username';
            }elseif ( 'package_name' == $_REQUEST['orderby'] ){
                $args['orderby'] = 'package_name';
            }elseif ( 'subscription_id' == $_REQUEST['orderby'] ){
                $args['orderby'] = 'subscription_id';
            }elseif ( 'subscription_date' == $_REQUEST['orderby'] ){
                $args['orderby'] = 'subscription_date';
            }elseif ( 'subscription_status' == $_REQUEST['orderby'] ){
                $args['orderby'] = 'subscription_status';
            }
        }
        
        if ( ! empty( $_REQUEST['order'] ) ) {
            if ( 'asc' == strtolower( $_REQUEST['order'] ) ){
                $args['order'] = 'ASC';
            }elseif ( 'desc' == strtolower( $_REQUEST['order'] ) ){
                $args['order'] = 'DESC';
            }
        }
        $query = "SELECT up.id, u.display_name as username, p.post_title as package_name, up.subscription_id, date_format(up.subscription_date, '%D %b, %Y at %l:%i %p') as subscription_date, up.subscription_status, up.subscription_canceled_at FROM $table_name_users as up INNER JOIN {$wpdb->prefix}users as u on up.id_user = u.ID INNER JOIN {$wpdb->prefix}posts as p on up.id_package = p.ID GROUP BY subscription_id";
        $this->items = $wpdb->get_results("$query $having ORDER BY ".$args['orderby']." ".$args['order']." LIMIT $per_page OFFSET ".$args['offset'], ARRAY_A);
        $total_items = $wpdb->get_var("SELECT COUNT(DISTINCT up.subscription_id) FROM $table_name_users as up INNER JOIN {$wpdb->prefix}users as u on up.id_user = u.ID INNER JOIN {$wpdb->prefix}posts as p on up.id_package = p.ID $where");
        $this->set_pagination_args(array(
            'total_items' => $total_items,
            'total_pages' => ceil($total_items / $per_page),
            'per_page' => $per_page
        ));
    }
}
?>