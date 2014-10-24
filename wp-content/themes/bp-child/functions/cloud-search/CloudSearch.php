<?php

class CloudSearch {

    private $_documentEndpoint = '';
    private $_searchDomainURL = '';

    public function __construct(){
        $this->_documentEndpoint = get_option( 'cloudsearch_document_endpoint' );
        $this->_searchDomainURL = get_option( 'cloudsearch_search_endpoint' );
    }

    public function search( $params = false, $full_results = false ){
        $str = array();
        $str['return'] = '_all_fields';
        $str['facet.type'] = '{}';
        $str['facet.test_type'] = '{}';
        $str['facet.test_suite'] = '{}';
        $str['facet.owner'] = '{}';
        $str['facet.level'] = '{}';
        $str['facet.role'] = '{}';
        $str['facet.status'] = '{}';
        if( $full_results ){
            $str['size'] = 10000;
        } else{
            $str['size'] = 25;
        }
        $l = '';
        $range_checked = false;
        if( is_user_logged_in() ) {
            $l .= "  (or ( term field=visibility 1 ) (  term field=visibility 3   ) ( term field=visibility 2 ) )";//( term field=user_id ".get_current_user_id()." )
        } else{
            $l .= "  ( term field=visibility 1 )";
        }
        foreach( $params AS $k => $v ){
            if( $k == 'q' ){
                if( ! empty( $v ) ) {
                    $str['q'] = $v;
                }
            }else if( $k == 'page' ){
                if( $v != 1 ){
                    $str['start'] = ( ( --$v * 25 ) ) ;
                }
            }else if( $k == 'date_from'  || $k == 'date_to' ){
                if( ! $range_checked ) {
                    if (isset($params['date_from']) && ! empty( $params['date_from'] )) {
                        $from = "['".$params['date_from'].'T00:00:00Z'."'";
                    } else{
                        $from = '{';
                    }
                    if ( isset($params['date_to'] ) && ! empty( $params['date_to'] ) ) {
                        $to = "'".$params['date_to'].'T23:59:59Z'."']";
                    } else{
                        $to = '}';
                    }
                    if( "$from, $to" !== '{, }') {
                        $l .= "(range field=date $from, $to   ) ";
                    }
                    $range_checked = true;
                }
            }else {
                if( $v !== 'All' ) {
                    $l .= " (term field=" . $k . " '" . urldecode( $v ) . "') ";
                }
            }
        }
        if( ! empty( $l ) ){
            $str['fq'] = ' ( and '.$l.' ) ';
        }
        if( ! isset( $str['q'] ) ){
            $str['q'] = 'matchall';
            $str['q.parser'] = 'structured';
        }
        $curl = curl_init( $this->_searchDomainURL . http_build_query( $str ) );
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $resp = curl_exec($curl);
        curl_close($curl);
        $res = json_decode( $resp, true );
        return $res;
    }

