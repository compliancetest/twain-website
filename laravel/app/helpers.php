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
    return env('APP_URL');
}