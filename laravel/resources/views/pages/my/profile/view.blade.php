@extends('app')
@section('content')
    <div class="container main-container">
        @include('pages.user-tabs', ['tab' => 'my-profile'])
        <div class="main-content">
            Profile page
        </div>
    </div>
@stop