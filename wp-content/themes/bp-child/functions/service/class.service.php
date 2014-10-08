<?php

class Service
{
    public $id = null;

    public $service_name = '';

    public $service_id = '';

    public $service_endpoint = '';

    public $service_endpoint_type = '';

    public $service_description = '';

    public $service_product_id = '';

    public $service_visibility = '';

    public $service_suite_id = '';

    public $service_roles = '';

    public $service_levels = '';

    public $service_protocol = '';

    public $service_owner = '';

    public function loadSingleValue($key)
    {
        return get_post_meta($this->id, $key, true);
    }
    
    public function __construct($id = null)
    {        
        if($id !== null)   
            $this->id = $id;        
    }
    
    public function load($id = null)
    {
        if($id !== null)   
            $this->id = $id;
        
        if(!$this->id)
            return;
        
        $this->service_name = $this->loadSingleValue('service_name');
        $this->service_id = $this->loadSingleValue('service_id');
        $this->service_endpoint = $this->loadSingleValue('service_endpoint');
        $this->service_endpoint_type = $this->loadSingleValue('service_endpoint_type');
        $this->service_description = $this->loadSingleValue('service_description');
        $this->service_visibility = $this->loadSingleValue('service_visibility');
        $this->service_roles = explode( ';;', $this->loadSingleValue('service_roles' ) );
        $this->service_levels = explode( ';;', $this->loadSingleValue('service_levels') );
        $this->service_protocol = $this->loadSingleValue('service_protocol');

        $this->service_product_id = $this->loadSingleValue('service_product_id');
        $this->service_suite_id = $this->loadSingleValue('service_suite_id');
        $this->service_owner = $this->loadSingleValue('service_owner');
    }
}