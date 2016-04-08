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


                        <div id="message" class="info">
                            <p>Once you have built up friend connections you will be able to invite others to your community.</p>
                        </div>


                        <div class="submit" id="previous-next">

                            <button type="submit" class="action-btn process-btn" id="group-creation-finish" name="save">
                                <span class="p"></span>
                                <span class="t">Confirm</span>
                            </button>

                            <div class="clear"></div>
                        </div>

                    </div>

                </div>
            </div>

            {!! Form::hidden('redirect', '/communities/') !!}

            {!! Form::close() !!}

            @include('pages.communities.partials.errors')

        </div>
    </div>
@stop