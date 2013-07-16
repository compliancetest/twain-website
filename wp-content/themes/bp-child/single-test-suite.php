<?php
/*
Template Name Posts: Test Suite
*/
  
  get_header();

	$suiteID = get_the_ID();
	
    $suite = new TestSuite($suiteID);
    $suite->load();
    
	$current_group_id = get_post_meta($suiteID, 'community_id', true);
	
	global $bp;
    
	$group = groups_get_group( array( 'group_id' => $current_group_id ) );
    
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
                        <?php if(can_edit_suite($suite->id)){ ?>
						<a href="/edit-test-suite?id=<?php echo get_the_ID()?>" class="action-btn edit-btn left10"><span class="p"></span><span class="t">EDIT</span></a>
                        <?php } ?>
						<div class="clear"></div>
					</div>
					<div class="width15P right">
						<a href="<?php echo bp_get_group_permalink($group); ?>" class="action-btn blue-edit-btn" style="float: right;"><span class="t">Issuer Home Page</span></a>
					</div>
					<div class="clear"></div>
					<div class="grids noradiusbottom">
						<div class="grid_row white_bcg noborderbottom">
							<div class="grid_cell width100P left">
								Version: <span><?php echo $suite->version; ?></span>
								Issue Date: <span><?php echo formatDate($suite->issueDate); ?></span>
								Issuer: <a href="<?php echo bp_get_group_permalink($group);; ?>"><span class="blue_txt"><?php echo $suite->issuer; ?></span></a>
								Status: <span class="green_txt"><?php echo $suite->status; ?></span> 
								Revision: <span><?php echo $suite->revisionDescription; ?></span> 								
							</div>
							<div class="clear"></div>
						</div>
					</div>
					<div class="space15"></div>					
					<div class="grids noborder nobackground">
					<p class="nomarginbottom"><?php echo $suite->description ?> </p>
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
							foreach($suite->relatedSuites as $row){
						?>
                        <div>
                            <a href="<?php echo get_permalink($row['id'])?>"><?php echo get_the_title($row['id'])?></a><br />
                            <?php echo $row['desc']?>
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
						    foreach($suite->specDocuments as $row){
							    $doc_name = $row->doc_name;
							    $doc_desc = $row->doc_desc;
							    $doc_loc = $row->doc_loc_url;
							    $doc_file_name = $row->doc_file_name;
							    $doc_file_url = $row->doc_loc_url;
                            ?>
                            <div class="grid_cell width100P">
                                <a href="<?php echo $row->doc_loc_url?>" target="_blank" class="underline blue_txt file"><?php echo $row->doc_name?></a>
                                <div class="paddingleft20"><?php echo $row->doc_desc?></div>                                
                            </div>
                            <div class="clear"></div>
                            <?php
						    }
						?>
					</div>
				</div> <!--end tab 2-->
				
				<div class="tab-content white_bcg" id="tabs_sv3" style="display: none; ">
                    <div class="column padding15-20">
                        
                        <?php    
                        foreach($suite->conformanceLevel as $i => $row){
                        ?>
                            <div class="grid_cell width10P bold blue_txt size26px top5 <?php if ($i == ((count($suite->conformanceLevel)) -1 )) { echo 'top0bottom5';} ?>"><?php echo $row['code']; ?></div>
                            <div class="grid_cell width90P">
                                <?php echo $row['desc']; ?>
                            </div>
                            <div class="clear"></div> 
                            <div class="grey-border-bottom <?php if ($i == ((count($suite->conformanceLevel)) -1 )) { echo 'displaynone';} ?>"></div>                                                                    
                        <?php                                        
                        }
                        ?>
                    </div>
                    <div class="clear"></div>
                </div><!--end tab 3-->
                
                <div class="tab-content white_bcg" id="tabs_sv4" style="display: none; ">
					<div class="column padding15-20">
						
						<?php
						    foreach($suite->roles as $idx=>$row){
							
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
            <?php if(is_customer($suite->id)){ ?>
            <?php
                $subscription = getUserPurchase($suite->id);
            ?>
            <div class="message success">
                You have already purchased a subscription to this test suite.
                If you want to unsubscribe it, please click <a onclick="return confirm('Are you sure that you want to unsubscribe it')" href="/?_paymentnonce=<?php echo wp_create_nonce('unsubscribe')?>&id=<?php echo $subscription->id ?>&return=<?php echo base64_encode(get_permalink())?>"><i>here</i></a>.
            </div>
            <?php }else{ ?>            
			<a href="<?php echo is_user_logged_in() ? '#subscribe-box' : '#registration-popup'?>" rel="custom-popup" cp-type="inline" class="suite-subscript-link" cp-closeWhenClickOveraly=0>
                <span class="price-b">
                    <span class="l"></span>
                    <span class="m"><b>$<?php echo $suite->monthlySubscriptionPrice?></b><br />per month</span>
                    <span class="r"></span>
                </span>
                <span class="text-b"><b>ACCESS</b><br />Test Harness</span>
            </a>
            <?php } ?>
            <div class="clear"></div>
            <div class="space20"></div>
		</div>
			<div class="clear"></div>
		
		<!-- Test Cases -->
		<?php
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
                    <div class="grid_cell right">
                        <span class="left padding5-10">Filter By: </span>
                        <div class="styled_select left right13">
                            <label>
                            <select name="tester_role" class="change_ts">
                              <option value="">- Tester Role -</option>
                              <?php 
                              foreach($suite->roles as $r){                                  
                                  echo '<option ' . ($r['name'] == $selectedRole ? 'selected="selected"' : '') . ' value="'.$r['name'].'" >'.$r['name'].'</option>';
                              }
                              ?>
                            </select>
                            </label>
                        </div>
                        <div class="styled_select left" style="margin-right: 30px">
                            <label>
                            <select name="conformance" class="change_ts">
                              <option value="">Conformance Level</option>
                              <?php 
                              foreach($suite->conformanceLevel as $r){
                                  echo '<option ' . ($r['code'] == $selectedConfLevel ? 'selected="selected"' : '') . ' value="'.$r['code'].'" >'.$r['code'].'</option>';
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
							
				<div class="grids" id="testcases-list">
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
                    $args['meta_query'][] = array('key' => 'test_suite', 'value' => $suiteID, 'compare' => '=');
                    
                    if($selectedRole){
                        $args['meta_query'][] = array('key' => 'choose_tester_role', 'value' => $selectedRole, 'compare' => '=');
                        $params[] = 'tester_role=' . $selectedRole;
                    }
                    
                    if($selectedConfLevel){
                        $args['meta_query'][] = array('key' => 'conformance_level', 'value' => $selectedConfLevel, 'compare' => '=');
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
                            <div class="grid_cell nopaddingtop width15P toleft">
                            <?php 
                                $intentDesc = get_post_meta($row->ID ,'test_intent_description', true);
                                if(strlen($intentDesc) > 150)
                                    echo substr($intentDesc, 0, 150) . "...";
                                else
                                    echo $intentDesc;
                            ?>
                            </div>
                            <div class="grid_cell nopaddingtop width5P toleft tocenter grid_action_cell">
                                <?php if(can_edit_test_case($row->ID)){ ?>
                                <a href="/edit-test-case?id=<?php echo $row->ID?>" class="action-btn icon-btn blue-edit-btn"><span class="p"></span></a>
                                <?php } ?>
                                <?php if(can_delete_test_case($row->ID)){ ?>
                                <a href="?id=<?php echo $row->ID?>&_wpnonce=<?php echo wp_create_nonce('delete-case')?>&return=<?php echo base64_encode(get_permalink()) ?>" class="action-btn icon-btn blue-edit-btn blue-delete-btn"><span class="p"></span></a>
                                <?php } ?>
                                <div class="clear"></div>                                                                        
                            </div>
                            <div class="clear"></div>
                        </div>
                <?php                        
                    }
				?>				
                <?php
                    if(!$testCases){
                        ?>
                        <div class="tocenter padding10">No Data Found.</div>
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
<?php
    $userCards = getUserCreditCards();    
?>
<div class="popup-box" id="subscribe-box" style="display: none;">
    <form name="paymentForm" id="paymentForm" action="">
        <div class="popup-box-header radius6 noradiusbottom">Purchase Subscription</div>        
            <div class="popup-box-content grid-box-body">    
                <div class="field-row">
                    <h5>Confirm Existing Payment Method or Add New Card Details</h5>
                    <span class="focus-tooltip"><span></span>You are about to purchase a monthly Subscription to: <a href="<?php echo get_permalink()?>"><?php echo $suite->name?></a> for $<?php echo $suite->monthlySubscriptionPrice?> per month (you can cancel anytime)</span>
                </div>
                <div class="field-row">
                    <div class="grid-cell">
                        <label>Existing Card</label>
                        <select name="card_id" id="card_id" class="select">
                            <option value="">Select a Card</option>
                            <?php foreach($userCards as $row){ ?>
                            <option value="<?php echo $row->id?>">
                                <?php echo ucfirst(check_cc($row->card_number)); ?>
                                <?php echo chunk_split(encrypt_card_number($row->card_number), 4)?>,
                                <?php echo $row->name?>
                            </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="clear"></div>
                </div>
                <div class="add-new-border"><span>or add new</span></div>
                <div class="field-row">
                    <div class="grid-cell">
                        <label>Name on Card</label>
                        <input type="text" name="name_on_card" id="name_on_card" value="" class="input" />
                        <img src="<?php echo CHILD_TEMPLATE_DIRECTORY?>/images/valid-icon.png" class="valid-icon" />
                    </div>                
                    <div class="clear"></div>
                </div>
                <div class="field-row">
                    <div class="grid-cell">
                        <label>Card Number</label>
                        <input type="text" name="card_number" id="card_number" value="" class="input" />
                    </div>                
                    <div class="clear"></div>
                </div>
                <div class="field-row">
                    <div class="grid-cell">
                        <label>Expiry Date</label>
                        <select name="exp_month" id="exp_month" class="select">
                            <option value="">Month</option>
                            <option value="1">Jan</option>
                            <option value="2">Feb</option>
                            <option value="3">Mar</option>
                            <option value="4">Apr</option>
                            <option value="5">May</option>
                            <option value="6">Jun</option>
                            <option value="7">Jul</option>
                            <option value="8">Aug</option>
                            <option value="9">Sep</option>
                            <option value="10">Oct</option>
                            <option value="11">Nov</option>
                            <option value="12">Dec</option>
                        </select>
                        <select name="exp_year" id="exp_year" class="select">
                            <option value="">Year</option>                        
                            <?php for($i=0; $i < 20; $i++){ ?>
                            <option value="<?php echo $i + date("y")?>"><?php echo $i + date("Y")?></option>
                            <?php } ?>
                        </select>                    
                    </div>                
                    <div class="clear"></div>
                </div>            
                <div class="field-row">
                    <div class="grid-cell">
                        <label class="left">CVC</label>
                        <input type="text" name="card_cvc" id="card_cvc" placeholder="****" value="" class="input" />
                    </div>                
                    <div class="clear"></div>
                </div>
                <div class="field-row notice-txt">
                    This is Photoshop's version  of Lorem Ipsum. Proin gravida bhavel velit auctor aliquet. Aenean sollicitudin, lorem quis nefertimauctor, nisi elit consequat ipsum.
                    <br />
                    <img src="<?php echo CHILD_TEMPLATE_DIRECTORY ?>/images/card-icon.png" />
                </div>                
            </div>
            <div class="popup-box-footer radius6 noradiustop">
                <a href="#" class="action-btn process-btn submit-btn"><span class="p"></span><span class="t">Submit</span></a>
                <a href="#" class="action-btn cancel-btn close-popup-btn"><span class="p"></span><span class="t">Close</span></a>            
                <div class="clear"></div>
            </div>
        <a class="close_btn"></a>                        
        <div class="loading loading-with-text"><div><b>PROCESSING YOUR PAYMENT</b><span>Please wait...</span></div></div>
        <input type="hidden" name="suite_id" value="<?php echo $suite->id?>" />
        <input type="hidden" name="_paymentnonce" value="<?php echo wp_create_nonce('direct_payment')?>" />
    </form>
</div>
<div class="popup-box" id="payment-success-box" style="display: none;">
    <div class="popup-box-header radius6 noradiusbottom">Success!</div>        
        <div class="popup-box-content grid-box-body">    
            <p>Thank you for purchasing a subscription to <?php echo $suite->name?>. <br />You payment has been successfully processed.  Please refer to your dashboard page for test harness access credentials and further configuration.</p>
        </div>
        <div class="popup-box-footer radius6 noradiustop">                        
            <a href="/my-profile" class="action-btn continue-btn"><span class="p"></span><span class="t">Goto My Dashbaord</span></a>
            <a href="#" class="action-btn cancel-btn close-popup-btn"><span class="p"></span><span class="t">Close</span></a>
            
            <div class="clear"></div>
        </div>
    <a class="close_btn"></a>
</div>
<div class="popup-box" id="has-subscribe-box" style="display: none; width: 300px;">
    <div class="popup-box-header radius6 noradiusbottom">You are a Customer.</div>        
        <div class="popup-box-content grid-box-body">    
            <p>You already purchased a subscription for this test suite.</p>
        </div>
        <div class="popup-box-footer radius6 noradiustop">                        
            <a href="#" class="action-btn cancel-btn close-popup-btn"><span class="p"></span><span class="t">Close</span></a>            
            <div class="clear"></div>
        </div>
    <a class="close_btn"></a>
</div>

<script type="text/javascript">
jQuery(document).ready(function($) {
	jQuery('.change_ts').change(function(){
		jQuery('#filter_ts').submit();
	});	
    
    jQuery('#subscribe-box form input[type=text]').keypress(function(){
        jQuery('#subscribe-box #card_id').val('');
    })
    
    jQuery('#paymentForm').submit(function(){
        jQuery('#subscribe-box .loading').show();
        jQuery('#subscribe-box .message').remove();
        jQuery.ajax({
            url: '/',
            type: 'post',
            data: jQuery(this).serialize(),
            success: function(rsp){
                jQuery('#subscribe-box .loading').hide();
                if(rsp != 'success')    
                {
                    jQuery('#subscribe-box .popup-box-footer').prepend('<div class="message error">' + rsp + '</div>');
                }else{                    
                    jQuery('#payment-success-box').showPopupBox({
                        onClose: function(){
                            document.location.reload();
                        }
                    });
                }
            }, 
            error: function(err){
                jQuery('#subscribe-box .loading').hide();
                jQuery('#subscribe-box .popup-box-footer').prepend('<div class="message error">' + err.responseText + '</div>');
            }
            
        })
        return false;
    })
});
</script>
<?php
get_footer();
?>
