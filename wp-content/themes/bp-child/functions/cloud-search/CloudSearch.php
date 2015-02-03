<?php

class CloudSearch {

    private $_documentEndpoint = '';
    private $_searchDomainURL = '';

    public function __construct(){
        $this->_documentEndpoint = get_option( 'cloudsearch_document_endpoint' );
        $this->_searchDomainURL = get_option( 'cloudsearch_search_endpoint' );
    }

    public function search( $params = false, $full_results = false ){
        global $wpdb;
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
            $str['size'] = SEARCH_RESULTS_LIMIT;
        }
        $l = '';
        $range_checked = false;
        if( is_user_logged_in() ) {
            if( is_super_admin() ) {
                //super admin should see all items
                $l .= "  (or ( term field=visibility 1 ) (  term field=visibility 3   ) ( term field=visibility 2 ) )";
            } else{
                //usual user should see only own and community items
                $groups = groups_get_user_groups( get_current_user_id() );
                $groups_str = '';
                foreach( $groups['groups'] AS $group_id ){
                    $groups_str .= ' ( term field=community_id '.$group_id. ' ) ';
                }
                if( ! empty( $groups_str ) ){
                    $groups_str = ' ( or '.$groups_str.' ) ';
                } else{
                    $groups_str = ' ( or ( term field=community_id 32 ) ( term field=community_id 35 ) ) ';
                }
                $private_where = '';
                $organisation_members = $wpdb->get_results( $wpdb->prepare("SELECT user_id FROM wp_organisations_members WHERE organisation_id = ( SELECT organisation_id FROM wp_organisations_members WHERE user_id = %d ) ", get_current_user_id() ) );
                if( $organisation_members ){
                    foreach( $organisation_members AS $organisation_members ){
                        $private_where .= '( term field=user_id '. $organisation_members->user_id .' )';
                    }
                }
                if( ! empty( $private_where ) ){
                    $private_where = ' ( or '. $private_where .' ) ';
                } else{
                    $private_where = " ( term field=user_id ". get_current_user_id() . " ) ";
                }
                $l .= "  (or ( term field=visibility 1 ) (  and ( term field=visibility 3 ) ". $private_where ."   ) ( and ( term field=visibility 2 ) ".$groups_str." ) )";//(  )
            }
        } else{
            //non-logged in user should see only public items
            $l .= "  ( term field=visibility 1 )";
        }
        $str['sort'] = 'name asc';
        
        foreach( $params AS $k => $v ){
            if( $k == 'q' ){
                if( ! empty( $v ) ) {
                    $str['q'] = $v;
                }
            }else if( $k == 'page' ){
                if( $v != 1 ){
                    $str['start'] = ( ( --$v * SEARCH_RESULTS_LIMIT ) ) ;
                }
            }else if( $k == 'orderby' ){
                    $str['sort'] = $v . " " . (isset($params['order']) ? $params['order'] : 'asc');
            }else if( $k == 'order' ){
                    
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

        $response_data = array();
        $data = array();
        $test_plans = $wpdb->get_results( "SELECT * FROM wp_test_plans WHERE is_deleted = 0 " );
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
            $test_suite = new TestSuite( $test_plan->suite_id );
            $test_suite->load();
            if( $product->visibility == 'Public' ){
                $visibility  = 1;
                $communities = array( 32, 35 );
            } else{
                if( $product->visibility == 'Community' ){
                    $visibility  = 2;
                    $communities = array( $test_suite->community_id );
                } else{
                    $visibility  = 3;
                    $communities = array( $test_suite->community_id );
                }
            }
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
                'date'        =>  date( 'Y-m-d\TH:i:s' ).'Z',
                'for_search'  => $product->descrition. ' + '.$product->owner.' + '.get_the_title( $test_plan->suite_id ).' + Software Product + Certification + '.implode(' ', $roles).' + '.implode(' ', $levels),
                'suite_id'    => $test_plan->suite_id,
                'post_id'     => $product->id,
                'visibility'  => $visibility,
                'community_id' => $communities,
                'user_id'     => $post_author,
                'product_id'  => $test_plan->product_id,
                'product_name' => $product->name,
                'start_date'  => date( 'Y-m-d\TH:i:s', strtotime( $test_plan->created_date ) ).'Z',
            );
            array_push( $data, array( 'type' => 'add', 'id' => 'test_plan_'.$test_plan->id, 'fields' => $temp_data ) );
        }

