<?php
/**
* User Purchase Class
*/
class CT_Purchase
{
    var $id = null;
    
    var $user_id = null;
    
    var $monthly_fee = null;
    
    var $paid_amount = null;
    
    var $card_id = null;
    
    var $created_date = null;
    
    var $expiry_date = null;
    
    var $status = null;
    
    var $inarrears_count = null;
    
    var $frozen_count = null;
    
    var $signup_fee = null;
    
    var $suites = array();
    
    public function __construct($id = null)
    {
        $this->id = $id;
        
        $this->load();    
    }
    
    /**
    * Load Data from ID
    * 
    */
    public function load()
    {
        global $wpdb;
        
        if($this->id)
        {
            $query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}users_purchases WHERE id=%d", $this->id);
            $row = $wpdb->get_row($query, ARRAY_A);
            if(!$row)
                $this->id = null;
            else
            {
                foreach($row as $k=>$v)
                {
                    $this->$k = $v;
                }                
                
                //Getting subscripted test suite ids with this purchase
                $query = $wpdb->prepare("SELECT suite_id FROM {$wpdb->prefix}users_purchases WHERE purchase_id=%d", $this->id);
                $this->suites = $wpdb->get_col($query);
                
                if($this->suites)
                {
                    //Getting Monthly Fee
                    $suiteMonthlyFee = doubleval(get_post_meta($this->suites[0], 'monthly_subscription_price', true));
                    $monthlyFee = min($suiteMonthlyFee, $this->monthly_fee);
                    
                    $userMonthlyFees = get_user_meta($this->user_id, 'monthly_fee', true);    
                    if(isset($userMonthlyFees[$subscription->suite_id]))
                        $monthlyFee = doubleval($userMonthlyFees[$subscription->suite_id]);
                        
                    $this->monthly_fee = $monthlyFee;
                }
            }
        }
    }
    
    /**
    * Subscription is in Arriears
    * 
    */
    public function inArrears()
    {
        //Update subscription status
        $wpdb->update($wpdb->prefix . 'users_purchases', array('status' => 'InArrears', 'inarrears_count' => 1, 'frozen_count' => 0), array('id' => $this->id));
        //Update Subscription Status
        $wpdb->update($wpdb->prefix . 'users_subscriptions', array('status' => 'InArrears'), array('purchase_id' => $this->id));
        //Update Card Status
        $wpdb->update($wpdb->prefix . 'users_cards', array('status' => 'Suspended'), array('id' => $this->card_id));
        
        //Send Email Notification
        $user = get_userdata($this->user_id);
        
        //Send Mail
        $emailData = array(
            '[name]' => $user->first_name . " " . $user->last_name,
            '[email]' => $user->user_email,
            '[suite_name]' => get_the_title($this->suite_id),
            '[suite_url]' => get_permalink($this->suite_id),
            '[monthly_fee]' => $this->monthly_fee,
            '[paid_amount]' => $this->paid_amount
        );
        
        cp_send_email(array('name' => $emailData['[name]'], 'email' => $emailData['[email]']), 'inarrears_subscription', $emailData);
        cp_send_email_to_admin('inarrears_subscription_admin', $emailData);         
    }
    
    
    public function frozen()
    {
        global $wpdb;
        if($this->id)
        {
            //Update subscription status
            $wpdb->update($wpdb->prefix . 'users_purchases', array('status' => 'Frozen', 'frozen_count' => 1), array('id' => $this->id));            
            //Update Subscription Status
            $wpdb->update($wpdb->prefix . 'users_subscriptions', array('status' => 'Frozen'), array('purchase_id' => $this->id));    
            //Send Email Notification
            $user = get_userdata($this->user_id);
            
            //Send Mail
            $emailData = array(
                '[name]' => $user->first_name . " " . $user->last_name,
                '[email]' => $user->user_email,
                '[suite_name]' => get_the_title($this->suite_id),
                '[suite_url]' => get_permalink($this->suite_id),
                '[monthly_amount]' => $this->monthly_fee,
                '[paid_amount]' => $this->paid_amount
            );
            
            cp_send_email(array('name' => $emailData['[name]'], 'email' => $emailData['[email]']), 'frozen_subscription', $emailData);
            cp_send_email_to_admin('frozen_subscription_admin', $emailData);            
        }
    }
}