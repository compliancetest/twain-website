<?php
/**
* Site Custom Tools
*/

add_action("admin_menu", "ct_custom_tools_menu");

function ct_custom_tools_menu()
{
    add_management_page("Copy Data From Source Site", "Copy Data", "manage_options", "duplicate_data", "ct_duplicate_data");
}

function ct_duplicate_data()
{
    global $wpdb;
    
    $error_message = '';
    
    $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : null;
    
    
    ?>
    <div class="wrap">
        <h2>Copy Data From the source site</h2>
        <?php
            if(wp_verify_nonce($_REQUEST['action'], 'start-copy-data') || wp_verify_nonce($_REQUEST['action'], 'complete-duplicate-data'))
            {
               $new_wpdb = new wpdb($_REQUEST['username'], $_REQUEST['password'], $_REQUEST['database'], $_REQUEST['host']);
               if(!$new_wpdb->dbh)
               {
                   ?>
                   <div id="message" class="error below-h2"><p>Database Connection Error</p></div>
                   <form action="" method="post">
                        <input type="hidden" name="page" value="duplicate_data" />
                        <input type="hidden" name="action" value="<?php echo wp_create_nonce('start-copy-data')?>" />
                        <h3>Source Database</h3>
                        <table cellpadding="5">
                            <tr>
                                <td><b>Host</b></td>
                                <td><input type="text" name="host" value="<?php echo $_POST['host']?>" autocomplete="off" /></td>
                            </tr>                
                            <tr>
                                <td><b>Database</b></td>
                                <td><input type="text" name="database" value="<?php echo $_POST['database']?>" autocomplete="off" /></td>
                            </tr>                                                        
                            <tr>
                                <td><b>Username</b></td>
                                <td><input type="text" name="username" value="<?php echo $_POST['username']?>" autocomplete="off" /></td>
                            </tr>                
                            <tr>
                                <td><b>Password</b></td>
                                <td><input type="text" name="password" value="<?php echo $_POST['password']?>" autocomplete="off" /></td>
                            </tr>                
                            <tr>
                                <td colspan="2"><input type="submit" class="button button-primary" value="Start" /></td>
                            </tr>                
                        </table>            
                    </form>
                   <?php
               }else{
                   if(wp_verify_nonce($action, 'start-copy-data'))                   
                   {
                      
                       //Getting Data from the Source Site
                       
                       $query = "SELECT * FROM " . $wpdb->prefix . "bp_groups";
                       $destCommunities = $wpdb->get_results($query, ARRAY_A);
                       
                       $query = "SELECT p.*, pm.meta_value AS community_id FROM " . $wpdb->posts . " AS p LEFT JOIN " . $wpdb->postmeta . " AS pm ON pm.post_id=p.ID AND pm.meta_key='community_id' WHERE post_type  = 'test-suite' ORDER BY community_id, post_title";
                       $results = $wpdb->get_results($query, ARRAY_A);
                       
                       $destSuites = array();
                       foreach($results as $r) {
                           if (!isset($r['community_id']))    
                               $destSuites[$r['community_id']] = array();
                           $destSuites[$r['community_id']][] = $r;
                       }
                       
                       //1. Communities
                       $query = "SELECT * FROM " . $wpdb->prefix . "bp_groups";
                       $sourceCommunities = $new_wpdb->get_results($query, ARRAY_A);
                       
                       //2. Test Suites, Test Case and Products
                       $query = "SELECT p.*, pm.meta_value AS community_id FROM " . $wpdb->posts . " AS p LEFT JOIN " . $wpdb->postmeta . " AS pm ON pm.post_id=p.ID AND pm.meta_key='community_id' WHERE post_type  = 'test-suite' ORDER BY community_id, post_title";
                       $resuls = $new_wpdb->get_results($query, ARRAY_A);
                       $sourceSuites = array();
                       foreach($results as $r) {
                           if (!isset($r['community_id']))    
                               $sourceSuites[$r['community_id']] = array();
                           $sourceSuites[$r['community_id']][] = $r;
                       }
                       
                       /*$query = "SELECT * FROM " . $wpdb->posts . " WHERE post_type = 'test-case' ORDER BY post_title";
                       $cases = $new_wpdb->get_results($query, ARRAY_A);*/
                       
                       $query = "SELECT * FROM " . $wpdb->posts . " WHERE post_type = 'product-service' ORDER BY post_title";
                       $products = $new_wpdb->get_results($query, ARRAY_A);
                       ?>
                       <style type="text/css">
                            .community-tr > td{
                                border-bottom: solid 1px #ccc;
                                border-top: solid 1px #ccc;
                                vertical-align: middle;
                            }
                            .community-suite-tr > td{
                                position: relative;
                            }
                            .community-suite-tr .wrap{
                                position: absolute;
                                width: 100%;
                                height: 100%;
                                background: rgba(255, 255, 255, 0.4);
                                left: 0;
                                top: 0;
                            }
                       </style>
                        <br />
                        <h2>Select data copied from the source site</h2>
                        <form action="" method="post" id="copyDataForm">
                            <input type="hidden" name="page" value="duplicate_data" />
                            <input type="hidden" name="action" value="<?php echo wp_create_nonce('complete-duplicate-data')?>" />
                            <div class="articles">
                                <h4 style="float: left; margin-top: 0; margin-bottom: 5px; margin-right: 10px;">Select Communities</h4> <!--<a href="#" class="check-all">Select All</a>-->
                                <div style="clear:both; font-weight: bold;">(The test suites, test cases and test data that belong to the community will be copied together.)</div>
                                <table class="widefat" style="width: auto; border-collapse: collapse;">
                                    <thead>
                                        <tr>
                                            <th>Community</th>
                                            <th>Copy / Update</th>
                                            <th>Updated Community</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($sourceCommunities as $c): ?>
                                        <tr class="community-tr" id="community-tr<?php echo $c['id']?>" data-id='<?php echo $c['id']?>'>
                                            <td>
                                                <label><input type="checkbox" name="community_ids[]" value="<?php echo $c['id']?>" /> (ID: #<?php echo $c['id']?>) <?php echo $c['name']?></label>
                                            </td>
                                            <td>
                                                <label><input type="radio" name="copy_method<?php echo $c['id']?>" value="copy" checked="checked" class="copy_method" /> Copy</label>
                                                <label><input type="radio" name="copy_method<?php echo $c['id']?>" value="update" class="copy_method" /> Update</label>
                                            </td>
                                            <td>
                                                <select name="updated_community<?php echo $c['id']?>" class="updated_community" disabled="disabled">
                                                    <option>Select Community</option>
                                                    <?php foreach($destCommunities as $sc): ?>
                                                    <option value="<?php echo $sc['id']?>"><?php echo $sc['name']?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </td>
                                        </tr>   
                                        <?php /*
                                        <tr class='community-suite-tr' id="community-suite-tr<?php echo $c['id']?>" style="display: none;">
                                            <td colspan="3" style="padding-left: 20px">
                                                <h4 style="margin: 0;">Test Suites</h4>
                                                <table>
                                                    <?php foreach($sourceSuites[$c['id']] as $s): ?>                                
                                                    <tr>
                                                        <td>
                                                            <label><input type="checkbox" name="suite_ids[]" value="<?php echo $s['ID']?>" /> (ID: #<?php echo $s['ID']?>) <?php echo $s['post_title']?></label>
                                                        </td>
                                                        <td>
                                                            <label><input type="radio" name="suite_copy_method<?php echo $s['ID']?>" value="copy" checked="checked" class="suite_copy_method" /> Copy</label>
                                                            
                                                            <label><input type="radio" name="suite_copy_method<?php echo $s['ID']?>" value="update" class="suite_copy_method" /> Update</label>
                                                        </td>
                                                        <td>
                                                            <select name="updated_suite<?php echo $s['ID']?>" class="updated_suite" disabled="disabled">
                                                                <option value="">Select Suite</option>
                                                                <?php foreach($destSuites[$c['id']] as $sc): ?>
                                                                <option value="<?php echo $sc['ID']?>"><?php echo $sc['post_title']?></option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </td>
                                                    </tr> 
                                                    <?php endforeach; ?>
                                                </table>
                                                <div class="wrap"></div>
                                            </td>
                                        </tr>
                                        */
                                        ?>                                     
                                        <?php endforeach; ?>        
                                    </tbody>
                                </table>
                                
                            </div>
                            <br />
                            
                            
                            <div class="articles">
                                <h4 style="float: left; margin-top: 0; margin-bottom: 5px; margin-right: 10px;">Select Products and Services</h4><a href="#" class="check-all">Select All</a>
                                <table class="widefat" style="width: auto;">
                                    <thead>
                                        <tr>
                                            <th>Products and Services</th>                                                                                        
                                        </tr>
                                    </thead>
                                    <tbody>                                        
                                    <?php foreach($products as $c): ?>
                                    <tr>
                                        <td>
                                        <label><input type="checkbox" name="product_ids[]" value="<?php echo $c['ID']?>" /> (ID: #<?php echo $c['ID']?>) <?php echo $c['post_title']?></label>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
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
                                    if($(this).html() == 'Select All')
                                    {
                                        $(this).html('Deselect All');
                                        $(this).parent().find('input[type="checkbox"]').prop('checked', true);
                                    }else{
                                        $(this).html('Select All');
                                        $(this).parent().find('input[type="checkbox"]').prop('checked', false);
                                    }
                                    return false;
                                });
                               /* $('.community-tr input[type="checkbox"]').change(function(){
                                    if (this.checked) {
                                        showCommunitySelect(this.value);
                                    } else {
                                        hideCommunitySelect(this.value);
                                    }
                                })
                                
                                function showCommunitySelect(cid)
                                {
                                    $('#community-suite-tr' + cid).show();
                                    $('#community-suite-tr' + cid + ' input[type="checkbox"]').prop('checked', true);
                                }
                                function hideCommunitySelect(cid)
                                {
                                    $('#community-suite-tr' + cid).hide();
                                    $('#community-suite-tr' + cid + ' input[type="checkbox"]').prop('checked', false);
                                }*/
                                
                                $('input.copy_method').click(function(){                                    
                                    if($(this).val() == 'copy')
                                    {
                                        $(this).parent().parent().parent().find('select.updated_community').prop('disabled', true);
                                        /*$(this).parents('tr').next().find('.wrap').show();
                                        
                                        $(this).parents('tr').next().find('input[type="checkbox"]').prop('checked', true);
                                        $(this).parents('tr').next().find('input.suite_copy_method[value="copy"]').prop('checked', true);
                                        $(this).parents('tr').next().find('select.updated_suite').val('').prop('disabled', true);*/
                                        
                                    }else{
                                        
                                        $(this).parent().parent().parent().find('select.updated_community').prop('disabled', false);
//                                        $(this).parents('tr').next().find('.wrap').hide();
                                    }
                                });
                                /*$('input.suite_copy_method').click(function(){                                    
                                    if($(this).val() == 'copy')
                                    {
                                        $(this).parent().parent().parent().find('select.updated_suite').val('').prop('disabled', true);
                                    }else{
                                        $(this).parent().parent().parent().find('select.updated_suite').prop('disabled', false);
                                    }
                                });*/
                                //Toggle Community Selection
                            })
                        </script>
                       <?php
                   }else if(wp_verify_nonce($action, 'complete-duplicate-data')){
                       
                        
                    ?>
                            <h3>Start Copy Data</h3>
                            <?php
                            $communityIDs = isset($_POST['community_ids']) ? $_POST['community_ids'] : null;
                            $suiteIDs = isset($_POST['suite_ids']) ? $_POST['suite_ids'] : null;
                            $caseIDs = isset($_POST['case_ids']) ? $_POST['case_ids'] : null;
                            $productIDs = isset($_POST['product_ids']) ? $_POST['product_ids']: null;
                            
                            //Copy Products and Services
                            if($productIDs)
                            {
                                echo '<h4>Copy Products and Services</h4>';
                                $query = "SELECT * FROM {$wpdb->posts} WHERE ID in (" . implode(', ', $productIDs) . ")";
                                $rows = $new_wpdb->get_results($query, ARRAY_A);
                                
                                foreach($rows as $row)
                                {
                                    $oldID = $row['ID'];
                                    
                                    $row['ID'] = null;
                                    unset($row['ID']);
                                    if($newId = wp_insert_post($row))
                                    {
                                        //Getting Post Meta
                                        $query = "SELECT * FROM {$wpdb->postmeta} WHERE post_id=" . $oldID;
                                        $metas = $new_wpdb->get_results($query);
                                        foreach($metas as $mrow)
                                            $wpdb->insert($wpdb->postmeta, array('post_id' => $newId, 'meta_key' => $mrow->meta_key, 'meta_value' => $mrow->meta_value));
                                    }
                                    echo '<b>' . $row['post_title'] . ' has been copied.</b><br />';
                                }
                                echo "<hr />";
                            }
                            
                            if($communityIDs)
                            {
                                //Copy Groups
                                $query = "SELECT * FROM " . $wpdb->prefix . "bp_groups WHERE id in (" . implode(", ", $communityIDs) . ")";
                                echo '<h3>Copying Communities</h3>';
                                $communities = $new_wpdb->get_results($query, ARRAY_A);
                                
                                //Getting Side Admins
                                $admins = get_users(array('role' => 'administrator'));
                                
                                //Keep Suites Names AND Ids
                                $query = "SELECT family_mark, suite_title FROM wp_test_suites GROUP BY family_mark ORDER BY suite_title";
                                $old_suites_family_marks = $wpdb->get_results($query);
                                
                                //Getting Suite Titles and Ids
                                $query = "SELECT * FROM wp_test_suites ORDER BY suite_title, version_major, version_minor, version_patch";
                                $old_suites = $wpdb->get_results($query);
                                
                                //Getting Old Test Suite Configuration Table
                                $esb = new ManageESB();
                                $query = "SELECT * FROM " . $esb->table_test_suite_configuration . " ORDER BY TEST_SUITE_WP_ID";
                                $esb_suite_configurations = ManageESB::$esbdb->get_results($query);
                                
                                //Getting Old Test Case Configuration Table
                                $query = "SELECT * FROM " . $esb->table_test_case_configuration . " ORDER BY TEST_CASE_WP_ID";
                                $esb_case_configurations = ManageESB::$esbdb->get_results($query);

                                if(!$communities)
                                {
                                    //Remove Trash Posts
                                    $query = "SELECT ID FROM " . $wpdb->posts . " WHERE post_status='trash' AND (post_type='test-suite' OR post_type='test-case')";
                                    $trashes = $wpdb->get_col($query);
                                    foreach($trashes as $t)
                                    {
                                        wp_delete_post($t);
                                    }
                                } else {
                                    //Remove Pricing Plans
                                    $query = "DELETE FROM wp_pricing_plans";
                                    $wpdb->query($query);
                                    $query = "DELETE FROM wp_pricing_plans_attributes";
                                    $wpdb->query($query);
                                    
                                    $query = "SELECT * FROM wp_pricing_plans";
                                    $results = $new_wpdb->get_results($query, ARRAY_A);
                                    foreach( $results as $row) {
                                        $wpdb->insert('wp_pricing_plans', $row);
                                    }
                                    
                                    $query = "SELECT * FROM wp_pricing_plans_attributes";
                                    $results = $new_wpdb->get_results($query, ARRAY_A);
                                    foreach( $results as $row) {
                                        $wpdb->insert('wp_pricing_plans_attributes', $row);
                                    }
                                }
                                
                                foreach($communities as $community)
                                {
                                    $data = $community;
                                    $data['id'] = null;
                                    unset($data['id']);
                                    
                                    //Meta Data
                                    $metadata = $new_wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "bp_groups_groupmeta WHERE group_id=" . $community['id'], ARRAY_A);
                                    
                                    if($_POST['copy_method' . $community['id']] == 'update' && $_POST['updated_community' . $community['id']])
                                    {
                                        $wpdb->update($wpdb->prefix . "bp_groups", $data, array('id' => $_POST['updated_community' . $community['id']]));
                                        $new_community_id = $_POST['updated_community' . $community['id']];
                                        
                                        $wpdb->delete($wpdb->prefix . "bp_groups_groupmeta", array('group_id' => $new_community_id, 'meta_key' => 'terms_and_conditions'));
                                        $wpdb->delete($wpdb->prefix . "bp_groups_groupmeta", array('group_id' => $new_community_id, 'meta_key' => 'license_agreements'));
                                        $wpdb->delete($wpdb->prefix . "bp_groups_groupmeta", array('group_id' => $new_community_id, 'meta_key' => 'obligation_for_claim'));                                
                                        $wpdb->delete($wpdb->prefix . "bp_groups_groupmeta", array('group_id' => $new_community_id, 'meta_key' => 'notification_email_of_changes'));                                                         foreach($metadata as $mrow)
                                        {
                                            if($mrow['meta_key'] == 'terms_and_conditions' || $mrow['meta_key'] == 'license_agreements' || $mrow['meta_key'] == 'obligation_for_claim' || $mrow['meta_key'] == 'notification_email_of_changes')
                                            {
                                                $mrow['id'] = null;
                                                unset($mrow['id']);
                                                $mrow['group_id'] = $new_community_id;                                
                                                $wpdb->insert($wpdb->prefix . "bp_groups_groupmeta", $mrow);
                                            }
                                        }
                                        
                                        //Remove Test Suites and Test Cases
                                        $query = "SELECT DISTINCT(post_id) FROM {$wpdb->postmeta} WHERE meta_key='community_id' AND meta_value=" . $new_community_id;
                                        $tSuiteIDs = $wpdb->get_col($query);
                                        foreach($tSuiteIDs as $sId)
                                        {
                                            wp_delete_post($sId);
                                            //Getting Test Cases
                                            $query = "SELECT DISTINCT(post_id) FROM {$wpdb->postmeta} WHERE meta_key='test_suite' AND meta_value=" . $sId;    
                                            $tCaseIDs = $wpdb->get_col($query);
                                            
                                            foreach($tCaseIDs as $cId)
                                            {
                                                wp_delete_post($cId);
                                            }
                                        }                                        
                                    }else{
                                        //Add New Community
                                        //Insert New Data
                                        $wpdb->insert($wpdb->prefix . "bp_groups", $data);
                                        $new_community_id = $wpdb->insert_id;
                                        foreach($metadata as $mrow)
                                        {
                                            $mrow['id'] = null;
                                            unset($mrow['id']);
                                            $mrow['group_id'] = $new_community_id;                                
                                            $wpdb->insert($wpdb->prefix . "bp_groups_groupmeta", $mrow);
                                        }
                                        
                                        //Add site admin to the communitye admin
                                        foreach($admins as $admin)
                                        {
                                            $wpdb->insert($wpdb->prefix . "bp_groups_members", array('group_id' => $new_community_id, 'user_id' => $admin->ID, 'is_admin' => 1, 'user_title' => $admin->display_name, 'date_modified' => date('Y-m-d H:i:s'), 'is_confirmed' => 1));
                                        }
                                    }
                                    
                                    //Delete Old Profile Type and Profile Data
                                    $wpdb->query("DELETE FROM {$wpdb->prefix}community_profile_instances WHERE community_id=" . $new_community_id);
                                    $wpdb->query("DELETE FROM {$wpdb->prefix}community_profile_types WHERE community_id=" . $new_community_id);
                                    
                                    //ID Map Cache
                                    $profile_type_ids = array();
                                    $profile_instance_ids = array();
                                    
                                    //Getting Profile Types
                                    $query = "SELECT * FROM {$wpdb->prefix}community_profile_types WHERE community_id=" . $community['id'];
                                    $pTypes = $new_wpdb->get_results($query, ARRAY_A);
                                    foreach($pTypes as $pType)
                                    {
                                        $data = $pType;
                                        $data['id'] = null; unset($data['id']);
                                        $data['community_id'] = $new_community_id;
                                        $wpdb->insert($wpdb->prefix . 'community_profile_types', $data);
                                        $nTypeId = $wpdb->insert_id;
                                        
                                        $profile_type_ids[$pType['id']] = $nTypeId;
                                        
                                        //Copy Profile Instances
                                        $query = "SELECT * FROM {$wpdb->prefix}community_profile_instances WHERE type_id=" . $pType['id'];
                                        $pInstances = $new_wpdb->get_results($query, ARRAY_A);    
                                        foreach($pInstances as $pIns)
                                        {
                                            $pIns['community_id'] = $new_community_id;
                                            $pIns['type_id'] = $nTypeId;
                                            $oPInsId = $pIns['id'];
                                            $pIns['id'] = null;
                                            unset($pIns['id']);
                                            $wpdb->insert($wpdb->prefix . 'community_profile_instances', $pIns);
                                            $profile_instance_ids[$oPInsId] = $wpdb->insert_id;
                                            $wpdb->query($wpdb->prepare("UPDATE " . $wpdb->prefix . "community_profile_types SET `instances`=`instances` + 1 WHERE id=%d", $pIns['type_id']));
                                        }
                                    }
                                    
                                    //Getting Downloads
                                    $query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}bp_groups_downloads WHERE group_id=%d", $community['id']); 
                                    $downloads = $new_wpdb->get_results($query, ARRAY_A);
                                    foreach($downloads as $drow)
                                    {
                                        $drow['id'] = null; unset($drow['id']);
                                        $drow['group_id'] = $new_community_id;
                                        $wpdb->insert($wpdb->prefix . 'bp_groups_downloads', $drow);
                                    }
                                    
                                    echo '<b>Community: ' . $community['name'] . ' has been copied.</b><br />';
                                    
                                    //Copy Suites
                                    $query = "SELECT DISTINCT(post_id) FROM {$wpdb->postmeta} WHERE meta_key='community_id' AND meta_value=" . $community['id'];
                                    $tSuiteIDs = $new_wpdb->get_col($query);
                                    
                                    $query = "SELECT * FROM {$wpdb->posts} WHERE ID in (" . implode(', ', $tSuiteIDs) . ")";
                                    $rows = $new_wpdb->get_results($query, ARRAY_A);
                                    
                                    //Old Id, new Id Maps
                                    $suitesMap = array();
                                    $scenariosMap = array();
                                    
                                    foreach($rows as $row)
                                    {
                                        $newId = ct_copy_test_suite($new_wpdb, $row);
                                        update_post_meta($newId, 'community_id', $new_community_id);
                                        
                                        $suitesMap[intval($row['ID'])] = $newId;
                                        
                                        //Copy Test Suite Scenarios
                                        $query = "SELECT * FROM {$wpdb->prefix}test_suites_scenarios WHERE suite_id=" . $row['ID'];
                                        $scenarios = $new_wpdb->get_results($query);
                                        
                                        foreach($scenarios as $scenario)
                                        {
                                            $wpdb->insert($wpdb->prefix . 'test_suites_scenarios', 
                                                array('suite_id' => $newId, 'code' => $scenario->code, 'description' => $scenario->description, 'sequence' => $scenario->sequence));
                                            $newScenarioId = $wpdb->insert_id;
                                            $scenariosMap[$scenario->id] = $newScenarioId;
                                        }
                                        
                                    }
                                    
                                    //Update Profile Types
                                    foreach($profile_type_ids as $oid => $nid)
                                    {
                                        $wpdb->query("UPDATE {$wpdb->postmeta} SET `meta_value`=REPLACE(`meta_value`, ';;{$oid};;', ';;{$nid};;') WHERE `meta_key`='ts_profile_types'");
                                    }
                                    
                                    //Copy Test Cases that are associated to the test suites
                                    $query = "SELECT DISTINCT(post_id) FROM {$wpdb->postmeta} WHERE meta_key='test_suite' AND meta_value IN ('" . implode("','", $tSuiteIDs) . "')";
                                    $tCaseIDs = $new_wpdb->get_col($query);
                                    
                                    $query = "SELECT * FROM {$wpdb->posts} WHERE ID in (" . implode(', ', $tCaseIDs) . ")";
                                    $rows = $new_wpdb->get_results($query, ARRAY_A);
                                    
                                    foreach($rows as $row)
                                    {
                                        ct_copy_test_case($new_wpdb, $row, $suitesMap, $scenariosMap, $esb_case_configurations);
                                    }
                                    //var_dump($profile_instance_ids);
                                    //Update Profile Types
                                    foreach($profile_instance_ids as $oid => $nid)
                                    {
                                        $wpdb->query("UPDATE {$wpdb->postmeta} SET `meta_value`=REPLACE(`meta_value`, ';;{$oid};;', ';;{$nid};;') WHERE `meta_key`='profile_instances'");
                                    }
                                    
                                }
                                
                               //Getting New Test Suites Family Marks and Suites
                                $query = "SELECT family_mark, suite_title FROM wp_test_suites GROUP BY family_mark ORDER BY suite_title";
                                $new_suites_family_marks = $wpdb->get_results($query);
                                
                                //Getting Suite Titles and Ids
                                $query = "SELECT * FROM wp_test_suites ORDER BY suite_title, version_major, version_minor, version_patch";
                                $new_suites = $wpdb->get_results($query);

                            ?>
                                <h3>Update Suites For Subscriptions, Test Plans, Claims, Conversations, Services</h3>
                                <form action="" method="post">
                                    <input type="hidden" name="page" value="duplicate_data" />
                                    <input type="hidden" name="action" value="<?php echo wp_create_nonce('conform-update-suite-ids')?>" />
                                    <table>
                                        <tr>
                                            <td valign="top">
                                                <h4>Update Suites</h4>
                                                <table cellpadding="5" border="1" class="widefat" style="width: auto; border-collapse: collapse;">
                                                    <thead>
                                                        <tr>
                                                            <th>Old Suite</th>
                                                            <th>New Suite</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach($old_suites_family_marks as  $orow): ?>
                                                        <tr>
                                                            <td>
                                                                <?php echo $orow->suite_title ?>
                                                                <input type="hidden" name="old_family_mark[]" value="<?php echo $orow->family_mark ?>" />
                                                            </td>
                                                            <td>
                                                                <select name="new_family_mark[]">
                                                                    <?php foreach ($new_suites_family_marks as  $nrow) :?>
                                                                    <option value="<?php echo $nrow->family_mark?>" <?php echo $nrow->suite_title == $orow->suite_title  ? 'selected="selected"' : ''?>><?php echo $nrow->suite_title?></option>
                                                                    <?php endforeach; ?>
                                                                </select>
                                                            </td>
                                                        </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>                                
                                                </table>  
                                            </td>
                                            <td valign="top">
                                                <h4>Update Suites Versions</h4>
                                                <table cellpadding="5" border="1" class="widefat" style="width: auto; border-collapse: collapse;">
                                                    <thead>
                                                        <tr>
                                                            <th>Old Suite</th>
                                                            <th>New Suite</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach($old_suites as  $orow): ?>
                                                        <tr>
                                                            <td>
                                                                <?php 
                                                                $o_title = $orow->suite_title . ' v' . $orow->version_major . '.' . $orow->version_minor  . ($orow->version_patch > 0 ? ".{$orow->version_patch}" : "");
                                                                echo $o_title?>
                                                                <input type="hidden" name="old_suites[]" value="<?php echo $orow->suite_id ?>" />
                                                            </td>
                                                            <td>
                                                                <select name="new_suites[]">
                                                                    <?php foreach ($new_suites as  $nrow) :?>
                                                                    <?php
                                                                        $t_title = $nrow->suite_title . ' v' . $nrow->version_major . '.' . $nrow->version_minor  . ($nrow->version_patch > 0 ? ".{$nrow->version_patch}" : "");
                                                                    ?>
                                                                    <option value="<?php echo $nrow->suite_id?>" <?php echo $o_title == $t_title ? 'selected="selected"' : ''?>><?php echo $t_title?></option>
                                                                    <?php endforeach; ?>
                                                                </select>
                                                            </td>
                                                        </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>                                
                                                </table>    
                                            </td>
                                        </tr>
                                    </table>
                                    
                                    
                                           
                                    <br />
                                    <input type="submit" class="button button-primary" value="Update" />
                                    
                                    <?php foreach ($esb_suite_configurations as $row): ?>
                                    <input type="hidden" name="esb_suite<?php echo $row->TEST_SUITE_WP_ID?>" value="<?php echo $row->ID?>" />
                                    <?php endforeach; ?>
                                    
                                </form>
                                <script type="text/javascript">
                                    jQuery(document).ready(function(){
                                        jQuery('html, body').animate({
                                            'scrollTop' : jQuery(document).height()
                                        }, 'slow');
                                    })
                                </script>
                                <?php
                            }else{
                                echo '<br />Completed!<br /><a href="/wp-admin/tools.php?page=duplicate_data">Back</a>';
                                exit; 
                            }               
                    }
                   
               }
                
            }else if(wp_verify_nonce($action, 'conform-update-suite-ids')){
                //Update family Marks for organisation subscriptions
                $old_family_marks = $_POST['old_family_mark'];
                $new_family_marks = $_POST['new_family_mark'];
                for ($i=0; $i < count($old_family_marks); $i++) {
                    $query = $wpdb->prepare('UPDATE wp_organisations_subscriptions SET suite_family_mark=%d WHERE suite_family_mark=%d', $new_family_marks[$i],  $old_family_marks[$i]);
                    $wpdb->query($query);
                }
                
                $esb = new ManageESB();
                
                $old_suites = $_POST['old_suites'];
                $new_suites = $_POST['new_suites'];
                
                for ($i=0; $i < count($old_suites); $i++) {
                    //Update User Subscriptions
                    $query = $wpdb->prepare('UPDATE wp_users_subscriptions SET suite_id=%d WHERE suite_id=%d', $new_suites[$i],  $old_suites[$i]);                    
                    $wpdb->query($query);
                    //Update Test Plans
                    $query = $wpdb->prepare('UPDATE wp_test_plans SET suite_id=%d WHERE suite_id=%d', $new_suites[$i],  $old_suites[$i]);                    
                    $wpdb->query($query);
                    //Update Claims
                    $query = $wpdb->prepare('UPDATE wp_compliance_claims SET suite_id=%d WHERE suite_id=%d', $new_suites[$i],  $old_suites[$i]);                    
                    $wpdb->query($query);
                    //Updating Services
                    $query = $wpdb->prepare('UPDATE wp_postmeta SET meta_value=%d WHERE meta_key="service_suite_id" AND meta_value=%d', $new_suites[$i],  $old_suites[$i]);                    
                    $wpdb->query($query);
                    
                    //Update Conversations
                    if(isset($_POST['esb_suite' . $old_suites[$i]])) {
                        $old_esb_id = $_POST['esb_suite' . $old_suites[$i]];
                        $new_esb_id = $esb->getTestSuiteConfigurationID($new_suites[$i]);
                        $query = "UPDATE " . $esb->table_conversation_metadata . " SET TEST_SUITE_CONFIGURATION_ID=" . $new_esb_id . " WHERE TEST_SUITE_CONFIGURATION_ID=" . $old_esb_id;
                        
                        ManageESB::$esbdb->query($query);
                    }
                }
                
                echo '<br />Completed!<br /><a href="/wp-admin/tools.php?page=duplicate_data">Back</a>';
                exit; 
                
            }else{
                ?>
                <form action="" method="post">
                    <input type="hidden" name="page" value="duplicate_data" />
                    <input type="hidden" name="action" value="<?php echo wp_create_nonce('start-copy-data')?>" />
                    <h3>Source Database</h3>
                    <table cellpadding="5">
                        <tr>
                            <td><b>Host</b></td>
                            <td><input type="text" name="host" value="<?php echo $_POST['host']?>" autocomplete="off" /></td>
                        </tr>                
                        <tr>
                            <td><b>Database</b></td>
                            <td><input type="text" name="database" value="<?php echo $_POST['database']?>" autocomplete="off" /></td>
                        </tr>                                                        
                        <tr>
                            <td><b>Username</b></td>
                            <td><input type="text" name="username" value="<?php echo $_POST['username']?>" autocomplete="off" /></td>
                        </tr>                
                        <tr>                         
                            <td><b>Password</b></td>
                            <td><input type="text" name="password" value="<?php echo $_POST['password']?>" autocomplete="off" /></td>
                        </tr>                
                        <tr>
                            <td colspan="2"><input type="submit" class="button button-primary" value="Start" /></td>
                        </tr>                
                    </table>            
                </form>
                <?php
            }
            ?>
        <hr/>
        <h2>Generate Search Meta Data of Profiles</h2>
        <div>
            <form action="" method="post">
                <input type="hidden" name="action" value="<?php echo wp_create_nonce('generate-search-meta')?>" />
                <table>
                    <tr>
                        <td>
                            <input type="submit" class="button button-primary" value="Generate" />
                        </td>
                    </tr>
                    <?php if (wp_verify_nonce($action, 'generate-search-meta')): ?>
                    <tr>
                        <td>
                            <i>
                            <?php
                                $wpdb->delete($wpdb->prefix . 'community_profile_meta', array('1'=>'1'), '%d');
                                $results = $wpdb->get_results("SELECT * FROM $wpdb->prefix" . "community_profile_instances");

                                foreach ($results as $row) {
                                    $content = S3Wrapper::getProfile( $row->token );
                                    $profile_meta = getProfileMetaData($content);
                                    foreach ($profile_meta as $meta_key => $meta_value) {
                                        $wpdb->insert($wpdb->prefix . "community_profile_meta", array(
                                            'profile_id' => $row->id,
                                            'meta_key' => $meta_key,
                                            'meta_value' => $meta_value,
                                        ));
                                    }
                                }

                                echo 'Generated ' . count($results) . ' profiles\' search meta.';
                            ?>
                            </i>
                        </td>
                    </tr>
                    <?php endif; ?>
                </table>                
            </form>
        </div>

        <h2>Copy Services data to wp_services table</h2>
        <div>
            <form action="" method="post">
                <input type="hidden" name="action" value="<?php echo wp_create_nonce('populate_wp_services')?>" />
                <table>
                    <tr>
                        <td>
                            <input type="submit" class="button button-primary" value="Populate" />
                        </td>
                    </tr>
                    <?php if (wp_verify_nonce($action, 'populate_wp_services')): ?>
                        <tr>
                            <td>
                                <i>
                                    <?php
                                        $args = array(
                                            'post_type' => 'service',
                                            'posts_per_page' => -1,
                                            'tax_query' => array('relation' => 'and')
                                        );
                                        $posts = get_posts( $args );
                                        $posts_processed = 0;
                                        global $wpdb;
                                        $wpdb->query("TRUNCATE wp_services;");
                                        foreach( $posts AS $post ) {
                                            save_wp_service( $post->ID );
                                            $posts_processed++;
                                        }

                                        echo 'Processed ' . $posts_processed . ' services.';
                                    ?>
                                </i>
                            </td>
                        </tr>
                    <?php endif; ?>
                </table>
            </form>
        </div>

        <h2>Copy Blobs to S3</h2>
        <div>
            <form action="" method="post">
                <input type="hidden" name="action" value="<?php echo wp_create_nonce('copy_blobs_to_s3')?>" />
                <table>
                    <tr>
                        <td>
                            <select name="type">
                                <option value="all" <?php if( isset( $_POST['type'] ) && $_POST['type'] == 'all'):?>selected="selected" <?php endif;?>>All</option>
                                <option value="p_claims" <?php if( isset( $_POST['type'] ) && $_POST['type'] == 'p_claims'):?>selected="selected" <?php endif;?>>Claims - Products</option>
                                <option value="a_claims" <?php if( isset( $_POST['type'] ) && $_POST['type'] == 'a_claims'):?>selected="selected" <?php endif;?>>Claims / Attachments - Agreements</option>
                                <option value="a_tickets" <?php if( isset( $_POST['type'] ) && $_POST['type'] == 'a_tickets'):?>selected="selected" <?php endif;?>>Attachments - Tickets</option>
                                <option value="profiles" <?php if( isset( $_POST['type'] ) && $_POST['type'] == 'profiles'):?>selected="selected" <?php endif;?>>Profiles</option>
                            </select>
                        </td>
                        <td>
                            <input type="submit" class="button button-primary" value="Populate" />
                        </td>
                    </tr>
                    <?php if (wp_verify_nonce($action, 'copy_blobs_to_s3')): ?>
                        <tr>
                            <td>
                                <i>
                                    <?php
                                    if( $_POST['type'] == 'profiles' ){
                                        $counter = BlobsMigration::uploadProfiles();
                                        echo 'Processed: '.$counter.' profiles';
                                    } else if( $_POST['type'] == 'all' ){

                                        $counter = BlobsMigration::uploadProfiles();
                                        echo 'Processed: '.$counter.' profiles';
                                        $counter = BlobsMigration::uploadProductClaims();
                                        echo 'Processed: '.$counter.' product certificates';
                                        $counter = BlobsMigration::uploadServiceClaims();
                                        echo 'Processed: '.$counter.' service certificates';

                                    } else if( $_POST['type'] == 'p_claims' ){
                                        $counter = BlobsMigration::uploadProductClaims();
                                        echo 'Processed: '.$counter.' product certificates';
                                    } else if( $_POST['type'] == 'a_claims' ){
                                        $counter = BlobsMigration::uploadServiceClaims();
                                        echo 'Processed: '.$counter.' service certificates';
                                    } else if( $_POST['type'] == 'a_tickets' ){
                                        $counter = BlobsMigration::uploadTicketsAttachments();
                                        echo 'Processed: '.$counter.' attachments';
                                    }else {
                                        echo 'Not implemented yet';
                                    }
                                    ?>
                                </i>
                            </td>
                        </tr>
                    <?php endif; ?>
                </table>
            </form>
        </div>
    </div>
    <?php
}

