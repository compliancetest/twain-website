<?php
/**
* Test Suite Page
*/
global $groups_template;
$group = $groups_template->group;
//Getting Test Suites
$args = array(
        'post_type' => 'test-suite', 
        'posts_per_page' => -1,
        'tax_query' => array('relation' => 'and'),
        'meta_query' => array(
            array(
                'key' => 'community_id',
                'value' => $group->id,
                'compare' => '='
            )
        )
    );

//Getting Filter Params
$filterType = getFilterParam('type');
$filterIssuer = getFilterParam('issuer');
$filterYear = getFilterParam('year');
$filterStatus = getFilterParam('status');

foreach($filterType as $f)
{
    $args['tax_query'][] = array('taxonomy' => 'test_suite_type', 'field' => 'slug', 'terms' => $f);
}
foreach($filterIssuer as $v)
{
    $args['meta_query'][] = array('key' => 'ts_issuer', 'value' => $v, 'compare' => '=');
}
foreach($filterYear as $v)
{
    $args['meta_query'][] = array('key' => 'ts_issue_date', 'value' => $v . "-", 'compare' => 'LIKE');
}
foreach($filterStatus as $v)
{
    $args['meta_query'][] = array('key' => 'ts_status', 'value' => $v, 'compare' => '=');
}

$testsuites = get_posts( $args );
$roles = array();
?>
<div id="testsuites-container" class="tab-content white_bcg">
    <?php if(count($testsuites) > 0) {?> 
    <div class="column four_fifths left padding20-10">
        <div class="grid dark_gray_txt" id="test_suites_tab_grid">
            <div class="grid_head grid_head_border">
                <div class="padding10 nopaddingtop">
                    <div class="grid_cell nopaddingtop width50P">Name</div>
                    <div class="grid_cell nopaddingtop width20P tocenter">Date</div>
                    <div class="grid_cell nopaddingtop width15P tocenter">Status</div>
                    <div class="grid_cell nopaddingtop width15P tocenter">Products</div>
                    <div class="clear"></div>
                </div>
            </div>
            <div class="grid_body noborder">
            <?php
                //For Right Panels
                $tsTypes = array();
                $tsIssuers = array();
                $tsIssueYears = array();
                $tsStatuses = array();
                
                foreach($testsuites as $row){
            ?>
            <div class="grid_row grid_row_border">
                <div class="grid_cell width50P">
                    <h5><a href="<?php echo get_permalink($row->ID)?>" class="blue_txt"><?php echo apply_filters('the_title', $row->post_title)?></a></h5>
                    <?php echo apply_filters('the_excerpt', $row->post_excerpt) ?>
                </div>
                <div class="grid_cell width20P tocenter">
                <?php
                    $issueDate = get_post_meta($row->ID, 'ts_issue_date', true);
                    echo date('M Y', strtotime($issueDate));
                ?>
                </div>
                <div class="grid_cell width15P tocenter">
                    <?php 
                    $issueStatus = get_post_meta($row->ID, 'ts_status', true);
                    if($issueStatus == 'Active')
                        echo '<span class="status_btn status_btn_active">ACTIVE</span>';
                    else if($issueStatus == 'On Hold')
                        echo '<span class="status_btn status_btn_on_hold">ON HOLD</span>';
                    ?>
                </div>
                <div class="grid_cell width15P tocenter">
                    <a href="#" class="white_grey_btn view_products_btn"><b></b>VIEW</a>
                </div>              
                <div class="clear"></div>
            </div>
            <?php
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
            ?>
            </div>
        </div>
    </div>
    <div class="column fifth right expendables">
        <form name="form_filter" id="form_filter" action="<?php echo bp_get_group_permalink() ?>" method="get">
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
                <label for="<?php echo $k?>" class="blue_txt"><input type="checkbox" name="year[]" <?php echo in_array($k, $filterYear) ? 'checked="checked"' : '' ?> value="<?php echo $k?>" id="<?php echo $k?>" class="input_filter"> <?php echo $k?> (<?php echo $v?>)</label>
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
    <div class="clear"></div>
    <?php }else{ ?>
    <p class="column">No Data Found!</p>
    <?php } ?>
</div>
