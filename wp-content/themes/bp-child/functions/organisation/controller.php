<?php
/**
* Organisation Controller
* Process All actions are related with Organisation
*/

class CT_Organisation_Controller
{
    public $last_message = '';
    
    /**
    * Create Subscriptions for Organisation
    * 
    */
    public function subscribe($family_mark, $payment_method, $nickname, $user_id)
    {
        global $wpdb;
        
        //Get organisation id of which the user is assigned
        $query = $wpdb->prepare("SELECT organisation_id FROM {$wpdb->prefix}organisations_members WHERE user_id=%d AND is_admin=1", $user_id);
        $organisation_id = $wpdb->get_var($query);
        
        if (!$organisation_id) {
            $this->last_message = "Only an organisation admin can purchase subscription.";
            return false;
        }
        
        
        $query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}test_suites WHERE family_mark=%d ORDER BY version_major DESC, version_minor DESC, version_patch DESC LIMIT 1", $family_mark);
        $suite_info = $wpdb->get_row($query);
        
        if (!$suite_info) {
            $this->last_message = "Test suite id is not correct.";
            return false;
        }
        
        $query = $wpdb->prepare("SELECT id FROM {$wpdb->prefix}organisations_payment_methods WHERE organisation_id=%d AND id=%d", $organisation_id, $payment_method);
        $payment_method = $wpdb->get_var($query);
        
        if (!$payment_method) {
            $this->last_message = "Payment method is not valid.";
            return false;
        }
        
        if (!$nickname) {
            $this->last_message = "Nickname should not be empty.";
            return false;
        }
        
