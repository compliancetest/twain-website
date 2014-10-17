<?php

class ProductAndService
{
    var $id = null;
    
    var $name = '';
    
    var $product_id = '';
    
    var $release_date = '';
    
    var $type = '';
    
    var $version = '';
    
    var $accessURL = '';
    
    var $owner = '';
    
    var $descrition = '';

    var $visibility = '';

    var $relatedProducts = array();

    public $service_related_services = '';

    public $relatedServices = array();
    
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
        $this->product_id = $this->loadSingleValue('product_id');
        $this->release_date = $this->loadSingleValue('product_release_date');
        $this->type = $this->loadSingleValue('product_type');
        $this->owner = $this->loadSingleValue('product_owner');
        $this->version = $this->loadSingleValue('product_version');
        $this->accessURL = $this->loadSingleValue('product_url');
        $this->descrition = $this->loadSingleValue('product_description');
        $this->visibility = $this->loadSingleValue('product_visibility');
        $this->services_not_permitted = $this->loadSingleValue('services_not_permitted');
        $this->service_related_services = $this->loadSingleValue('related_services');
        if( $this->service_related_services ){
            $this->service_related_services = json_decode( $this->service_related_services );
        }

        $this->loadRelatedProducts();

        $this->loadRelatedServices();
    }
    
    public function loadRelatedProducts()
    {
        global $wpdb;
        
        $query = $wpdb->prepare("SELECT pr.*, pm.meta_value as product_name FROM " . $wpdb->prefix . "products_relationships as pr " .
                                "LEFT JOIN " . $wpdb->postmeta . " AS pm ON pm.post_id=pr.related_product_id and pm.meta_key='product_name' WHERE pr.product_id=%d", $this->id);
        $rows = $wpdb->get_results($query);
        
        $this->relatedProducts = $rows;
        
        return $this->relatedProducts;
    }

    public function loadRelatedServices(){
        global $wpdb;
        $this->relatedServices = $wpdb->get_results( $wpdb->prepare("SELECT post_id AS id FROM " . $wpdb->prefix . "postmeta WHERE meta_key = 'service_product_id' AND meta_value = %d", $this->id) );
        return $this->relatedServices;
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
    
    public function getComplianceClaims($status = null)
    {
        global $wpdb;
        
        $query = "SELECT count(id) FROM " . $wpdb->prefix . "compliance_claims WHERE product_id='" . $this->id . "'";
        if($status != null)
            $query .= " AND `status`='" . $status . "'";
        
        $count = $wpdb->get_var($query);
        
        return $count;
    }
}