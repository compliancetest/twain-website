<?php
/**
* Get All Organisations
* 
*/
function ct_get_all_organisations()
{
    global $wpdb;
    
    $query = "SELECT * FROM {$wpdb->prefix}organisations ORDER BY organisation_name";
    $rows = $wpdb->get_results($query);
    
    return $rows;
}

/**
* Check the user is an organisation admin
* 
* @param Int $user_id
* @param Int $organisation_id
* 
* @return organisation id or false
*/
function ct_is_organisation_admin($user_id = null, $organisation_id = null)
{
    global $wpdb;
    
    if(!$user_id)
        $user_id = get_current_user_id();
    
    $query = $wpdb->prepare("SELECT organisation_id FROM {$wpdb->prefix}organisations_members WHERE user_id=%d AND is_admin=1", $user_id);
    if($organisation_id) {
        $query .= $wpdb->prepare(" AND organisation_id=%d", $organisation_id);
    }
    
    $id = $wpdb->get_var($query);
    
    return $id;
}

function ct_get_organisation_admin($organisation_id)
{
    global $wpdb;
    
    $query = $wpdb->prepare("SELECT u.* FROM {$wpdb->prefix}organisations_members AS om LEFT JOIN {$wpdb->users} AS u ON u.ID=om.user_id WHERE om.organisation_id=%d AND om.user_id=u.ID", $organisation_id);
    $data = $wpdb->get_row($query);
    
    return $data;
}

/**
* Check the organisation purchased a subscription to the test suite
* 
* @param Int $organisation_id
* @param Int $suite_id
* @return subscription id or FALSE
*/
function ct_is_organisation_purchased_subscription($organisation_id, $suite_family_mark)
{
    global $wpdb;
    
    $query = $wpdb->prepare("SELECT id FROM {$wpdb->prefix}organisations_subscriptions AS os                             
            WHERE os.organisation_id=%d AND os.suite_family_mark=%d", $organisation_id, $suite_family_mark);
    $id = $wpdb->get_var($query);
    
    return !$id ? false : $id;
}

/**
* Get organisation subscription
* 
* @param Int $organisation_id
* @param Int $suite_family_mark
* 
* @array or null
*/
function ct_get_organisation_subscription($organisation_id, $suite_family_mark)
{
    global $wpdb;
    
    $query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}organisations_subscriptions                             
            WHERE organisation_id=%d AND suite_family_mark=%d", $organisation_id, $suite_family_mark);
    $data = $wpdb->get_row($query);
    
    return $data;
}

