<?php

namespace ClaimsConversations;


class ClaimsConversations {

    public static function add( $row )
    {
        global $wpdb;
        return $wpdb->replace( 'wp_compliance_claims_conversations',
                array(
                    'claim_id' => $row['claim_id'],
                    'conv_id'  => $row['conv_id']
                ),
                array( '%d', '%d' )
            );
    }

    public static function doesConversationExists( $conversationId )
    {
        global $wpdb;
        return (boolean) $wpdb->get_results( $wpdb->prepare("SELECT id FROM wp_compliance_claims_conversations WHERE conv_id = %d ", $conversationId ) );
    }

    /**
     * Function filters conversations ids and return only those which could be deleted
     * @param $ids - array
     * @return array
     */
    public static function filterConversationsIds( $ids )
    {
        global $wpdb;
        if( empty( $ids ) ){
            return array();
        }
        $idstoExclude = $wpdb->get_var("SELECT GROUP_CONCAT( conv_id ) FROM wp_compliance_claims_conversations WHERE conv_id IN(".implode( ',', $ids).") " );
        return array_diff( $ids, explode( ',', $idstoExclude ) );
    }

    public static function deleteByClaimId( $claimId )
    {
        global $wpdb;
        return $wpdb->query( $wpdb->prepare("DELETE FROM wp_compliance_claims_conversations WHERE claim_id = %d ", $claimId ) );
    }
}