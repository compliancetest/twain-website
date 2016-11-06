<div class="product-info">
    <div class="product-identifiers clearfix">
        <div class="pull-left">
            <div class="product-name">Name: <strong>{{ $product->full_name }}</strong></div>
            <div class="product-id">(ID: <strong>{{ $product->slug }}</strong>)</div>
        </div>
        <div class="pull-right">
            @can('change', $product)
                <a href="/laravel-product/{{ $product->slug }}/edit" class="btn btn-primary btn-with-icon btn-edit">Edit</a>
                <button type="button" data-toggle="modal" data-target="#deleteProductModal1" class="btn btn-danger btn-with-icon btn-delete">Delete</button>
                @include('pages.products.partials.confirm-delete-product-modal', ['k' => 1])
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
        @include('pages.products.partials.claims-list')
    </table>
</div>