function ct_copy_test_case($new_wpdb, $case, $suiteIDMap = null, $scenariosIDMap = null, $old_case_configurations = array())
{
    global $wpdb;
    
    $oldID = $case['ID'];
                            
    $case['ID'] = null;
    unset($case['ID']);
    if($newId = wp_insert_post($case))
    {
        //Getting Post Meta
        $query = "SELECT * FROM {$wpdb->postmeta} WHERE post_id=" . $oldID;
        $metas = $new_wpdb->get_results($query);
        
        $version_major = 0;
        $version_minor = 0;
        $version_patch = 0;
        
        $testCaseId = null;
        
        foreach($metas as $mrow)
        {
            if($mrow->meta_key == 'test_case_id')
                $testCaseId = $mrow->meta_value;
            else if($mrow->meta_key == 'version_major')
                $version_major = $mrow->meta_value;
            else if($mrow->meta_key == 'version_minor')
                $version_minor = $mrow->meta_value;
            else if($mrow->meta_key == 'version_patch')
                $version_patch = $mrow->meta_value;
            
            //Conf Level, Test_Suite, Scenario
            if($suiteIDMap && (strpos($mrow->meta_key, 'conformance_level_') !== false || strpos($mrow->meta_key, 'scenario_') !== false || $mrow->meta_key == 'test_suite'))
            {
                if(strpos($mrow->meta_key, 'conformance_level_') !== false)
                {
                    $oSuiteId = intval(str_replace('conformance_level_', '', $mrow->meta_key));
                }else if(strpos($mrow->meta_key, 'scenario_') !== false){
                    $oSuiteId = intval(str_replace('scenario_', '', $mrow->meta_key));
                }else{
                    $oSuiteId = intval($mrow->meta_value);
                }
                if(!isset($suiteIDMap[$oSuiteId]))
                    continue;
                if(strpos($mrow->meta_key, 'conformance_level_') !== false)   
                    $wpdb->insert($wpdb->postmeta, array('post_id' => $newId, 'meta_key' => 'conformance_level_' . $suiteIDMap[$oSuiteId], 'meta_value' => $mrow->meta_value));
                else if(strpos($mrow->meta_key, 'scenario_') !== false)   
                    $wpdb->insert($wpdb->postmeta, array('post_id' => $newId, 'meta_key' => 'scenario_' . $suiteIDMap[$oSuiteId], 'meta_value' => isset($scenariosIDMap[$mrow->meta_value]) ? $scenariosIDMap[$mrow->meta_value] : $mrow->meta_value));
                else
                    $wpdb->insert($wpdb->postmeta, array('post_id' => $newId, 'meta_key' => 'test_suite', 'meta_value' => $suiteIDMap[$oSuiteId]));
            }else{            
                $wpdb->insert($wpdb->postmeta, array('post_id' => $newId, 'meta_key' => $mrow->meta_key, 'meta_value' => $mrow->meta_value));
            }
        }
        
        //Update wp_test_cases table
        $query = $wpdb->prepare("SELECT family_mark FROM {$wpdb->prefix}test_cases WHERE case_name=%s", $testCaseId);
        $familyMark = $wpdb->get_var($query);
        
        if(!$familyMark)
            $familyMark = $newId;
       $wpdb->insert($wpdb->prefix . "test_cases", 
                        array('case_id' => $newId, 
                              'case_name' => $testCaseId, 
                              'version_major' => $version_major, 
                              'version_minor' => $version_minor, 
                              'version_patch' => $version_patch,
                              'family_mark' => $familyMark)
                     );
       
        cp_sort_test_cases($familyMark, $version_major);
       
        
        $versions = array($version_major, $version_minor);
        if($version_patch)
            $versions[] = $version_patch;
        
        $esb = new ManageESB();
        $newEsbId = $esb->saveTestCaseInfo($newId, $testCaseId . "_V" . implode(".", $versions), get_post_meta($newId, 'outcome_type', true), get_post_meta($newId, 'message_count', true));
        
        //Update esb conversation table
        foreach ($old_case_configurations as $orow) {
            if ($orow->TEST_CASE_ID == $testCaseId . "_V" . implode(".", $versions)) {
                $query = $wpdb->prepare("UPDATE " . $esb->table_conversation_metadata . " SET TEST_CASE_CONFIGURATION_ID=%d WHERE TEST_CASE_CONFIGURATION_ID=%d", $newEsbId, $orow->ID);
                ManageESB::$esbdb->query($query);
                echo $query . "<br />";
            }
        }
        
        echo '<b>Test Case: ' . $case['post_title'] . ' has been copied.</b><br />';
    }
    
    return $newId;
}

