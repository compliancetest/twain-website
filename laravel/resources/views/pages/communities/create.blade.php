@extends('app')

@section('content')
    <div id="content">
        <div class="padder">

            @include('pages.communities.partials.errors')


            {!! Form::open(['id'=> 'create-group-form', 'class' => 'standard-form', 'action' => 'CommunitiesController@store']) !!}

                <div class="page-title-block column">
                    <h2 class="nomarginbottom left">
                        Create a Community
                    </h2>
                    <a class="right top10" href="/communities/"><b>Communities</b></a>

                    <div class="clear"></div>
                </div>

                <div class="column">
                    <div class="tabs_wrap radius6 light_gray_bcg system-section">
                        <div class="item-list-tabs no-ajax" id="group-create-tabs" role="navigation">
                            <ul>

                                <li class="current">
                                    <a href="/communities/create/">
                                        1.Details
                                    </a>
                                </li>
                                <li><span>2. Settings</span></li>
                                <li><span>3. Forum</span></li>
                                <li><span>4. Wiki</span></li>
                                <li><span>5. Avatar</span></li>
                                <li><span>6. Invites</span></li>
                            </ul>
                            <div class="clear"></div>
                        </div>

                        {!! Form::hidden('current-step', 'group-details') !!}

                        <div class="item-body tab-content white_bcg padding10" id="group-create-body">

                            @include('pages.communities.partials.steps.group-details')

                        </div>

                    </div>
                </div>

            {!! Form::close() !!}

        </div>
    </div>
@stop