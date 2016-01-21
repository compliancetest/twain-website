@extends('app')

@section('content')
    <div id="content">
        <div class="padder">

            @include('pages.communities.partials.errors')

            {!! Form::model($community, ['id'=> 'create-group-form', 'class' => 'standard-form', 'method' => 'PATCH', 'action' => ['CommunitiesController@update', 'id' => $community->slug]]) !!}

            <div class="page-title-block column">
                <h2 class="nomarginbottom left">
                    Create a Community
                </h2>
                <a class="right top10" href="/communities/"><b>Communities</b></a>

                <div class="clear"></div>
            </div>

            <div class="column">
                <div class="tabs_wrap radius6 light_gray_bcg system-section">

                    @include('pages.communities.edit.partials.tabs')

                    <div class="item-body tab-content white_bcg padding10" id="group-create-body">

                        <h4>Privacy Options</h4>

                        <div class="radio">
                            <label><input name="status" value="public" @if($community['status'] == 'public') checked="checked" @endif type="radio">
                                <strong>This is a public community</strong>
                                <ul>
                                    <li>Any site member can join this community.</li>
                                    <li>This community will be listed in the communities directory and in search
                                        results.
                                    </li>
                                    <li>Community content and activity will be visible to any site member.</li>
                                </ul>
                            </label>

                            <label><input name="status" value="private" {!! isChecked('private', @$community->status) !!} type="radio">
                                <strong>This is a private community</strong>
                                <ul>
                                    <li>Only users who request membership and are accepted can join the community.</li>
                                    <li>This community will be listed in the communities directory and in search
                                        results.
                                    </li>
                                    <li>Community content and activity will only be visible to members of the
                                        community.
                                    </li>
                                </ul>
                            </label>

                            <label><input name="status" value="hidden" {!! isChecked('hidden', @$community->status) !!} type="radio">
                                <strong>This is a hidden community</strong>
                                <ul>
                                    <li>Only users who are invited can join the community.</li>
                                    <li>This community will not be listed in the communities directory or search
                                        results.
                                    </li>
                                    <li>Community content and activity will only be visible to members of the
                                        community.
                                    </li>
                                </ul>
                            </label>
                        </div>

                        <h4>Community Invitations</h4>

                        <p>Which members of this community are allowed to invite others?</p>

                        <div class="radio">
                            <label>
                                <input name="group-invite-status" value="members" {!! isChecked('members', @$communityMeta['invite_status']) !!} type="radio">
                                <strong>All community members</strong>
                            </label>

                            <label>
                                <input name="group-invite-status" value="mods" {!! isChecked('mods', @$communityMeta['invite_status']) !!} type="radio">
                                <strong>Community admins and mods only</strong>
                            </label>

                            <label>
                                <input name="group-invite-status" value="admins" {{ isChecked('admins', @$communityMeta['invite_status']) }} type="radio">
                                <strong>Community admins only</strong>
                            </label>
                        </div>

                        <div class="submit" id="previous-next">

                            <button type="submit" class="action-btn next-btn" id="group-creation-next" name="save">
                                <span class="p"></span>
                                <span class="t">Next</span>
                            </button>

                            <div class="clear"></div>
                        </div>

                    </div>

                </div>
            </div>


            {!! Form::hidden('redirect', '/communities/edit/' .$community->slug. '/step/forum/') !!}

            {!! Form::close() !!}

        </div>
    </div>
@stop