<?php
$isAdmin = $community->isAdmin();

//Getting Test Suites
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
$testsuites = get_posts($args);
$instances = getCommunityProfileInstatnces($community->id);

?>
<div class="community-test-data row">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table">
                <thead>
                <tr>
                    <th class="text-left col-md-6">Profile Name</th>
                    <th class="text-left">Profile Purpose</th>
                    <th>Profile Type</th>
                    <th>Created Date</th>
                    <th>Valid?</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>
                        <a href="#">Test Clearing House v1.3.3 </a>

                        <p>A Clearing House supporting multiple employers. Employer data included by reference</p>
                    </td>
                    <td>For Tester</td>
                    <td class="text-center"><a href="#">Clearing House v2.3.1</a></td>
                    <td class="text-center">2015-04-15</td>
                    <td class="text-center"><span class="item-pending"></span></td>
                    <td class="text-center">
                        <a href="#" class="btn btn-icon btn-primary btn-edit" data-tooltip="tooltip"
                           title="Edit Profile"></a>
                        <a href="#" class="btn btn-icon btn-danger btn-delete" data-tooltip="tooltip"
                           title="Delete Profile"></a>
                        <a href="#" class="btn btn-icon btn-primary btn-copy" data-tooltip="tooltip"
                           title="Copy Profile"></a>
                    </td>
                </tr>
                <tr>
                    <td>
                        <a href="#">Test Employer v2.1.4 </a>

                        <p>Test Employer is a template test data set that is designed to be replaced with tester
                            specific ABN details</p>
                    </td>
                    <td>For Tester - Bulk and Performance Testing</td>
                    <td class="text-center"><a href="#">Clearing House v2.3.1</a></td>
                    <td class="text-center">2015-04-15</td>
                    <td class="text-center"><span class="item-invalid"></span></td>
                    <td class="text-center text-nowrap">
                        <a href="#" class="btn btn-icon btn-primary btn-edit" data-tooltip="tooltip"
                           title="Edit Profile"></a>
                        <a href="#" class="btn btn-icon btn-danger btn-delete" data-tooltip="tooltip"
                           title="Delete Profile"></a>
                        <a href="#" class="btn btn-icon btn-primary btn-copy" data-tooltip="tooltip"
                           title="Copy Profile"></a>
                    </td>
                </tr>
                <tr>
                    <td>
                        <a href="#">Test Product v2.1.2</a>

                        <p>Test Product is a template test data set that is designed to be replaced with tester specific
                            USI details</p>
                    </td>
                    <td>Performance Testing</td>
                    <td class="text-center"><a href="#">Clearing House v2.3.1</a></td>
                    <td class="text-center">2015-04-15</td>
                    <td class="text-center"><span class="item-valid"></span></td>
                    <td class="text-center">
                        <a href="#" class="btn btn-icon btn-primary btn-edit" data-tooltip="tooltip"
                           title="Edit Profile"></a>
                        <a href="#" class="btn btn-icon btn-danger btn-delete" data-tooltip="tooltip"
                           title="Delete Profile"></a>
                        <a href="#" class="btn btn-icon btn-primary btn-copy" data-tooltip="tooltip"
                           title="Copy Profile"></a>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>


    </div>
</div>