    /**
     * Function used to upload all items to CloudSearch domain.
     * You can delete uploaded items using _delete_all_items method
     */
    public  function _initial_upload(){
        global $wpdb;

        // step 1 - upload test plans

        $data = array();
        $test_plans = $wpdb->get_results( "SELECT * FROM wp_test_plans" );
        foreach( $test_plans AS $test_plan ){
            $product = new ProductAndService( $test_plan->product_id );
            $product->load();
            $test_plan->level = trim( $test_plan->level, ';;' );
            $test_plan->role = trim( $test_plan->role, ';;' );
            if( strpos( $test_plan->role, ';;' ) ){
                $roles = explode( ';;', $test_plan->role );
            } else{
                $roles = array( $test_plan->role );
            }
            if( strpos( $test_plan->level, ';;' ) ){
                $levels = explode( ';;', $test_plan->level );
            } else{
                $levels = array( $test_plan->level );
            }
            $post_author = $wpdb->get_var( $wpdb->prepare( "SELECT post_author FROM wp_posts WHERE ID = %d ", $product->id ) );
            $groups = groups_get_user_groups( $post_author );
            $temp_data = array(
                'name'        => $product->name,
                'version'     => $product->version,
                'owner'       => $product->owner,
                'type'        => 'Software Product',
                'test_suite'  => get_the_title( $test_plan->suite_id ),
                'role'        => $roles,
                'level'       => $levels,
                'status'      => 'In Progress',
                'test_type'   => 'Certification',
                'date'        =>  date( 'Y-m-d\TH:i:s', strtotime( $test_plan->created_date ) ).'Z',
                'for_search'  => $product->descrition. ' + '.$product->owner.' + '.get_the_title( $test_plan->suite_id ).' + Software Product + Certification + '.implode(' ', $roles).' + '.implode(' ', $levels),
                'suite_id'    => $test_plan->suite_id,
                'post_id'     => $product->id,
                'visibility'  => $product->visibility == 'Public' ? 1 : 3,
                'community_id' => $groups['groups'],
                'user_id'     => $post_author
            );
            array_push( $data, array( 'type' => 'add', 'id' => 'test_plan_'.$test_plan->id, 'fields' => $temp_data ) );
        }
        var_dump( $this->_sendDataToSearchDomain( $data ) );
        // step 2 - upload claims

        $data = array();
        $claims = $wpdb->get_results( "SELECT * FROM wp_compliance_claims" );
        foreach( $claims AS $claim ){
            $product = new ProductAndService( $claim->product_id );
            $product->load();
            $claim->level = trim( $claim->level, ';;' );
            $claim->role = trim( $claim->role, ';;' );
            if( strpos( $claim->role, ';;' ) ){
                $roles = explode( ';;', $claim->role );
            } else{
                $roles = array( $claim->role );
            }
            if( strpos( $claim->level, ';;' ) ){
                $levels = explode( ';;', $claim->level );
            } else{
                $levels = array( $claim->level );
            }
            $groups = groups_get_user_groups( $post_author );
            $post_author = $wpdb->get_var( $wpdb->prepare( "SELECT post_author FROM wp_posts WHERE ID = %d ", $product->id ) );
            $temp_data = array(
                'name'        => $product->name,
                'version'     => $product->version,
                'owner'       => $product->owner,
                'type'        => 'Software Product',
                'test_suite'  => get_the_title( $claim->suite_id ),
                'role'        => $roles,
                'level'       => $levels,
                'status'      => 'Verified',
                'test_type'   => 'Certification',
                'date'        =>  date( 'Y-m-d\TH:i:s', strtotime( $claim->created_date ) ).'Z',
                'for_search'  => $product->descrition. ' + '.$product->owner.' + '.get_the_title( $claim->suite_id ).' + Software Product + Certification + '.implode(' ', $roles).' + '.implode(' ', $levels),
                'suite_id'    => $claim->suite_id,
                'post_id'     => $product->id,
                'visibility'  => $product->visibility == 'Public' ? 1 : 3,
                'community_id' => $groups['groups'],
                'user_id'     => $post_author
            );
            array_push( $data, array( 'type' => 'add', 'id' => 'claim_'.$claim->id, 'fields' => $temp_data ) );
        }

        var_dump( $this->_sendDataToSearchDomain( $data ) );

        // step 3 - upload agreements

        $data = array();
        $agreements = $wpdb->get_results( "SELECT * FROM wp_e2e_agreement" );
        foreach( $agreements AS $agreement ){
            $service = new Service( $agreement->requester_service_id );
            $service->load();
            $post_author = $wpdb->get_var( $wpdb->prepare( "SELECT post_author FROM wp_posts WHERE ID = %d ", $service->id ) );
            if( $service->service_visibility == 'Public' ){
                $v = 1;
            } else if( $service->service_visibility == 'Community' ){
                $v = 2;
            } else {
                $v = 3;
            }
            $groups = groups_get_user_groups( $post_author );
            $temp_data = array(
                'name'        => $service->service_name,
                'version'     => $service->service_version,
                'owner'       => $service->service_owner,
                'type'        => 'Web Service',
                'test_suite'  => get_the_title( $service->service_suite_id ),
                'role'        => $service->service_roles,
                'level'       => $service->service_levels,
                'status'      => $agreement->status,
                'test_type'   => 'End to End',
                'date'        =>  date( 'Y-m-d\TH:i:s', $agreement->claim_date ).'Z',
                'for_search'  => $service->service_description.' + '.$service->service_owner.' + '.get_the_title( $service->service_suite_id ).' + Web Service + End to End + e2e + '.implode(' ', $service->service_roles).' + '.implode(' ', $service->service_levels),
                'suite_id'    => $service->service_suite_id,
                'post_id'     => $service->id,
                'visibility'  => $v,
                'community_id' => $groups['groups'],
                'user_id'     => $post_author
            );
            array_push( $data, array( 'type' => 'add', 'id' => 'agreement_'.$agreement->id, 'fields' => $temp_data ) );
        }
        var_dump( $this->_sendDataToSearchDomain( $data ) );

        //step 4 upload products
        /*$data = array();
        $args = array(
            'post_type' => 'product-service',
            'posts_per_page' => -1
        );
        $posts = get_posts($args);
        foreach( $posts AS $post ){
            $product = new ProductAndService( $post->ID );
            $product->load();
            $post_author = $wpdb->get_var( $wpdb->prepare( "SELECT post_author FROM wp_posts WHERE ID = %d ", $product->id ) );
            $groups = groups_get_user_groups( $post_author );
            $temp_data = array(
                'name'        => $product->name,
                'version'     => $product->version,
                'owner'       => $product->owner,
                'type'        => 'Software Product',
                'test_type'   => 'Certification',
                'for_search'  => $product->descrition. ' + '.$product->owner.' + Software Product + Certification + ',
                'post_id'     => $product->id,
                'visibility'  => $product->visibility == 'Public' ? 1 : 3,
                'community_id' => $groups['groups'],
                'user_id'     => $post_author
            );
            array_push( $data, array( 'type' => 'add', 'id' => 'product_'.$product->id, 'fields' => $temp_data ) );
        }
        var_dump( $this->_sendDataToSearchDomain( $data ) );*/

        //step 5 upload services
        $data = array();
        $args = array(
            'post_type' => 'service',
            'posts_per_page' => -1
        );
        $posts = get_posts($args);
        foreach( $posts AS $post ){
            $service = new Service( $post->ID );
            $service->load();
            $post_author = $wpdb->get_var( $wpdb->prepare( "SELECT post_author FROM wp_posts WHERE ID = %d ", $post->ID ) );
            $groups = groups_get_user_groups( $post_author );
            if( $service->service_visibility == 'Public' ){
                $v = 1;
            } else if( $service->service_visibility == 'Community' ){
                $v = 2;
            } else {
                $v = 3;
            }
            $temp_data = array(
                'name'        => $service->service_name,
                'version'     => $service->service_version,
                'owner'       => $service->service_owner,
                'type'        => 'Web Service',
                'test_type'   => 'End to End',
                'for_search'  => $service->service_description. ' + '.$service->service_owner.' + Service + Certification + ',
                'post_id'     => $post->ID,
                'visibility'  => $v,
                'community_id' => $groups['groups'],
                'user_id'     => $post_author
            );
            array_push( $data, array( 'type' => 'add', 'id' => 'service_'.$post->ID, 'fields' => $temp_data ) );
        }
        var_dump( $this->_sendDataToSearchDomain( $data ) );
        die;
    }

