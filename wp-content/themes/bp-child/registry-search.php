<?php
/***
 * Template Name: Product / Service Registry Search
 */

get_header();

global $post;

$baseURL = get_permalink();

$cloud_search = new CloudSearch();
$page = get_query_var('paged') ? get_query_var('paged') : 1;
if( $page != 1 ){
    $_POST['page'] = $page;
}

$results = $cloud_search->search( $_POST );

if( isset( $_POST['download']) ){
    $d_args = $args;
    $d_args['paged'] = 1;
    $d_args['posts_per_page'] = -1;
    $posts_to_download = new WP_Query($d_args);
    generateDataAndDownload( $posts_to_download->get_posts() );
}
?>
    <div class="content container" id="search">
        <div class="column search-results">
            <div class="search-results-filter">
                <h4 class="filter-head">Filter By:</h4>
                <div class="search-filters-box">
                    <form name="form_filter" id="form_filter" action="<?php echo get_permalink()?>" method="post">
                        <div class="search-result-form">
                            <input type="text" name="q" id="q" class="keyword" value="<?php echo htmlspecialchars(trim(isset($_POST['q']) ? $_POST['q'] : '')) ?>" placeholder="Enter a Product Name, Service Name or Business Name" autocomplete="off" />
                            <input type="submit" id="search_test_suite_submit" class="search-button" value="" />
                        </div>
                        <input type="hidden" name="page" value="<?php echo $page;?>">
                        <ul class="search-filters-list clearfix">
                            <li class="first">
                                <label for="implementation-type-filter">Implementation Type</label>
                                <select name="type" id="implementation-type-filter" class="select">
                                    <option>All</option>
                                    <?php if( is_array( $results['facets']['owner']['buckets'] ) ):?>
                                        <?php foreach ($results['facets']['type']['buckets'] AS $v): ?>
                                            <option value="<?php echo $v['value'];?>" <?php if( isset( $_POST['type'] ) && $_POST['type'] == $v['value'] ):?> selected="selected" <?php endif;?>><?php echo $v['value']; ?></option>
                                        <?php endforeach; ?>
                                    <?php endif;?>
                                </select>
                            </li>
                            <li>
                                <label for="owner-filter">Owner</label>
                                <select name="owner" id="owner-filter" class="select">
                                    <option>All</option>
                                    <?php if( is_array( $results['facets']['owner']['buckets'] ) ):?>
                                        <?php foreach ($results['facets']['owner']['buckets'] AS $v): ?>
                                            <option value="<?php echo $v['value'];?>"<?php if( isset( $_POST['owner'] ) && $_POST['owner'] == $v['value'] ):?> selected="selected" <?php endif;?>><?php echo $v['value']; ?></option>
                                        <?php endforeach; ?>
                                    <?php endif;?>
                                </select>
                            </li>
                            <li>
                                <label for="test-suite-filter">Test Suite</label>
                                <select name="test_suite" id="test-suite-filter" class="select">
                                    <option>All</option>
                                    <?php if( is_array( $results['facets']['owner']['buckets'] ) ):?>
                                        <?php foreach ($results['facets']['test_suite']['buckets'] AS $v): ?>
                                            <option value="<?php echo $v['value'];?>"<?php if( isset( $_POST['test_suite'] ) && $_POST['test_suite'] == $v['value'] ):?> selected="selected" <?php endif;?>><?php echo $v['value']; ?></option>
                                        <?php endforeach; ?>
                                    <?php endif;?>
                                </select>
                            </li>
                            <li>
                                <label for="test-type-filter">Test Type</label>
                                <select name="test_type" id="test-type-filter" class="select">
                                    <option>All</option>
                                    <?php if( is_array( $results['facets']['owner']['buckets'] ) ):?>
                                        <?php foreach ($results['facets']['test_type']['buckets'] AS $v): ?>
                                            <option value="<?php echo $v['value'];?>"<?php if( isset( $_POST['test_type'] ) && $_POST['test_type'] == $v['value'] ):?> selected="selected" <?php endif;?>><?php echo $v['value']; ?></option>
                                        <?php endforeach; ?>
                                    <?php endif;?>
                                </select>
                            </li>
                            <li class="first">
                                <label for="role-filter">Role</label>
                                <select name="role" id="role-filter" class="select">
                                    <option>All</option>
                                    <?php if( is_array( $results['facets']['owner']['buckets'] ) ):?>
                                        <?php foreach ($results['facets']['role']['buckets'] AS $v): ?>
                                            <option value="<?php echo $v['value'];?>"<?php if( isset( $_POST['role'] ) && $_POST['role'] == $v['value'] ):?> selected="selected" <?php endif;?>><?php echo $v['value']; ?></option>
                                        <?php endforeach; ?>
                                    <?php endif;?>
                                </select>
                            </li>
                            <li>
                                <label for="level-filter">Level</label>
                                <select name="level" id="level-filter" class="select">
                                    <option>All</option>
                                    <?php if( is_array( $results['facets']['owner']['buckets'] ) ):?>
                                        <?php foreach ($results['facets']['level']['buckets'] AS $v): ?>
                                            <option value="<?php echo $v['value'];?>"<?php if( isset( $_POST['level'] ) && $_POST['level'] == $v['value'] ):?> selected="selected" <?php endif;?>><?php echo $v['value']; ?></option>
                                        <?php endforeach; ?>
                                    <?php endif;?>
                                </select>
                            </li>
                            <li>
                                <label for="test-status-filter">Test Status</label>
                                <select name="status" id="test-status-filter" class="select">
                                    <option>All</option>
                                    <?php if( is_array( $results['facets']['owner']['buckets'] ) ):?>
                                        <?php foreach ($results['facets']['status']['buckets'] AS $v): ?>
                                            <option value="<?php echo $v['value'];?>"<?php if( isset( $_POST['status'] ) && $_POST['status'] == $v['value'] ):?> selected="selected" <?php endif;?>><?php echo $v['value']; ?></option>
                                        <?php endforeach; ?>
                                    <?php endif;?>
                                </select>
                            </li>
                            <li>
                                <label>Claim Date Range</label>
                                <div class="date-filter filter-from">
                                    <input type="text" class="input datepicker" placeholder="From" name="date_from" <?php if( isset( $_POST['date_from'] ) ):?> value="<?php echo $_POST['date_from'];?>" <?php endif;?>/>
                                </div>
                                <div class="date-filter filter-to">
                                    <input type="text" class="input datepicker" placeholder="To" name="date_to" <?php if( isset( $_POST['date_to'] ) ):?> value="<?php echo $_POST['date_to'];?>" <?php endif;?>/>
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
                    <?php if( $results['hits']['found'] > 0):?>
                        Showing <?php echo $results['hits']['start'] + 1?> - <?php echo $results['hits']['found'] < ( $results['hits']['start'] + 1 ) * 10 ?  $results['hits']['found'] : ( $results['hits']['start'] + 1 ) * 10; ?> of <b><?php echo $results['hits']['found']?></b> Results
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
            <?php if( $results['hits']['found'] > 0 ): ?>
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
                        <?php if( $results['hits']['found'] > 0):?>
                            <?php foreach( $results['hits']['hit'] as $row ): ?>
                                <?php $row_data = $row['fields'];?>
                                <tr>
                                    <td class="first"><a href="<?php echo get_permalink( $row_data['post_id'] );?>" class="blue_txt"><?php echo $row_data['name']?></a></td>
                                    <td><?php echo $row_data['version']; ?></td>
                                    <td><?php echo $row_data['owner']; ?></td>
                                    <td>
                                        <?php if ($row_data['type'] == 'Software Product'): ?>
                                            Product
                                        <?php else: ?>
                                            Service
                                        <?php endif; ?>
                                    </td>
                                    <td class="test-suite-column"><a href="<?php echo get_permalink( $row_data['suite_id'] );?>"><?php echo $row_data['test_suite'];?></a></td>
                                    <td><?php if( ! empty( $row_data['role'] ) ) echo implode( ', ', $row_data['role'] );?></td>
                                    <td><?php if( ! empty( $row_data['level'] ) ) echo implode( ', ', $row_data['level'] );?></td>
                                    <td><?php echo $row_data['status'];?></td>
                                    <td><?php echo $row_data['test_type'];?></td>
                                    <?php $claim_date = date( 'Y-m-d', strtotime($row_data['date'] ) );?>
                                    <td class="last"><?php if( $claim_date != '1970-01-01') echo $claim_date;?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif;?>
                        </tbody>
                    </table>
                    <div class="pagination-wrapper">
                        <div class="pagination">
                            <?php
                            $args = array(
                                'base'         =>  get_permalink() . '%_%?',
                                'format'       => 'page/%#%',
                                'total'        => ceil( $results['hits']['found'] / 10 ),
                                'current'      => $page,
                                'show_all'     => False,
                                'end_size'     => 5,
                                'mid_size'     => 5,
                                'prev_next'    => True,
                                'prev_text'    => __('« Previous'),
                                'next_text'    => __('Next »'),
                                'type'         => 'plain',
                                'add_args'     => false,
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