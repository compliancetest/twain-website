<?php

class FulltextSearch {

    private $_documentEndpoint = '';
    private $_searchDomainURL = '';

    public function __construct(){
        $this->_documentEndpoint = get_option( 'cloudsearch_fulltext_document_endpoint' );
        $this->_searchDomainURL  = get_option( 'cloudsearch_fulltext_search_endpoint' );
    }

    public function fullUpload( $post_id = false ){
        global $wpdb;
        $data = array();
        if( $post_id ){
            $posts = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM wp_posts WHERE post_type IN( 'page', 'forum', 'product-service', 'service', 'test-case', 'test-suite', 'topic', 'bp_doc' ) AND ID = %d ", $post_id  ) );
        } else{
            $posts = $wpdb->get_results( "SELECT * FROM wp_posts WHERE post_type IN( 'page', 'forum', 'product-service', 'service', 'test-case', 'test-suite', 'topic', 'bp_doc' )" );
        }
        if( $posts ) {
            foreach ($posts AS $post) {
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
                $post_data = $this->_processPost($post);
                $temp_data = array(
                    'community' => $communityNames,
                    'last_updated' => date('Y-m', strtotime($post->post_modified)),
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
            $data = json_decode($this->_sendDataToSearchDomain($data), true);
            echo 'Status: ' . $data['status'];
            echo '<br>Added: ' . $data['adds'];
            echo '<br>Deleted: ' . $data['deletes'];
        }
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
        $data = json_decode( $this->_sendDataToSearchDomain( $data ), true );
        echo '<b>Status:</b> '.$data['status'];
        echo '<br><b>Added:</b> '.$data['adds'];
        echo '<br><b>Deleted:</b> '.$data['deletes'];
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
                    'descr'      => $post->post_content
                );
                break;
            case 'test-suite':
                $data = array(
                    'type'       => 'Test Suite',
                    'visibility' => 1,
                    'for_search' => 'Test Suite',
                    'descr'      => $post->post_content
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