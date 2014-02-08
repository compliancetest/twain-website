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
                       $sourceCommunities = $wpdb->get_results($query, ARRAY_A);
                       
                       $query = "SELECT * FROM " . $wpdb->posts . " WHERE post_type  = 'test-suite' ORDER BY post_title";
                       $sourceSuites = $wpdb->get_results($query, ARRAY_A);
                       
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
                        <br />
                        <h3>Select data copied from the source site</h3>
                        <form action="" method="post" id="copyDataForm">
                            <input type="hidden" name="page" value="duplicate_data" />
                            <input type="hidden" name="action" value="<?php echo wp_create_nonce('complete-duplicate-data')?>" />
                            <div class="articles">
                                <h4 style="float: left; margin-top: 0; margin-bottom: 5px; margin-right: 10px;">Select Communities</h4> <a href="#" class="check-all">Select All</a>
                                <div style="clear:both; font-weight: bold;">(The test suites, test cases and test data that belong to the community will be copied together.)</div>
                                <table class="widefat" style="width: auto;">
                                    <thead>
                                        <tr>
                                            <th>Community</th>
                                            <th>Copy / Update</th>
                                            <th>Updated Community</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($communities as $c): ?>
                                        <tr>
                                            <td>
                                                <label><input type="checkbox" name="community_ids[]" value="<?php echo $c['id']?>" /> (ID: #<?php echo $c['id']?>) <?php echo $c['name']?></label>
                                            </td>
                                            <td>
                                                <label><input type="radio" name="copy_method<?php echo $c['id']?>" value="copy" checked="checked" /> Copy</label>
                                                <label><input type="radio" name="copy_method<?php echo $c['id']?>" value="update" /> Update</label>
                                            </td>
                                            <td>
                                                <select name="updated_community<?php echo $c['id']?>">
                                                    <option>Select Community</option>
                                                    <?php foreach($sourceCommunities as $sc): ?>
                                                    <option value="<?php echo $sc['id']?>"><?php echo $sc['name']?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </td>
                                        </tr>                                        
                                        <?php endforeach; ?>        
                                    </tbody>
                                </table>
                                
                            </div>
                            <br />
                            <div class="articles">
                                <h4 style="float: left; margin-top: 0; margin-bottom: 5px; margin-right: 10px;">Select Test Suites</h4> <a href="#" class="check-all">Select All</a>
                                <div style="clear:both; font-weight: bold;">(The test cases that are assigned to the test suite will be copied together.)</div>
                                <table class="widefat" style="width: auto;">
                                    <thead>
                                        <tr>
                                            <th>Test Suite</th>                                            
                                            <th>Assigned to</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach($suites as $c): ?>                                
                                    <tr>
                                        <td>
                                            <label><input type="checkbox" name="suite_ids[]" value="<?php echo $c['ID']?>" /> (ID: #<?php echo $c['ID']?>) <?php echo $c['post_title']?></label>
                                        </td>
                                        <td>
                                            <select name="dest_community<?php echo $c['ID']?>">
                                                <option>Select Community</option>
                                                <?php foreach($sourceCommunities as $sc): ?>
                                                <option value="<?php echo $sc['id']?>"><?php echo $sc['name']?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                    </tr> 
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>                                
                            </div>
                            <br />
                            <div class="articles">
                                <h4 style="float: left; margin-top: 0; margin-bottom: 5px; margin-right: 10px;">Select Test Cases</h4><a href="#" class="check-all">Select All</a>
                                <br clear="all" />
                                <table class="widefat" style="width: auto;">
                                    <thead>
                                        <tr>
                                            <th>Test Case</th>                                            
<!--                                            <th>Assigned to</th>-->
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach($cases as $c): ?>
                                    <tr>
                                        <td>
                                            <label><input type="checkbox" name="case_ids[]" value="<?php echo $c['ID']?>" /> (ID: #<?php echo $c['ID']?>) <?php echo $c['post_title']?></label>
                                        </td>
                                        <!--<td>
                                            <select name="dest_suites<?php echo $c['ID']?>[]" multiple="multiple">
                                                <?php foreach($sourceSuites as $sc): ?>
                                                <option value="<?php echo $sc['ID']?>"><?php echo $sc['post_title']?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>-->
                                    </tr> 
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
                                })
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
                            
                            //Copy Test Cases
                            if($caseIDs)
                            {
                                echo '<h4>Copy Test Cases</h4>';
                                $query = "SELECT * FROM {$wpdb->posts} WHERE ID in (" . implode(', ', $caseIDs) . ")";
                                $rows = $new_wpdb->get_results($query, ARRAY_A);
                                
                                foreach($rows as $row)
                                {
                                    ct_copy_test_case($new_wpdb, $row);
                                }
                                echo "<hr />";
                            }
                            
                            //Copy Test Suite
                            if($suiteIDs)
                            {
                                echo '<h4>Copy Test Suites</h4>';
                                $query = "SELECT * FROM {$wpdb->posts} WHERE ID in (" . implode(', ', $suiteIDs) . ")";
                                $suites = $new_wpdb->get_results($query, ARRAY_A);
                                
                                //Old Id, new Id Maps
                                $suitesMap = array();
                                $scenariosMap = array();
                                
                                foreach($suites as $suite)
                                {
                                    $newId = ct_copy_test_suite($new_wpdb, $suite);
                                    
                                    //Update Community ID if it is set
                                    if($_POST['dest_community' . $suite['ID']])
                                    {
                                        update_post_meta($newId, 'community_id', $_POST['dest_community' . $suite['ID']]);
                                    }
                                    $suitesMap[intval($suite['ID'])] = $newId;
                                    
                                    //Copy Test Suite Scenarios
                                    $query = "SELECT * FROM {$wpdb->prefix}test_suites_scenarios WHERE suite_id=" . $suite['ID'];
                                    $scenarios = $new_wpdb->get_results($query);
                                    
                                    foreach($scenarios as $scenario)
                                    {
                                        $wpdb->insert($wpdb->prefix . 'test_suites_scenarios', 
                                            array('suite_id' => $newId, 'code' => $scenario->code, 'description' => $scenario->description, 'sequence' => $scenario->sequence));
                                        $newScenarioId = $wpdb->insert_id;
                                        $scenariosMap[$scenario->id] = $newScenarioId;
                                    }
                                }
                                
                                //Copy Test Cases that are associated to the test suites
                                $query = "SELECT DISTINCT(post_id) FROM {$wpdb->postmeta} WHERE meta_key='test_suite' AND meta_value IN ('" . implode("','", $suiteIDs) . "')";
                                $tCaseIDs = $new_wpdb->get_col($query);
                                
                                $query = "SELECT * FROM {$wpdb->posts} WHERE ID in (" . implode(', ', $tCaseIDs) . ")";
                                $rows = $new_wpdb->get_results($query, ARRAY_A);
                                
                                foreach($rows as $row)
                                {
                                    ct_copy_test_case($new_wpdb, $row, $suitesMap, $scenariosMap);
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
                                        //Copy Profile Instances
                                        $query = "SELECT * FROM {$wpdb->prefix}community_profile_instances WHERE type_id=" . $pType['id'];
                                        $pInstances = $new_wpdb->get_results($query, ARRAY_A);    
                                        foreach($pInstances as $pIns)
                                        {
                                            $pIns['community_id'] = $new_community_id;
                                            $pIns['type_id'] = $nTypeId;
                                            $pIns['id'] = null;
                                            unset($pIns['id']);
                                            $wpdb->insert($wpdb->prefix . 'community_profile_instances', $pIns);
                                        }
                                    }
                                    
                                    echo '<b>Community: ' . $community['name'] . ' has been copied.</b>';
                                    
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
                                    
                                    //Copy Test Cases that are associated to the test suites
                                    $query = "SELECT DISTINCT(post_id) FROM {$wpdb->postmeta} WHERE meta_key='test_suite' AND meta_value IN ('" . implode("','", $tSuiteIDs) . "')";
                                    $tCaseIDs = $new_wpdb->get_col($query);
                                    
                                    $query = "SELECT * FROM {$wpdb->posts} WHERE ID in (" . implode(', ', $tCaseIDs) . ")";
                                    $rows = $new_wpdb->get_results($query, ARRAY_A);
                                    
                                    foreach($rows as $row)
                                    {
                                        ct_copy_test_case($new_wpdb, $row, $suitesMap, $scenariosMap);
                                    }
                                    
                                }
                            }
                            echo '<br />Completed!<br /><a href="/wp-admin/tools.php?page=duplicate_data">Back</a>';
                            exit;                
                    }
                   
               }
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
            
    </div>
    <?php
}

function ct_copy_test_case($new_wpdb, $case, $suiteIDMap = null, $scenariosIDMap = null)
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
    }
    echo '<b>Test Suite: ' . $suite['post_title'] . ' has been copied.</b><br />';
    return $newSuiteId;
}
    

