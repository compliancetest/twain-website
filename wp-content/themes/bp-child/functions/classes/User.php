<?php

namespace User;


class User {

    public static function isGatewayConfigured()
    {
        global $wpdb;
        $subscription = $wpdb->get_row($wpdb->prepare("SELECT * FROM wp_users_subscriptions WHERE user_id = %d", get_current_user_id()));
        return !empty($subscription->profile_id);
    }

    public static function isProfileUsedInUserGateway($profileID, $userId = null)
    {
        global $wpdb;
        if (!$userId) {
            $userId = get_current_user_id();
        }
        return (boolean) $wpdb->get_row($wpdb->prepare("SELECT * FROM wp_users_subscriptions WHERE user_id = %d AND profile_id = %d", $userId, $profileID));
    }
}