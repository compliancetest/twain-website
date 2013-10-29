<?php
/**
* Test Data  
*/

function saveProfileType()
{
    global $wpdb;
    
    $content = stripslashes($_POST['profile_type_text']);
    $file = $_FILES['profile_type_file'];
    
    $community_id = $_POST['community_id'];
    $type_id = isset($_POST['type_id']) ? $_POST['type_id'] : null;
    $user_id = get_current_user_id();
    
    if(!groups_is_user_admin($user_id, $community_id) && !is_super_admin() && !is_admin())
    {
        addMessage('Invalid Request!', 'error');
        return false;
    }
    
    if($file['error'] == UPLOAD_ERR_OK && $file['size'] > 0)
    {
        $fp = fopen($file['tmp_name'], 'r');
        $content = fread($fp, filesize($file['tmp_name']));
        fclose($fp);
        $_POST['profile_type_text'] = $content;
    }
    
    $schemaObj = json_decode($content);
    if(!$schemaObj)
    {
        addMessage('Sorry, the entered schema is not valid structure.', 'error');
        return false;
    }
    
    if($type_id)
    {
        //Validate Type ID
        $query = $wpdb->prepare("SELECT id FROM ". $wpdb->prefix . "community_profile_types WHERE id=%d AND community_id=%d", $type_id, $community_id);
        $id = $wpdb->get_var($query);
        if(!$id)
        {
            addMessage('Invaild Request!', 'error');
            return;
        }
        $result = $wpdb->update($wpdb->prefix . "community_profile_types", 
                                array('community_id' => $community_id, 'title' => $schemaObj->title, 'creator_id' => $user_id, 'created_date' => date('Y-m-d H:i:s'), 'schema' => base64_encode($content)),
                                array('id' => $type_id));        
    }else{
        $wpdb->insert($wpdb->prefix . "community_profile_types", 
                                array('community_id' => $community_id, 'title' => $schemaObj->title, 'creator_id' => $user_id, 'created_date' => date('Y-m-d H:i:s'), 'schema' => base64_encode($content)));
    }
    
    addMessage('Profile Type successfully saved!');
    $group = groups_get_group(array('group_id' => $community_id));
    
    wp_redirect(bp_get_group_admin_permalink($group));
    exit;
    
}

function deleteProfileType()
{
    global $wpdb;
    
    $type_id = $_REQUEST['type_id'];
    $community_id = $_REQUEST['community_id'];
    $user_id = get_current_user_id();
    
    if(!groups_is_user_admin($user_id, $community_id) && !is_super_admin() && !is_admin())
    {
        addMessage('Invalid Request!', 'error');
        return false;
    }
    
    $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "community_profile_types WHERE id=%d AND community_id=%d", $type_id, $community_id);
    $row = $wpdb->get_row($query);
    
    if(!$row)
    {
        addMessage('Invalid Request!', 'error');
        return false;
    }
    
    if($row->instances > 0)
    {
        addMessage("Sorry, you can't delete the profile type because it still includes some instances.", "error");
        return;
    }
    
    $wpdb->delete($wpdb->prefix . "community_profile_types", array('id' => $row->id));
    addMessage("The profile type was deleted.");
    $group = groups_get_group(array('group_id' => $community_id));    
    wp_redirect(bp_get_group_admin_permalink($group));
    
    exit;
}

function readProfileType()
{
    global $wpdb;
    
    $type_id = $_REQUEST['type_id'];
    $community_id = $_REQUEST['community_id'];
    $user_id = get_current_user_id();
    
    header('Content-type: application/xml');
    
    if(!groups_is_user_admin($user_id, $community_id) && !is_super_admin() && !is_admin())
    {
        echo '<result><status>error</status><msg>Invalid Request!</msg></result>';
        exit;
    }
    
    $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "community_profile_types WHERE id=%d AND community_id=%d", $type_id, $community_id);
    $row = $wpdb->get_row($query);
    
    if(!$row)
    {
        echo '<result><status>error</status><msg>Invalid Request!</msg></result>';
        exit;
    }
    
    echo '<result>';
    echo '<status>success</status>';
    echo '<schema><![CDATA[' . base64_decode($row->schema) . ']]></schema>';
    echo '<id>' . $row->id . '</id>';
    echo '</result>';
    exit;
}

