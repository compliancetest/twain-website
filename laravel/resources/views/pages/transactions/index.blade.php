@extends('app')

@section('content')

<div class="container main-container">

    <div class="tabs-menu">
        <ul>
            @if(is_organisation_admin())
            <li class="organisation-tab"><a href="/my-organisation/" data-tooltip="tooltip" title="My Organisation">Organisation</a></li>
            @endif

            <li class="communities-tab"><a data-tooltip="tooltip" href="/my-communities/" title="My community memberships">Communities</a></li>
            <li class="test-suites-tab"><a href="/my-test-suites/" data-tooltip="tooltip" title="My test suite subscriptions">Test Suites</a></li>
            <li class="products-tab"><a href="/my-products/" data-tooltip="tooltip" title="My products under test">Products</a></li>
            <li class="coverage-tab"><a href="/test-suite-coverage/" data-tooltip="tooltip" title="Completeness of my testing">Coverage</a></li>
            <li class="coverage-tab"><a href="/verify-requests/" data-tooltip="tooltip" title="My Verify Transactions Requests">Verify Requests</a></li>
            <li class="transactions-tab"><a href="/my-transaction-log/" class="active" data-tooltip="tooltip" title="My test transactions">Transactions</a></li>
            <li class="support-tab"><a href="/my-support-tickets/" data-tooltip="tooltip" title="My support tickets">Support</a></li>
            <li class="profile-tab"><a href="/my-profile/" data-tooltip="tooltip" title="My profile">Profile</a></li>

        </ul>
    </div>

    <div class="main-content">
        <div class="transaction-filter">
            <div class="transaction-filter-title">Filter By:</div>
            <div class="transaction-filter-content">
                <form action="#" method="post">
                    <div class="row">
                        <div class="form-group col-sm-6 col-md-3">
                            <label for="filterSubscription">Subscription:</label>
                            <select class="form-control" id="filterSubscription" name="subscription">
                                <option value="">- All -</option>
                                <option value="1">Option 1</option>
                                <option value="1">Option 2</option>
                                <option value="1">Option 3</option>
                            </select>
                        </div>

                        <div class="form-group col-sm-6 col-md-3">
                            <label for="filterProduct">Product:</label>
                            <select class="form-control" id="filterProduct" name="subscription">
                                <option value="">- All -</option>
                                <option value="1">Product 1</option>
                                <option value="1">Product 2</option>
                                <option value="1">Product 3</option>
                            </select>
                        </div>

                        <div class="form-group col-sm-6 col-md-3">
                            <label for="filterSuite">Test Suite:</label>
                            <select class="form-control" id="filterSuite" name="subscription">
                                <option value="">- All -</option>
                                <option value="1">Suite 1</option>
                                <option value="1">Suite 2</option>
                                <option value="1">Suite 3</option>
                            </select>
                        </div>

                        <div class="form-group col-sm-6 col-md-3">
                            <label for="filterCase">Test Case:</label>
                            <select class="form-control" id="filterCase" name="subscription">
                                <option value="">- All -</option>
                                <option value="1">Case 1</option>
                                <option value="1">Case 2</option>
                                <option value="1">Case 3</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-sm-6 col-md-3">
                            <label for="filterAudit">Audit:</label>
                            <select class="form-control" id="filterAudit" name="subscription">
                                <option value="">- All -</option>
                                <option value="1">Audit 1</option>
                                <option value="1">Audit 2</option>
                                <option value="1">Audit 3</option>
                            </select>
                        </div>

                        <div class="form-group col-sm-6 col-md-3">
                            <label for="filterTestOutcome">Test Outcome:</label>
                            <select class="form-control" id="filterTestOutcome" name="subscription">
                                <option value="">- All -</option>
                                <option value="1">Test Outcome 1</option>
                                <option value="1">Test Outcome 2</option>
                                <option value="1">Test Outcome 3</option>
                            </select>
                        </div>

                        <div class="form-group col-sm-6 col-md-3">
                            <label for="filterScenario">Scenario:</label>
                            <select class="form-control" id="filterScenario" name="subscription">
                                <option value="">- All -</option>
                                <option value="1">Scenario 1</option>
                                <option value="1">Scenario 2</option>
                                <option value="1">Scenario 3</option>
                            </select>
                        </div>

                        <div class="form-group col-sm-6 col-md-3">
                            <label for="filterDate">Date:</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="filterDate" readonly data-provide="datepicker">
                                <span class="input-group-addon"><span id="filterCalendar" class="calendar-icon"></span></span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-sm-6 col-md-3">
                            <label for="filterOrganisation">Organisation:</label>
                            <select class="form-control" id="filterOrganisation" name="subscription">
                                <option value="">- All -</option>
                                <option value="1">Organisation 1</option>
                                <option value="1">Organisation 2</option>
                                <option value="1">Organisation 3</option>
                            </select>
                        </div>
                    </div>

                    <div class="transaction-filter-footer">
                        <button type="submit" class="btn btn-success btn-with-icon btn-confirm">Confirm</button>&nbsp;&nbsp;
                        <button type="button" class="btn btn-default btn-with-icon btn-clear">Clear</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="transaction-list-actions">
            <div class="pull-left">
                <button type="button" class="btn btn-success btn-with-icon btn-trigger">Verify As Pass</button>
                <button type="button" class="btn btn-danger btn-with-icon btn-trigger">Verify As Fail</button>
                <button type="button" class="btn btn-default btn-with-icon btn-trigger">Verify As Skip</button>
                <button type="button" class="btn btn-danger btn-with-icon btn-delete" data-tooltip="tooltip" data-placement="top">Remove Selected</button>
            </div>
            <div class="pull-right">
                <div class="form-inline">
                    <div class="form-group">
                        <label for="paginationLimit">Display #</label>
                        <select class="form-control" id="paginationLimit" name="limit">
                            <option value="10">10</option>
                            <option value="20">20</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                            <option value="-1">All</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div id="log-result-table">
            <div class="table-responsive">
                <div class="log-results-table-wrapper">
                    <table class="table colored-parent-table log-results-table">
                        <thead>
                            <tr>
                                <th class="text-center"><input type="checkbox"></th>
                                <th class="text-left">Product Name</th>
                                <th>Test Suite<br/>Test Case</th>
                                <th>Test<br/>Outcome</th>
                                <th>Audit<br/>Record</th>
                                <th>
                                    Organisation<br/>
                                    Subscription Nickname<br/>
                                    Execution ID
                                </th>
                                <th>Date<br/>Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-center"><input type="checkbox"></td>
                                <td class="product-name">
                                    <a data-toggle="collapse" class="product-collapse-link collapsed" href="#product-1"><span class="collapse-icon"></span></a>
                                    <a href="#" class="product-name-link">Panasonic KV-S1026C KV-S1015C v15.0</a>
                                </td>
                                <td class="text-center">
                                    <a href="http://twain.lc/test-suite/twain-v2-3-compliance-data-sources-v1-0/">TWAIN v2.3 Compliance &ndash; Data Sources v1.0</a>
                                    <br />
                                    <a href="http://twain.lc/test-case/vn-06-v1-0/">VN-06 v1.0</a>
                                </td>
                                <td>
                                    <a data-toggle="collapse" class="collapsed" href="#product-1"><span class="collapse-icon"></span></a>
                                    <span class="text-status-ignored">Skip</span>
                                </td>
                                <td class="text-center"><input type="checkbox"></td>
                                <td class="text-center">
                                    Panasonic<br />
                                    DS Tech 4<br />
                                    <a target="_blank" href="#">7cc6066a-db11-40bb-a040-0fe5356424db</a>
                                </td>
                                <td class="text-center">2016-07-04<br/>16:30:53</td>
                            </tr>
                            <tr id="product-1" class="collapse">
                                <td></td>
                                <td colspan="6">
                                    <table class="table colored-table log-results-sub-table">
                                        <thead>
                                            <tr>
                                                <th>From<br/>To</th>
                                                <th>Test<br/> Step</th>
                                                <th>
                                                    Operation Triplet<br/>
                                                    Return Code
                                                </th>
                                                <th>Session<br/>State</th>
                                                <th>Message<br/> Data</th>
                                                <th>Date<br/>Time</th>
                                                <th>Step<br/>Outcome</th>
                                                <th>Screen<br/>Capture</th>
                                                <th>Scan<br/>Result</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>APP<br/>DS</td>
                                                <td><a href="#">3</a></td>
                                                <td>
                                                    DG_CONTROL / DAT_IDENTITY / MSG_OPENDS<br/>
                                                    <span class="text-status-success">TWRC_SUCCESS</span>
                                                </td>
                                                <td>3</td>
                                                <td><a href="#">View</a></td>
                                                <td>2016-07-04<br/>13:20:59</td>
                                                <td><span class="text-status-success">Pass</span></td>
                                                <td>-</td>
                                                <td>-</td>
                                            </tr>
                                            <tr>
                                                <td>APP<br/>DS</td>
                                                <td><a href="#">3</a></td>
                                                <td>
                                                    DG_CONTROL / DAT_IDENTITY / MSG_OPENDS<br/>
                                                    <span class="text-status-success">TWRC_SUCCESS</span>
                                                </td>
                                                <td>3</td>
                                                <td><a href="#">View</a></td>
                                                <td>2016-07-04<br/>13:20:59</td>
                                                <td><span class="text-status-success">Pass</span></td>
                                                <td>-</td>
                                                <td>-</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-center"><input type="checkbox"></td>
                                <td class="product-name">
                                    <a data-toggle="collapse" class="product-collapse-link collapsed" href="#product-2"><span class="collapse-icon"></span></a>
                                    <a href="#" class="product-name-link">Panasonic KV-S1026C KV-S1015C v15.0</a>
                                </td>
                                <td class="text-center">
                                    <a href="http://twain.lc/test-suite/twain-v2-3-compliance-data-sources-v1-0/">TWAIN v2.3 Compliance &ndash; Data Sources v1.0</a>
                                    <br />
                                    <a href="http://twain.lc/test-case/vn-06-v1-0/">VN-06 v1.0</a>
                                </td>
                                <td>
                                    <a data-toggle="collapse" class="collapsed" href="#product-2"><span class="collapse-icon"></span></a>
                                    <span class="text-status-success">Pass</span>
                                </td>
                                <td class="text-center"><input type="checkbox"></td>
                                <td class="text-center">
                                    Panasonic<br />
                                    DS Tech 4<br />
                                    <a target="_blank" href="#">7cc6066a-db11-40bb-a040-0fe5356424db</a>
                                </td>
                                <td class="text-center">2016-07-04<br/>16:30:53</td>
                            </tr>
                            <tr id="product-2" class="collapse">
                                <td></td>
                                <td colspan="6">
                                    <table class="table colored-table log-results-sub-table">
                                        <thead>
                                        <tr>
                                            <th>From<br/>To</th>
                                            <th>Test<br/> Step</th>
                                            <th>
                                                Operation Triplet<br/>
                                                Return Code
                                            </th>
                                            <th>Session<br/>State</th>
                                            <th>Message<br/> Data</th>
                                            <th>Date<br/>Time</th>
                                            <th>Step<br/>Outcome</th>
                                            <th>Screen<br/>Capture</th>
                                            <th>Scan<br/>Result</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td>APP<br/>DS</td>
                                            <td><a href="#">3</a></td>
                                            <td>
                                                DG_CONTROL / DAT_IDENTITY / MSG_OPENDS<br/>
                                                <span class="text-status-success">TWRC_SUCCESS</span>
                                            </td>
                                            <td>3</td>
                                            <td><a href="#">View</a></td>
                                            <td>2016-07-04<br/>13:20:59</td>
                                            <td><span class="text-status-success">Pass</span></td>
                                            <td>-</td>
                                            <td>-</td>
                                        </tr>
                                        <tr>
                                            <td>APP<br/>DS</td>
                                            <td><a href="#">3</a></td>
                                            <td>
                                                DG_CONTROL / DAT_IDENTITY / MSG_OPENDS<br/>
                                                <span class="text-status-success">TWRC_SUCCESS</span>
                                            </td>
                                            <td>3</td>
                                            <td><a href="#">View</a></td>
                                            <td>2016-07-04<br/>13:20:59</td>
                                            <td><span class="text-status-success">Pass</span></td>
                                            <td>-</td>
                                            <td>-</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>

                        </tbody>
                    </table>
                </div>
            </div>
            <div class="pagination-wrapper">
                <div class="pagination">
                    <a href="#" class="prev">prev</a>
                    <span class="current">1</span>
                    <a href="#">2</a>
                    <a href="#">3</a>
                    <span class="pager-dots">...</span>
                    <a href="#">140</a>
                    <a href="#">141</a>
                    <a href="#">142</a>
                    <a href="#" class="next">next</a>
                </div>
            </div>

        </div>
    </div>

</div>

@stop

@section('page-scripts')
<script src="{{ getSiteUrl() }}/laravel/resources/assets/js/vendor/bootstrap-datepicker.min.js"></script>
<script>
    jQuery(document).ready(function($) {
        $('#filterCalendar').click(function () {
            $('#filterDate').datepicker('show');
        });
    });
</script>
@stop