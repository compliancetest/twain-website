<?php
/**
* Template Name:Add/Edit Test Case
*/


$caseID = isset($_GET['id']) ? htmlspecialchars($_GET['id']) : null;

if( ($caseID != null && !can_edit_test_case($caseID)) || ($caseID == null && !can_create_test_case()) )
{
    wp_redirect("/");
    exit;
}

$isNew = true;

$case = new TestCase($caseID);
$case->load();

if(!$case->id)
    $isNew = true;
else
    $isNew = false;

if($isNew)
    $testsuites = $case->getAvailableTestSuites(isset($_GET['suite_id']) ? htmlspecialchars($_GET['suite_id']) : null );
else
    $testsuites = $case->getAvailableTestSuites($case->testSuite[0]);

if(isset($_GET['suite_id']))
{
    foreach($testsuites as $r)
    {
        if($r->ID == htmlspecialchars($_GET['suite_id']))
        {
            $case->testSuite = array($r->ID);
            break;
        }
    }
}

if(!$case->testSuite && $isNew && count($testsuites) > 0)
    $case->testSuite = array($testsuites[0]->ID);

$allSuiteRoles = $case->getAvailableRoles();
$suiteRoles = array();

foreach($allSuiteRoles as $r)
{
    if(!in_array($r['name'], $suiteRoles))        
        $suiteRoles[] = $r['name'];
}
    
asort($suiteRoles);

$suiteInitMessages = $case->getAvailableInitMessages();

if(!$isNew){
    $newMajorVersionExist = isNewVersionExist($case->familyMark, $case->version_major);
    $newMinorVersionExist = isNewVersionExist($case->familyMark, $case->version_major, $case->version_minor);
    $newPatchVersionExist = isNewVersionExist($case->familyMark, $case->version_major, $case->version_minor, $case->version_patch);
}else{
    $newMajorVersionExist = false;
    $newMinorVersionExist = false;
    $newPatchVersionExist = false;
}
get_header();


