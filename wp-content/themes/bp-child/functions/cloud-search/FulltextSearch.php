<?php

class FulltextSearch {

    private $_documentEndpoint = '';
    private $_searchDomainURL = '';

    public function __construct(){
        $this->_documentEndpoint = get_option( 'cloudsearch_fulltext_document_endpoint' );
        $this->_searchDomainURL  = get_option( 'cloudsearch_fulltext_search_endpoint' );
    }

    public function search( $params = false, $full_results = false ){
        $str = array();
        $str['return'] = '_all_fields';
        $str['facet.post_type'] = '{}';
        $str['facet.community'] = '{}';
//        $str['facet.last_updated_date'] = '{}';
        if( $full_results ){
            $str['size'] = 10000;
        } else{
            $str['size'] = SEARCH_RESULTS_LIMIT;
        }
        $l = '';
        $range_checked = false;
        if( is_user_logged_in() ) {
            $l .= "  (or ( term field=visibility 1 ) (  term field=visibility 3   ) ( term field=visibility 2 ) )";//( term field=user_id ".get_current_user_id()." )
        } else{
            $l .= "  ( term field=visibility 1 )";
        }

        $str['sort'] = 'post_title asc';

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
                        $l .= "(range field=last_updated_date $from, $to   ) ";
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

    public function fullUpload( $post_id = false ){
        global $wpdb;
        $data = array();
        if( $post_id ){
            $posts = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM wp_posts WHERE post_type IN( 'page', 'forum', 'product-service', 'service', 'test-case', 'test-suite', 'topic', 'bp_doc' ) AND ID = %d ", $post_id  ) );
        } else{
            $posts = $wpdb->get_results( "SELECT * FROM wp_posts WHERE post_type IN( 'page', 'forum', 'product-service', 'service', 'test-case', 'test-suite', 'topic', 'bp_doc' )" );
        }
        foreach ($posts AS $post) {
            $groups = groups_get_user_groups($post->post_author);
            $communityNames = array();
            if (is_array($groups['groups'])) {
                foreach ($groups['groups'] AS $group) {
                    $communityNames[] = $wpdb->get_var($wpdb->prepare("SELECT name FROM wp_bp_groups WHERE id = %d ", $group));
                }
            }
            if (empty($communityNames)) {
                $communityNames = array( 'ebMS3', 'SuperStream' );
                $groups['groups'] = array( 32, 35 );
            }
            if( $post->post_type == 'test-suite' && $post->ID != $wpdb->get_var( $wpdb->prepare( "SELECT suite_id FROM wp_test_suites WHERE family_mark IN( SELECT family_mark FROM wp_test_suites WHERE suite_id = %d ) ORDER BY suite_id DESC LIMIT 1", $post->ID ) ) ){
                continue;
            }
            $post_data = $this->_processPost($post);
            $temp_data = array(
                'community' => $communityNames,
                'last_updated_date' => date('Y-m-d\TH:i:s', strtotime($post->post_modified)) . 'Z',
                'post_author_name' => cp_get_user_fullname($post->post_author),
                'post_author_id' => $post->post_author,
                'post_content' => $post_data['descr'],
                'post_status' => $post->post_status,
                'post_title' => $post->post_title,
                'post_type' => $post_data['type'],
                'post_id' => $post->ID,
                'visibility' => $post_data['visibility'],
                'community_id' => $groups['groups'],
                'for_search' => $post_data['for_search'],
                'link' => get_permalink($post->ID)
            );
            array_push($data, array('type' => 'add', 'id' => $post->ID, 'fields' => $temp_data));
        }
        $test_scenarios = $wpdb->get_results( "SELECT * FROM wp_test_suites_scenarios" );
        foreach( $test_scenarios AS $test_scenario ){
            $post = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM wp_posts WHERE ID = %d ", $test_scenario->suite_id ) );
            if( ! $post ){
                continue;
            }
            $groups = groups_get_user_groups($post->post_author);
            $communityNames = array();
            if (is_array($groups['groups'])) {
                foreach ($groups['groups'] AS $group) {
                    $communityNames[] = $wpdb->get_var($wpdb->prepare("SELECT name FROM wp_bp_groups WHERE id = %d ", $group));
                }
            }
            if (empty($communityNames)) {
                $communityNames = array('All');
                $groups['groups'] = array(0);
            }
            $temp_data = array(
                'community' => $communityNames,
                'last_updated_date' => date('Y-m-d\TH:i:s', strtotime($post->post_modified)) . 'Z',
                'post_author_name' => cp_get_user_fullname($post->post_author),
                'post_author_id' => $post->post_author,
                'post_content' => $test_scenario->description,
                'post_status' => $post->post_status,
                'post_title' => $test_scenario->code,
                'post_type' => 'Test Scenario',
                'post_id' => $post->ID,
                'visibility' => 1,
                'community_id' => $groups['groups'],
                'for_search' => $test_scenario->description . 'Test Scenario' .  $test_scenario->code . cp_get_user_fullname($post->post_author),
                'link' => get_permalink($post->ID)
            );
            array_push($data, array('type' => 'add', 'id' => 'scenario_'.$test_scenario->id, 'fields' => $temp_data));
        }
        $data = json_decode($this->_sendDataToSearchDomain($data), true);
        echo 'Status: ' . $data['status'].'<br>Added: ' . $data['adds'].'<br>Deleted: ' . $data['deletes'];
    }

    public function fullDelete( $post_id = false ){
        global $wpdb;
        $data = array();
        if( $post_id ){
            $posts = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM wp_posts WHERE post_type IN( 'page', 'forum', 'product-service', 'service', 'test-case', 'test-suite', 'topic', 'bp_doc' ) AND ID = %d ", $post_id) );
        } else {
            $posts = $wpdb->get_results("SELECT * FROM wp_posts WHERE post_type IN( 'page', 'forum', 'product-service', 'service', 'test-case', 'test-suite', 'topic', 'bp_doc' )");
        }
        foreach( $posts AS $post ){
            array_push( $data, array( 'type' => 'delete', 'id' => $post->ID ) );
        }
        $test_scenarios = $wpdb->get_results( "SELECT * FROM wp_test_suites_scenarios" );
        foreach( $test_scenarios AS $test_scenario ){
            array_push( $data, array( 'type' => 'delete', 'id' => 'scenario_'.$test_scenario->id ) );
        }
        $data = json_decode( $this->_sendDataToSearchDomain( $data ), true );
        echo '<b>Status:</b> '.$data['status'].'<br><b>Added:</b> '.$data['adds'].'<br><b>Deleted:</b> '.$data['deletes'];
    }
    private function _processPost( $post ){
        switch( $post->post_type ){
            case 'page':
                $data = array(
                    'type'       => 'Page',
                    'visibility' => 1,
                    'for_search' => 'Page',
                    'descr'      => $post->post_content
                );
                break;

            case 'product-service':
                $product = new ProductAndService( $post->ID );
                $product->load();
                $data = array(
                    'type'       => 'Product',
                    'visibility' => $product->visibility == 'Public' ? 1 : 3,
                    'for_search' => 'Product',
                    'descr'      => $product->descrition
                );
                break;
            case 'service':
                $service = new Service( $post->ID );
                $service->load();
                $data = array(
                    'type'       => 'Service',
                    'visibility' => $service->service_visibility == 'Public' ? 1 : 3,
                    'for_search' => 'Service',
                    'descr'      => $service->service_description
                );
                break;
            case 'test-case':
                $data = array(
                    'type'       => 'Test Case',
                    'visibility' => 1,
                    'for_search' => 'Test Case',
                    'descr'      => get_post_meta( $post->ID, 'test_intent_description', true )
                );
                break;
            case 'test-suite':
                $data = array(
                    'type'       => 'Test Suite',
                    'visibility' => 1,
                    'for_search' => 'Test Suite',
                    'descr'      => get_post_meta( $post->ID, 'ts_description', true )
                );
                break;
            case 'topic':
                $data = array(
                    'type'       => 'Forum Topic',
                    'visibility' => 1,
                    'for_search' => 'Forum Topic',
                    'descr'      => $post->post_content
                );
                break;
            case 'forum':
                $data = array(
                    'type'       => 'Forum Post',
                    'visibility' => 1,
                    'for_search' => 'Forum Post',
                    'descr'      => $post->post_content
                );
                break;
            case 'bp_doc':
                $data = array(
                    'type'       => 'Wiki Article',
                    'visibility' => 1,
                    'for_search' => 'Wiki Article',
                    'descr'      => $post->post_content
                );
                break;
            default:
                $data = array(
                    'type'       => '',
                    'visibility' => 1,
                    'for_search' => '',
                    'descr'      => ''
                );
                break;
        }
        return $data;
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