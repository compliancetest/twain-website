<?php

class Service
{
    public $id = null;

    public $service_name = '';

    public $service_id = '';

    public $service_endpoint = '';

//    public $service_endpoint_type = '';

    public $service_description = '';

    public $service_product_id = '';

    public $service_visibility = '';

    public $service_suite_id = '';

    public $service_roles = '';

    public $service_levels = '';

    public $service_protocol = '';

    public $service_owner = '';

    public $service_user_id = '';

    public $service_version = '';

    public $service_related_services = array();

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
        
        if( ! $this->id || ! get_post( $this->id ) )
            return;
        
        $this->service_name = $this->loadSingleValue('service_name');
        $this->service_id = $this->loadSingleValue('service_id');
        $this->service_endpoint = $this->loadSingleValue('service_endpoint');
//        $this->service_endpoint_type = $this->loadSingleValue('service_endpoint_type');
        $this->service_description = $this->loadSingleValue('service_description');
        $this->service_visibility = $this->loadSingleValue('service_visibility');
        $this->service_roles = explode( ';;', $this->loadSingleValue('service_roles' ) );
        $this->service_levels = explode( ';;', $this->loadSingleValue('service_levels') );
        $this->service_protocol = $this->loadSingleValue('service_protocol');

        $this->service_product_id = $this->loadSingleValue('service_product_id');
        $this->service_suite_id = $this->loadSingleValue('service_suite_id');
        $this->service_owner = $this->loadSingleValue('service_owner');
        $this->service_user_id = $this->loadSingleValue('service_user_id');
        $this->service_type = $this->loadSingleValue( 'service_type' );
    }

    public static function has_assess( $service_id, $user_id = false ){
        global $wpdb;
        $service = new Service( $service_id );
        $service->load();
        if( $service->service_visibility != 'Public' && ! is_user_logged_in() ){
            return false;
        }
        if( ! $user_id ){
            $user_id = get_current_user_id();
        }

        if( $service->service_visibility == 'Public' ){
            return true;
        }
        // service has private access
        if( $service->service_visibility == 'Private' && ( $user_id == $service->service_user_id || is_super_admin() ) ){
            return true;
        }
        // both users has same community
        if( $service->service_visibility == 'Community' ){
            if( $wpdb->get_results($wpdb->prepare("SELECT * FROM wp_bp_groups_members WHERE user_id = %d AND group_id IN( SELECT group_id FROM wp_bp_groups_members WHERE user_id = %d )", $user_id, $service->service_user_id ) ) ){
                return true;
            }
        }
        return false;
    }

    /**
     *
     */
    public static function can_request_e2e( $user_id, $service_id ){
        if( count( $services = Service::get_user_services( $user_id, $service_id ) ) > 0 ){
            $service = new Service( $service_id );
            $service->load();
            $can_request = false;
            foreach( $services AS $s ){
                $tem_service = new Service( $s->ID );
                $tem_service->load();
                if( $tem_service->service_roles != $service->service_roles ){
                    $can_request  = true;
                }
            }
            return $can_request;
        }
        return false;
    }

    public static function get_user_services( $user_id = false, $exclude_service = false ){
        if( ! is_user_logged_in() && ! $user_id ){
            return false;
        }
        if( ! $user_id ) $user_id = get_current_user_id();

        $args = array(
            'post_type' => 'service',
            'posts_per_page' => -1,
            'tax_query' => array('relation' => 'and'),
        );
        if( $exclude_service ){
            $args['post__not_in'] = array( $exclude_service );
        }
        $args['meta_query'][] = array('key' => 'service_user_id', 'value' => $user_id, 'compare' => '=');

        return get_posts( $args );
    }
}