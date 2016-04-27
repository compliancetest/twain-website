@extends('app')

@section('content')
    <div class="container main-container">
        <div class="main-content">

            <div class="community-header row">

                @include('pages.communities.partials.button', ['community' => $community])

                <div class="community-logo">
                    <img src="{{ $community->getImageUrl() }}" alt="{{ $community->title }}"/>
                </div>
                <div class="community-short-description">
                    <h3>{{ $community->title }}</h3>

                    <p>{{ $community->description }}</p>
                </div>



            </div>

            <div class="community-tabs">

                @include('pages.communities.partials.community-nav')

                <div class="community-tab-content">

                    @if(Auth::check() && $community->hasAccess())
                        @include('pages.communities.partials.show.'.$action)
                    @else
                        @include('pages.communities.partials.show.tab-content-notlogged')
                    @endif

                </div>
            </div>


        </div>
    </div>
    <script>
        Page.communities.init();
    </script>
@stop