<?php
/***
* Template Name: Search Products & Services Page
* Test Suite Search Page
* This page will be included from search page
*/

get_header(); 

global $post; 

$baseURL = get_permalink();
$params = array();
$filterParams = array();

$post_type = 'product-service';

$posts_per_page = 10;
    
//Search Test Suites
$args = get_products_args();
//Getting Search Query
$term = trim(isset($_GET['q']) ? $_GET['q'] : '');

if($term)
    $args['s'] = $term;

if($term)
    $params[] = 'q=' . $term;
    
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
$postsIds = $notInPostIdsArray = array();
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
    <div id="search_title_block" class="page-title-block column noshadow">                    
        <?php get_sidebar('search') ?>        
        <p class="search_result_label">            
            <?php if (count($products) > 0) { ?>
                <?php
                    if ($get_posts->found_posts < $page * $posts_per_page){
                        $last_post_of_page = $get_posts->found_posts;
                    } else {
                        $last_post_of_page = $page * $posts_per_page;
                    }
                ?>
                Showing <?php echo ($page - 1) * $posts_per_page + 1?> - <?php echo $last_post_of_page; ?> of <b><?php echo $get_posts->found_posts?></b> Results
                <?php if($term){ ?> for "<b><?php echo $term?></b>" <?php } ?>
            <?php }else{ ?>
                No result found!
            <?php } ?>
        </p>
        <?php if(count($params) > 0) {?>
        <a href="<?php echo get_permalink()?>" class="action-btn delete-grey-btn" id="clear-search-filter-btn"><span class="p"></span><span class="t">Clear All</span></a>
        <?php } ?>
        <?php if (count($products) > 0) : ?>
            <a href="<?php echo add_query_arg( 'download', '1' )?>" class="blue-btn action-btn icon-btn" style="margin-top: 20px; float: right;">Download Results</a>
        <?php endif;?>
        <div class="clear"></div>
    </div> <!-- end search_title_block -->
        
    <?php if(count($products) > 0) {?> 
    <div id="search_product_block" class="search_result_block">      
        <div class="four_fifths right">
            <div class="column padding20-10">
                <div class="grid dark_gray_txt">
                    <div class="grid_head grid_head_border">       
                        <?php if(is_admin() || is_super_admin() || groups_is_user_admin_in_any_community( get_current_user_id() ) ): ?>
                        <div class="grid_cell nopaddingtop width25P">Name</div>
                        <div class="grid_cell nopaddingtop width10P">Visibility</div>
                        <?php else: ?>
                        <div class="grid_cell nopaddingtop width35P">Name</div>
                        <?php endif; ?>                        
                        <div class="grid_cell nopaddingtop width15P tocenter">Owner</div>
                        <div class="grid_cell nopaddingtop width20P tocenter">Type</div>
                        <div class="grid_cell nopaddingtop width15P tocenter">Date</div>
                        <div class="grid_cell nopaddingtop width15P tocenter">Claims</div>
                        <div class="clear"></div>                        
                    </div>
                    <div class="grid_body">
                        <?php
                            foreach($products as $row){
                                $product = new ProductAndService($row->ID);
                                $product->load();
                                
                                $owner = $product->owner;
                                if(!$owner)
                                    $owner = get_user_meta($row->post_author, 'user_organisation', ture);
                                $productType = $product->type;
                                $productDate = $product->release_date;
                                
                                $groupID = get_post_meta($row->ID, 'community_id', true);
                                $group = groups_get_group( array( 'group_id' => $groupID ) )
                        ?>
                        <div class="grid_row grid_row_border">
                            <?php if(is_admin() || is_super_admin() || groups_is_user_admin_in_any_community( get_current_user_id() )): ?>
                            <div class="grid_cell width25P">
                                <h5><a href="<?php echo get_permalink($row->ID)?>" class="blue_txt"><?php echo apply_filters('the_title', $row->post_title)?></a></h5>
                                <?php echo apply_filters('the_excerpt', $row->post_excerpt) ?>
                            </div>
                            <div class="grid_cell width10P">
                                <?php echo !$product->visibility ? 'Public' : $product->visibility; ?>
                            </div>
                            <?php else: ?>
                            <div class="grid_cell width35P">
                                <h5><a href="<?php echo get_permalink($row->ID)?>" class="blue_txt"><?php echo apply_filters('the_title', $row->post_title)?></a></h5>
                                <?php echo apply_filters('the_excerpt', $row->post_excerpt) ?>
                            </div>
                            <?php endif; ?>                            
                            <div class="grid_cell width15P tocenter">
                                <?php echo $owner?>
                            </div>           
                            <div class="grid_cell width20P tocenter">
                                <?php echo $productType?>
                            </div>                                       
                            <div class="grid_cell width15P tocenter">
                            <?php                            
                                echo date('M Y', strtotime($productDate));
                            ?>
                            </div>
                            <div class="grid_cell width15P tocenter">
                                <?php
                                    $testPlansClaimsCounter = 0;
                                    $testPlans = getTestPlansByProductId($product->id);
                                    $processed_claims = array();
                                    foreach($testPlans as $testPlan){
                                        if( ! get_the_title($testPlan->suite_id) ){
                                            continue;
                                        }
                                        $claim = getClaimByTestPlanData( array( 'product_id' => $product->id, 'suite_id' => $testPlan->suite_id ) );
                                        if( in_array( $claim->id, $processed_claims ) ){
                                            $claim = false;
                                        } else{
                                            array_push( $processed_claims, $claim->id );
                                        }
                                        $testPlan->level = trim( str_replace( ';;', ' ', $testPlan->level ) );
                                        $testPlan->role = trim( str_replace( ';;', ' ', $testPlan->role ) );
                                        if( $claim ){
                                            $claim->role = trim( str_replace( ';;', ' ', $claim->role ) );
                                            $claim->conformance_level = trim( str_replace( ';;', ' ', $claim->conformance_level ) );
                                        }
                                        if( $claim && ( $claim->conformance_level !== $testPlan->level || $claim->role !== $testPlan->role ) ){
                                            $testPlansClaimsCounter++;
                                        }
                                        $testPlansClaimsCounter++;
                                    }
                                    echo !$testPlansClaimsCounter ? 'None' : $testPlansClaimsCounter;
                                ?>
                            </div>                       
                            <div class="clear"></div>
                        </div>
                        <?php                                          
                            }
                        ?>
                    </div>
                    <div class="space30"></div>
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
                    <div class="space30"></div>
                </div>
            </div>
        </div>
        <div class="fifth left expandable">
            <div class="column">            
                <form name="form_filter" id="form_filter" action="<?php echo get_permalink()?>" method="get">
                    <?php if($term){ ?>
                    <input type="hidden" name="q" value="<?php echo $term?>" />
                    <?php } ?>
                    <div class="expandable">
                        <h6 class="exp_title">Type</h6>
                        <div class="exp_content">
                        <?php
                            foreach ($caseTypes as $k => $v){
                        ?>
                            <label for="<?php echo $k?>" class="blue_txt">
                                <input type="checkbox" name="type[]" value="<?php echo $k?>" id="<?php echo $k?>" <?php echo in_array($k, $filterType) ? 'checked="checked"' : '' ?> class="input_filter"> <?php echo $k?> (<?php echo $v?>)
                            </label>
                            <div class="clear"></div>
                        <?php
                            }
                        ?>
                        </div>
                    </div>
                    <div class="expandable">
                        <h6 class="exp_title">Test Suite</h6>
                        <div class="exp_content">
                        <?php
                            foreach ($testSuites as $k => $v){
                        ?>
                            <label for="<?php echo $k?>" class="blue_txt"><input type="checkbox" name="test_suite[]" <?php echo in_array($k, $filterTestSuite) ? 'checked="checked"' : '' ?> value="<?php echo $k?>" id="<?php echo $k?>" class="input_filter"> <?php echo $k?> (<?php echo $v?>)</label>
                            <div class="clear"></div>
                        <?php
                            }
                        ?>
                        </div>
                    </div>
                    <div class="expandable">
                        <h6 class="exp_title">Year of Issue</h6>
                        <div class="exp_content">
                        <?php
                            foreach ($caseIssueYears as $k => $v){
                        ?>
                            <label for="<?php echo $k?>" class="blue_txt"><input type="checkbox" name="product_year[]" <?php echo in_array($k, $filterYear) ? 'checked="checked"' : '' ?> value="<?php echo $k?>" id="<?php echo $k?>" class="input_filter"> <?php echo $k?> (<?php echo $v?>)</label>
                            <div class="clear"></div>
                        <?php
                            }
                        ?>
                        </div>
                    </div>                   
                </form>
            </div>
        </div>
        <div class="clear"></div>

    </div> <!-- end search_result_block -->
    <?php } ?>
  </div>

<div class="clear"></div>
<?php get_footer() ?>