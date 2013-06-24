<?php
/*
Template Name Posts: Test Suite
*/
  
  get_header();

	$suiteID = get_the_ID();
	
	$current_group_id = get_post_meta($suiteID, 'community_id', true);
	
	global $bp;
	$group = groups_get_group( array( 'group_id' => $current_group_id ) );
	$group_url = home_url( $bp->groups->slug . '/' . $group -> slug );
    
    //Check Permission
    
?>
	

	<div class="content container">
		<div class="infos">
                <?php if (has_post_thumbnail()) { ?>
				<div class="grid_cell width10P">					
						<?php echo the_post_thumbnail('post-thumb', array('class' => 'sbr')); ?>					
				</div> 
                <?php } ?>
				<div class="grid_cell <?php echo has_post_thumbnail() ? 'width90P' : 'width100P'?>">
					<div class="dark_gray_txt bold width80P left">
						<h2 class="left"><?php the_title(); ?></h2>
                        <?php if(is_admin() || is_super_admin()){ ?>
						<a href="/wp-admin/post.php?post=<?php echo get_the_ID()?>&action=edit" class="action-btn edit-btn left10"><span class="p"></span><span class="t">EDIT</span></a>
                        <?php } ?>
						<div class="clear"></div>
					</div>
					<div class="width15P right">
						<a href="<?php echo $group_url; ?>" class="action-btn blue-edit-btn" style="float: right;"><span class="t">Issuer Home Page</span></a>
					</div>
					<div class="clear"></div>
					<div class="grids noradiusbottom">
						<div class="grid_row white_bcg noborderbottom">
							<div class="grid_cell width100P left">
								Version: <span><?php echo get_post_meta(get_the_ID(), 'ts_version', true); ?></span>
								Issue Date: <span><?php echo get_post_meta(get_the_ID(), 'ts_issue_date', true); ?></span>
								Issuer: <a href="<?php echo $group_url; ?>"><span class="blue_txt"><?php echo get_post_meta(get_the_ID(), 'ts_issuer', true); ?></span></a>
								Status: <span class="green_txt"><?php echo get_post_meta(get_the_ID(), 'ts_status', true); ?></span> 
								Revision: <span><?php echo get_post_meta(get_the_ID(), 'ts_revision_description', true); ?></span> 								
							</div>
							<div class="clear"></div>
						</div>
					</div>
					<div class="space15"></div>
					
					<div class="grids noborder nobackground">
					<p class="nomarginbottom"><?php echo get_post_meta(get_the_ID(), 'ts_description', true); ?> </p>
					</div>
				</div>
				
				<div class="clear"></div>
						
		</div> <!--end infos-->
		<div class="clear"></div>
		
		
		<div class="column nopaddingbottom">
		    <!-- tabs -->
			<div class="tabs-contr">
				<ul class="tab-nav">
					<li class="active">
						<a href="javascript: void(0)" rel="tabs_sv1">Related Compliance Suites</a>
					</li>
					<li class="">
						<a href="javascript: void(0)" rel="tabs_sv2">Specification Documents &amp; Materials</a>
					</li>
					<li class="">
                        <a href="javascript: void(0)" rel="tabs_sv3">Comformance Levels</a>
                    </li>
                    
                    <li class="">
						<a href="javascript: void(0)" rel="tabs_sv4">Test Suite Roles</a>
					</li>
                    
				</ul>
				
				<div class="clear"></div>
				
				<div class="tab-content white_bcg" id="tabs_sv1" style="display: block; ">
					<div class="column">										
						<div class="grid_cell width10P bold top3">Related To: </div>
						<div class="grid_cell width90P">
						<?php 
						
							$relatedSuites = get_post_meta(get_the_ID(), 'ts', true) ; 
							$relatedSuitesDesc = get_post_meta(get_the_ID(), 'ts_desc', true);
                            
							foreach($relatedSuites as $i => $sid){
						?>
                        <div>
                            <a href="<?php echo get_permalink($sid)?>"><?php echo get_the_title($sid)?></a><br />
                            <?php echo $relatedSuitesDesc[$i]?>
                            <div class="space7"></div>
                        </div>
                        <?php
							} 
                        ?>
						</div>
						<div class="clear"></div>
					</div>
					<div class="clear"></div>
				</div> <!--end tab 1-->
				
				<div class="tab-content white_bcg" id="tabs_sv2" style="display: none; ">
					<div class="column">
						<?php 
						$the_post_id= get_the_ID();
						$myrows = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "ts_options_documents WHERE ts_id={$the_post_id}");
						foreach($myrows as $row){
							$doc_name = $row->doc_name;
							$doc_desc = $row->doc_desc;
							$doc_loc = $row->doc_loc_url;
							$doc_file_name = $row->doc_file_name;
							$doc_file_url = $row->doc_loc_url;
							echo '<div class="grid_cell width100P">';
							echo '<a href="'.$doc_loc.'" target="_blank" class="underline blue_txt file">';
							echo $doc_name;
							echo '</a><div class="paddingleft20">'.$doc_desc.'</div>';
							echo '</div><div class="clear"></div>';
						}
							//$doc_type_array = get_post_meta(get_the_ID(), 'doc_type', true); 
							$doc_name_array = get_post_meta(get_the_ID(), 'doc_name', true); 
							$doc_loc_array = get_post_meta(get_the_ID(), 'doc_loc', true); 
							$doc_desc_array = get_post_meta(get_the_ID(), 'doc_desc', true); 
						?>
					</div>
				</div> <!--end tab 2-->
				
				<div class="tab-content white_bcg" id="tabs_sv3" style="display: none; ">
                    <div class="column padding15-20">
                        
                        <?php
                        $lvl_code_array = get_post_meta(get_the_ID(), 'lvl_code', true); 
                        $lvl_desc_array = get_post_meta(get_the_ID(), 'lvl_desc', true); 
                        
                        foreach($lvl_code_array as $key => $lvl_code){
                            foreach ($lvl_desc_array as $key2 => $lvl_desc){
                                        if( $key == $key2 ){ ?>
                                        <div class="grid_cell width10P bold blue_txt size26px top5 <?php if ($key == ((count($lvl_code_array)) -1 )) { echo 'top0bottom5';} ?>"><?php echo $lvl_code; ?></div>
                                        <div class="grid_cell width90P">
                                            <?php echo $lvl_desc; ?>
                                        </div>
                                        <div class="clear"></div> 
                                        <div class="grey-border-bottom <?php if ($key == ((count($lvl_code_array)) -1 )) { echo 'displaynone';} ?>"></div>                                                                    
                                <?php    }
                                    }
                                }
                        ?>
                    </div>
                    <div class="clear"></div>
                </div><!--end tab 3-->
                
                <div class="tab-content white_bcg" id="tabs_sv4" style="display: none; ">
					<div class="column padding15-20">
						
						<?php
						$roles = getTestSuiteRoles(get_the_ID());
						
						foreach($roles as $idx=>$row){
							
					        ?>		
										<div class="grid_cell width25P bold blue_txt size26px top5 <?php if ($idx == ((count($roles)) -1 )) { echo 'top0bottom5';} ?>"><?php echo $row['name']; ?></div>
										<div class="grid_cell width70P">
											<?php echo $row['desc']; ?>
										</div>
										<div class="clear"></div> 
										<div class="grey-border-bottom <?php if ($idx == ((count($roles)) -1 )) { echo 'displaynone';} ?>"></div>																	
						    <?php
							
							}
						?>
					</div>
					<div class="clear"></div>
				</div><!--end tab 3-->
				
                
			</div>
			<!--end tabs-->
            <div class="space15"></div>
            <?php $price = get_post_meta(get_the_ID(), 'monthly_subscription_price', true) ?>
			<a href="javascript: void(0)" class="suite-subscript-link">
                <span class="price-b">
                    <span class="l"></span>
                    <span class="m"><b>$<?php echo $price?></b><br />per month</span>
                    <span class="r"></span>
                </span>
                <span class="text-b"><b>ACCESS</b><br />Test Harness</span>
            </a>
            <div class="clear"></div>
            <div class="space20"></div>
		</div>
			<div class="clear"></div>
		
		<!-- Test Cases -->
		<?php
            $testerRoles = getTestSuiteRoles($suiteID);
            $confLevels = get_post_meta($suiteID, 'lvl_code', true);
            $selectedRole = isset($_GET['tester_role']) ? $_GET['tester_role'] : '';
            $selectedConfLevel = isset($_GET['conformance']) ? $_GET['conformance'] : '';
        ?>
		<div class="clear"></div>
		<div class="grid_row test_cases">
			<div id="append_filter">
                <div class="grid_cell width35P">
                    <h5 class="blue_txt">Test Cases</h5>
                </div>
                <form id="filter_ts" method="get" action="<?php echo get_the_guid()?>">                                        
                    <div class="grid_cell width55P right selecteds">
                        <span class="left padding5-10">Filter By: </span>
                        <div class="styled_select left width25P right13">
                            <label>
                            <select name="tester_role" class="change_ts">
                              <option value="">- Tester Role -</option>
                              <?php 
                              foreach($testerRoles as $r){                                  
                                  echo '<option ' . ($r['name'] == $selectedRole ? 'selected="selected"' : '') . ' value="'.$r['name'].'" >'.$r['name'].'</option>';
                              }
                              ?>
                            </select>
                            </label>
                        </div>
                        <div class="styled_select left width30P right13">
                            <label>
                            <select name="conformance" class="change_ts">
                              <option value="">Conformance Level</option>
                              <?php 
                              foreach($confLevels as $r){
                                  echo '<option ' . ($r == $selectedConfLevel ? 'selected="selected"' : '') . ' value="'.$r.'" >'.$r.'</option>';
                              }
                              ?>
                            </select>
                            </label>
                        </div>
                        <?php if(is_admin() || is_super_admin()){ ?>
                        <a href="/wp-admin/post-new.php?post_type=test-case" class="action-btn add-new-btn"><span class="p"></span><span class="t">New Test Case</span></a>
                        <?php } ?>
                        <div class="clear"></div>
                    </div>
                </form>
                <div class="clear"></div>
                <div id="double_border"></div>
			</div>
				<div class="grid_head blue_grid special_grid_big">
					<div class="grid_row nopaddingbottom nopaddingtop tocenter testcases_grid special_grid_inner">
						<div class="grid_cell nopaddingtop width10P toleft single_line">Test Case ID</div>
						<!--<div class="grid_cell nopaddingtop width5P toleft tocenter single_line">Version</div>-->
						<div class="grid_cell nopaddingtop width10P toleft tocenter single_line">Published</div>
						<div class="grid_cell nopaddingtop width10P toleft tocenter">Tester<br/>Role</div>
						<div class="grid_cell nopaddingtop width10P toleft tocenter">Harness<br/>Role(s)</div>
						<div class="grid_cell nopaddingtop width5P toleft tocenter single_line">Initiator</div>
						<div class="grid_cell nopaddingtop width5P toleft tocenter">Conf<br/>Level</div>
						<div class="grid_cell nopaddingtop width10P toleft tocenter">Outcome<br/>Type</div>
						<div class="grid_cell nopaddingtop width5P toleft tocenter">Test<br/>Pattern</div>
						<div class="grid_cell nopaddingtop width5P toleft tocenter single_line">Bulk</div>
						<div class="grid_cell nopaddingtop width10P toleft tocenter">Initiating<br/>Message</div>
						<div class="grid_cell nopaddingtop width15P toleft single_line">Test Intent Description</div>
						<div class="grid_cell nopaddingtop width5P toleft single_line">Actions</div>
						<div class="clear"></div>	
					</div>
				</div>
				
				
				<div class="clear"></div>
							
				<div class="grids">
				<?php 
                    $posts_per_page = 10;
                    $page = get_query_var('page') ? get_query_var('page') : 1;
				    //Getting Test Cases
                    $args = $args = array(
                            'post_type' => 'test-case',         
                            'posts_per_page' => $posts_per_page,
                            'paged' => $page,
                            'tax_query' => array('relation' => 'and')
                    );
                    $params = array();
                    //Add Test Suite ID
                    $args['meta_query'][] = array('key' => 'test_suites', 'value' => "|" . $suiteID . "|", 'compare' => 'LIKE');
                    
                    if($selectedRole){
                        $args['meta_query'][] = array('key' => 'choose_tester_role', 'value' => $selectedRole, 'compare' => '=');
                        $params[] = 'tester_role=' . $selectedRole;
                    }
                    
                    if($selectedConfLevel){
                        $args['meta_query'][] = array('key' => 'conformance_level', 'value' => $selectedRole, 'compare' => '=');
                        $params[] = 'conformance=' . $selectedConfLevel;
                    }
                    
                    $get_query = new WP_Query($args);
                    $testCases = $get_query->get_posts();
                    
                    foreach($testCases as $row)
                    {
                        ?>
                        <div class="grid_row white_bcg tocenter testcase_line ">
                            <div class="grid_cell nopaddingtop width10P toleft ">
                                <a href="<?php echo get_permalink($row->ID) ?>"><?php echo get_the_title($row->ID) ?></a>
                                <br /><span class="version"><?php echo get_post_meta($row->ID ,'version', true)?></span>
                            </div>
                            <div class="grid_cell nopaddingtop width10P toleft tocenter ">
                                <?php echo get_post_meta($row->ID ,'published', true)?>
                            </div>
                            <div class="grid_cell nopaddingtop width10P toleft tocenter">
                                <?php echo get_post_meta($row->ID ,'choose_tester_role', true)?>
                            </div>
                            <div class="grid_cell nopaddingtop width10P toleft tocenter">
                                <?php echo get_post_meta($row->ID ,'choose_harness_role', true)?>
                            </div>
                            <div class="grid_cell nopaddingtop width5P toleft tocenter ">
                                <?php echo get_post_meta($row->ID ,'choose_initiator', true)?>
                            </div>
                            <div class="grid_cell nopaddingtop width5P toleft tocenter">
                                <?php echo get_post_meta($row->ID ,'conformance_level', true)?>
                            </div>
                            <div class="grid_cell nopaddingtop width10P toleft tocenter">
                                <?php echo get_post_meta($row->ID ,'outcome_type', true)?>
                            </div>
                            <div class="grid_cell nopaddingtop width5P toleft tocenter">
                                <?php echo get_post_meta($row->ID ,'message_count', true)?>
                            </div>
                            <div class="grid_cell nopaddingtop width5P toleft tocenter ">
                                <?php echo get_post_meta($row->ID ,'bulk', true)?>
                            </div>
                            <div class="grid_cell nopaddingtop width10P toleft tocenter">
                                <?php echo get_post_meta($row->ID ,'choose_init_messages', true)?>
                            </div>
                            <div class="grid_cell nopaddingtop width15P toleft ">
                                <?php echo get_post_meta($row->ID ,'test_intent_description', true)?>
                            </div>
                            <div class="grid_cell nopaddingtop width5P toleft tocenter ">
                                <?php if(is_admin() || is_super_admin()){ ?>
                                <a href="/wp-admin/post.php?post=<?php echo $row->ID?>&action=edit" class="action-btn icon-btn blue-edit-btn"><span class="p"></span></a>
                                <a href="/wp-admin/post.php?post=<?php echo $row->ID?>&action=trash&_wpnonce=<?php echo wp_create_nonce('trash-post_' . $row->ID)?>" class="action-btn icon-btn blue-edit-btn blue-delete-btn"><span class="p"></span></a>
                                <?php } ?>
                                <div class="clear"></div>                                                                        
                            </div>
                            <div class="clear"></div>
                        </div>
                <?php                        
                    }
				?>				
			</div>            
            <div class="space10"></div>
			<div class="pagination-wrapper">
                <div class="pagination">
                    <?php                                 
                        $args = array(
                            'base'         => get_permalink() . '%_%',
                            'format'       => '&page=%#%',
                            'total'        => $get_query->max_num_pages,
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
            <div class="space15"></div>
		</div>
		
			
	</div> <!--end content container-->

<script type="text/javascript">
jQuery(document).ready(function($) {
	jQuery('.change_ts').change(function(){

		jQuery('#filter_ts').submit();
	});	
});
</script>
<?php
get_footer();
?>
