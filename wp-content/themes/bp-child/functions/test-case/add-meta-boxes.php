<?php
/**
* Add Metaboxes
*/


//MetaBoxes For Test Cases
add_action('admin_init', 'add_test_cases_metaboxes');

function add_test_cases_metaboxes(){        
    add_meta_box("select_test_suites_metabox", "Select Test Suite", 'test_case_test_suites_metabox_html', "test-case", "normal", "high");
    add_meta_box("choose_initiating_message_metabox", "Choose Initiating Message", 'test_case_choose_initiating_message_metabox_html','test-case',"normal","high");
    add_meta_box("choose_roles_metabox", "Choose Roles", 'test_case_choose_roles_metabox_html','test-case',"normal","high");
    add_meta_box("choose_level_metabox", "Choose Conformance Level", 'test_case_conformance_level_metabox_html','test-case',"normal","high");
    add_meta_box("test_execution_metabox", "Test Execution", 'test_case_test_execution_metabox_html', "test-case", "normal", "high");
    add_meta_box("test_data_metabox", "Test Data", 'test_case_test_data_metabox_html', "test-case", "normal", "high");
    add_meta_box("test_steps_metabox", "Test Steps 2", 'test_case_test_step2_metabox_html', "test-case", "normal", "high");    
    add_meta_box("execution_sequence_number", "Execution Sequence Number", 'execution_sequence_number_metabox_html','test-case',"side","high");
}

