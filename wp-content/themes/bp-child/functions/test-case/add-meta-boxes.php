<?php
/**
* Add Metaboxes
*/


//MetaBoxes For Test Cases
add_action('admin_init', 'add_test_cases_metaboxes');

function add_test_cases_metaboxes(){    
    
    add_meta_box("select_test_suites_metabox", "Select Test Suite", 'test_case_test_suites_metabox_html', "test-case", "normal", "high");
    add_meta_box("test_execution_metabox", "Test Execution", 'test_case_test_execution_metabox_html', "test-case", "normal", "high");
    add_meta_box("test_data_metabox", "Test Data", 'test_case_test_data_metabox_html', "test-case", "normal", "high");
    add_meta_box("test_steps_metabox", "Test Steps 2", 'test_case_test_step2_metabox_html', "test-case", "normal", "high");
    add_meta_box("choose_initiating_message_metabox", "Choose Initiating Message", 'test_case_choose_initiating_message_metabox_html','test-case',"normal","high");
    add_meta_box("choose_roles_metabox", "Choose Roles", 'test_case_choose_roles_metabox_html','test-case',"normal","high");
    add_meta_box("choose_level_metabox", "Choose Conformance Level", 'test_case_conformance_level_metabox_html','test-case',"normal","high");
}

function test_case_test_suites_metabox_html(){
    global $post;
    
    
    $current_test_suite = _get_current_test_suite($post->ID);
    
    $testsuites = get_posts( array( 'post_type' => 'test-suite', 'posts_per_page' => -1) );
    //if(!$current_test_suite)
//        $current_test_suite = array(0);
    //foreach($current_test_suite as $key => $test_suite_id){
//        if($test_suite_id === '' || $test_suite_id === null)
//            continue;
    ?>
    <div class="elem-ts"> <div class="elem-ts">
        <select name="test_suite" class="testsuite_values">
            <option value="">Select Test Suites</option>
            <?php
            foreach($testsuites as $testsuite){
                 ?>
                 <option <?php if ($testsuite->ID == $current_test_suite) { echo 'selected="selected"'; }; ?> value="<?php echo $testsuite->ID; ?>" style="margin-right: 5px; margin-bottom: 5px;"><?php echo $testsuite->post_title; ?> </option>
                <?php
            }
            ?>
        </select>
        <!--<div class="button remove_ts left">Remove Test Suite</div>-->
    </div> </div>    
    <?php 
//} 
    
        ?>
        <div class="copy-correct-ts">    
        </div>
        
<!--        <a class="add_new_ts button right">Add</a>-->
        
        <div class="clear"></div>
        
        <script type="text/javascript">
        jQuery(document).ready(function() {
            jQuery(".add_new_ts").click(function(data) {
                jQuery('.copy-correct-ts').append(jQuery('.elem-ts').html());
            });
            jQuery(".remove_ts").click( function() {
                jQuery(this).parents('.elem-ts').remove();
            });
            //Select Test Suite, Show Roles & Initiating Message
            
            if (jQuery(".testsuite_values").val() ==''){
                jQuery("#choose_roles_metabox .inside #tester_role").html('<b>Tester Role</b><br />First choose a test suite');
                jQuery("#choose_roles_metabox .inside #harness_role").html('<b>Harness Role</b><br />First choose a test suite');
                jQuery("#choose_initiating_message_metabox .inside").html('First choose a test suite');
                jQuery("#choose_level_metabox .inside").html('First choose a test suite');
            }
            
            var checkElem_tester = jQuery('#choose_tester_role').size() > 0 ? 1 : 0;
            var checkElem_harness = jQuery('#checkharness').size() > 0 ? 1 : 0;
            var checkElem_initiator = jQuery('#checkinitiator').size() > 0 ? 1 : 0;
            var checkElem2 = jQuery('#checkinitmsg').size() > 0 ? 1 : 0;
            var checkElem3 = jQuery('#checkconflvl').size() > 0 ? 1 : 0;
            
            jQuery(document).on('change', '.testsuite_values', function(){
                
                checkElem_tester = jQuery('#choose_tester_role').size() > 0 ? 1 : 0;
                checkElem_harness = jQuery('#checkharness').size() > 0 ? 1 : 0;
                checkElem_initiator = jQuery('#checkinitiator').size() > 0 ? 1 : 0;
                checkElem2 = jQuery('#checkinitmsg').size() > 0 ? 1 : 0;
                checkElem3 = jQuery('#checkconflvl').size() > 0 ? 1 : 0;
                
                var testsuite_val = jQuery(this).val();
                
                //Tester Role
                var fields = {
                        suite_id: testsuite_val,                        
                        post_id: '<?php echo $post->ID?>',
                        '_wpnonce': '<?php echo wp_create_nonce('get_suite_roles')?>'
                    }
                
                jQuery.ajax({
                    url: '',
                    data: fields,
                    type:'POST',
                    success: function(data){
                        jQuery("#choose_roles_metabox .inside #tester_role").html('<label for="choose_tester_role"><b>Tester Role</b></label><br /><select name="choose_tester_role" id="choose_tester_role">' + data + '</select>');
                        jQuery("#choose_roles_metabox .inside #harness_role").html('<label for="choose_harness_role"><b>Harness Role</b></label><br /><select name="choose_harness_role" id="choose_harness_role">' + data + '</select>');
                        
                    },
                    error: function(data){
                    }
                });
                
                //Level Code
                var fields3 = {
                        suite_id: testsuite_val,                        
                        post_id: '<?php echo $post->ID?>',
                        '_wpnonce': '<?php echo wp_create_nonce('get_init_message')?>'
                    }
                jQuery.ajax({
                    url: '',
                    data: fields3,
                    type:'POST',
                    success: function(data){                        
                        jQuery("#choose_initiating_message_metabox .inside").html(data);
                    },
                    
                    error: function(data){
                    }
                });
                
                //Level Code
                var fields3 = {
                        suite_id: testsuite_val,                        
                        post_id: '<?php echo $post->ID?>',
                        '_wpnonce': '<?php echo wp_create_nonce('get_suite_conf_level')?>'
                    }
                jQuery.ajax({
                    url: '',
                    data: fields3,
                    type:'POST',
                    success: function(data){                        
                        jQuery("#choose_level_metabox .inside").html(data);
                    },
                    
                    error: function(data){
                    }
                });
            });
                    
        });
        </script>
<?php    
}

