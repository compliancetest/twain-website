<?php
/**
* Add Custom Metaboxes to Test Suite Post Type
*/


//Add Metaboxes
add_action('admin_init', 'add_metaboxes_for_test_suites');
function add_metaboxes_for_test_suites()
{
    /* Metabox Choose Test Cases*/
    add_meta_box("test_cases_metabox", "Test Cases Associated", 'test_suite_test_cases_metabox_html', "test-suite", "normal", "high");
    /* Metabox Declare Initiating Message*/
    add_meta_box("initiating_message_metabox", "Initiating Messages", 'test_suite_initiating_message_metabox_html', "test-suite", "normal", "high");
    /* Metabox Declare Roles*/
    add_meta_box("roles_ts_metabox", "Roles", 'test_suite_roles_metabox_html', "test-suite", "normal", "high");
    // Metabox Associate Communities
    add_meta_box("community_metabox", "Choose Community", 'test_suite_community_metabox_html', "test-suite", "normal", "high");    
    //Add Related Test Suites
    add_meta_box("ts_metabox", "Related Test Suites ", 'test_suites_related_test_suites_metabox_html', "test-suite", "normal", "high");
    //Add Specification Documents
    add_meta_box("specdoc_metabox", "Specification Documents", 'test_suites_spec_doc_metabox_html', "test-suite", "normal", "high");
    //Add Conformance Level
    add_meta_box("conf_levels_metabox", "Conformance Levels", 'test_suites_conformance_level_metabox_html', "test-suite", "normal", "high");
    //Add Subscription Price
    add_meta_box("subscription_price_metabox", "Subscription Price", 'subscription_price_metabox_html', "test-suite", "side", "core");
    
}

function subscription_price_metabox_html()
{
    global $post;
    
    $monthly_price = get_post_meta($post->ID, 'monthly_subscription_price', true);
    
    ?>
    <div>
        <label><b>Monthly Subscription Price:</b></label>
        <input type="text" name="monthly_subscription_price" value="<?php echo $monthly_price?>" />
    </div>
    <?php
}

//Add Conformance Level
function test_suites_conformance_level_metabox_html(){
    global $post;
    
    $current_lvl_code = get_post_meta($post->ID, 'lvl_code', true);
    $current_lvl_desc= get_post_meta($post->ID, 'lvl_desc', true);
        
    ?>
    <div id="suites3">
        <div class="elements3">
        <?php
        foreach($current_lvl_code as $key => $lvl_code){
          foreach ($current_lvl_desc as $key2 => $lvl_desc){
                if ($key == $key2) { ?>
                <div class="elem3"><div class="elem3">
                    <label for="lvl_code"><b>Conformance Level Code:</b></label> <br />
                    <input type="text" name="lvl_code[]" value="<?php echo $lvl_code; ?>" size="30" /> 

                    <br clear="all" />
                    <label for="lvl_desc"><b>Conformance Level Description:</b></label> <br />
                    <input type="text" name="lvl_desc[]" value="<?php echo $lvl_desc; ?>" size="30" class="mf_text"/> 
                    
                    <br clear="all" />
                    
                    <div class="button remove_lvl left">Remove</div>
                    <br /> <br />
                </div> </div>
                
                <?php }
                    }
                  }

             if (empty($current_lvl_code)){
                 ?>
                 <div class="elem3"> <div class="elem3">
                    <label for="lvl_code"><b>Conformance Level Code:</b></label> <br />
                    <input type="text" name="lvl_code[]" size="30" /> 
                    <br clear="all" />
                    
                    <label for="lvl_desc"><b>Conformance Level Description:</b></label> <br />
                    <input type="text" name="lvl_desc[]" size="30" class="mf_text"/> 
                    <br clear="all" />
                    
                    <div class="button remove_lvl left">Remove</div>
                    <br /> <br />
                </div> </div>
                 <?php
                 }
         ?>
    </div>

    <div class="copy-correct-lvl">

    </div>
    
    </div>
    <a class="add_new_lvl button right">Add</a>

    <div class="clear"></div>

    <script type="text/javascript">
    jQuery(document).ready(function() {        
        jQuery('.add_new_lvl').click(function() {
             jQuery('.copy-correct-lvl').append(jQuery('.elem3').html());
        });
        jQuery('.remove_lvl').live('click', function() {
            jQuery(this).parents('.elem3').remove();
        });

    });
    </script>    

    <?php
    
}

