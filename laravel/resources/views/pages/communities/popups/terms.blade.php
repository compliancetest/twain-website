<div id="community-terms-box35" style="top: 50px;" class="popup-box redactor_editor community-terms-box">
    <div class="popup-box-header radius6 noradiusbottom">Terms and Conditions</div>
    <div class="popup-box-content">
        <p>{!! $communityMeta['terms_and_conditions'] !!}</p>
    </div>
    <div class="popup-box-footer radius6 noradiustop">
        <a  href="javascript: void(0);" cp-type="ajax" rel="join-popup" class="action-btn cancel-btn"><span class="p"></span><span class="t">CANCEL</span></a>
        <a  href="javascript: void(0);" cp-type="ajax" rel="join-popup"  class="action-btn process-btn"><span class="p"></span><span class="t">AGREE</span></a>
        <div class="clear"></div>
    </div>
    <div class="loading loading-with-text radius6"><div><b>SENDING REQUEST</b><p>Please wait...</p></div></div>
</div>

<script>
    jQuery( document).ready( function( $ ) {
        jQuery(".process-btn").off("click").cplightbox({'href': '/communities/popups/{{ $community->slug }}/join/1'});
        jQuery(".cancel-btn").off("click").cplightbox({'href': '/communities/popups/{{ $community->slug }}/join'});
    });
</script>