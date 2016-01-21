<div class="generic-button group-button">
    @if($userEntry = $community->getMember(get_current_user_id()))
        @if($userEntry->is_confirmed)

            @if($community->isAdmin() && count($community->getAdmins()) == 1)

            @else
                <a href="/communities/popups/{{ $community->slug }}/leave"
                   rel="leave-popup"
                   cp-type="ajax"
                   title="Cancel Membership"
                   class="group-button button button_medium button_red white_txt radius6"
                   style="position: inherit;">Cancel Membership</a>
            @endif

        @else

            <a href="#"
               title="Request Sent"
               class="group-button pending membership-requested button button_medium status_deprecated white_txt radius6"
               style="position: inherit;">Request Sent</a>

        @endif
    @else
        <a href="/communities/popups/{{ $community->slug }}/join"
           cp-type="ajax"
           rel='join-popup'
           title="Join Community"
           class="group-button button button_medium button_red white_txt radius6"
           style="position: inherit;">Join Community</a>
    @endif
</div>

<script>
    jQuery(document).ready(function ($) {
        $("[rel='leave-popup']").off('click').cplightbox({});
        $("[rel='join-popup']").off('click').cplightbox();

    });
</script>