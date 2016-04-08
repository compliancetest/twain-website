@extends('app')

@section('content')
    <div id="content" xmlns="http://www.w3.org/1999/html">
        <div class="padder">

            {!! Form::model($community, ['id'=> 'create-group-form', 'class' => 'standard-form', 'files' => true, 'method' => 'PATCH', 'action' => ['CommunitiesController@update', 'id' => $community->slug]]) !!}

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

                        <div class="left-menu">
                            <img src="{{ @$communityMeta['logo'] }}" class="avatar group-38-avatar avatar-photo" alt="Community avatar" height="150" width="150">
                        </div><!-- .left-menu -->

                        <div class="main-column">
                            <p>Upload an image to use as an avatar for this community. The image will be shown on the main community page, and in search results.</p>

                            <p>
                                {!! Form::file('image', null) !!}
                            </p>
                            <div class="clear paddingbottom10"></div>
                            <p>To skip the avatar upload process, hit the "Next Step" button.</p>
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

            {!! Form::hidden('redirect', '/communities/edit/' .$community->slug. '/step/group-invites/') !!}

            {!! Form::close() !!}

            @include('pages.communities.partials.errors')

        </div>
    </div>
@stop