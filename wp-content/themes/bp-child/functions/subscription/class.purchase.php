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
            }
        }
    }
    
    
    
}