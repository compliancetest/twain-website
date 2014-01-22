<?php
/**
* Download Files Page
*/
global $groups_template;

$downloads = new CP_Downloads_Group_Extension();

?>
<div id="downloads-container" class="tab-content white_bcg padding10">    
    <!-- Files List Page -->
    <div id="uploaded-files">
<!--          <form name="fileListForm" action="" method="post">-->
        <div class="grid-list">
            <div class="grid-list-row grid-list-header">
                <div class="grid-list-cell width40P">File Name</div>
                <div class="grid-list-cell width15P tocenter">Size</div>
                <div class="grid-list-cell grid-list-cell-line2 tocenter width15P">License<br />Agreement</div>
                <div class="grid-list-cell width20P tocenter">Last Updated</div>
                <div class="clear"></div>
            </div>                          
            <?php                    
                $files = $downloads->getFiles(bp_get_group_id());
                foreach($files as $file)
                {
            ?>
            <div class="grid-list-row" id="fileRow<?php echo $file->id?>">
                <div class="grid-list-cell width40P">
                    <?php 
                        if($file->license){
                    ?>                    
                    <a href="<?php bp_group_permalink()?><?php echo $downloads->slug?>?_wpnonce=<?php echo wp_create_nonce('groups_downloads_show_license')?>&id=<?php echo $file->id?>" class="download-link" rel="has-license"><?php echo $file->name?></a><br />
                    <?php }else{ ?>
                    <a href="<?php bp_group_permalink()?><?php echo $downloads->slug?>?_wpnonce=<?php echo wp_create_nonce('groups_downloads_download')?>&id=<?php echo $file->id?>" class="download-link"><?php echo $file->name?></a><br />
                    <?php } ?>
                    <?php if($file->version){ ?>
                    Version: <b><?php echo $file->version?></b> <?php echo $file->version_description ? "(" . $file->version_description . ")" : "" ?><br />
                    <?php } ?>
                    <?php echo $file->description?>
                </div>
                <div class="grid-list-cell width15P tocenter">
                    <?php echo formatBytes($file->size); ?>
                </div>
                <div class="grid-list-cell grid-list-cell-line2 tocenter width15P">
                    <?php
                        if($file->license){
                    ?>
                    <a href="<?php bp_group_permalink()?><?php echo $downloads->slug?>?_wpnonce=<?php echo wp_create_nonce('groups_downloads_show_license')?>&id=<?php echo $file->id?>" class="license-link has-license download-link" rel="has-license">License<br />Agreement<span class="simple_tooltip"><span></span>To download this file<br />you have to read &<br />agree Licence Agreement</span></a>
                    <?php                                
                        }else{
                    ?>
                    <a href="<?php bp_group_permalink()?><?php echo $downloads->slug?>?_wpnonce=<?php echo wp_create_nonce('groups_downloads_download')?>&id=<?php echo $file->id?>" class="license-link download-link">License<br />Agreement</a>
                    <?php
                        }
                    ?>
                </div>
                <div class="grid-list-cell width20P tocenter"><?php echo date('M d, Y', strtotime($file->last_updated)) ?></div>
                <div class="grid-list-cell width10P">
                    <?php if(bp_group_is_admin()) { ?>
                        <a href="<?php bp_group_permalink()?><?php echo $downloads->slug?>?_wpnonce=<?php echo wp_create_nonce('groups_downloads_get_file')?>&id=<?php echo $file->id?>" class="action-btn blue-edit-btn icon-btn" data-id="<?php echo $file->id?>"><span class="p"></span><span class="simple_tooltip"><span></span>Edit</span></a>
                        <a href="<?php bp_group_permalink()?><?php echo $downloads->slug?>?_wpnonce=<?php echo wp_create_nonce('groups_downloads_delete')?>&id=<?php echo $file->id?>" class="action-btn delete-btn icon-btn no-submit" data-id="<?php echo $file->id?>"><span class="p"></span><span class="simple_tooltip"><span></span>Delete</span></a>
                    <?php } ?>                        
                </div>
                <div class="clear"></div>
            </div>                                
        <?php
            }
            if(!$files)
            {
        ?>
            <div class="grid-list-row">
                <div class="grid-list-cell tocenter width100P">
                    No file uploaded yet
                </div>
                <div class="clear"></div>
            </div>
        <?php
            }
        ?>
        <?php if(bp_group_is_admin()) {?> 
            <div class="grid-list-footer grid-list-row">                    
                <div class="grid-list-cell width100P">
                    <a href="#" id="add-new-download" class="large-plus-link">Upload New File(s)</a>
                </div>
                <div class="clear"></div>
            </div>
        <?php } ?>
         </div> 
<!--          </form>-->
    </div>
    <?php if(bp_group_is_admin()) {?> 
    <div id="new-downloads" style="display: none;">
        <form name="newfileform" id="newfileform" action="" enctype="multipart/form-data" method="post">
            <h3>Upload New File(s)</h3>
            <div class="grid-list">
                <div class="grid-list-row">
                    <div class="grid-list-cell width35P">
                        <input type="file" name="file[]" class="input-file" />
                        <a href="#" class="action-btn delete-btn" id="cancel-download"><span class="p"></span><span class="t">Remove</span></a>    
                    </div>
                    <div class="grid-list-cell left15 grid-field-cell">
                        <label>File Name:</label>
                        <input type="text" class="input" name="file_name[]" /><br clear="all">
                        <label>File Version:</label>
                        <input type="text" class="input" name="file_version[]" value="" /><br clear="all">
                        <label>Description:</label>
                        <input type="text" class="input" name="file_description[]" class="text" /><br clear="all">
                        <label>File License Agreement:</label>
                        <textarea cols="20" rows="5" name="file_license[]" class="text"></textarea>                            
                    </div>
                    <div class="clear"></div>
                </div>
                <div class="grid-list-footer grid-list-row">                                        
                    <a href="#" id="add-more-file" class="large-plus-link small-plus-link">Add New File</a>
                    <a href="#" class="action-btn cancel-btn" id="cancel-download"><span class="p"></span><span class="t">Cancel</span></a>
                    <a href="#" class="action-btn upload-btn" id="save-download"><span class="p"></span><span class="t">Upload &amp; Save</span></a>
                    <div class="clear"></div>
                    <div class="message" style="display: none;"></div>
                    
                </div>
            </div>                
            <?php             
                wp_nonce_field('groups_downloads_save'); 
            ?>
        </form>
    </div>        
    <?php } ?>     
</div>
