<?php
/**
* Template Name:Add/Edit Test Case
*/


$caseID = isset($_GET['id']) ? $_GET['id'] : null;

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


$testsuites = $case->getAvailableTestSuites();

if(isset($_GET['suite_id']))
{
    foreach($testsuites as $r)
    {
        if($r->ID == $_GET['suite_id'])
        {
            $case->testSuite = $r->ID;
            break;
        }
    }
}

if(!$case->testSuite && $isNew && count($testsuites) > 0)
    $case->testSuite = $testsuites[0]->ID;

$suite = new TestSuite($case->testSuite);
$suite->load();

get_header();


?>
<div class="content edit-item-wrapper" id="edit_test_case_wrapper">
    <div class="space25"></div>
    <div class="column fifth left nopaddingleft nopaddingright sidebar">
        <?php get_sidebar('dashboard'); ?>
    </div>        
    <div class="column four_fifths right container relative"> 
      <form name="caseForm" id="caseForm" action="" method="post" enctype="multipart/form-data">
        <?php if($isNew){ ?>
        <h2>Add New Test Case</h2>
        <?php }else{ ?>
        <h2>Edit Test Case: <?php $case->name ?></h2>
        <?php } ?>        
        <div class="grid-box grid-box-expandable grid-box-opened" id="suites-box">
           <div class="grid-box-header">
               <a class="gbh-btn gbh-btn-expandable left" href="javascript: void(0);">Ex</a>
               <h5 class="left">Choose Test Suite</h5>
               <div class="clear"></div>
           </div>
           <div class="grid-box-body">
               <div class="column">
                   <div class="field-row">
                       <div class="grid-cell">
                           <select name="suite_id" id="suite_id" class="select">                           
                               <?php foreach($testsuites as $row){ ?>
                               <option value="<?php echo $row->ID?>" <?php echo $case->testSuite == $row->ID ? 'selected="selected"' : ''?>><?php echo apply_filters('the_title', $row->post_title)?></option>
                               <?php } ?>
                           </select>                           
                       </div>
                       <div class="clear"></div>
                   </div>
               </div>
           </div>
        </div>
        <div class="space25"></div>
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
                           <label>Test Case ID:</label>
                           <input type="text" name="test_case_id" id="test_case_id" value="<?php echo $case->testCaseID?>" class="input" <?php echo !$isNew ? 'readonly="readonly"' : ''?> />
                       </div>           
                       <div class="grid-cell">
                           <label>Version:</label>
                           <input type="text" name="version" id="version" value="<?php echo $case->version?>" class="input" />
                       </div>                                                
                       <div class="grid-cell">
                           <label>Published:</label>
                           <input type="text" name="published" id="published" value="<?php echo $case->publishedDate?>" class="input datepicker" />
                       </div>                   
                       <div class="clear"></div>
                   </div>               
                   <div class="field-row">
                       <div class="grid-cell radio-cell width90P">
                           <label for="ts_identifier">Status: </label>
                           <input type="radio" name="test_case_status" id="ts_status_draft" value="Draft" <?php echo $case->status == 'Draft' ? 'checked="checked"' : ''?> /> Draft
                           <input type="radio" name="test_case_status" id="ts_status_build" class="left15" value="Build" <?php echo $case->status == 'Build' ? 'checked="checked"' : ''?> /> Build
                           <input type="radio" name="test_case_status" id="ts_status_active" class="left15" value="Active" <?php echo $case->status == 'Active' ? 'checked="checked"' : ''?> /> Active
                           <input type="radio" name="test_case_status" id="ts_status_deprecated" class="left15" value="Deprecated" <?php echo $case->status == 'Deprecated' ? 'checked="checked"' : ''?> /> Deprecated
                           <input type="radio" name="test_case_status" id="ts_status_obsolete" class="left15" value="Obsolete" <?php echo $case->status == 'Obsolete' ? 'checked="checked"' : ''?> /> Obsolete
                           
                       </div>
                       <div class="clear"></div>
                   </div>
                   <div class="field-row">                                 
                       <div class="grid-cell">
                           <label>Test Intent Description:</label>
                           <textarea name="test_intent_description" id="test_intent_description" class="textarea"><?php echo $case->testIntentDescription?></textarea>
                       </div>                   
                       <div class="grid-cell">
                           <label>Execution Sequence Number:</label>
                           <input type="text" name="sequence_number" id="sequence_number" value="<?php echo $case->sequenceNumber?>" class="input" />                           
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
               <h5 class="left">Choose Conformance Level</h5>
               <div class="clear"></div>
           </div>
           <div class="grid-box-body">
               <div class="column">
                   <?php foreach($suite->conformanceLevel as $row){ ?>
                   <div class="field-row">
                       <div class="grid-cell radio-cell">
                           <label><input type="radio" name="conformance_level" value="<?php echo $row['code']?>" <?php echo $case->conformanceLevel == $row['code'] ? 'checked="checked"' : ''?> /> <?php echo $row['code']?></label>
                       </div>
                       <div class="grid-cell width60P">
                           <?php echo $row['desc']?>
                       </div>
                       <div class="clear"></div>
                   </div>
                   <?php } ?>                   
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
                               
                               <?php foreach($suite->roles as $row) {?>
                               <option value="<?php echo $row['name']?>" <?php echo $case->testerRole == $row['name'] ? 'selected="selected"' : ''?>><?php echo $row['name']?></option>
                               <?php } ?>
                           </select>
                       </div>
                       <div class="grid-cell">
                           <label>Harness Role:</label>
                           <select name="choose_harness_role" class="select">
                               <option>- Select -</option>
                               <?php foreach($suite->roles as $row) {?>
                               <option value="<?php echo $row['name']?>" <?php echo $case->harnessRole == $row['name'] ? 'selected="selected"' : ''?>><?php echo $row['name']?></option>
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
                               $messages = explode(',', $suite->initiatingMessage);
                               foreach($messages as $row) {?>
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
                       <?php foreach($case->messageTemplates as $row){ ?>
                       <div class="field-row">
                           <div class="grid-cell">
                               <label>Template Name:</label>
                               <input type="text" name="message_template_name[]" value="<?php echo $row['name']?>" class="input" />
                           </div> 
                           <div class="grid-cell">
                               <label>Template URL:</label>
                               <input type="text" name="message_template_url[]" value="<?php echo $row['url']?>" class="input medium-input" />
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
                               <label>Template Name:</label>
                               <input type="text" name="message_template_name[]" value="" class="input" />
                           </div> 
                           <div class="grid-cell">
                               <label>Template URL:</label>
                               <input type="text" name="message_template_url[]" value="" class="input medium-input" />
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
                               <a href="#" class="action-btn add-new-btn" id="add-message-template"><span class="p"></span><span class="t">New Template</span></a>                       
                           </div>
                           <div class="clear"></div>
                       </div>
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
                       $profileInstances = $suite->getProfileInstancesRows();
                       foreach($profileInstances as $instance){
                           $instanceObj = json_decode(base64_decode($instance->content));
                   ?>
                       <div class="field-row">
                           <div class="grid-cell width15P">
                               <input type="checkbox" name="profile_instances[]" value="<?php echo $instance->id?>" <?php echo cp_checked($instance->id, $case->profileInstances) ?> />
                               <a href="<?php echo get_site_url()?>?td-action=<?php echo wp_create_nonce('view-profile-instance')?>&id=<?php echo $instance->id?>" rel="custom-popup" cp-type="ajax"><?php echo $instance->profile_name?></a>
                           </div>
                           <div class="grid-cell width10P">
                               <label><?php echo $instanceObj->ProfilePurpose?></label>
                           </div>
                           <div class="grid-cell width15P">
                               <a href="<?php echo get_site_url()?>?td-action=<?php echo wp_create_nonce('view-profile-type')?>&id=<?php echo $instance->type_id?>" rel="custom-popup" cp-type="ajax" class="view-profile-type-link"><?php echo $instance->profile_type_title; ?></a> 
                           </div>
                           <div class="grid-cell width45P">
                               <input type="text" readonly="readonly" value="<?php echo get_site_url()?>/profiles/<?php echo $instance->type?>/<?php echo $instance->filename?>" class="input width100P" />
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
                           <label>Test endpoint URL:</label>
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
                           <input type="text" name="message_count" value="<?php echo $case->testPattern?>" class="input" />
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
                       <div class="grid-cell">
                           <label>Action:</label>
                           <textarea name="step_action[]" class="textarea width280"><?php echo $row['action']?></textarea>
                       </div>                       
                       <div class="grid-cell">
                           <label>Expected Result:</label>
                           <textarea name="step_expected[]" value="<?php echo $row['result']?>" class="textarea width350"><?php echo $row['result']?></textarea>
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
                           <label>Action:</label>
                           <textarea name="step_action[]" class="textarea width280"></textarea>
                       </div>                       
                       <div class="grid-cell">
                           <label>Expected Result:</label>
                           <textarea name="step_expected[]" value="<?php echo $row['result']?>" class="textarea width350"></textarea>
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
                   <a href="javascript: history.go(-1)" class="action-btn cancel-btn"><span class="p"></span><span class="t">Cancel</span></a>
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
jQuery(document).ready(function(){
    //Add Loading Div
    jQuery('#edit_test_case_wrapper .grid-box-body').append('<div class="loading1"></div>');
    //Delete
    jQuery('#test-data-box, #test-execution-box, #test-step-box').on('click', '.blue-delete-btn', function(){
        jQuery(this).parents('.field-row').fadeOut('fast', function(){
            jQuery(this).remove();                
        })
        return false;
    })        
    jQuery('#add-message-template').click(function(){
        jQuery('#case-template-data .btn-row').before('<div class="field-row">' + 
                       '<div class="grid-cell">' + 
                           '<label>Template Name:</label>' +
                           '<input type="text" name="message_template_name[]" value="" class="input" />' +
                       '</div>' +
                       '<div class="grid-cell">' +
                           '<label>Template URL:</label>' +
                           '<input type="text" name="message_template_url[]" value="" class="input medium-input" />' +
                       '</div>' +
                       '<div class="grid-cell">' +
                           '<label>&nbsp;</label>' +
                           '<a href="#" class="action-btn blue-delete-btn icon-btn"><span class="p"></span></a>' +
                       '</div>' +
                       '<div class="clear"></div>' +
                   '</div>');
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
                       '<div class="grid-cell">' + 
                           '<label>Action:</label>' +
                           '<textarea name="step_action[]" class="textarea width280"></textarea>' +
                       '</div>' +
                       '<div class="grid-cell">' +
                           '<label>Expected Result:</label>' +
                           '<textarea name="step_expected[]" class="textarea width350"></textarea>' +
                       '</div>' +
                       '<div class="grid-cell">' +
                           '<label>&nbsp;</label>' +
                           '<a href="#" class="action-btn blue-delete-btn icon-btn"><span class="p"></span></a>' +
                       '</div>' +
                       '<div class="clear"></div>' +
                   '</div>');
        return false;
    })
    
    jQuery('#suite_id').change(function(){
        jQuery('#suites-box .loading1, #choose-conf-level-box .loading1, #choose-roles-box .loading1, #choose-init-msg-box .loading1, #test-data-box .loading1').show();
        jQuery.ajax({
            url: '<?php echo get_site_url()?>',
            data: {
                'suite_id': jQuery(this).val(),
                '_wpnonce': '<?php echo wp_create_nonce('get-suite-info-for-case')?>'
            },
            type: 'POST',
            dataType: 'xml',
            complete: function(){
                jQuery('#suites-box .loading1, #choose-conf-level-box .loading1, #choose-roles-box .loading1, #choose-init-msg-box .loading1, #test-data-box .loading1').hide();
            },
            success: function(rsp)
            {
                if(jQuery(rsp).find('status').text() == 'success')
                {
                    jQuery('#choose-conf-level-box .field-row').remove();
                    jQuery('#choose-conf-level-box .column').append(jQuery(rsp).find('conflevel').text());
                    
                    jQuery('#choose-roles-box .grid-cell:lt(2)').remove();
                    jQuery('#choose-roles-box .column .field-row').prepend(jQuery(rsp).find('roles').text());
                    
                    jQuery('#choose-init-msg-box select[name="choose_init_message"]').replaceWith(jQuery(rsp).find('initmsg').text());
                    jQuery('#profile-instances').html(jQuery(rsp).find('profiles').text());
                    jQuery("#profile-instances a[rel='custom-popup']").cplightbox();
                    
                }                
            }
        })
    })
    
    jQuery('#caseForm').submit(function(){
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
});
</script>
<?php
get_footer();
