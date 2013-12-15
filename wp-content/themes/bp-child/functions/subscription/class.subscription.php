<?php
/**
* Subscription Class
*/

class CT_Subscription
{
    var $id = null;
    
    var $status = null;
    
    var $user_id = null;
    
    var $suite_id = null;
    
    var $expiry_date = null;
    
    var $created_date = null;
    
    var $price = null;
    
    var $paid_amount = null;
    
    var $customer_id = null;
    
    var $card_id = null;
    
    var $esb_user_id = null;
    
    var $harness_username = null;
    var $harness_password = null;
    var $harness_endpoint_url = null;
    
    var $p_mode_agreement = null;
    
    var $tester_username = null;
    var $tester_password = null;
    var $tester_endpoint_url = null;
    
    public function __construct($id = null)
    {
        $this->id = $id;
        
        $this->load();
        
        return;
    }
    
    function load()
    {
        global $wpdb;
        
        if($this->id)
        {
            $query = $wpdb->prepare("SELECT p.*, c.customer_id FROM {$wpdb->prefix}users_purchases AS p LEFT JOIN {$wpdb->prefix}users_cards AS c ON c.id=p.card_id WHERE p.id=%d", $this->id);
            
            $row = $wpdb->get_row($query, ARRAY_A);
            if($row)
            {
               foreach($row as $f=>$v) 
               {                   
                   $this->$f = $v;
               }
               //Getting Card
            }else{
                $this->id = null;
                return null;
            }
            
        }
    }
    
    function bind($data)
    {
        foreach($data as $f=>$v) 
       {                   
           $this->$f = $v;
       }
    }
    
    /**
    * Cancel Subscription
    * Update the subscription to Unsubscribing Status
    * 
    */
    function cancel()
    {
        global $wpdb;
        if($this->id)
        {
            //Update subscription status
            $wpdb->update($wpdb->prefix . 'users_purchases', array('status' => 'Unsubscribing'), array('id' => $this->id));
            
            //Send Email Notification
            $user = get_userdata($this->user_id);
        
            //Send Subscription UnSubscribing Mails
            
            $suite = new TestSuite($this->suite_id);
            $suite->load();
            //Send Mail
            $emailData = array(
                '[name]' => cp_get_user_fullname($user->ID),
                '[email]' => $user->user_email,
                '[suite_name]' => get_the_title($this->suite_id),
                '[suite_url]' => get_permalink($this->suite_id),
            );
            
            cp_send_email(array('name' => $emailData['[name]'], 'email' => $emailData['[email]']), 'unsubscribing', $emailData);
            cp_send_email_to_admin('unsubscribing_admin', $emailData);            
        }
    }
    
    
    /**
    * Set Subscription status to InArrears 
    * 
    */
    function inArrears()
    {
        global $wpdb;
        if($this->id)
        {
            //Update subscription status
            $wpdb->update($wpdb->prefix . 'users_purchases', array('status' => 'InArrears', 'inarrears_count' => 1, 'frozen_count' => 0), array('id' => $this->id));
            //Update Card Status
            $wpdb->update($wpdb->prefix . 'users_cards', array('status' => 'Suspended'), array('id' => $this->card_id));
            //Send Email Notification
            $user = get_userdata($this->user_id);
        
            $cur_suite_price = get_post_meta($this->suite_id, 'monthly_subscription_price', true);
            if($this->price > $cur_suite_price)
                $this->price = $cur_suite_price;
            
            //Send Mail
            $emailData = array(
                '[name]' => $user->first_name . " " . $user->last_name,
                '[email]' => $user->user_email,
                '[suite_name]' => get_the_title($this->suite_id),
                '[suite_url]' => get_permalink($this->suite_id),
                '[paid_amount]' => $this->price
            );
            
            cp_send_email(array('name' => $emailData['[name]'], 'email' => $emailData['[email]']), 'inarrears_subscription', $emailData);
            cp_send_email_to_admin('inarrears_subscription_admin', $emailData);            
        }
    }
    
