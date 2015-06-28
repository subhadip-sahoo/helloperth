<?php
function stripe_api_key($key_type = 'public'){
    $stripe_envirnment = get_field('stripe_api_environment', 'option');
    if($stripe_envirnment == 'test'){
        if($key_type == 'public'){
            return get_field('stripe_test_publishable_key', 'option');
        }else if($key_type == 'secret'){
            return get_field('stripe_test_secret_key', 'option');
        }
    }else if($stripe_envirnment == 'live'){
        if($key_type == 'public'){
            return get_field('stripe_live_publishable_key', 'option');
        }else if($key_type == 'secret'){
            return get_field('stripe_live_secret_key', 'option');
        }
    }
}

function is_stripe_plan_exists($plan_id){
    \Stripe\Stripe::setApiKey(stripe_api_key('secret'));
    try {
        $stripe_plan = \Stripe\Plan::retrieve($plan_id);
        return TRUE;
    } catch(\Stripe\Error\Card $e) {
        $body = $e->getJsonBody();
        $err  = $body['error'];
        return FALSE;
    } catch (\Stripe\Error\InvalidRequest $e) {
        $body = $e->getJsonBody();
        $err  = $body['error'];
        return FALSE;
    } catch (\Stripe\Error\Authentication $e) {
        $body = $e->getJsonBody();
        $err  = $body['error'];
        return FALSE;
    } catch (\Stripe\Error\ApiConnection $e) {
        $body = $e->getJsonBody();
        $err  = $body['error'];
        return FALSE;
    } catch (\Stripe\Error\Base $e) {
        $body = $e->getJsonBody();
        $err  = $body['error'];
        return FALSE;
    } catch (Exception $e) {
        return FALSE;
    }
}

function create_stripe_plan($plan_id, $req){
    \Stripe\Stripe::setApiKey(stripe_api_key('secret'));
    $plan = get_post($plan_id);
    try {
        $stripe_plan = \Stripe\Plan::create(array(
            'amount' => $req['package_price'] * 100,
            'interval' => $req['interval'],
            'interval_count' => $req['interval_count'],
            'name' => $plan->post_title,
            'currency' => 'aud',
            'id' => $plan_id)
        );
        return $stripe_plan->id;
    } catch(\Stripe\Error\Card $e) {
        $body = $e->getJsonBody();
        $err  = $body['error'];
        return FALSE;
    } catch (\Stripe\Error\InvalidRequest $e) {
        $body = $e->getJsonBody();
        $err  = $body['error'];
        return FALSE;
    } catch (\Stripe\Error\Authentication $e) {
        $body = $e->getJsonBody();
        $err  = $body['error'];
        return FALSE;
    } catch (\Stripe\Error\ApiConnection $e) {
        $body = $e->getJsonBody();
        $err  = $body['error'];
        return FALSE;
    } catch (\Stripe\Error\Base $e) {
        $body = $e->getJsonBody();
        $err  = $body['error'];
        return FALSE;
    } catch (Exception $e) {
        return FALSE;
    }
}

function update_stripe_plan($plan_id){
    \Stripe\Stripe::setApiKey(stripe_api_key('secret'));
    $plan = get_post($plan_id);
    try {
        $stripe_plan = \Stripe\Plan::retrieve($plan_id);
        $stripe_plan->name = $plan->post_title;
        $stripe_plan->save();
        return $stripe_plan->id;
    } catch(\Stripe\Error\Card $e) {
        $body = $e->getJsonBody();
        $err  = $body['error'];
        return FALSE;
    } catch (\Stripe\Error\InvalidRequest $e) {
        $body = $e->getJsonBody();
        $err  = $body['error'];
        return FALSE;
    } catch (\Stripe\Error\Authentication $e) {
        $body = $e->getJsonBody();
        $err  = $body['error'];
        return FALSE;
    } catch (\Stripe\Error\ApiConnection $e) {
        $body = $e->getJsonBody();
        $err  = $body['error'];
        return FALSE;
    } catch (\Stripe\Error\Base $e) {
        $body = $e->getJsonBody();
        $err  = $body['error'];
        return FALSE;
    } catch (Exception $e) {
        return FALSE;
    }
}

