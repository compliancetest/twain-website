<?php
/***
* Template Name: Product / Service Registry Search
*/

get_header(); 

global $post; 

$baseURL = get_permalink();
$params = array();
$filterParams = array();

$post_type = 'product-service';

$posts_per_page = 25;
    
//Search Test Suites
$args = get_products_args();
//Getting Search Query
$term = trim(isset($_GET['q']) ? $_GET['q'] : '');
$postsIds = $notInPostIdsArray = array();

if($term) {
    $t_args = $args;
    unset($t_args['tax_query']);// = array('relation' => 'OR');
    unset($t_args['meta_key']);
    unset($t_args['meta_value']);
    $t_args['meta_query']['relation'] = 'OR';
    $t_args['meta_query'][] = array('key' => 'product_owner', 'value' => $term, 'compare' => 'LIKE');
    $t_args['meta_query'][] = array('key' => 'product_name', 'value' => $term, 'compare' => 'LIKE');
    $a_posts = new WP_Query($t_args);
    $allP = $a_posts->get_posts();
    if( $allP ) {
        foreach ($allP AS $one_post) {
            array_push($postsIds, $one_post->ID);
        }
    } else{
        $postsIds = array( -1 );
    }
}
    
//Getting Filter Params
$filterType = getFilterParam('type');
$filterYear = getFilterParam('product_year');
$filterTestSuite = getFilterParam('test_suite');

foreach($filterType as $f)
{
    $f = htmlspecialchars($f);
    $args['meta_query'][] = array('key' => 'product_type', 'value' => $f, 'compare' => '=');
    $params[] = urlencode('type[]') . '=' . urlencode($f);
    $filterParams['type[]'] = $f;
}
foreach($filterTestSuite as $v)
{
    $v = htmlspecialchars($v);
    if( $v == 'None' ){
        $tempPostIds = getProductsByTestSuiteName( $v, true );
        $notInPostIdsArray = array_merge( $notInPostIdsArray, explode( ',', $tempPostIds ) );
    } else {
        $tempPostIds = getProductsByTestSuiteName( $v );
        foreach( explode( ',', $tempPostIds ) AS $post__id ){
            $t = get_post_meta( $post__id, 'product_visibility', true );
            if( ( $t == 'Public' && ! ( is_super_admin() || groups_is_user_admin_in_any_community( get_current_user_id() ) ) ) || ( ( $t == 'Private' || $t == 'Public' ) && ( is_super_admin() || groups_is_user_admin_in_any_community( get_current_user_id() ) ) ) ){
                array_push( $postsIds, $post__id );
            }
        }
    }
    $params[] = urlencode('test_suite[]') . '=' . urlencode($v);
    $filterParams['test_suite[]'] = $v;
}
foreach($filterYear as $v)
{
    $v = htmlspecialchars($v);
    $args['meta_query'][] = array('key' => 'product_release_date', 'value' => $v . "-", 'compare' => 'LIKE');
    $params[] = urlencode('product_year[]') . '=' . urlencode($v);
    $filterParams['product_year[]'] = $v;
}


//For Right Panels
$caseTypes = array();
//$caseOwners = array();
$caseIssueYears = array();
$testSuites = array();

if( ! empty( $postsIds ) ){
    $args['post__in'] = $postsIds;
}
if( ! empty( $notInPostIdsArray ) ){
    foreach( $notInPostIdsArray AS $notInPostIdValue ){
        if(($key = array_search($notInPostIdValue, $args['post__in'])) !== false) {
            unset($args['post__in'][$key]);
        }
    }
    $args['post__not_in'] = $notInPostIdsArray;
}
//For Filter Values
$all_posts = new WP_Query($args);
$allSuites = $all_posts->get_posts();
$processedIDs = array();

