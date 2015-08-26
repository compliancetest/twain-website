<?php

class Tag {

    public static function assignTag( $tagName, $itemId, $itemType = 'PROFILE' ){
        global $wpdb;
        $tag = $wpdb->get_row($wpdb->prepare("SELECT * FROM wp_tags WHERE name = %s", $tagName));
        $tagId = 0;
        if ($tag){
            $tagId = $tag->id;
        }
        if( ! $tagId ){
            $wpdb->insert( 'wp_tags',
                    array( 'name' => trim( $tagName ) ),
                    array( '%s' )
                );
            $tagId = $wpdb->insert_id;
        }
        $wpdb->query($wpdb->prepare("INSERT INTO `wp_tags2items` (`item_id`, `item_type`, `tag_id`) VALUES (%d, %s,%d) ON duplicate key update `item_id`= values(`item_id`), `item_type`= values(`item_type`), `tag_id`= values(`tag_id`)", $itemId, $itemType, $tagId));
    }

    public static function copyTags( $sourceProfileId, $targetProfileId )
    {
        global $wpdb;
        return $wpdb->query( $wpdb->prepare( "INSERT INTO wp_tags2items ( item_id, item_type, tag_id ) ( SELECT '{$targetProfileId}' AS item_id, item_type, tag_id FROM wp_tags2items WHERE item_id = %d )", $sourceProfileId ) );
    }
    public static function getItemTags( $itemId, $itemType = 'PROFILE' ){
        global $wpdb;
        return $wpdb->get_results( $wpdb->prepare( "SELECT t.* FROM wp_tags2items AS t2a
                                                    JOIN wp_tags AS t ON t.id = t2a.tag_id
                                                    WHERE item_id = %d AND item_type = '%s'", $itemId, $itemType ));
    }

    public static function clearItemTags( $itemId, $itemType = 'PROFILE' ){
        global $wpdb;
        return $wpdb->query( $wpdb->prepare("DELETE FROM wp_tags2items WHERE item_id = %d AND item_type = %s ", $itemId, $itemType ) );
    }
}