//Add Specification Documents
function test_suites_spec_doc_metabox_html(){
    
    global $post, $wpdb;
    
    //Get already uploaded
    $current_specs = $wpdb->get_results( "SELECT * FROM " . $wpdb->prefix . "ts_options_documents WHERE ts_id=" . $post->ID . " ORDER BY id");
    
    foreach($current_specs as $row){
        $doc_name = $row->doc_name;
        $doc_desc = $row->doc_desc;
        $doc_loc = $row->doc_loc_url;
        $doc_file_name = $row->doc_file_name;
        $doc_file_url = $row->doc_loc_url;
        ?>
        <div class="elem2">             
            <label for="doc_name"><b>Document Name: </b></label> <br />
            <input type="text" autocomplete="off" name="doc_name[]" value="<?php echo $doc_name ?>" size="30" class="mf_text"/> 
            <br clear="all" />
            <label for="doc_desc"><b>Document Description: </b></label> <br />
            <input type="text" autocomplete="off" name="doc_desc[]" value="<?php echo $doc_desc ?>" size="30" class="mf_text"/>
            <br clear="all" /> <br clear="all" />    
            <label for="doc_loc"><b>Document Location: </b></label> <br />
            <input type="text" autocomplete="off" name="doc_loc[]" value="<?php echo $doc_loc ?>" size="30" class="mf_text"/>                     
            <br />
            <a href="<?php echo $doc_file_url ?>" target="_blank">View Document</a>
            <br clear="all" /><a href="javascript: void(0)" class="button remove_doc left">Remove</a><br />
            
        </div>
        <?php        
    }
    
    ?>
    <input type="hidden" name="testsuiteid" value="<?php echo $post->ID;?>">

    <div class="copy-correct-docs">
    </div>
    

    <a class="add_new_doc button right">Add</a>

    <div class="clear"></div>
    
    <style type="text/css">
        
        .right { float:right; }
        .clear { clear: both; }        
    </style>

    <script type="text/javascript">
    jQuery(document).ready(function() {
        jQuery('.add_new_doc').click(function(data) {
            jQuery('.copy-correct-docs').append('<div class="elem2"><input type="hidden" name="doc_id[]" value="0" /> <label for="doc_name"><b>Document Name: </b></label> <br /><input type="text" autocomplete="off" name="doc_name[]" size="30" class="mf_text"/> <br clear="all" /><label for="doc_desc"><b>Document Description:</b></label> <br /> <input type="text" autocomplete="off" name="doc_desc[]" size="30" class="mf_text"/> <br clear="all" /> <label for="doc_loc"><em>Provide a </em><b>Document Location URL:</b></label> <br />    <input type="text" autocomplete="off" name="doc_loc[]" size="30" class="mf_text"/> <br clear="all" /> <label for="doc_upload"><em>OR </em><b>Upload a Document: </b></label> <br /> <input type="file" name="attachment_doc[]"> <br clear="all" /> <a href="javascript: void(0)" class="button remove_doc left">Remove</a> <br clear="all" /></div>');
        });
        
        jQuery('.remove_doc').live('click', function() {
            jQuery(this).parents('.elem2').remove();
        });
        jQuery('form#post').attr('enctype','multipart/form-data');
    });
    </script>    
    
    <?php
    $post = $post_backup;
}