        $data = json_decode( $this->_sendDataToSearchDomain( $data ), true );
        $response_data['Test Plans'] = array(
            'Status'   => $data['status'],
            'Added'    => $data['adds'],
            'Deleted'  => $data['deletes'],
        );
        // step 2 - upload claims

        $data = array();
        $claims = $wpdb->get_results( "SELECT * FROM wp_compliance_claims" );
        foreach( $claims AS $claim ){
            $product = new ProductAndService( $claim->product_id );
            $product->load();
            $claim->conformance_level = trim( $claim->conformance_level, ';;' );
            $claim->role = trim( $claim->role, ';;' );
            if( strpos( $claim->role, ';;' ) ){
                $roles = explode( ';;', $claim->role );
            } else{
                $roles = array( $claim->role );
            }
            if( strpos( $claim->conformance_level, ';;' ) ){
                $levels = explode( ';;', $claim->conformance_level );
            } else{
                $levels = array( $claim->conformance_level );
            }
            $test_suite = new TestSuite( $test_plan->suite_id );
            $test_suite->load();
            if( $product->visibility == 'Public' ){
                $visibility  = 1;
                $communities = array( 32, 35 );
            } else{
                if( $product->visibility == 'Community' ){
                    $visibility  = 2;
                    $communities = array( $test_suite->community_id );
                } else{
                    $visibility  = 3;
                    $communities = array( $test_suite->community_id );
                }
            }
            $post_author = $wpdb->get_var( $wpdb->prepare( "SELECT post_author FROM wp_posts WHERE ID = %d ", $product->id ) );
            $s3 = new S3Wrapper();
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
                'date'        =>  date( 'Y-m-d\TH:i:s', strtotime( $claim->last_updated ) ).'Z',
                'for_search'  => $product->descrition. ' + '.$product->owner.' + '.get_the_title( $claim->suite_id ).' + Software Product + Certification + '.implode(' ', $roles).' + '.implode(' ', $levels),
                'suite_id'    => $claim->suite_id,
                'post_id'     => $product->id,
                'visibility'  => $visibility,
                'community_id' => $communities,
                'user_id'     => $post_author,
                'product_id'  => $claim->product_id,
                'product_name' => $product->name,
                'start_date'  => date( 'Y-m-d\TH:i:s', strtotime( $claim->created_date ) ).'Z',
                'cert_number' => $claim->claim_id,
                'cert_url'    => $s3->getProductClaimLink( $claim->token )
             );
            array_push( $data, array( 'type' => 'add', 'id' => 'claim_'.$claim->id, 'fields' => $temp_data ) );
        }
        $data = json_decode( $this->_sendDataToSearchDomain( $data ), true );
        $response_data['Claims'] = array(
            'Status'   => $data['status'],
            'Added'    => $data['adds'],
            'Deleted'  => $data['deletes'],
        );
        // step 3 - upload agreements

        $data = array();
        $agreements = $wpdb->get_results( "SELECT * FROM wp_e2e_agreement" );
        foreach( $agreements AS $agreement ){
            $requester_service = new Service( $agreement->requester_service_id );
            $requester_service->load();
            $responder_service = new Service( $agreement->responder_service_id );
            $responder_service->load();
            $service = new Service( $agreement->responder_service_id );
            $service->load();
            if( $requester_service->service_visibility == 'Public' ){
                $v = 1;
                $communities = array( 32, 35 );
            } else {
                $requester_test_suite = new TestSuite( $requester_service->service_suite_id );
                $requester_test_suite->load();
                $responder_test_suite = new TestSuite( $service->service_suite_id );
                $responder_test_suite->load();
                if( $requester_service->service_visibility == 'Community' ){
                    $v = 2;
                    $communities = array( $responder_test_suite->community_id, $requester_test_suite->community_id );
                } else {
                    $v = 3;
                    $communities = array( $responder_test_suite->community_id, $requester_test_suite->community_id );
                }
            }
            $post_author = $wpdb->get_var( $wpdb->prepare( "SELECT post_author FROM wp_posts WHERE ID = %d ", $service->id ) );
            $s3 = new S3Wrapper();
            $temp_data = array(
                'name'        => $requester_service->service_name,
                'version'     => $requester_service->service_version,
                'owner'       => array( $requester_service->service_owner, $service->service_owner ),
                'type'        => 'Agreement',
                'test_suite'  => array( get_the_title( $requester_service->service_suite_id ), get_the_title( $service->service_suite_id ) ),
                'role'        => array_unique( array_merge( $requester_service->service_roles, $service->service_roles ) ),
                'level'       => array_unique( array_merge( $requester_service->service_levels, $service->service_levels ) ),
                'status'      => $agreement->status,
                'test_type'   => 'End to End',
                'date'        =>  date( 'Y-m-d\TH:i:s', $agreement->claim_date ).'Z',
                'for_search'  => $requester_service->service_description.' + '.$requester_service->service_owner.' + '.get_the_title( $requester_service->service_suite_id ).' + Web Service + End to End + e2e + '.implode(' ', $requester_service->service_roles).' + '.implode(' ', $requester_service->service_levels).$service->service_description.' + '.$service->service_owner.' + '.get_the_title( $service->service_suite_id ).' + Web Service + End to End + e2e + '.implode(' ', $service->service_roles).' + '.implode(' ', $service->service_levels),
                'suite_id'    => $requester_service->service_suite_id,
                'post_id'     => $requester_service->id,
                'visibility'  => $v,
                'community_id' => $communities,
                'user_id'     => $post_author,
                'product_id'  => $requester_service->service_product_id,
                'product_name' => get_the_title( $requester_service->service_product_id ),
                'start_date'  => date( 'Y-m-d\TH:i:s', $agreement->requestor_message_date ).'Z',
                'cert_number' => $agreement->claim_id,
                'cert_url'    => $s3->getAgreementClaimLink( $agreement->requester_token ),
                'service_id'  => $requester_service->id,
                'service_name'   => get_the_title( $requester_service->id ),
                'entity_id'      => $requester_service->service_id,
                'entity_id_type' => $requester_service->service_type,
                'e2e_partner_service_id' => $responder_service->id
            );
            array_push( $data, array( 'type' => 'add', 'id' => 'agreement_'.$agreement->id, 'fields' => $temp_data ) );
        }
        $data = json_decode( $this->_sendDataToSearchDomain( $data ), true );
        $response_data['Agreements'] = array(
            'Status'   => $data['status'],
            'Added'    => $data['adds'],
            'Deleted'  => $data['deletes'],
        );

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
            $test_suite = new TestSuite( $service->service_suite_id );
            $test_suite->load();
            if( $requester_service->service_visibility == 'Public' ){
                $v = 1;
                $communities = array( 32, 35 );
            } else if( $requester_service->service_visibility == 'Community' ){
                $v = 2;
                $communities = array( $test_suite->community_id );
            } else {
                $v = 3;
                $communities = array( $test_suite->community_id );
            }
            $temp_data = array(
                'name'        => $service->service_name,
                'version'     => $service->service_version,
                'owner'       => $service->service_owner,
                'type'        => 'Web Service',
                'test_suite'  => get_the_title( $service->service_suite_id ),
                'role'        => $service->service_roles,
                'level'       => $service->service_levels,
                'test_type'   => 'End to End',
                'status'   => 'Available',
                'for_search'  => $service->service_description. ' + '.$service->service_owner.' + Service + Certification + ' . implode(' ', $service->service_roles).' + '.implode(' ', $service->service_levels),
                'post_id'     => $post->ID,
                'visibility'  => $v,
                'community_id' => $communities,
                'user_id'     => $post_author,
                'product_id'  => $service->service_product_id,
                'product_name' => get_the_title( $service->service_product_id ),
                'start_date'  => date( 'Y-m-d\TH:i:s', strtotime( $wpdb->get_var( $wpdb->prepare( "SELECT post_date FROM wp_posts WHERE ID = %d ", $post->ID ) ) ) ).'Z',
                'service_id'  => $service->id,
                'service_name'   => get_the_title( $service->id ),
                'entity_id'      => $service->service_id,
                'entity_id_type' => $service->service_type
            );
            array_push( $data, array( 'type' => 'add', 'id' => 'service_'.$post->ID, 'fields' => $temp_data ) );
        }
        $data = json_decode( $this->_sendDataToSearchDomain( $data ), true );
        $response_data['Services'] = array(
            'Status'   => $data['status'],
            'Added'    => $data['adds'],
            'Deleted'  => $data['deletes'],
        );
        return $response_data;
    }

    /**
     * Use this function to delete all items ( test plans, claims, agreements, products, services )
     * from CloudSearch domain. You can use _initial_upload function to re-upload all items
     */
    public  function _delete_all_items(){

        //Remove All Results
        $results = $this->search( array(), true );
        
        $data = $response_data = array();
        
        foreach( $results['hits']['hit'] as $row ) {
            array_push( $data, array( 'type' => 'delete', 'id' =>$row['id'] ) );
        }
        if( ! empty( $data ) ) {
            $data = json_decode($this->_sendDataToSearchDomain($data), true);
            $response_data = array(
                'Status'   => $data['status'],
                'Added'    => $data['adds'],
                'Deleted'  => $data['deletes'],
            );
        } else{
            $response_data = array(
                'Status'   => 'Error',
            );
        }
        return $response_data;
    }

    /**
     * @param $plan_id - integer. Plan ID from wp_test_plans table
     * @return mixed
     */
    public function cloud_search_update_test_plan( $plan_id ){
        global $wpdb;
        $test_plan = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM wp_test_plans WHERE id = %d", $plan_id ) );
        if( ! $test_plan ){
            return $this->cloud_search_delete_item( $plan_id, 'test_plan' );
        }
        $fulltext = new FulltextSearch();
        $fulltext->fullUpload();
        $this->_initial_upload();
    }

    /**
     * @param $claim_id - integet. Claim ID value from wp_compliance_claims
     * @return mixed
     */
    public function cloud_search_update_claim( $claim_id ){
        global $wpdb;
        $claim = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM wp_compliance_claims WHERE id = %d", $claim_id ) );
        if( ! $claim ){
            return $this->cloud_search_delete_item( $claim_id, 'claim' );
        }
        $fulltext = new FulltextSearch();
        $fulltext->fullUpload();
        $this->_initial_upload();
    }

    /**
     * @param $agreement_id - integer. Agreement ID value from wp_e2e_agreement table
     * @return mixed
     */
    public function cloud_search_update_agreement( $agreement_id ){
        global $wpdb;
        $agreement = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM wp_e2e_agreement WHERE id = %d ", $agreement_id ) );
        if( ! $agreement ){
            return $this->cloud_search_delete_item( $agreement_id, 'agreement' );
        }
        $fulltext = new FulltextSearch();
        $fulltext->fullUpload();
        $this->_initial_upload();
    }


    /**
     * Use this function to update service data in CloudSearch domain.
     *
     * @param $service_id - integer. Post ID from wp_posts table
     * @return mixed
     */
    public function cloud_search_update_service( $service_id ){
        $service = new Service( $service_id );
        $service->load();
        if( ! $service->id ){
            return $this->cloud_search_delete_item( $service_id, 'service' );
        }
        $fulltext = new FulltextSearch();
        $fulltext->fullUpload();
        $this->_initial_upload();
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
        if( $type == 'agreement' ){
            array_push( $data, array( 'type' => 'delete', 'id' => $type.'_requester_'.$id  ) );
            array_push( $data, array( 'type' => 'delete', 'id' => $type.'_responder_'.$id  ) );
        } else{
            array_push( $data, array( 'type' => 'delete', 'id' => $type.'_'.$id  ) );
        }
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