foreach($allSuites as $row)
{
    $issueStatus = get_post_meta($row->ID, 'product_status', true);
    $issueDate = get_post_meta($row->ID, 'product_release_date', true);
    
    $productType = get_post_meta($row->ID, 'product_type', true);    
    $caseTypes[$productType] = isset($caseTypes[$productType]) ? $caseTypes[$productType] + 1 : 1;
    $testSuiteProducts = getTestSuitProducts( $row->ID );
    if( ! empty( $testSuiteProducts ) ){
        foreach( $testSuiteProducts AS $testSuiteProduct ){
                if( in_array( $testSuiteProduct->suite_title.$row->ID, $processedIDs )){
                    continue;
                }
                array_push( $processedIDs, $testSuiteProduct->suite_title.$row->ID );
                if( ! empty( $testSuiteProduct->suite_title ) ){
                $testSuites[$testSuiteProduct->suite_title] = isset($testSuites[$testSuiteProduct->suite_title]) ? $testSuites[$testSuiteProduct->suite_title] + 1 : 1;
            }
        }
    } else {
        $testSuites['None'] = isset($testSuites['None']) ? $testSuites['None'] + 1 : 1;
    }
    $tsYear = date('Y', strtotime($issueDate));
    $caseIssueYears[$tsYear] = isset($caseIssueYears[$tsYear]) ? $caseIssueYears[$tsYear] + 1 : 1;

}

//Getting Pagination Params
$page = get_query_var('paged') ? get_query_var('paged') : 1;
$args['paged'] = $page;

