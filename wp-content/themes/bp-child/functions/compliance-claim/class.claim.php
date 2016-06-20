<?php
class ComplianceClaim
{
    var $id = null;
    
    var $claim_id = null;
    
    var $product_id = null;
    
    var $organisation_id = null;
    
    var $suite_id = null;
    
    var $conformance_level = '';
    
    var $role = '';
    
    var $status = '';
    
    var $created_date = '';
    
    var $last_updated = '';
    
    var $audit = null;
    
    var $issuer = '';
    
    var $token = '';
    
    public function __construct($id = null)
    {
        if($id != null)
            $this->id = $id;
        
    }
    
    public function load()
    {
        global $wpdb;
        
        $query = $wpdb->prepare("SELECT c.*, pm.meta_value as issuer FROM claims as c LEFT JOIN " . $wpdb->postmeta . " as pm on pm.post_id=c.test_suite_id AND pm.meta_key='ts_issuer' WHERE id=%d", $this->id);
        $row = $wpdb->get_row($query);
        
        if($row)
        {
            $this->product_id = $row->product_id;
            $this->organisation_id = $row->organisation_id;
            $this->suite_id = $row->suite_id;
            $this->conformance_level = $row->conformance_level;
            $this->role = $row->role;
            $this->claim_id = $row->claim_id;
            $this->status = $row->status;
            $this->created_date = $row->created_date;
            $this->last_updated = $row->last_updated;
            $this->audit = $row->audit;
            $this->issuer = $row->issuer;
            $this->token = $row->token;
        }
        
        return $row;
    }
    
}