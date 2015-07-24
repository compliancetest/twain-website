<?php

add_action('wp_ajax_get_organisation_info_on_admin', 'ct_get_organisation_detail_by_ajax');
function ct_get_organisation_detail_by_ajax()
{
    global $wpdb;
    
    if(!is_super_admin())    
    {
        die("Invalid Request");
    }
    
    $org_id = $_POST['id'];
    
    $orgClass = new CT_Organisation($org_id);
    
    $payment_methods = $orgClass->get_payment_methods();
    $users = $orgClass->get_organisation_members();
    
    $pHtml = '';
    foreach($payment_methods as $p){
        $pHtml .= '<option value="' . $p->id . '" ' .  (isset($data) && $data->payment_method == $p->id ? 'selected="selected"' : '') . '>' .  $p->nickname . '(' .  ($p->invoice_me == 0 ? chunk_split($p->card_number, 4) : 'Invoice') . ')</option>';
    }
    
    $uHtml = '<option value="0">-</option>';
    foreach($users as $u){
        $uHtml .= '<option value="' . $u->ID . '" ' . (isset($data) && $data->user_id == $u->ID ? 'selected="selected"' : '') . '>' . get_user_meta($u->ID, 'first_name', true) . " " . get_user_meta($u->ID, 'last_name', true) . ', ' . $u->user_email . '</option>';
    }
    
    header('Content-type: application/xml');
    echo '<result>';
    echo '<methods><![CDATA[';
    echo $pHtml;
    echo ']]></methods>';
    echo '<users><![CDATA[';
    echo $uHtml;
    echo ']]></users>';    
    echo '</result>';
    
    exit;
}

function ct_save_organisation_subscription_on_admin()
{
    global $wpdb;
    
    $data = array(
        'nickname' => $_POST['nickname'],
        'organisation_id' => $_POST['organisation_id'],
        'purchased_date' => $_POST['purchased_date'],
        'status' => $_POST['status'],
        'suite_family_mark' => $_POST['suite_family_mark'],
        'user_id' => $_POST['user_id'],
        'payment_method' => $_POST['payment_method'],
        'purchaser_id' => 0
    );
    
    $id = $_POST['id'];
    if (!$id) {
        $wpdb->insert($wpdb->prefix . "organisations_subscriptions", $data, array("%s", "%d", "%s", '%s', '%d', '%d', '%d', '%d'));
    } else {
        $wpdb->update($wpdb->prefix . "organisations_subscriptions", $data, array('id' => $id), array("%s", "%d", "%s", '%s', '%d', '%d', '%d', '%d'), array('%d'));
    }
    
    addMessage('Subscription saved successfully.');
    wp_redirect(admin_url() . 'admin.php?page=organisation-subscriptions');
    exit;
}

function ct_delete_organisation_subscription_on_admin()
{
    global $wpdb;
    
    $id = $_GET['id'];
    
    //Delete Users Subcription
    /*$orgController = new CT_Organisation_Controller();
    $orgController->delete_organisation_subscription($id);*/
    //Remove harness detail
    $wpdb->delete($wpdb->prefix . "users_subscriptions", array('parent_id' => $id), array('%d'));
    
    //Delete the organisation subscription
    $wpdb->delete($wpdb->prefix . "organisations_subscriptions", array('id' => $id), array('%d'));
    
    addMessage("Organsation subscription was deleted");
    wp_redirect(admin_url() . 'admin.php?page=organisation-subscriptions');
    exit;
}

add_action('wp_ajax_get_organisation_subscription_info_on_admin', 'ct_get_organisation_subscription_info_by_ajax');
function ct_get_organisation_subscription_info_by_ajax()
{
    global $wpdb;
    
    if(!is_super_admin())    
    {
        die("Invalid Request");
    }
    
    $org_id = $_POST['id'];
    
    $orgClass = new CT_Organisation($org_id);
    
    $subscriptions = $orgClass->get_subscriptions();
    $users = $orgClass->get_organisation_members();
    
    $query = "SELECT p.ID, p.post_title 
              FROM
                (
                SELECT * FROM
                  wp_test_suites 
                ORDER BY suite_title,
                  version_major,
                  version_minor DESC,
                  version_patch DESC 
                )  AS t
              LEFT JOIN {$wpdb->posts} AS p ON t.suite_id=p.ID 
              WHERE t.family_mark IN (
                SELECT suite_family_mark FROM {$wpdb->prefix}organisations_subscriptions WHERE organisation_id=$org_id
              )
              GROUP BY family_mark,
                  version_major 
              ORDER BY p.post_title";
    $test_suites = $wpdb->get_results($query);
    
    $sHtml = '';
    foreach($subscriptions as $s){
        $sHtml .= '<option value="' . $s->id . '" >' . $s->nickname . '</option>';
    }
    
    $pHtml = '';
    foreach($test_suites as $p){
        $pHtml .= '<option value="' . $p->ID . '" >' . $p->post_title . '</option>';
    }
    
    $uHtml = '';
    foreach($users as $u){
        $uHtml .= '<option value="' . $u->ID . '" ' . (isset($data) && $data->user_id == $u->ID ? 'selected="selected"' : '') . '>' . get_user_meta($u->ID, 'first_name', true) . " " . get_user_meta($u->ID, 'last_name', true) . ', ' . $u->user_email . '</option>';
    }
    
    header('Content-type: application/xml');
    echo '<result>';
    echo '<subscriptions><![CDATA[';
    echo $sHtml;
    echo ']]></subscriptions>';
    echo '<suites><![CDATA[';
    echo $pHtml;
    echo ']]></suites>';
    echo '<users><![CDATA[';
    echo $uHtml;
    echo ']]></users>';    
    echo '</result>';
    
    exit;
}

function ct_save_user_subscription_on_admin()
{
    global $wpdb;
    
    $subscription = ct_get_organisation_subscription_by_id($_POST['organisation_subscription_id']);
    
    $wpdb->update($wpdb->prefix . "organisations_subscriptions", array('user_id' => $_POST['user_id']), array('id' => $subscription->id), array('%d'), array('%d'));
    
    $controller = new CT_Organisation_Controller();
    
    if (!$_POST['id']) {
        $controller->create_user_harness_detail($_POST['user_id'], $_POST['suite_id'], $subscription->organisation_id, $subscription->id);    
    } else {
        $wpdb->update($wpdb->prefix . "users_subscriptions", array('user_id' => $_POST['user_id'], 'parent_id' => $subscription->id, 'suite_id' => $_POST['suite_id']), array('id' => $_POST['id']), array('%d'), array('%d'));
    }
    
    addMessage('Subscription saved successfully.');
    wp_redirect(admin_url() . 'admin.php?page=user-subscriptions');
    exit;
}

function ct_delete_user_subscription_on_admin()
{
    global $wpdb;
    
    $id = $_GET['id'];
    
    $data = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "users_subscriptions WHERE id=" . $id);
    
    $wpdb->delete($wpdb->prefix . "users_subscriptions", array('id' => $id), array('%d'));
    
    //Delete the organisation subscription
    $wpdb->update($wpdb->prefix . "organisations_subscriptions", array('user_id' => 0), array('id' => $data->parent_id), array('%d'), array('%d'));
    
    addMessage("User subscription was deleted");
    wp_redirect(admin_url() . 'admin.php?page=user-subscriptions');
    exit;
}

