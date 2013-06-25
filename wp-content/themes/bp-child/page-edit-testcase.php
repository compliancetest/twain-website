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

get_header();


?>
<div class="content" id="edit_test_case_wrapper">
    <div class="space25"></div>
    <div class="column fifth left nopaddingleft nopaddingright sidebar">
        <?php get_sidebar('dashboard'); ?>
    </div>        
    <div class="column four_fifths right container"> 
      <form name="suiteForm" id="suiteForm" action="" method="post" enctype="multipart/form-data">
        <?php if($isNew){ ?>
        <h2>Add New Test Case</h2>
        <?php }else{ ?>
        <h2>Edit Test Case: <?php ?></h2>
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
                               <?php foreach($groups as $row){ ?>
                               <option value="<?php echo $row->id?>"><?php echo apply_filters('the_title', $row->name)?></option>
                               <?php } ?>
                           </select>                           
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
                   <div class="field-row">
                       <div class="grid-cell radio-cell">
                           <label><input type="radio" name="conformance_level" value="" /> Conformance Level1</label>
                       </div>
                       <div class="grid-cell width60P">
                           Conformance Level1 Description
                       </div>
                       <div class="clear"></div>
                   </div>
                   <div class="field-row">
                       <div class="grid-cell radio-cell">
                           <label><input type="radio" name="conformance_level" value="" /> Conformance Level2</label>
                       </div>
                       <div class="grid-cell width60P">
                           Conformance Level2 Description
                       </div>
                       <div class="clear"></div>
                   </div>                   
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
                           </select>
                       </div>
                       <div class="grid-cell">
                           <label>Harness Role:</label>
                           <select name="choose_harness_role" class="select">
                               <option>- Select -</option>
                           </select>
                       </div>
                       <div class="grid-cell radio-cell">
                           <label>Initiator:</label>
                           <input type="radio" name="choose_initiator" value="tester" /> Tester
                           <input type="radio" name="choose_initiator" value="harness" /> Harness
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
                           <select name="choose_tester_role" class="select">
                               <option>- Select -</option>
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
                           <input type="text" name="test_case_id" id="test_case_id" value="" class="input" />
                       </div>                       
                       <div class="grid-cell">
                           <label>Version:</label>
                           <input type="text" name="version" id="version" value="" class="input" />
                       </div>                       
                       <div class="grid-cell">
                           <label>Published:</label>
                           <input type="text" name="published" id="published" value="" class="input datepicker" />
                       </div>                   
                       <div class="clear"></div>
                   </div>               
                   <div class="field-row">
                       <div class="grid-cell">
                           <label>Test Intent Description:</label>
                           <textarea name="test_intent_description" id="test_intent_description" value="" class="textarea"></textarea>
                       </div>                   
                       <div class="clear"></div>
                   </div>               
                   
               </div>
           </div>
        </div>   
        <div class="space25"></div>
        <div class="grid-box grid-box-expandable grid-box-opened" id="test-dadta-box">
           <div class="grid-box-header">
               <a class="gbh-btn gbh-btn-expandable left" href="javascript: void(0);">Ex</a>
               <h5 class="left">Test Data</h5>
               <div class="clear"></div>
           </div>
           <div class="grid-box-body">
               <div class="column">
                   <div class="field-row">
                       <div class="grid-cell">
                           <label>Property Name:</label>
                           <input type="text" name="property_name_data[]" value="" class="input" />
                       </div>                       
                       <div class="grid-cell">
                           <label>Property Value:</label>
                           <input type="text" name="property_value_data[]" value="" class="input medium-input" />
                       </div>                       
                       <div class="grid-cell">
                           <label>&nbsp;</label>
                           <a href="#" class="action-btn blue-delete-btn icon-btn"><span class="p"></span></a>
                       </div>                   
                       <div class="clear"></div>
                   </div>               
                   <div class="btn-row">
                       <div class="grid-cell">
                           <a href="#" class="action-btn add-new-btn" id="add-test-data"><span class="p"></span><span class="t">New Test Data</span></a>                       
                       </div>
                       <div class="clear"></div>
                   </div>
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
                           <input type="text" name="protocol_binding2" value="" class="input" />
                       </div>             
                       <div class="grid-cell">
                           <label>Test endpoint URL:</label>
                           <input type="text" name="test_url" value="" class="input medium-input" />
                       </div>                     
                       <div class="clear"></div>
                   </div>               
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
                   <div class="btn-row">
                       <div class="grid-cell">
                           <a href="#" class="action-btn add-new-btn" id="add-test-data"><span class="p"></span><span class="t">New Property</span></a>                       
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
                           <input type="radio" name="outcome_type" value="Positive" /> Positive
                           <input type="radio" name="outcome_type" value="Negative" /> Negative
                       </div>             
                       <div class="grid-cell radio-cell">
                           <label>Bluk:</label>
                           <input type="radio" name="bulk" value="Yes" /> Yes
                           <input type="radio" name="bulk" value="No" /> No
                       </div>             
                       
                       <div class="grid-cell">
                           <label>Test Pattern:</label>
                           <input type="text" name="message_count" value="" class="input" />
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
                    <div class="field-row">
                       <div class="grid-cell">
                           <label>Action:</label>
                           <input type="text" name="step_action[]" value="" class="input" />
                       </div>                       
                       <div class="grid-cell">
                           <label>Expected Result:</label>
                           <input type="text" name="step_expected[]" value="" class="input medium-input" />
                       </div>                       
                       <div class="grid-cell">
                           <label>&nbsp;</label>
                           <a href="#" class="action-btn blue-delete-btn icon-btn"><span class="p"></span></a>
                       </div>                   
                       <div class="clear"></div>
                   </div>               
                   <div class="btn-row">
                       <div class="grid-cell">
                           <a href="#" class="action-btn add-new-btn" id="add-test-data"><span class="p"></span><span class="t">New Test Step</span></a>                       
                       </div>
                       <div class="clear"></div>
                   </div>      
               </div>
           </div>
        </div>     
        
       </form>
    </div>
    <div class="clear space25"></div>
</div>
<?php
get_footer();
