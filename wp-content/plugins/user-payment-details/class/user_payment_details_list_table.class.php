<?php
class user_payment_details_list_table extends WP_List_Table {
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

//    function column_display_name($item){
//        $actions = array(
//            'edit' => sprintf('<a href="?page=edit-user-details&id=%s">%s</a>', $item['id'], __('Edit', 'user_payment_details')),
//            'delete' => sprintf('<a href="?page=%s&action=delete&id=%s">%s</a>', $_REQUEST['page'], $item['id'], __('Delete', 'user_payment_details')),
//        );
//
//        return sprintf('%s %s', $item['username'], $this->row_actions($actions));
//    }
//
//    function column_cb($item){
//        return sprintf(
//            '<input type="checkbox" name="id[]" value="%s" />',
//            $item['id']
//        );
//    }

    function get_columns(){
        $columns = array(
//            'cb' => '<input type="checkbox" />', 
            'username' => __('Name', 'user_payment_details'),
            'package_name' => __('Plan', 'user_payment_details'),
            'transaction_id' => __('Txn. ID', 'user_payment_details'),
            'transaction_amount' => __('Txn. Amount', 'user_payment_details'),
            'subscription_id' => __('Subscription ID', 'user_payment_details'),
            'payment_date' => __('Payment Date', 'user_payment_details'),
            'expiry_date' => __('Expiry Date', 'user_payment_details'),
        );
        return $columns;
    }

    function get_sortable_columns(){
        $sortable_columns = array(
            'username' => array('username', true),
            'package_name' => array('package_name', false),
            'transaction_id' => array('transaction_id', false),
            'transaction_amount' => array('transaction_amount', false),
            'subscription_id' => array('subscription_id', false),
            'payment_date' => array('payment_date', false),
            'expiry_date' => array('expiry_date', false)
        );
        return $sortable_columns;
    }

//    function get_bulk_actions(){
//        $actions = array(
//            'delete' => 'Delete'
//        );
//        return $actions;
//    }

//    function process_bulk_action(){
//        global $wpdb, $table_name_users;
//
//        if ('delete' === $this->current_action()) {
//            $ids = isset($_REQUEST['id']) ? $_REQUEST['id'] : array();
//            if (is_array($ids)) $ids = implode(',', $ids);
//
//            if (!empty($ids)) {
//                $wpdb->query("DELETE FROM $table_name_users WHERE id IN($ids)");
//            }
//        }
//    }
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
        $where = '';
        if (isset($_REQUEST['s']) && ! empty( $_REQUEST['s'] ) ){
            $where = " WHERE u.display_name LIKE '%%".$_REQUEST['s']."%%' OR p.post_title LIKE '%%".$_REQUEST['s']."%%' OR up.transaction_id LIKE '%%".$_REQUEST['s']."%%' OR up.transaction_amount LIKE '%%".$_REQUEST['s']."%%' OR up.subscription_id LIKE '%%".$_REQUEST['s']."%%' OR date_format(up.payment_date, '%D %b, %Y at %l:%i %p') LIKE '%%".$_REQUEST['s']."%%'";
        }
        if ( ! empty( $_REQUEST['orderby'] ) ) {
            if ( 'username' == $_REQUEST['orderby'] ){
                $args['orderby'] = 'username';
            }
            elseif ( 'package_name' == $_REQUEST['orderby'] ){
                $args['orderby'] = 'package_name';
            }
            elseif ( 'transaction_id' == $_REQUEST['orderby'] ){
                $args['orderby'] = 'transaction_id';
            }
            elseif ( 'transaction_amount' == $_REQUEST['orderby'] ){
                $args['orderby'] = 'transaction_amount';
            }
            elseif ( 'subscription_id' == $_REQUEST['orderby'] ){
                $args['orderby'] = 'subscription_id';
            }
            elseif ( 'payment_date' == $_REQUEST['orderby'] ){
                $args['orderby'] = 'payment_date';
            }
            elseif ( 'expiry_date' == $_REQUEST['orderby'] ){
                $args['orderby'] = 'expiry_date';
            }
        }
        
        if ( ! empty( $_REQUEST['order'] ) ) {
            if ( 'asc' == strtolower( $_REQUEST['order'] ) ){
                $args['order'] = 'ASC';
            }
            elseif ( 'desc' == strtolower( $_REQUEST['order'] ) ){
                $args['order'] = 'DESC';
            }
        }
        $query = "SELECT u.display_name as username, p.post_title as package_name, up.id, up.transaction_id, up.transaction_amount, up.subscription_id, date_format(up.payment_date, '%D %b, %Y at %l:%i %p') as payment_date, (SELECT date_format(meta_value,'%D %b, %Y at %l:%i %p') FROM {$wpdb->prefix}usermeta WHERE meta_key = 'account_expiry' and user_id = up.id_user) AS expiry_date FROM $table_name_users as up inner join {$wpdb->prefix}users as u on up.id_user = u.ID inner join {$wpdb->prefix}posts as p on up.id_package = p.ID";
        $this->items = $wpdb->get_results("$query $where ORDER BY ".$args['orderby']." ".$args['order']." LIMIT $per_page OFFSET ".$args['offset'], ARRAY_A);
        $total_items = $wpdb->get_var("SELECT COUNT(up.id) FROM $table_name_users as up INNER JOIN {$wpdb->prefix}users as u on up.id_user = u.ID INNER JOIN {$wpdb->prefix}posts as p on up.id_package = p.ID $where");
        $this->set_pagination_args(array(
            'total_items' => $total_items,
            'total_pages' => ceil($total_items / $per_page),
            'per_page' => $per_page
        ));
    }
}

?>