function delete_stripe_plan($plan_id){
    \Stripe\Stripe::setApiKey(stripe_api_key('secret'));
    try {
        $stripe_plan = \Stripe\Plan::retrieve($plan_id);
        $stripe_plan->delete();
        return $stripe_plan->deleted;
    } catch(\Stripe\Error\Card $e) {
        $body = $e->getJsonBody();
        $err  = $body['error'];
        return FALSE;
    } catch (\Stripe\Error\InvalidRequest $e) {
        $body = $e->getJsonBody();
        $err  = $body['error'];
        return FALSE;
    } catch (\Stripe\Error\Authentication $e) {
        $body = $e->getJsonBody();
        $err  = $body['error'];
        return FALSE;
    } catch (\Stripe\Error\ApiConnection $e) {
        $body = $e->getJsonBody();
        $err  = $body['error'];
        return FALSE;
    } catch (\Stripe\Error\Base $e) {
        $body = $e->getJsonBody();
        $err  = $body['error'];
        return FALSE;
    } catch (Exception $e) {
        return FALSE;
    }
}

function create_stripe_customer($user_email, $metadata){
    \Stripe\Stripe::setApiKey(stripe_api_key('secret'));
    try {
        $customer = \Stripe\Customer::create(array(
            'email' => $user_email,
            'metadata' => $metadata)
        );
        return $customer->id;
    } catch(\Stripe\Error\Card $e) {
        $body = $e->getJsonBody();
        $err  = $body['error'];
        return FALSE;
    } catch (\Stripe\Error\InvalidRequest $e) {
        $body = $e->getJsonBody();
        $err  = $body['error'];
        return FALSE;
    } catch (\Stripe\Error\Authentication $e) {
        $body = $e->getJsonBody();
        $err  = $body['error'];
        return FALSE;
    } catch (\Stripe\Error\ApiConnection $e) {
        $body = $e->getJsonBody();
        $err  = $body['error'];
        return FALSE;
    } catch (\Stripe\Error\Base $e) {
        $body = $e->getJsonBody();
        $err  = $body['error'];
        return FALSE;
    } catch (Exception $e) {
        return FALSE;
    }
}

function update_stripe_customer($cus_id, $user_email, $metadata){
    \Stripe\Stripe::setApiKey(stripe_api_key('secret'));
    try {
        $customer = \Stripe\Customer::retrieve($cus_id);
        $customer->email = $user_email;
        $customer->metadata = $metadata;
        $customer->save();
        return $customer->id;
    } catch(\Stripe\Error\Card $e) {
        $body = $e->getJsonBody();
        $err  = $body['error'];
        return FALSE;
    } catch (\Stripe\Error\InvalidRequest $e) {
        $body = $e->getJsonBody();
        $err  = $body['error'];
        return FALSE;
    } catch (\Stripe\Error\Authentication $e) {
        $body = $e->getJsonBody();
        $err  = $body['error'];
        return FALSE;
    } catch (\Stripe\Error\ApiConnection $e) {
        $body = $e->getJsonBody();
        $err  = $body['error'];
        return FALSE;
    } catch (\Stripe\Error\Base $e) {
        $body = $e->getJsonBody();
        $err  = $body['error'];
        return FALSE;
    } catch (Exception $e) {
        return FALSE;
    }
}

function get_stripe_customer($cus_id){
    \Stripe\Stripe::setApiKey(stripe_api_key('secret'));
    try {
        $customer = \Stripe\Customer::retrieve($cus_id);
        return $customer;
    } catch(\Stripe\Error\Card $e) {
        $body = $e->getJsonBody();
        $err  = $body['error'];
        return FALSE;
    } catch (\Stripe\Error\InvalidRequest $e) {
        $body = $e->getJsonBody();
        $err  = $body['error'];
        return FALSE;
    } catch (\Stripe\Error\Authentication $e) {
        $body = $e->getJsonBody();
        $err  = $body['error'];
        return FALSE;
    } catch (\Stripe\Error\ApiConnection $e) {
        $body = $e->getJsonBody();
        $err  = $body['error'];
        return FALSE;
    } catch (\Stripe\Error\Base $e) {
        $body = $e->getJsonBody();
        $err  = $body['error'];
        return FALSE;
    } catch (Exception $e) {
        return FALSE;
    }
}

