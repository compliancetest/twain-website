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
                                <input type="text" class="form-control" id="inputDate" aria-describedby="inputGroupSuccess3Status">
                                <span class="input-group-addon"><img src="/wp-content/themes/bp-child/images/calendar-icon.png" alt=""></span>
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
                        <button type="submit" class="btn btn-success btn-with-icon btn-confirm">Confirm</button>
                        <button type="button" class="btn btn-default btn-with-icon btn-clear">Clear</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

@stop