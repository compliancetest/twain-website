@extends('app')

@section('content')
    <div class="container main-container">
        <div class="container main-container">

            <!-- todo-migration - 1-->
            @include('pages.user-tabs', ['tab' => 'my-products'])

            <div class="main-content products-list">

                <div class="colored-box collapsible-box">
                    <div class="colored-box-header">
                        <a href="#productItemBox1" class="collapse-arrow" data-toggle="collapse"><span class="glyphicon glyphicon-triangle-bottom"></span><span class="glyphicon glyphicon-triangle-right"></span></a>
                        Product: <a href="#"><strong>A_test v3.1 for TEST_MODEL2  (1.1)</strong></a>
                        <ul class="colored-box-header-actions">
                            <li><a href="#" data-tooltip="tooltip" title="Edit"><span class="edit-icon"></span></a></li>
                            <li><a href="#" data-tooltip="tooltip" title="Delete"><span class="delete-icon"></span></a></li>
                        </ul>
                    </div>
                    <div class="colored-box-body collapse in" id="productItemBox1">
                        <div class="colored-box-content">
                            <table class="table colored-table">
                                <thead>
                                    <tr>
                                        <th>Claim ID</th>
                                        <th>Certificate</th>
                                        <th>Issuer</th>
                                        <th>Suite</th>
                                        <th>Level</th>
                                        <th>Role</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="9" class="empty-row">No compliance claim recorded yet</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="colored-box collapsible-box">
                    <div class="colored-box-header">
                        <a href="#productItemBox2" class="collapse-arrow" data-toggle="collapse"><span class="glyphicon glyphicon-triangle-bottom"></span><span class="glyphicon glyphicon-triangle-right"></span></a>
                        Product: <a href="#"><strong>A_test v3.1 for TEST_MODEL2  (1.1)</strong></a>
                        <ul class="colored-box-header-actions">
                            <li><a href="#" data-tooltip="tooltip" title="Edit"><span class="edit-icon"></span></a></li>
                            <li><a href="#" data-tooltip="tooltip" title="Delete"><span class="delete-icon"></span></a></li>
                        </ul>
                    </div>
                    <div class="colored-box-body collapse in" id="productItemBox2">
                        <div class="colored-box-content">
                            <table class="table colored-table">
                                <thead>
                                    <tr>
                                        <th class="text-left">Claim ID</th>
                                        <th>Certificate</th>
                                        <th class="text-left">Issuer</th>
                                        <th class="text-left">Suite</th>
                                        <th>Level</th>
                                        <th>Role</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>f9dbca9f-e13f-4bc5-8048-77ea54ad7fab</td>
                                        <td class="text-center">
                                            <a href="#" class="btn btn-primary btn-icon btn-view" data-tooltip="tooltip" title="View">View</a>
                                            <a href="#" class="btn btn-success btn-icon btn-download" data-tooltip="tooltip" title="Download">Download</a>
                                        </td>
                                        <td>twain.org</td>
                                        <td><a href="#">TWAIN v2.3 Compliance - Applications v1.0</a></td>
                                        <td class="text-center">B</td>
                                        <td class="text-center">Application</td>
                                        <td class="text-center"><span class="text-status-verified">Verified</span></td>
                                        <td class="text-center">2016-09-10</td>
                                        <td class="text-center"><a href="#" class="btn btn-danger btn-icon btn-delete" data-tooltip="tooltip" title="Delete Claim">Delete</a></td>
                                    </tr>
                                    <tr>
                                        <td>f9dbca9f-e13f-4bc5-8048-77ea54ad7fab</td>
                                        <td class="text-center">
                                            <a href="#" class="btn btn-primary btn-icon btn-view" data-tooltip="tooltip" title="View">View</a>
                                            <a href="#" class="btn btn-success btn-icon btn-download" data-tooltip="tooltip" title="Download">Download</a>
                                        </td>
                                        <td>twain.org</td>
                                        <td><a href="#">TWAIN v2.3 Compliance - Applications v1.0</a></td>
                                        <td class="text-center">B</td>
                                        <td class="text-center">Application</td>
                                        <td class="text-center"><span class="text-status-verified">Verified</span></td>
                                        <td class="text-center">2016-09-10</td>
                                        <td class="text-center"><a href="#" class="btn btn-danger btn-icon btn-delete" data-tooltip="tooltip" title="Delete Claim">Delete</a></td>
                                    </tr>
                                    <tr>
                                        <td>f9dbca9f-e13f-4bc5-8048-77ea54ad7fab</td>
                                        <td class="text-center">
                                            <a href="#" class="btn btn-primary btn-icon btn-view" data-tooltip="tooltip" title="View">View</a>
                                            <a href="#" class="btn btn-success btn-icon btn-download" data-tooltip="tooltip" title="Download">Download</a>
                                        </td>
                                        <td>twain.org</td>
                                        <td><a href="#">TWAIN v2.3 Compliance - Applications v1.0</a></td>
                                        <td class="text-center">B</td>
                                        <td class="text-center">Application</td>
                                        <td class="text-center"><span class="text-status-verified">Verified</span></td>
                                        <td class="text-center">2016-09-10</td>
                                        <td class="text-center"><a href="#" class="btn btn-danger btn-icon btn-delete" data-tooltip="tooltip" title="Delete Claim">Delete</a></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="colored-box collapsible-box">
                    <div class="colored-box-header">
                        <a href="#productItemBox3" class="collapse-arrow" data-toggle="collapse"><span class="glyphicon glyphicon-triangle-bottom"></span><span class="glyphicon glyphicon-triangle-right"></span></a>
                        Product: <a href="#"><strong>A_test v3.1 for TEST_MODEL2  (1.1)</strong></a>
                        <ul class="colored-box-header-actions">
                            <li><a href="#" data-tooltip="tooltip" title="Edit"><span class="edit-icon"></span></a></li>
                            <li><a href="#" data-tooltip="tooltip" title="Delete"><span class="delete-icon"></span></a></li>
                        </ul>
                    </div>
                    <div class="colored-box-body collapse in" id="productItemBox3">
                        <div class="colored-box-content">
                            <table class="table colored-table">
                                <thead>
                                    <tr>
                                        <th class="text-left">Claim ID</th>
                                        <th>Certificate</th>
                                        <th class="text-left">Issuer</th>
                                        <th class="text-left">Suite</th>
                                        <th>Level</th>
                                        <th>Role</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>436e55f8-78e1-415d-98b4-40a9d02facef</td>
                                        <td class="text-center">
                                            <a href="#" class="btn btn-primary btn-icon btn-view" data-tooltip="tooltip" title="View">View</a>
                                            <a href="#" class="btn btn-success btn-icon btn-download" data-tooltip="tooltip" title="Download">Download</a>
                                        </td>
                                        <td>twain.org</td>
                                        <td><a href="#">TWAIN v2.2 Compliance – Data Sources v1.0</a></td>
                                        <td class="text-center">A</td>
                                        <td class="text-center">DataSource</td>
                                        <td class="text-center"><span class="text-status-verified">Verified</span></td>
                                        <td class="text-center">2016-09-10</td>
                                        <td class="text-center"><a href="#" class="btn btn-danger btn-icon btn-delete" data-tooltip="tooltip" title="Delete Claim">Delete</a></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@stop