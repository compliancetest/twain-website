<?php
$args = array(
        'post_type' => 'test-suite',
        'posts_per_page' => -1,
        'tax_query' => array('relation' => 'and'),
        'meta_query' => array(
                array(
                        'key' => 'community_id',
                        'value' => $community->id,
                        'compare' => '='
                )
        ),
        'orderby' => 'title',
        'order' => 'ASC'
);

if (!$community->isAdmin()) {
    $args['meta_query'][] = array(
            'key' => 'hide_suite',
            'value' => 0,
            'compare' => '='
    );
}

//Getting Filter Params
$filterType = getFilterParam('type');
$filterIssuer = getFilterParam('issuer');
$filterYear = getFilterParam('year');
$filterStatus = getFilterParam('status');

foreach ($filterType as $f) {
    $args['tax_query'][] = array('taxonomy' => 'test_suite_type', 'field' => 'slug', 'terms' => $f);
}
foreach ($filterIssuer as $v) {
    $args['meta_query'][] = array('key' => 'ts_issuer', 'value' => $v, 'compare' => '=');
}
foreach ($filterYear as $v) {
    $args['meta_query'][] = array('key' => 'ts_issue_date', 'value' => $v . "-", 'compare' => 'LIKE');
}
foreach ($filterStatus as $v) {
    $args['meta_query'][] = array('key' => 'ts_status', 'value' => $v, 'compare' => '=');
}
$testsuites = get_posts($args);

$roles = array();
//For Right Panels
$tsTypes = array();
$tsIssuers = array();
$tsIssueYears = array();
$tsStatuses = array();
?>
<div class="community-test-suites row">
    <div class="col-md-12">

        <div class="filter-panel">
            <div class="collapsible-panel-group" id="accordion">

            </div>
        </div>

        <div class="table-responsive result-content-panel-inner">
            <table class="table">
                <thead>
                <tr>
                    <th class="text-left">Name</th>
                    <th>Published</th>
                    <th>Status</th>
                    <th>Products</th>
                    <th>Notify Changes</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>
                        <a href="#" class="test-suite-name">Contributions v1.0</a>
                        <p class="test-suite-description">SuperStream Contributions Test Suite</p>
                    </td>
                    <td class="text-center">2014-01-13</td>
                    <td class="text-center"><span class="status status-partial">Partial</span>
                    </td>
                    <td class="text-center"><a href="#" class="view-products-btn">view</a></td>
                    <td class="text-center"><input type="checkbox" value="" name="notify_changes"></td>
                    <td class="text-center td-actions">
                        <a href="#" class="btn btn-icon btn-primary btn-edit" data-tooltip="tooltip" title="Edit Suite"></a>
                        <a href="#" class="btn btn-icon btn-danger btn-delete" onclick="return confirm('Are you sure to delete this test suite?')" data-tooltip="tooltip" title="Delete Suite"></a>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

    </div>
</div>