function test_case_choose_initiating_message_metabox_html(){
    global $post;
    $post_backup = $post;
    
    $current_test_suite = _get_current_test_suite($post->ID);
    
    $current_init_message = get_post_meta($post->ID, 'choose_init_messages', true);
    
    $all_init_messages = array();
    echo '<select name="choose_init_messages" id="checkinitmsg">';
    echo '<option value="">Choose Initiating Message</option>';
//    foreach ($current_test_suite as $test_suite){
        $metas_result = get_post_meta($current_test_suite, 'init_message', true);
        $metas_array = explode(',', $metas_result);
        foreach($metas_array as $ts_init_message){
            if(!in_array($ts_init_message,$all_init_messages)){
                if($ts_init_message == $current_init_message){
                    $selected_init_message = 'selected = "selected"';
                }
                else {
                    $selected_init_message = '';
                }
            echo '<option value="'.$ts_init_message.'" '.$selected_init_message.'>'.$ts_init_message.'</option>';
            array_push($all_init_messages,$ts_init_message);
            }
        }    
//    }
    echo '</select>';
    $post = $post_backup;
}

function test_case_conformance_level_metabox_html(){
    global $post;
    $post_backup = $post;
    
    $current_test_suite = _get_current_test_suite($post->ID);
    
    $current_conformance_level = get_post_meta($post->ID, 'conformance_level', true);
    $all_conf_level = array();
    echo '<select name="conformance_level" id="checkconflvl">';
    echo '<option value="">Choose Conformance Level</option>';
//    foreach ($current_test_suite as $test_suite){
        $metas_array = get_post_meta($current_test_suite, 'lvl_code', true);
        //$metas_array = explode(',', $metas_result);
        foreach($metas_array as $ts_lvl){
            if(!in_array($ts_lvl,$all_conf_level)){
                if($ts_lvl == $current_conformance_level){
                    $selected_conf_lvl = 'selected = "selected"';
                }
                else {
                    $selected_conf_lvl = '';
                }
            echo '<option value="'.$ts_lvl.'" '.$selected_conf_lvl.'>'.$ts_lvl.'</option>';
            array_push($all_conf_level,$ts_lvl);
            }
        }    
//    }
    echo '</select>';
    $post = $post_backup;
}

