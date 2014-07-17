<?php

class CT_Organisation
{
    var $id = null;
    
    var $organisation_name = '';
    var $organisation_domain = '';
    var $invoice_me = 0;
    
    var $contact_first_name = '';
    var $contact_last_name = '';
    var $contact_email = '';
    
    var $abn = '';
    
    var $billing_address_attention = '';    
    var $billing_address1 = '';
    var $billing_address2 = '';
    var $billing_address3 = '';
    var $billing_address4 = '';
    
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
        $xero = new CT_Xero();
        $data['contact_id'] = strtolower( $data['contact_id'] );
        $response = false;
        //remove empty values
        $data = array_diff( $data, array( '' ) );
        $data = array_map( 'stripslashes_deep', $data );
        if( count( $data ) != 2 ){
            $response = $xero->upsertContact( $data );
        }
        if( ! $this->id)
        {
            if( ! is_string( $response) ){
                return $wpdb->insert($wpdb->prefix . "organisations", $data );
            }
            //Insert
            return $response;
        }
        else
        {
            if( ! is_string( $response) ){
                return $wpdb->update($wpdb->prefix . "organisations", $data, array('id' => $this->id));
            }
            //Update
            return $response;
        }
    }
    
    public function save_force()
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
                
        //remove empty values
        $data = array_map( 'stripslashes_deep', $data );
        
        if( ! $this->id) {
            $result = $wpdb->insert($wpdb->prefix . "organisations", $data );
        } else {
            $result = $wpdb->update($wpdb->prefix . "organisations", $data, array('id' => $this->id));
        }
        
        return $result;
    }
    
}