    /**
     * Use this function to delete all local items ( test plans, claims, agreements, products, services )
     * from CloudSearch domain. You can use _initial_upload function to re-upload all items
     */
    public  function _delete_all_items(){
        global $wpdb;

        // delete test plans
        $data = array();
        $test_plans = $wpdb->get_results( "SELECT * FROM wp_test_plans" );
        foreach( $test_plans AS $test_plan ){
            array_push( $data, array( 'type' => 'delete', 'id' => 'test_plan_'.$test_plan->id ) );
        }
        var_dump( $this->_sendDataToSearchDomain( $data ) );

        // delete products claims

        $data = array();
        $claims = $wpdb->get_results( "SELECT * FROM wp_compliance_claims" );
        foreach( $claims AS $claim ){
            array_push( $data, array( 'type' => 'delete', 'id' => 'claim_'.$claim->id ) );
        }

        var_dump( $this->_sendDataToSearchDomain( $data ) );

        //delete agreements
        $data = array();
        $agreements = $wpdb->get_results( "SELECT * FROM wp_e2e_agreement" );
        foreach( $agreements AS $agreement ){
            array_push( $data, array( 'type' => 'delete', 'id' => 'agreement_'.$agreement->id ) );
        }
        var_dump( $this->_sendDataToSearchDomain( $data ) );

        //delete products
       /* $data = array();
        $args = array(
            'post_type' => 'product-service',
            'posts_per_page' => -1
        );
        $posts = get_posts($args);
        foreach( $posts AS $post ){
            array_push( $data, array( 'type' => 'delete', 'id' => 'product_'.$post->ID ) );
        }
        var_dump( $this->_sendDataToSearchDomain( $data ) );*/

        //delete services
        $data = array();
        $args = array(
            'post_type' => 'service',
            'posts_per_page' => -1
        );
        $posts = get_posts($args);
        foreach( $posts AS $post ){
            array_push( $data, array( 'type' => 'delete', 'id' => 'service_'.$post->ID ) );
        }
        var_dump( $this->_sendDataToSearchDomain( $data ) );

        die;
    }