function test_case_choose_roles_metabox_html(){
    global $post;
    $post_backup = $post;
    
    $current_test_suite = _get_current_test_suite($post->ID);
    
    //Current Roles Selected
    $current_tester_role = get_post_meta($post->ID, 'choose_tester_role', true);
    $current_harness_role = get_post_meta($post->ID, 'choose_harness_role', true);
    
    //Roles Test Suite Associated
    //Tester Role
    echo '<div id="tester_role"><label for="choose_tester_role"><b>Tester Role</b></label><br />';
    echo '<select name="choose_tester_role" id="choose_tester_role">';
    echo '<option value="">Choose Tester Role</option>';
//    foreach ($current_test_suite as $test_suite){
        $suiteRoles = getTestSuiteRoles($current_test_suite);
        
        foreach($suiteRoles as $role){            
            if($role['name'] == $current_tester_role){
                $selected_tester_role = 'selected = "selected"';
            }
            else {
                $selected_tester_role = '';
            }
            echo '<option value="'.$role['name'].'" '.$selected_tester_role.' >'.$role['name'].'</option>';                        
        }    
//    }
    echo '</select> </div> <br />';
    
    //Harness Role
    echo '<div id="harness_role"><label for="choose_harness_role"><b>Harness Role</b></label><br />';
    echo '<select name="choose_harness_role" id="choose_harness_role">';
    echo '<option value="">Choose Harness Role</option>';
//    foreach ($current_test_suite as $test_suite){
        
        
        foreach($suiteRoles as $role){            
            if($role['name'] == $current_harness_role){
                $selected_tester_role = 'selected = "selected"';
            }
            else {
                $selected_tester_role = '';
            }
            echo '<option value="'.$role['name'].'" '.$selected_tester_role.' class="'.$test_suite.'">'.$role['name'].'</option>';                        
        }   
//    }
    echo '</select> </div> <br />';
    
    $current_initiator = get_post_meta($post->ID, 'choose_initiator', true);
    //Initiator
    echo '<div id="initiator_role"><label for="choose_initiator"><b>Initiator</b></label><br />';
    echo '<label><input type="radio" value="tester" name="choose_initiator" ' . ($current_initiator == 'tester' ? 'checked="checked"' : '') . ' /> Tester</label>';
    echo '<label style="margin-left: 5px;"><input type="radio" value="harness" '  . ($current_initiator == 'harness' ? 'checked="checked"' : '') .  ' name="choose_initiator" /> Harness</label>';
    echo '</div>';
    
    $post = $post_backup;
}

