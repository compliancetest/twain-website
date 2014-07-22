<?php

class CT_Organisation
{
    var $_fields = array(
                            'id', 
                            'organisation_name', 
                            'organisation_domain', 
                            'invoice_me', 
                            
                            'contact_first_name', 
                            'contact_last_name', 
                            'contact_email', 
                            
                            'abn', 
                            
                            'billing_address_attention',
                            'billing_address1',
                            'billing_address2',
                            'billing_address3',
                            'billing_address4',
                            'billing_city',
                            'billing_postcode',
                            'billing_state',
                            'billing_country',
                            
                            'phonenumber',
                            'phonenumber_areacode',
                            'phonenumber_countrycode',
                            
                            'contact_id'
                         );
    
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
    
    var $admin_id = '';
    
    public function __construct($id = null)
    {
        global $wpdb;
        
        if($id)        
        {
            $query = $wpdb->prepare("SELECT o.*, om.user_id AS admin_id FROM {$wpdb->prefix}organisations AS o 
                                     LEFT JOIN {$wpdb->prefix}organisations_members AS om ON om.organisation_id=o.id AND om.is_admin=1 
                                     WHERE o.id=%d", $id);
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
        foreach($this->_fields as $_m)
        {            
            if (isset($data[$_m])) {
                $this->$_m = $data[$_m];
            }
        }
    }
    
    public function save()
    {
        global $wpdb;
        
        $data = array();
        
        foreach($this->_fields as $_m)
        {
            if($_m == 'id')
                continue;
            $data[$_m] = $this->$_m;            
        }
        $xero = new CT_Xero();
        $data['contact_id'] = strtolower( $data['contact_id'] );
        
        $response = false;        
        
        if( count( $data ) != 2 ){
            $response = $xero->upsertContact( $data );
        }
        
        //Updatnig organisations error on Xero
        if( is_string( $response) ){            
            addMessage($response, 'error');
            return false;
        }
        else
        {       
            $data['contact_id'] = $response['Contacts']['Contact']['ContactID'];        
            if( ! $this->id)
            {   //Insert organisation to CT
                if( $wpdb->insert($wpdb->prefix . "organisations", $data ) === false )
                {
                    addMessage('Saving Organisation Error: ' . $wpdb->last_error, 'error');
                    return false;
                }
                    
                $this->id = $wpdb->insert_id;
            }
            else
            {
                if( $wpdb->update($wpdb->prefix . "organisations", $data, array('id' => $this->id)) === false )
                {
                    addMessage('Saving Organisation Error: ' . $wpdb->last_error, 'error');
                    return false;
                }
            }            
        }
        
        return true;
    }
    
    public function save_force()
    {
        global $wpdb;
        
        $data = array();
        
        foreach($this->_fields as $_m)
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
    
    public function save_organisation_admin($organisation_id, $admin_id)
    {
        global $wpdb;
        
        if(!$admin_id)
        {
            return true;
        }
        
        //Validate Admin id
        $query = $wpdb->prepare("SELECT ID FROM {$wpdb->users} WHERE user_status=0 AND ID=%d", $admin_id);
        $user_id = $wpdb->get_var($query);
        
        if(!$user_id)
        {
            addMessage("The admin id is not valid!", "error");
            return false;
        }
        
        //Demote old admin to member
        $wpdb->delete( $wpdb->prefix . "organisations_members", 
                       array('organisation_id' => $organisation_id, 'is_admin' => 1), array('%d', '%d')
                     );
        /*$wpdb->update( $wpdb->prefix . "organisations_members", 
                       array('is_admin' => 0), 
                       array('organisation_id' => $organisation_id, 'is_admin' => 1), array('%d'), array('%d', '%d')
                     );*/
        
        //Check the user is member or not
        $query = $wpdb->prepare("SELECT id, is_admin FROM {$wpdb->prefix}organisations_members WHERE organisation_id=%d AND user_id=%d", $organisation_id, $user_id);
        $row = $wpdb->get_row($query);
        
        if(!$row)
        {
            //Insert Member
            $wpdb->insert( $wpdb->prefix . "organisations_members", 
                           array('organisation_id' => $organisation_id, 'user_id' => $user_id, 'is_admin' => 1, 'created_date' => date("Y-m-d H:i:s")),
                           array('%d', '%d', '%d', '%s')
                          );
        } else if(!$row['is_admin']) {
            $wpdb->update( $wpdb->prefix . "organisations_members", 
                       array('is_admin' => 1), 
                       array('id' => $row['id']), 
                       array('%d'), 
                       array('%d')
                  );
        }
        
        return true;
    }
    
}