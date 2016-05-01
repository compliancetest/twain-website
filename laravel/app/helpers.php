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

    error_log(json_encode($_SERVER));
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

//if(!function_exists('getFilterParam')) {
//    function getFilterParam($name)
//    {
//        $param = array();
//        if (isset($_GET[$name]))
//            $param = $_GET[$name];
//        if (!is_array($param))
//            $param = array($param);
//
//        return $param;
//    }
//}

function getSiteUrl()
{
    $useHttps = getenv('ENVIRONMENT') == 'local' ? false : true;
    return url()->to('/', [], $useHttps);
}