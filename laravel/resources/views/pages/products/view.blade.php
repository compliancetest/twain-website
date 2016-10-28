@extends('app')

@section('content')

<div class="container main-container">
    <div class="main-content product-page">
        <div class="page-title">
            <button onclick="window.print();" class="btn btn-primary btn-with-icon btn-print pull-right">Print</button>
            <h1 class="pull-left">Product Details <a href="/my-products/" class="btn btn-default btn-with-icon btn-back" data-tooltip="tooltip" title="Back to My Products">Back</a></h1>
        </div>
        <div class="product-info">
            <div class="product-identifiers clearfix">
                <div class="pull-left">
                    <div class="product-name">Name: <strong>{{ $product->full_name }}</strong></div>
                    <div class="product-id">(ID: <strong>{{ $product->slug }}</strong>)</div>
                </div>
                <div class="pull-right">
                    @can('change', $product)
                        <a href="/laravel-product/{{ $product->slug }}/edit" class="btn btn-primary btn-with-icon btn-edit">Edit</a>
                        <!-- todo-migration add confirmation popup with confirmed DELETE request to /laravel-product/{{ $produc->slug }}-->
                        <a href="#" class="btn btn-danger btn-with-icon btn-delete">Delete</a>
                    @endcan
                </div>
            </div>
            <ul class="product-attributes">
                <li>Organization: <strong>Panasonic</strong></li>
                <li>Manufacturer: <strong>{{ $product->manufacturer }}</strong></li>
                <li>Release Date: <strong>{{ $product->released_at->format('M Y') }}</strong></li>
                <li>Version: <strong>{{ $product->version }}</strong></li>
                <li>Visibility: <strong>{{ $product->visibility }}</strong></li>
                <li>Product Type: <strong>{{ $product->type }}</strong></li>
                <li>Protocol Version: <strong>{{ $product->protocol_version }}</strong></li>
            </ul>
            <div class="product description">{!! $product->descrition !!}</div>
        </div>


        <h2 class="product-subtitle">Compliance Claims</h2>
        <div class="blue-colored-table-wrapper table-responsive">
            <table class="table blue-colored-table">
                <thead>
                    <tr>
                        <th class="text-left">Claim ID</th>
                        <th>Issuer</th>
                        <th>Suite</th>
                        <th>Level</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Certificate</th>
                    </tr>
                </thead>
                <tbody>
                    @if($product->claims)
                        @foreach($product->claims AS $claim)
                            <tr>
                                <td>{{ $claim->id }}</td>
                                <td class="text-center">{{ $claim->testSuite->issuer }}</td>
                                <td class="text-center"><a href="/test-suite/{{ $claim->testSuite->slug }}" target="_blank">{{ $claim->testSuite->full_name }}</a></td>
                                <td class="text-center">{{ $claim->conformance_level }}</td>
                                <td class="text-center">{{ $claim->role }}</td>
                                <td class="text-center">Verified</td>
                                <td class="text-center">{{ formatDate($claim->created_at) }}</td>
                                <td class="text-center">
                                    <a href="{{ $claim->getPdfUrl() }}" onclick="window.open('{{ $claim->getPdfUrl() }}', '', 'height=600');return false;" class="btn btn-primary btn-with-icon btn-view">View</a>
                                    <a href="{{ $claim->getPdfUrl() }}" class="btn btn-success btn-with-icon btn-download">Download</a>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="8" class="text-center">No claims yet</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

@stop