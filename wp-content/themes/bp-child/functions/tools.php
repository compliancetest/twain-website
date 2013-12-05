<?php
/**
* Site Custom Tools
*/

add_action("admin_menu", "ct_custom_tools_menu");

function ct_custom_tools_menu()
{
    add_management_page("Duplicate Data to Test Site", "Duplicate Data", "manage_options", "duplicate_data", "ct_duplicate_data");
}

function ct_duplicate_data()
{
    global $wpdb;
    
    if(isset($_REQUEST['action']) && wp_verify_nonce($_REQUEST['action'], 'duplicate-data'))
    {
       $new_wpdb = new wpdb($_REQUEST['username'], $_REQUEST['password'], $_REQUEST['database'], $_REQUEST['host']);
       if(!$new_wpdb->dbh)
       {
           echo "Database connection error";
       }else{
           //Duplicate
           
           //1. Duplicate Communities
           $query = "SELECT * FROM " . $wpdb->prefix . "bp_groups";
           $rows = $wpdb->get_results($query, ARRAY_A);
           foreach($rows as $row)
           {
               //Remove Old Data
               $new_wpdb->query("DELETE FROM " . $wpdb->prefix . "bp_groups WHERE id=" . $row['id']);
               //Insert New Data
               $new_wpdb->insert($wpdb->prefix . "bp_groups", $row);
               
               //Duplicate Group MetaData
               $metadata = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "groups_groupmeta WHERE group_id=" . $row->id, ARRAY_A);
               //Remove Old Metadata
               $new_wpdb->query("DELETE FROM " . $wpdb->prefix . "bp_groups_groupmeta WHERE group_id=" . $row['id']);
               foreach($metadata as $mrow)
               {
                   //Insert New Metadata
                   $new_wpdb->insert($wpdb->prefix . "bp_groups_groupmeta", $mrow);
               }
           }
           
           //2. Duplicate Test Suites, Test Case and Products
           $query = "SELECT * FROM " . $wpdb->posts . "WHERE post_type IN ('test-suite', 'test-case', 'product-service')";
           $rows = $wpdb->get_results($query, ARRAY_A);
           foreach($rows as $post)
           {
               //Remove Old Posts
               $oID = $new_wpdb->get_var("SELECT ID FROM " . $wpdb->posts . " WHERE post_name='" . $post['post_name'] . "'");
               if($oID)
               {
                    $new_wpdb->delete($wpdb->posts, array('ID' => $oID));
                    $new_wpdb->delete($wpdb->postmeta, array('post_id' => $oID));
               }
               //Insert New Data
               
           }
           
       }
    }
    ?>
    <div class="wrap">
        <h2>Duplicate Data to the destination site</h2>
        <form action="" method="post">
            <input type="hidden" name="page" value="duplicate_data" />
            <input type="hidden" name="action" value="<?php echo wp_create_nonce('duplicate-data')?>" />
            <h3>Destination Database</h3>
            <table cellpadding="5">
                <tr>
                    <td><b>Host</b></td>
                    <td><input type="text" name="host" value="" autocomplete="off" /></td>
                </tr>                
                <tr>
                    <td><b>Username</b></td>
                    <td><input type="text" name="username" value="" autocomplete="off" /></td>
                </tr>                
                <tr>
                    <td><b>Database</b></td>
                    <td><input type="text" name="database" value="" autocomplete="off" /></td>
                </tr>                                
                <tr>
                    <td><b>Password</b></td>
                    <td><input type="text" name="password" value="" autocomplete="off" /></td>
                </tr>                
                <tr>
                    <td colspan="2"><input type="submit" class="button button-primary" value="Duplicate" /></td>
                </tr>
            </table>            
        </form>
    </div>
    <?php
}