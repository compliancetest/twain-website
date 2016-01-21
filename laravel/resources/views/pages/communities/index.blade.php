@extends('app')

@section('content')
    <div id="content">
        <div class="content container">

            <div id="search_result_block" class="search_result_block">

                <div class="column">

                    <div class="grid dark_gray_txt" id="groups-dir-list">
                        <div class="grid_head grid_head_border">
                            <div class="grid_cell nopaddingtop width30P">Community Name</div>
                            <div class="grid_cell nopaddingtop width15P tocenter">Test Suites</div>
                            <div class="grid_cell nopaddingtop width20P tocenter">Members</div>
                            <div class="grid_cell nopaddingtop width15P tocenter">Compliant Products</div>
                            <div class="grid_cell nopaddingtop width20P tocenter">Action</div>
                            <div class="clear"></div>
                        </div>
                        @foreach($communities as $community)
                            @if($community->status == 'public' || ( $community->status == 'hidden' && $community->creator_id == get_current_user_id() ) )
                                <div class="grid_body" id="groups-list">
                                    <div class="grid_row grid_row_border">
                                        <div class="grid_cell nopaddingtop width30P">
                                            <div class="item-avatar width25P left">
                                                <a href="/communities/{{ $community->slug }}/">
                                                    <img src="{{ @$community->getAllMeta()['logo'] }}" class="avatar group-38-avatar avatar-50 photo" alt="Community logo of {{ $community->title }}" title="{{ $community->title }}" height="50" width="50"></a>
                                            </div>
                                            <div class="width75P left">
                                                <h5><a href="/communities/{{ $community->slug }}/">{{ $community->title }}</a></h5>
                                                <p></p><p>{{ $community->description }}</p>
                                                <p></p>
                                            </div>
                                            <div class="clear"></div>
                                        </div>
                                        <div class="grid_cell nopaddingtop width15P tocenter">
                                            0                                        </div>
                                        <div class="grid_cell nopaddingtop width20P tocenter">{{ count($community->activeMembers()) }}</div>
                                        <div class="grid_cell nopaddingtop width15P tocenter">0</div>
                                        <div class="grid_cell nopaddingtop width20P tocenter">

                                            @include('pages.communities.partials.button')

                                        </div>
                                        <div class="clear"></div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>

                    <div class="space10"></div>
                </div>
            </div>
        </div>
    </div>
@stop