//Related Test Suites Metabox
function test_suites_related_test_suites_metabox_html(){
    global $post;
    
    $current_ts = get_post_meta($post->ID, 'ts', true);
    if(!$current_ts)
        $current_ts = array(0);
        
    $current_desc = get_post_meta($post->ID, 'ts_desc', true);
    if(!$current_desc)
        $current_desc = array('');
    
    $testsuites = get_posts( array( 'post_type' => 'test-suite', 'posts_per_page' => -1, 'post__not_in' =>array($post->ID) ) );
    ?>
    <div id="rel_suite"> 
        <div class="elements">  
            <?php foreach($current_ts as $idx=>$cts) { ?>
            <div class="elem"> 
                <label for="ts"><b>Related Suites: </b></label> <br />
                <select name="ts[]">
                    <option value="">Choose Related Suite</option>
                    <?php
                    foreach($testsuites as $testcase)
                    {
                        ?>
                        <option <?php if ( $testcase->ID == $cts ) { echo 'selected="selected"'; } ?> value="<?php echo $testcase->ID; ?>" style="margin-right: 5px; margin-bottom: 5px;"><?php echo $testcase->post_title ?> </option>
                        <?php
                    }
                    ?>
                </select>
                <a href="javascript: void(0)" class="button remove left">Remove</a>
                <br clear="all" />
                
                <label for="ts_desc"><b>Description</b></label> <br />
                <input type="text" autocomplete="off" name="ts_desc[]" value="<?php echo $current_desc[$idx]; ?>" size="30" class="mf_text"/> 
                <br /><span class="description">Description for this Related Suite</span>
                <br /> <br />
            </div>
            <?php } ?>
         </div>
    </div>

    <div class="copy-correct">
        
    </div>
    
    <a class="add_new button right">Add</a>

    <div class="clear"></div>
    
    <script type="text/javascript">
    jQuery(document).ready(function() {
        jQuery(".add_new").click(function(data) {
            jQuery('.copy-correct').append(jQuery('.elem:eq(0)').html());
            //jQuery('.copy-correct input, .copy-correct select').val('');
        });
        jQuery(".remove").live('click', function() {
            jQuery(this).parents('.elem').remove();
        });
    });
    </script>    
    <?php
}

//Community Metabox
function test_suite_community_metabox_html(){
    
    global $wpdb, $post;
    
    $groups_result = $wpdb->get_results( "SELECT * FROM " . $wpdb->prefix . "bp_groups");
    $selected_group = $wpdb->get_row( "SELECT * FROM " . $wpdb->prefix . "bp_groups_testsuites WHERE ts_ids=" . $post->ID);    
    echo '<select name="group">';
    echo '<option value="">Choose Community</option>';
    foreach ( $groups_result as $group ) 
    {
        if ($selected_group && $selected_group->group_id == $group->id)
            $selected = 'selected="selected"';
        else 
            $selected ='';
        echo '<option value="'.$group->id.'" '.$selected.' >'.$group->name.'</option>';
    }
    echo '</select>';
    echo '<input type="hidden" value="'.$_GET['post'].'" name="postid"/>';
}

//Associated Test Cases
function test_suite_test_cases_metabox_html(){
    global $post, $wpdb;
    
    $get_cases = new WP_Query( array( 
        'post_type' => 'test-case', 
        'posts_per_page' => -1, 
        'meta_query' => array(
            array(
                'key' => 'test_suite', 
                'value' => $post->ID, 
                'compare' => '='
                )
            )
        ) 
    );
    $testcases = $get_cases->get_posts();
    if(count($testcases) > 0)
    {
        foreach($testcases as $case):            
                echo '<div class="the_test_case">';
                echo '<a href="'.get_permalink($case->ID).'" target="_blank"><b>'.get_the_title($case).'</b></a>';
                echo ' - ';
                echo '<a href="post.php?post='.$case->ID.'&action=edit" target="_blank" style="margin-right: 10px;">Edit</a>';
                echo '<a class="action_testcase" data-action="' . wp_create_nonce('hide_testcase') . '" data-id="'.$case->ID.'" style="margin-right: 10px; cursor:pointer; text-decoration: underline;">Remove from this suite</a>';
                echo '<a class="action_testcase" data-action="' . wp_create_nonce('trash_testcase') . '" data-id="'.$case->ID.'" style="cursor:pointer; text-decoration: underline;">Move to trash</a>';
                echo '</div>';    
        endforeach;
    }else{
        echo 'No test cases associated';
    }

    echo '<div class="clear"></div>';    
    echo '<a class="add_new_testcase button right" href="post-new.php?post_type=test-case&set_ts='.$_GET['post'].'" target="_blank">Add</a>';
    ?>
    <div class="clear"></div>
    <!-- Script Hide / Delete Test Case
    -->
    <script type="text/javascript">
    jQuery(document).ready(function() {
        jQuery(document).on('click', '.action_testcase', function(){
            var the_id = jQuery(this).attr('data-id');
            var the_action = jQuery(this).attr('data-action');
            var get_parent = jQuery(this);
            
            var field_tc = {
                suite_id : '<?php echo $post->ID?>',
                case_id : the_id,
                '_wpnonce' : the_action
                };
            
            jQuery.ajax({
                url: window.location.href,
                data: field_tc,
                type:'POST',
                success: function(data){
                    get_parent.parent().remove();
                },
                error: function(data){
                }
            });    
        });
    });
    </script>
    
    <?php
}

