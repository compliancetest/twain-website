<?php

/**
 * This class used to override default laravel hashing functionality
 * because we using wordpress wp_users table for auth purposes.
 */
namespace App;
use Illuminate\Contracts\Hashing\Hasher as HasherContract;

class WordpressHasher implements HasherContract {

    public function make($value, array $options = array()) {
        return wp_hash_password($value);
    }

    public function check($value, $hashedValue, array $options = array()) {
        return wp_check_password($value, $hashedValue);
    }

    public function needsRehash($hashedValue, array $options = array()) {
        return false;
    }

}