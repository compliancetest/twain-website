<?php
/**
* Download Files Page
*/
?>
<div id="downloads-container" class="tab-content white_bcg column">
    <div class="grid_row grid_head grey-border-bottom">
        <div class="grid_cell width60P">Name</div>
        <div class="grid_cell width30P left15">Size</div>
        <div class="grid_cell width5P left15">Action</div>
        <div class="clear"></div>
    </div>
    <form name="downloadsform" id="downloadsform" action="">
        <h3>Upload New File(s)</h3>
        <div class="grid_row grid_head grey-border-bottom">
            <div class="grid_cell width20P">File Name</div>
            <div class="grid_cell width45P">Description</div>
            <div class="grid_cell width20P">File</div>
            <div class="grid_cell width10P"></div>
            <div class="clear"></div>
        </div>
        <div class="grid_row grey-border-bottom">        
            <div class="grid_cell width20P">
                <b>File Name(optional)</b><br />
                <input type="text" class="input" name="file_name[]" />
            </div>
            <div class="grid_cell width45P">
                <b>File Description(optional)</b><br />
                <textarea cols="20" rows="5" name="file_description[]" class="text"></textarea>
            </div>
            <div class="grid_cell width20P"><input type="file" name="file[]" /></div>
            <div class="grid_cell width10P"><a href="#" class="delete-file">Delete</a></div>
            <div class="clear"></div>
        </div>
        <div class="grid_row grey-border-bottom">        
            <div class="gird_cell width100P">
                <a href="#" class="action-btn add-new-btn"><span class="p"></span><span class="t">Add New File</span></a>
            </div>
            <div class="clear"></div>
        </div>
        
    </form>
</div>