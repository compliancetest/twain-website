<div class="product-info">
    <div class="product-identifiers clearfix">
        <div class="pull-left">
            <div class="product-name">Name: <strong>{{ $product->full_name }}</strong></div>
            <div class="product-id">(ID: <strong>{{ $product->slug }}</strong>)</div>
        </div>
        <div class="pull-right">
            @can('change', $product)
                <a href="/product/{{ $product->slug }}/edit" class="btn btn-primary btn-with-icon btn-edit">Edit</a>
                <button type="button" data-toggle="modal" data-target="#deleteProductModal1" class="btn btn-danger btn-with-icon btn-delete">Delete</button>
                @include('pages.products.partials.confirm-delete-product-modal', ['k' => 1])
            @endcan
        </div>
    </div>
    <ul class="product-attributes">
        <li>Organization: <strong>Panasonic</strong></li>
        <li>Manufacturer: <strong>{{ $product->manufacturer }}</strong></li>
        @if($product->released_at)
            <li>Release Date: <strong>{{ $product->released_at->format('Y-m-d') }}</strong></li>
        @endif
        <li>Version: <strong>{{ $product->version }}</strong></li>
        <li>Visibility: <strong>{{ $product->visibility }}</strong></li>
        <li>Product Type: <strong>{{ $product->type }}</strong></li>
        <li>Protocol Version: <strong>{{ $product->protocol_version }}</strong></li>
        @if($product->access_url)
            <li>Access URL: <a href="{{ $product->access_url }}" target="_blank">{{ $product->access_url }}</a></li>
        @endif
    </ul>

    @if($product->description)
        <div class="product-description">
            <strong>Description:</strong><br/>
            {!! $product->description !!}
        </div>
    @endif

    @if(!empty($product->capabilities->pluck('capability')->toArray()))
        <div class="product-description">
            <strong>Capabilities:</strong><br/>
            {{ implode(', ', $product->capabilities->pluck('capability')->toArray()) }}
        </div>
    @endif
</div>

@if($product->getFeatures())
    <h2 class="product-subtitle">Product Features</h2>
    @foreach($product->getFeatures() as $testSuiteId => $features)
        <div class="blue-colored-table-wrapper table-responsive">
            <table class="table blue-colored-table">
                <thead>
                <tr>
                    <th class="col-sm-4 text-left">Test Suite</th>
                    <th class="text-left">Features</th>
                </tr>
                </thead>
                <tr>
                    <td>{{ \App\LaravelTestSuite::find($testSuiteId)->full_name }}</td>
                    <td>
                        @foreach($features as $feature)
                        <span data-tooltip="tooltip" title="{{ $feature['description'] }}">{{ $feature['name'] }}</span>@if ($feature !== end($features)), @endif
                        @endforeach
                    </td>
                </tr>
            </table>
        </div>
    @endforeach
@endif

<h2 class="product-subtitle">Compliance Claims</h2>
<div class="blue-colored-table-wrapper table-responsive">
    <table class="table blue-colored-table">
        @include('pages.products.partials.claims-list')
    </table>
</div>

@include('pages.products.partials.scripts')