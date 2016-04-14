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
            //if(bp_group_is_member())
            if (is_user_logged_in()) {
                locate_template( array( 'groups/single/downloads.php'        ), true );
            } else {
                echo '<p>' . MESSAGE_WARNING_ANONYMOUS . '</p>';
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
            
            /*$uploadDir = wp_upload_dir();
            
            $baseDir = $uploadDir['basedir'] . "/downloads";
            if(!is_dir($baseDir)){
                mkdir($baseDir, 0777);
                //Create a htaccess to preven direct access
                addHTAccessProtection($baseDir);
            }
            $baseDir = $baseDir . "/" . $group_id;
            if(!is_dir($baseDir))
                mkdir($baseDir, 0777);*/
            
            //Upload Files
//            $fileNames = stripslashes_deep($_POST['file_name']);
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
                   
                   //Save data
                   $token = createClaimToken();
                   $wpdb->insert($wpdb->prefix . 'bp_groups_downloads', 
                        array('group_id'=>$group_id, 
                              'name' => $fileName,
//                              'name' => !$fileNames[$i] ? $fileName : htmlspecialchars($fileNames[$i]),
                              'version' => $fileVersions[$i],
                              'description' => $fileDescs[$i], 
                              'version_description' => '', 
                              'license' => $fileLicenses[$i], 
                              'size' => $file['size'], 
                              'created_date' => date('Y-m-d H:i:s'), 
                              'last_updated' => date('Y-m-d H:i:s'), 
                              'location' => $fileName,
                              'token' => $token
//                              'download_file' => file_get_contents($file['tmp_name'])
                       )
                   );
                   $s3 = new S3Wrapper();
                   $ext = pathinfo( $fileName, PATHINFO_EXTENSION );
                   $s3->putObject('/attachments/downloads/' . $token . '/'.$fileName, file_get_contents($file['tmp_name']), 'application/'.$ext );
                   unlink($file['tmp_name']);
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
                if($row->location)
                {
                    wp_redirect( S3Wrapper::getAttachmentLink( $row->token, $row->name, 'downloads', true ), 301 );
                    exit;
                }else{
                    addMessage('File not found!', 'error');
                    $group = groups_get_current_group();                    
                    wp_redirect(bp_get_group_permalink($group) . $this->slug);    
                    exit;
                }
            }else{
                $group = groups_get_current_group();
                wp_redirect(bp_get_group_permalink($group) . $this->slug);
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
                $data = array();
                
                $data['description'] = ($_POST['file_desc']);
                $data['version'] = $_POST['file_version'];
                $data['version_description'] = htmlspecialchars($_POST['file_changes_desc']);
                $data['license'] = ($_POST['file_license']);
                $data['last_updated'] = date('Y-m-d H:i:s');
//                $data['name'] = htmlspecialchars($_POST['file_name']);
                
                $file = $_FILES['file'];
                
                if ($file['error'] == UPLOAD_ERR_OK) {
                    
                    /*if(isset($_POST['file_name']) && $_POST['file_name'] != '')
                        $data['name'] = htmlspecialchars($_POST['file_name']);
                    else{*/
                        $data['name'] = basename($file['name']);
//                    }
                    
                    
//                    $data['download_file'] = file_get_contents($file['tmp_name']);
                    $data['location'] = $file['name'];
                    $data['size'] = $file['size'];
                    $s3 = new S3Wrapper();
                    $ext = pathinfo( $file['name'], PATHINFO_EXTENSION );
                    $s3->putObject('/attachments/downloads/' . $row->token . '.'.$ext, file_get_contents($file['tmp_name']), 'application/'.$ext );
                    unlink($file['tmp_name']);
                    
                } 
                
                $data = stripslashes_deep( $data );                    
                if(!$wpdb->update($wpdb->prefix . 'bp_groups_downloads', $data, array('id' => $row->id))){
                    addMessage('Invalid Request!', 'error');
                    return 'File not found!';
                }

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

        //@todo Refactor this block
        if(($_REQUEST["action"]=="bpnoaccess") && !is_user_logged_in()){
            addMessage(MESSAGE_WARNING_ANONYMOUS, 'notice');
            wp_redirect(home_url());
            exit;
        }

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
                    $subscriptions = getUserSubscriptions(null, true);
                    //Get File
                    $file = $obj->getFile($group->id, $_REQUEST['id']);
                    $license = groups_get_groupmeta($group->id, 'license_agreements');
                    ?>
                    <div id="agree-file-license" class="popup-box" style="display: none;">                
                        <div class="popup-box-header radius6 noradiusbottom">License Agreement</div>
                        <div class="popup-box-content redactor_editor">
                            <form method="post" action="<?php echo bp_get_group_permalink($group) . 'downloads'?>" id="agree-file-license-form">                        
                                <?php
                                    if(!$file)
                                    {
                                ?>
                                <div class="message error">Invalid Request!</div>
                                <?php
                                    }else if(!$file->license && !$license){
                                ?>
                                <p>This file doesn't have a License Agreement.</p>
                                <?php
                                    }else{
                                ?>
                                    <?php if (count($subscriptions) > 0): ?>
                                    <p><?php echo !$file->license ? $license : $file->license?></p>
                                    <label><input type="checkbox" name="agree_license" value="agree_license" id="agree_community_license" <?php echo 0 && isset($_SESSION['agree_license'][$file->id]) ? 'checked="checked"' : ''?> autocomplete="off" /> I agree with the License Agreement</label>                
                                    <?php else: ?>
                                    <p><?php echo MESSAGE_WARNING_COMMUNITY_SUBSCRIBER; ?></p>
                                    <?php endif; ?>
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
                        <?php if (count($subscriptions) > 0): ?>
                        <div class="popup-box-footer radius6 noradiustop">                                                        
                            <a href="javascript: void(0)" class="action-btn download-btn"><span class="p"></span><span class="t">DOWNLOAD</span></a>
                            <a href="javascript: void(0)" class="action-btn cancel-btn"><span class="p"></span><span class="t">CANCEL</span></a>                    
                            <div class="clear"></div>
                            <div class="message error" style="display: none;">Please agree with the License Agreement.</div>
                        </div>
                        <div class="loading"></div>
                        <?php endif; ?>
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
                                            <div class="grid-list-cell left15 grid-field-cell">
                                                <label>File Name:</label>
                                                <input type="text" class="text readonly" name="file_name" value="<?php echo $file->name?>" readonly="readonly" /><br clear="all">
                                                <label>File Version:</label>
                                                <input type="text" class="text" name="file_version" value="<?php echo $file->version?>" /><br clear="all">
                                            </div>
                                            <div class="grid-list-cell width35P">
                                                <input type="file" name="file" class="input-file" />
                                                <br clear="all" />(The original file will be replaced. Please leave this blank if you don't want change the file.)
                                            </div>
                                            <div class="clear"></div>
                                            <div class="grid-list-cell grid-field-cell width85P">                                                
                                                <label>Description:</label>
                                                <textarea cols="20" rows="5" name="file_desc" class="text"><?php echo $file->description?></textarea><br clear="all">
                                                <label>Description of Changes:</label>
                                                <input type="text" class="text file_changes_desc" name="file_changes_desc" value="<?php echo $file->version_description?>" /><br clear="all">
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