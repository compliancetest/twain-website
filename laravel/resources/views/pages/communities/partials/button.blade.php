@if(Auth::check())
    <div class="community-membership-action">
        @if($userEntry = $community->getMember(Auth::user()->ID))
            @if($userEntry->is_confirmed)

                @if(!($community->isAdmin() && count($community->getAdmins()) == 1))
                    <a class="btn btn-danger btn-lg joinCommunity" href="#confirmCancelMembership{{ $community->slug }}">Cancel Membership</a>
                @endif

            @else
                <span class="status status-sent status-lg">Request Sent</span>
            @endif
        @else
            <a class="btn btn-danger btn-lg joinCommunity" href="#confirmJoinCommunity{{ $community->slug }}">Join Community</a>
        @endif
    </div>

    <!-- Community Join Modal -->
    <div class="modal modal-small fade" id="confirmJoinCommunity{{ $community->slug}}" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content block-loading-wrapper">
                <div class="modal-header">
                    <button type="button" class="close-modal" data-placement="left" data-dismiss="modal" aria-label="Close">Close</button>
                    Community Registration
                </div>
                <div class="modal-body">
                    You need to join the community of interest in order to view Test Cases
                    <div class="popup-terms-box">
                        <input type="checkbox" id="agree_community_terms_{{ $community->slug }}" value="agree" name="agree_terms"> I agree with <a href="#readTermsAndConditions{{ $community->slug }}" data-toggle="modal" data-dismiss="modal" data-backdrop="static" data-keyboard="false">Terms &amp; Conditions</a>
                    </div>
                    <div class="error-message error-message_{{ $community->slug }}" style="display: none;">You must agree the community Terms &amp; Conditions.</div>
                </div>
                <div class="modal-footer">
                    <a class="btn btn-success btn-with-icon btn-confirm registerInCommunity" data-community-id="{{ $community->slug }}">Register</a>
                    <button type="button" class="btn btn-default btn-with-icon btn-cancel" data-dismiss="modal">Cancel</button>
                </div>
                <div class="block-loading"><div class="loading-content"><span class="loader"></span><div class="loading-text">SENDING REQUEST</div><div class="loading-wait">Please wait...</div></div></div>
            </div>
        </div>
    </div>

    <div class="modal fade readTermsModal" id="readTermsAndConditions{{ $community->slug }}" data-target="#confirmJoinCommunity{{ $community->slug }}" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close-modal" data-placement="left" data-target="#confirmJoinCommunity{{ $community->slug }}" data-toggle="modal" data-dismiss="modal" aria-label="Close">Close</button>
                    Terms and Conditions
                </div>
                <div class="modal-body">
                    <div class="modal-terms-content">
                        {!! @$community->meta->keyBy('meta_key')->get('terms_and_conditions')->meta_value !!}
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-with-icon btn-cancel" data-target="#confirmJoinCommunity{{ $community->slug }}" onclick="document.getElementById('agree_community_terms_{{ $community->slug }}').checked = false;" data-toggle="modal" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-success btn-with-icon btn-confirm" onclick="document.getElementById('agree_community_terms_{{ $community->slug }}').checked = true; $('.error-message_{{ $community->slug }}').hide();" data-target="#confirmJoinCommunity{{ $community->slug }}" data-toggle="modal" data-dismiss="modal">Agree</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirm Membership Cancellation -->
    <div class="modal fade" id="confirmCancelMembership{{ $community->slug }}" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content block-loading-wrapper">
                <div class="modal-header">
                    <button type="button" class="close-modal" data-tooltip="tooltip" title="Close popup" data-placement="left" data-dismiss="modal" aria-label="Close">Close</button>
                    Confirm Community Membership Cancellation
                </div>
                <div class="modal-body">
                    This will cancel your membership of the {{ $community->title }} community. Are you sure?
                </div>
                <div class="modal-footer">
                    <a class="btn btn-success btn-with-icon btn-confirm cancelMembershipInCommunity" data-community-id="{{ $community->slug }}">Confirm</a>
                    <button type="button" class="btn btn-default btn-with-icon btn-cancel" data-dismiss="modal">Cancel</button>
                </div>
                <div class="block-loading"><div class="loading-content"><span class="loader"></span><div class="loading-text">Loading</div><div class="loading-wait">Please wait...</div></div></div>
            </div>
        </div>
    </div>


@endif