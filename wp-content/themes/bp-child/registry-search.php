<?php
/***
* Template Name: Product / Service Registry Search
*/

get_header(); 

global $post; 

$baseURL = get_permalink();


function process_search( $search_params = array() ){
    global $wpdb;

    $product_claims = $wpdb->get_results( "SELECT * FROM wp_compliance_claims" );
    $service_claims = $wpdb->get_results( "SELECT * FROM wp_e2e_agreement WHERE status = 'Verified'" );

    $type_filter = $owner_filter = $suite_filter = $testtype_filter = $role_filter = $level_filter = $teststatus_filter = array( 'All' );

    foreach( $product_claims AS $product_claim ){
        if( ! isset( $product_claim['1']) ){
            $product_claim['1'] = 'Product';
        }
        if( ! isset( $owner_filter[$product_claim->owner]) ){
            $owner_filter['1'] = 'Product';
        }
        if( ! isset( $suite_filter[$product_claim->suite_id]) ){
            $suite_filter[$product_claim->suite_id] = get_the_title( $product_claim->suite_id );
        }
        if( ! isset( $testtype_filter['1']) ){
            $testtype_filter['1'] = 'Product testing';
        }
        if( strpos( $role_filter, ';;' ) !== false ) {
            $temp_roles = explode( ';;', trim( $product_claims->role, ';;' ) );
            foreach( $temp_roles AS $temp_role ) {
                if (!isset($role_filter[$temp_role])) {
                    $role_filter[$temp_role] = $temp_role;
                }
            }
        }
        if( strpos( $level_filter, ';;' ) !== false ) {
            $temp_levels = explode( ';;', trim( $product_claims->level, ';;' ) );
            foreach( $temp_levels AS $temp_level ) {
                if (!isset($level_filter[$temp_level])) {
                    $level_filter[$temp_level] = $temp_level;
                }
            }
        }
        if( ! isset( $teststatus_filter[$product_claim->status]) ){
            $teststatus_filter[$product_claim->status] = $product_claim->status;
        }
    }
    foreach( $service_claims AS $service_claim ){
        if( ! isset( $type_filter['2']) ){
            $type_filter['2'] = 'Service';
        }
        if( ! isset( $owner_filter[$service_claim->owner]) ){
            $owner_filter['1'] = 'Product';
        }
        if( ! isset( $suite_filter[$product_claim->suite_id]) ){
            $suite_filter[$product_claim->suite_id] = get_the_title( $product_claim->suite_id );
        }
        if( ! isset( $testtype_filter['1']) ){
            $testtype_filter['1'] = 'Product testing';
        }
        if( strpos( $role_filter, ';;' ) !== false ) {
            $temp_roles = explode( ';;', trim( $product_claims->role, ';;' ) );
            foreach( $temp_roles AS $temp_role ) {
                if (!isset($role_filter[$temp_role])) {
                    $role_filter[$temp_role] = $temp_role;
                }
            }
        }
        if( strpos( $level_filter, ';;' ) !== false ) {
            $temp_levels = explode( ';;', trim( $product_claims->level, ';;' ) );
            foreach( $temp_levels AS $temp_level ) {
                if (!isset($level_filter[$temp_level])) {
                    $level_filter[$temp_level] = $temp_level;
                }
            }
        }
        if( ! isset( $teststatus_filter[$product_claim->status]) ){
            $teststatus_filter[$product_claim->status] = $product_claim->status;
        }
    }
}
$posts_per_page = 25;

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
                            <select name="type_filter" id="implementation-type-filter" class="select">
                                <option>All</option>
                                <?php foreach( $ty as $k => $v): ?>
                                    <option><?php echo $k; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </li>
                        <li>
                            <label for="owner-filter">Owner</label>
                            <select name="owner_filter" id="owner-filter" class="select">
                                <option>All</option>
                                <option>Value 1</option>
                                <option>Value 2</option>
                            </select>
                        </li>
                        <li>
                            <label for="test-suite-filter">Test Suite</label>
                            <select name="suite_filter" id="test-suite-filter" class="select">
                                <option>All</option>
                                <?php foreach ($testSuites as $k => $v): ?>
                                    <option><?php echo $k; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </li>
                        <li>
                            <label for="test-type-filter">Test Type</label>
                            <select name="testtype_filter" id="test-type-filter" class="select">
                                <option>All</option>
                                <option>Value 1</option>
                                <option>Value 2</option>
                            </select>
                        </li>
                        <li class="first">
                            <label for="role-filter">Role</label>
                            <select name="role_filter" id="role-filter" class="select">
                                <option>All</option>
                                <option>Value 1</option>
                                <option>Value 2</option>
                            </select>
                        </li>
                        <li>
                            <label for="level-filter">Level</label>
                            <select name="level_filter" id="level-filter" class="select">
                                <option>All</option>
                                <option>Value 1</option>
                                <option>Value 2</option>
                            </select>
                        </li>
                        <li>
                            <label for="test-status-filter">Test Status</label>
                            <select name="teststatus_filter" id="test-status-filter" class="select">
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