function dalete_stripe_customer($cus_id){
    \Stripe\Stripe::setApiKey(stripe_api_key('secret'));
    try {
        $customer = \Stripe\Customer::retrieve($cus_id);
        $customer->delete();
        return $customer->deleted;
    } catch(\Stripe\Error\Card $e) {
        $body = $e->getJsonBody();
        $err  = $body['error'];
        return FALSE;
    } catch (\Stripe\Error\InvalidRequest $e) {
        $body = $e->getJsonBody();
        $err  = $body['error'];
        return FALSE;
    } catch (\Stripe\Error\Authentication $e) {
        $body = $e->getJsonBody();
        $err  = $body['error'];
        return FALSE;
    } catch (\Stripe\Error\ApiConnection $e) {
        $body = $e->getJsonBody();
        $err  = $body['error'];
        return FALSE;
    } catch (\Stripe\Error\Base $e) {
        $body = $e->getJsonBody();
        $err  = $body['error'];
        return FALSE;
    } catch (Exception $e) {
        return FALSE;
    }
}

function create_stripe_subscription($token, $cus_id, $plan_id){
    \Stripe\Stripe::setApiKey(stripe_api_key('secret'));
    try {
        $customer = \Stripe\Customer::retrieve($cus_id);
        $subscription = $customer->subscriptions->create(array(
            'plan' => $plan_id,
            'source' => $token)
        );
        return array(1, $subscription);
    } catch(\Stripe\Error\Card $e) {
        $body = $e->getJsonBody();
        $err  = $body['error'];
        return array(0, $err['message']);
    } catch (\Stripe\Error\InvalidRequest $e) {
        $body = $e->getJsonBody();
        $err  = $body['error'];
        return array(0, $err['message']);
    } catch (\Stripe\Error\Authentication $e) {
        $body = $e->getJsonBody();
        $err  = $body['error'];
        return array(0, $err['message']);
    } catch (\Stripe\Error\ApiConnection $e) {
        $body = $e->getJsonBody();
        $err  = $body['error'];
        return array(0, $err['message']);
    } catch (\Stripe\Error\Base $e) {
        $body = $e->getJsonBody();
        $err  = $body['error'];
        return array(0, $err['message']);
    } catch (Exception $e) {
        return array(0, 'Subscription failed');
    }
}

function cancel_stripe_subscription($cus_id, $subscription_id){
    \Stripe\Stripe::setApiKey(stripe_api_key('secret'));
    try {
        $customer = \Stripe\Customer::retrieve($cus_id);
        $subscription = $customer->subscriptions->retrieve($subscription_id)->cancel();
        return $subscription->status;
    } catch(\Stripe\Error\Card $e) {
        $body = $e->getJsonBody();
        $err  = $body['error'];
        return $err['message'];
    } catch (\Stripe\Error\InvalidRequest $e) {
        $body = $e->getJsonBody();
        $err  = $body['error'];
        return $err['message'];
    } catch (\Stripe\Error\Authentication $e) {
        $body = $e->getJsonBody();
        $err  = $body['error'];
        return $err['message'];
    } catch (\Stripe\Error\ApiConnection $e) {
        $body = $e->getJsonBody();
        $err  = $body['error'];
        return $err['message'];
    } catch (\Stripe\Error\Base $e) {
        $body = $e->getJsonBody();
        $err  = $body['error'];
        return $err['message'];
    } catch (Exception $e) {
        return 'Subscription cancel failed due to unknown reason.';
    }
}