function createUIFromProfileType($action)
{
    global $wpdb;
    
    $type_id = $_REQUEST['profile-type-id'];
    $instance_id = $_REQUEST['instance-id'];
    
    $user_id = get_current_user_id();
    
    header('Content-type: application/xml');
    
    $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "community_profile_types WHERE id=%d", $type_id);
    $row = $wpdb->get_row($query);
    
    if(!$row)
    {
        echo '<result><status>error</status><message>Invalid Request!</message></result>';
        exit;
    }
    
    $community_id = $row->community_id;
    
    if(wp_verify_nonce($action, 'get-harness-profile-ui'))
    {
        $instance_type = 'harness';
        
        if(!groups_is_user_admin($user_id, $community_id) && !is_super_admin() && !is_admin())
        {
            echo '<result><status>error</status><message>Invalid Request!</message></result>';
            exit;    
        }
    }else if(wp_verify_nonce($action, 'get-tester-profile-ui')){
        $instance_type = 'tester';
        $community_ids = getUserSubscribedCommunities($user_id);
        if(!in_array($community_id, $community_ids))
        {
            echo '<result><status>error</status><message>Invalid Request!</message></result>';
            exit;    
        }
    }
    
    if(!$instance_id)
    {
        echo '<result><status>success</status><schema><![CDATA[' . base64_decode($row->schema) . ']]></schema><data>{}</data></result>';    
    }else{
        $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "community_profile_instances WHERE id=%d AND community_id=%d", $instance_id, $community_id);
        $instance_row = $wpdb->get_row($query);
        if(!$instance_row)
        {
            echo '<result><status>error</status><message>Invalid Request!</message></result>';
            exit;    
        }else{
            echo '<result><status>success</status><schema><![CDATA[' . base64_decode($row->schema) . ']]></schema><data>' . base64_decode($instance_row->content) . '</data></result>';    
        }
    }
    
    exit;
}

function saveProfileInstance($action)
{
    global $wpdb;
    
    $type_id = $_REQUEST['profile-type-id'];    
    $instance_id = $_REQUEST['instance-id'];
    $user_id = get_current_user_id();
    $instance_type = '';
    
    header('Content-type: application/xml');
    
    $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "community_profile_types WHERE id=%d", $type_id);
    $profile_type = $wpdb->get_row($query);
    
    if(!$profile_type)
    {
        echo '<result><status>error</status><msg>Invalid Request!</msg></result>';
        exit;
    }
    
    $community_id = $profile_type->community_id;
    
    if(wp_verify_nonce($action, 'save-harness-instance'))
    {
        $instance_type = 'harness';
        
        if(!groups_is_user_admin($user_id, $community_id) && !is_super_admin() && !is_admin())
        {
            echo '<result><status>error</status><msg>Invalid Request!</msg></result>';
            exit;    
        }
    }else if(wp_verify_nonce($action, 'save-tester-instance')){
        $instance_type = 'tester';
        $community_ids = getUserSubscribedCommunities($user_id);
        if(!in_array($community_id, $community_ids))
        {
            echo '<result><status>error</status><msg>Invalid Request!</msg></result>';
            exit;    
        }
    }
    
    $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "community_profile_types WHERE id=%d AND community_id=%d", $type_id, $community_id);
    $profile_type = $wpdb->get_row($query);
        
    if($instance_id)
    {
        $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "community_profile_instances WHERE id=%d AND community_id=%d", $instance_id, $community_id);
        $profile_instance = $wpdb->get_row($query);
        
        if(!$profile_instance)
        {
            echo '<result><status>error</status><msg>Invalid Request!</msg></result>';
            exit;
        }
    }
    
    
    //Getting Data
    $data = stripcslashes($_POST['data']);
    
    $jsonData = base64_encode($data);
    $jsonObject = json_decode($data);
    $basePath = ABSPATH . "profiles";
    
    //Save File
    if(!is_dir($basePath))
    {
        mkdir($basePath, 0775);
        //Add Index.html
        $fp = fopen($basePath . "/index.html", "w");        
        fclose($fp);        
    }
    
    
    $basePath  = $basePath . "/" . $instance_type;
    //Save File
    if(!is_dir($basePath))
    {
        mkdir($basePath, 0775);
        //Add Index.html
        $fp = fopen($basePath . "/index.html", "w");        
        fclose($fp);        
    }
    
    //Remove Old File
    if($instance_id && $profile_instance)
    {
        @unlink($basePath . "/" . $profile_instance->filename );
    }
    
    $filename = sanitize_file_name($jsonObject->Profile->Title) . ".json";
    
    $idx = 2;
    while(file_exists($basePath . "/" . $filename))
    {
        $filename = sanitize_file_name($jsonObject->Profile->Title) . "-$idx.json";
        $idx++;
    }
    
    $fp = fopen($basePath . "/" . $filename, "w");
    fwrite($fp, base64_decode($jsonData), strlen(base64_decode($jsonData)));
    fclose($fp);
    
    if($instance_id)
    {
        $wpdb->update($wpdb->prefix . "community_profile_instances", 
                        array(
                            'type' => $instance_type,
                            'profile_name' => $jsonObject->Profile->Title,
                            'type_id' => $type_id,
                            'community_id' => $community_id,
                            'filename' => $filename,
                            'content' => $jsonData,
                            'created_date' => date('Y-m-d H:i:s'),
                            'creator_id' => $user_id
                        ),
                        array('id' => $instance_id)
                    );
    }else{
        $wpdb->insert($wpdb->prefix . "community_profile_instances", 
                        array(
                            'type' => $instance_type,
                            'profile_name' => $jsonObject->Profile->Title,
                            'type_id' => $type_id,
                            'community_id' => $community_id,
                            'filename' => $filename,
                            'content' => $jsonData,
                            'created_date' => date('Y-m-d H:i:s'),
                            'creator_id' => $user_id
                        )
                    );   
          $wpdb->query($wpdb->prepare("UPDATE " . $wpdb->prefix . "community_profile_types SET `instances`=`instances` + 1 WHERE id=%d", $type_id));
    }
    
    echo '<result><status>success</status></result>';
    exit;
}

