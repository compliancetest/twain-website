<?php


add_action('admin_init', 'do_test_case_ajax_action');
function do_test_case_ajax_action()
{
    if(!is_admin())     //If not admin, return;
        return;
        
    if(isset($_POST['suite_id']) && isset($_POST['post_id']))    
    {
        $postID = $_POST['post_id'];
        //Check _wpnounce
        if(wp_verify_nonce($_POST['_wpnonce'], 'get_suite_roles'))
        {   
            $suiteRoles = getTestSuiteRoles($_POST['suite_id']);
            echo '<option value="">Choose Tester Role</option>';
            foreach($suiteRoles as $role){            
                echo '<option value="'. $role['name']. '"  class="'.$test_suite.'">'.$role['name'].'</option>';                        
            }    
            exit;
        }else if(wp_verify_nonce($_POST['_wpnonce'], 'get_suite_init_messages')){   
            $metas_result = get_post_meta($_POST['suite_id'], 'init_message', true);
            echo '<select name="choose_init_messages" id="checkinitmsg">';
            echo '<option value="">Choose Initiating Message</option>';
            $metas_array = explode(',', $metas_result);
            if($metas_result)
            {
                foreach($metas_array as $ts_init_message){
                    echo '<option value="'.$ts_init_message.'">'.$ts_init_message.'</option>';             
                }
            }
            echo '</select>';
            exit;
        }else if(wp_verify_nonce($_POST['_wpnonce'], 'get_suite_conf_level')){   
            $metas_result = get_post_meta($_POST['suite_id'], 'lvl_code', true);
            echo '<select name="conformance_level" id="checkconflvl">';
            echo '<option value="">Choose Conformance Level</option>';
            
            if($metas_result)
            {
                foreach($metas_result as $v){
                    echo '<option value="'.$v.'">'.$v.'</option>';             
                }
            }
            echo '</select>';
            exit;
        }else if(wp_verify_nonce($_POST['_wpnonce'], 'get_init_message')){   
            $metas_result = get_post_meta($_POST['suite_id'], 'init_message', true);
            $metas_result = explode(',', $metas_result);
            echo '<select name="choose_init_messages" id="checkinitmsg">';
            echo '<option value="">Choose Initiating Message</option>';
            
            if($metas_result)
            {
                foreach($metas_result as $v){
                    echo '<option value="'.$v.'">'.$v.'</option>';             
                }
            }
            echo '</select>';
            exit;
        }
        
        
    }
}

//Get selected test suites of the test case
function _get_current_test_suites($test_case_id)
{
    $current_test_suites = get_post_meta($test_case_id, 'test_suites', true);
    
    if(is_array($current_test_suites))
        $current_test_suites = $current_test_suites[0];
    if(!$current_test_suites)
        $current_test_suites = null;
    else
        $current_test_suites = explode('|', $current_test_suites);
    
    if(!$current_test_suites)
        $current_test_suites = array(isset($_GET['set_ts']) ? $_GET['set_ts'] : 0);
    
    $ids = array();
    foreach($current_test_suites as $id)
    {
        if($id == '')
            continue;
        $ids[] = $id;
    }
    return $ids;
}