function get_stripe_subscription($cus_id, $subcription_id){
    \Stripe\Stripe::setApiKey(stripe_api_key('secret'));
    try {
        $customer = \Stripe\Customer::retrieve($cus_id);
        $subscription = $customer->subscriptions->retrieve($subcription_id);
        return $subscription;
    } catch(\Stripe\Error\Card $e) {
        $body = $e->getJsonBody();
        $err  = $body['error'];
        return FALSE;
    } catch (\Stripe\Error\InvalidRequest $e) {
        $body = $e->getJsonBody();
        $err  = $body['error'];
        return FALSE;
    } catch (\Stripe\Error\Authentication $e) {
        $body = $e->getJsonBody();
        $err  = $body['error'];
        return FALSE;
    } catch (\Stripe\Error\ApiConnection $e) {
        $body = $e->getJsonBody();
        $err  = $body['error'];
        return FALSE;
    } catch (\Stripe\Error\Base $e) {
        $body = $e->getJsonBody();
        $err  = $body['error'];
        return FALSE;
    } catch (Exception $e) {
        return FALSE;
    }
}

function get_stripe_invoice($invoice_id){
    \Stripe\Stripe::setApiKey(stripe_api_key('secret'));
    try {
        $invoice = \Stripe\Invoice::retrieve($invoice_id);
        $invoice_lines = \Stripe\Invoice::retrieve($invoice_id)->lines->all(); // get all lines items
        return $invoice; // array($invoice, $invoice_lines)
    } catch(\Stripe\Error\Card $e) {
        $body = $e->getJsonBody();
        $err  = $body['error'];
        return FALSE;
    } catch (\Stripe\Error\InvalidRequest $e) {
        $body = $e->getJsonBody();
        $err  = $body['error'];
        return FALSE;
    } catch (\Stripe\Error\Authentication $e) {
        $body = $e->getJsonBody();
        $err  = $body['error'];
        return FALSE;
    } catch (\Stripe\Error\ApiConnection $e) {
        $body = $e->getJsonBody();
        $err  = $body['error'];
        return FALSE;
    } catch (\Stripe\Error\Base $e) {
        $body = $e->getJsonBody();
        $err  = $body['error'];
        return FALSE;
    } catch (Exception $e) {
        return FALSE;
    }
}

function get_stripe_charge($charge_id){
    \Stripe\Stripe::setApiKey(stripe_api_key('secret'));
    try {
        $chagre = \Stripe\Charge::retrieve($invoice_id);
        return $chagre;
    } catch(\Stripe\Error\Card $e) {
        $body = $e->getJsonBody();
        $err  = $body['error'];
        return FALSE;
    } catch (\Stripe\Error\InvalidRequest $e) {
        $body = $e->getJsonBody();
        $err  = $body['error'];
        return FALSE;
    } catch (\Stripe\Error\Authentication $e) {
        $body = $e->getJsonBody();
        $err  = $body['error'];
        return FALSE;
    } catch (\Stripe\Error\ApiConnection $e) {
        $body = $e->getJsonBody();
        $err  = $body['error'];
        return FALSE;
    } catch (\Stripe\Error\Base $e) {
        $body = $e->getJsonBody();
        $err  = $body['error'];
        return FALSE;
    } catch (Exception $e) {
        return FALSE;
    }
}

function customer_subscription_created($event){
    global $wpdb;
    $stripe_cus_id = $event->data->object->customer;
    $subcription_id = $event->data->object->id;
    if($customer = get_stripe_customer($stripe_cus_id)){
        $ID = $customer->metadata->system_id;
        $display_name = $customer->metadata->system_name;
        $user_email = $customer->email;
        if($subscription = get_stripe_subscription($stripe_cus_id, $subcription_id)){
            $plan = $subscription->plan->name;
            $interval = $subscription->plan->interval_count.' '.$subscription->plan->interval;
            $amount = ($subscription->plan->amount / 100).' '.strtoupper($subscription->plan->currency);
            $status = strtoupper(strtolower($subscription->status));
            $date = $subscription->start;
        }
    }
    
    //******  A mail has been thrown after executing this code ************** //
    $from = get_option('admin_email');
    $from_name = get_option('blogname');
    $headers = "From: $from_name <$from>\r\n";
    $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
    $subject = "Your subscription has been successful.";
    $msg = "Dear $display_name,<br/><br/>";
    $msg .= "Thank you for subscription.<br/>Your subscription details are as follows<br/>";
    $msg .= "Plan: ".$plan."<br/>";
    $msg .= "Amount: ".$amount."<br/>";
    $msg .= "Interval: ".$interval."<br/>";
    $msg .= "Status: ".$status."<br/>";
    $msg .= "Subscription ID: ".$subcription_id."<br/>";
    $msg .= "Subscription Date: ".date(DATETIME_DISPLAY_FORMAT, $date)."<br/><br/>"; // date('jS M, Y h:i a')
    $msg .= "Best regards,<br/>$from_name admin";

    wp_mail( $user_email, $subject, $msg, $headers );
}

