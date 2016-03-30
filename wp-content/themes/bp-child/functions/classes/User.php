<?php

namespace User;


class User
{

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
        return (boolean)$wpdb->get_row($wpdb->prepare("SELECT * FROM wp_users_subscriptions WHERE user_id = %d AND profile_id = %d", $userId, $profileID));
    }

    /**
     *   at least one lowercase char
     *   at least one uppercase char
     *   at least one digit
     *   at least one special sign of [~`!#$%\^&*+=\-\[\]\\';,|\\":<>\?]
     *
     * @param $password
     * @return bool
     */
    public static function isPasswordValid($password)
    {
        $e = preg_match('/.{8,}/', $password);//At least 8 chars
        $a = preg_match('/[0-9]+/', $password);//numeric
        $b = preg_match('/[A-Z]+/', $password);//Capitals
        $c = preg_match('/[a-z]+/', $password);//small letters
        $d = preg_match('/[~`!#$%\^&*+=\-\[\]\\\';,|\\":<>\?]/', $password);//special chars

        if ($a && $b && $c && $d && $e) {
            return true;
        }
        return false;
    }
}