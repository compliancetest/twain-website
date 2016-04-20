@extends('app')

@section('content')
    <div class="container main-container">

        <div class="main-content">
            <div class="page-title">
                <h1>Communities</h1>
            </div>
            <div class="table-responsive community-lists">
                <table class="table">
                    <thead>
                    <tr>
                        <th colspan="2" class="text-left">Community Name</th>
                        <th class="col-sm-1">Test Suites</th>
                        <th class="col-sm-1 text-left">Members</th>
                        <th>Compliant Products</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                      @foreach($communities as $community)
                            <tr>
                                <td class="community-image">
                                    <a href="{{ getSiteUrl() }}/communities/{{ $community->slug }}"><img width="50" height="50" title="{{ $community->title }}" alt="Community logo of {{ $community->title }}" src="{{ $community->getImageUrl() }}"></a>
                                </td>
                                <td class="community-name">
                                    <a href="{{ getSiteUrl() }}/communities/{{ $community->slug }}">{{ $community->title }}</a>
                                    <p>{{ $community->description }}</p>
                                </td>
                                <td class="text-center">4</td>
                                <td class="text-center">11</td>
                                <td class="text-center">0</td>
                                <td class="text-center community-action">
                                    @include('pages.communities.partials.button', ['community' => $community])
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if(Auth::check())
                <a href="{{ getSiteUrl() }}/communities/create" class="btn btn-success btn-with-icon btn-add">Add Community</a>
            @endif
        </div>
    </div>




    <script>
        Page.communities.init();
    </script>
@stop