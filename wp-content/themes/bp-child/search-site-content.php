<?php
/***
 * Template Name: Site Search Results
 */

get_header();

global $post;

$baseURL = get_permalink();

$cloud_search = new CloudSearch();
$params = array();
$params1 = array();
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

if( isset( $_GET['download']) ){
    unset( $_GET['download']);
    $results = $cloud_search->search( $_GET, true );
    generate_and_download( $results );
}
?>
<div class="content container" id="search">
    <div class="column search-results">
        <form name="form_filter" id="form_filter" action="/search-results/" method="get">
            <div class="search-result-form">
                <div class="searchform">
                    <input type="text" name="q" id="q" class="keyword" value="<?php echo htmlspecialchars(trim(isset($_GET['q']) ? $_GET['q'] : '')) ?>" placeholder="Search" autocomplete="off" />
                    <input type="submit" id="search_test_suite_submit" class="search-button" value="" />
                </div>
            </div>
            <div class="search-results-filter">
                <h4 class="filter-head">Filter By:</h4>
                <div class="search-filters-box">
                    <ul class="search-filters-list clearfix">
                        <li class="first">
                            <label for="content-type-filter">Type</label>
                            <select name="type" id="content-type-filter" class="select">
                                <option>All</option>
                                <option>Forum Post</option>
                                <option>Wiki Article</option>
                                <option>Site Page</option>
                                <option>Test Case</option>
                                <option>Blog</option>
                            </select>
                        </li>
                        <li>
                            <label for="community-filter">Community</label>
                            <select name="owner" id="community-filter" class="select">
                                <option>All</option>
                                <option>SuperStream</option>
                                <option>ebMS</option>
                            </select>
                        </li>
                        <li>
                            <label>Last Update</label>
                            <div class="date-filter filter-from">
                                <input type="text" class="input datepicker" placeholder="From" name="date_from" <?php if( isset( $_GET['date_from'] ) ):?> value="<?php echo $_GET['date_from'];?>" <?php endif;?>/>
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
                    Showing <?php echo $results['hits']['start'] + 1 ;?> - <?php echo $results['hits']['start'] +  count($results['hits']['hit']) ?> of <b><?php echo $results['hits']['found']?></b> Results
                    <?php if(isset($_GET['q']) && ! empty( $_GET['q'] ) ){ ?> for "<b><?php echo $_GET['q']?></b>" <?php } ?>
                </p>
                <?php if( $results['hits']['found'] > 0 ): ?>
                    <a href="<?php echo add_query_arg( 'download', '1' ); ?>" class="action-btn download-btn">
                        <span class="p"></span>
                        <span class="t">Download Results</span>
                    </a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <p class="no-data">No result found!</p>
        <?php endif; ?>

        <?php if( $results['hits']['found'] > 0 ): ?>
            <div class="search-result-list-wrapper">
                <table class="search-result-list site-search-result-list">
                    <thead>
                        <tr>
                            <th class="first row-title">Title</th>
                            <th class="row-description">Description</th>
                            <th>
                                <a href="<?php echo get_permalink()?>?<?php echo implode("&", $params)?>&orderby=date&order=<?php echo $orderby == 'type' && $order == 'asc' ? 'desc' : 'asc'?>">Type</a>
                                <div class="sorting-box">
                                    <a href="<?php echo get_permalink()?>?<?php echo implode("&", $params)?>&orderby=type&order=asc"  class="sorting-link asc<?php if($orderby == 'type' && $order == 'asc'){ ?> current<?php } ?>"><span class="sort"></span></a>
                                    <a href="<?php echo get_permalink()?>?<?php echo implode("&", $params)?>&orderby=type&order=desc" class="sorting-link desc<?php if($orderby == 'type' && $order == 'desc'){ ?> current<?php } ?>"><span class="sort"></span></a>
                                </div>
                            <th>Community</th>
                            <th class="last row-date">
                                <a href="<?php echo get_permalink()?>?<?php echo implode("&", $params)?>&orderby=date&order=<?php echo $orderby == 'name' && $order == 'asc' ? 'desc' : 'asc'?>">Last Update</a>
                                <div class="sorting-box">
                                    <a href="<?php echo get_permalink()?>?<?php echo implode("&", $params)?>&orderby=date&order=asc"  class="sorting-link asc<?php if($orderby == 'date' && $order == 'asc'){ ?> current<?php } ?>"><span class="sort"></span></a>
                                    <a href="<?php echo get_permalink()?>?<?php echo implode("&", $params)?>&orderby=date&order=desc" class="sorting-link desc<?php if($orderby == 'date' && $order == 'desc'){ ?> current<?php } ?>"><span class="sort"></span></a>
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if( $results['hits']['found'] > 0):?>
                        <?php foreach( $results['hits']['hit'] as $row ): ?>
                        <tr>
                            <td class="first"><a href="#">Page Title Lorem Ipsum Text</a></td>
                            <td>
                                <p class="short-description">This is Photoshop's version  of Lorem Ipsum. Proin gravida nibh vel velit auctor aliquet. Aenean sollicitudin, lorem quis bibendum auctor, nisi elit consequat ipsum, nec sagittis sem nibh id elit. Duis sed odio sit amet nibh vulputate cursus a sit amet mauris. Morbi accumsan ipsum velit. Nam nec tellus a odio tincidunt auctor a ornare odio. Sed non  mauris vitae erat ...</p>
                            </td>
                            <td>Forum Post</td>
                            <td><a href="#">SuperStream</a></td>
                            <td class="last">2014-11-11</td>
                        </tr>
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
<script>
//    jQuery( document).ready(function(){
//        jQuery('.page-numbers').on('click', function(e){
//            e.preventDefault();
//            var page_number = jQuery( this ).attr( 'href' ).split('/');
//            page_number = page_number[page_number.length - 1].replace( '?', '' );
//            if( page_number ) {
//                jQuery('#page_v').val(page_number);
//                jQuery('#form_filter').submit();
//            } else{
//                location.reload();
//            }
//            return false;
//        });
//    });
</script>
<?php get_footer() ?>