function charge_succeeded($event){
    global $wpdb;
    $stripe_cus_id = $event->data->object->customer;
    $stripe_invoice_id = $event->data->object->invoice;
    $payment_amount = ($event->data->object->amount / 100);
    $currency = strtoupper($event->data->object->currency);
    $transaction_id = $event->data->object->balance_transaction;
    $payment_status = $event->data->object->status;
    $payment_date = $event->data->object->created;
    if($customer = get_stripe_customer($stripe_cus_id)){
        $ID = $customer->metadata->system_id;
        $display_name = $customer->metadata->system_name;
        $user_email = $customer->email;
    }
    if($invoice = get_stripe_invoice($stripe_invoice_id)){
        $subcription_id = $invoice->lines->data[0]->id;
        $period_start = $invoice->lines->data[0]->period->start;
        $period_end = $invoice->lines->data[0]->period->end;
        if($subscription = get_stripe_subscription($stripe_cus_id, $subcription_id)){
            $plan = $subscription->plan->name;
            $plan_id = $subscription->plan->id;
            $interval = $subscription->plan->interval_count.' '.$subscription->plan->interval;
            $amount = ($subscription->plan->amount / 100).' '.strtoupper($subscription->plan->currency);
            $status = strtoupper(strtolower($subscription->status));
            $actual_payment = ($subscription->plan->amount / 100);
            $subscription_date = $subscription->start;
        }
    }
    
    // *********** Mail to admin ************ //
    
    $to = get_option('admin_email');
    $from = $user_email;
    $from_name = $display_name;
    $headers = "From: $from_name <$from>\r\n";
    $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
    $subject = $display_name. " has made payment for ".$plan;
    $msg = "Please find the details below.<br/><br/>";
    $msg .= "Plan: ".$plan."<br/>";
    $msg .= "Transaction ID: ".$transaction_id."<br/>";
    $msg .= "Transaction amount: ".$payment_amount." ".$currency."<br/>";
    $msg .= "Payment Date: ".date(DATETIME_DISPLAY_FORMAT, $payment_date)."<br/>";
    $msg .= "Subscription ID: ".$subcription_id."<br/>";
    $msg .= "Subscription Date: ".date(DATETIME_DISPLAY_FORMAT, $subscription_date)."<br/>";

    wp_mail( $to, $subject, $msg, $headers );

    if(get_post_meta($plan_id, 'package_price', true) == $actual_payment){
        $data = array(
            'id_user' => $ID,
            'id_package' => $plan_id,
            'transaction_id' => $transaction_id,
            'transaction_amount' => $payment_amount.' '.$currency,
            'subscription_id' => $subcription_id,
            'subscription_date' => date(DATETIME_DATABASE_FORMAT, $subscription_date),
            'subscription_status' => $status,
            'invoice_id' => $stripe_invoice_id,
            'payment_date' => date(DATETIME_DATABASE_FORMAT, $payment_date)
        );

        $stripe_payment_log_table = $wpdb->prefix.'user_payment_stripe';
        $wpdb->insert($stripe_payment_log_table, $data);
        
        update_user_meta($ID,'account_status', 1);
        update_user_meta($ID, 'account_expiry', date(DATE_DATABASE_FORMAT, $period_end));
         
        // confirmation mail to user
        $from = get_option('admin_email');
        $from_name = get_option('blogname');
        $headers = "From: $from_name <$from>\r\n";
        $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
        $subject = "Your payment has been successful completed for the subscription of $plan";
        $msg = "Dear $display_name,<br/><br/>";
        $msg .= "Your payment regrading subscription for the <strong>$plan</strong> has been successfully completed.<br/> ";
        $msg .= "Your payment details are as follows.<br/><br/>";
        $msg .= "Transaction ID: ".$transaction_id."<br/>";
        $msg .= "Transaction amount: ".$payment_amount." ".$currency."<br/>";
        $msg .= "Payment Date: ".date(DATETIME_DISPLAY_FORMAT, $payment_date)."<br/>"; // date('jS M, Y h:i a')
        $msg .= "Subscription ID: ".$subcription_id."<br/>";
        $msg .= "Subscription Date: ".date(DATETIME_DISPLAY_FORMAT, $subscription_date)."<br/><br/>";
        $msg .= "Note: Please note down the <strong>Transaction ID</strong> and <strong>Subscription ID</strong> for future communication.<br/><br/>";
        $msg .= "Best regards,<br/>$from_name admin";

        wp_mail( $user_email, $subject, $msg, $headers );
    }
}