function ct_copy_test_suite($new_wpdb, $suite)
{
    global $wpdb;
    
    $oldSuiteID = $suite['ID'];
    
    $suite['ID'] = null;
    unset($suite['ID']);
    
    $esb = new ManageESB();
    
    if($newSuiteId = wp_insert_post($suite))
    {
        $suitesMap[$oldSuiteID] = $newSuiteId;
        
        //Getting Post Meta
        $query = "SELECT * FROM {$wpdb->postmeta} WHERE post_id=" . $oldSuiteID;
        $metas = $new_wpdb->get_results($query);
        
        $version_major = 0;
        $version_minor = 0;
        $version_patch = 0;
        
        $testSuiteName = null;
        
        foreach($metas as $mrow)
        {
            if($mrow->meta_key == 'ts_name')
                $testSuiteName = $mrow->meta_value;
            else if($mrow->meta_key == 'ts_version_major')
                $version_major = $mrow->meta_value;
            else if($mrow->meta_key == 'ts_version_minor')
                $version_minor = $mrow->meta_value;
            else if($mrow->meta_key == 'ts_version_patch')
                $version_patch = $mrow->meta_value;
            
            $wpdb->insert($wpdb->postmeta, array('post_id' => $newSuiteId, 'meta_key' => $mrow->meta_key, 'meta_value' => $mrow->meta_value));
        }
        
        //Update wp_test_cases table
        $query = $wpdb->prepare("SELECT family_mark FROM {$wpdb->prefix}test_suites WHERE suite_title=%s", $testSuiteName);
        $familyMark = $wpdb->get_var($query);
        
        if(!$familyMark)
            $familyMark = $newSuiteId;
            
       $wpdb->insert($wpdb->prefix . "test_suites", 
                        array('suite_id' => $newSuiteId, 
                              'suite_title' => $testSuiteName, 
                              'version_major' => $version_major, 
                              'version_minor' => $version_minor, 
                              'version_patch' => $version_patch,
                              'family_mark' => $familyMark)
                     );
        cp_sort_test_suites($familyMark, $version_major);

        //Copy Special Documents
        $query = "SELECT * FROM {$wpdb->prefix}ts_options_documents WHERE ts_id=" . $oldSuiteID;
        $docs = $new_wpdb->get_results($query);
        
        foreach($docs as $doc)
        {
            $wpdb->insert($wpdb->prefix . 'ts_options_documents', 
                array('ts_id' => $newSuiteId, 'doc_name' => $doc->doc_name, 'doc_desc' => $doc->doc_desc, 'doc_loc_url' => $doc->doc_loc_url));
        }        
        
        $version = array($version_major, $version_minor);
        if($version_patch)
            $version[]=  $version_patch;
        
        $esb->saveTestSuiteInfo($newSuiteId, get_post_meta($newSuiteId, 'ts_identifier', true) . "_V" . implode(".", $version), $suite['post_title']);
    }
    
    echo '<b>Test Suite: ' . $suite['post_title'] . ' has been copied.</b><br />';
    return $newSuiteId;
}
    

