@extends('app')

@section('content')
    <div class="padder">

        <div id="item-header" role="complementary">

            <div id="issuer_title_block" class="page-title-block">
                <div class="column four_fifths left">
                    <div id="item-header-avatar" class="page-title-avatar">
                        <a href="/communities/{{ $community->slug }}" title="{{ $community->title }}">

                            <img src="{{ @$communityMeta['logo'] }}" class="avatar group-35-avatar avatar- photo" alt="Community logo of {{ $community->title }}" title="{{ $community->title }}" height="150" width="150">
                        </a>
                    </div><!-- #item-header-avatar -->

                    <div id="item-header-content" class="page-title-content redactor_editor">
                        <h3 class="dark_gray_txt">{{ $community->title }}</h3>
                        {{ $community->description }}

                    </div>
                </div>
                <div class="fifth right">
                    <div id="item-buttons" class="page-title-buttons">
                        @include('pages.communities.partials.button')
                    </div>
                </div>
                <div class="clear"></div>
            </div>
        </div>

        <div id="issuer_content_block" class="column">
            <div class="tabs_wrap light_gray_bcg radius6">

                <div id="item-nav">
                    <div class="item-list-tabs no-ajax" id="object-nav" role="navigation">
                        @include('pages.communities.partials.community-nav')
                        <div class="clear"></div>
                    </div>
                </div>

                <div id="item-body">
                    @include('pages.communities.partials.show.'.$action)
                </div>
            </div>
        </div>

    </div>
@stop