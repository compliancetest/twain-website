@extends('app')
@section('content')
    <div class="container main-container">
        @include('pages.user-tabs', ['tab' => 'my-organisation'])
        <div class="main-content">
            organisation tabs
        </div>
    </div>
@stop