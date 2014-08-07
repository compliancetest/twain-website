<?php
/***
* Define the functions that are related with User Subscriptions
* 
*/

if(!defined('HARNESS_ENDPOINT_URL'))
    define('HARNESS_ENDPOINT_URL', 'https://esb.compliancetest.net/services/Superstream');

/** 
* Process Eway Payment
* 
* @param Int $customer_id
* @param Int $amount
*/
function processEwayPayment($eway_profile_id, $amount, $invoiceDescription = '')
{
    $webserviceURL = get_eway_token_webservice_url();
    $customerID = get_eway_customer_id();
    $userName = get_eway_user_name();
    $userPWD = get_eway_user_pwd();
    
    require_once(THE_FUNCTION . '/soap/nusoap.php');
    
    $client = new nusoap_client($webserviceURL, false);
    $err = $client->getError();
    
    if ($err) {
        return array('ewayTrxnStatus' => 'False', 'ewayTrxnError' => 'Soap Construction Error: ' . $err);
    }
    
    $client->namespaces['man'] = 'https://www.eway.com.au/gateway/managedpayment';
    $headers = "<man:eWAYHeader><man:eWAYCustomerID>" . $customerID . "</man:eWAYCustomerID><man:Username>" . $userName . "</man:Username><man:Password>" . $userPWD . "</man:Password></man:eWAYHeader>";
    $client->setHeaders($headers);    
    
    $requestbody = array(
        'man:managedCustomerID' => $eway_profile_id,
        'man:amount' => $amount * 100,
//            'man:cvn' => $card->cvn,
//        'man:invoiceReference' => '',
        'man:invoiceDescription' => $invoiceDescription
    );
    $soapaction = 'https://www.eway.com.au/gateway/managedpayment/ProcessPayment';
    $result = $client->call('man:ProcessPayment', $requestbody, '', $soapaction);
    
    return $result;
}

/**
* Get the list of the subscribed suites
* 
* @param Int $user_id
* @return []
*/
function getUserSubscribedSuites($user_id = null)
{
    global $wpdb;
    
    if($user_id == null)
        $user_id = get_current_user_id();
        
    $query = $wpdb->prepare(
        "SELECT sp.*, p.post_title as `name` FROM " . $wpdb->prefix . "users_subscriptions AS sp " .
        "LEFT JOIN " . $wpdb->posts . " AS p ON p.ID=sp.suite_id " .
        "WHERE sp.user_id=%d AND p.ID IS NOT NULL", $user_id
    );
    
    $rows = $wpdb->get_results($query);
    
    return $rows;
}

/**
* Calculate the first paid amount by Pro Rata
* 
* @param Int $monthly_price
*/
function calculateFirstPaymentAmount($monthly_price)
{
    $remainedDay = (strtotime(date("Y-m-d", mktime(0, 0, 0, date('n') + 1, 1, date("Y")))) - strtotime(date("Y-m-d"))) / 86400;
    $totalDay = date("t");
    
    return ceil($monthly_price * ($remainedDay / $totalDay));
}

/**
* Get the count of the subscriptions with the purchase id
* 
* @param mixed $purchase_id
*/
function getNumSubscriptions($purchase_id)
{
    global $wpdb;
    
    $query = $wpdb->prepare("SELECT count(1) FROM {$wpdb->prefix}users_subscriptions WHERE purchase_id=%d", $purchase_id);
    $count = $wpdb->get_var($query);
    
    return $count;
    
}