function deleteProfileTypeInstance($action)
{
    global $wpdb;
    
    $id = $_REQUEST['id'];
    
    $user_id = get_current_user_id();
    
    $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "community_profile_instances WHERE id=%d", $id);
    $row = $wpdb->get_row($query);
    
    if(!$row)
    {
        addMessage('Invalid Request!', 'error');
        return;
    }
    
    $redirect = isset($_REQUEST['return']) ? base64_decode($_REQUEST['return']) : cp_get_group_permalink_by_id($row->community_id) . "testdata";
    
    if( (wp_verify_nonce($action, 'delete-harness-instance') && !groups_is_user_admin($user_id, $row->community_id)) || (wp_verify_nonce($action, 'delete-harness-instance') && $row->creator_id != $user_id ) )
    {
        addMessage('Permission Denied!', 'error');        
        wp_redirect($redirect);
        exit;
    }
    
    //Remove File
    @unlink(ABSPATH . "profiles/" . $row->type . "/" . $row->filename);
    $wpdb->delete($wpdb->prefix . "community_profile_instances", array('id' => $row->id));
    $wpdb->query($wpdb->prepare("UPDATE " . $wpdb->prefix . "community_profile_types SET `instances`=`instances` - 1 WHERE id=%d", $row->type_id));
    addMessage('Profile instance was removed.');
    wp_redirect($redirect);
    exit;
}

function downloadProfileTypeInstance()
{
    global $wpdb;
    
    $id = $_REQUEST['id'];
    
    $user_id = get_current_user_id();
    
    $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "community_profile_instances WHERE id=%d", $id);
    $row = $wpdb->get_row($query);
    
    if(!$row)
    {
        addMessage('Invalid Request!', 'error');
        return;
    }
    
    header("Expires: Mon, 26 Nov 1962 00:00:00 GMT");
    header("Last-Modified: " . gmdate("D,d M Y H:i:s") . " GMT");
    header("Cache-Control: no-cache, must-revalidate");
    header("Pragma: no-cache");
    header("Content-Type: Application/octet-stream");
    header("Content-disposition: attachment; filename=" . $row->filename);
    
    $fp = fopen(ABSPATH . "profiles/" . $row->type . "/" . $row->filename, 'r');
    while (!feof($fp))
    {
        echo fread($fp, 65536); 
        flush();
    }  
    fclose($fp); 
    exit;
}

function downloadProfileType()
{
    global $wpdb;
    
    $id = $_REQUEST['id'];
    
    $user_id = get_current_user_id();
    
    $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "community_profile_types WHERE id=%d", $id);
    $row = $wpdb->get_row($query);
    
    if(!$row)
    {
        addMessage('Invalid Request!', 'error');
        return;
    }
    
    header("Expires: Mon, 26 Nov 1962 00:00:00 GMT");
    header("Last-Modified: " . gmdate("D,d M Y H:i:s") . " GMT");
    header("Cache-Control: no-cache, must-revalidate");
    header("Pragma: no-cache");
    header("Content-Type: Application/octet-stream");
    header("Content-disposition: attachment; filename=" . sanitize_file_name($row->title) . ".json");
    
    echo base64_decode($row->schema);
    exit;
}



