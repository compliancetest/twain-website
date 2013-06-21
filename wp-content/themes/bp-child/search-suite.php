<?php
/***
* Template Name: Search Test Suites Page
* Test Suite Search Page
* This page will be included from search page
*/

get_header(); 

global $post; 

$baseURL = get_permalink();
$params = array();
$filterParams = array();

$post_type = 'test-suite';

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
$filterIssuer = getFilterParam('issuer');
$filterYear = getFilterParam('issue_year');
$filterStatus = getFilterParam('status');

foreach($filterType as $f)
{
    $args['tax_query'][] = array('taxonomy' => 'test_suite_type', 'field' => 'slug', 'terms' => $f);
    $params[] = urlencode('type[]') . '=' . $f;
    $filterParams['type[]'] = $f;
}
foreach($filterIssuer as $v)
{
    $args['meta_query'][] = array('key' => 'ts_issuer', 'value' => $v, 'compare' => '=');
    $params[] = urlencode('issue_year[]') . '=' . $v;
    $filterParams['issue_year[]'] = $v;
}
foreach($filterYear as $v)
{
    $args['meta_query'][] = array('key' => 'ts_issue_date', 'value' => $v . "-", 'compare' => 'LIKE');
    $params[] = urlencode('year[]') . '=' . $v;
    $filterParams['year[]'] = $v;
}
foreach($filterStatus as $v)
{
    $args['meta_query'][] = array('key' => 'ts_status', 'value' => $v, 'compare' => '=');
    $params[] = urlencode('status[]') . '=' . $v;
    $filterParams['status[]'] = $v;
}

//For Right Panels
$tsTypes = array();
$tsIssuers = array();
$tsIssueYears = array();
$tsStatuses = array();
    
//For Filter Values
$all_posts = new WP_Query($args);
$allSuites = $all_posts->get_posts();

foreach($allSuites as $row)
{
    $issueStatus = get_post_meta($row->ID, 'ts_status', true);
    $issueDate = get_post_meta($row->ID, 'ts_issue_date', true);
    
    $typeList = wp_get_post_terms($row->ID, 'test_suite_type', array("fields" => "all"));
    foreach($typeList as $r)
    {
        $tsTypes[$r->name] = isset($tsTypes[$r->name]) ? $tsTypes[$r->name] + 1 : 1;
    }    
    
    $issuer = get_post_meta($row->ID, 'ts_issuer', true);
    $tsIssuers[$issuer] = isset($tsIssuers[$issuer]) ? $tsIssuers[$issuer] + 1 : 1;
    
    $tsYear = date('Y', strtotime($issueDate));
    $tsIssueYears[$tsYear] = isset($tsIssueYears[$tsYear]) ? $tsIssueYears[$tsYear] + 1 : 1;
    
    $tsStatuses[$issueStatus] = isset($tsStatuses[$issueStatus]) ? $tsStatuses[$issueStatus] + 1 : 1;                    
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
                        <div class="grid_cell nopaddingtop width50P">Name</div>
                        <div class="grid_cell nopaddingtop width15P tocenter">Issuer</div>
                        <div class="grid_cell nopaddingtop width20P tocenter">Date</div>
                        <div class="grid_cell nopaddingtop width15P tocenter">Status</div>                        
                        <div class="clear"></div>                        
                    </div>
                    <div class="grid_body">
                        <?php
                            foreach($testsuites as $row){
                                $issuer = get_post_meta($row->ID, 'ts_issuer', true);
                                $issueDate = get_post_meta($row->ID, 'ts_issue_date', true);
                                $issueStatus = get_post_meta($row->ID, 'ts_status', true);
                                $groupID = get_post_meta($row->ID, 'community_id', true);
                                $group = groups_get_group( array( 'group_id' => $groupID ) )
                        ?>
                        <div class="grid_row grid_row_border">
                            <div class="grid_cell width50P">
                                <h5><a href="<?php echo get_permalink($row->ID)?>" class="blue_txt"><?php echo apply_filters('the_title', $row->post_title)?></a></h5>
                                <?php echo apply_filters('the_excerpt', $row->post_excerpt) ?>
                            </div>
                            <div class="grid_cell width15P tocenter">
                                <a href="<?php echo bp_get_group_permalink($group) ?>"><?php echo $issuer?></a>
                            </div>           
                            <div class="grid_cell width20P tocenter">
                            <?php                            
                                echo date('M Y', strtotime($issueDate));
                            ?>
                            </div>
                            <div class="grid_cell width15P tocenter">
                                <?php                             
                                if($issueStatus == 'Active')
                                    echo '<span class="status_btn status_btn_active">ACTIVE</span>';
                                else if($issueStatus == 'On Hold')
                                    echo '<span class="status_btn status_btn_on_hold">ON HOLD</span>';
                                ?>
                            </div>                       
                            <div class="clear"></div>
                        </div>
                        <?php                                          
                            }
                        ?>
                    </div>
                    <div class="pagination-wrapper">
                        <div class="pagination">
                            <?php                                 
                                $args = array(
                                    'base'         => get_permalink() . '?%_%',
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
                            foreach ($tsTypes as $k => $v){
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
                        <h6 class="exp_title">Issuer</h6>
                        <div class="exp_content">
                        <?php
                            foreach ($tsIssuers as $k => $v){
                        ?>
                            <label for="<?php echo $k?>" class="blue_txt"><input type="checkbox" name="issuer[]" <?php echo in_array($k, $filterIssuer) ? 'checked="checked"' : '' ?> value="<?php echo $k?>" id="<?php echo $k?>" class="input_filter"> <?php echo $k?> (<?php echo $v?>)</label>
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
                            foreach ($tsIssueYears as $k => $v){
                        ?>
                            <label for="<?php echo $k?>" class="blue_txt"><input type="checkbox" name="issue_year[]" <?php echo in_array($k, $filterYear) ? 'checked="checked"' : '' ?> value="<?php echo $k?>" id="<?php echo $k?>" class="input_filter"> <?php echo $k?> (<?php echo $v?>)</label>
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
                            foreach ($tsStatuses as $k => $v){
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