<?php
require_once '../../../../wp-blog-header.php';
global $wpdb;
\Stripe\Stripe::setApiKey(stripe_api_key('secret'));
$input = @file_get_contents("php://input");
$event_json = json_decode($input);
$serialize_event_data = serialize($event_json);
wp_mail('subhadip.sahoo@businessprodesigns.com', $event_json->type, print_r($event_json, true) );
$stripe_data_table = $wpdb->prefix.'stripe_webhook_data';
$stripe_api_data = array(
    'event_type' => $event_json->type,
    'data' => $serialize_event_data,
    'date_added' => date(DATETIME_DATABASE_FORMAT)
);
$wpdb->insert($stripe_data_table, $stripe_api_data);
switch($event_json->type){
    case 'customer.subscription.created':
        customer_subscription_created($event_json);
        break;
    case 'charge.succeeded':
        charge_succeeded($event_json);
        break;
    case 'customer.subscription.deleted':
        customer_subscription_deleted($event_json);
        break;
    case 'customer.subscription.updated':
        break;
    case 'invoice.created':
        break;
    case 'invoice.updated':
        break;
    case 'invoice.payment_succeeded':
        break;
    case 'customer.updated':
        break;
    case 'customer.source.created':
        break;
    case 'customer.source.deleted':
        break;
    case 'plan.created':
        break;
    case 'plan.updated':
        break;
    case 'plan.deleted':
        break;
    case 'customer.created':
        break;
}

http_response_code(200);