?>
<div class="content edit-item-wrapper" id="edit_test_case_wrapper">
    <div class="column container relative"> 
      <form name="caseForm" id="caseForm" action="" method="post" enctype="multipart/form-data">
        <?php if($isNew){ ?>
        <h2>Add New Test Case</h2>
        <?php }else{ ?>
        <h2>Edit Test Case: <?php $case->name ?></h2>
        <?php } ?>        
        <div class="grid-box grid-box-expandable grid-box-opened" id="case-info-box">
           <div class="grid-box-header">
               <a class="gbh-btn gbh-btn-expandable left" href="javascript: void(0);">Ex</a>
               <h5 class="left">Test Case Information</h5>
               <div class="clear"></div>
           </div>
           <div class="grid-box-body">
               <div class="column">
                   <div class="field-row">
                       <div class="grid-cell">
                           <label for="test_case_id">Name:</label>
                           <input type="text" name="test_case_id" id="test_case_id" value="<?php echo $case->testCaseID?>" class="input medium-input" />
                       </div>           
                        
                       <div class="grid-cell">
                           <label for="published">Published:</label>
                           <input type="text" name="published" id="published" value="<?php echo formatDate($case->publishedDate) ?>" class="input datepicker" />
                       </div>                   
                       <!--<div class="grid-cell">
                           <label for="sequence_number">Execution Sequence Number:</label>
                           <input type="text" name="sequence_number" id="sequence_number" value="<?php echo $case->sequenceNumber?>" class="input" />                           
                       </div> -->
                       <div class="clear"></div>
                   </div>               
                   <div class="field-row">
                       <div class="grid-cell status-cell">
                           <label for="ts_identifier">Status: </label>
                           <input type="radio" name="test_case_status" id="ts_status_draft" value="Draft" <?php echo $case->status == 'Draft' ? 'checked="checked"' : ''?> /> 
                           <span class="label"><span class="status_btn status_circle status_draft">D</span> Draft</span>
                           <input type="radio" name="test_case_status" id="ts_status_build" value="Build" <?php echo $case->status == 'Build' ? 'checked="checked"' : ''?> /> 
                           <span class="label"><span class="status_btn status_circle status_build">B</span> Build</span>
                           <input type="radio" name="test_case_status" id="ts_status_active"  value="Active" <?php echo $case->status == 'Active' ? 'checked="checked"' : ''?> /> 
                           <span class="label"><span class="status_btn status_circle status_active">A</span> Active</span><br />
                           <input type="radio" name="test_case_status" id="ts_status_deprecated" value="Deprecated" <?php echo $case->status == 'Deprecated' ? 'checked="checked"' : ''?> />
                           <span class="label"><span class="status_btn status_circle status_deprecated">C</span> Deprecated</span>
                           <input type="radio" name="test_case_status" id="ts_status_obsolete" value="Obsolete" <?php echo $case->status == 'Obsolete' ? 'checked="checked"' : ''?> /> 
                           <span class="label"><span class="status_btn status_circle status_obsolete">O</span> Obsolete</span>                           
                       </div>      
                       <div class="grid-cell version-cell">
                           <label for="version">Version: </label>
                           <span>
                               <b>Major:</b> 
                               <input type="text" id="version_major" name="version_major" class="input input-readonly"  value="<?php echo $case->version_major?>" data-default="<?php echo $case->version_major?>" />                               
                               <a href="#" class="action-btn icon-btn blue-plus-btn <?php if($newMajorVersionExist){?>disabled-btn has-tooltip<?php } ?>"><span class="p"></span>
                                   <?php if($newMajorVersionExist){?><span class="simple_tooltip"><span></span>Later version already exists.</span><?php } ?>
                               </a>
                           </span>
                           <span>
                               <b>Minor:</b>
                               <input type="text" id="version_minor" name="version_minor" class="input input-readonly"  value="<?php echo $case->version_minor?>" data-default="<?php echo $case->version_minor?>" />
                               <a href="#" class="action-btn icon-btn blue-plus-btn <?php if($newMinorVersionExist){?>disabled-btn has-tooltip<?php } ?>"><span class="p"></span>
                                   <?php if($newMinorVersionExist){?><span class="simple_tooltip"><span></span>Later version already exists.</span><?php } ?>
                               </a>
                           </span>
                           <span>
                               <b>Patch:</b> <input type="text" id="version_patch" name="version_patch" class="input input-readonly"  value="<?php echo $case->version_patch?>" data-default="<?php echo $case->version_patch?>" />
                               <a href="#" class="action-btn icon-btn blue-plus-btn <?php if($newPatchVersionExist){?>disabled-btn has-tooltip<?php } ?>"><span class="p"></span>
                                   <?php if($newPatchVersionExist){?><span class="simple_tooltip"><span></span>Later version already exists.</span><?php } ?>
                               </a>
                           </span>
                           <div class="clear"></div>                           
                       </div>                                                                     
                       <div class="clear"></div>
                   </div>
                   <div class="field-row">                                                     
                       <div class="grid-cell width100P">
                           <label>Description:</label>
                           <textarea name="test_intent_description" id="test_intent_description" class="textarea large-textarea"><?php echo $case->testIntentDescription?></textarea>
                       </div>                                          
                       <div class="clear"></div>
                   </div>               
                   
               </div>
           </div>
        </div>   
        <div class="space25"></div>
        <div class="grid-box grid-box-expandable grid-box-opened" id="suites-box">
           <div class="grid-box-header">
               <a class="gbh-btn gbh-btn-expandable left" href="javascript: void(0);">Ex</a>
               <h5 class="left">Choose Test Suite</h5>
               <div class="clear"></div>
           </div>
           <div class="grid-box-body">
               <div class="column">
                   <div class="field-row">
                       <div class="grid-cell" id="suites-cell">
                           <?php foreach($testsuites as $row){ ?>
                           <label class="bottom8"><input type="checkbox" name="suite_id[]" id="suite_id<?php echo $row->ID?>" value="<?php echo $row->ID?>" <?php echo in_array($row->ID, $case->testSuite) ? 'checked="checked"' : ''?>> <?php echo apply_filters('the_title', $row->post_title)?></label>
                           <?php } ?>
                       </div>
                       <div class="clear"></div>
                   </div>
               </div>
           </div>
        </div>
        <div class="space25"></div>
        
        <div class="grid-box grid-box-expandable grid-box-opened" id="choose-conf-level-box">
           <div class="grid-box-header">
               <a class="gbh-btn gbh-btn-expandable left" href="javascript: void(0);">Ex</a>
               <h5 class="left">Choose Conformance Levels</h5>
               <div class="clear"></div>
           </div>
           <div class="grid-box-body">               
               <div class="column">               
                   <?php                    
                   $isFirst = true;
                   foreach($testsuites as $crow){ 
                       if(!in_array($crow->ID, $case->testSuite))
                        continue;
                   ?>
                      <div class="conf-level-suite-box">
                       <p <?php if(!$isFirst){?>style="border-top: solid 2px #e3e3e3; padding: 10px 0 5px; margin-top: 15px;"<?php } ?>><b><?php echo get_the_title($crow->ID)?></b></p>
                       <?php
                           $suiteObj = new TestSuite($crow->ID);
                           $levels = $suiteObj->loadConformanceLevel();
                       ?>
                       <div class="field-row">
                           <div class="grid-cell radio-cell">
                               <label><input type="checkbox" name="conformance_level<?php echo $crow->ID?>[]" value="<?php echo TEST_SUITE_DEFAULT_CONFORMANCE_LEVEL_CODE?>" class="default-level" checked="checked" /> <?php echo TEST_SUITE_DEFAULT_CONFORMANCE_LEVEL_CODE?></label>
                           </div>
                           <div class="grid-cell width70P">
                               <?php echo TEST_SUITE_DEFAULT_CONFORMANCE_LEVEL_DESCRIPTION?>
                           </div>
                           <div class="clear"></div>
                       </div>
                       <?php foreach($levels as $row){ ?>
                       <?php
                           if($row['code'] == TEST_SUITE_DEFAULT_CONFORMANCE_LEVEL_CODE)
                               continue;
                       ?>
                       <div class="field-row">
                           <div class="grid-cell radio-cell">
                               <label><input type="checkbox" name="conformance_level<?php echo $crow->ID?>[]" value="<?php echo $row['code']?>" <?php echo isset($case->conformanceLevel[$crow->ID]) && in_array($row['code'], $case->conformanceLevel[$crow->ID]) ? 'checked="checked"' : ''?> /> <?php echo $row['code']?></label>
                           </div>
                           <div class="grid-cell width60P">
                               <?php echo $row['desc']?>
                           </div>
                           <div class="clear"></div>
                       </div>
                       <?php } ?>                   
                      </div> 
                   <?php 
                       $isFirst = false;
                   } 
                   ?>                   
               </div>
           </div>
        </div>   
        <div class="space25"></div>
        
        <div class="grid-box grid-box-expandable grid-box-opened" id="choose-scenarios-box">
           <div class="grid-box-header">
               <a class="gbh-btn gbh-btn-expandable left" href="javascript: void(0);">Ex</a>
               <h5 class="left">Choose Scenarios</h5>
               <div class="clear"></div>
           </div>
           <div class="grid-box-body">               
               <div class="column">               
                   <?php                    
                   $isFirst = true; 
                   foreach($testsuites as $crow){ 
                       if(!in_array($crow->ID, $case->testSuite))
                        continue;
                   ?>
                      <div class="scenario-box">
                       <p <?php if(!$isFirst){?>style="border-top: solid 2px #e3e3e3; padding: 10px 0 5px; margin-top: 15px;"<?php } ?>><b><?php echo get_the_title($crow->ID)?></b></p>
                       <?php
                           $suiteObj = new TestSuite($crow->ID);
                           $scenarios = $suiteObj->loadScenarios();
                           
                           foreach($scenarios as $row){ 
                       ?>
                       <div class="field-row">
                           <div class="grid-cell radio-cell">
                               <label><input type="radio" name="scenario_<?php echo $crow->ID?>" value="<?php echo $row['id']?>" <?php echo $case->scenario[$crow->ID] == $row['id'] ? 'checked="checked"' : ''?> /> <?php echo $row['code']?></label>
                           </div>
                           <div class="grid-cell width60P redactor_editor">
                               <?php echo $row['description']?>
                           </div>
                           <div class="clear"></div>
                       </div>
                       <?php } ?>                   
                      </div> 
                   <?php 
                       $isFirst = false;
                   } 
                   ?>                   
               </div>
           </div>
        </div>   
        <div class="space25"></div>
        <div class="grid-box grid-box-expandable grid-box-opened" id="choose-roles-box">
           <div class="grid-box-header">
               <a class="gbh-btn gbh-btn-expandable left" href="javascript: void(0);">Ex</a>
               <h5 class="left">Choose Roles</h5>
               <div class="clear"></div>
           </div>
           <div class="grid-box-body">
               <div class="column">
                   <div class="field-row">
                       <div class="grid-cell">
                           <label>Tester Role:</label>                           
                           <select name="choose_tester_role" class="select">
                               <option>- Select -</option>
                               
                               <?php foreach($suiteRoles as $row) {?>
                               <option value="<?php echo $row?>" <?php echo $case->testerRole == $row ? 'selected="selected"' : ''?>><?php echo $row?></option>
                               <?php } ?>
                           </select>
                       </div>
                       <div class="grid-cell">
                           <label>Harness Role:</label>
                           <select name="choose_harness_role" class="select">
                               <option>- Select -</option>
                               <?php foreach($suiteRoles as $row) {?>
                               <option value="<?php echo $row?>" <?php echo $case->harnessRole == $row ? 'selected="selected"' : ''?>><?php echo $row?></option>
                               <?php } ?>
                           </select>
                       </div>
                       <div class="grid-cell radio-cell">
                           <label>Initiator:</label>
                           <input type="radio" name="choose_initiator" value="tester" <?php echo $case->Initiator == 'tester' ? 'checked="checked"' : ''?> /> Tester
                           <input type="radio" name="choose_initiator" value="harness" <?php echo $case->Initiator == 'harness' ? 'checked="checked"' : ''?> /> Harness
                       </div>                       
                       <div class="clear"></div>
                   </div>               
               </div>
           </div>
        </div>   
        <div class="space25"></div>
        <div class="grid-box grid-box-expandable grid-box-opened" id="choose-init-msg-box">
           <div class="grid-box-header">
               <a class="gbh-btn gbh-btn-expandable left" href="javascript: void(0);">Ex</a>
               <h5 class="left">Choose Initiating Message</h5>
               <div class="clear"></div>
           </div>
           <div class="grid-box-body">
               <div class="column">
                   <div class="field-row">
                       <div class="grid-cell width100P">
                           <select name="choose_init_message" class="select">
                               <option>- Select -</option>
                               <?php                                
                               foreach($suiteInitMessages as $row) {?>
                               <option value="<?php echo $row?>" <?php echo $case->initiationgMessage == $row ? 'selected="selected"' : ''?>><?php echo $row?></option>
                               <?php } ?>
                           </select>
                       </div>                       
                       <div class="clear"></div>
                   </div>               
               </div>
           </div>
        </div>   
        <div class="space25"></div>        
        <div class="grid-box grid-box-expandable grid-box-opened" id="test-data-box">
           <div class="grid-box-header">
               <a class="gbh-btn gbh-btn-expandable left" href="javascript: void(0);">Ex</a>
               <h5 class="left">Test Data</h5>
               <div class="clear"></div>
           </div>
           <div class="grid-box-body">
               <div class="column">
                   <div id="case-template-data">
                       <h6><B>Message Templates</b></h6>
                       <div class="field-row">
                           <div class="grid-cell width35P">
                               <label>Template Title</label>
                           </div>
                           <div class="grid-cell width45P">
                               <label>Template URI</label>
                           </div>
                           <div class="clear"></div>
                       </div>
                       <?php
                           $suiteTemplates = $case->getAvailableMessageTemplates();
                           
                           foreach($suiteTemplates as $idx=>$row){                               
                               $checked = false;
                               foreach($case->messageTemplates as $crow)
                               {
                                   if($crow['name'] == $row['name'] && $crow['url'] == $row['url'])
                                   {
                                       $checked = true;
                                       break;
                                   }
                               }
                       ?>
                       <div class="field-row">                           
                           <div class="grid-cell width35P">                               
                               <input type="checkbox" name="message_template_id[]" value="<?php echo $idx?>" <?php echo $checked ? 'checked="checked"' : '' ?> />
                               <input type="text" name="message_template_name<?php echo $idx?>" value="<?php echo $row['name']?>" class="input" readonly="readonly" />
                           </div>
                           <div class="grid-cell width55P">
                               <input type="text" name="message_template_url<?php echo $idx?>" value="<?php echo $row['url']?>" class="input medium-input" readonly="readonly" />
                           </div>
                           <div class="clear"></div>
                       </div>
                       <?php } ?>
                   </div>
                   
                   <h6><B>Profile Data</b></h6>      
                   <div class="field-row">
                       <div class="grid-cell width15P">
                           <label>Profile Name</label>
                       </div>
                       <div class="grid-cell width10P">
                           <label>Purpose</label>
                       </div>
                       <div class="grid-cell width15P">
                           <label>Type</label>
                       </div>
                       <div class="grid-cell width45P">
                           <label>URL</label>
                       </div>
                       <div class="grid-cell width5P">
                           
                       </div>
                       
                       <div class="clear"></div>
                   </div>
                   <div id="profile-instances">
                   <?php
                       $profileInstances = $case->getAvailableProfileInstances();
                       $testSuitesRolesProfilesTypes = $suiteObj->loadProfileTypesToRoles( $case->testSuite );
                       $testSuitesRoles = array( $case->testerRole, $case->harnessRole );
                       foreach($profileInstances as $instance){
                           $profileName = $instance->profile_name;
                           
                           $profileTypeName = $generalProfileType = $instance->profile_type_title;
                           $pJSON = json_decode(base64_decode($instance->schema));
                           if($pJSON->Version)
                           {
                               $version = array();
                               foreach(get_object_vars($pJSON->Version) as $k=>$v)
                               {
                                   $version[] = $v;
                               }
                               $profileTypeName .= " v" . implode(".", $version);
                           }
                           if( ! $instance->lookup && ! cp_checked($instance->id, $case->profileInstances) ){
                               continue;
                           }
                           if( ! empty( $testSuitesRolesProfilesTypes ) ){
                               $isAllowed = false;
                               foreach( $testSuitesRolesProfilesTypes AS $cRoleName => $cProfilesTypes ){
                                   if( ( in_array( $cRoleName, $testSuitesRoles ) && in_array( $generalProfileType, $cProfilesTypes ) ) || cp_checked($instance->id, $case->profileInstances) ){
                                       $isAllowed = true;
                                   }
                               }
                               if( ! $isAllowed ) {
                                   continue;
                               }
                           }
                           $instanceObj = json_decode(base64_decode($instance->content));
                   ?>
                       <div class="field-row">
                           <div class="grid-cell width15P">
                               <input type="checkbox" name="profile_instances[]" value="<?php echo $instance->id?>" <?php echo cp_checked($instance->id, $case->profileInstances) ?> />
                               <a href="<?php echo get_site_url()?>?td-action=<?php echo wp_create_nonce('view-profile-instance')?>&id=<?php echo $instance->id?>" rel="custom-popup" cp-type="ajax">
                                    <?php 
                                        echo $profileName; 
                                        if($instanceObj->Profile->Version)
                                        {
                                            if(is_object($instanceObj->Profile->Version))
                                            {
                                                $version = array();
                                                foreach(get_object_vars($instanceObj->Profile->Version) as $k=>$v)      
                                                {
                                                    $version[] = $v;
                                                }
                                                echo " v" . implode(".", $version);
                                            }else{
                                                echo " v " . $instanceObj->Profile->Version;
                                            }
                                        }
                                    ?>
                               </a>
                           </div>
                           <div class="grid-cell width10P">
                               <label><?php echo $instanceObj->Profile->Purpose?></label>
                           </div>
                           <div class="grid-cell width15P">
                               <a href="<?php echo get_site_url()?>?td-action=<?php echo wp_create_nonce('view-profile-type')?>&id=<?php echo $instance->type_id?>" rel="custom-popup" cp-type="ajax" class="view-profile-type-link">
                                   <?php echo $profileTypeName; ?>                                   
                               </a>
                           </div>
                           <div class="grid-cell width45P">
                               <input type="text" readonly="readonly" value="<?php echo get_site_url()?>/get-profile?id=<?php echo $instance->token?>" class="input width100P" />
                           </div>                                                             
                           <div class="clear"></div>
                       </div>              
                   <?php
                       }
                   ?>
                   </div>
                        <div class="field-row noborder"></div>
               </div>
           </div>
        </div>   
        <div class="space25"></div>
        <div class="grid-box grid-box-expandable grid-box-opened" id="test-execution-box">
           <div class="grid-box-header">
               <a class="gbh-btn gbh-btn-expandable left" href="javascript: void(0);">Ex</a>
               <h5 class="left">Test Execution</h5>
               <div class="clear"></div>
           </div>
           <div class="grid-box-body">
               <div class="column">
                   <div class="field-row">
                       <div class="grid-cell">
                           <label>Protocol Binding:</label>
                           <input type="text" name="protocol_binding2" value="<?php echo $case->protocolBinding?>" class="input" />
                       </div>             
                       <div class="grid-cell">
                           <label>Test trigger URL:</label>
                           <input type="text" name="test_url" value="<?php echo $case->testEndpointURL?>" class="input medium-input" />
                       </div>                     
                       <div class="clear"></div>
                   </div>
                   <?php foreach($case->testExecutionData as $row){ ?>
                   <div class="field-row">
                       <div class="grid-cell">
                           <label>Property Name:</label>
                           <input type="text" name="property_name_exec[]" value="<?php echo $row['name']?>" class="input" />
                       </div>                       
                       <div class="grid-cell">
                           <label>Property Value:</label>
                           <input type="text" name="property_value_exec[]" value="<?php echo $row['value']?>" class="input medium-input" />
                       </div>                       
                       <div class="grid-cell">
                           <label>&nbsp;</label>
                           <a href="#" class="action-btn blue-delete-btn icon-btn"><span class="p"></span></a>
                       </div>                   
                       <div class="clear"></div>
                   </div>           
                   <?php } ?>
                   <?php if($isNew){ ?>
                   <div class="field-row">
                       <div class="grid-cell">
                           <label>Property Name:</label>
                           <input type="text" name="property_name_exec[]" value="" class="input" />
                       </div>                       
                       <div class="grid-cell">
                           <label>Property Value:</label>
                           <input type="text" name="property_value_exec[]" value="" class="input medium-input" />
                       </div>                       
                       <div class="grid-cell">
                           <label>&nbsp;</label>
                           <a href="#" class="action-btn blue-delete-btn icon-btn"><span class="p"></span></a>
                       </div>                   
                       <div class="clear"></div>
                   </div>
                   <?php } ?>
                   <div class="btn-row">
                       <div class="grid-cell">
                           <a href="#" class="action-btn add-new-btn" id="add-test-exec-data"><span class="p"></span><span class="t">New Property</span></a>                       
                       </div>
                       <div class="clear"></div>
                   </div>
               </div>
           </div>
        </div>
        <div class="space25"></div>
        <div class="grid-box grid-box-expandable grid-box-opened" id="property-box">
           <div class="grid-box-header">
               <a class="gbh-btn gbh-btn-expandable left" href="javascript: void(0);">Ex</a>
               <h5 class="left">Test Case Property</h5>
               <div class="clear"></div>
           </div>
           <div class="grid-box-body">
               <div class="column">
                   <div class="field-row">                                   
                       <div class="grid-cell radio-cell">
                           <label>Outcome Type:</label>
                           <input type="radio" name="outcome_type" value="Positive" <?php echo $case->outcomeType == 'Positive' ? 'checked="checked"' : ''?> /> Positive
                           <input type="radio" name="outcome_type" value="Negative" <?php echo $case->outcomeType == 'Negative' ? 'checked="checked"' : ''?> /> Negative
                       </div>             
                       <div class="grid-cell radio-cell">
                           <label>Bulk:</label>
                           <input type="radio" name="bulk" value="Yes"  <?php echo $case->bulk == 'Yes' ? 'checked="checked"' : ''?> /> Yes
                           <input type="radio" name="bulk" value="No"  <?php echo $case->bulk == 'No' ? 'checked="checked"' : ''?> /> No
                       </div>             
                       
                       <div class="grid-cell">
                           <label>Test Pattern:</label>
                           <select name="message_count" id="message_count" class="select">
                               <?php $test_patterns = get_test_patterns_number(); ?>
                               <?php foreach ( $test_patterns as $test_pattern ) : setup_postdata( $test_pattern ); ?>
                                   <?php $test_pattern_number = cp_get_post_meta($test_pattern->ID, 'test_pattern_number', true); ?>
                                   <option value="<?php echo $test_pattern_number; ?>" <?php if($test_pattern_number== $case->testPattern){?>selected="selected" <?php } ?>><?php echo get_the_title($test_pattern->ID); ?></option>
                               <?php endforeach; ?>
                           </select>
                       </div>
                       <div class="clear"></div>
                   </div>                                    
               </div>
           </div>
        </div>     
        <div class="space25"></div>
        <div class="grid-box grid-box-expandable grid-box-opened" id="test-step-box">
           <div class="grid-box-header">
               <a class="gbh-btn gbh-btn-expandable left" href="javascript: void(0);">Ex</a>
               <h5 class="left">Test Step</h5>
               <div class="clear"></div>
           </div>
           <div class="grid-box-body">
               <div class="column">
                    <?php foreach($case->testSteps as $row){ ?>
                    <div class="field-row">
                       <div class="grid-cell width40P">
                           <label>Action:</label>
                           <textarea name="step_action[]" class="textarea width98P"><?php echo $row['action']?></textarea>
                       </div>                       
                       <div class="grid-cell width50P">
                           <label>Expected Result:</label>
                           <textarea name="step_expected[]" class="textarea width98P"><?php echo $row['result']?></textarea>
                       </div>                       
                       <div class="grid-cell">
                           <label>&nbsp;</label>
                           <a href="#" class="action-btn blue-delete-btn icon-btn"><span class="p"></span></a>
                       </div>                   
                       <div class="clear"></div>
                   </div>               
                   <?php } ?>
                   <?php if($isNew){ ?>
                   <div class="field-row">
                       <div class="grid-cell width40P">
                           <label>Action:</label>
                           <textarea name="step_action[]" class="textarea width98P"></textarea>
                       </div>                       
                       <div class="grid-cell width50P">
                           <label>Expected Result:</label>
                           <textarea name="step_expected[]" class="textarea width98P"></textarea>
                       </div>                       
                       <div class="grid-cell">
                           <label>&nbsp;</label>
                           <a href="#" class="action-btn blue-delete-btn icon-btn"><span class="p"></span></a>
                       </div>                   
                       <div class="clear"></div>
                   </div>    
                   <?php } ?>
                   <div class="btn-row">
                       <div class="grid-cell">
                           <a href="#" class="action-btn add-new-btn" id="add-test-step"><span class="p"></span><span class="t">New Test Step</span></a>                       
                       </div>
                       <div class="clear"></div>
                   </div>      
               </div>
           </div>
        </div>     
        <div class="grid-box">
           <div class="grid-box-footer nobackground noshadow">
               <div class="btn-row nopaddingright nopaddingleft">
                   <?php if(!$isNew) { ?>
                   <div class="left"><label><input type="checkbox" name="send-notification" id="send-notification" value="1" autocomplete="off" /> Send Notification to subscribers</label></div>
                   <?php } ?>
                   <a href="#" class="action-btn process-btn submit-btn left15"><span class="p"></span><span class="t">SAVE TEST CASE</span></a>
                   <a href="<?php echo get_permalink($_SESSION['test_suite_id'])?>" class="action-btn cancel-btn"><span class="p"></span><span class="t">Cancel</span></a>
                   <div class="clear"></div>                   
               </div>
           </div>
       </div>
       <div class="loading loading-with-text" id="saving-wrapper"><div><b>SAVING YOUR DATA</b><span>Please wait...</span></div></div>
       <input type="hidden" name="id" value="<?php echo $case->id?>" />
       <?php
           wp_nonce_field('save-case');
       ?>
       </form>
    </div>
    <div class="clear space25"></div>
