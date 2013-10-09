<?php
if ( class_exists( 'BP_Group_Extension' ) )
{
    class CP_Downloads_Group_Extension extends BP_Group_Extension {
        var $enable_create_step = false;
        function __construct() {
            $this->name = 'Buddypress Group Downloads';
            $this->slug = 'downloads';
 
            $this->create_step_position = 21;
            $this->nav_item_position = 31;            
        }
        
        function edit_screen() {
            if(!bp_is_group_creation_step($this->slug))
            {
                return false;
            }
            ?><?php
            wp_nonce_field('groups_create_save_' . $this->slug);
        }
        
        function display()
        {
            //Check if the current user is member of the group or not
            if(bp_group_is_member())
            {
                locate_template( array( 'groups/single/downloads.php'        ), true );
            }else{
                ?><p>You need to join the community to download the files</p><?php
            }
            ?>
            
            <?php
        }
        
        
        function getFiles($group_id)
        {
            global $wpdb;
            
            $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "bp_groups_downloads WHERE group_id=%d", $group_id);
            $rows = $wpdb->get_results($query);
            
            return $rows;
        }
        
        function getFile($group_id, $file_id)
        {
            global $wpdb;
            
            $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "bp_groups_downloads WHERE id=%d AND group_id=%d", $file_id, $group_id);
            $row = $wpdb->get_row($query);
            
            return $row;
        }
        
        /**
        * Save File
        * 
        * @param mixed $group_id
        */
        function saveFiles($group_id)
        {
            global $wpdb;
            
            $uploadDir = wp_upload_dir();
            
            $baseDir = $uploadDir['basedir'] . "/downloads";
            if(!is_dir($baseDir)){
                mkdir($baseDir, 0777);
                //Create a htaccess to preven direct access
                addHTAccessProtection($baseDir);
            }
            $baseDir = $baseDir . "/" . $group_id;
            if(!is_dir($baseDir))
                mkdir($baseDir, 0777);
            
            //Upload Files
            $fileNames = stripslashes_deep($_POST['file_name']);
            $fileVersions = $_POST['file_version'];
            $fileDescs = stripslashes_deep($_POST['file_description']);
            $fileLicenses = stripslashes_deep($_POST['file_license']);
            $files = $_FILES['file'];
            
            for($i=0; $i < count($files['name']); $i++)
            {
               $file = array(
                    'name' => $files['name'][$i],
                    'tmp_name' => $files['tmp_name'][$i],
                    'error' => $files['error'][$i],
                    'size' => $files['size'][$i],
                    'type' => $files['type'][$i],
               ); 
               
               if ($file['error'] == UPLOAD_ERR_OK) {
                   //Upload File
                   //check file exists or not
                   $fileName = $file['name']; 
                   while(file_exists($baseDir . '/' . $fileName))
                   {
                       $fileName = rand(0, 9999) . $file['name'];
                   }
                   if(move_uploaded_file($file['tmp_name'], $baseDir . '/' . $fileName))
                   {
                       //Save data
                       $wpdb->insert($wpdb->prefix . 'bp_groups_downloads', 
                            array('group_id'=>$group_id, 
                                  'name' => !$fileNames[$i] ? $fileName : htmlspecialchars($fileNames[$i]),
                                  'version' => $fileVersions[$i],
                                  'description' => htmlspecialchars($fileDescs[$i]), 
                                  'version_description' => '', 
                                  'license' => htmlspecialchars($fileLicenses[$i]), 
                                  'size' => $file['size'], 
                                  'created_date' => date('Y-m-d H:i:s'), 
                                  'last_updated' => date('Y-m-d H:i:s'), 
                                  'location' => $baseDir . '/' . $fileName)
                        );
                   }
               }
            }
        }
        
        /**
        * Download File
        * 
        * @param mixed $file_id
        */
        function downloadFile($group_id, $file_id)
        {
            global $wpdb;
            
            $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "bp_groups_downloads WHERE id=%d AND group_id=%d", $file_id, $group_id);
            $row = $wpdb->get_row($query);
            
            if($row)
            {
                if(file_exists($row->location))
                {
                    $info = pathinfo($row->location);
                    $info1 = pathinfo($row->name);
                    //Add Extension
                    if(!isset($info1['extension']) || !$info1['extension'])
                        $row->name .= "." . $info['extension'];
                    
                    header("Expires: Mon, 26 Nov 1962 00:00:00 GMT");
                    header("Last-Modified: " . gmdate("D,d M Y H:i:s") . " GMT");
                    header("Cache-Control: no-cache, must-revalidate");
                    header("Pragma: no-cache");
                    header("Content-Type: Application/octet-stream");
                    header("Content-disposition: attachment; filename=" . $row->name);
                    
                    $fp = fopen($row->location, 'r');
                    while (!feof($fp))
                    {
                        echo fread($fp, 65536); 
                        flush();
                    }  
                    fclose($fp); 
                    exit;
                }else{
                    addMessage('File not found!', 'error');
                    $group = groups_get_current_group();
                    wp_redirect(bp_get_group_permalink($group) . $obj->slug);    
                    exit;
                }
            }else{
                $group = groups_get_current_group();
                wp_redirect(bp_get_group_permalink($group) . $obj->slug);
                exit;
            }
        }
        
        function deleteFile($group_id, $file_id)
        {
            global $wpdb;
            
            $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "bp_groups_downloads WHERE id=%d AND group_id=%d", $file_id, $group_id);
            $row = $wpdb->get_row($query);
            if($row)
            {
                if(file_exists($row->location))
                    unlink($row->location);
                $wpdb->delete($wpdb->prefix . 'bp_groups_downloads', array('id' => $row->id));
                return true;
            }else{
                return 'File not found!';
            }
        }
        
        function updateFile($group_id, $file_id)
        {
            global $wpdb;
            
            $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "bp_groups_downloads WHERE id=%d AND group_id=%d", $file_id, $group_id);
            $row = $wpdb->get_row($query);
            if($row)
            {
                $uploadDir = wp_upload_dir();
            
                $baseDir = $uploadDir['basedir'] . "/downloads/" . $group_id;
                
                $data = array();
                $file = $_FILES['file'];
                if ($file['error'] == UPLOAD_ERR_OK) {
                    //Upload File
                    //check file exists or not
                    $fileName = $file['name'];
                    while(file_exists($baseDir . '/' . $fileName))
                    {
                        $fileName = rand(0, 9999) . $file['name'];
                    }
                    
                    if(move_uploaded_file($file['tmp_name'], $baseDir . '/' . $fileName))
                    {
                        //Remove Old One
                        @unlink($row->location);                        
                        $data['location'] = $baseDir . '/' . $fileName;
                    }
                }
               
                
                if(isset($_POST['file_name']) && $_POST['file_name'] != '')
                    $data['name'] = htmlspecialchars($_POST['file_name']);
                else{
                    $data['name'] = basename($row->location);
                }
                
                $data['description'] = htmlspecialchars($_POST['file_desc']);
                $data['version'] = $_POST['file_version'];
                $data['version_description'] = htmlspecialchars($_POST['file_changes_desc']);
                $data['license'] = htmlspecialchars($_POST['file_license']);
                $data = stripslashes_deep( $data );
                $wpdb->update($wpdb->prefix . 'bp_groups_downloads', $data, array('id' => $row->id));
                addMessage('File has been updated successfully!');
                return true;
            }else{
                addMessage('Invalid Request!', 'error');
                return 'File not found!';
            }
        }
        
        
    }
    
    bp_register_group_extension('CP_Downloads_Group_Extension');
    
    add_action("init", 'manageGroupDownloadsAction');
    function manageGroupDownloadsAction()
    {
        $group = groups_get_current_group();
        
        if(is_user_logged_in() && bp_group_is_member($group))
        {
            $obj = new CP_Downloads_Group_Extension();
            if(bp_is_current_action($obj->slug))
            {
                if(wp_verify_nonce($_POST['_wpnonce'], 'groups_downloads_save')) //Save Files
                {
                    if(bp_group_is_admin())
                    {
                        $obj->saveFiles($group->id);
                        addMessage('File(s) was uploaded successfully!');
                    }
                    //Redirect
                    wp_redirect(bp_get_group_permalink($group) . $obj->slug);
                    exit;    
                }
                if(wp_verify_nonce($_POST['_wpnonce'], 'groups_downloads_update')) //Save Files
                {
                    if(bp_group_is_admin())
                    {
                        $obj->updateFile($group->id, $_POST['file_id']);
                    }
                    //Redirect
                    wp_redirect(bp_get_group_permalink($group) . $obj->slug);
                    exit;    
                }
                if(wp_verify_nonce($_REQUEST['_wpnonce'], 'groups_downloads_download')) //Download Files
                {
                    //Get File
                    $file = $obj->getFile($group->id, $_REQUEST['id']);
                    if($file)
                    {
                        if($file->license && !isset($_SESSION['agree_license'][$file->id]))
                        {
                            addMessage('Please read License Agreement of the file and agree with it to download the file.', 'error');
                            //Goto File Detail Page                            
                            wp_redirect(bp_get_group_permalink($group) . $obj->slug . "?id=" . $file->id);
                        }else{
                            //Download File
                            $obj->downloadFile($group->id, $_REQUEST['id']);        
                        }
                        
                    }
                    exit;    
                }
                if(wp_verify_nonce($_REQUEST['_wpnonce'], 'groups_downloads_agree_license')) //Download Files
                {                    
                    //Get File
                    $file = $obj->getFile($group->id, $_POST['file_id']);
                    if(!$file)
                    {
                        echo 'error';
                    }else{
                        if(!isset($_SESSION['agree_license']))
                            $_SESSION['agree_license'] = array();
                        $_SESSION['agree_license'][$file->id] = true;
                        echo bp_get_group_permalink($group) . $obj->slug . "?_wpnonce=" . wp_create_nonce('groups_downloads_download') . "&id=" . $file->id;
                    }
                    exit;    
                }
                if(wp_verify_nonce($_REQUEST['_wpnonce'], 'groups_downloads_show_license')) //Download Files
                {
                    
                    //Get File
                    $file = $obj->getFile($group->id, $_REQUEST['id']);
                    ?>
                    <div id="agree-file-license" class="popup-box" style="display: none;">                
                        <div class="popup-box-header radius6 noradiusbottom">License Agreement</div>
                        <div class="popup-box-content">
                            <form method="post" action="<?php echo bp_get_group_permalink($group) . 'downloads'?>" id="agree-file-license-form">                        
                                <?php
                                    if(!$file)
                                    {
                                ?>
                                <div class="message error">Invalid Request!</div>
                                <?php
                                    }else if(!$file->license){
                                ?>
                                <p>This file doesn't have a License Agreement.</p>
                                <?php
                                    }else{
                                ?>
                                    <p><?php echo $file->license?></p>
                                    <label><input type="checkbox" name="agree_license" value="agree_license" id="agree_community_license" <?php echo isset($_SESSION['agree_license'][$file->id]) ? 'checked="checked"' : ''?> autocomplete="off" /> I agree with the License Agreement</label>                
                                <?php
                                    }
                                ?>   
                                <?php
                                    wp_nonce_field('groups_downloads_agree_license');                                  
                                ?>                             
                                <input type="hidden" name="file_id" value="<?php echo $file->id?>" />
                                <input type="hidden" name="group_id" value="<?php echo $group->id?>" />
                                <div class="clear"></div>                            
                            </form>    
                        </div>
                        <div class="popup-box-footer radius6 noradiustop">                                                        
                            <a href="javascript: void(0)" class="action-btn process-btn"><span class="p"></span><span class="t">DOWNLOAD</span></a>
                            <a href="javascript: void(0)" class="action-btn cancel-btn"><span class="p"></span><span class="t">CANCEL</span></a>                    
                            <div class="clear"></div>
                            <div class="message error" style="display: none;">Please aggree the License Agreement.</div>
                        </div>
                        <div class="loading"></div>
                        <a id="close-popup-community" class="close_btn"></a>                
                    </div>
                    <?php
                    exit;    
                }
                
                if(wp_verify_nonce($_REQUEST['_wpnonce'], 'groups_downloads_delete')) //Download Files
                {
                    if(bp_group_is_admin())
                    {
                        $result = $obj->deleteFile($group->id, $_REQUEST['id']);
                        if($result === true)
                            addMessage('The file was deleted successfully!');
                        else
                            addMessage($result, 'error');
                    }else{
                        addMessage('Permission Denied!', error);
                    }
                    wp_redirect(bp_get_group_permalink($group) . $obj->slug);
                    exit;    
                }
                
                if(wp_verify_nonce($_REQUEST['_wpnonce'], 'groups_downloads_get_file')) //Edit File
                {
                    if(bp_group_is_admin())
                    {
                        $file = $obj->getFile($group->id, $_REQUEST['id']);
                        if($file)
                        {
                            json_encode(array('name' => $file->name, 'id' => $file->id, 'description' => $file->description, 'license' => $file->license));
                            ob_start();
                            ?>
                            <div class="grid-list-row grid-file-edit-row" id="fileEditRow<?php echo $file->id?>">
                                <form id="fileEditForm<?php echo $file->id?>" class="file-edit-form" action="" enctype="multipart/form-data" method="post">
                                    <h3>Edit File</h3>
                                    <div class="grid-list">
                                        <div class="grid-list-row">
                                            <div class="grid-list-cell width35P">
                                                <input type="file" name="file" class="input-file" />
                                                <br />(The original file will be replaced. Please leave this blank if you don't want change the file.)
                                            </div>
                                            <div class="grid-list-cell left15 grid-field-cell">
                                                <label>File Name:</label>
                                                <input type="text" class="text" name="file_name" value="<?php echo $file->name?>" /><br clear="all">
                                                <label>File Version:</label>
                                                <input type="text" class="text" name="file_version" value="<?php echo $file->version?>" /><br clear="all">
                                                <label>Description:</label>
                                                <input type="text" class="text" name="file_desc" value="<?php echo $file->description?>" /><br clear="all">
                                                <label>Description of Changes:</label>
                                                <input type="text" class="text" name="file_changes_desc" value="<?php echo $file->version_description?>" /><br clear="all">
                                                <label>File License Agreement:</label>
                                                <textarea cols="20" rows="5" class="textarea" name="file_license"><?php echo $file->license?></textarea>
                                            </div>
                                            <div class="clear"></div>
                                        </div>
                                        <div class="grid-list-footer grid-list-row">                                        
                                            <a href="#" class="action-btn cancel-btn" data-id="<?php echo $file->id?>"><span class="p"></span><span class="t">Cancel</span></a>
                                            <a href="javascript: void(0)" onclick="document.getElementById('fileEditForm<?php echo $file->id?>').submit();" class="action-btn process-btn" data-id="<?php echo $file->id?>"><span class="p"></span><span class="t">SAVE</span></a>
                                            <div class="clear"></div>
                                            <div class="message" style="display: none;"></div>                                            
                                        </div>
                                    </div>                
                                    <?php             
                                        wp_nonce_field('groups_downloads_update'); 
                                    ?>
                                    <input type="hidden" name="group_id" value="<?php echo $group->id?>" />
                                    <input type="hidden" name="file_id" value="<?php echo $file->id?>" />
                                </form>
                                <div class="loading"></div>
                            </div>
                            <?php
                            $html = ob_get_contents();
                            ob_end_clean();
                            echo $html;
                        }else{
                            echo '<div class="message error" style="display: none">Invalid Request!</div>';
                        }
                    }else{
                        echo '<div class="message error" style="display: none">Permission Denied!</div>';
                    }
                    exit;    
                }
                
            }    
        }
        
    }
}