function test_case_test_execution_metabox_html(){
    global $post;
    $post_backup = $post;
    $test_url = get_post_meta($post->ID, 'test_url', true);
    $protocol_binding2 = get_post_meta($post->ID, 'protocol_binding2', true);
    $current_property_name_exec= get_post_meta($post->ID, 'property_name_exec', true);
    $current_property_value_exec= get_post_meta($post->ID, 'property_value_exec', true);
    echo '<input type="hidden" name="custom_test_execution" value="', wp_create_nonce(basename(__FILE__)), '" />';
    ?>
    <label for="test_url"><b>Test endpoint URL:</b></label> <br />
    <input type="text" name="test_url" value="<?php echo $test_url; ?>" size="30" class="mf_text"/> 
    <br />
    <label for="protocol_binding2"><b>Protocol Binding:</b></label> <br />
    <input type="text" name="protocol_binding2" value="<?php echo $protocol_binding2; ?>" size="30" class="mf_text"/> 
    <br />    <br />
                
    <?php
    foreach($current_property_name_exec as $key => $property_name_exec){
        foreach($current_property_value_exec as $key2 => $property_value_exec){
            if( $key == $key2){ ?>
            <div class="elem-te"> <div class="elem-te"> 
                <label for="property_name_exec"><b>Property Name:</b></label> <br />
                <input type="text" name="property_name_exec[]" value="<?php echo $property_name_exec; ?>" size="30" class="mf_text"/> 
                <br />
                
                <label for="property_value_exec"><b>Property Value:</b></label> <br />
                <input type="text" name="property_value_exec[]" value="<?php echo $property_value_exec; ?>" size="30" class="mf_text"/> 
                <br />
                
                <div class="button remove_te left">Remove</div>
                <br clear="all" /> <br />
                </div> </div>
      <?php }
        }
    }
    
    if (empty($current_property_name_exec)){ ?>
        <div class="elem-te">
            <label for="property_name_exec"><b>Property Name:</b></label> <br />
            <input type="text" name="property_name_exec[]" size="30" class="mf_text"/> 
            <br />
            
            <label for="property_value_exec"><b>Property Value:</b></label> <br />
            <input type="text" name="property_value_exec[]" size="30" class="mf_text"/> 
            <br /> <br />
        </div>
    <?php
    $post = $post_backup;
    } ?>
    
    <div class="copy-correct-te">    
    </div>
    
    <a class="add_new_te button right">Add</a>
    
    <div class="clear"></div>

    <script type="text/javascript">
    jQuery(document).ready(function() {
        jQuery(".add_new_te").click(function(data) {
            jQuery('.copy-correct-te').append(jQuery('.elem-te').html());
            //jQuery('.copy-correct input, .copy-correct select').val('');
        });
        jQuery(".remove_te").live('click', function() {
            jQuery(this).parents('.elem-te').remove();
        });
    });
    </script>    
<?php    
}

function test_case_test_data_metabox_html(){
    global $post;
    $post_backup = $post;
    $current_property_name_data= get_post_meta($post->ID, 'property_name_data', true);
    $current_property_value_data= get_post_meta($post->ID, 'property_value_data', true);
    echo '<input type="hidden" name="custom_test_data" value="', wp_create_nonce(basename(__FILE__)), '" />';
    ?>
                
    <?php
    foreach($current_property_name_data as $key => $property_name_data){
        foreach($current_property_value_data as $key2 => $property_value_data){
            if( $key == $key2){ ?>
            <div class="elem-td"> <div class="elem-td"> 
                <label for="property_name_data"><b>Property Name:</b></label> <br />
                <input type="text" name="property_name_data[]" value="<?php echo $property_name_data; ?>" size="30" class="mf_text"/> 
                <br />
                
                <label for="property_value_data"><b>Property Value:</b></label> <br />
                <input type="text" name="property_value_data[]" value="<?php echo $property_value_data; ?>" size="30" class="mf_text"/> 
                <br />
                
                <div class="button remove_td left">Remove</div>
                <br clear="all" /> <br />
                </div> </div>
      <?php }
        }
    }
    
    if (empty($current_property_name_data)){ ?>
        <div class="elem-td">
            <label for="property_name_data"><b>Property Name:</b></label> <br />
            <input type="text" name="property_name_data[]" size="30" class="mf_text"/> 
            <br />
            
            <label for="property_value_data"><b>Property Value:</b></label> <br />
            <input type="text" name="property_value_data[]" size="30" class="mf_text"/> 
            <br /> <br />
        </div>
    <?php
    $post = $post_backup;
    } ?>
    
    <div class="copy-correct-td">    
    </div>
    
    <a class="add_new_td button right">Add</a>
    
    <div class="clear"></div>

    <script type="text/javascript">
    jQuery(document).ready(function() {
        jQuery(".add_new_td").click(function(data) {
            jQuery('.copy-correct-td').append(jQuery('.elem-td').html());
            //jQuery('.copy-correct input, .copy-correct select').val('');
        });
        jQuery(".remove_td").live('click', function() {
            jQuery(this).parents('.elem-td').remove();
        });
    });
    </script>    
<?php    
}