$args['posts_per_page'] = $posts_per_page;
$get_posts = new WP_Query($args);
$products = $get_posts->get_posts();
if( isset( $_GET['download']) ){
    $d_args = $args;
    $d_args['paged'] = 1;
    $d_args['posts_per_page'] = -1;
    $posts_to_download = new WP_Query($d_args);
    generateDataAndDownload( $posts_to_download->get_posts() );
}
?>
<div class="content container" id="search">
    <div class="column search-results">
        <div class="search-result-form">
            <form role="search" method="get" id="searchform" action="<?php echo get_permalink() ?>" class="searchform">
                <input type="text" name="q" id="q" class="keyword" value="<?php echo htmlspecialchars(trim(isset($_GET['q']) ? $_GET['q'] : '')) ?>" placeholder="Enter a Product Name, Service Name or Business Name" autocomplete="off" />
                <?php
                foreach($filterParams as $k=>$v)
                {
                    ?>
                    <input type="hidden" name="<?php echo $k?>" value="<?php echo $v?>" />
                <?php
                }
                ?>
                <input type="submit" id="search_test_suite_submit" class="search-button" value="" />
            </form>
        </div>

        <div class="search-results-filter">
            <h4 class="filter-head">Filter By:</h4>
            <div class="search-filters-box">
                <form name="form_filter" id="form_filter" action="<?php echo get_permalink()?>" method="get">
                    <?php if($term){ ?>
                        <input type="hidden" name="q" value="<?php echo $term?>" />
                    <?php } ?>
                    <ul class="search-filters-list clearfix">
                        <li class="first">
                            <label for="implementation-type-filter">Implementation Type</label>
                            <select name="" id="implementation-type-filter" class="select">
                                <option>All</option>
                                <?php foreach ($caseTypes as $k => $v): ?>
                                    <option><?php echo $k; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </li>
                        <li>
                            <label for="owner-filter">Owner</label>
                            <select name="" id="owner-filter" class="select">
                                <option>All</option>
                                <option>Value 1</option>
                                <option>Value 2</option>
                            </select>
                        </li>
                        <li>
                            <label for="test-suite-filter">Test Suite</label>
                            <select name="" id="test-suite-filter" class="select">
                                <option>All</option>
                                <?php foreach ($testSuites as $k => $v): ?>
                                    <option><?php echo $k; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </li>
                        <li>
                            <label for="test-type-filter">Test Type</label>
                            <select name="" id="test-type-filter" class="select">
                                <option>All</option>
                                <option>Value 1</option>
                                <option>Value 2</option>
                            </select>
                        </li>
                        <li class="first">
                            <label for="role-filter">Role</label>
                            <select name="" id="role-filter" class="select">
                                <option>All</option>
                                <option>Value 1</option>
                                <option>Value 2</option>
                            </select>
                        </li>
                        <li>
                            <label for="level-filter">Level</label>
                            <select name="" id="level-filter" class="select">
                                <option>All</option>
                                <option>Value 1</option>
                                <option>Value 2</option>
                            </select>
                        </li>
                        <li>
                            <label for="test-status-filter">Test Status</label>
                            <select name="" id="test-status-filter" class="select">
                                <option>All</option>
                                <option>Value 1</option>
                                <option>Value 2</option>
                            </select>
                        </li>
                        <li>
                            <label>Claim Date Range</label>
                            <div class="date-filter filter-from">
                                <input type="text" class="input datepicker" placeholder="From" />
                            </div>
                            <div class="date-filter filter-to">
                                <input type="text" class="input datepicker" placeholder="To" />
                            </div>
                        </li>
                    </ul>
                    <div class="search-filter-actions">
                        <a class="action-btn process-btn submit-btn" href="#"><span class="p"></span><span class="t">Confirm</span></a>
                        <a href="<?php echo get_permalink(); ?>" class="action-btn clear-btn" id="clear-search-filter-btn"><span class="p"></span><span class="t">Clear</span></a>
                    </div>
                </form>
            </div>
        </div>

        <div class="search-results-count clearfix">
            <p class="search-results-count-label">
                <?php if (count($products) > 0): ?>
                    <?php
                    if ($get_posts->found_posts < $page * $posts_per_page){
                        $last_post_of_page = $get_posts->found_posts;
                    } else {
                        $last_post_of_page = $page * $posts_per_page;
                    }
                    ?>
                    Showing <?php echo ($page - 1) * $posts_per_page + 1?> - <?php echo $last_post_of_page; ?> of <b><?php echo $get_posts->found_posts?></b> Results
                    <?php if($term){ ?> for "<b><?php echo $term?></b>" <?php } ?>
                <?php else: ?>
                    <p class="no-data">No result found!</p>
                <?php endif; ?>
            </p>
            <?php if (count($products) > 0): ?>
                <a href="<?php echo add_query_arg( 'download', '1' ); ?>" class="action-btn download-btn">
                    <span class="p"></span>
                    <span class="t">Download Results</span>
                </a>
            <?php endif; ?>
        </div>
        <?php if(count($products) > 0): ?>
            <div class="search-result-list-wrapper">
                <table class="search-result-list">
                    <thead>
                        <tr>
                            <th class="first">Product / Service Name</th>
                            <th>Version</th>
                            <th>Owner</th>
                            <th>Type</th>
                            <th>Test Suite</th>
                            <th>Role</th>
                            <th>Level</th>
                            <th>Test Status</th>
                            <th>Test Type</th>
                            <th class="last">Claim Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($products as $row): ?>
                            <?php
                                $product = new ProductAndService($row->ID);
                                $product->load();

                                $owner = $product->owner;
                                if(!$owner) {
                                    $owner = get_user_meta($row->post_author, 'user_organisation', true);
                                }
                            ?>
                            <tr>
                                <td class="first"><a href="<?php echo get_permalink($row->ID)?>" class="blue_txt"><?php echo apply_filters('the_title', $row->post_title)?></a></td>
                                <td><?php echo $product->version; ?></td>
                                <td><?php echo $owner; ?></td>
                                <td>
                                    <?php if ($product->type == 'Software Product'): ?>
                                        Product
                                    <?php else: ?>
                                        Service
                                    <?php endif; ?>
                                </td>
                                <td class="test-suite-column"><a href="#">Contributions 1.3</a></td>
                                <td>Fund</td>
                                <td>B</td>
                                <td>Verified</td>
                                <td>End to End</td>
                                <td class="last">2014-11-11</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div class="pagination-wrapper">
                    <div class="pagination">
                        <?php
                        $args = array(
                            'base'         =>  get_permalink() . '%_%?',
                            'format'       => 'page/%#%',
                            'total'        => $get_posts->max_num_pages,
                            'current'      => $page,
                            'show_all'     => False,
                            'end_size'     => 5,
                            'mid_size'     => 5,
                            'prev_next'    => True,
                            'prev_text'    => __('« Previous'),
                            'next_text'    => __('Next »'),
                            'type'         => 'plain',
                            'add_args'     => false,
                            'add_fragment' => (count($params) > 0 ? '&' : '') . implode('&', $params)
                        );

                        echo paginate_links($args);
                        ?>
                    </div>
                </div>
            </div> <!-- end search_result_block -->
        <?php endif; ?>
      </div>
    </div>
<?php get_footer() ?>