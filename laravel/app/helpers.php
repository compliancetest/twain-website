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

function isServerValidationEnabled()
{
    $option = \App\WpOptions::where(['option_name' => 'server_validation'])->first();
    if ($option && $option->option_value == 'yes') {
        return true;
    }
    return false;
}

function isImageViewerEnabled()
{
    $option = \App\WpOptions::where(['option_name' => 'image_viewer'])->first();
    if ($option && $option->option_value == 'yes') {
        return true;
    }
    return false;
}

/**
 * Ensure that test case's transactions with 'Pending' status not used in another verify requests
 * @param $testSuiteId
 * @param $testCaseId
 * @param $productId
 * @return bool
 */
function checkTransactionsCanBeAddedToRequest($testSuiteId, $testCaseId, $productId)
{
    $transactions = [];
    $verifyRequests = \App\VerifyRequest::where(['test_suite_id' => $testSuiteId, 'product_id' => $productId])->get();
    foreach ($verifyRequests as $verifyRequest) {
        $transactions = array_merge($transactions, json_decode($verifyRequest->transactions, true));
    }
    if (\App\Transaction::whereIn('id', $transactions)
        ->where('test_outcome_status_id', \App\TestOutcomeStatus::getIdByCode('PENDING'))
        ->where('test_case_id', $testCaseId)
        ->get()->isEmpty()
    ) {
        return true;
    }
    return false;
}