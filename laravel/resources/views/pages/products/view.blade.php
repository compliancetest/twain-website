@extends('app')

@section('content')

    <div class="container main-container">
        <div class="main-content product-page">
            <div class="page-title">
                <button onclick="window.print();" class="btn btn-primary btn-with-icon btn-print pull-right">Print</button>
                <h1 class="pull-left">Product Details <a href="/my-products/" class="btn btn-default btn-with-icon btn-back" data-tooltip="tooltip"
                                                         title="Back to My Products">Back</a></h1>
            </div>
            @can('view', $product)
                @include('pages.products.partials.view.can_view')
            @endcan
            @cannot('view', $product)
                @include('pages.products.partials.view.cannot_view')
            @endcannot
        </div>
    </div>

@stop