    /**
     * @param $plan_id - integer. Plan ID from wp_test_plans table
     * @return mixed
     */
    public function cloud_search_update_test_plan( $plan_id ){
        global $wpdb;
        $data = array();
        $test_plan = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM wp_test_plans WHERE id = %d", $plan_id ) );
        if( ! $test_plan ){
            return $this->cloud_search_delete_item( $plan_id, 'test_plan' );
        }
        $product = new ProductAndService( $test_plan->product_id );
        $product->load();
        $test_plan->level = trim( $test_plan->level, ';;' );
        $test_plan->role = trim( $test_plan->role, ';;' );
        if( strpos( $test_plan->role, ';;' ) ){
            $roles = explode( ';;', $test_plan->role );
        } else{
            $roles = array( $test_plan->role );
        }
        if( strpos( $test_plan->level, ';;' ) ){
            $levels = explode( ';;', $test_plan->level );
        } else{
            $levels = array( $test_plan->level );
        }
        $post_author = $wpdb->get_var( $wpdb->prepare( "SELECT post_author FROM wp_posts WHERE ID = %d ", $product->id ) );
        $groups = groups_get_user_groups( $post_author );
        $temp_data = array(
            'name'        => $product->name,
            'version'     => $product->version,
            'owner'       => $product->owner,
            'type'        => 'Software Product',
            'test_suite'  => get_the_title( $test_plan->suite_id ),
            'role'        => $roles,
            'level'       => $levels,
            'status'      => 'In Progress',
            'test_type'   => 'Certification',
            'date'        =>  date( 'Y-m-d\TH:i:s', strtotime( $test_plan->created_date ) ).'Z',
            'for_search'  => $product->descrition. ' + '.$product->owner.' + '.get_the_title( $test_plan->suite_id ).' + Software Product + Certification + '.implode(' ', $roles).' + '.implode(' ', $levels),
            'suite_id'    => $test_plan->suite_id,
            'post_id'     => $product->id,
            'visibility'  => $product->visibility == 'Public' ? 1 : 3,
            'community_id' => $groups['groups'],
            'user_id'     => $post_author
        );
        array_push( $data, array( 'type' => 'add', 'id' => 'test_plan_'.$test_plan->id, 'fields' => $temp_data ) );
        return $this->_sendDataToSearchDomain( $data );
    }

