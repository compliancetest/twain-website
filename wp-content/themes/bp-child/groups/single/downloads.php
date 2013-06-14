<?php
/**
* Download Files Page
*/
global $groups_template;

$downloads = new CP_Downloads_Group_Extension();

?>
<div id="downloads-container" class="tab-content white_bcg column">
    <?php if(isset($_GET['id'])){ ?>
    <!-- File Detail Page -->
    <h2>Download Attachment</h2>
        <?php    
            $fileId = isset($_GET['id']) ? $_GET['id'] : null;
            $file = $downloads->getFile(bp_get_group_id(), $fileId);
            if($file)
            {
        ?>
        <div id="file-detail">
            <form name="downloadform" id="downloadform" action="<?php bp_group_permalink()?><?php echo $downloads->slug ?>" method="post">
                <div class="grid_cell width20P"><label>File Name</label></div>
                <div class="grid_cell width70P left10">
                    <?php echo $file->name?> (<?php echo formatBytes($file->size)?>)<br />
                    <?php echo $file->description ?>
                </div>                    
                <div class="clear"></div>
                
                <?php if($file->license){ ?>
                <div class="grid_cell width100P"><label>License Agreement</label></div>
                <div class="grid_cell width100P">
                    <p class="file-license"><?php echo $file->license?></p>                        
                </div>                    
                <div class="grid_cell width100P">
                    <input type="checkbox" name="agree_license" id="agree_license" value="1" />
                    I agree with this License Agreement.
                </div>                    
                
                    <div class="clear"></div>
                <div class="message notice">Please agree the license agreement to download this file.</div>
                <?php } ?>                
                
                <button class="action-btn process-btn"><span class="p"></span><span class="t">Download</span></button>
                <div class="clear"></div>
                <input type="hidden" name="id" value="<?php echo $file->id?>" />
                <?php wp_nonce_field('groups_downloads_download') ?>
            </form>
        </div>
        <?php   
            }else{
        ?>
        <div class="message error">Sorry, we can't find the file that you requested.</div>
        <?php      
            }
        ?>
    <?php }else{ ?>
        <!-- Files List Page -->
        <h2>Files</h2>
    
        <div id="uploaded-files">
            <div class="grid_row grid_head grey-border-bottom">
                <div class="grid_cell width60P">Name</div>
                <div class="grid_cell width30P left15">Size</div>
                <div class="grid_cell width5P left15">Action</div>
                <div class="clear"></div>
            </div>
            <?php
                    
                    $files = $downloads->getFiles(bp_get_group_id());
                    foreach($files as $file)
                    {
            ?>
                <div class="grid_row grey-border-bottom">                    
                    <div class="grid_cell width60P">
                        <?php echo $file->name?><br />
                        <?php echo $file->description?>
                    </div>
                    <div class="grid_cell width15P">
                        <?php
                            echo formatBytes($file->size);
                        ?>
                    </div>
                    <div class="grid_cell width15P">
                        <?php if(bp_group_is_admin()) {?> 
                        <a href="<?php bp_group_permalink()?><?php echo $downloads->slug ?>?_wpnonce=<?php echo wp_create_nonce('groups_downloads_get_file') ?>&id=<?php echo $file->id?>" class="edit-btn">Edit</a>
                        <a href="<?php bp_group_permalink()?><?php echo $downloads->slug ?>?_wpnonce=<?php echo wp_create_nonce('groups_downloads_delete') ?>&id=<?php echo $file->id?>" class="delete-btn">Delete</a>
                        <?php } ?>
                        <a href="<?php bp_group_permalink()?><?php echo $downloads->slug ?>?_wpnonce=<?php echo wp_create_nonce('groups_downloads_download') ?>&id=<?php echo $file->id?>" class="download-btn">Download</a>
                    </div>
                    <div class="clear"></div>
                </div>
            <?php
                }
            ?>
        </div>
        <?php if(bp_group_is_admin()) {?> 
        <form name="newfileform" id="newfileform" action="" enctype="multipart/form-data" method="post">
            <h3>Upload New File(s)</h3>
            <div class="grid_row grid_head grey-border-bottom">
                <div class="grid_cell width20P">Information</div>
                <div class="grid_cell width20P">File</div>
                <div class="grid_cell width10P"></div>
                <div class="clear"></div>
            </div>        
            <div class="grid_row grey-border-bottom">        
                <div class="grid_cell width60P">
                    <label>Name(optional)</label><br />
                    <input type="text" class="input" name="file_name[]" />
                    <label>Description(optional)</label><br />
                    <input type="text" class="input" name="file_description[]" class="text" />            
                    <label>License Agreement</label><br />
                    <textarea cols="20" rows="5" name="file_license[]" class="text"></textarea>
                </div>
                <div class="grid_cell width25P"><input type="file" name="file[]" /></div>
                <div class="grid_cell width10P"><a href="#" class="delete-file">Delete</a></div>
                <div class="clear"></div>
            </div>
            <div class="grid_row grey-border-bottom">        
                <div class="gird_cell width100P">                
                    <a href="#" class="action-btn process-btn" id="save-download"><span class="p"></span><span class="t">Upload and Save</span></a>
                    <a href="#" class="action-btn add-new-btn" id="add-new-download"><span class="p"></span><span class="t">Add New File</span></a>
                </div>
                <div class="clear"></div>
                <div class="message" style="display: none;"></div>
            </div>
            <?php             
                wp_nonce_field('groups_downloads_save'); 
            ?>
        </form>
        <?php } ?>
    <?php } ?>    
</div>