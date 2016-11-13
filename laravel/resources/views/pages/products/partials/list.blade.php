<div class="colored-box collapsible-box">
    <div class="colored-box-header">
        <a href="#productItemBox{{ $productType }}" class="collapse-arrow" data-toggle="collapse"><span class="glyphicon glyphicon-triangle-bottom"></span><span
                    class="glyphicon glyphicon-triangle-right"></span></a>
        {{ $productType }} Products
    </div>
    <div class="colored-box-body collapse in" id="productItemBox{{ $productType }}">
        <div class="colored-box-content product-item-box-content">
            @if(count($products))
                @foreach($products as $k => $product)
                    <div class="colored-box collapsible-box">
                        <div class="colored-box-header">
                            <a href="#productItemBox{{ $k }}" class="collapse-arrow" data-toggle="collapse"><span class="glyphicon glyphicon-triangle-bottom"></span><span
                                        class="glyphicon glyphicon-triangle-right"></span></a>
                            Product: <a href="/product/{{ $product->slug }}"><strong>{{ $product->full_name }} ({{ $product->protocol_version }})</strong></a>
                            <ul class="colored-box-header-actions">
                                <li><a href="/product/{{ $product->slug }}/edit" data-tooltip="tooltip" title="Edit"><span class="edit-icon"></span></a></li>
                                <li><a href="#" data-tooltip="tooltip" title="Delete" data-toggle="modal" data-target="#deleteProductModal{{ $k }}"><span class="delete-icon"></span></a></li>
                            </ul>
                        </div>
                        @include('pages.products.partials.confirm-delete-product-modal', ['k' => $k, 'productSlug'=>$product->slug])
                        <div class="colored-box-body collapse in" id="productItemBox{{ $k }}">
                            <div class="colored-box-content">
                                <table class="table colored-table">
                                    @include('pages.products.partials.claims-list')
                                </table>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="colored-box collapsible-box">
                        <div class="colored-box-body">
                            <div class="colored-box-content">
                                <table class="table colored-table">
                                    <tr>
                                        <td class="text-center">You do not have products with "{{ $productType }}" type yet</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
            @endif
        </div>
    </div>
</div>