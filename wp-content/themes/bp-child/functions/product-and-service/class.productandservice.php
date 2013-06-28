<?php

class ProductAndService
{
    var $id = null;
    
    var $name = '';
    
    var $date = '';
    
    var $type = '';
    
    var $owner = '';
    
    var $accessURL = '';
    
    var $status = 'Active';
    
    var $descrition = '';
    
    var $relatedProducts = array();
    
    var $certifications = array();
    
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
        
        $this->name = $this->loadSingleValue('product_name');
        $this->date = $this->loadSingleValue('product_date');
        $this->type = $this->loadSingleValue('product_type');
        $this->owner = $this->loadSingleValue('product_owner');
        $this->accessURL = $this->loadSingleValue('product_url');
        $this->status = $this->loadSingleValue('product_status');
        $this->descrition = $this->loadSingleValue('product_description');
        
        $this->loadRelatedProducts();
        
        $this->loadCertifications();
        
    }
    
    public function loadRelatedProducts()
    {
        $this->relatedProducts = get_post_meta($this->id, 'related_products');
        
        return $this->relatedProducts;
    }
    
    public function loadCertifications()
    {        
        $this->certifications = get_post_meta($this->id, 'test_suites');
        
        return $this->certifications;
    }
    
    public function getAvailableProducts()
    {
        $groups = groups_get_groups( array('user_id' => get_current_user_id()) );
        
        $args = array( 'post_type' => 'product-service', 'posts_per_page' => -1, 'meta_query' => array('relation' => 'OR') );
        
        if($this->id)
        {
            $args['post__not_in'] = array($this->id);
        }
        
        if(!is_admin() && !is_super_admin())
        {
            foreach($groups['groups'] as $g)
            {
                $args['meta_query'][] = array(
                    'key' => 'community_id',
                    'value' => $g->id,
                    'compare' => '='
                );
            }
        }
        
        $rows = get_posts($args);
        
        return $rows;
    }
    
    public function getCommunityID()
    {
        return $this->loadSingleValue('community_id');
    }
}