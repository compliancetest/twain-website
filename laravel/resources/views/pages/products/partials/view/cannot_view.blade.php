<div class="product-info">
    <div class="product-identifiers clearfix">
        <div class="pull-left">
            <div class="product-name">Name: <strong>{{ $product->full_name }}</strong></div>
        </div>
        <div class="pull-right">
        </div>
    </div>
    @cannot('view', $product)
        <div class="alert alert-danger">
            The visibility settings defined by the product owner prevent the display of further information.
        </div>
    @endcannot
</div>

