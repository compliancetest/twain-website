@extends('app')

@section('content')
    <div id="content">
        <div class="padder">

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

                        <h4>Community Forum</h4>

                        <p>Create a discussion forum to allow members of this community to communicate in a structured,
                            bulletin-board style fashion.</p>

                        <div class="checkbox">
                            <label><input name="community-forum" id="community-forum" @if($community->getMeta('forum_id')) checked="checked" @endif value="1"
                                          type="checkbox"> Yes. I want this community to have a forum.</label>
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

            {!! Form::hidden('redirect', '/communities/edit/' .$community->slug. '/step/wiki/') !!}

            {!! Form::close() !!}

            @include('pages.communities.partials.errors')

        </div>
    </div>
@stop