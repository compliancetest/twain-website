<?php
if ( class_exists( 'BP_Group_Extension' ) )
{
    class CP_Downloads_Group_Extension extends BP_Group_Extension {
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
        function admin_screen( $group_id ) {
            ?>
 
            <p>The HTML for my admin panel.</p>
 
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
            $fileNames = $_POST['file_name'];
            $fileDescs = $_POST['file_description'];
            $fileLicenses = $_POST['file_license'];
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
                                  'name' => !$fileNames[$i] ? $fileName : $fileNames[$i],
                                  'description' => $fileDescs[$i], 
                                  'license' => $fileLicenses[$i], 
                                  'size' => $file['size'], 
                                  'created_date' => date('Y-m-d H:i:s'), 
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
                        if($file->license && !isset($_POST['agree_license']))
                        {
                            //Goto File Detail Page                            
                            wp_redirect(bp_get_group_permalink($group) . $obj->slug . "?id=" . $file->id);
                        }else{
                            //Download File
                            $obj->downloadFile($group->id, $_REQUEST['id']);        
                        }
                        
                    }
                    exit;    
                }
                if(wp_verify_nonce($_REQUEST['_wpnonce'], 'groups_downloads_delete')) //Download Files
                {
                    if(bp_group_is_admin())
                    {
                        $result = $obj->deleteFile($group->id, $_REQUEST['id']);
                        if($result === true)
                            echo 'success';
                        else
                            echo $result;
                    }else{
                        echo "Permission Denied!";
                    }
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
                            <form id="fileEditForm<?php echo $file->id?>" action="" onsubmit="return saveFile()" style="display: none;">
                                <div class="grid_cell width30P">
                                    <label>File Name:</label>
                                    <input type="text" name="file_name" value="<?php echo $file->name?>" />
                                </div>
                                <div class="grid_cell width60P left10">
                                    <label>Description:</label>
                                    <input type="text" name="file_desc" value="<?php echo $file->description?>" />
                                </div>
                                <div class="clear"></div>
                                <div class="grid_cell width100P">
                                    <label>License Agreement: </label>
                                    <textarea cols="20" rows="5" name="file_license"><?php echo $file->license?></textarea>
                                </div>
                                <div class="clear"></div>
                                <button class="action-btn process-btn"><span class="p"></span><span class="t">Save</span></button>
                                <a href="#" class="action-btn cancel-btn"><span class="p"></span><span class="t">Cancel</span></a>
                                <div class="clear"></div>
                                <?php wp_nonce_field('groups_downlaods_save_file') ?>
                                <input type="hidden" name="group_id" value="<?php echo $group->id?>" />
                            </form>
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