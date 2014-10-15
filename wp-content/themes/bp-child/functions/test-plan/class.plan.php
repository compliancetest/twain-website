<?php
/**
* Manage Test Plan
*/

class TestPlan
{
    var $id = null;
    
    var $suite_id = null;
    
    var $product_id = null;
    
    var $level = null;
    
    var $role = null;
    
    var $organisation_id = null;
    
    var $created_date = null;
    
    public function __construct($id = null)
    {
        if($id != null)
            $this->id = $id;
        
    }
    
    public function load()
    {
        global $wpdb;
        
        $query = $wpdb->prepare("SELECT p.*, pm.meta_value as suiteName, ppm.meta_value as productName FROM " . $wpdb->prefix . "test_plans as p " . 
                                "LEFT JOIN " . $wpdb->postmeta . " as pm on pm.post_id=p.suite_id AND pm.meta_key='ts_name' " . 
                                "LEFT JOIN " . $wpdb->postmeta . " as ppm on ppm.post_id=p.product_id AND ppm.meta_key='product_name' " . 
                                "WHERE p.id=%d", $this->id);
        $row = $wpdb->get_row($query);
        
        if($row)
        {
            $this->id = $row->id;
            $this->suite_id = $row->suite_id;
            $this->product_id = $row->product_id;
            $this->level = cp_explode($row->level);
            $this->role = cp_explode($row->role);
            $this->created_date = $row->created_date;
            $this->organisation_id = $row->organisation_id;
            $this->product_name = $row->productName;
            $this->suite_name = $row->suiteName;
        }
        
        return $row;
    }
}