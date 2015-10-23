<?php
/***
 * Template Name: Product / Service Registry Search
 */

get_header();

global $post;

$baseURL = get_permalink();

$cloud_search = new CloudSearch();
$params = array();
$params1 = array();
$is_download = false;
if( isset( $_GET['download'] ) ){
    $is_download = true;
    unset( $_GET['download']);
}
if( $_GET ){
    foreach( $_GET AS $k => $v ){
        $params[] = urlencode($k) . '=' . urlencode($v);
        if ($k != 'orderby' && $k != 'order') {
            $params1[] = urlencode($k) . '=' . urlencode($v);
        }
    }
}

$orderby = isset($_GET['orderby']) ? $_GET['orderby'] : 'name';
$order = isset($_GET['order']) ? $_GET['order'] : 'asc';

$page = get_query_var('paged') ? get_query_var('paged') : 1;
$_GET['page'] = $page;
$results = $cloud_search->search( $_GET );
if( $is_download ){
    $results = $cloud_search->search( $_GET, true );
    generate_and_download( $results );
}
?>
<div class="content container" id="search">
    <div class="column search-results">
        <form name="form_filter" id="form_filter" action="/products-and-services/" method="get">
            <div class="search-result-form">
                <div class="searchform">
                    <input type="text" name="q" id="q" class="keyword" value="<?php echo isset( $_GET['q'] ) ? htmlspecialchars(trim($_GET['q'] ) ) : ''?>" placeholder="Enter a Product Name, Service Name or Business Name" autocomplete="off" />
                    <input type="submit" id="search_test_suite_submit" class="search-button" value="" />
                </div>
            </div>
            <div class="search-results-filter">
                <h4 class="filter-head">Filter By:</h4>
                <div class="search-filters-box">
                    <ul class="search-filters-list clearfix">
                        <li class="first">
                            <label for="implementation-type-filter">Type</label>
                            <select name="type" id="implementation-type-filter" class="select">
                                <option>All</option>
                                <?php if( is_array( $results->getPath('facets/type/buckets') ) ):?>
                                    <?php foreach(  $results->getPath('facets/type/buckets') AS $v ): ?>
                                        <?php if ($v['value'] == 'Web Service'):?>
                                            <option value="<?php echo $v['value'];?>" <?php if( isset( $_GET['type'] ) && $_GET['type'] == $v['value'] ):?> selected="selected" <?php endif;?>><?php echo 'Service'; ?></option>
                                        <?php elseif ($v['value'] == 'Agreement'):?>
                                            <option value="<?php echo $v['value'];?>" <?php if( isset( $_GET['type'] ) && $_GET['type'] == $v['value'] ):?> selected="selected" <?php endif;?>><?php echo 'Agreement'; ?></option>
                                            <?php else: ?>
                                            <option value="<?php echo $v['value'];?>" <?php if( isset( $_GET['type'] ) && $_GET['type'] == $v['value'] ):?> selected="selected" <?php endif;?>><?php echo 'Product'; ?></option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php endif;?>
                            </select>
                        </li>
                        <li>
                            <label for="owner-filter">Owner</label>
                            <select name="owner" id="owner-filter" class="select">
                                <option>All</option>
                                <?php if( is_array( $results->getPath('facets/owner/buckets') ) ):?>
                                    <?php foreach(  $results->getPath('facets/owner/buckets') AS $v ): ?>
                                        <option value="<?php echo $v['value'];?>"<?php if( isset( $_GET['owner'] ) && $_GET['owner'] == $v['value'] ):?> selected="selected" <?php endif;?>><?php echo $v['value']; ?></option>
                                    <?php endforeach; ?>
                                <?php endif;?>
                            </select>
                        </li>
                        <li>
                            <label for="test-suite-filter">Test Suite</label>
                            <select name="test_suite" id="test-suite-filter" class="select">
                                <option>All</option>
                                <?php if( is_array( $results->getPath('facets/test_suite/buckets') ) ):?>
                                    <?php foreach(  $results->getPath('facets/test_suite/buckets') AS $v ): ?>
                                        <option value="<?php echo $v['value'];?>"<?php if( isset( $_GET['test_suite'] ) && $_GET['test_suite'] == $v['value'] ):?> selected="selected" <?php endif;?>><?php echo $v['value']; ?></option>
                                    <?php endforeach; ?>
                                <?php endif;?>
                            </select>
                        </li>
                        <li>
                            <label for="test-type-filter">Test Type</label>
                            <select name="test_type" id="test-type-filter" class="select">
                                <option>All</option>
                                <?php if( is_array( $results->getPath('facets/test_type/buckets') ) ):?>
                                    <?php foreach(  $results->getPath('facets/test_type/buckets') AS $v ): ?>
                                        <option value="<?php echo $v['value'];?>"<?php if( isset( $_GET['test_type'] ) && $_GET['test_type'] == $v['value'] ):?> selected="selected" <?php endif;?>><?php echo $v['value']; ?></option>
                                    <?php endforeach; ?>
                                <?php endif;?>
                            </select>
                        </li>
                        <li class="first">
                            <label for="role-filter">Role</label>
                            <select name="role" id="role-filter" class="select">
                                <option>All</option>
                                <?php if( is_array( $results->getPath('facets/role/buckets') ) ):?>
                                    <?php foreach(  $results->getPath('facets/role/buckets') AS $v ): ?>
                                        <option value="<?php echo $v['value'];?>"<?php if( isset( $_GET['role'] ) && $_GET['role'] == $v['value'] ):?> selected="selected" <?php endif;?>><?php echo $v['value']; ?></option>
                                    <?php endforeach; ?>
                                <?php endif;?>
                            </select>
                        </li>
                        <li>
                            <label for="level-filter">Level</label>
                            <select name="level" id="level-filter" class="select">
                                <option>All</option>
                                <?php if( is_array( $results->getPath('facets/level/buckets') ) ):?>
                                    <?php foreach(  $results->getPath('facets/level/buckets') AS $v ): ?>
                                        <option value="<?php echo $v['value'];?>"<?php if( isset( $_GET['level'] ) && $_GET['level'] == $v['value'] ):?> selected="selected" <?php endif;?>><?php echo $v['value']; ?></option>
                                    <?php endforeach; ?>
                                <?php endif;?>
                            </select>
                        </li>
                        <li>
                            <label for="test-status-filter">Status</label>
                            <select name="status" id="test-status-filter" class="select">
                                <option>All</option>
                                <?php if( is_array( $results->getPath('facets/status/buckets') ) ):?>
                                    <?php foreach(  $results->getPath('facets/status/buckets') AS $v ): ?>
                                        <option value="<?php echo $v['value'];?>"<?php if( isset( $_GET['status'] ) && $_GET['status'] == $v['value'] ):?> selected="selected" <?php endif;?>><?php echo $v['value']; ?></option>
                                    <?php endforeach; ?>
                                <?php endif;?>
                            </select>
                        </li>
                        <li>
                            <label>Date</label>
                            <div class="date-filter filter-from">
                                <input type="text" class="input datepicker" placeholder="From" name="date_from" <?php if( isset( $_GET['date_from'] ) ):?> value="<?php echo htmlspecialchars($_GET['date_from']);?>" <?php endif;?>/>
                            </div>
                            <div class="date-filter filter-to">
                                <input type="text" class="input datepicker" placeholder="To" name="date_to" <?php if( isset( $_GET['date_to'] ) ):?> value="<?php echo $_GET['date_to'];?>" <?php endif;?>/>
                            </div>
                        </li>
                    </ul>
                    <div class="search-filter-actions">
                        <a class="action-btn process-btn submit-btn" href="#"><span class="p"></span><span class="t">Confirm</span></a>
                        <a href="<?php echo get_permalink(); ?>" class="action-btn clear-btn" id="clear-search-filter-btn"><span class="p"></span><span class="t">Clear</span></a>
                    </div>
                </div>
            </div>
            <input type="hidden" name="page" />
        </form>
        <?php if( $results['hits']['found'] > 0):?>
            <div class="search-results-count clearfix">
                <p class="search-results-count-label">
                    Showing <?php echo $results->getPath( 'hits/start' ) + 1 ;?> - <?php echo $results->getPath( 'hits/start' ) +  count( $results->getPath( 'hits/hit' ) );?> of <b><?php echo $results->getPath( 'hits/found' ); ?></b> Results
                    <?php if(isset($_GET['q']) && ! empty( $_GET['q'] ) ){ ?> for "<b><?php echo $_GET['q']?></b>" <?php } ?>
                </p>
                <?php if( $results->getPath( 'hits/found' ) > 0 ): ?>
                    <a href="<?php echo add_query_arg( 'download', '1' ); ?>" class="action-btn download-btn">
                        <span class="p"></span>
                        <span class="t">Download Results</span>
                    </a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <p class="no-data">No result found!</p>
        <?php endif; ?>

        <?php if( $results->getPath( 'hits/found' ) > 0 ): ?>
            <div class="search-result-list-wrapper">
                <table class="search-result-list">
                    <thead>
                    <tr>
                        <th class="first">
                            <a href="<?php echo get_permalink()?>?<?php echo implode("&", $params)?>&orderby=name&order=<?php echo $orderby == 'name' && $order == 'asc' ? 'desc' : 'asc'?>">Product / Service</a>
                            <div class="sorting-box">
                                <a href="<?php echo get_permalink()?>?<?php echo implode("&", $params)?>&orderby=name&order=asc"  class="sorting-link asc<?php if($orderby == 'name' && $order == 'asc'){ ?> current<?php } ?>"><span class="sort"></span></a>
                                <a href="<?php echo get_permalink()?>?<?php echo implode("&", $params)?>&orderby=name&order=desc" class="sorting-link desc<?php if($orderby == 'name' && $order == 'desc'){ ?> current<?php } ?>"><span class="sort"></span></a>
                            </div>
                        </th>
                        <th>Version</th>
                        <th>Owner</th>
                        <th>Type</th>
                        <th>Test Suite</th>
                        <th>Role</th>
                        <th>Level</th>
                        <th>Status</th>
                        <th>Test Type</th>
                        <th>
                            <a href="<?php echo get_permalink()?>?<?php echo implode("&", $params)?>&orderby=date&order=<?php echo $orderby == 'name' && $order == 'asc' ? 'desc' : 'asc'?>">Date</a>
                            <div class="sorting-box">
                                <a href="<?php echo get_permalink()?>?<?php echo implode("&", $params)?>&orderby=date&order=asc"  class="sorting-link asc<?php if($orderby == 'date' && $order == 'asc'){ ?> current<?php } ?>"><span class="sort"></span></a>
                                <a href="<?php echo get_permalink()?>?<?php echo implode("&", $params)?>&orderby=date&order=desc" class="sorting-link desc<?php if($orderby == 'date' && $order == 'desc'){ ?> current<?php } ?>"><span class="sort"></span></a>
                            </div>
                        <?php if( is_super_admin() ): ?>
                            <th>Action</th>
                        <?php endif; ?>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if( $results->getPath( 'hits/found' ) > 0):?>
                        <?php foreach( $results->getPath( 'hits/hit' ) AS $row ): ?>
                            <?php $row_data = $row['fields'];?>
                            <?php if( $row_data['type'][0] == 'Agreement' ):?>
                                <?php
                                    $agreement_id = str_replace( 'agreement_', '', $row['id'] );
                                    $agreement = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM wp_e2e_agreement WHERE id = %d ", $agreement_id ) );
                                    $requester_service = new Service( $agreement->requester_service_id );
                                    $requester_service->load();
                                    $responder_service = new Service( $agreement->responder_service_id );
                                    $responder_service->load();
                                ?>
                                <tr>
                                    <td class="first">
                                        <div class="agreement-row"><a href="<?php echo get_permalink( $agreement->requester_service_id );?>" class="blue_txt"><?php echo $requester_service->service_name;?></a></div>
                                        <a href="<?php echo get_permalink( $agreement->responder_service_id );?>" class="blue_txt"><?php echo $responder_service->service_name;?></a>
                                    </td>
                                    <td>
                                        <div class="agreement-row"><?php echo $requester_service->service_version;?></div>
                                        <?php echo $responder_service->service_version;?>
                                    </td>
                                    <td>
                                        <div class="agreement-row"><?php echo $requester_service->service_owner;?></div>
                                        <?php echo $responder_service->service_owner;?>
                                    </td>
                                    <td>Agreement</td>
                                    <td class="test-suite-column">
                                        <div class="agreement-row"><a href="<?php echo get_permalink( $requester_service->service_suite_id );?>"><?php echo get_the_title( $requester_service->service_suite_id );?></a></div>
                                        <a href="<?php echo get_permalink( $responder_service->service_suite_id );?>"><?php echo get_the_title( $responder_service->service_suite_id );?></a>
                                    </td>
                                    <td>
                                        <div class="agreement-row"><?php if( ! empty( $requester_service->service_roles ) ) echo implode( ', ', $requester_service->service_roles );?></div>
                                        <?php if( ! empty( $responder_service->service_roles ) ) echo implode( ', ', $responder_service->service_roles );?>
                                    </td>
                                    <td>
                                        <div class="agreement-row"><?php if( ! empty( $requester_service->service_levels ) ) echo implode( ', ', $requester_service->service_levels );?></div>
                                        <?php if( ! empty( $responder_service->service_levels ) ) echo implode( ', ', $responder_service->service_levels );?>
                                    </td>
                                    <td>
                                        <?php if( $row_data['test_type'][0] == 'Certification' && $row_data['status'][0] == 'Verified' && $wpdb->get_var( $wpdb->prepare("SELECT has_exclusions FROM wp_compliance_claims WHERE id = %d ",  end( explode( '_', $row['id'] ) ) ) ) == '1' ):?>
                                            <a href="#"  class="has-tooltip" title="Some test cases were excluded/not performed during testing. Please consult the claim certificate on the product summary page for more details."><img src="/wp-content/themes/bp-child/images/verify_icon.png" style="float:left;" /></a><?php echo $row_data['status'][0];?>
                                        <?php else:?>
                                            <?php echo $row_data['status'][0];?>
                                        <?php endif;?>
                                    </td>
                                    <td><?php echo $row_data['test_type'][0];?></td>
                                    <?php $claim_date = date( 'Y-m-d', strtotime( $row_data['date'][0] ) );?>
                                    <td class="last"><?php if( $claim_date != '1970-01-01') echo $claim_date;?></td>
                                    <?php if( is_super_admin() ): ?>
                                        <td class="remove_entry"><a href="/?_psnonce=<?php echo wp_create_nonce('get-delete-search-entry');?>&id=<?php echo $row['id'];?>" rel="custom-popup" cp-type="ajax" cp-removeBoxAfterClose=1 class="action-btn delete-btn icon-btn delete_search_entry has-tooltip"><span class="simple_tooltip radius6" style="margin-left: -90px; width: 170px;">Delete <?php echo $row_data['name'][0];?><span></span></span><span class="p"></span></a></td>
                                    <?php endif; ?>
                                </tr>
                            <?php else:?>
                                <tr>
                                    <td class="first"><a href="<?php echo get_permalink( $row_data['post_id'][0] );?>" class="blue_txt"><?php echo $row_data['name'][0]?></a></td>
                                    <td><?php echo $row_data['version'][0]; ?></td>
                                    <td><?php echo $row_data['owner'][0]; ?></td>
                                    <td>
                                        <?php if ($row_data['type'][0] == 'Software Product'): ?>
                                            Product
                                        <?php else: ?>
                                            Service
                                        <?php endif; ?>
                                    </td>
                                    <td class="test-suite-column"><a href="<?php echo get_permalink( $row_data['suite_id'][0] );?>"><?php echo $row_data['test_suite'][0];?></a></td>
                                    <td><?php if( ! empty( $row_data['role'] ) ) echo implode( ', ', $row_data['role'] );?></td>
                                    <td><?php if( ! empty( $row_data['level'] ) ) echo implode( ', ', $row_data['level'] );?></td>
                                    <td>
                                        <?php if( $row_data['test_type'][0] == 'Certification' && $row_data['status'][0] == 'Verified' && $wpdb->get_var( $wpdb->prepare("SELECT has_exclusions FROM wp_compliance_claims WHERE id = %d ",  end( explode( '_', $row['id'] ) ) ) ) == '1' ):?>
                                            <a href="#"  class="has-tooltip" title="Some test cases were excluded/not performed during testing. Please consult the claim certificate on the product summary page for more details."><img src="/wp-content/themes/bp-child/images/verify_icon.png" style="float:left;" /></a><?php echo $row_data['status'][0];?>
                                        <?php else:?>
                                            <?php echo $row_data['status'][0];?>
                                        <?php endif;?>
                                    </td>
                                    <td><?php echo $row_data['test_type'][0];?></td>
                                    <?php $claim_date = date( 'Y-m-d', strtotime($row_data['date'][0] ) );?>
                                    <td class="last"><?php if( $claim_date != '1970-01-01') echo $claim_date;?></td>
                                    <?php if( is_super_admin() ): ?>
                                        <td class="remove_entry"><a href="/?_psnonce=<?php echo wp_create_nonce('get-delete-search-entry');?>&id=<?php echo $row['id'];?>" rel="custom-popup" cp-type="ajax" cp-removeBoxAfterClose=1 class="action-btn delete-btn icon-btn delete_search_entry has-tooltip"><span class="simple_tooltip radius6" style="margin-left: -90px; width: 170px;">Delete <?php echo $row_data['name'][0];?><span></span></span><span class="p"></span></a></td>
                                    <?php endif; ?>
                                </tr>
                            <?php endif;?>
                        <?php endforeach; ?>
                    <?php endif;?>
                    </tbody>
                </table>
                <script>
                        jQuery( document).ready(function($){
                            var rowHeight = 0,
                                agreementFirstRows = jQuery('.search-result-list .agreement-row');

                                agreementFirstRows.each(function(){
                                    if ($(this).height() > rowHeight){
                                        rowHeight = $(this).height();
                                    }
                                });
                                agreementFirstRows.height(rowHeight);
                        });
                </script>
                <div class="pagination-wrapper flat-pagination">
                    <div class="pagination">
                        <?php
                        $args = array(
                            'base'         =>  get_permalink() . '%_%?',
                            'format'       => 'page/%#%',
                            'total'        => ceil( $results['hits']['found'] / SEARCH_RESULTS_LIMIT ),
                            'current'      => $page,
                            'show_all'     => False,
                            'end_size'     => 5,
                            'mid_size'     => 5,
                            'prev_next'    => True,
                            'prev_text'    => __('Previous'),
                            'next_text'    => __('Next'),
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