function test_case_test_step2_metabox_html(){
    global $post;
    $post_backup = $post;
    $current_step_action= get_post_meta($post->ID, 'step_action', true);
    $current_step_expected= get_post_meta($post->ID, 'step_expected', true);
    echo '<input type="hidden" name="custom_test_data" value="', wp_create_nonce(basename(__FILE__)), '" />';
    ?>
                
    <?php
    foreach($current_step_action as $key => $step_action){
        foreach($current_step_expected as $key2 => $step_expected){
            if( $key == $key2){ ?>
            <div class="elem-step"> <div class="elem-step"> 
                <label for="step_action"><b>Step <?php echo ($key+1); ?>. Action:</b></label> <br />
                <input type="text" name="step_action[]" value="<?php echo $step_action; ?>" size="30" class="mf_text"/> 
                <br />
                
                <label for="step_expected"><b>Step <?php echo($key+1); ?>. Expected Result:</b></label> <br />
                <input type="text" name="step_expected[]" value="<?php echo $step_expected; ?>" size="30" class="mf_text"/> 
                <br />
                
                <div class="button remove_step left">Remove</div>
                <br clear="all" /> <br />
                </div> </div>
      <?php }
        }
    }
    
    if (empty($current_step_action)){ ?>
        <div class="elem-step">
            <label for="step_action"><b>Step Action:</b></label> <br />
            <input type="text" name="step_action[]" size="30" class="mf_text"/> 
            <br />
            
            <label for="step_expected"><b>Step Expected Result:</b></label> <br />
            <input type="text" name="step_expected[]" size="30" class="mf_text"/> 
            <br /> <br />
        </div>
    <?php
    $post = $post_backup;
    } ?>
    
    <div class="copy-correct-step">    
    </div>
    
    <a class="add_new_step button right">Add</a>
    
    <div class="clear"></div>

    <script type="text/javascript">
    jQuery(document).ready(function() {
        jQuery(".add_new_step").click(function(data) {
            jQuery('.copy-correct-step').append(jQuery('.elem-step').html());
            //jQuery('.copy-correct input, .copy-correct select').val('');
        });
        jQuery(".remove_step").live('click', function() {
            jQuery(this).parents('.elem-step').remove();
        });
    });
    </script>    
<?php    
}

//Save Test Cases
add_action('save_post', 'save_test_case_on_admin');
function save_test_case_on_admin($post_id)
{
    global $post;
    
    // check autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }
    // check permissions
    if (!current_user_can('edit_post', $post_id)) {
        return $post_id;
    }
    
    //Check Post Type
    if($post->post_type != 'test-case')
    {
        return $post_id;
    }
    
    if( wp_is_post_revision( $post_id ) )
    {
        return $post_id;
    }
    
    //Save Selected Test Suites
    $test_suite = isset($_POST['test_suite']) ? $_POST['test_suite'] : null;    
    
    update_post_meta($post_id, 'test_suite', $test_suite);    
    
    //Save Conformance level
    update_post_meta($post_id, 'conformance_level', $_POST['conformance_level']);
    
    //Save Role
    $tester_role = $_POST['choose_tester_role'];
    update_post_meta($post_id, 'choose_tester_role',$tester_role);
    $harness_role = $_POST['choose_harness_role'];
    update_post_meta($post_id, 'choose_harness_role',$harness_role);
    $initiator = $_POST['choose_initiator'];
    update_post_meta($post_id, 'choose_initiator',$initiator);
    
    //Save Initial Message
    $message_type = $_POST['choose_init_messages'] ;
    update_post_meta($post_id, 'choose_init_messages',$message_type);
    
    //Save Test Step2
    $step_expected = $_POST['step_expected']; 
    update_post_meta($post_id, 'step_expected', $step_expected);
    $step_action = $_POST['step_action']; 
    update_post_meta($post_id, 'step_action', $step_action);
    
    //Saev Test Data
    $property_name_data = $_POST['property_name_data']; 
    update_post_meta($post_id, 'property_name_data', $property_name_data);
    $property_value_data = $_POST['property_value_data']; 
    update_post_meta($post_id, 'property_value_data', $property_value_data);
    
    //Save Test Execution
    $test_url = $_POST['test_url']; 
    update_post_meta($post_id, 'test_url', $test_url);
    $protocol_binding2 = $_POST['protocol_binding2']; 
    update_post_meta($post_id, 'protocol_binding2', $protocol_binding2);
    
    $property_name_exec = $_POST['property_name_exec']; 
    update_post_meta($post_id, 'property_name_exec', $property_name_exec);
    $property_value_exec = $_POST['property_value_exec']; 
    update_post_meta($post_id, 'property_value_exec', $property_value_exec);
    
    
    return $post_id;
}