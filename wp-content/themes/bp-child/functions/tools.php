<?php
/**
* Site Custom Tools
*/

add_action("admin_menu", "ct_custom_tools_menu");

function ct_custom_tools_menu()
{
    add_management_page("Copy Data From Destination Site", "Copy Data", "manage_options", "duplicate_data", "ct_duplicate_data");
}

function ct_duplicate_data()
{
    global $wpdb;
    
    ?>
    <div class="wrap">
        <?php
            if(isset($_REQUEST['action']) && wp_verify_nonce($_REQUEST['action'], 'duplicate-data'))
            {
               $new_wpdb = new wpdb($_REQUEST['username'], $_REQUEST['password'], $_REQUEST['database'], $_REQUEST['host']);
               if(!$new_wpdb->dbh)
               {
                   echo "Database connection error";
               }else{
                   //Duplicate
                   
                   //1. Communities
                   $query = "SELECT * FROM " . $wpdb->prefix . "bp_groups";
                   $communities = $new_wpdb->get_results($query, ARRAY_A);
                   
                   //2. Test Suites, Test Case and Products
                   $query = "SELECT * FROM " . $wpdb->posts . " WHERE post_type  = 'test-suite' ORDER BY post_title";
                   $suites = $new_wpdb->get_results($query, ARRAY_A);
                   
                   $query = "SELECT * FROM " . $wpdb->posts . " WHERE post_type = 'test-case' ORDER BY post_title";
                   $cases = $new_wpdb->get_results($query, ARRAY_A);
                   
                   $query = "SELECT * FROM " . $wpdb->posts . " WHERE post_type = 'product-service' ORDER BY post_title";
                   $products = $new_wpdb->get_results($query, ARRAY_A);
                   
                   ?>
                    <h2>Select data copied from the destination site</h2>
                    <form action="" method="post" id="copyDataForm">
                        <input type="hidden" name="page" value="duplicate_data" />
                        <input type="hidden" name="action" value="<?php echo wp_create_nonce('complete-duplicate-data')?>" />
                        <div class="articles">
                            <h3 style="float: left; margin-top: 0; margin-right: 10px;">Select Communities</h3> <a href="#" class="check-all">Select All</a>
                            <br clear="all" />
                            <?php foreach($communities as $c): ?>
                            <label><input type="checkbox" name="community_ids[]" value="<?php echo $c['id']?>" /> (ID: #<?php echo $c['id']?>) <?php echo $c['name']?></label><br />
                            <?php endforeach; ?>
                        </div>
                        <br />
                        <div class="articles">
                            <h3 style="float: left; margin-top: 0; margin-right: 10px;">Select Test Suites</h3> <a href="#" class="check-all">Select All</a>
                            <br clear="all" />
                            <?php foreach($suites as $c): ?>
                            <label><input type="checkbox" name="suite_ids[]" value="<?php echo $c['ID']?>" /> (ID: #<?php echo $c['ID']?>) <?php echo $c['post_title']?></label><br />
                            <?php endforeach; ?>
                        </div>
                        <br />
                        <div class="articles">
                            <h3 style="float: left; margin-top: 0; margin-right: 10px;">Select Test Cases</h3><a href="#" class="check-all">Select All</a>
                            <br clear="all" />
                            <?php foreach($cases as $c): ?>
                            <label><input type="checkbox" name="case_ids[]" value="<?php echo $c['ID']?>" /> (ID: #<?php echo $c['ID']?>) <?php echo $c['post_title']?></label><br />
                            <?php endforeach; ?>
                        </div>
                        <br />
                        <div class="articles">
                            <h3 style="float: left; margin-top: 0; margin-right: 10px;">Select Products and Services</h3><a href="#" class="check-all">Select All</a>
                            <br clear="all" />
                            <?php foreach($products as $c): ?>
                            <label><input type="checkbox" name="product_ids[]" value="<?php echo $c['ID']?>" /> (ID: #<?php echo $c['ID']?>) <?php echo $c['post_title']?></label><br />
                            <?php endforeach; ?>
                        </div>
                        <p><input type="submit" class="button button-primary" value="Continue" /></p>
                        <input type="hidden" name="host" value="<?php echo $_POST['host']?>" />
                        <input type="hidden" name="database" value="<?php echo $_POST['database']?>" />
                        <input type="hidden" name="username" value="<?php echo $_POST['username']?>" />
                        <input type="hidden" name="password" value="<?php echo $_POST['password']?>" />
                    </form>
                    <script type="text/javascript">
                        jQuery(document).ready(function($){
                            $('#copyDataForm .check-all').click(function(){
                                $(this).parent().find('input[type="checkbox"]').prop('checked', true);
                                return false;
                            })
                        })
                    </script>
                   <?php
                   
               }
            }else if(isset($_REQUEST['action']) && wp_verify_nonce($_REQUEST['action'], 'complete-duplicate-data')){
                $new_wpdb = new wpdb($_REQUEST['username'], $_REQUEST['password'], $_REQUEST['database'], $_REQUEST['host']);
                if(!$new_wpdb->dbh)
                {
                    echo "Database connection error";
                }else{
                    ?>
                    <h2>Copy Data From the destination site</h2>
                    <?php
                    $communityIDs = $_POST['community_ids'];
                    $suiteIDs = $_POST['suite_ids'];
                    $caseIDs = $_POST['case_ids'];
                    $productIDs = $_POST['product_ids'];
                    
                    if($communityIDs)
                    {
                        //Copy Groups
                        $query = "SELECT * FROM " . $wpdb->prefix . "bp_groups WHERE id in (" . implode(", ", $communityIDs) . ")";
                        echo '<h3>Copying Communities</h3>';
                        $rows = $new_wpdb->get_results($query, ARRAY_A);
                        foreach($rows as $row)
                        {
                            //Remove Old Data
    //                        $new_wpdb->query("DELETE FROM " . $wpdb->prefix . "bp_groups WHERE id=" . $row['id']);
                            
                            $data = $row;
                            $data['id'] = null;
                            unset($data['id']);
                            
                            //Insert New Data
                            $wpdb->insert($wpdb->prefix . "bp_groups", $data);
                            $new_community_id = $wpdb->insert_id;
                           
                            //Duplicate Group MetaData
                            $metadata = $new_wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "bp_groups_groupmeta WHERE group_id=" . $row['id'], ARRAY_A);
                            //Remove Old Metadata
    //                        $new_wpdb->query("DELETE FROM " . $wpdb->prefix . "bp_groups_groupmeta WHERE group_id=" . $row['id']);
                            foreach($metadata as $mrow)
                            {
                                $mrow['group_id'] = $new_community_id;
                                //Insert New Metadata
                                $wpdb->insert($wpdb->prefix . "bp_groups_groupmeta", $mrow);
                            }
                            echo "<b>" . $row['name'] . "</b> Copied.";
                        }
                    }
                    //2. Duplicate Test Suites, Test Case and Products
                    /*$query = "SELECT * FROM " . $wpdb->posts . "WHERE post_type IN ('test-suite', 'test-case', 'product-service')";
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
                       
                    }*/
                    exit;
                }
                
            }else{
                ?>
                <h2>Copy Data From the destination site</h2>
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
                            <td><b>Database</b></td>
                            <td><input type="text" name="database" value="" autocomplete="off" /></td>
                        </tr>                                                        
                        <tr>
                            <td><b>Username</b></td>
                            <td><input type="text" name="username" value="" autocomplete="off" /></td>
                        </tr>                
                        <tr>
                            <td><b>Password</b></td>
                            <td><input type="text" name="password" value="" autocomplete="off" /></td>
                        </tr>                
                        <tr>
                            <td colspan="2"><input type="submit" class="button button-primary" value="Start" /></td>
                        </tr>                
                    </table>            
                </form>
                <?php
            }
        ?>        
    </div>
    <?php
}