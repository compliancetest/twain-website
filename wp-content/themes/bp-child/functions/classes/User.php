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

    /**
     *   at least one lowercase char
     *   at least one uppercase char
     *   at least one digit
     *   at least one special sign of @#-_$%^&+=§!?
     *
     * @param $password
     * @return bool
     */
    public static function isPasswordValid($password)
    {
        if(!preg_match('/^(?=.*\d)(?=.*[@#\-_$%^&+=§!\?])(?=.*[a-z])(?=.*[A-Z])[0-9A-Za-z@#\-_$%^&+=§!\?]{8,20}$/', $password)) {
            return false;
        }
        return true;
    }
}