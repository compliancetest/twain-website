<div class="popup-box" id="delete-community-box" style="width: 500px; top: 252.5px; display: block;">
    <div class="popup-box-header radius6 noradiusbottom">Confirm Community Membership Cancellation</div>
    {{ Form::open(['class' => 'leave_community_confirm', 'url' => 'communities/popups/'.$community->slug.'/leave', 'method' => 'DELETE']) }}
        <div class="popup-box-content">
            This will cancel your membership of the {{ $community->title }} community. Are you sure?
        </div>
        <div class="popup-box-footer radius6 noradiustop">
            <a href="#" class="action-btn process-btn" onclick="jQuery('.leave_community_confirm').submit()"><span class="p"></span><span class="t">Confirm</span></a>
            <a href="#" class="action-btn cancel-btn close-popup-btn"><span class="p"></span><span class="t">Cancel</span></a>
            <div class="clear"></div>
        </div>
        <a class="close_btn"></a>
    {{ Form::close() }}
</div>