        $query = $wpdb->prepare("SELECT count(os.id) FROM {$wpdb->prefix}organisations_subscriptions AS os                             
            WHERE os.organisation_id=%d AND os.suite_family_mark=%d", $organisation_id, $family_mark);
        $pCount = $wpdb->get_var($query);
        
        //Nickname should be unique        
        $n_nickname = $nickname;
        $i = 0;
        do {
            $query = $wpdb->prepare("SELECT id FROM {$wpdb->prefix}organisations_subscriptions WHERE nickname=%s AND organisation_id=%d", $n_nickname, $organisation_id);            
            if (!$wpdb->get_var($query)) {
                break;
            }       
            $i++;
            $n_nickname = $nickname . "_" . $i;
        } while (1);
        
        
        //Create Organisation Subscription
        $data = array(
            array(
                'nickname'   =>  $n_nickname, 
                'organisation_id'   =>  $organisation_id, 
                'purchased_date'    =>  date('Y-m-d H:i:s'), 
                'status'            =>  'Active', 
                'suite_family_mark' =>  $family_mark, 
                'payment_method'    =>  $payment_method, 
                'purchaser_id'      =>  $user_id,
                'user_id'           =>  0
            ),
            array(
                '%s',
                '%d',
                '%s',
                '%s',
                '%d',
                '%d',
                '%d',
                '%d'
            )
        );
        
        if (!$wpdb->insert($wpdb->prefix . "organisations_subscriptions", $data[0], $data[1])) {
            $this->last_message = "Saving subscription error: " . $wpdb->last_error;
            return false;
        }
        
        $subscription_id = $wpdb->insert_id;
        
        $suite_class = new TestSuite($suite_info->suite_id);
        
        $sign_price_code = $suite_class->loadSingleValue('signup_price');
        $monthly_price_code = $suite_class->loadSingleValue('monthly_subscription_price');
        
        //Create Charge Table
        $query = $wpdb->prepare("SELECT no_billing FROM {$wpdb->prefix}organisations WHERE id=%d", $organisation_id);
        $no_billing = $wpdb->get_var($query);
        
        if ($no_billing != '1')
        {
            $charge_data = array(
                array(
                    'organisation_id'       => $organisation_id,
                    'payment_id'            => $payment_method,
                    'item_code'             => $sign_price_code,
                    'quantity'              => 1,
                    'start_date'            => date("Y-m-d H:i:s"),
                    'end_date'              => date("Y-m-d", strtotime('last day of this month')),
                    'reference_type'        => 'subscription',
                    'reference_id'          => $subscription_id,
                    'invoice_number'    => '',
                    'is_paid'               => 0,
                    'comment'               => ''
                ),
                array('%d', '%d', '%s', '%d', '%s', '%s', '%s', '%d', '%s', '%d', '%s')
            );
            
            $wpdb->insert($wpdb->prefix . "organisations_charge", $charge_data[0], $charge_data[1]);
            
            $charge_data = array(
                array(            
                    'organisation_id'       => $organisation_id,
                    'payment_id'            => $payment_method,
                    'item_code'             => $monthly_price_code,
                    'quantity'              => ct_calculate_first_month_quantity(1),
                    'start_date'            => date("Y-m-d H:i:s"),
                    'end_date'              => date("Y-m-d", strtotime('last day of this month')),
                    'reference_type'        => 'subscription',
                    'reference_id'          => $subscription_id,
                    'invoice_number'    => '',
                    'is_paid'               => 0,
                    'comment'               => date("F Y")
                ),
                array('%d', '%d', '%s', '%s', '%s', '%s', '%s', '%d', '%s', '%d', '%s')
            );
            
            $wpdb->insert($wpdb->prefix . "organisations_charge", $charge_data[0], $charge_data[1]);
        }
        
        $user = get_userdata($user_id);
        
        //Sending Email
        
        $suite = new TestSuite($family_mark);
        $suite->load();
        
        $group = groups_get_group(array('group_id' => $suite->community_id));
        $card = ct_get_payment_method_by_id($payment_method);
        
        $paymentAmount = $suite->signupPriceValue + calculateFirstPaymentAmount($suite->monthlySubscriptionPriceValue);
        
        $organisation = new CT_Organisation($organisation_id);
        
        $emailData = array(
            '[name]' => $user->first_name . " " . $user->last_name,
            '[email]' => $user->user_email,
            '[suite_name]' => $suite->name,
            '[paid_amount]' => $paymentAmount,
            '[nickname]'        => $nickname,
            '[organisation]'    => $organisation->organisation_name,
            '[community_url]' => bp_get_group_permalink($group),
            '[payment_email]' => $card->email
        );
        cp_send_email(array('name' => $emailData['[name]'], 'email' => $emailData['[email]']), 'purchase_subscription', $emailData);
        cp_send_email_to_admin('purchase_subscription_admin', $emailData);        
        
        return true;
        
    }
    
    public function send_subscription_request($user_id, $suite_id)
    {
        global $wpdb;
        
        $organisation = ct_get_user_organisation($user_id);
        $organisation_admin = ct_get_organisation_admin($organisation->id);
        
        if ($organisation_admin) {        
            $requester = get_userdata($user_id);
            
            $email_data = array(
                '[organisation_name]' => $organisation->organisation_name,
                '[requester_name]' => $requester->first_name . " " . $requester->last_name,
                '[requester_email]' => $requester->user_email,
                '[suite_name]' => get_the_title($suite_id),
                '[suite_url]' => get_permalink($suite_id),
                '[admin_name]' => $organisation_admin->display_name,
                '[admin_email]' => $organisation_admin->user_email
            );
            
            cp_send_email(array('email' => $organisation_admin->user_email, 'name' => $organisation_admin->display_name), 'request_subscription_to_admin', $email_data);
            
            return true;
        } else {
            $this->last_message = "There was an error while processing your request.";
            return false;
        }
    }
    
