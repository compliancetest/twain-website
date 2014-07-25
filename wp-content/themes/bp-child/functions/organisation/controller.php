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
            WHERE os.organisation_id=%d AND os.suite_family_mark=%d", $organisation_id, $suite_family_mark);
        $pCount = $wpdb->get_var($query);
        
        //Check if the user already purchased
        if( $pCount > 0 )
        {
            $nickname .= ($pCount + 1);            
        }
        
        //Create Organisation Subscription
        $data = array(
            array(
                'nickname'   =>  $nickname, 
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
        $charge_data = array(
            array(
                'organisation_id'       => $organisation_id,
                'payment_id'            => $payment_method,
                'item_code'             => $sign_price_code,
                'quantity'              => 1,
                'start_date'            => date("Y-m-d H:i:s"),
                'end_date'              => date("Y-m-d", strtotime('first day next month')),
                'reference_type'        => 'subscription',
                'reference_id'          => $subscription_id,
                'invoice_identifier'    => '',
                'is_paid'               => 0,
                'comment'               => 'Subscription Signup Fee'
            ),
            array('%d', '%d', '%s', '%d', '%s', '%s', '%s', '%d', '%s', '%d', '%s')
        );
        
        $wpdb->insert($wpdb->prefix . "organisations_charge", $charge_data[0], $charge_data[1]);
        
        $charge_data = array(
            array(            
                'organisation_id'       => $organisation_id,
                'payment_id'            => $payment_method,
                'item_code'             => $monthly_price_code,
                'quantity'              => 1,
                'start_date'            => date("Y-m-d H:i:s"),
                'end_date'              => date("Y-m-d", strtotime('first day next month')),
                'reference_type'        => 'subscription',
                'reference_id'          => $subscription_id,
                'invoice_identifier'    => '',
                'is_paid'               => 0,
                'comment'               => 'Monthly Subscription Fee'
            ),
            array('%d', '%d', '%s', '%d', '%s', '%s', '%s', '%d', '%s', '%d', '%s')
        );
        
        $wpdb->insert($wpdb->prefix . "organisations_charge", $charge_data[0], $charge_data[1]);
        
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
                '[requester_name]' => $requester->first_name . " " . $requester->last_name,
                '[requester_email]' => $requester->user_email,
                '[suite_name]' => get_the_title($suite_id),
                '[suite_url]' => get_permalink($suite_id),
                '[admin_name]' => $organisation_admin->display_name,
                '[admin_email]' => $organisation_admin->user_email
            );
            
            cp_send_email(array('email' => $organisation_admin->user_email, 'name' => $organisation_admin->display_name), $email_data);
            
            return true;
        } else {
            $this->last_message = "There was an error while processing your request.";
            return false;
        }
    }
    
    
    
}
