<div class="popup-box width500" id="delete-file-box">

    <div class="popup-box-header radius6 noradiusbottom">Confirm Deletion</div>

    {{ Form::open(['method' => "DELETE", 'url' => '/downloads/' . $community->slug . '/' .$downloadId]) }}
        <div class="popup-box-content">
            Are you sure that you want to delete this file?
        </div>
        <div class="popup-box-footer radius6 noradiustop">
            <div class="loading loading-with-text radius6"><div><b>DELETING FILE</b><span>Please wait...</span></div></div>
            <a href="#" onclick="jQuery(this).closest('form').submit();" class="action-btn process-btn"><span class="p"></span><span class="t">Confirm</span></a>
            <a href="#" class="action-btn cancel-btn close-popup-btn"><span class="p"></span><span class="t">Cancel</span></a>
            <div class="clear"></div>
        </div>

    {{ Form::close() }}

    <a class="close_btn"></a>

</div>