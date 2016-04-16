{{--<div id="agree-file-license" class="popup-box width500">--}}
    {{--<div class="popup-box-header radius6 noradiusbottom">License Agreement</div>--}}
    {{--{{ Form::open(['method' => 'GET', 'id' => 'agree-file-license-form', 'url' => '/downloads/' . $community->slug . '/getfile/'. $download->id]) }}--}}
        {{--<div class="popup-box-content">--}}

                {{--<p>{!! $license !!}</p>--}}

                {{--<label>--}}
                    {{--<input name="agree_license" value="agree_license" id="agree_community_license" autocomplete="off" type="checkbox"> I agree with the License Agreement--}}
                {{--</label>--}}

                {{--<div class="clear"></div>--}}
        {{--</div>--}}
        {{--<div class="popup-box-footer radius6 noradiustop">--}}
            {{--<a href="javascript: void(0)" class="action-btn download-btn get_file"><span class="p"></span><span class="t">DOWNLOAD</span></a>--}}
            {{--<a href="javascript: void(0)" class="action-btn cancel-btn"><span class="p"></span><span class="t">CANCEL</span></a>--}}
            {{--<div class="clear"></div>--}}
            {{--<div class="message error" style="display: none;">Please agree with the License Agreement.</div>--}}
        {{--</div>--}}
        {{--<div class="loading"></div>--}}
        {{--<a id="close-popup-community" class="close_btn"></a>--}}
    {{--{{ Form::close() }}--}}

{{--</div>--}}
<div class="modal fade" id="getDownload" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close-modal" data-tooltip="tooltip" title="Close popup"
                        data-placement="left" data-dismiss="modal" aria-label="Close">Close
                </button>
                License Agreement
            </div>
            <div class="modal-body">
                {{ $license }}
            </div>
            <div class="modal-footer">
                <a class="btn btn-success btn-with-icon btn-confirm">DOWNLOAD</a>
                <button type="button" class="btn btn-default btn-with-icon btn-cancel"
                        data-dismiss="modal">Cancel
                </button>
            </div>
        </div>
    </div>
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