    public function send_signup_organisation_request($user_id)
    {
        global $wpdb;
        
        $requester = get_userdata($user_id);
        
        $email_data = array(
            '[requester_name]' => $requester->first_name . " " . $requester->last_name,
            '[requester_email]' => $requester->user_email,
            '[organisation]' => get_user_meta($user_id, 'user_organisation', true),
            '[organisation_website]' => get_user_meta($user_id, 'user_organisation_web', true),
            '[organisation_description]' => get_user_meta($user_id, 'user_organisation_desc', true),
            '[organisation_abn]' => get_user_meta($user_id, 'user_organisation_abn', true)
        );
        
        cp_send_email_to_admin('send_organisation_signup_request_to_admin', $email_data);
        
        return true;
        
    }
    
    public function save_subscription($subscription_id, $nickname)
    {
        global $wpdb;
        
        //Getting Detail
        $query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}organisations_subscriptions WHERE id = %d", $subscription_id);
        $subscription = $wpdb->get_row($query);
        
        $n_nickname = $nickname;
        $i = 0;
        do {
            $query = $wpdb->prepare("SELECT id FROM {$wpdb->prefix}organisations_subscriptions WHERE id <> %d AND nickname=%s AND organisation_id=%d", $subscription_id, $n_nickname, $subscription->id);
            if (!$wpdb->get_var($query)) {
                break;
            }       
            $i++;
            $n_nickname = $nickname . "_" . $i;
        } while (1);
        
        $query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}organisations_subscriptions WHERE id=%d", $subscription_id);
        $subscription = $wpdb->get_row($query);
        
