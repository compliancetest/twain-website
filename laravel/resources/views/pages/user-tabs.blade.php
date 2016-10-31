<div class="tabs-menu">
    <ul>
        @if(is_organisation_admin())
            <li class="organisation-tab"><a href="/my-organisation/" @if($tab == 'my-organisation') class='active' @endif data-tooltip="tooltip" title="My Organization">Organization</a></li>
        @endif

        <li class="communities-tab"><a data-tooltip="tooltip" href="/my-communities/" @if($tab == 'my-communities') class='active' @endif title="My community memberships">Communities</a></li>
        <li class="test-suites-tab"><a href="/my-test-suites/" @if($tab == 'my-test-suites') class='active' @endif data-tooltip="tooltip" title="My test suite subscriptions">Test Suites</a></li>
        <li class="products-tab"><a href="/my-products/" @if($tab == 'my-products') class='active' @endif data-tooltip="tooltip" title="My products under test">Products</a></li>
        <li class="coverage-tab"><a href="/test-suite-coverage/" @if($tab == 'my-suite-coverage') class='active' @endif data-tooltip="tooltip" title="Completeness of my testing">Coverage</a></li>
        <li class="coverage-tab"><a href="/verify-requests/" @if($tab == 'my-requests') class='active' @endif data-tooltip="tooltip" title="My Verify Test Results Requests">Verify Requests</a></li>
        <li class="transactions-tab"><a href="/my-transaction-log/" @if($tab == 'my-transaction-log') class='active' @endif data-tooltip="tooltip" title="My Test Results">Test Results</a></li>
        <li class="support-tab"><a href="/my-support-tickets/" @if($tab == 'my-support-tickets') class='active' @endif data-tooltip="tooltip" title="My support tickets">Support</a></li>
        <li class="profile-tab"><a href="/my-profile/" @if($tab == 'my-profile') class='active' @endif data-tooltip="tooltip" title="My profile">Profile</a></li>

        @if(is_super_admin())
            <li class="transactions-tab"><a href="/api-logs/" @if($tab == 'my-logs') class='active' @endif data-tooltip="tooltip" title="ApiLogs">ApiLogs</a></li>
            <li class="transactions-tab"><a href="/test-outcome-logs/" @if($tab == 'my-outcome-logs') class='active' @endif data-tooltip="tooltip" title="Outcome Logs">Outcome Logs</a></li>
        @endif
    </ul>
</div>