/**
* Check the user purchased a subscription to one of the brother test suites 
* 
* @param Int $suite_id
* 
* @return Boolean
*/
function isPurchasedForOtherVersions($familyMark, $user_id = null)
{
    global $wpdb;
    
    if(!$user_id)
        $user_id = get_current_user_id();
    
    if(!$user_id)
        return false;
            
    $query = $wpdb->prepare("SELECT count(DISTINCT(s.id)) FROM {$wpdb->prefix}users_subscriptions AS s
                             INNER JOIN {$wpdb->prefix}test_suites AS ts ON s.suite_id=ts.suite_id
                             WHERE ts.family_mark=%d AND s.user_id=%d AND s.status != 'Unsubscribing'", $familyMark, $user_id);
    $count = $wpdb->get_var($query);
    
    return !$count ? false : true;
}


function getOrganisationPurchaseId($familyMark, $user_id = null)
{
    global $wpdb;
    
    if(!$user_id)
        $user_id = get_current_user_id();
    
    if(!$user_id)
        return false;
        
    $user = get_userdata($user_id);
    
    $user_domain = substr($user->user_email, strpos($user->user_email, '@'));
    
    $query = $wpdb->prepare("SELECT s.purchase_id, op.user_count, op.joined_count FROM {$wpdb->prefix}users_subscriptions AS s " .
             "LEFT JOIN {$wpdb->prefix}test_suites AS ts ON s.suite_id=ts.suite_id " .
             "LEFT JOIN {$wpdb->prefix}users_organisation_pricing AS op ON s.user_id=op.user_id AND ts.family_mark=op.family_mark " .
             "LEFT JOIN {$wpdb->users} AS u ON s.user_id=u.ID " .
             "WHERE op.family_mark=%d AND u.user_email LIKE %s", $familyMark, '%' . $user_domain);
    
    $prow = $wpdb->get_row($query);
    
    if(!$prow)
        return false;
    
    if($prow->user_count <= $prow->joined_count)
        return false;
    
    return $prow->purchase_id;
}

/**
* Get Subscription Monthly Fee
* 
* @param Int or CT_Subscription $subscription
*/
function getSubscriptionMonthlyFee($subscription, $user_id = null)
{
    if(!is_a($subscription, 'CT_Subscription'))
        $subscription = new CT_Subscription($subscription);
    
    if(!$user_id)
        $user_id = get_current_user_id();
    
    
    $suiteMonthlyFee = doubleval(get_post_meta($subscription->suite_id, 'monthly_subscription_price', true));
    
    
    $monthlyFee = min($suiteMonthlyFee, $subscription->monthly_fee);
    
    
    $userMonthlyFees = get_user_meta($user_id, 'monthly_fee', true);
    
    if(isset($userMonthlyFees[$subscription->suite_id]))
        $monthlyFee = doubleval($userMonthlyFees[$subscription->suite_id]);
        
    return $monthlyFee;
}

function getSubscriptionMonthlyFee2($suite_id, $subMonthlyFee, $user_id = null)
{
    if(!$user_id)
        $user_id = get_current_user_id();
    
    $suiteMonthlyFee = doubleval(get_post_meta($suite_id, 'monthly_subscription_price', true));    
    
    $monthlyFee = min($suiteMonthlyFee, $subMonthlyFee);    
    
    $userMonthlyFees = get_user_meta($user_id, 'monthly_fee', true);
    
    if(isset($userMonthlyFees[$suite_id]))
        $monthlyFee = doubleval($userMonthlyFees[$suite_id]);
        
    return $monthlyFee;
}

function getExpiredPayments()
{
    global $wpdb;
    
    $cur_date = date("Y-m-d");
    
    $query = $wpdb->prepare("SELECT up.*, count(us.id) AS subscriptions, ts.family_mark FROM {$wpdb->prefix}users_purchases AS up 
                            LEFT JOIN {$wpdb->prefix}users_subscriptions AS us ON up.id=us.purchase_id
                            LEFT JOIN {$wpdb->prefix}test_suites AS ts ON ts.suite_id=us.suite_id
                            WHERE up.expiry_date <= %s
                            GROUP BY up.id
                            ", $cur_date);
    $rows = $wpdb->get_results($query);
    
    return $rows;
}

function ct_get_months($date1, $date2)
{
    $date1 = strtotime(date("Y-m-01", strtotime($date1)));
    $date2 = strtotime(date("Y-m-01", strtotime($date2)));
    
    $months = 0;
    while($date1 < $date2)
    {
        
        $months++;
        $date1 = strtotime("+1 MONTH", $date1);        
    }
    
    return $months;
}


function _ct_manage_subscriptions_get_statuses_html($name = 'status', $default = '', $attr = '')
{
    $statuses = array('Active', 'InArrears', 'Frozen', 'Unsubscribing');
    $html = '<select name="' . $name . '" ' .  $attr . '>';
    foreach($statuses as $s)
    {
        $html .= '<option value="' . $s . '" ' . ($default == $s ? 'selected="selected"' : '') . '>' . $s . '</option>';
    }
    $html .= '</select>';
    
    return $html;
}

function _ct_manage_subscriptions_get_statuses_html2($name = 'status', $default = '', $attr = '')
{
    $statuses = array('Active', 'Suspended');
    $html = '<select name="' . $name . '" ' .  $attr . '>';
    foreach($statuses as $s)
    {
        $html .= '<option value="' . $s . '" ' . ($default == $s ? 'selected="selected"' : '') . '>' . $s . '</option>';
    }
    $html .= '</select>';
    
    return $html;
}