    /**
    * Set Subscription status to Frozen
    * 
    */
    function frozen()
    {
        global $wpdb;
        if($this->id)
        {
            //Update subscription status
            $wpdb->update($wpdb->prefix . 'users_purchases', array('status' => 'Frozen', 'frozen_count' => 1), array('id' => $this->id));            
            //Send Email Notification
            $user = get_userdata($this->user_id);
            //Send Mail
            $cur_suite_price = get_post_meta($this->suite_id, 'monthly_subscription_price', true);
            if($this->price > $cur_suite_price)
                $this->price = $cur_suite_price;
            
            //Send Mail
            $emailData = array(
                '[name]' => $user->first_name . " " . $user->last_name,
                '[email]' => $user->user_email,
                '[suite_name]' => get_the_title($this->suite_id),
                '[suite_url]' => get_permalink($this->suite_id),
                '[paid_amount]' => $this->price
            );
            
            cp_send_email(array('name' => $emailData['[name]'], 'email' => $emailData['[email]']), 'frozen_subscription', $emailData);
            cp_send_email_to_admin('frozen_subscription_admin', $emailData);            
        }
    }
    
    /**
    * Remove Subscription and Test Plans, Compliance Claims
    * 
    */
    function delete()
    {
        global $wpdb;
        
        //Remove subscription
        $wpdb->delete($wpdb->prefix . 'users_purchases', array('id' => $this->id));
        //Remove Test Plans
        $wpdb->delete($wpdb->prefix . 'test_plans', array('suite_id' => $this->suite_id, 'creator_id' => $this->user_id));
        //Remove Compliance Claims
        $wpdb->delete($wpdb->prefix . 'compliance_claims', array('suite_id' => $this->suite_id, 'creator_id' => $this->user_id));
        
        $user = get_userdata($this->user_id);
        
        $cur_suite_price = get_post_meta($this->suite_id, 'monthly_subscription_price', true);
        if($this->price > $cur_suite_price)
            $this->price = $cur_suite_price;
        
        //Send Mail
        $emailData = array(
            '[name]' => $user->first_name . " " . $user->last_name,
            '[email]' => $user->user_email,
            '[suite_name]' => get_the_title($this->suite_id),
            '[suite_url]' => get_permalink($this->suite_id),
            '[paid_amount]' => $this->price
        );
        
        cp_send_email(array('name' => $emailData['[name]'], 'email' => $emailData['[email]']), 'cancel_subscription', $emailData);
        cp_send_email_to_admin('cancel_subscription_admin', $emailData);            
    }
    
    /**
    * Active Subscription and Test Plans, Compliance Claims
    * 
    */
    function active()
    {
        global $wpdb;
        
        //Extend the period of the subscription
        $wpdb->update($wpdb->prefix . "users_purchases", array('status'=>'Active', 'expiry_date' => date("Y-m-d", strtotime('first day next month'))), array('id' => $this->id));
        
        $user = get_userdata($this->user_id);
        
        $cur_suite_price = get_post_meta($this->suite_id, 'monthly_subscription_price', true);
        if($this->price > $cur_suite_price)
            $this->price = $cur_suite_price;
        
        //Send Mail
        $emailData = array(
            '[name]' => $user->first_name . " " . $user->last_name,
            '[email]' => $user->user_email,
            '[suite_name]' => get_the_title($this->suite_id),
            '[suite_url]' => get_permalink($this->suite_id),
            '[paid_amount]' => $this->price
        );
        
        cp_send_email(array('name' => $emailData['[name]'], 'email' => $emailData['[email]']), $this->status == 'InArrears' ? 'active_subscription' : 'active_subscription2', $emailData);
        cp_send_email_to_admin($this->status == 'InArrears' ? 'active_subscription_admin' : 'active_subscription2_admin', $emailData);            
    }
    
}