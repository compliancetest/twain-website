<?php

class CT_Organisation
{
    var $id = null;
    
    var $organisation_name = '';
    var $organisation_domain = '';
    var $invoice_me = 0;
    var $xero_contact_name = '';
    
    var $contact_first_name = '';
    var $contact_last_name = '';
    var $contact_email = '';
    
    var $abn = '';
    
    var $billing_address_attention = '';    
    var $billing_address = '';
    var $billing_city = '';
    var $billing_postcode = '';
    var $billing_state = '';
    var $billing_country = '';
    
    var $phonenumber = '';
    var $phonenumber_areacode = '';
    var $phonenumber_countrycode = '';
    
    var $contact_id = '';
    
    var $admin_id = 0;
    
    
    public function __construct($id = null)
    {
        global $wpdb;
        
        if($id)        
        {
            $query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}organisations WHERE id=%d", $id);
            $row = $wpdb->get_row($query, ARRAY_A);
            if($row)
            {
                foreach(array_keys($row) as $_m)
                {
                    $this->$_m = $row[$_m];
                }    
            }
        }
    }
    
    public function bind($data)
    {
        $variables = get_class_vars('CT_Organisation');
        
        foreach(array_keys($variables) as $_m)
        {            
            if(isset($data[$_m]))
                $this->$_m = $data[$_m];
        }
    }
    
    public function save()
    {
        global $wpdb;
        
        $data = array();
        
        $variables = get_class_vars('CT_Organisation');
        foreach(array_keys($variables) as $_m)
        {
            
            if($_m == 'id')
                continue;
            $data[$_m] = $this->$_m;            
        }        
        
        if(!$this->id)
        {
            //Insert
            return $wpdb->insert($wpdb->prefix . "organisations", $data);
        }
        else
        {
            //Update
            return $wpdb->update($wpdb->prefix . "organisations", $data, array('id' => $this->id));
        }
    }
    
}