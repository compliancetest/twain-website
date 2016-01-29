<?php

function isChecked($checkboxValue, $valueToVerify)
{
    return $valueToVerify == $checkboxValue ? 'checked="checked"' : '';
}

function sendEmails($sendTo, $template, $emailData)
{
    foreach ($sendTo as $user) {
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