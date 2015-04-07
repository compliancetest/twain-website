<?php

class ProfileType {

    public static function getExpandableTypes(){
        global $wpdb;
        $response = array();
        $results = $wpdb->get_results( "SELECT * FROM wp_community_profile_types WHERE is_expandable = 1 " );
        if( $results ){
            foreach( $results AS $result ){
                $response[] = str_replace( ' ', '', $result->title );
            }
        }
        $response[] = 'SMSF';
        return $response;
    }
}