    /**
     * @param $claim_id - integet. Claim ID value from wp_compliance_claims
     * @return mixed
     */
    public function cloud_search_update_claim( $claim_id ){
        global $wpdb;
        $data = array();
        $claim = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM wp_compliance_claims WHERE id = %d", $claim_id ) );
        if( ! $claim ){
            return $this->cloud_search_delete_item( $claim_id, 'claim' );
        }
        $product = new ProductAndService( $claim->product_id );
        $product->load();
        $claim->level = trim( $claim->level, ';;' );
        $claim->role = trim( $claim->role, ';;' );
        if( strpos( $claim->role, ';;' ) ){
            $roles = explode( ';;', $claim->role );
        } else{
            $roles = array( $claim->role );
        }
        if( strpos( $claim->level, ';;' ) ){
            $levels = explode( ';;', $claim->level );
        } else{
            $levels = array( $claim->level );
        }
        $post_author = $wpdb->get_var( $wpdb->prepare( "SELECT post_author FROM wp_posts WHERE ID = %d ", $product->id ) );
        $groups = groups_get_user_groups( $post_author );
        $temp_data = array(
            'name'        => $product->name,
            'version'     => $product->version,
            'owner'       => $product->owner,
            'type'        => 'Software Product',
            'test_suite'  => get_the_title( $claim->suite_id ),
            'role'        => $roles,
            'level'       => $levels,
            'status'      => 'Verified',
            'test_type'   => 'Certification',
            'date'        =>  date( 'Y-m-d\TH:i:s', strtotime( $claim->created_date ) ).'Z',
            'for_search'  => $product->descrition. ' + '.$product->owner.' + '.get_the_title( $claim->suite_id ).' + Software Product + Certification + '.implode(' ', $roles).' + '.implode(' ', $levels),
            'suite_id'    => $claim->suite_id,
            'post_id'     => $product->id,
            'visibility'  => $product->visibility == 'Public' ? 1 : 3,
            'community_id' => $groups['groups'],
            'user_id'     => $post_author
        );
        array_push( $data, array( 'type' => 'add', 'id' => 'claim_'.$claim->id, 'fields' => $temp_data ) );
        return $this->_sendDataToSearchDomain( $data );
    }

    /**
     * @param $agreement_id - integer. Agreement ID value from wp_e2e_agreement table
     * @return mixed
     */
    public function cloud_search_update_agreement( $agreement_id ){
        global $wpdb;
        $data = array();
        $agreement = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM wp_e2e_agreement WHERE id = %d ", $agreement_id ) );
        if( ! $agreement ){
            return $this->cloud_search_delete_item( $agreement_id, 'agreement' );
        }
        $service = new Service( $agreement->requester_service_id );
        $service->load();
        $post_author = $wpdb->get_var( $wpdb->prepare( "SELECT post_author FROM wp_posts WHERE ID = %d ", $service->id ) );
        if( $service->service_visibility == 'Public' ){
            $v = 1;
        } else if( $service->service_visibility == 'Community' ){
            $v = 2;
        } else {
            $v = 3;
        }
        $groups = groups_get_user_groups( $post_author );
        $temp_data = array(
            'name'        => $service->service_name,
            'version'     => $service->service_version,
            'owner'       => $service->service_owner,
            'type'        => 'Web Service',
            'test_suite'  => get_the_title( $service->service_suite_id ),
            'role'        => $service->service_roles,
            'level'       => $service->service_levels,
            'status'      => $agreement->status,
            'test_type'   => 'End to End',
            'date'        =>  date( 'Y-m-d\TH:i:s', $agreement->claim_date ).'Z',
            'for_search'  => $service->service_description.' + '.$service->service_owner.' + '.get_the_title( $service->service_suite_id ).' + Web Service + End to End + e2e + '.implode(' ', $service->service_roles).' + '.implode(' ', $service->service_levels),
            'suite_id'    => $service->service_suite_id,
            'post_id'     => $service->id,
            'visibility'  => $v,
            'community_id' => $groups['groups'],
            'user_id'     => $post_author
        );
        array_push( $data, array( 'type' => 'add', 'id' => 'agreement_'.$agreement->id, 'fields' => $temp_data ) );
        return $this->_sendDataToSearchDomain( $data );
    }

