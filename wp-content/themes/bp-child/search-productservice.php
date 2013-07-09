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
$args = array(
        'post_type' => $post_type,         
        'posts_per_page' => -1,
        'tax_query' => array('relation' => 'and')
);

//Getting Search Query
$term = trim(isset($_GET['q']) ? $_GET['q'] : '');

if($term)
    $args['s'] = $term;

if($term)
    $params[] = 'q=' . $term;
    
//Getting Filter Params
$filterType = getFilterParam('type');
$filterOwner = getFilterParam('owner');
$filterYear = getFilterParam('product_year');
$filterStatus = getFilterParam('status');

foreach($filterType as $f)
{
    $args['meta_query'][] = array('key' => 'product_type', 'value' => $f, 'compare' => '=');
    $params[] = urlencode('type[]') . '=' . $f;
    $filterParams['type[]'] = $f;
}
foreach($filterOwner as $v)
{
    $args['meta_query'][] = array('key' => 'product_owner', 'value' => $v, 'compare' => '=');
    $params[] = urlencode('owner[]') . '=' . $v;
    $filterParams['owner[]'] = $v;
}
foreach($filterYear as $v)
{
    $args['meta_query'][] = array('key' => 'product_date', 'value' => $v . "-", 'compare' => 'LIKE');
    $params[] = urlencode('product_year[]') . '=' . $v;
    $filterParams['product_year[]'] = $v;
}
foreach($filterStatus as $v)
{
    $args['meta_query'][] = array('key' => 'product_status', 'value' => $v, 'compare' => '=');
    $params[] = urlencode('status[]') . '=' . $v;
    $filterParams['status[]'] = $v;
}

//For Right Panels
$caseTypes = array();
$caseOwners = array();
$caseIssueYears = array();
$caseStatuses = array();
    
//For Filter Values
$all_posts = new WP_Query($args);
$allSuites = $all_posts->get_posts();

foreach($allSuites as $row)
{
    $issueStatus = get_post_meta($row->ID, 'product_status', true);
    $issueDate = get_post_meta($row->ID, 'product_date', true);
    
    $productType = get_post_meta($row->ID, 'product_type', true);    
    $caseTypes[$productType] = isset($caseTypes[$productType]) ? $caseTypes[$productType] + 1 : 1;
    
    $issuer = get_post_meta($row->ID, 'product_owner', true);
    if(!$issuer)
        $issuer = get_user_meta($row->post_author, 'user_organisation', ture);
    $caseOwners[$issuer] = isset($caseOwners[$issuer]) ? $caseOwners[$issuer] + 1 : 1;
    
    $tsYear = date('Y', strtotime($issueDate));
    $caseIssueYears[$tsYear] = isset($caseIssueYears[$tsYear]) ? $caseIssueYears[$tsYear] + 1 : 1;
    
    $caseStatuses[$issueStatus] = isset($caseStatuses[$issueStatus]) ? $caseStatuses[$issueStatus] + 1 : 1;                    
}

//Getting Pagination Params
$page = get_query_var('page') ? get_query_var('page') : 1;
$args['paged'] = $page;
$args['posts_per_page'] = $posts_per_page;

$get_posts = new WP_Query($args);
$testsuites = $get_posts->get_posts();
?>
<div class="content container" id="search">      
    <div id="search_title_block" class="page-title-block column noshadow">                    
        <?php get_sidebar('search') ?>        
        <p class="search_result_label">            
            <?php if (count($testsuites) > 0) { ?>
                Showing <?php echo ($page - 1) * $posts_per_page + 1?> - <?php echo $page * $posts_per_page ?> of <b><?php echo $get_posts->found_posts?></b> Results
                <?php if($term){ ?> for "<b><?php echo $term?></b>" <?php } ?>
            <?php }else{ ?>
                No result found!
            <?php } ?>
        </p>
        <?php if(count($params) > 0) {?>
        <a href="<?php echo get_permalink()?>" class="action-btn delete-grey-btn" id="clear-search-filter-btn"><span class="p"></span><span class="t">Clear All</span></a>
        <?php } ?>
        <div class="clear"></div>        
    </div> <!-- end search_title_block -->
        
    <?php if(count($testsuites) > 0) {?> 
    <div id="search_result_block" class="search_result_block">      
        <div class="four_fifths right">
            <div class="column padding20-10">
                <div class="grid dark_gray_txt">
                    <div class="grid_head grid_head_border">                        
                        <div class="grid_cell nopaddingtop width35P">Name</div>
                        <div class="grid_cell nopaddingtop width15P tocenter">Owner</div>
                        <div class="grid_cell nopaddingtop width15P tocenter">Type</div>
                        <div class="grid_cell nopaddingtop width20P tocenter">Date</div>
                        <div class="grid_cell nopaddingtop width15P tocenter">Status</div>                        
                        <div class="clear"></div>                        
                    </div>
                    <div class="grid_body">
                        <?php
                            foreach($testsuites as $row){
                                $owner = get_post_meta($row->ID, 'product_owner', true);
                                if(!$owner)
                                    $owner = get_user_meta($row->post_author, 'user_organisation', ture);
                                $productType = get_post_meta($row->ID, 'product_type', true);
                                $productDate = get_post_meta($row->ID, 'product_date', true);
                                $productStatus = get_post_meta($row->ID, 'product_status', true);
                                $groupID = get_post_meta($row->ID, 'community_id', true);
                                $group = groups_get_group( array( 'group_id' => $groupID ) )
                        ?>
                        <div class="grid_row grid_row_border">
                            <div class="grid_cell width35P">
                                <h5><a href="<?php echo get_permalink($row->ID)?>" class="blue_txt"><?php echo apply_filters('the_title', $row->post_title)?></a></h5>
                                <?php echo apply_filters('the_excerpt', $row->post_excerpt) ?>
                            </div>
                            <div class="grid_cell width15P tocenter">
                                <?php echo $owner?>
                            </div>           
                            <div class="grid_cell width15P tocenter">
                                <?php echo $productType?>
                            </div>                                       
                            <div class="grid_cell width20P tocenter">
                            <?php                            
                                echo date('M Y', strtotime($productDate));
                            ?>
                            </div>
                            <div class="grid_cell width15P tocenter">
                                <?php                             
                                if($productStatus == 'Active')
                                    echo '<span class="status_btn status_btn_active">ACTIVE</span>';
                                else if($productStatus == 'On Hold')
                                    echo '<span class="status_btn status_btn_on_hold">ON HOLD</span>';
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
                                    'base'         =>  get_permalink() . '?%_%',
                                    'format'       => '&page=%#%',
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
                        <h6 class="exp_title">Owner</h6>
                        <div class="exp_content">
                        <?php
                            foreach ($caseOwners as $k => $v){
                        ?>
                            <label for="<?php echo $k?>" class="blue_txt"><input type="checkbox" name="owner[]" <?php echo in_array($k, $filterOwner) ? 'checked="checked"' : '' ?> value="<?php echo $k?>" id="<?php echo $k?>" class="input_filter"> <?php echo $k?> (<?php echo $v?>)</label>
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
                    <div class="expandable">
                        <h6 class="exp_title">Status</h6>
                        <div class="exp_content">
                        <?php
                            foreach ($caseStatuses as $k => $v){
                        ?>
                            <label for="<?php echo $k?>" class="blue_txt"><input type="checkbox" name="status[]" <?php echo in_array($k, $filterStatus) ? 'checked="checked"' : '' ?> value="<?php echo $k?>" id="<?php echo $k?>" class="input_filter"> <?php echo $k?> (<?php echo $v?>)</label>
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