function invoice_payment_succeeded($event){
    // after successfull payment created.
}

function customer_subscription_deleted($event){
    global $wpdb;
    $table_name = $wpdb->prefix . 'user_payment_stripe';
    $stripe_cus_id = $event->data->object->customer;
    $subcription_id = $event->data->object->id;
    $cancel_at_period_end = $event->data->object->cancel_at_period_end;
    $ended_at = $event->data->object->ended_at;
    $canceled_at = $event->data->object->canceled_at;
    $query = "SELECT * FROM `{$table_name}` WHERE `subscription_id` = '{$subcription_id}'";
    $result = $wpdb->get_results($query, ARRAY_A);
    foreach($result as $res){
        $plan_OBJ = get_post($res['id_package']);
        $plan = $plan_OBJ->post_title;
    }
    
    if($customer = get_stripe_customer($stripe_cus_id)){
        $ID = $customer->metadata->system_id;
        $display_name = $customer->metadata->system_name;
        $user_email = $customer->email;
        if($cancel_at_period_end != 1){
            update_user_meta($ID, 'account_expiry', date(DATE_DATABASE_FORMAT, $ended_at));
            $wpdb->update($table_name, array('subscription_status' => 'CANCELED', 'subscription_canceled_at' => date(DATETIME_DATABASE_FORMAT, $canceled_at)), array('subscription_id' => $subcription_id));
        }
        
        /*  Mail to admin */
        
        $to = get_option('admin_email');
        $from = $user_email;
        $from_name = $display_name;
        $headers = "From: $from_name <$from>\r\n";
        $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
        $subject = "Subscription for {$plan} has been canceled";
        $msg = "Please find the details below.<br/><br/>";
        $msg .= "Plan: ".$plan."<br/>";
        $msg .= "Subscription ID: ".$subcription_id."<br/>";
        $msg .= "Canceled at: ".date(DATETIME_DISPLAY_FORMAT, $canceled_at)."<br/>";

        wp_mail( $to, $subject, $msg, $headers );
        
        /* Mail to user */
        
        $from = get_option('admin_email');
        $from_name = get_option('blogname');
        $headers = "From: $from_name <$from>\r\n";
        $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
        $subject = "Your subscription has been successfully canceled for the plan {$plan}";
        $msg = "Dear $display_name,<br/><br/>";
        $msg .= "Your subscription has been successfully canceled for the plan <strong>$plan</strong><br/> ";
        $msg .= "Please find the details below.<br/><br/>";
        $msg .= "Plan: ".$plan."<br/>";
        $msg .= "Subscription ID: ".$subcription_id."<br/>";
        $msg .= "Canceled at: ".date(DATETIME_DISPLAY_FORMAT, $canceled_at)."<br/><br/>";
        $msg .= "Best regards,<br/>$from_name admin";

        wp_mail( $user_email, $subject, $msg, $headers );
    }
}