/**
* Get user subscription
* 
* @param int $user_id
* @param int $suite_family_mark
* 
* @return array or null
*/
function ct_get_assigned_organisation_subscription($user_id, $suite_family_mark)
{
    global $wpdb;
    
    $query = $wpdb->prepare("SELECT * from {$wpdb->prefix}organisations_subscriptions 
                            WHERE user_id=%d AND suite_family_mark=%d AND `status` != 'Unsubscribing'", $user_id, $suite_family_mark);
       
    $data = $wpdb->get_row($query);
    
    return $data;
}

function ct_get_user_subscriptions($user_id)
{
    global $wpdb;
    
    $query = $wpdb->prepare("SELECT us.*, os.status, os.nickname from {$wpdb->prefix}users_subscriptions AS us
                             LEFT JOIN {$wpdb->prefix}organisations_subscriptions AS os ON os.id=us.parent_id
                            WHERE us.user_id=%d", $user_id);
       
    $data = $wpdb->get_results($query);
    
    return $data;
}

function ct_get_suite_harness_detail($user_id, $suite_id)
{
    global $wpdb;
    
    $query = $wpdb->prepare("SELECT * from {$wpdb->prefix}users_subscriptions 
                            WHERE user_id=%d AND suite_id=%d", $user_id, $suite_id);
       
    $data = $wpdb->get_row($query);
    
    return $data;
}

/**
* Get the organisation of $user_id
* If $user_id is null, get the organisation of the current logged user
* 
* @param Int $user_id
* 
* @return Array or null
*/
function ct_get_user_organisation($user_id = null)
{
    global $wpdb;
    
    if(!$user_id)
        $user_id = get_current_user_id();
    
    $query = $wpdb->prepare("SELECT o.* FROM {$wpdb->prefix}organisations_members AS m 
                                LEFT JOIN {$wpdb->prefix}organisations AS o ON m.organisation_id=o.id
                             WHERE m.user_id=%d", $user_id);
    $data = $wpdb->get_row($query);

    return $data;    
}

function ct_get_organisation_unallocated_subscriptions($organisation_id, $suite_family_mark)
{
    global $wpdb;
    
    $query = $wpdb->prepare("SELECT count(id) FROM {$wpdb->prefix}organisations_subscriptions WHERE organisation_id=%d AND suite_family_mark=%d AND user_id=0", $organisation_id, $suite_family_mark);
    $c = $wpdb->get_var($query);
    
    return $c;
}

function ct_get_organisation_subscriptions($organisation_id)
{
    global $wpdb;
    
    $query = $wpdb->prepare("SELECT os.*, u.user_email, u.display_name, t.suite_title FROM {$wpdb->prefix}organisations_subscriptions AS os 
                            LEFT JOIN {$wpdb->users} AS u ON u.ID=os.user_id 
                            LEFT JOIN {$wpdb->prefix}test_suites AS t ON t.family_mark=os.suite_family_mark
                            WHERE us.organisation_id=%d", $organisation_id);
    
    $rows = $wpdb->get_results($query);
    
    return $rows;
}

function ct_get_organisation_subscription_by_id($subscription_id)
{
    global $wpdb;
    
    $query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}organisations_subscriptions WHERE id=%d", $subscription_id);
    $data = $wpdb->get_row($query);
    
    return $data;
}

function ct_get_organisation_subscription_by_user_subscription_id( $user_subscription_id ){
    global $wpdb;

    $query = $wpdb->prepare("SELECT os.* FROM {$wpdb->prefix}users_subscriptions AS us JOIN {$wpdb->prefix}organisations_subscriptions AS os ON us.parent_id = os.id  WHERE us.id=%d", $user_subscription_id);
    $data = $wpdb->get_row($query);

    return $data;
}

function ct_get_organisation_by_user_subscription_id( $user_subscription_id ){
    global $wpdb;

    $query = $wpdb->prepare("SELECT o.* FROM {$wpdb->prefix}users_subscriptions AS us JOIN {$wpdb->prefix}organisations AS o ON o.id = us.organisation_id  WHERE us.id=%d", $user_subscription_id);
    $data = $wpdb->get_row($query);

    return $data;
}
function ct_get_test_suites_without_version()
{
    global $wpdb;
    
    $query = "SELECT family_mark, suite_title, suite_id FROM {$wpdb->prefix}test_suites GROUP BY family_mark ORDER BY suite_title";
    $data = $wpdb->get_results($query);
    
    return $data;
}

function ct_get_user_viewable_subscriptions($user_id, $org_id = null)
{
    global $wpdb;
    
            
    $user_data = get_userdata($user_id);
    
    if($org_id == 'all')
        $org_id = null;
    
    if (is_super_admin()) {
        $query = "SELECT os.nickname, os.id, os.organisation_id FROM {$wpdb->prefix}organisations_subscriptions AS os
                  WHERE 1 ";
    } else if(ct_is_group_admin_or_support($user_id)) {
        $query = $wpdb->prepare("SELECT DISTINCT( os.id ), os.nickname, os.organisation_id FROM {$wpdb->prefix}bp_groups_members AS bm, {$wpdb->prefix}organisations_subscriptions AS os
                WHERE 
                    os.user_id = bm.user_id AND bm.is_confirmed=1 
                    AND
                    (bm.user_id=%d OR bm.group_id 
                        IN 
                        ( SELECT group_id FROM {$wpdb->prefix}bp_groups_members WHERE user_id=%d AND (is_mod = 1 OR is_admin = 1)))
                ", $user_id, $user_id);
        
    } else {
        //Getting User Membership
        $organisation = ct_get_user_organisation();

        $query = $wpdb->prepare("SELECT os.nickname, os.id, os.organisation_id FROM {$wpdb->prefix}organisations_subscriptions AS os
                                 WHERE os.organisation_id=%d ", 
                                 $organisation->id);
    }
    
    if ($org_id !== null) {
        $query .= $wpdb->prepare(" AND os.organisation_id=%d", $org_id);
    }
    
    $query .= " ORDER BY os.nickname ";

    $data = $wpdb->get_results($query);

    return $data;
}

function ct_get_user_viewable_organisations($user_id = null)
{
    global $wpdb;
    
    if ($user_id == null)
        $user_id = get_current_user_id();
            
    $user_data = get_userdata($user_id);
    
    if (is_super_admin()) {       
        $query = "SELECT DISTINCT(o.id), o.organisation_name FROM {$wpdb->prefix}users_subscriptions AS us
                                 LEFT JOIN {$wpdb->prefix}organisations AS o ON o.id = us.organisation_id
                                 ORDER BY o.organisation_name
                                 ";
        $data = $wpdb->get_results($query);
        
    } else if(ct_is_group_admin_or_support($user_id)) {
        $query = $wpdb->prepare("SELECT DISTINCT(o.id), o.organisation_name FROM {$wpdb->prefix}bp_groups_members AS bm, {$wpdb->prefix}users_subscriptions AS s
                LEFT JOIN {$wpdb->prefix}organisations AS o ON o.id = s.organisation_id
                WHERE 
                    s.user_id = bm.user_id AND bm.is_confirmed=1 
                    AND
                    (bm.user_id=%d OR bm.group_id 
                        IN 
                        ( SELECT group_id FROM {$wpdb->prefix}bp_groups_members WHERE user_id=%d AND (is_mod = 1 OR is_admin = 1)))
                ORDER BY o.organisation_name
                ", $user_id, $user_id);
        $data = $wpdb->get_results($query);
        
    }
    
    return $data;
}

function ct_calculate_first_month_quantity($quantity)
{
    $remainedDay = (strtotime(date("Y-m-d", mktime(0, 0, 0, date('n') + 1, 1, date("Y")))) - strtotime(date("Y-m-d"))) / 86400;
    $totalDay = date("t");
    
    return $quantity * ($remainedDay / $totalDay);
}

function ct_get_payment_method_by_id($id)
{
    global $wpdb;
    
    $query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}organisations_payment_methods WHERE id=%d", $id);
    $row = $wpdb->get_row($query);
    
    return $row;
}

function ct_get_user_subscription_by_id($subscription_id)
{
    global $wpdb;
    
    $query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}users_subscriptions WHERE id=%d", $subscription_id);
    $data = $wpdb->get_row($query);
    
    return $data;
}

function ct_generate_organisation_key()
{
    global $wpdb;
    
    do{            
        $str = md5(cp_generate_password() . time());    
        
        $query = $wpdb->prepare("SELECT count(*) FROM "  . $wpdb->prefix . "organisations WHERE id=%d", $str);
        $id = $wpdb->get_var($query);
        if(!$id)
            break;
    }while(1);    
    
    return $str;
}

function ct_get_user_organisation_membership($user_id)
{
    global $wpdb;
    
    $query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}organisations_members WHERE user_id=%d", $user_id);
    $row = $wpdb->get_row($query);
    
    return $row;    
}

function ct_get_organisation_by_key($org_key)
{
    global $wpdb;
    
    $query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}organisations WHERE organisation_key=%s", $org_key);
    $row = $wpdb->get_row($query);
    
    return $row;    
}

function ct_get_organisation_by_id($id)
{
    global $wpdb;
    
    $query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}organisations WHERE id=%s", $id);
    $row = $wpdb->get_row($query);
    
    return $row;    
}

function ct_get_user_privileges($user_id, $organisation_id)
{
    global $wpdb;
    
    $query = $wpdb->prepare("SELECT p.* FROM {$wpdb->prefix}users_privileges AS up LEFT JOIN {$wpdb->prefix}privileges AS p ON p.id=up.privilege_id WHERE up.user_id=%d AND up.organisation_id=%d ORDER BY p.`order`", $user_id, $organisation_id);
    
    $rows = $wpdb->get_results($query);
    
    return $rows;
}

function ct_get_privileges()
{
    global $wpdb;
    
    $query = "SELECT * FROM {$wpdb->prefix}privileges ORDER BY `order`";
    $rows = $wpdb->get_results($query);
    
    return $rows;
}

function ct_get_privilege_by_code($code, $field)
{
    global $wpdb;
    
    $query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}privileges WHERE code=%s", $code);
    $row = $wpdb->get_row($query);
    
    if (!$row)    
        $value = null;
    else
        $value = $row->$field;
    
    return $value;
}


function ct_check_user_privilege($user_id, $organisation_id, $privilege)
{
    global $wpdb;
    
    $privilege_id = ct_get_privilege_by_code($privilege, 'id');
    
    $query = $wpdb->prepare("SELECT id FROM {$wpdb->prefix}users_privileges WHERE user_id=%d AND organisation_id=%d AND privilege_id=%d", $user_id, $organisation_id, $privilege_id);
    
    $id = $wpdb->get_var($query);
    
    return $id ? true : false;
    
}