/*        if ($assignee != $subscription->user_id) {
            //Remove Old Subscription
            $query = $wpdb->prepare("DELETE FROM {$wpdb->prefix}users_subscriptions WHERE parent_id=%d", $subscription_id);
            $wpdb->prepare($query);
        }*/
        
        $wpdb->update($wpdb->prefix . "organisations_subscriptions", 
                      array('nickname' => $n_nickname),
                      array('id' => $subscription_id),
                      array('%s', '%d'), array('%d')
        );
        
        return true;
    }
    
    public function allocate_subscription_to_user($user_id, $organisation_id, $family_mark)   
    {
        global $wpdb;
        
        $query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}organisations_subscriptions WHERE organisation_id=%d AND suite_family_mark=%d AND user_id=0 ORDER BY nickname LIMIT 1", $organisation_id, $family_mark);
        $subscription = $wpdb->get_row($query);
        
        if (!$subscription) {
            $this->last_message = "There is not unallocated subscription now. Please request a subscription from your organisation administrator.";
            return false;
        }
        
        //Assign it to the user
        $wpdb->update($wpdb->prefix . "organisations_subscriptions", array('user_id' => $user_id), array('id' => $subscription->id), array('%d'), array('%d'));
        
        $user = get_userdata($user_id);
        
        $suite = new TestSuite($family_mark);
        $suite->load();
        
        $group = groups_get_group(array('group_id' => $suite->community_id));
        
        $organisation = new CT_Organisation($organisation_id);
        
        //Send Email
        $emailData = array(
            '[name]'            => $user->first_name . " " . $user->last_name,
            '[email]'           => $user->user_email,
            '[suite_name]'      => $suite->name,
            '[nickname]'        => $subscription->nickname,
            '[organisation]'    => $organisation->organisation_name,
            '[community_url]'   => bp_get_group_permalink($group)
        );
        
        cp_send_email(array('name' => $emailData['[name]'], 'email' => $emailData['[email]']), 'allocate_subscription_to_user', $emailData);
        cp_send_email_to_admin('allocate_subscription_to_user_admin', $emailData);
        
        return $sid;
    }
    
    public function create_user_harness_detail($user_id, $suite_id, $organisation_id, $parent_id)
    {
        global $wpdb;
        
        $data = array(
            array(
                'organisation_id' => $organisation_id,
                'user_id' => $user_id,
                'suite_id' => $suite_id,
                'parent_id' => $parent_id,
                'created_date' => date('Y-m-d H:i:s'),
                'p_mode_agreement' => 'LIGHT',
                'harness_endpoint_url' => HARNESS_ENDPOINT_URL, 
                'harness_username' => "harness" . $user_id . "_" . $suite_id, 
                'harness_password' => cp_generate_password(8),
                'tester_username' => '',
                'tester_password' => '',
                'tester_endpoint_url' => '',            
                'harness_key' => '',            
                'harness_certificate' => '',            
                'harness_certificate_p12' => '',            
                'profile_id' => 'NULL',            
                'entity_id' => '',            
                'entity_type' => '',            
                'gateway_id' => 'NULL',            
                'alias' => ''
            ),
            array('%d', '%d', '%d', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')
        );
        
        add_filter( 'query', 'wp_db_null_value' );
        $wpdb->insert($wpdb->prefix . "users_subscriptions", $data[0], $data[1]);
        remove_filter( 'query', 'wp_db_null_value' );
    }
    
    public function delete_user_subscription($user_id, $subscription_id)
    {
        global $wpdb;
        
        //Remove harness detail
        $wpdb->delete($wpdb->prefix . "users_subscriptions", array('user_id' => $user_id, 'parent_id' => $subscription_id), array('%d', '%d'));
        
        //Release the organisation subscription
        $wpdb->update($wpdb->prefix . "organisations_subscriptions", array('user_id' => 0), array('id' => $subscription_id), array('%d'), array('%d'));
        
    }
    
    
    
    public function delete_organisation_subscription($subscription_id)
    {
        global $wpdb;
        
        $subscription = ct_get_organisation_subscription_by_id($subscription_id);
        $organisation = new CT_Organisation($subscription->organisation_id);
        
        $orgAdmin = get_userdata($organisation->admin_id);
        
        $suite = new TestSuite($subscription->suite_family_mark);
        $suite->load();
        
        //Remove harness detail
        $wpdb->delete($wpdb->prefix . "users_subscriptions", array('parent_id' => $subscription_id), array('%d'));
        
        //Delete the organisation subscription
        $wpdb->delete($wpdb->prefix . "organisations_subscriptions", array('id' => $subscription_id), array('%d'));
        
        //Sending Cancel Unsubscribing Email        
        $emailData = array(
            '[name]' => $orgAdmin->first_name . " " . $orgAdmin->last_name,
            '[email]' => $orgAdmin->user_email,
            '[nickname]' => $subscription->nickname,
            '[organisation]' => $organisation->organisation_name,
            '[suite_name]' => $suite->name
        );
        
        cp_send_email(array('name' => $emailData['[name]'], 'email' => $emailData['[email]']), 'cancel_subscription', $emailData);
        cp_send_email_to_admin('cancel_subscription_admin', $emailData);

    }
    
    public function unsubscribe_organisation_subscription($subscription_id)
    {
        global $wpdb;
        
        //Release the organisation subscription
        $wpdb->update($wpdb->prefix . "organisations_subscriptions", array('status' => 'Unsubscribing'), array('id' => $subscription_id), array('%s'), array('%d'));
        
        $subscription = ct_get_organisation_subscription_by_id($subscription_id);
        $organisation = new CT_Organisation($subscription->organisation_id);
        
        $orgAdmin = get_userdata($organisation->admin_id);
        
        $suite = new TestSuite($subscription->suite_family_mark);
        $suite->load();
        
        $organisation = new CT_Organisation($subscription->organisation_id);
        
        //Sending Unsubscribing Email
        $emailData = array(
            '[name]' => $orgAdmin->first_name . " " . $orgAdmin->last_name,
            '[email]' => $orgAdmin->user_email,
            '[nickname]' => $subscription->nickname,
            '[organisation]' => $organisation->organisation_name,
            '[suite_name]' => $suite->name            
        );

        cp_send_email(array('name' => $emailData['[name]'], 'email' => $emailData['[email]']), 'unsubscribing', $emailData);
        cp_send_email_to_admin('unsubscribing_admin', $emailData);
        
    }
}