function execution_sequence_number_metabox_html()
{
    global $post;
    $n = cp_get_post_meta($post->ID, 'sequence_number', true);
    
    if(!$n)
        $n = get_next_sequence_number(cp_get_post_meta($post->ID, 'test_suite', true))    
    ?>
    <p><input type="text" name="sequence_number" id="sequence_number" value="<?php echo $n ?>" /></p>
    <?php
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
            <?php
            foreach($testsuites as $testsuite){
                 ?>
                 <label><input type="checkbox" name="suite_id[]" class="testsuite_values" <?php if (in_array($testsuite->ID, $current_test_suite)) { echo 'checked="checked"'; }; ?> value="<?php echo $testsuite->ID; ?>" /><?php echo $testsuite->post_title; ?> </label><br />
                <?php
            }
            ?>
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
            
            if (jQuery(".testsuite_values:checked").length == 0){
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
                if (jQuery(".testsuite_values:checked").length == 0){
                    jQuery("#choose_roles_metabox .inside #tester_role").html('<b>Tester Role</b><br />First choose a test suite');
                    jQuery("#choose_roles_metabox .inside #harness_role").html('<b>Harness Role</b><br />First choose a test suite');
                    jQuery("#choose_initiating_message_metabox .inside").html('First choose a test suite');
                    jQuery("#choose_level_metabox .inside").html('First choose a test suite');
                    return false;
                }
                checkElem_tester = jQuery('#choose_tester_role').size() > 0 ? 1 : 0;
                checkElem_harness = jQuery('#checkharness').size() > 0 ? 1 : 0;
                checkElem_initiator = jQuery('#checkinitiator').size() > 0 ? 1 : 0;
                checkElem2 = jQuery('#checkinitmsg').size() > 0 ? 1 : 0;
                checkElem3 = jQuery('#checkconflvl').size() > 0 ? 1 : 0;
                
                var testsuite_val = jQuery(this).val();
                
                jQuery.ajax({
                    url: '',
                    data: jQuery('.testsuite_values').serialize() + '&post_id=<?php echo $post->ID?>&_wpnonce=<?php echo wp_create_nonce('get_suite_roles')?>',
                    type:'POST',
                    success: function(data){
                        jQuery("#choose_roles_metabox .inside #tester_role").html('<label for="choose_tester_role"><b>Tester Role</b></label><br /><select name="choose_tester_role" id="choose_tester_role">' + data + '</select>');
                        jQuery("#choose_roles_metabox .inside #harness_role").html('<label for="choose_harness_role"><b>Harness Role</b></label><br /><select name="choose_harness_role" id="choose_harness_role">' + data + '</select>');
                        
                    },
                    error: function(data){
                    }
                });
                
                //Level Code
                jQuery.ajax({
                    url: '',
                    data: jQuery('.testsuite_values').serialize() + '&post_id=<?php echo $post->ID?>&_wpnonce=<?php echo wp_create_nonce('get_init_message')?>',
                    type:'POST',
                    success: function(data){                        
                        jQuery("#choose_initiating_message_metabox .inside").html(data);
                    },
                    
                    error: function(data){
                    }
                });
                
                jQuery.ajax({
                    url: '',
                    data: jQuery('.testsuite_values').serialize() + '&post_id=<?php echo $post->ID?>&_wpnonce=<?php echo wp_create_nonce('get_suite_conf_level')?>',
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
    
    $testCase = new TestCase($post->ID);
    $testCase->testSuite = _get_current_test_suite($post->ID);
    $allInitMessages = $testCase->getAvailableInitMessages();
    
    $current_init_message = cp_get_post_meta($post->ID, 'choose_init_messages', true);
    
    $all_init_messages = array();
    echo '<select name="choose_init_messages" id="checkinitmsg">';
    echo '<option value="">Choose Initiating Message</option>';
    foreach($allInitMessages as $initMessage){
        echo '<option value="'. $initMessage .'" '. ($current_init_message == $initMessage) .'>'.$initMessage.'</option>';            
    }    
    echo '</select>';
    
}

function test_case_conformance_level_metabox_html(){
    global $post;
    $post_backup = $post;
    
    $current_test_suite = _get_current_test_suite($post->ID);
    
    $current_conformance_level = cp_get_post_meta($post->ID, 'conformance_level');
    
    foreach ($current_test_suite as $test_suite){
        echo "<b>" . get_the_title($test_suite) . "</b><br />";
        echo '<select name="conformance_level" id="checkconflvl">';
        echo '<option value="">Choose Conformance Level</option>';
        
        $suiteObj = new TestSuite($test_suite);
        $levels = $suiteObj->loadConformanceLevel();
        
        foreach($levels as $row){
            echo '<option value="'. $row['code'] .'" '.(in_array($row['code'], $current_conformance_level) ? 'selected="selected"' : '').'>'.$row['code'].'</option>';        
        }    
        echo '</select><br /><br />';
    }
    
}

function test_case_choose_roles_metabox_html(){
    global $post;
    
    $case = new TestCase($post->ID);
    
    $current_test_suite = _get_current_test_suite($post->ID);
    $case->testSuite = $current_test_suite;
    
    $allRoles = $case->getAvailableRoles();
    
    //Current Roles Selected
    $current_tester_role = cp_get_post_meta($post->ID, 'choose_tester_role', true);
    $current_harness_role = cp_get_post_meta($post->ID, 'choose_harness_role', true);
    
    //Roles Test Suite Associated
    //Tester Role
    echo '<div id="tester_role"><label for="choose_tester_role"><b>Tester Role</b></label><br />';
    echo '<select name="choose_tester_role" id="choose_tester_role">';
    echo '<option value="">Choose Tester Role</option>';
    foreach($allRoles as $role){                        
        echo '<option value="'.$role['name'].'" '. ($role == $current_tester_role ? 'selected="selected"' : '') .' >'.$role['name'].'</option>';                        
    }    
    echo '</select> </div> <br />';
    
    //Harness Role
    echo '<div id="harness_role"><label for="choose_harness_role"><b>Harness Role</b></label><br />';
    echo '<select name="choose_harness_role" id="choose_harness_role">';
    echo '<option value="">Choose Harness Role</option>';
    foreach($allRoles as $role){                        
        echo '<option value="'.$role['name'].'" '. ($role == $current_harness_role ? 'selected="selected"' : '') .' >'.$role['name'].'</option>';                        
    } 
    echo '</select> </div> <br />';
    
    $current_initiator = cp_get_post_meta($post->ID, 'choose_initiator', true);
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
    $test_url = cp_get_post_meta($post->ID, 'test_url', true);
    $protocol_binding2 = cp_get_post_meta($post->ID, 'protocol_binding2', true);
    $current_property_name_exec= cp_get_post_meta($post->ID, 'property_name_exec', true);
    $current_property_value_exec= cp_get_post_meta($post->ID, 'property_value_exec', true);
    echo '<input type="hidden" name="custom_test_execution" value="', wp_create_nonce(basename(__FILE__)), '" />';
    ?>
    <label for="test_url"><b>Test endpoint URL:</b></label> <br />
    <input type="text" name="test_url" value="<?php echo $test_url; ?>" size="30" class="mf_text"/> 
    <br />
    <label for="protocol_binding2"><b>Protocol Binding:</b></label> <br />
    <input type="text" name="protocol_binding2" value="<?php echo $protocol_binding2; ?>" size="30" class="mf_text"/> 
    <br />    <br />
                
    <?php
    if(!$current_property_name_exec)
        $current_property_name_exec = array();
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
    $current_property_name_data= cp_get_post_meta($post->ID, 'property_name_data', true);
    $current_property_value_data= cp_get_post_meta($post->ID, 'property_value_data', true);
    echo '<input type="hidden" name="custom_test_data" value="', wp_create_nonce(basename(__FILE__)), '" />';
    if(!$current_property_name_data)
        $current_property_name_data = array();
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
    $current_step_action= cp_get_post_meta($post->ID, 'step_action', true);
    $current_step_expected= cp_get_post_meta($post->ID, 'step_expected', true);
    echo '<input type="hidden" name="custom_test_data" value="', wp_create_nonce(basename(__FILE__)), '" />';
    
    if(!$current_step_action)
        $current_step_action = array();
    
    foreach($current_step_action as $key => $step_action){
        foreach($current_step_expected as $key2 => $step_expected){
            if( $key == $key2){ ?>
            <div class="elem-step"> <div class="elem-step"> 
                <label for="step_action"><b>Step <?php echo ($key+1); ?>. Action:</b></label> <br />
                <textarea name="step_action[]" class="mf_text"><?php echo $step_action; ?></textarea> 
                <br />
                
                <label for="step_expected"><b>Step <?php echo($key+1); ?>. Expected Result:</b></label> <br />
                <textarea name="step_expected[]" class="mf_text"><?php echo $step_expected; ?></textarea> 
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
            <textarea name="step_action[]" class="mf_text"></textarea>
            <br />
            
            <label for="step_expected"><b>Step Expected Result:</b></label> <br />
            <textarea name="step_expected[]" class="mf_text"></textarea>
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
    global $post, $wpdb;
    
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
    
    $versions = array();
    if($_POST['version_major'] != '')
        $versions[] = $_POST['version_major'];
    if($_POST['version_minor'] != '')
        $versions[] = $_POST['version_minor'];
    if($_POST['version_patch'] != '')
        $versions[] = $_POST['version_patch'];
    
    $version = " v" . implode(".", $versions);
    
    $post_title = $_POST['test_case_id'] ." v" . $version;
    
    $post_name = wp_unique_post_slug(sanitize_title($post_title), $post->ID, $post->post_status, $post->post_type, $post->post_parent);
    
    $guid = get_sample_permalink($post->ID, $post_title, $post_name);
    $wpdb->update($wpdb->posts, array('post_title' => $post_title, 'post_name' => $guid[1], 'guid' => str_replace('%pagename%', $guid[1], $guid[0])), array('ID' => $post->ID));
    
    //Save Selected Test Suites
    $test_suite = isset($_POST['test_suite']) ? $_POST['test_suite'] : null;    
    
    cp_update_post_meta($post_id, 'test_suite', $test_suite);    
    
    //Save Conformance level
    cp_update_post_meta($post_id, 'conformance_level', $_POST['conformance_level']);
    
    //Save Role
    $tester_role = $_POST['choose_tester_role'];
    cp_update_post_meta($post_id, 'choose_tester_role',$tester_role);
    $harness_role = $_POST['choose_harness_role'];
    cp_update_post_meta($post_id, 'choose_harness_role',$harness_role);
    $initiator = $_POST['choose_initiator'];
    cp_update_post_meta($post_id, 'choose_initiator',$initiator);
    
    //Save Initial Message
    $message_type = $_POST['choose_init_messages'] ;
    cp_update_post_meta($post_id, 'choose_init_messages',$message_type);
    
    //Save Test Step2
    $step_expected = $_POST['step_expected']; 
    cp_update_post_meta($post_id, 'step_expected', $step_expected);
    $step_action = $_POST['step_action']; 
    cp_update_post_meta($post_id, 'step_action', $step_action);
    
    //Saev Test Data
    $property_name_data = $_POST['property_name_data']; 
    cp_update_post_meta($post_id, 'property_name_data', $property_name_data);
    $property_value_data = $_POST['property_value_data']; 
    cp_update_post_meta($post_id, 'property_value_data', $property_value_data);
    
    //Save Test Execution
    $test_url = $_POST['test_url']; 
    cp_update_post_meta($post_id, 'test_url', $test_url);
    $protocol_binding2 = $_POST['protocol_binding2']; 
    cp_update_post_meta($post_id, 'protocol_binding2', $protocol_binding2);
    
    $property_name_exec = $_POST['property_name_exec']; 
    cp_update_post_meta($post_id, 'property_name_exec', $property_name_exec);
    $property_value_exec = $_POST['property_value_exec']; 
    cp_update_post_meta($post_id, 'property_value_exec', $property_value_exec);
    
    cp_update_post_meta($post_id, 'sequence_number', $_POST['sequence_number']);
    
    return $post_id;
}