<div id="agree-file-license" class="popup-box width500">
    <div class="popup-box-header radius6 noradiusbottom">License Agreement</div>
    {{ Form::open(['method' => 'GET', 'id' => 'agree-file-license-form', 'url' => '/downloads/' . $community->slug . '/getfile/'. $download->id]) }}
        <div class="popup-box-content">

                <p>{!! $license !!}</p>

                <label>
                    <input name="agree_license" value="agree_license" id="agree_community_license" autocomplete="off" type="checkbox"> I agree with the License Agreement
                </label>

                <div class="clear"></div>
        </div>
        <div class="popup-box-footer radius6 noradiustop">
            <a href="javascript: void(0)" class="action-btn download-btn get_file"><span class="p"></span><span class="t">DOWNLOAD</span></a>
            <a href="javascript: void(0)" class="action-btn cancel-btn"><span class="p"></span><span class="t">CANCEL</span></a>
            <div class="clear"></div>
            <div class="message error" style="display: none;">Please agree with the License Agreement.</div>
        </div>
        <div class="loading"></div>
        <a id="close-popup-community" class="close_btn"></a>
    {{ Form::close() }}

</div>
<script>

    jQuery('.get_file').on('click', function(){
        jQuery('.message').hide();
        if(jQuery('#agree_community_license').prop('checked')) {
           location.href = '/downloads/{{ $community->slug }}/getfile/{{ $download->id }}';
        } else {
            jQuery('.message').show();
            return false;
        }
    });
</script>