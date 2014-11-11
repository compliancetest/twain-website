<?php

class CT_Organisation
{
    var $_fields = array(
                            'id', 
                            'organisation_name', 
                            'organisation_description', 
                            'organisation_website', 
                            'organisation_key', 
                            'invoice_me', 
                            
                            'contact_first_name', 
                            'contact_last_name', 
                            'contact_email',
                            'secondary_contact_first_name',
                            'secondary_contact_last_name',
                            'secondary_contact_email',
                            
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
                            
                            'contact_id',
                            'no_billing'
                         );
    
    var $id = null;
    
    var $organisation_name = '';
    var $organisation_key = '';
    var $organisation_description = '';
    var $organisation_website = '';
    var $invoice_me = 0;
    
    var $contact_first_name = '';
    var $contact_last_name = '';
    var $contact_email = '';

    public $secondary_contact_first_name = '';
    public $secondary_contact_last_name = '';
    public $secondary_contact_email = '';
    
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

    public $no_billing = '';
    
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
        $data = stripslashes_deep( $data );
        if( isset( $data['contact_id'] ) && ! empty( $data['contact_id'] ) && empty( $data['organisation_name']) ){
            if( ! $wpdb->get_row( $wpdb->prepare("SELECT * FROM {$wpdb->prefix}organisations WHERE contact_id = %s ", $data['contact_id'] ) ) ){
                $wpdb->insert($wpdb->prefix . "organisations", $data );
            }
            return true;
        }
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
            
            if(!$data['organisation_key'])
                $data['organisation_key'] = ct_generate_organisation_key();
            
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
            $wpdb->insert($wpdb->prefix . "organisations", $data );
        } else {
            $wpdb->update($wpdb->prefix . "organisations", $data, array('id' => $this->id));
        }
        
        return 1;
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
    
    
    
    public function get_subscriptions()
    {
        global $wpdb;
        
        if(isset($this->subscriptions))
            return $this->subscriptions;
        
        $query = $wpdb->prepare("SELECT DISTINCT(os.id), os.*, u.user_email, CONCAT(um1.meta_value, ' ', um2.meta_value) AS full_name, t.suite_title, bg.name as community_name FROM {$wpdb->prefix}organisations_subscriptions AS os 
                            LEFT JOIN {$wpdb->users} AS u ON u.ID=os.user_id 
                            LEFT JOIN {$wpdb->usermeta} AS um1 ON um1.user_id=u.ID AND um1.meta_key='first_name'
                            LEFT JOIN {$wpdb->usermeta} AS um2 ON um2.user_id=u.ID AND um2.meta_key='last_name'
                            LEFT JOIN {$wpdb->prefix}test_suites AS t ON t.family_mark=os.suite_family_mark
                            LEFT JOIN {$wpdb->prefix}postmeta AS pm ON pm.meta_key='community_id' AND pm.post_id=os.suite_family_mark
                            LEFT JOIN {$wpdb->prefix}bp_groups AS bg ON bg.id=pm.meta_value
                            WHERE os.organisation_id=%d
                            ORDER BY bg.name, t.suite_title, os.nickname", $this->id);
    
        $this->subscriptions = $wpdb->get_results($query);
        
        return $this->subscriptions;
    }
    
    public function get_payment_methods()
    {
        global $wpdb;
        
        if(isset($this->payment_methods))
            return $this->payment_methods;
            
        $query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}organisations_payment_methods WHERE organisation_id=%d", $this->id);
        $rows = $wpdb->get_results($query);
        
        $this->payment_methods = $rows;
        
        return $this->payment_methods;
    }
    
    /**
    * Getting not subsbscribed test suites
    * 
    */
    public function get_free_test_suites()
    {
        global $wpdb;
        
        $query = $wpdb->prepare("SELECT DISTINCT(t.family_mark), t.suite_title FROM {$wpdb->prefix}test_suites AS t 
                                LEFT JOIN {$wpdb->prefix}organisations_subscriptions AS os ON os.suite_family_mark=t.family_mark AND os.organisation_id=%d
                                WHERE os.id IS NULL ORDER BY t.suite_title", $this->id);
        
        $data = $wpdb->get_results($query);
        
        return $data;
    }
    
    public function get_organisation_members()
    {
        global $wpdb;
        
        if (isset($this->members)) {
            return $this->members;
        }
        
        $query = $wpdb->prepare("SELECT DISTINCT(u.ID), u.user_email, u.display_name, CONCAT(um1.meta_value, ' ', um2.meta_value) AS full_name, m.is_admin, m.id AS membership_id
                                FROM {$wpdb->prefix}organisations_members AS m
                                LEFT JOIN {$wpdb->users} AS u ON u.ID=m.user_id 
                                LEFT JOIN {$wpdb->usermeta} AS um1 ON um1.user_id=u.ID AND um1.meta_key='first_name'
                                LEFT JOIN {$wpdb->usermeta} AS um2 ON um2.user_id=u.ID AND um2.meta_key='last_name'
                                WHERE m.organisation_id =%d AND user_status=0 
                                ORDER BY display_name", $this->id);
        $this->members = $wpdb->get_results($query);
        
        return $this->members;
    }
}