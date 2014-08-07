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
    
    $uHtml = '';
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
    
    $id = $_POST['id'];
    
    //Delete Users Subcription
    $orgController = new CT_Organisation_Controller();
    $orgController->delete_organisation_subscription($id);
    
    addMessage("Organsation subscription was deleted");
    wp_redirect(admin_url() . 'admin.php?page=organisation-subscriptions');
    exit;
}

