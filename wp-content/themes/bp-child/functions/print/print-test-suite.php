<?php
/***
* Test Suite Print Page
*/

$suiteID = get_the_ID();

$suite = new TestSuite($suiteID);
$suite->load();

$current_group_id = get_post_meta($suiteID, 'community_id', true);

global $bp;

$group = groups_get_group( array( 'group_id' => $current_group_id ) );
?>
<!DOCTYPE HTML>
<html>
    <head profile="http://gmpg.org/xfn/11">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title><?php wp_title( '', true, 'right' ); ?></title>
    </head>
    <body onload="print_page()">
    <style type="text/css">
        body{
            font-family: 'Open Sans',Arial,Tahoma,Helevetica,sans-serif; font-size: 12px; line-height: 14px; color: #111;            
            padding: 20px 20px;
        }
        a{color: #2c80e8; text-decoration: none;}
        h2{font-size: 20px; margin:0 0 20px;}
        h3{font-size: 18px; margin:0 0 15px;}
        h4{font-size: 16px;  margin:0 0 10px;}
        h5{font-size: 14px;  margin:0 0 8px;}
        table{border: solid 1px #999; border-collapse: collapse; vertical-align: top;}
        th{text-align: left; font-weight: bold; border: solid 1px #999; padding: 5px;}
        td{border: solid 1px #999;  padding: 5px;}
        .clear{
            clear: both;
        }
        .block{
            padding: 10px 0;
            border-top: solid 1px #999;
        }
    </style>
     
    <?php if (has_post_thumbnail()) { ?>
    <div style="float: left">
        <?php echo the_post_thumbnail('post-thumb', array('class' => 'sbr')); ?>                    
    </div>
    <?php } ?>
    
    <h2 style="text-align: center; margin-bottom: 20px;"><a href="<?php echo get_permalink()?>"><?php the_title(); ?></a></h2>
    <a href="<?php echo bp_get_group_permalink($group); ?>" class="action-btn blue-edit-btn" style="float: right;"><span class="t">Community Home Page</span></a> 
    <div class="clear"></div>
    <div>
        <span style="">Version: <b><?php echo $suite->version; ?></b></span>
        <span style="margin-left: 10px;">Issue Date: <b><?php echo formatDate($suite->issueDate); ?></b></span>
        <span style="margin-left: 10px;">Issuer: <a href="<?php echo bp_get_group_permalink($group);; ?>"><b class="blue_txt"><?php echo $suite->issuer; ?></b></a></span>
        <span style="margin-left: 10px;">Status: <b class="green_txt"><?php echo $suite->status; ?></b> </span>
        <span style="margin-left: 10px;">Revision: <b><?php echo $suite->revisionDescription; ?></b> </span>
    </div>
    <p>
        <?php echo $suite->description ?>
    </p>
    <div class="block">
        <h5 style="float: left; width: 33%;">Test Suite Roles:</h5>
        <div style="float: left; width: 67%;">
            <?php
            foreach($suite->roles as $idx=>$row){

                ?>
                <p style="margin: 0 0 5px; border-bottom: dotted 1px #999; padding-bottom: 10px;">
                    <b style="float: left; width: 20%;"><?php echo $row['name']; ?></b>
                        <span style="float: left; width: 80%;">
                            <?php echo $row['desc']; ?>
                        </span>
                    <br class="clear" />
                </p>
            <?php

            }
            ?>
        </div>
        <div class="clear"></div>
    </div>
    <div class="block">
        <h5 style="float: left; width: 33%;">Conformance Levels:</h5>
        <div style="float: left; width: 67%;">
            <?php
            foreach($suite->conformanceLevel as $i => $row){
                ?>
                <p style="margin: 0 0 5px; border-bottom: dotted 1px #999; padding-bottom: 10px;">
                    <b style="float: left; width: 20%;"><?php echo $row['code']; ?></b>
                    <span style="float: left; width: 80%;">
                        <?php echo $row['desc']; ?>
                    </span>
                    <br class="clear" />
                </p>
            <?php
            }
            ?>
        </div>
        <div class="clear"></div>
    </div>
    <div class="block">
        <h5 style="float: left; width: 33%;">Specification Documents &amp; Materials:</h5>
        <div style="float: left; width: 67%;">
            <?php
            foreach($suite->specDocuments as $row){
                $doc_name = $row->doc_name;
                $doc_desc = $row->doc_desc;
                $doc_loc = $row->doc_loc_url;
                $doc_file_name = $row->doc_file_name;
                $doc_file_url = $row->doc_loc_url;
                ?>
                <p style="margin: 0 0 5px">
                    <a href="<?php echo $row->doc_loc_url?>" target="_blank" class="underline blue_txt file"><?php echo $row->doc_name?></a>
                    <br /><?php echo $row->doc_desc?>
                </p>
            <?php
            }
            ?>
        </div>
        <div class="clear"></div>
    </div>
    <div class="block">
        <h5 style="float: left; width: 33%;">Scenarios:</h5>
        <div style="float: left; width: 67%;">
            <?php
            foreach($suite->scenarios as $row){
                $scenario_name = $row['code'];
                $scenario_desc = strip_tags($row['description']);
                ?>
                <p style="margin: 0 0 5px; border-bottom: dotted 1px #999; padding-bottom: 10px;">
                    <b style="float: left; width: 20%;"><?php echo $scenario_name; ?></b>
                    <span style="float: left; width: 80%;">
                        <?php echo $scenario_desc;?>
                    </span>
                    <br class="clear" />
                </p>
            <?php
            }
            ?>
        </div>
        <div class="clear"></div>
    </div>

    <div class="block">
        <h5 style="float: left; width: 33%;">Related Compliance Suites:</h5>
        <div style="float: left; width: 67%;">
            <?php                         
                foreach($suite->relatedSuites as $row){
            ?>
            <p style="margin: 0 0 5px;">
                <a href="<?php echo get_permalink($row['id'])?>"><?php echo get_the_title($row['id'])?></a><br />
                <?php echo $row['desc']?>
            </p>    
            <?php
                } 
            ?>
        </div>
        <div class="clear"></div>
    </div>

    <h4 style="border-top: solid 1px #999; padding: 20px 0 10px;">Test Cases</h4>
    <table width="100%">
        <thead>
            <tr>
                <th>Test Scenario</th>
                <th>Test Case ID</th>
                <th>Published</th>
                <th>Tester Role</th>
                <th>Harnes Role(s)</th>
                <th>Initiator</th>
                <th>Conf Leve</th>
                <th>Outcome Type</th>
                <th>Test Pattern</th>
                <th>Bulk</th>
                <th>Initating Message</th>
                <th style="min-width: 150px">Test Intent Description</th>                
            </tr>
        </thead>
        <tbody>
            <?php
                //Getting Test Cases
                $args = $args = array(
                        'post_type' => 'test-case',
                        'posts_per_page' => -1,
                        'order_by'  => 'meta_value title',
                        'order'     => 'ASC',
                        'meta_key'  => 'sequence_number',
                        'tax_query' => array('relation' => 'and')
                );
                $params = array();
                //Add Test Suite ID
                $args['meta_query'][] = array('key' => 'test_suite', 'value' => $suiteID, 'compare' => '=');


                $get_query = new WP_Query($args);

                //Add Order by Scenaro
                $get_query->set('suppress_filters', false);
                add_filter('posts_join_paged', 'add_scenario_join_query', 100, 2);
                add_filter('posts_orderby', 'add_scenario_orderby_query', 100, 2);
                add_filter('posts_fields_request', 'add_scenario_fields_query', 100, 2);
                $testCases = $get_query->get_posts();

                //Remove Filters
                remove_filter('posts_join_paged', 'add_scenario_join_query');
                remove_filter('posts_orderby', 'add_scenario_orderby_query');
                remove_filter('posts_fields_request', 'add_scenario_fields_query');

                $get_query = new WP_Query($args);

                $testCases = $get_query->get_posts();
                $i = 0;
                foreach($testCases as $row)
                {
                    ?>
                    <tr>
                        <td><?php echo $testCases[$i]->scenarioCode?></td>
                        <td><a href="<?php echo get_permalink($row->ID) ?>"><?php echo get_the_title($row->ID) ?></a></td>
                        <td><?php echo formatDate(get_post_meta($row->ID ,'published', true))?></td>
                        <td><?php echo get_post_meta($row->ID ,'choose_tester_role', true)?></td>
                        <td><?php echo get_post_meta($row->ID ,'choose_harness_role', true)?></td>
                        <td><?php echo get_post_meta($row->ID ,'choose_initiator', true)?></td>
                        <td>
                            <?php
                            $levels = get_post_meta($row->ID ,'conformance_level_' . $suite->id);
                            $lArr = array();

                            foreach($levels as $level)
                            {
                                if(!groups_is_user_admin(get_current_user_id(), $suite->community_id) && $level == TEST_SUITE_DEFAULT_CONFORMANCE_LEVEL_CODE)
                                    continue;
                                $lArr[] = $level;
                            }
                            sort($lArr);
                            echo implode(", ", $lArr);
                            ?>
                        </td>
                        <td><?php echo get_post_meta($row->ID ,'outcome_type', true)?></td>
                        <td><?php echo get_post_meta($row->ID ,'message_count', true)?></td>
                        <td><?php echo get_post_meta($row->ID ,'bulk', true)?></td>
                        <td><?php echo get_post_meta($row->ID ,'choose_init_messages', true)?></td>
                        <td style="min-width: 150px">
                        <?php
                            $intentDesc = get_post_meta($row->ID ,'test_intent_description', true);
                            if(strlen($intentDesc) > 150)
                                echo substr($intentDesc, 0, 150) . "...";
                            else
                                echo $intentDesc;
                        ?>
                        </td>
                    </tr>
            <?php
                    $i++;
                }
            ?>
            <?php
                if(!$testCases){
                    ?>
                    <tr><td style="text-align: center" colspan="11">No Data Found.</td></tr>
                    <?php
                }
            ?>
        </tbody>
    </table>
    <br />
    <br />
    <br />
    </body>
    <script type="text/javascript">
        function print_page()
        {
            window.print();
        }
    </script>
</html>
