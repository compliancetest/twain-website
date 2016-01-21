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
<div id="testsuites-container" class="tab-content white_bcg">

    @if(count($testsuites) > 0)

        <div class="column four_fifths left padding20-10">
            <div class="grid dark_gray_txt" id="test_suites_tab_grid">
                <div class="grid_head grid_head_border">
                    <div class="padding10 nopaddingtop">
                        <div class="grid_cell nopaddingtop width40P">Name</div>
                        <div class="grid_cell nopaddingtop width10P tocenter">Published</div>
                        <div class="grid_cell nopaddingtop width15P tocenter">Status</div>
                        <div class="grid_cell nopaddingtop width15P tocenter">Products</div>
                        <div class="grid_cell nopaddingtop width10P tocenter two-lines">Notify Changes</div>
                        <div class="grid_cell nopaddingtop width10P tocenter">Action</div>
                        <div class="clear"></div>
                    </div>
                </div>
                <div class="grid_body noborder">

                    @foreach($testsuites as $row)

                        <div class="grid_row grid_row_border relative">

                            <div class="grid_cell width40P">

                                <h5><a href="{{ get_permalink($row->ID) }}"
                                       class="blue_txt">{{ apply_filters('the_title', $row->post_title) }}</a></h5>
                                {!! apply_filters('the_excerpt', $row->post_excerpt) !!}

                            </div>

                            <div class="grid_cell width10P tocenter">

                                <?php $issueDate = get_post_meta($row->ID, 'ts_issue_date', true);?>
                                {{ formatDate($issueDate) }}

                            </div>

                            <div class="grid_cell width15P tocenter">

                                <?php $issueStatus = get_post_meta($row->ID, 'ts_status', true); ?>
                                <span class="status_btn status_{{ sanitize_title($issueStatus) }}">{{ $issueStatus }}</span>

                            </div>

                            <div class="grid_cell width15P tocenter">
                                <a href="#" class="white_grey_btn view_products_btn"><b></b>VIEW</a>
                            </div>

                            <div class="grid_cell width10P tocenter notify-changes">

                                <input type="checkbox" name="notify_changes{{ $row->ID }}" value="{{ $row->ID }}"
                                       @if(get_user_meta(get_current_user_id(), 'notify_suite_changes' . $row->ID, true)) checked="checked" @endif />
                                <img src="{{ CHILD_TEMPLATE_DIRECTORY }}/images/loading-small.gif" alt="" style="display: none;"/>

                            </div>

                            <div class="grid_cell width10P tocenter">

                                @if($community->isAdmin())
                                    <a href="/edit-test-suite?id={{ $row->ID }}" class="action-btn edit-btn icon-btn has-tooltip left10">
                                        <span class="p"></span>
                                        <span class="simple_tooltip">Edit Suite<span></span></span>
                                    </a>
                                    <a href="?suite_id={{ $row->ID }}&_wpnonce={{ wp_create_nonce('delete-suite') }}&return={{ base64_encode($community->getUrl()) }}"
                                       class="action-btn delete-btn icon-btn has-tooltip left5"
                                       onclick="return confirm('Are you sure to delete this test suite?')">
                                        <span class="p"></span>
                                        <span class="simple_tooltip">Delete Suite<span></span></span>
                                    </a>
                                @else
                                -
                                @endif
                            </div>

                            <div class="clear"></div>
                        </div>
                    <?php
                        $typeList = wp_get_post_terms($row->ID, 'test_suite_type', array("fields" => "all"));
                        foreach ($typeList as $r) {
                            $tsTypes[$r->name] = isset($tsTypes[$r->name]) ? $tsTypes[$r->name] + 1 : 1;
                        }

                        $issuer = get_post_meta($row->ID, 'ts_issuer', true);
                        $tsIssuers[$issuer] = isset($tsIssuers[$issuer]) ? $tsIssuers[$issuer] + 1 : 1;

                        $tsYear = date('Y', strtotime(@$issueDate));
                        $tsIssueYears[$tsYear] = isset($tsIssueYears[$tsYear]) ? $tsIssueYears[$tsYear] + 1 : 1;

                        $tsStatuses[$issueStatus] = isset($tsStatuses[$issueStatus]) ? $tsStatuses[$issueStatus] + 1 : 1;
                    ?>

                    @endforeach
                </div>
            </div>

        </div>
        <div class="column fifth right expendables">
            <form name="form_filter" id="form_filter" action="{{ $community->getUrl() }}" method="get">

                @include('pages.communities.partials.show.filters',
                         ['filterName' => 'Type', 'filterData' => $tsTypes, 'selecteFilters' => $filterType, 'searchType' => 'type']
                )

                @include('pages.communities.partials.show.filters',
                         ['filterName' => 'Issuer', 'filterData' => $tsIssuers, 'selecteFilters' => $filterIssuer, 'searchType' => 'issuer' ]
                )

                @include('pages.communities.partials.show.filters',
                         ['filterName' => 'Year of Issue', 'filterData' => $tsIssueYears, 'selecteFilters' => $filterYear, 'searchType' => 'year' ]
                )

                @include('pages.communities.partials.show.filters',
                         ['filterName' => 'Status', 'filterData' => $tsStatuses, 'selecteFilters' => $filterStatus, 'searchType' => 'status' ]
                )

            </form>
        </div>
        <div class="clear"></div>
        <div class="loading"></div>

    @else

        <p class="column">No Data Found!</p>

    @endif

    @if($community->isAdmin())

        <div class="column">

            <a href="/add-new-test-suite?community_id={{ $community->id }}" class="action-btn add-new-btn">
                <span class="p"></span><span class="t">New Test Suite</span>
            </a>
            <div class="clear"></div>

        </div>

    @endif

</div>
<script type="text/javascript">
    jQuery(document).ready(function () {
        jQuery('.notify-changes input[type="checkbox"]').click(function () {
            var chObj = jQuery(this);
            chObj.next().show();
            chObj.hide();
            var isChecked = this.checked;
            var sID = chObj.val();
            jQuery.ajax({
                url: '/?cp-action={{wp_create_nonce('suite-notify-changes') }}',
                type: 'post',
                data: {id: sID, checked: isChecked ? 1 : 0},
                complete: function (rsp) {
                    chObj.next().hide();
                    chObj.show();
                }
            });
        })
    })
</script>
