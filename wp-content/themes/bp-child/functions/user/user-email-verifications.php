<?php
/**
* Manage the Email Verification per a users
*/

add_action("admin_menu", "ct_users_email_verification_menu");

function ct_users_email_verification_menu()
{
    add_users_page("Manage User Email Verifications", "Email Verifications", "manage_options", "user_email_verifications", "ct_manage_email_verifications");
}

function ct_manage_email_verifications()
{
    global $wpdb;
    
    require_once('user-verification-list-table.php');
    
    if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'verify')
    {
        if ( empty($_REQUEST['users']) )
            $userids = array( intval( $_REQUEST['user'] ) );
        else
            $userids = array_map( 'intval', (array) $_REQUEST['users'] );
        
        foreach ($userids as $userid)
        {
            $wpdb->update($wpdb->prefix . 'users', array('user_activation_key'=>'', 'user_status'=>0), array('ID'=>$userid));
            $user_temp = $wpdb->get_row('SELECT * FROM ' . $wpdb->prefix . 'users_changes WHERE user_id=' . $userid);
            
            if ($user_temp) {
                $wpdb->update($wpdb->prefix . 'users', array('user_email'=>$user_temp->email_changed), array('ID'=>$userid));
                $wpdb->query("DELETE FROM " . $wpdb->prefix . "users_changes WHERE user_id =" . $userid);    
            }
        }
        
        $msg = 'Successfully Verified!';
    }
        
    $listTable = new CT_User_Verification_List_Table();
    $listTable->prepare_items();
    ?>
    <div class="wrap">
        <h2>Users</h2>
        <?php if(isset($msg)){ ?>
        <div id="message" class="updated below-h2"><p><?php echo $msg?></p></div>
        <?php } ?>
        <form name="adminform" action="users.php?page=user_email_verifications" method="post">
        <?php
            $listTable->search_box("Search", "search");
            echo $listTable->display();
        ?>
        </form>
    </div>
<?php    
}
