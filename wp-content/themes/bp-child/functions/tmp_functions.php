<?php
/**
* Tmp Functions to fix data
* Only Supper Admin Can access this page
*/

//Fix the family_mark of test cases

if(is_super_admin())
{
    global $wpdb;
    if(isset($_GET['fix_suite_family_mark']))
    {
        $query = "SELECT distinct(suite_title), suite_id FROM {$wpdb->prefix}test_suites GROUP BY suite_title ORDER BY suite_id";
        $rows = $wpdb->get_results($query);
        foreach($rows as $row)
        {
            $wpdb->update($wpdb->prefix . 'test_suites', array('family_mark' => $row->suite_id), array('suite_title' => $row->suite_title));
        }
        
        die("Done!");                        
    }
    if(isset($_GET['fix_case_family_mark']))
    {
        $query = "SELECT distinct(case_name), case_id FROM {$wpdb->prefix}test_cases GROUP BY case_name ORDER BY case_id";
        $rows = $wpdb->get_results($query);
        foreach($rows as $row)
        {
            $wpdb->update($wpdb->prefix . 'test_cases', array('family_mark' => $row->case_id), array('case_name' => $row->case_name));
        }
        
        die("Done!");                        
    }
    if(isset($_GET['fix_case_scenario']))
    {        
        
        //Getting Default Levels
        $query = "SELECT suite_id, id FROM wp_test_suites_scenarios WHERE `code`='" . TEST_SUITE_DEFAULT_SCENARIO_CODE . "'";
        $rows = $wpdb->get_results($query);
        $dScenarios = array();
        foreach($rows as $row)
            $dScenarios[$row->suite_id] = $row->id;
        
        $query = "SELECT * FROM {$wpdb->postmeta} WHERE meta_key='test_suite' order by post_id";
        $rows =  $wpdb->get_results($query);
        
        foreach($rows as $row)
        {
            if(!$dScenarios[$row->meta_value])
                continue;
            $wpdb->insert($wpdb->postmeta, array('post_id' => $row->post_id, 'meta_key' => 'scenario_' . $row->meta_value, 'meta_value' => $dScenarios[$row->meta_value]));
        }
        
        die("Done!");
    }
    
    
}
if(isset($_GET['download_profile_type']))
    {
        downloadProfileType();
        exit;
    }