function ct_fix_whole_test_suites_table()
{
    global $wpdb;
    
    $args = array(
            'post_type' => 'test-suite',         
            'posts_per_page' => -1,
            'tax_query' => array('relation' => 'and'),
            'orderby' => 'title',
            'order' => 'asc'
    );
    $all_posts = new WP_Query($args);
    
    $testsuites = $all_posts->get_posts();
    
    foreach($testsuites as $suite)
    {
        $version_major = get_post_meta($suite->ID, 'ts_version_major', true);
        $version_minor = get_post_meta($suite->ID, 'ts_version_minor', true);
        $version_patch = get_post_meta($suite->ID, 'ts_version_patch', true);
        
        //Getting Family
        $testSuiteName = get_post_meta($suite->ID, 'ts_name', true);
        
        //Update wp_test_cases table
        $query = $wpdb->prepare("SELECT family_mark FROM {$wpdb->prefix}test_suites WHERE suite_title=%s", $testSuiteName);
        $familyMark = $wpdb->get_var($query);
        
        if(!$familyMark)
            $familyMark = $suite->ID;
        
        $wpdb->insert($wpdb->prefix . "test_suites", 
                        array('suite_id' => $suite->ID, 
                              'suite_title' => $testSuiteName, 
                              'version_major' => $version_major, 
                              'version_minor' => $version_minor, 
                              'version_patch' => $version_patch,
                              'family_mark' => $familyMark)
                     );
        cp_sort_test_suites($familyMark, $version_major);
    }
}


