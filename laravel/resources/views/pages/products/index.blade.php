@extends('app')

@section('content')
    <div class="container main-container">
        <div class="container main-container">

            @include('pages.user-tabs', ['tab' => 'my-products'])

            <div class="main-content products-list">

                @include('pages.products.partials.list', ['products' => $applicationProducts, 'productType' => 'Application'])
                @include('pages.products.partials.list', ['products' => $dataSourceProducts, 'productType' => 'DataSource'])

            </div>
        </div>
    </div>
@stop