</div>
<script type="text/javascript">
jQuery(document).ready(function($){
    //Add Loading Div
    jQuery('#edit_test_case_wrapper .grid-box-body').append('<div class="loading1"></div>');
    //Delete
    jQuery('#test-data-box, #test-execution-box, #test-step-box').on('click', '.blue-delete-btn', function(){
        jQuery(this).parents('.field-row').fadeOut('fast', function(){
            jQuery(this).remove();                
        })
        return false;
    })            
    jQuery('#add-test-exec-data').click(function(){
        jQuery('#test-execution-box .btn-row').before('<div class="field-row">' + 
                       '<div class="grid-cell">' + 
                           '<label>Property Name:</label>' +
                           '<input type="text" name="property_name_exec[]" value="" class="input" />' +
                       '</div>' +
                       '<div class="grid-cell">' +
                           '<label>Property Value:</label>' +
                           '<input type="text" name="property_value_exec[]" value="" class="input medium-input" />' +
                       '</div>' +
                       '<div class="grid-cell">' +
                           '<label>&nbsp;</label>' +
                           '<a href="#" class="action-btn blue-delete-btn icon-btn"><span class="p"></span></a>' +
                       '</div>' +
                       '<div class="clear"></div>' +
                   '</div>');
        return false;
    })
    jQuery('#add-test-step').click(function(){
        jQuery('#test-step-box .btn-row').before('<div class="field-row">' + 
                       '<div class="grid-cell width40P">' + 
                           '<label>Action:</label>' +
                           '<textarea name="step_action[]" class="textarea width98P"></textarea>' +
                       '</div>' +
                       '<div class="grid-cell width50P">' +
                           '<label>Expected Result:</label>' +
                           '<textarea name="step_expected[]" class="textarea width98P"></textarea>' +
                       '</div>' +
                       '<div class="grid-cell">' +
                           '<label>&nbsp;</label>' +
                           '<a href="#" class="action-btn blue-delete-btn icon-btn"><span class="p"></span></a>' +
                       '</div>' +
                       '<div class="clear"></div>' +
                   '</div>');
        $('#test-step-box .btn-row').prev().find('textarea').redactor({
            air: true,
            minHeight: 100,
            maxHeight: 100
        })
        return false;
    })
    
    jQuery('body').on('change', 'input[name="suite_id[]"], select[name="choose_tester_role"], select[name="choose_harness_role"]', function(){
        if(jQuery('input[name="suite_id[]"]:checked').length < 1)
        {
            jQuery('#choose-conf-level-box .column').html('');
            jQuery('#choose-roles-box select').each(function(){
                jQuery(this).find('option:gt(0)').remove();
            })
            jQuery('#choose-scenarios-box .column').html('');
            jQuery('#choose-init-msg-box  select option:gt(0)').remove();
            jQuery('#case-template-data  .field-row:gt(0)').remove();
            jQuery('#profile-instances').html('');            
            return false;
        }
        jQuery('#suites-box .loading1, #choose-conf-level-box .loading1, #choose-roles-box .loading1, #choose-init-msg-box .loading1, #test-data-box .loading1, #choose-scenarios-box .loading1').show();
        jQuery.ajax({
            url: '<?php echo get_site_url()?>',
            data: jQuery('input[name="suite_id[]"]:checked').serialize() + '&tester_role=' + jQuery('select[name="choose_tester_role"]').val() + '&harness_role=' + jQuery('select[name="choose_harness_role"]').val() +'&_wpnonce=<?php echo wp_create_nonce('get-suite-info-for-case')?>&id=<?php echo $case->id?>',
            type: 'POST',
            dataType: 'xml',
            complete: function(){
                jQuery('#suites-box .loading1, #choose-conf-level-box .loading1, #choose-roles-box .loading1, #choose-init-msg-box .loading1, #test-data-box .loading1, #choose-scenarios-box .loading1').hide();
            },
            success: function(rsp)
            {
                if(jQuery(rsp).find('status').text() == 'success')
                {
                    jQuery('#choose-conf-level-box .column').html('');
                    jQuery('#choose-conf-level-box .column').append(jQuery(rsp).find('conflevel').text());
                    jQuery('.conf-level-suite-box input.default-level').on('click', function(){
                        this.checked = true;
                    })
                    jQuery('#choose-roles-box .grid-cell:lt(2)').remove();
                    jQuery('#choose-roles-box .column .field-row').prepend(jQuery(rsp).find('roles').text());
                    
                    jQuery('#choose-scenarios-box .column').html('');
                    jQuery('#choose-scenarios-box .column').append(jQuery(rsp).find('scenario').text());
                    
                    jQuery('#choose-init-msg-box select[name="choose_init_message"]').replaceWith(jQuery(rsp).find('initmsg').text());
                    jQuery('#profile-instances').html(jQuery(rsp).find('profiles').text());
                    jQuery("#profile-instances a[rel='custom-popup']").cplightbox();
                    
                    jQuery('#case-template-data').append(jQuery(rsp).find('templates').text());                    
                }                
            }
        })
    })
    
    jQuery('#caseForm').submit(function(){                
        var nameReg = /^[A-Za-z0-9-.]+$/;
        if(!nameReg.test(jQuery('#test_case_id').val()))
        {
            jQuery('#edit_test_case_wrapper').find('.message').remove();
            jQuery('#edit_test_case_wrapper .grid-box-footer').append('<div class="message error">Names must consist of only letters, numbers, dots and dashes [A-Za-z0-9-.]+</div>');
            return false;
        }
        
        jQuery('#saving-wrapper').show();
        jQuery('#edit_test_case_wrapper .grid-box-footer .message').remove();
        jQuery.ajax({
            url: "/",
            type: "post",
            data: jQuery("#caseForm").serialize() + "&byAjax=1",
            dataType: 'json',
            success: function(rsp){                
                if(rsp.status == 'success')
                {
                    document.location.href = rsp.link;
                }else{
                    jQuery('#saving-wrapper').hide();
                    jQuery('#edit_test_case_wrapper .grid-box-footer').append('<div class="message error">' + rsp.message + "</div>");
                }
            },
            error: function(err){
                jQuery('#saving-wrapper').hide();
                jQuery('#edit_test_case_wrapper .grid-box-footer').append('<div class="message error">' + err.responseText + "</div>");
            }
        })
        return false;
    })
    
    //Manage Version
    jQuery('.version-cell .action-btn').click(function(){
        if(jQuery(this).hasClass('disabled-btn'))
            return false;
        var prev = jQuery(this).prev();
        prev.val( parseInt(prev.val()) + 1 );
        if(prev.attr('id') == 'version_major')
        {
            jQuery('#version_minor').val(0);
            jQuery('#version_patch').val(0);
        }else if(prev.attr('id') == 'version_minor'){
            jQuery('#version_patch').val(0);
        }
        jQuery(this).before('<a href="#" class="version-cancel has-tooltip"><span class="simple_tooltip"><span></span>Undo</span></a>');
        jQuery('.version-cell .action-btn').hide();
        return false;
    })
    
    jQuery('.version-cell').on('click', '.version-cancel', function(){
        jQuery('.version-cell .version-updated, .version-cell .version-cancel').remove();
        jQuery('.version-cell .action-btn').show();
        jQuery('#version_major').val(jQuery('#version_major').attr('data-default'));
        jQuery('#version_minor').val(jQuery('#version_minor').attr('data-default'));
        jQuery('#version_patch').val(jQuery('#version_patch').attr('data-default'));
        return false;
    })
    
    jQuery('.conf-level-suite-box').on('click', 'input.default-level', function(){
        this.checked = true;
    })
    
    $('#test_intent_description').redactor({
        air: true,
        minHeight: 120
    })
    
    $('#test-step-box textarea').redactor({
        air: true,
        minHeight: 100,
        maxHeight: 100
    })
    
});
</script>
<?php
get_footer();
