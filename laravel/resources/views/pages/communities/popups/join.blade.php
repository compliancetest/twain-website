<div style="width: 305px; top: 216px;" class="popup-box community-registration-box">
    <div class="popup-box-header radius6 noradiusbottom">Community Registration</div>
    <div class="popup-box-content">

        {{ Form::open(['class' => 'join-community-form', 'url' => getSiteUrl() . 'membership/'.$community->slug.'/request']) }}

            <div class="grey-border-bottom">
                <p>You need to join the community of interest in order to view Test Cases and Participate in the Forum</p>
            </div>
            <div class="top10">
                <input name="agree_terms" value="agree" id="agree_community_terms" type="checkbox" @if($isChecked) checked="checked" value="1" @endif> I agree with
                <a class="terms_box" href="javascript: void(0);" cp-type="ajax" rel="join-popup">Terms &amp; Conditions</a>
                <div class="clear"></div>
                <div class="space5"></div>
            </div>
            <div class="clear"></div>

        {{ Form::close() }}
    </div>

    <div class="popup-box-footer radius6 noradiustop">
        <a href="javascript: void(0)" class="action-btn process-btn"><span class="p"></span><span class="t">REGISTER</span></a>
        <a href="javascript: void(0)" class="action-btn cancel-btn close-popup-btn"><span class="p"></span><span class="t">CANCEL</span></a>
        <div class="clear"></div>

        <div class="message error" style="display:none;">You must agree the community Terms &amp; Conditions.</div>

    </div>
    <div class="loading loading-with-text radius6"><div><b>SENDING REQUEST</b><p>Please wait...</p></div></div>
    <a id="close-popup-community" class="close_btn"></a>
</div>
<script>
    jQuery( document).ready( function( $ ) {
        jQuery(".terms_box").off("click").cplightbox({'href': '/communities/popups/{{ $community->slug }}/terms'});
        $('.process-btn').on('click', function(){
            if($('#agree_community_terms').attr('checked')) {
                $('.join-community-form').submit();
            } else {
                $('.message').show();
            }
        });
    });
</script>