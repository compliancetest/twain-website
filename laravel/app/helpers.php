<?php
function is_selected($v1, $v2)
{
    return $v1 == $v2 ? 'selected="selected"' : '';
}

function is_readonly($status = false)
{
    return $status ? 'readonly="readonly"' : '';
}

function is_disabled($status = false)
{
    return $status ? 'disabled="disabled"' : '';
}

function isChecked($checkboxValue, $valueToVerify)
{
    return $valueToVerify == $checkboxValue ? 'checked="checked"' : '';
}

function sendEmails($sendTo, $template, $emailData)
{
    foreach ($sendTo as $user) {
        $user = json_decode(json_encode($user));
        $userData = get_userdata($user->user_id)->data;

        cp_send_email(array('name' => $userData->display_name, 'email' => $userData->user_email), $template, $emailData);
    }
}

function getClientIP()
{
    return '31.202.16.252';
    if (isset($_SERVER)) {

        if (isset($_SERVER["HTTP_X_FORWARDED_FOR"]))
            return $_SERVER["HTTP_X_FORWARDED_FOR"];

        if (isset($_SERVER["HTTP_CLIENT_IP"]))
            return $_SERVER["HTTP_CLIENT_IP"];

        return $_SERVER["REMOTE_ADDR"];
    }

    if (getenv('HTTP_X_FORWARDED_FOR'))
        return getenv('HTTP_X_FORWARDED_FOR');

    if (getenv('HTTP_CLIENT_IP'))
        return getenv('HTTP_CLIENT_IP');

    return getenv('REMOTE_ADDR');
}

function dateDiffForHumans($date)
{
    $now = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', formatDate(date('Y-m-d H:i:s'), 'Y-m-d H:i:s'));
    return \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', formatDate($date, 'Y-m-d H:i:s'))->diffForHumans($now);
}

function getSiteUrl()
{
    $useHttps = getenv('ENVIRONMENT') == 'local' ? false : true;
    return url()->to('/', [], $useHttps);
}

/**
 * get meta_value from wp_postmeta value for provided post
 * @param $postId
 * @param $metaKey
 * @return bool
 */
function getPostMeta($postId, $metaKey)
{
    $meta = \App\PostMeta::where(['post_id' => $postId, 'meta_key' => $metaKey])->first();
    if ($meta) {
        return $meta->meta_value;
    }
    return false;
}