function viewProfileType()
{
    global $wpdb;
    
    $type_id = $_REQUEST['id'];
    
    $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "community_profile_types WHERE id=%d", $type_id);
    $row = $wpdb->get_row($query);
        
    if(!$type_id)
    {
        ?>
        <div class="popup-box view-profile-type-box" style="display: none; width: 900px;" id="view-profile-type-box<?php echo $boxId?>">
            <div class="popup-box-header radius6 noradiusbottom">Error</div>        
            <div class="popup-box-content grid-box-body">                    
                <p class="message error">Invalid Request!</p>
            </div>
            <div class="popup-box-footer radius6 noradiustop">                
                <a href="#" class="action-btn cancel-btn close-popup-btn"><span class="p"></span><span class="t">Close</span></a>            
                <div class="clear"></div>
            </div>                        
            <a class="close_btn"></a>                        
        </div>
        <?php
    }else{
        $boxId = time();
        ?>
        <div class="popup-box view-profile-type-box" style="display: none; width: 900px;" id="view-profile-type-box<?php echo $boxId?>">
            <div class="popup-box-header radius6 noradiusbottom">Profile Type Detail</div>        
            <div class="popup-box-content grid-box-body">                    
                <div id="json-view-panel<?php echo $boxId?>"><?php echo base64_decode($row->schema)?></div>                
            </div>
            
            <div class="popup-box-footer radius6 noradiustop">                                                
                <a href="<?php echo cp_get_group_permalink_by_id($row->community_id)?>testdata?td-action=<?php echo wp_create_nonce('download-profile-type')?>&id=<?php echo $row->id?>" target="blank" class="action-btn process-btn"><span class="p"></span><span class="t">Download</span></a>
                <?php if(isset($_REQUEST['back'])){ ?>
                    <a href="#trigger-message-box" class="action-btn cancel-btn" rel="custom-popup" cp-type="inline"><span class="p"></span><span class="t">Close</span></a>
                <?php }else{?>
                    <a href="#" class="action-btn cancel-btn close-popup-btn"><span class="p"></span><span class="t">Close</span></a>
                <?php } ?>
                <div class="clear"></div>
            </div>                        
            <?php if(!isset($_REQUEST['back'])){ ?>
            <a class="close_btn"></a>                    
            <?php }?>                               
        </div>
        <script type="text/javascript">
            var t_data = Jsonary.create(<?php echo base64_decode($row->schema)?>).readOnlyCopy();
            var t_element = document.getElementById('json-view-panel<?php echo $boxId?>');
            Jsonary.render(t_element, t_data);                
        </script>
        <?php
    }
    exit;
}

function viewProfileInstance()
{
    global $wpdb;
    
    $instance_id = $_REQUEST['id'];
    
    $query = $wpdb->prepare("SELECT i.*, t.title as profile_type_title FROM " . $wpdb->prefix . "community_profile_instances AS i LEFT JOIN " . $wpdb->prefix ."community_profile_types AS t ON t.id=i.type_id WHERE i.id=%d", $instance_id);
    $row = $wpdb->get_row($query);
        
    if(!$row)
    {
        ?>
        <div class="popup-box" style="display: none; width: 900px;">
            <div class="popup-box-header radius6 noradiusbottom">Error</div>        
            <div class="popup-box-content grid-box-body">                    
                <p class="message error">Invalid Request!</p>
            </div>
            <div class="popup-box-footer radius6 noradiustop">                
                <a href="#" class="action-btn cancel-btn close-popup-btn"><span class="p"></span><span class="t">Close</span></a>            
                <div class="clear"></div>
            </div>                        
            <a class="close_btn"></a>                        
        </div>
        <?php
    }else{
        $boxId = time();
        ?>
        <div class="popup-box" style="display: none; width: 900px;" id="view-profile-instance-box<?php echo $boxId?>">
            <div class="popup-box-header radius6 noradiusbottom">Profile Instance Detail</div>        
            <div class="popup-box-content grid-box-body">                            
                <div id="json-view-panel<?php echo $boxId?>"><?php echo base64_decode($row->content)?></div>                
            </div>
            
            <div class="popup-box-footer radius6 noradiustop">                
                <input type="text" readonly="readonly" value="<?php echo get_site_url()?>/profiles/<?php echo $row->type?>/<?php echo $row->filename?>" class="input width60P" />                
                <a href="<?php echo cp_get_group_permalink_by_id($row->community_id)?>testdata?td-action=<?php echo wp_create_nonce('download-profile-instance')?>&id=<?php echo $row->id?>" target="blank" class="action-btn process-btn"><span class="p"></span><span class="t">Download</span></a>  
                <?php if(isset($_REQUEST['back'])){ ?>
                <a href="#trigger-message-box" class="action-btn cancel-btn" rel="custom-popup" cp-type="inline"><span class="p"></span><span class="t">Close</span></a>            
                <?php }else{ ?>
                <a href="#" class="action-btn cancel-btn close-popup-btn"><span class="p"></span><span class="t">Close</span></a>            
                <?php } ?>
                <div class="clear"></div>
            </div>                        
            <?php if(!isset($_REQUEST['back'])){ ?>
            <a class="close_btn"></a>                    
            <?php }?>                       
        </div>
        <script type="text/javascript">
            var t_data = Jsonary.create(<?php echo base64_decode($row->content)?>).readOnlyCopy();
            var t_element = document.getElementById('json-view-panel<?php echo $boxId?>');
            Jsonary.render(t_element, t_data);    
        </script>
        <?php
    }
    exit;
}
