<div class="item-list-tabs no-ajax" id="group-create-tabs" role="navigation">
    <ul>

        <li @if($step == 'group-details') class="current" @endif>
            <a href="/communities/edit/{{ $community->slug }}/step/group-details/">
                1.Details
            </a>
        </li>
        <li @if($step == 'group-settings') class="current" @endif>
            <a href="/communities/edit/{{ $community->slug }}/step/group-settings/">
                2.Settings
            </a>
        </li>
        <li @if($step == 'forum') class="current" @endif>
            <a href="/communities/edit/{{ $community->slug }}/step/forum/">
                3. Forum
            </a>
        </li>
        <li @if($step == 'wiki') class="current" @endif>
            <a href="/communities/edit/{{ $community->slug }}/step/wiki/">
                4. Wiki
            </a>
        </li>
        <li @if($step == 'group-avatar') class="current" @endif>
            <a href="/communities/edit/{{ $community->slug }}/step/group-avatar/">
                5. Avatar
            </a>
        </li>
        <li @if($step == 'group-invites') class="current" @endif>
            <a href="/communities/edit/{{ $community->slug }}/step/group-invites/">
                6. Invites
            </a>
        </li>

    </ul>
    <div class="clear"></div>
</div>