function ct_fix_whole_test_cases_table()
{
    global $wpdb;
    
    $args = array(
            'post_type' => 'test-case',         
            'posts_per_page' => -1,
            'tax_query' => array('relation' => 'and'),
            'orderby' => 'title',
            'order' => 'asc'
    );
    $all_posts = new WP_Query($args);
    
    $all = $all_posts->get_posts();
    
    foreach($all as $case)
    {
        $version_major = get_post_meta($case->ID, 'version_major', true);
        $version_minor = get_post_meta($case->ID, 'version_minor', true);
        $version_patch = get_post_meta($case->ID, 'version_patch', true);
        
        //Getting Family
        $caseId = get_post_meta($case->ID, 'test_case_id', true);
        
        //Update wp_test_cases table
        $query = $wpdb->prepare("SELECT family_mark FROM {$wpdb->prefix}test_cases WHERE case_name=%s", $caseId);
        $familyMark = $wpdb->get_var($query);
        
        if(!$familyMark)
            $familyMark = $case->ID;
        
        $wpdb->insert($wpdb->prefix . "test_cases", 
                        array('case_id' => $case->ID, 
                              'case_name' => $caseId, 
                              'version_major' => $version_major, 
                              'version_minor' => $version_minor, 
                              'version_patch' => $version_patch,
                              'family_mark' => $familyMark)
                     );
        cp_sort_test_cases($familyMark, $version_major);
    }
}

