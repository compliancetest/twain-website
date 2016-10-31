@extends('app')

@section('content')

    <div class="container main-container">
        <div class="main-content products">
            <div class="page-title">
                <h1>Edit product</h1>
            </div>
            <div class="edit-product">
                <div class="colored-box">
                    <div class="colored-box-header">Information</div>
                    <div class="colored-box-body">
                        {!! Form::model($product, ['id'=> 'edit-product-form', 'data-save-method' => 'ajax', 'data-redirect-after-submit' => '/laravel-product/' . $product->slug, 'method' => 'POST', 'url' => '/laravel-product/'.$product->slug]) !!}
                            <div class="colored-box-content">
                                <div class="row">
                                    <div class="col-sm-5">

                                        <div class="form-group">
                                            <label for="productName">Name:</label>
                                            <input type="text" class="form-control" id="productName" required="required" name="product_name" value="{{ $product->name }}" data-tooltip="tooltip" title="Enter your product or service name as it is known in the marketplace." disabled="disabled">
                                        </div>

                                        <div class="form-group">
                                            <label for="productVersion">Version:</label>
                                            <input type="text" class="form-control" id="productVersion" required="required" name="product_version" value="{{ $product->version }}" disabled="disabled">
                                        </div>

                                        <div class="form-group organization-group">
                                            <label for="productOrganization">Organization:</label>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="product-owner-override">
                                                        <input class="form-control" name="product_owner_override" id="productOwnerOverride" value="{{ \App\Organisation::find($product->organisation_id)->organisation_name }}" title="The owner of your product or service. It is set to the same as the organisation name from your profile." disabled="disabled">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="productManufacturer">Manufacturer:</label>
                                            <input type="text" class="form-control" id="productManufacturer" required="required" name="product_manufacturer" value="{{ $product->manufacturer }}" disabled="disabled">
                                        </div>

                                        <div class="form-group">
                                            <label for="productVisibility">Visibility:</label>
                                            <select name="visibility" id="productVisibility" class="form-control" data-tooltip="tooltip" title="'Public' means anyone can see this product, 'Community' means visibility is limited to community members, 'Private' means that the product is only visible to your own organisation">
                                                <option value="Public" @if($product->visibility == 'Public') selected="selected" @endif>Public</option>
                                                <option value="Community" @if($product->visibility == 'Community') selected="selected" @endif>Community</option>
                                                <option value="Private" @if($product->visibility == 'Private') selected="selected" @endif>Private</option>
                                            </select>
                                        </div>

                                    </div>

                                    <div class="col-sm-7">
                                        <div class="form-group">
                                            <label for="productReleaseDate">Release Date:</label>
                                            <div class="input-group col-sm-6 col-md-4">
                                                <input type="text" class="form-control" id="productReleaseDate" required="required" name="release_date" value="{{ formatDate($product->released_at) }}"
                                                       readonly data-provide="datepicker" data-date-autoclose="true" data-date-format="yyyy-mm-dd"
                                                       data-tooltip="tooltip" title="Enter the date that this version of your product or service was released to the market."
                                                >
                                                <div class="input-group-addon productReleaseCalendar"><span class="calendar-icon"></span></div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="productAccessUrl">Access URL:</label>
                                            <input type="text" class="form-control" id="productAccessUrl" name="access_url" value="{{ $product->access_url }}" data-tooltip="tooltip" title="Provide a link to the page on your website that describes this product.">
                                        </div>

                                        <div class="form-group">
                                            <label for="productDescription">Description:</label>
                                            <textarea class="form-control" id="productDescription" required="required" name="description" data-tooltip="tooltip" title="Provide a few paragraphs to describe your product or service. This information is displayed to users who may be searching CompliacneTest for certified products.">{{ $product->description }}</textarea>
                                        </div>

                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-5">

                                        <div class="form-group">
                                            <label for="productType">Product Type:</label>
                                            <select name="product_type" id="productType" class="form-control" disabled="disabled">
                                                <option value="DataSource" @if($product->type == 'DataSource') selected="selected" @endif>DataSource</option>
                                                <option value="Application" @if($product->type == 'Application') selected="selected" @endif>Application</option>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="productVersion">Protocol Version:</label>
                                            <input type="text" class="form-control" id="productVersion" required="required" name="product_version" value="{{ $product->version }}" disabled="disabled">
                                        </div>

                                    </div>
                                    <div class="col-sm-7">
                                        <div class="form-group">
                                            @if($product->type == 'DataSource')
                                                <label for="productDescription">Capabilities(comma separated):</label>
                                                <textarea class="form-control" id="productDescription" required="required" name="product_capabilities"
                                                          disabled="disabled">{{ implode(', ', $product->capabilities->pluck('capability')->toArray()) }}</textarea>

                                            @else
                                                <label>Product Features:</label>
                                                @foreach($product->getFeatures() as $testSuiteId => $features)
                                                    <div class="suite-checkboxes-group">
                                                        <div class="checkbox">
                                                            <label>
                                                                <input type="checkbox" name="product_features[]" checked="checked" disabled="disabled">
                                                                {{ \App\LaravelTestSuite::find($testSuiteId)->full_name }}
                                                            </label>
                                                        </div>

                                                        @foreach($features as $feature)
                                                            <div class="checkbox">
                                                                <label data-tooltip="tooltip" title="UI image transfer">
                                                                    <input type="checkbox" name="product_features[]" value="{{ $feature['description'] }}" checked="checked" disabled="disabled">
                                                                    {{ $feature['name'] }}
                                                                </label>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>

                                    </div>
                                </div>

                            </div>
                            <div class="colored-box-footer">
                                <button type="submit" class="btn btn-success btn-with-icon btn-confirm">Confirm</button>
                                <button type="button" class="btn btn-default btn-with-icon btn-cancel" onclick="history.go(-1);">Cancel</button>
                            </div>
                        <div class="color-box-loading"><div class="loading-content"><span class="loader"></span><div class="loading-text">SAVING YOUR DATA</div><div class="loading-wait">Please wait...</div></div></div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('page-scripts')
    <script src="{{ getSiteUrl() }}/laravel/resources/assets/js/vendor/bootstrap-datepicker.min.js"></script>
    <script>
        jQuery(document).ready(function ($) {
            $('#productReleaseDate').datepicker();
            $('body').on('click', '.productReleaseCalendar', function () {
                $('#productReleaseDate').focus();
            });

            $('#productAllowOverride').click(function () {
                $('.product-owner-override').toggle();
            })
        });
    </script>
@stop