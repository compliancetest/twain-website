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
    public function subscribe($user_id, $data)
    {
        global $wpdb;
        
        //Get organisation id of which the user is assigned
        $query = $wpdb->prepare("SELECT organisation_id FROM {$wpdb->prefix}organisations_members WHERE user_id=%d AND is_admin=1", $user_id);
        $organisation_id = $wpdb->get_var($query);
        
        if (!$organisation_id) {
            $this->last_message = "Only an organisation admin can purchase subscription.";
            return false;
        }
        
        $suite_id = $data['suite_id'];
        $query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}test_suites WHERE suite_id=%d", $suite_id);
        $suite_family_info = $wpdb->get_row($query);
        
        if (!$suite_family_info) {
            $this->last_message = "Test suite id is not correct.";
            return false;
        }
        
        //Check if the user already purchased
        if( ct_is_organisation_purchased_subscription($organisation_id, $suite_id) )
        {
            $this->last_message = "You already purchased a subscription to the test suite.";
            return false;
        }
        
        //The suite id should be the latest one of the major version
        $query = $wpdb->prepare("SELECT suite_id FROM {$wpdb->prefix}test_suites WHERE version_major=%d AND family_mark=%d ORDER BY version_minor DESC, version_patch DESC LIMIT 1", 
                                    $suite_family_info['version_major'], $suite_family_info['family_mark']);
        $last_suite_id = $wpdb->get_var($query);
        
        if ($last_suite_id != $suite_id) {
            $this->last_message = "Test suite id is not correct.";
            return false;
        }
        
        //Create Organisation Subscription
        $data = array(
            array(
                'organisation_id'   =>  $organisation_id, 
                'purchased_date'    =>  date('Y-m-d H:i:s'), 
                'status'            =>  'Active', 
                'suite_family_mark' =>  $suite_family_info['family_mark'], 
                'payment_method'    =>  $data['card_id'], 
                'user_id'           =>  $user_id 
            ),
            array(
                '%d',
                '%s',
                '%s',
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
        
        $suite_class = new TestSuite($suite_family_info['suite_id']);
        
        $sign_price_code = $suite_class->loadSingleValue('signup_price');
        $monthly_price_code = $suite_class->loadSingleValue('monthly_subscription_price');
        
        //Create Charge Table
        $charge_data = array(
            array(
                'organisation_id'       => $organisation_id,
                'payment_id'            => $data['card_id'],
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
        
        $wpdb->insert($wpdb->prefix . "organisations_charges", $charge_data[0], $charge_data[1]);
        
        $charge_data = array(
            array(            
                'organisation_id'       => $organisation_id,
                'payment_id'            => $data['card_id'],
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
        
        $wpdb->insert($wpdb->prefix . "organisations_charges", $charge_data[0], $charge_data[1]);
        
        return true;
        
    }
    
}
