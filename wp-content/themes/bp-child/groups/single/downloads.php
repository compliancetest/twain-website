<?php
/**
* Download Files Page
*/
global $groups_template;
?>
<div id="downloads-container" class="tab-content white_bcg column">
    <h2>Files</h2>
    <div id="uploaded-files">
        <div class="grid_row grid_head grey-border-bottom">
            <div class="grid_cell width60P">Name</div>
            <div class="grid_cell width30P left15">Size</div>
            <div class="grid_cell width5P left15">Action</div>
            <div class="clear"></div>
        </div>
        <?php
                $downloads = new CP_Downloads_Group_Extension();
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
                    <a href="<?php bp_group_permalink()?><?php echo $downloads->slug ?>?_wpnonce=<?php echo wp_create_nonce('groups_downloads_delete') ?>&id=<?php echo $file->id?>" class="delete-btn">Delete</a>
                    <a href="<?php bp_group_permalink()?><?php echo $downloads->slug ?>?_wpnonce=<?php echo wp_create_nonce('groups_downloads_download') ?>&id=<?php echo $file->id?>" class="download-btn">Download</a>
                </div>
                <div class="clear"></div>
            </div>
            <?php
                }
            ?>
    </div>
    <form name="downloadsform" id="downloadsform" action="" enctype="multipart/form-data" method="post">
        <h3>Upload New File(s)</h3>
        <div class="grid_row grid_head grey-border-bottom">
            <div class="grid_cell width20P">File Name</div>
            <div class="grid_cell width40P">Description</div>
            <div class="grid_cell width20P">File</div>
            <div class="grid_cell width10P"></div>
            <div class="clear"></div>
        </div>        
        <div class="grid_row grey-border-bottom">        
            <div class="grid_cell width20P">
                <b>File Name(optional)</b><br />
                <input type="text" class="input" name="file_name[]" />
            </div>
            <div class="grid_cell width40P">
                <b>File Description(optional)</b><br />
                <textarea cols="20" rows="5" name="file_description[]" class="text"></textarea>
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
</div>