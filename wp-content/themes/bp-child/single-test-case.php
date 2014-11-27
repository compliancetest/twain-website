<?php
/*
Template Name Posts: Test Case
*/

if(!isset($_REQUEST['is_ajax']))
{
    get_header();
}
$case = new TestCase(get_the_ID());
$case->load();
$test_suite_id = isset($_SESSION['test_suite_id']) ? $_SESSION['test_suite_id'] : $case->testSuite[0];
$community_id = get_post_meta($test_suite_id, "community_id", true);

?>
<?php if(!isset($_REQUEST['is_ajax'])){ ?>
	<div class="content container">
<?php }else{ ?>
<div id="suite-detail-popup" style="width: 900px" class="popup-box">
    <div class="popup-box-header radius6 noradiusbottom">Test Case Details</div>
    <div class="popup-box-content">
<?php } ?>
		<div class="infos">
				<h3 class="dark_gray_txt normal left">Test case: <span class="dark_blue_txt bold"><?php echo $case->title ; ?></span></h3>
                <?php
                    if(can_edit_test_case($case->id)){ 
                ?>
                <a href="/edit-test-case?id=<?php echo $case->id?>" class="action-btn edit-btn left10"><span class="p"></span><span class="t">Edit</span></a>
                <?php
                    }
                ?>
                <a href="<?php echo addPrintParams(get_permalink(), 'test-case')?>" class="action-btn print-btn print-page-btn" id="print-case-btn"><span class="p"></span><span class="t">PRINT</span></a>
                <span class="right nomarginright"> Back to <a href="<?php echo get_permalink($test_suite_id)?>"><?php echo get_the_title($test_suite_id) ?></a></span>
                <div class="clear"></div>
				<div class="dark_gray_txt redactor_editor"><?php echo $case->testIntentDescription ; ?></div>
				<div class="grids noradiusbottom">
					<div class="grid_row white_bcg noborderbottom">
						<div class="grid_cell width10P left size13 bold dark_blue_txt">Info:</div>
						<div class="grid_cell width20P left">
							<p>ID: <span><?php echo $case->testCaseID?>_V<?php echo $case->version; ?></span></p>
						</div>
						<div class="grid_cell width20P left">
                            <p>Published: <span>
                                <?php 
                                    echo formatDate($case->publishedDate);
                                ?>
                                </span></p>
                        </div>
                        <div class="grid_cell width20P left">
							<p>Status: 
                                <span class="status_btn status_<?php echo sanitize_title($case->status)?>"><?php echo $case->status?></span>								
							</p>
						</div>
                        
						<div class="grid_cell width30P left">
							<p>Initiating Messsage: <span><?php echo $case->initiationgMessage; ?></span></p>
						</div>
						<div class="clear"></div>
					</div>
					
					<div class="grey-border-bottom width98P"></div>
										
					<div class="grid_row white_bcg noborderbottom">
						<div class="grid_cell width10P left size13 bold dark_blue_txt">Roles:</div>
						<div class="grid_cell width30P left"> 
							<p>Tester Role: <span><?php echo $case->testerRole ?></span></p>
						</div>
						<div class="grid_cell width30P left">
							<p>Harness Role: <span><?php echo $case->harnessRole ?></span></p>
						</div>
						<div class="grid_cell width30P left">
							<p>Initiator: <span><?php echo ucfirst($case->Initiator) ?></span></p>
						</div>
						<div class="clear"></div>
					</div>
					
					<div class="grey-border-bottom width98P"></div>
						
					<div class="grid_row white_bcg noborderbottom">
						<div class="grid_cell width10P left size13 bold dark_blue_txt">Properties:</div>
						<div class="grid_cell width30P left">
							<p>Conformance Levels: <span>
                            <?php
                                $lArr = array();
                                if( isset( $case->conformanceLevel[$test_suite_id]) && is_iterable( $case->conformanceLevel[$test_suite_id] ) ) {
                                    foreach($case->conformanceLevel[$test_suite_id] as $level)
                                    {

                                        if(groups_is_user_admin(get_current_user_id(), $community_id) || $level != TEST_SUITE_DEFAULT_CONFORMANCE_LEVEL_CODE )
                                        {
                                            $lArr[] = $level;
                                        }
                                    }
                                }
                                sort($lArr);
                                echo implode(", ", $lArr);
                            ?>
                            </span></p>
						</div>						
                        <div class="grid_cell width20P left">
							<p>Outcome Type: <span><?php echo $case->outcomeType; ?></span></p>
						</div>
						<div class="grid_cell width15P left">
							<div class="test-pattern">
                                <?php $test_patterns_description = get_test_patterns_description($case->testPattern); ?>
                                <p>Test Pattern:</p>
                                <a href="<?php echo get_site_url() ?>/help-faq/test-patterns/" class="test-pattern-icon test-pattern-<?php echo $case->testPattern; ?> has-tooltip"><span class="simple_tooltip"><?php echo $test_patterns_description ;?><span></span></span></a>
                            </div>
						</div>
                        
						<div class="grid_cell width10P left">
							<p>Bulk: <span><?php echo $case->bulk; ?></span></p>
						</div>

                        <div class="grid_cell width15P left">
                            <p>Optional: <span><?php echo $case->optional; ?></span></p>
                        </div>

						<div class="clear"></div>
					</div>
                                        
                    <div class="grey-border-bottom width98P"></div>
                    
                    <div class="grid_row white_bcg noborderbottom">
                        <div class="grid_cell width10P left size13 bold dark_blue_txt">Scenario:</div>
                        <div class="grid_cell width90P left redactor_editor"> 
                            <?php
                                $family_mark = $wpdb->get_var( $wpdb->prepare( "SELECT family_mark FROM wp_test_suites WHERE suite_id = %d ", $test_suite_id ) );
                                $temp_test_suite = $wpdb->get_var( $wpdb->prepare( "SELECT suite_id FROM wp_test_suites WHERE family_mark = %d ORDER BY suite_id desc LIMIT 1", $family_mark ) );
                                $scenarioDetail = $case->getScenario($temp_test_suite);
                                echo '<p class="bottom8"><b>' . $scenarioDetail->code . '</b></p>';
                                echo $scenarioDetail->description;
                            ?>
                        </div>                        
                        <div class="clear"></div>
                    </div>
				</div>	
						
		</div> <!--end infos-->
		
		<div class="clear"></div>
		
		<div class="column">
			<div class="grid_cell width100P toleft"> 
				<div class="grid_head lighter_gray_bcg2 related">
					<div class="grid_row nopaddingbottom nopaddingtop ">
						<div class="grid_cell width100P size14 normal shadowwhite">Test Execution</div>
						<div class="clear"></div>
					</div>
				</div>

                <div class="grids noradiusbottom">
                    <?php if (!empty($case->testEndpointURL) || !empty($case->protocolBinding) ): ?>
                        <div class="grid_row white_bcg nopaddingbottom noborderbottom">
                            <div class="grid_cell width20P left size13 bold">Test trigger endpoint URL:</div>
                            <div class="grid_cell width75P left"><a href="<?php echo $case->testEndpointURL?>" class="blue_txt"><?php echo $case->testEndpointURL ; ?></a></div>
                            <div class="clear"></div>
                        </div>

                        <div class="grid_row white_bcg nopaddingbottom noborderbottom">
                            <div class="grid_cell width20P left size13 bold">Protocol Binding:</div>
                            <div class="grid_cell width75P left"><?php echo $case->protocolBinding ; ?></div>
                            <div class="clear"></div>
                        </div>
                    <?php endif; ?>
					<?php
					foreach($case->testExecutionData as $key => $row){
                    ?>
						<div class="grid_row white_bcg nopaddingbottom noborderbottom <?php if($key == (count($case->testExecutionData)-1)) {echo "paddingbottom10";} ?>">
							<div class="grid_cell width20P left size13 bold"><?php  echo $row['name'].':';?></div>
							<div class="grid_cell width75P left">
                                <?php if(strpos($row['value'], 'http://') !== false || strpos($row['value'], 'https://') !== false){ ?>
                                <a href="<?php echo $row['value']; ?>" class="blue_txt"><?php echo $row['value']; ?></a>
                                <?php }else{ ?>
                                <?php echo $row['value']?>
                                <?php } ?>
                            </div>
							<div class="clear"></div>
						</div>	
					<?php	
					} 
                    ?>
				</div>
						
			</div>
			
            <div class="clear"></div>
			<div class="space7"></div>
            <div class="grid_cell width100P toleft">
                <div class="grid_head lighter_gray_bcg2 related">
                    <div class="grid_row nopaddingbottom nopaddingtop">
                        <div class="grid_cell width100P size14 normal shadowwhite">Message Templates</div>
                        <div class="clear"></div>
                    </div>
                </div>
                <div class="grids noradiusbottom">
                    <?php
                    foreach($case->messageTemplates as $key => $row){
                        ?>
                        <div class="grid_row white_bcg nopaddingbottom noborderbottom <?php if($key == (count($case->messageTemplates)-1)) {echo "paddingbottom10";} ?>">
                            <div class="grid_cell width15P left size13 bold"><?php  echo $row['name'].':';?></div>
                            <div class="grid_cell width80P left">
                                <?php if(strpos($row['url'], 'http://') !== false || strpos($row['url'], 'https://') !== false){ ?>
                                    <a href="<?php echo $row['url']; ?>" class="blue_txt"><?php echo $row['url']; ?></a>
                                <?php }else{ ?>
                                    <?php echo $row['url']?>
                                <?php } ?>
                            </div>
                            <div class="clear"></div>
                        </div>
                    <?php
                    } ?>

                </div>
            </div>

            <div class="clear"></div>
            <div class="space7"></div>
			<div class="grid_cell width100P toleft">
                <div class="grid_head lighter_gray_bcg2 related">
                    <div class="grid_row nopaddingbottom nopaddingtop">
                        <div class="grid_cell width100P size14 normal shadowwhite">Test Data Profiles</div>
                        <div class="clear"></div>
                    </div>
                </div>
                <div class="grid_head">
                    <div class="grid_row padding5-10">
                        <div class="grid_cell width15P">Name</div>
                        <div class="grid_cell width15P left5P">Purpose</div>
                        <div class="grid_cell width10P left5P">Type</div>
                        <div class="grid_cell width40P left5P">URL</div>
                        <div class="clear"></div>
                    </div>
                </div>
                <div class="grids">
                    <?php                    
                    
                    $profileInstances = $case->getProfileInstanceRows();                    
                    foreach($profileInstances as $instance){
                        $instanceObj = S3Wrapper::getProfile( $instance->token );
                    ?>
                            <div class="grid_row white_bcg padding5-10">
                                <div class="grid_cell width15P">
                                    <a href="<?php echo get_site_url()?>?td-action=<?php echo wp_create_nonce('view-profile-instance')?>&id=<?php echo $instance->id?>" rel="custom-popup" cp-type="ajax">
                                        <?php echo $instance->profile_name?>
                                        <?php
                                        if($instanceObj->Profile->Version)
                                        {
                                            $version = array();
                                            foreach(get_object_vars($instanceObj->Profile->Version) as $k=>$v)
                                            {
                                                $version[] = $v;
                                            }
                                            echo " v" . implode(".", $version);
                                        }
                                        ?>
                                    </a>
                                </div>
                                <div class="grid_cell width15P left5P">
                                    <?php echo $instanceObj->Profile->Purpose?>
                                </div>
                                <div class="grid_cell width10P left5P">
                                    <a href="<?php echo get_site_url()?>?td-action=<?php echo wp_create_nonce('view-profile-type')?>&id=<?php echo $instance->type_id?>" rel="custom-popup" cp-type="ajax" class="view-profile-type-link">
                                        <?php echo $instance->profile_type_title; ?>
                                        <?php
                                        $pJSON = json_decode(base64_decode($instance->schema));
                                        if($pJSON->Version)
                                        {
                                            $version = array();
                                            foreach(get_object_vars($pJSON->Version) as $k=>$v)
                                            {
                                                $version[] = $v;
                                            }
                                            echo " v" . implode(".", $version);
                                        }
                                        ?>
                                    </a>
                                </div>
                                <div class="grid_cell width40P left5P">
                                <input type="text" readonly="readonly" value="<?php echo get_site_url()?>/get-profile?id=<?php echo $instance->token?>" class="input width100P" />
                                </div>
                                <div class="clear"></div>
                            </div>    
                    <?php    
                    } ?>
                </div>
                        
            </div>
            <div class="clear"></div>
            <div class="space7"></div>
            <?php if ($case->messageTemplates && $profileInstances): ?>
			<div class="grid_cell width100P toleft">
                <div class="grid_head lighter_gray_bcg2 related">
                    <div class="grid_row nopaddingbottom nopaddingtop">
                        <div class="grid_cell width100P size14 normal shadowwhite">Message Sample</div>
                        <div class="clear"></div>
                    </div>
                </div>
                <div class="grid_head">
                    <div class="grid_row padding5-10">
                        <div class="grid_cell width20P">Template</div>
                        <div class="grid_cell width20P left5P">Tester Profile</div>
                        <div class="grid_cell width20P left5P">Harness Profile</div>
                        <div class="grid_cell width20P left5P">Action</div>
                        <div class="clear"></div>
                    </div>
                </div>

                <div class="grids message-sample">
                        <div class="grid_row white_bcg padding5-10">
                            <div class="grid_cell width20P">
                                <select class="select" id="message-template">
                                    <?php foreach($case->messageTemplates as $key => $row): ?>
                                    <option value="<?php echo base64_encode($row['url']) ?>"><?php  echo $row['name'];?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <?php
                                $suiteObj = new TestSuite();
                                $testSuitesRolesProfilesTypes = $suiteObj->loadProfileTypesToRoles( $case->testSuite );
                                $testSuitesRoles = array( str_replace( ' ', '', $case->testerRole ) );
                            ?>
                            <div class="grid_cell width20P left5P">
                                <select class="select" id="tester-profile">
                                    <?php foreach($profileInstances as $instance): ?>
                                        <?php
                                            $pJSON = S3Wrapper::getProfile( $instance->token );
                                            $profileTypeName = $pJSON->Profile->Type;
                                            if( ! $instance->lookup ){
                                                continue;
                                            }
                                        ?>
                                        <?php if( ! empty( $testSuitesRolesProfilesTypes ) ){
                                            $isAllowed = false;
                                            foreach( $testSuitesRolesProfilesTypes AS $cRoleName => $cProfilesTypes ){
                                                $cProfilesTypes = str_replace( ' ', '', $cProfilesTypes );
                                                if( in_array( str_replace( ' ', '', $cRoleName ), $testSuitesRoles ) && in_array( str_replace( ' ', '', $profileTypeName ), $cProfilesTypes ) ){
                                                    $isAllowed = true;
                                                }
                                            }
                                            if( ! $isAllowed ) {
                                                continue;
                                            }
                                        }
                                        ?>
                                            <option value="<?php echo $instance->id ?>">
                                                <?php echo $instance->profile_name; ?>
                                                <?php
                                                if($pJSON->Profile->Version)
                                                {
                                                    $version = array();
                                                    foreach(get_object_vars($pJSON->Profile->Version) as $k=>$v)
                                                    {
                                                        $version[] = $v;
                                                    }
                                                    echo " v" . implode(".", $version);
                                                }
                                                ?>
                                            </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <?php
                                $testSuitesRoles = array( $case->harnessRole );
                            ?>
                            <div class="grid_cell width20P left5P">
                                <select class="select" id="harness-profile">
                                    <?php foreach($profileInstances as $instance): ?>
                                        <?php
                                        $pJSON = S3Wrapper::getProfile( $instance->token );
                                        $profileTypeName = $pJSON->Profile->Type;
                                        if( ! $instance->lookup ){
                                            continue;
                                        }
                                        ?>
                                        <?php if( ! empty( $testSuitesRolesProfilesTypes ) ){
                                            $isAllowed = false;
                                            foreach( $testSuitesRolesProfilesTypes AS $cRoleName => $cProfilesTypes ){
                                                if( in_array( $cRoleName, $testSuitesRoles ) && in_array( $profileTypeName, $cProfilesTypes ) ){
                                                    $isAllowed = true;
                                                }
                                            }
                                            if( ! $isAllowed ) {
                                                continue;
                                            }
                                        }
                                        ?>
                                        <option value="<?php echo $instance->id?>">
                                            <?php echo $instance->profile_name; ?>
                                            <?php
                                            if($pJSON->Profile->Version)
                                            {
                                                $version = array();
                                                foreach(get_object_vars($pJSON->Profile->Version) as $k=>$v)
                                                {
                                                    $version[] = $v;
                                                }
                                                echo " v" . implode(".", $version);
                                            }
                                            ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="grid_cell width20P left5P actions-column">
                                <a href="/view-message-template?mode=xml" target="_blank" class="view-message-template" data-mode="xml">XML</a> | <a href="/view-message-template?mode=html" target="_blank" class="view-message-template" data-mode="html">HTML</a>
                            </div>
                            <div class="clear"></div>
                        </div>
                </div>
            </div>
			<div class="clear"></div>
			<div class="space15"></div>
            <?php endif; ?>

			<!-- Steps -->
			
			<div class="grid_cell width100P toleft"> 
				<div class="grid_head blue_bcg2 related">
					<div class="grid_row nopaddingbottom nopaddingtop">
						<div class="grid_cell width100P size14 normal white_txt shadowblue">Test Steps</div>
						<div class="clear"></div>
					</div>
				</div>
				<div class="grid_head">
					<div class="grid_row padding5-10">
						<div class="grid_cell width10P tocenter">Steps</div>
						<div class="grid_cell width35P left5P">Action</div>
						<div class="grid_cell width35P left5P">Expected Result</div>
						<div class="clear"></div>
					</div>
				</div>
				<div class="grids redactor_editor">
					<?php					
					foreach($case->testSteps as $key => $row){
					?>
							<div class="grid_row white_bcg padding5-10">
								<div class="grid_cell width10P tocenter"><?php echo ($key+1); ?></div>
								<div class="grid_cell width35P left5P"><?php echo _convertLineSymbolToBR($row['action']); ?></div>
								<div class="grid_cell width35P left5P"><?php echo _convertLineSymbolToBR($row['result']); ?></div>
								<div class="clear"></div>
							</div>	
					<?php	
					} ?>
				</div>
						
			</div>
			
		</div><!--end column-->
		
		<div class="clear"></div>
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            jQuery('.test-pattern-icon .simple_tooltip').each(function(){
                jQuery(this).css("margin-left", '-' + jQuery(this).width()/2-5 + "px" );
            });
            
            $('a.view-message-template').click(function(){                
                var template = $('select#message-template').val();
                var tester = $('select#tester-profile').val();
                var harness = $('select#harness-profile').val();
                var mode = $(this).attr('data-mode');
                
                var link = '/view-message-template?id=<?php echo $case->id?>&mode=' + mode + '&tester-profile=' + tester + '&harness-profile=' + harness + '&template=' + template;
                $(this).attr('href', link);
                
            })
        });
    </script>

<?php 
if(!isset($_REQUEST['is_ajax']))
{
?>
		<div class="space15"></div>
	</div> <!--end content container-->
    <?php

get_footer();
}else{
    ?>
    </div><!--End Popup Content -->
    <a class="close_btn"></a>                
</div>
    <?php
}

