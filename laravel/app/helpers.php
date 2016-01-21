<?php

function isChecked($checkboxValue, $valueToVerify)
{
    return $valueToVerify == $checkboxValue ? 'checked="checked"' : '';
}

function sendEmails($sendTo, $template, $emailData)
{
    foreach($sendTo as $user) {
        $userData = get_userdata($user->user_id)->data;
        cp_send_email(array('name' => $userData->display_name, 'email' => $userData->user_email), $template, $emailData);
    }
}