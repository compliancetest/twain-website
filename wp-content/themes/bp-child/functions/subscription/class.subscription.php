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
    
    var $monthly_fee = null;
    
    var $signup_fee = null;
    
    var $purchase_id = null;
    
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
            $query = $wpdb->prepare("SELECT s.*, p.monthly_fee, p.signup_fee, p.paid_amount, p.card_id, p.created_date, p.expiry_date,p.inarrears_count, p.frozen_count, c.customer_id FROM {$wpdb->prefix}users_subscriptions AS s
                                     LEFT JOIN {$wpdb->prefix}users_purchases AS p ON p.id = s.purchase_id
                                     LEFT JOIN {$wpdb->prefix}users_cards AS c ON c.id=p.card_id                                      
                                     WHERE s.id=%d", $this->id);
            
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
    
    function unsubscribing()
    {
        global $wpdb;
        
        if($this->id)
        {
            //Update subscription status
            $wpdb->update($wpdb->prefix . 'users_subscriptions', array('status' => 'Unsubscribing'), array('id' => $this->id));
            
            //Send Email Notification
            $user = get_userdata($this->user_id);
            
            $monthlyFee = getSubscriptionMonthlyFee($this, $this->user_id);
        
            //Send Subscription UnSubscribing Mails            
            $suite = new TestSuite($this->suite_id);
            $suite->load();
            
            //Send Mail
            $emailData = array(
                '[name]' => cp_get_user_fullname($user->ID),
                '[email]' => $user->user_email,
                '[suite_name]' => get_the_title($this->suite_id),
                '[paid_amount]' => $this->paid_amount,
                '[signup_fee]' => $this->signup_fee,
                '[monthly_fee]' => $monthlyFee,
                '[suite_url]' => get_permalink($this->suite_id),
            );

            cp_send_email(array('name' => $emailData['[name]'], 'email' => $emailData['[email]']), 'unsubscribing', $emailData);
            cp_send_email_to_admin('unsubscribing_admin', $emailData);
        }
    }
    
    /**
    * Unsubscribing
    * Update the subscription to Unsubscribing Status
    * 
    */
    function cancel()
    {
        global $wpdb, $CPRest;
        
        if($this->id)
        {
            $user = get_userdata(get_current_user_id());
    
            if(intval($this->esb_user_id) > 0)
            {
                //Remove Backend Account
                $data = '<api:deleteUserRequest xmlns:api="http://compliancetest.net/api">
                            <api:user>
                                <api:userId>' . $this->esb_user_id . '</api:userId>                        
                            </api:user>
                        </api:deleteUserRequest>';
                
                $result = $CPRest->doUserAPI('user/delete', $data);
                
                $resultDoc = new DOMDocument(); 
            }
            
            if(intval($this->esb_user_id) > 0 && (!$result || !$resultDoc->loadXML($result)))
            {
                addMessage("There was a problem deleting your test credentials.", "error");
                
            }else{
                if(intval($this->esb_user_id) > 0 && $resultDoc->getElementsByTagName('code')->item(0)->nodeValue == 'ERROR')
                {
                    addMessage('There was a problem deleting your test credentials: ' . $resultDoc->getElementsByTagName('error')->item(0)->nodeValue, 'error');
                }else{                        
                    //Remove Subscription
                    $this->delete();            
                    
                    addMessage('Your subscription has been cancelled.');
                }
            }
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
            if($this->monthly_fee > $cur_suite_price)
                $this->monthly_fee = $cur_suite_price;
            
            //Send Mail
            $emailData = array(
                '[name]' => $user->first_name . " " . $user->last_name,
                '[email]' => $user->user_email,
                '[suite_name]' => get_the_title($this->suite_id),
                '[suite_url]' => get_permalink($this->suite_id),
                '[paid_amount]' => $this->monthly_fee
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
            if($this->monthly_fee > $cur_suite_price)
                $this->monthly_fee = $cur_suite_price;
            
            //Send Mail
            $emailData = array(
                '[name]' => $user->first_name . " " . $user->last_name,
                '[email]' => $user->user_email,
                '[suite_name]' => get_the_title($this->suite_id),
                '[suite_url]' => get_permalink($this->suite_id),
                '[paid_amount]' => $this->monthly_fee
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
        
        $purchase = new CT_Purchase($this->purchase_id);
        $purchase->load();
        
        //Remove subscription
        $wpdb->delete($wpdb->prefix . 'users_subscriptions', array('id' => $this->id));
        
        $remainingSubscriptions = getNumSubscriptions($this->purchase_id);
        if(!$remainingSubscriptions)
        {
            //Remove Purchases
            $wpdb->delete($wpdb->prefix . 'users_purchases', array('id' => $this->purchase_id));
            //Remove transactions
            $wpdb->delete($wpdb->prefix . 'users_transactions', array('purchase_id' => $this->purchase_id));            
        }
        
        //Remove Test Plans
        $wpdb->delete($wpdb->prefix . 'test_plans', array('suite_id' => $this->suite_id, 'creator_id' => $this->user_id));
        //Remove Compliance Claims
        $wpdb->delete($wpdb->prefix . 'compliance_claims', array('suite_id' => $this->suite_id, 'creator_id' => $this->user_id));
        
        $suite = new TestSuite($this->suite_id);
        $suite->loadfamilyMark();
        
        //Check if Organisation Subscription Data exist
        $query = "SELECT * FROM {$wpdb->prefix}users_organisation_subscriptions WHERE subscription_id=" . $this->id;
        $orow = $wpdb->get_row($query);
        if($orow)
        {
            $wpdb->delete($wpdb->prefix . "users_organisation_subscriptions", array('subscription_id=' . $this->id));
            //Decrease the joined_user
            $wpdb->query("UPDATE {$wpdb->prefix}users_organisation_pricing SET `joined_count`=`joined_count` - 1 WHERE user_id=" . $purchase->user_id . " AND family_mark=" . $suite->familyMark);
        }
        
        $user = get_userdata($this->user_id);
        
        $monthlyFee = getSubscriptionMonthlyFee($this, $this->user_id);
        
        //Send Mail
        $emailData = array(
            '[name]' => cp_get_user_fullname($user->ID),
            '[email]' => $user->user_email,
            '[suite_name]' => get_the_title($this->suite_id),
            '[paid_amount]' => $this->paid_amount,
            '[signup_fee]' => $this->signup_fee,
            '[monthly_fee]' => $monthlyFee,
            '[suite_url]' => get_permalink($this->suite_id),
        );
        
        
        
        //Getting Email Template
        if(isPurchasedForOtherVersions($suite->familyMark)) //Cancel Additional Version
        {
            $email_template = 'cancel_additional_subscription';
        }else{
            if($this->monthly_fee > 0)
            {            
                $email_template = 'cancel_subscription';
            }else{
                $email_template = 'cancel_free_subscription';
            }
        }
        
        cp_send_email(array('name' => $emailData['[name]'], 'email' => $emailData['[email]']), $email_template, $emailData);
        cp_send_email_to_admin($email_template.'_admin', $emailData);
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
        if($this->monthly_fee > $cur_suite_price)
            $this->monthly_fee = $cur_suite_price;
        
        //Send Mail
        $emailData = array(
            '[name]' => $user->first_name . " " . $user->last_name,
            '[email]' => $user->user_email,
            '[suite_name]' => get_the_title($this->suite_id),
            '[suite_url]' => get_permalink($this->suite_id),
            '[paid_amount]' => $this->monthly_fee
        );
        
        cp_send_email(array('name' => $emailData['[name]'], 'email' => $emailData['[email]']), $this->status == 'InArrears' ? 'active_subscription' : 'active_subscription2', $emailData);
        cp_send_email_to_admin($this->status == 'InArrears' ? 'active_subscription_admin' : 'active_subscription2_admin', $emailData);            
    }
    
}