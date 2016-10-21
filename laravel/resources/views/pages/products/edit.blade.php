@extends('app')

@section('content')

    <div class="container main-container">
        <div class="main-content products">
            <div class="page-title">
                <h1>Add/Edit product page</h1>
            </div>
            <div class="edit-product">
                <div class="colored-box">
                    <div class="colored-box-header">Information</div>
                    <div class="colored-box-body">
                        <form method="post" action="#" id="edit-product-form">
                            <div class="colored-box-content">
                                <div class="row">
                                    <div class="col-sm-5">

                                        <div class="form-group">
                                            <label for="productName">Name:</label>
                                            <input type="text" class="form-control" id="productName" required="required" name="product_name" value="" data-tooltip="tooltip" title="Enter your product or service name as it is known in the marketplace.">
                                        </div>

                                        <div class="form-group">
                                            <label for="productVersion">Version:</label>
                                            <input type="text" class="form-control" id="productVersion" required="required" name="product_version" value="">
                                        </div>

                                        <div class="form-group organization-group">
                                            <label for="productOrganization">Organization:</label>
                                            <div class="row">
                                                <div class="col-md-9">
                                                    <select name="product_organization" id="productOrganization" class="form-control" data-tooltip="tooltip" title="The owner of your product or service. It is set to the same as the organisation name from your profile.">
                                                        <option>- Select Organization -</option>
                                                        <option>Organisation 1</option>
                                                        <option>Organisation 2</option>
                                                        <option>Organisation 3</option>
                                                    </select>
                                                    <div class="product-owner-override" style="display: none;">
                                                        <input class="form-control" name="product_owner_override" id="productOwnerOverride" value="" title="The owner of your product or service. It is set to the same as the organisation name from your profile.">
                                                    </div>
                                                </div>
                                                <div class="checkbox col-md-3">
                                                    <label><input type="checkbox" id="productAllowOverride" name="allow_override" value=""> Override</label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="productManufacturer">Manufacturer:</label>
                                            <input type="text" class="form-control" id="productManufacturer" required="required" name="product_manufacturer" value="">
                                        </div>

                                        <div class="form-group">
                                            <label for="productVisibility">Visibility:</label>
                                            <select name="product_visibility" id="productVisibility" class="form-control" data-tooltip="tooltip" title="'Public' means anyone can see this product, 'Community' means visibility is limited to community members, 'Private' means that the product is only visible to your own organisation">
                                                <option value="Public">Public</option>
                                                <option value="Community">Community</option>
                                                <option value="Private">Private</option>
                                            </select>
                                            <div class="checkbox">
                                                <label><input type="checkbox" name="services_not_permitted" value=""> Services not permitted</label>
                                            </div>
                                        </div>

                                    </div>

                                    <div class="col-sm-7">
                                        <div class="form-group">
                                            <label for="productReleaseDate">Release Date:</label>
                                            <div class="input-group col-sm-6 col-md-4">
                                                <input type="text" class="form-control" id="productReleaseDate" required="required" name="product_name" value=""
                                                       readonly data-provide="datepicker" data-date-autoclose="true" data-date-format="yyyy-mm-dd"
                                                       data-tooltip="tooltip" title="Enter the date that this version of your product or service was released to the market."
                                                >
                                                <div class="input-group-addon productReleaseCalendar"><span class="calendar-icon"></span></div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="productAccessUrl">Access URL:</label>
                                            <input type="text" class="form-control" id="productAccessUrl" required="required" name="product_url" value="" data-tooltip="tooltip" title="Provide a link to the page on your website that describes this product.">
                                        </div>

                                        <div class="form-group">
                                            <label for="productDescription">Description:</label>
                                            <textarea class="form-control" id="productDescription" required="required" name="product_description" data-tooltip="tooltip" title="Provide a few paragraphs to describe your product or service. This information is displayed to users who may be searching CompliacneTest for certified products."></textarea>
                                        </div>

                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-5">

                                        <div class="form-group">
                                            <label for="productType">Product Type:</label>
                                            <select name="product_type" id="productType" class="form-control">
                                                <option value="DataSource">DataSource</option>
                                                <option value="Application">Application</option>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="productVersion">Protocol Version:</label>
                                            <input type="text" class="form-control" id="productVersion" required="required" name="product_version" value="">
                                        </div>

                                    </div>
                                    <div class="col-sm-7">

                                        <div class="form-group">
                                            <label for="productDescription">Capabilities(comma separated):</label>
                                            <textarea class="form-control" id="productDescription" required="required" name="product_capabilities"></textarea>
                                        </div>

                                    </div>
                                </div>

                            </div>
                            <div class="colored-box-footer">
                                <button type="submit" class="btn btn-success btn-with-icon btn-confirm">Confirm</button>
                                <button type="button" class="btn btn-default btn-with-icon btn-cancel" onclick="history.go(-1);">Cancel</button>
                            </div>
                        </form>
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