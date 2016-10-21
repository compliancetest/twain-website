@extends('app')

@section('content')

<div class="container main-container">
    <div class="main-content product-page">
        <div class="page-title">
            <a href="#" onclick="window.print();" class="btn btn-primary btn-with-icon btn-print pull-right">Print</a>
            <h1 class="pull-left">Product Details <a href="/my-products/" class="btn btn-default btn-with-icon btn-back" data-tooltip="tooltip" title="Back to My Products">Back</a></h1>
        </div>
        <div class="product-info">
            <div class="product-identifiers clearfix">
                <div class="pull-left">
                    <div class="product-name">Name: <strong>Virtual Scanner v0.3</strong></div>
                    <div class="product-id">(ID: <strong>2_drummond-group_virtual-scanner_v0-3</strong>)</div>
                </div>
                <div class="pull-right">
                    <a href="/laravel-product/123/edit" class="btn btn-primary btn-with-icon btn-edit">Edit</a>
                    <a href="#" class="btn btn-danger btn-with-icon btn-delete">Delete</a>
                </div>
            </div>
            <ul class="product-attributes">
                <li>Organization: <strong>Panasonic</strong></li>
                <li>Manufacturer: <strong>Drummond Group</strong></li>
                <li>Release Date: <strong>Sep 2016</strong></li>
                <li>Version: <strong>0.3</strong></li>
                <li>Visibility: <strong>Community</strong></li>
                <li>Product Type: <strong>DataSource</strong></li>
                <li>Protocol Version: <strong>2.3</strong></li>
            </ul>
            <div class="product description">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</div>
        </div>


        <h2 class="product-subtitle">Test Plans and Compliance Claims</h2>
        <div class="blue-colored-table-wrapper table-responsive">
            <table class="table blue-colored-table">
                <thead>
                    <tr>
                        <th class="text-left">Claim ID</th>
                        <th class="text-left">Issuer</th>
                        <th class="text-left">Suite</th>
                        <th>Level</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Certificate</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>6bdb04a5-5048-47ab-b52e-61ac798023ad</td>
                        <td><a href="#">twain.org</a></td>
                        <td><a href="#">Scanning Operations – Data Sources v1.0</a></td>
                        <td class="text-center">A</td>
                        <td class="text-center">DataSource</td>
                        <td class="text-center">Verified</td>
                        <td class="text-center">2016-09-06</td>
                        <td class="text-center">
                            <a href="#" class="btn btn-primary btn-with-icon btn-view">View</a>
                            <a href="#" class="btn btn-success btn-with-icon btn-download">Download</a>
                        </td>
                    </tr>
                    <tr>
                        <td>6bdb04a5-5048-47ab-b52e-61ac798023ad</td>
                        <td><a href="#">twain.org</a></td>
                        <td><a href="#">Scanning Operations – Data Sources v1.0</a></td>
                        <td class="text-center">A</td>
                        <td class="text-center">DataSource</td>
                        <td class="text-center">Verified</td>
                        <td class="text-center">2016-09-06</td>
                        <td class="text-center">
                            <a href="#" class="btn btn-primary btn-with-icon btn-view">View</a>
                            <a href="#" class="btn btn-success btn-with-icon btn-download">Download</a>
                        </td>
                    </tr>
                    <tr>
                        <td>6bdb04a5-5048-47ab-b52e-61ac798023ad</td>
                        <td><a href="#">twain.org</a></td>
                        <td><a href="#">Scanning Operations – Data Sources v1.0</a></td>
                        <td class="text-center">A</td>
                        <td class="text-center">DataSource</td>
                        <td class="text-center">Verified</td>
                        <td class="text-center">2016-09-06</td>
                        <td class="text-center">
                            <a href="#" class="btn btn-primary btn-with-icon btn-view">View</a>
                            <a href="#" class="btn btn-success btn-with-icon btn-download">Download</a>
                        </td>
                    </tr>
                    <tr>
                        <td>6bdb04a5-5048-47ab-b52e-61ac798023ad</td>
                        <td><a href="#">twain.org</a></td>
                        <td><a href="#">Scanning Operations – Data Sources v1.0</a></td>
                        <td class="text-center">A</td>
                        <td class="text-center">DataSource</td>
                        <td class="text-center">Verified</td>
                        <td class="text-center">2016-09-06</td>
                        <td class="text-center">
                            <a href="#" class="btn btn-primary btn-with-icon btn-view">View</a>
                            <a href="#" class="btn btn-success btn-with-icon btn-download">Download</a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

@stop