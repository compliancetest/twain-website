<?php

namespace LoginAttempts;


class LoginAttempts
{

    public static function getAttempts($ipAddress)
    {
        global $wpdb;
        return intval($wpdb->get_var($wpdb->prepare("SELECT attempts FROM wp_login_attempts WHERE ip = %s", $ipAddress)));
    }

    public static function setAttempts($ipAddress, $attempts = false)
    {
        global $wpdb;
        if ($attempts === false) {
            $attempts = self::getAttempts($ipAddress) + 1;
        }
        if ($wpdb->get_row($wpdb->prepare("SELECT * FROM wp_login_attempts WHERE ip = %s", $ipAddress))) {
            $wpdb->update('wp_login_attempts',
                array(
                    'attempts' => $attempts,
                    'last_attempts_date' => date('Y-m-d H:i:s')
                ),
                array(
                    'ip' => $ipAddress
                ),
                array('%d', '%s'),
                array('%s')
            );
        } else {
            $wpdb->insert('wp_login_attempts',
                array(
                    'attempts' => $attempts,
                    'ip' => $ipAddress,
                    'last_attempts_date' => date('Y-m-d H:i:s')
                ),
                array('%d', '%s', '%s')
            );
        }
    }

    public static function getUserIP()
    {
        if (isset($_SERVER['HTTP_CLIENT_IP']) && !empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = (isset($_SERVER['REMOTE_ADDR'])) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0';
        }

        $ip = filter_var($ip, FILTER_VALIDATE_IP);
        $ip = ($ip === false) ? '0.0.0.0' : $ip;
        return $ip;
    }
}