    /**
     * Function used to update product info in CloudSearch domain
     * @param $product_id - integer. Post ID from wp_posts table
     * @return mixed
     */
    public function cloud_search_update_product( $product_id ){
        global $wpdb;
        $data = array();
        $product = new ProductAndService( $product_id );
        $product->load();
        if( ! $product->id ){
            return $this->cloud_search_delete_item( $product_id, 'product' );
        }
        $post_author = $wpdb->get_var( $wpdb->prepare( "SELECT post_author FROM wp_posts WHERE ID = %d ", $product->id ) );
        $groups = groups_get_user_groups( $post_author );
        $temp_data = array(
            'name'        => $product->name,
            'version'     => $product->version,
            'owner'       => $product->owner,
            'type'        => 'Software Product',
            'test_type'   => 'Certification',
            'for_search'  => $product->descrition. ' + '.$product->owner.' + Software Product + Certification + ',
            'post_id'     => $product->id,
            'visibility'  => $product->visibility == 'Public' ? 1 : 3,
            'community_id' => $groups['groups'],
            'user_id'     => $post_author
        );
        array_push( $data, array( 'type' => 'add', 'id' => 'product_'.$product_id, 'fields' => $temp_data ) );
        return $this->_sendDataToSearchDomain( $data );
    }

    /**
     * Use this function to update service data in CloudSearch domain.
     *
     * @param $service_id - integer. Post ID from wp_posts table
     * @return mixed
     */
    public function cloud_search_update_service( $service_id ){
        global $wpdb;
        $data = array();
        $service = new Service( $service_id );
        $service->load();
        if( ! $service->id ){
            return $this->cloud_search_delete_item( $service_id, 'service' );
        }
        $post_author = $wpdb->get_var( $wpdb->prepare( "SELECT post_author FROM wp_posts WHERE ID = %d ", $service->id ) );
        $groups = groups_get_user_groups( $post_author );
        if( $service->service_visibility == 'Public' ){
            $v = 1;
        } else if( $service->service_visibility == 'Community' ){
            $v = 2;
        } else {
            $v = 3;
        }
        $temp_data = array(
            'name'        => $service->service_name,
            'version'     => $service->service_version,
            'owner'       => $service->service_owner,
            'type'        => 'Web Service',
            'test_type'   => 'End to End',
            'for_search'  => $service->service_description. ' + '.$service->service_owner.' + Service + Certification + ',
            'post_id'     => $service->id,
            'visibility'  => $v,
            'community_id' => $groups['groups'],
            'user_id'     => $post_author
        );
        array_push( $data, array( 'type' => 'add', 'id' => 'service_'.$service->id, 'fields' => $temp_data ) );
        return $this->_sendDataToSearchDomain( $data );
    }
    /**
     * Use this function to delete item from cloudSearch domain
     *
     * @param $id - primary key from entry table
     * @param $type - enum ( 'agreement', 'claim', 'test_plan', 'product', 'service')
     * @return mixed
     */
    public function cloud_search_delete_item( $id, $type ){
        $data = array();
        array_push( $data, array( 'type' => 'delete', 'id' => $type.'_'.$id  ) );
        return $this->_sendDataToSearchDomain( $data );
    }
    protected function _sendDataToSearchDomain( Array $rows ){
        $data = json_encode( $rows );
        $ch = curl_init( $this->_documentEndpoint );
        curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt( $ch, CURLOPT_POSTFIELDS,  $data );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data))
        );
        return curl_exec( $ch );
    }
} 