//Initiating Message
function test_suite_initiating_message_metabox_html(){
    global $post;
    $post_backup = $post;
    $current_initiating_messages = get_post_meta($post->ID, 'init_message', true);
    //echo '<input type="hidden" name="custom_initiating_message" value="', wp_create_nonce(basename(__FILE__)), '" />';
    ?>
    <textarea name="init_message" id="initiating_message_id" rows="4" cols="100"><?php echo $current_initiating_messages;?></textarea>
    <br /><span class="description">Type Initiating Messages (comma separated)</span>
<?php
    $post = $post_backup;    
}

//Roles Metabox
function test_suite_roles_metabox_html(){
    global $post;
    
    $roles = getTestSuiteRoles($post->ID);
    
?>
    <table id="roles-list" <?php if(count($roles) == 0) {?>style="display: none"<?php } ?>>
        <thead>
            <tr>
                <th>Role Name</th>
                <th>Role Description</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
        <?php foreach($roles as $row){ ?>
            <tr>            
                <td><input type="text" name="role_names[]" value="<?php echo $row['name']?>" size="50" autocomplete="off" /></td>            
                <td><input type="text" name="role_descs[]" value="<?php echo $row['desc']?>" size="80" autocomplete="off" /></td>            
                <td><a href="#" class="remove-role">Remove</a></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
    <input type="button" id="add-role" class="button" value="Add Role" />
    <script type="text/javascript">
        jQuery(document).ready(function(){
            jQuery('#add-role').click(function(){
                jQuery('#roles-list tbody').append('<tr>' +         
                    '<td><input type="text" name="role_names[]" value="" size="50" /></td>' +
                    '<td><input type="text" name="role_descs[]" value="" size="80" /></td>' +
                    '<td><a href="#" class="remove-role">Remove</a></td>' +
                '</tr>');
                jQuery('#roles-list').show();
            })
            jQuery('#roles-list').on('click', '.remove-role', function(){
                jQuery(this).parents('tr').remove();
                if(jQuery('#roles-list tbody').find('tr').length == 0)
                    jQuery('#roles-list').hide();
                return false;
            })
        })
    </script>
    <?php
}

//Save Test Cases
add_action('save_post', 'save_test_suite_on_admin');
function save_test_suite_on_admin($post_id)
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
    if($post->post_type != 'test-suite')
    {
        return $post_id;
    }
    if( wp_is_post_revision( $post_id ) )
    {
        return $post_id;
    }
    //Save Group
    $group_id = $_POST['group'];
    //Remove the assigned group
    $wpdb->delete($wpdb->prefix . "bp_groups_testsuites", array("ts_ids"=>$post_id));
    if(!$group_id)
    {        
        //Remove Meta data
        delete_post_meta($post_id, "community_id");
    }else{
        //Insert Group Id to bp_groups_testsuites table
        $wpdb->insert($wpdb->prefix . "bp_groups_testsuites", array('group_id' => $group_id, 'ts_ids' => $post_id));
        //Update Post Meta
        update_post_meta($post_id, 'community_id', $group_id);
    }
    
    //Save Initial Messages
    $init_message = $_POST['init_message'];
    update_post_meta($post_id, 'init_message', $init_message);
    
    //Save Roles
    
    $roleNames = array();
    $roleDescs = array();
    delete_post_meta($post_id, 'role_names');
    delete_post_meta($post_id, 'role_descs');
    
    if(isset($_POST['role_names']))
    {
        foreach($_POST['role_names'] as $i=>$rname)
        {
            if(trim($rname) != '')
            {
                $roleNames[] = $rname;
                $roleDescs[] = $_POST['role_descs'][$i];
            }
        }
        
        if(count($roleNames) > 0)
        {
            add_post_meta($post_id, 'role_names', '|' . implode('|', $roleNames) . '|', true);
            add_post_meta($post_id, 'role_descs', '|' . implode('|', $roleDescs) . '|', true);
        }
    }
    
    //Save Related Test Suites
    $ts = $_POST['ts'] ;
    $ts_desc = $_POST['ts_desc'];

    update_post_meta($post_id, 'ts', $ts);
    update_post_meta($post_id, 'ts_desc', $ts_desc);
    
    //Save Spec Documents        
    $docs_names = $_POST['doc_name'];
    $docs_descs = $_POST['doc_desc'];
    $docs_locs = $_POST['doc_loc'];
    $docs_files = $_FILES['attachment_doc'];
    $docs_ids = $_POST['doc_id'];
    
    //Remove Old documents
    $wpdb->query("DELETE FROM " . $wpdb->prefix . "ts_options_documents WHERE ts_id=" . $post_id);
    //Save New Data
    
    if(!$docs_names)
        $docs_names = array();
    
    if ( ! function_exists( 'wp_handle_upload' ) ) require_once( ABSPATH . 'wp-admin/includes/file.php' );
    
    foreach ($docs_names as $idx => $doc_name) {
        $doc_desc = $docs_descs[$idx];
        $doc_loc = $docs_locs[$idx];
        $doc_file = array(
            'name' => $docs_files['name'][$idx],
            'tmp_name' => $docs_files['tmp_name'][$idx],
            'error' => $docs_files['error'][$idx],
            'size' => $docs_files['size'][$idx],
            'type' => $docs_files['type'][$idx],
        );
        
        $dest = '';
        
        if (!$doc_loc && $doc_file['error'] == UPLOAD_ERR_OK) {
            $uploaded = wp_handle_upload($doc_file, array('test_form' => false));
            if($uploaded)
                $doc_loc = $uploaded['url'];            
        }
        $wpdb->insert(
            $wpdb->prefix.'ts_options_documents', 
            array( 
                'ts_id' => $post_id,
                'doc_name' => $doc_name,
                'doc_desc' => $doc_desc,
                'doc_loc_url' => $doc_loc,
                'doc_file_name'=> $doc_file['name'],
                'doc_file_path' => $dest
                
            ), 
            array( 
                '%d',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s'
            )
        );
    }    
    
    //Save Conformance Level
    $lvl_code = $_POST['lvl_code'];
    $lvl_desc = $_POST['lvl_desc'] ;
    
    update_post_meta($post_id, 'lvl_code', $lvl_code);
    update_post_meta($post_id, 'lvl_desc', $lvl_desc);
    
    //Subscription Price
    update_post_meta($post_id, 'monthly_subscription_price', $_POST['monthly_subscription_price']);
}

function getTestSuiteRoles($suite_id)
{
    $roleNames = get_post_meta($suite_id, 'role_names', true);
    $roleDescs = get_post_meta($suite_id, 'role_descs', true);
    $roles = array();
    if(!$roleNames)
    {
        return $roles;
    }else{        
        $arrName = explode('|', $roleNames);
        $arrDescs = explode('|', $roleDescs);
        
        foreach($arrName as $i=>$n)
        {
            if(!$arrName[$i])
                continue;
            
            $roles[] = array('name' => $arrName[$i], 'desc' => $arrDescs[$i]);
        }
    }
    
    return $roles;
}
