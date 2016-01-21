@extends('app')

@section('content')
    <div id="content">
        <div class="padder">

            @include('pages.communities.partials.errors')

            {!! Form::model($community, ['id'=> 'create-group-form', 'class' => 'standard-form', 'method' => 'PATCH', 'action' => ['CommunitiesController@update', $community->slug]]) !!}

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

                            @include('pages.communities.partials.steps.group-details')

                        </div>

                    </div>
                </div>

            {!! Form::hidden('redirect', '/communities/edit/' .$community->slug. '/step/group-settings/') !!}

            {!! Form::close() !!}

        </div>
    </div>
@stop