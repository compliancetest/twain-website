<?php
/*
 * Template Name: Add/Edit Test Suite
 */

$suiteID = isset($_GET['id']) ? $_GET['id'] : null;

if( ($suiteID != null && !can_edit_suite($suiteID)) || ($suiteID == null && !can_create_suite()) )
{
    wp_redirect("/");
    exit;
}

$suite = new TestSuite($suiteID);
if($suiteID)
    $suite->load();

get_header();

$groups = getUserAdminGroups(get_current_user_id());
if(!$suite->community_id)
    $suite->community_id = $groups[0]->id;
?>
<div class="content edit-item-wrapper" id="edit_test_suite_wrapper">
    <div class="space25"></div>
    <div class="column fifth left nopaddingleft nopaddingright sidebar">
        <?php get_sidebar('dashboard'); ?>
    </div>        
    <div class="column four_fifths right container"> 
      <form name="suiteForm" id="suiteForm" action="" method="post" enctype="multipart/form-data">
        <?php if(!$suite->id){ ?>
        <h2>Add New Test Suite</h2>
        <?php }else{ ?>
        <h2>Edit Test Suite: <?php echo $suite->name?></h2>
        <?php } ?>        
        <div class="grid-box grid-box-expandable grid-box-opened" id="community-box">
           <div class="grid-box-header">
               <a class="gbh-btn gbh-btn-expandable left" href="javascript: void(0);">Ex</a>
               <h5 class="left">Choose Community</h5>
               <div class="clear"></div>
           </div>
           <div class="grid-box-body">
               <div class="column">
                   <div class="field-row">
                       <div class="grid-cell">
                           <select name="community_id" id="community_id" class="select">
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
        <div class="grid-box grid-box-expandable grid-box-opened" id="suite-type-box">
           <div class="grid-box-header">
               <a class="gbh-btn gbh-btn-expandable left" href="javascript: void(0);">Ex</a>
               <h5 class="left">Test Suite Types</h5>
               <div class="clear"></div>
           </div>
           <div class="grid-box-body">
               <div class="column">
                   <div class="field-row">
                       <div class="grid-cell checkbox-cell">
                           <?php 
                           $suiteTypes = $suite->getAllTestSuiteTypes();
                           
                           foreach($suiteTypes as $row) { ?>
                           <label><input type="checkbox" name="test_suite_type[]" value="<?php echo $row->term_id?>" <?php echo isset($suite->type[$row->term_id]) ? 'checked="checked"' : '' ?>> <?php echo $row->name?></label>
                           <?php } ?>
                       </div>
                       <div class="clear"></div>
                   </div>
               </div>
           </div>
        </div>
        <div class="clear"></div>
        <div class="space25"></div>
        <div class="grid-box grid-box-expandable grid-box-opened" id="suite-info-box">
           <div class="grid-box-header">
               <a class="gbh-btn gbh-btn-expandable left" href="javascript: void(0);">Ex</a>
               <h5 class="left">Test Suite Information</h5>
               <div class="clear"></div>
           </div>
           <div class="grid-box-body">
               <div class="column">
                   <div class="field-row">
                       <div class="grid-cell">
                           <label for="ts_name">Name: </label>
                           <input type="text" id="ts_name" name="ts_name" class="input required" value="<?php echo $suite->name?>" />
                           
                       </div>
                       <div class="grid-cell">
                           <label for="ts_identifier">Identifier: </label>
                           <input type="text" id="ts_identifier" name="ts_identifier" class="input" value="<?php echo $suite->identifier?>" />
                       </div>
                       <div class="grid-cell">
                           <label for="ts_issue_date">Issue Date: </label>
                           <input type="text" id="ts_issue_date" name="ts_issue_date" class="input datepicker" value="<?php echo $suite->issueDate?>" />
                       </div>
                       <div class="clear"></div>
                   </div>
                   <div class="field-row">
                       <div class="grid-cell">
                           <label for="ts_name">Issuer: </label>
                           <input type="text" id="ts_issuer" name="ts_issuer" class="input" value="<?php echo $suite->issuer?>" />                           
                       </div>
                       <div class="grid-cell radio-cell">
                           <label for="ts_identifier">Status: </label>
                           <input type="radio" name="ts_status" id="ts_status_active" value="Active" <?php echo $suite->status == 'Active' ? 'checked="checked"' : ''?> /> Active
                           <input type="radio" name="ts_status" id="ts_status_on_hold" value="On Hold" <?php echo $suite->status == 'On Hold' ? 'checked="checked"' : ''?> /> On Hold
                       </div>
                       <div class="grid-cell">
                           <label for="ts_issue_date">Revision Description: </label>
                           <input type="text" id="ts_revision_description" name="ts_revision_description" class="input" value="<?php echo $suite->revisionDescription?>" />
                       </div>
                       <div class="clear"></div>
                   </div>
                   <div class="field-row">
                       <div class="grid-cell">
                           <label for="ts_name">Version Test Suite: </label>
                           <input type="text" id="ts_version" name="ts_version" class="input" value="<?php echo $suite->version?>" />
                       </div>
                       <div class="grid-cell">
                           <label for="ts_identifier">Description: </label>
                           <textarea cols="" rows="" class="textarea" name="ts_description" id="ts_description"><?php echo $suite->description?></textarea>
                       </div>
                       <div class="clear"></div>
                   </div>                   
               </div>
           </div>
       </div>
       <div class="space20"></div>
       <div class="grid-box grid-box-expandable grid-box-opened">
           <div class="grid-box-header">
               <a class="gbh-btn gbh-btn-expandable left" href="javascript: void(0);">Ex</a>
               <h5 class="left">Initiating Message</h5>
               <div class="clear"></div>
           </div>
           <div class="grid-box-body">
               <div class="column">
                   <div class="field-row">
                       <div class="grid-cell">
                           <textarea cols="" rows="" class="textarea" name="init_message" id="init_message"><?php echo $suite->initiatingMessage?></textarea>
                       </div>
                       <div class="grid-cell">
                           <label class="light-desc"><i>Type Initiating Messages (comma separated)</i></label>
                       </div>
                       <div class="clear"></div>
                   </div>                   
               </div>
           </div>
       </div>
       <div class="space20"></div>
       <div class="grid-box grid-box-expandable grid-box-opened">
           <div class="grid-box-header">
               <a class="gbh-btn gbh-btn-expandable left" href="javascript: void(0);">Ex</a>
               <h5 class="left">Subscription Price</h5>
               <div class="clear"></div>
           </div>
           <div class="grid-box-body">
               <div class="column">
                   <div class="field-row">
                       <div class="grid-cell">
                           <input type="text" id="monthly_subscription_price" name="monthly_subscription_price" class="input" value="<?php echo $suite->monthlySubscriptionPrice?>" />
                       </div>
                       <div class="grid-cell">
                           <label class="light-desc"><i>Monthly Subscription Price</i></label>
                       </div>
                       <div class="clear"></div>
                   </div>                   
               </div>
           </div>
       </div>       
       <div class="space20"></div>
       <div class="grid-box grid-box-expandable grid-box-opened" id="conf-level-box">
           <div class="grid-box-header">
               <a class="gbh-btn gbh-btn-expandable left" href="javascript: void(0);">Ex</a>
               <h5 class="left">Conformance Level</h5>
               <div class="clear"></div>
           </div>
           <div class="grid-box-body">
               <div class="column">               
               <?php foreach($suite->conformanceLevel as $row){ ?>
                   <div class="field-row">
                       <div class="grid-cell">
                           <label>Conformance Level Code:</label>
                           <input type="text" class="input" name="lvl_code[]" value="<?php echo $row['code']?>" />
                       </div>
                       <div class="grid-cell">
                           <label>Conformance Level Description:</label>
                           <textarea cols="" rows="" class="textarea" name="lvl_desc[]"><?php echo $row['desc'] ?></textarea>
                       </div>
                       <div class="grid-cell">
                           <label>&nbsp;</label>
                           <a href="#" class="action-btn blue-delete-btn icon-btn"><span class="p"></span></a>
                       </div>
                       <div class="clear"></div>
                   </div>                 
                <?php } ?>  
                <?php if(!$suite->id) {?>
                   <div class="field-row">
                       <div class="grid-cell">
                           <label>Conformance Level Code:</label>
                           <input type="text" class="input" name="lvl_code[]" value="" />
                       </div>
                       <div class="grid-cell">
                           <label>Conformance Level Description:</label>
                           <textarea cols="" rows="" class="textarea" name="lvl_desc[]"></textarea>
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
                           <a href="#" class="action-btn add-new-btn" id="add-conformance-level"><span class="p"></span><span class="t">New Conformance Level</span></a>                       
                       </div>
                       <div class="clear"></div>
                   </div>
               </div>
               
           </div>
       </div>
       <div class="space20"></div>
       <?php if($suite->id){ ?>
       <div class="grid-box grid-box-expandable grid-box-opened" id="test-cases-box">
           <div class="grid-box-header">
               <a class="gbh-btn gbh-btn-expandable left" href="javascript: void(0);">Ex</a>
               <h5 class="left">Test Cases</h5>
               <div class="clear"></div>
           </div>
           <div class="grid-box-body">
               <div class="column">
                   <div class="field-row">
                       <?php foreach($suite->testCases as $row){ ?>
                       <div class="grid-cell">                           
                           <a href="<?php echo get_permalink($row->ID)?>" class="test-case-link"><?php echo get_the_title($row->ID)?></a>
                           <a href="#" class="action-btn blue-delete-btn icon-btn"><span class="p"></span></a>
                       </div>
                       <?php } ?>  
                       
                       <a href="#" target="_blank" class="action-btn add-new-btn"><span class="p"></span><span class="t">New Test Case</span></a>                       
                       
                       <div class="clear"></div>
                   </div>                                      
               </div>
               
           </div>
       </div>
       <div class="space20"></div>
       <?php } ?>
       <?php
           $availableSuites = $suite->getBrotherSuites();
           
       ?>
       <div class="grid-box grid-box-expandable grid-box-opened" id="related-suites-box">
           <div class="grid-box-header">
               <a class="gbh-btn gbh-btn-expandable left" href="javascript: void(0);">Ex</a>
               <h5 class="left">Related Test Suites</h5>
               <div class="clear"></div>
           </div>
           <div class="grid-box-body">
               <div class="column">
                   <?php foreach($suite->relatedSuites as $crow){ ?>
                   <div class="field-row">
                       <div class="grid-cell">
                           <label>Related Suites:</label>
                           <div class="styled_select">
                           <select name="ts[]" class="select">
                               <option>- Select -</option>
                               <?php foreach($availableSuites as $row) { 
                                   echo '<option value="' . $row->ID . '" ' . ($crow['id'] == $row->ID ? ' selected="selected"' : '')  . '>' . $row->post_title . '</option>';
                               } ?>
                           </select>
                           </div>
                       </div>
                       <div class="grid-cell">
                           <label>Description:</label>
                           <textarea cols="" rows="" class="textarea" name="ts_desc[]"><?php echo $crow['desc'] ?></textarea>
                       </div>
                       <div class="grid-cell">
                           <label>&nbsp;</label>
                           <a href="#" class="action-btn blue-delete-btn icon-btn"><span class="p"></span></a>
                       </div>
                       <div class="clear"></div>
                   </div>
                   <?php } ?>
                   <?php if(!$suite->id) { ?>
                   <div class="field-row">
                       <div class="grid-cell">
                           <label>Related Suites:</label>
                           <div class="styled_select">
                           <select name="ts[]" class="select">
                               <option>- Select -</option>
                               <?php foreach($availableSuites as $row) { 
                                   echo '<option value="' . $row->ID . '">' . $row->post_title . '</option>';
                               } ?>
                           </select>
                           </div>
                       </div>
                       <div class="grid-cell">
                           <label>Description:</label>
                           <textarea cols="" rows="" class="textarea" name="ts_desc[]"></textarea>
                       </div>
                       <div class="grid-cell">
                           <label>&nbsp;</label>
                           <a href="#" class="action-btn blue-delete-btn icon-btn"><span class="p"></span></a>
                       </div>
                       <div class="clear"></div>
                   </div>
                   <?php } ?>
                   <div style="display: none" id="brother-suites">
                       <select name="ts[]" class="select">
                           <option>- Select -</option>
                           <?php foreach($availableSuites as $row) { 
                               echo '<option value="' . $row->ID . '">' . $row->post_title . '</option>';
                           } ?>
                       </select>
                   </div>
                   <div class="btn-row">
                       <div class="grid-cell">
                           <a href="#" class="action-btn add-new-btn" id="add-related-suite"><span class="p"></span><span class="t">New Test Suite</span></a>                       
                       </div>
                       <div class="clear"></div>
                   </div>
               </div>               
           </div>
       </div>
       <div class="space20"></div>
       <div class="grid-box grid-box-expandable grid-box-opened" id="roles-box">
           <div class="grid-box-header">
               <a class="gbh-btn gbh-btn-expandable left" href="javascript: void(0);">Ex</a>
               <h5 class="left">Roles</h5>
               <div class="clear"></div>
           </div>
           <div class="grid-box-body">
               <div class="column">
                   <?php                    
                   foreach($suite->roles as $row){ ?>
                   <div class="field-row">
                       <div class="grid-cell">
                           <label>Name:</label>
                           <input type="text" class="input" name="role_names[]" value="<?php echo $row['name']?>" />
                       </div>
                       <div class="grid-cell">
                           <label>Description:</label>
                           <input type="text" class="input medium-input" name="role_descs[]" value="<?php echo $row['desc']?>" />
                       </div>
                       <div class="grid-cell">
                           <label>&nbsp;</label>
                           <a href="#" class="action-btn blue-delete-btn icon-btn"><span class="p"></span></a>
                       </div>
                       <div class="clear"></div>
                   </div>                 
                   <?php } ?>  
                   <?php if(!$suite->id){ ?>
                   <div class="field-row">
                       <div class="grid-cell">
                           <label>Name:</label>
                           <input type="text" class="input" name="role_names[]" value="" />
                       </div>
                       <div class="grid-cell">
                           <label>Description:</label>
                           <input type="text" class="input medium-input" name="role_descs[]" value="" />
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
                           <a href="#" class="action-btn add-new-btn" id="add-new-role"><span class="p"></span><span class="t">New Role</span></a>                       
                       </div>
                       <div class="clear"></div>
                   </div>
               </div>               
           </div>
       </div>
       <div class="space20"></div>
       <div class="grid-box grid-box-expandable grid-box-opened" id="specs-box">
           <div class="grid-box-header">
               <a class="gbh-btn gbh-btn-expandable left" href="javascript: void(0);">Ex</a>
               <h5 class="left">Specification Documents</h5>
               <div class="clear"></div>
           </div>
           <div class="grid-box-body">
               <div class="column">
                   <?php foreach($suite->specDocuments as $row) { ?>
                   <div class="field-row">
                       <div class="grid-cell">
                           <label>Document Name:</label>
                           <input type="text" class="input" name="doc_name[]" value="<?php echo $row->doc_name?>" />
                       </div>
                       <div class="grid-cell">
                           <label>Document Description:</label>
                           <textarea cols="" rows="" name="doc_desc[]" class="textarea"><?php echo $row->doc_desc?></textarea>
                           <label>Document Location:</label>
                           <input type="text" class="input medium-input" name="doc_loc[]" value="<?php echo $row->doc_loc_url?>" />
                       </div>
                       <div class="grid-cell">
                           <label>&nbsp;</label>
                           <a href="#" class="action-btn blue-delete-btn icon-btn"><span class="p"></span></a>                           
                       </div>
                       <div class="clear"></div>
                   </div>        
                   <?php } ?>
                   <?php if(!$suite->id) { ?>
                   <div class="field-row">
                       <div class="grid-cell">
                           <label>Document Name:</label>
                           <input type="text" class="input" name="doc_name[]" value="" />
                       </div>
                       <div class="grid-cell">
                           <label>Document Description:</label>
                           <textarea cols="" rows="" name="doc_desc[]" class="textarea"></textarea>
                           <label>Document Location:</label>
                           <input type="text" class="input medium-input" name="doc_loc[]" value="" />
                           <label>Or Upload Document</label>
                           <input type="file" class="input" name="doc_file[]" value="" />
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
                           <a href="#" class="action-btn add-new-btn" id="add-spec-doc"><span class="p"></span><span class="t">New Document</span></a>                       
                       </div>
                       <div class="clear"></div>
                   </div>
               </div>               
           </div>
       </div>
       <div class="space20"></div>
       <div class="grid-box grid-box-expandable grid-box-opened" id="excerpt-box">
           <div class="grid-box-header">
               <a class="gbh-btn gbh-btn-expandable left" href="javascript: void(0);">Ex</a>
               <h5 class="left">Excerpt</h5>
               <div class="clear"></div>
           </div>
           <div class="grid-box-body">
               <div class="column">
                   <div class="field-row">
                       <div class="grid-cell">
                           <textarea cols="" rows="" name="excerpt" class="textarea"><?php echo $suite->excerpt?></textarea>
                       </div>
                       <div class="grid-cell width255">
                           <label class="light-desc">
                            <i>Excerpts are optional hand-crafted summaries of your content that can be used in your theme.</i>
                           </label>
                       </div>
                       <div class="clear"></div>
                   </div>                                                         
               </div>               
           </div>
       </div>
       <div class="grid-box">
           <div class="grid-box-footer nobackground noshadow">
               <div class="btn-row nopaddingright">
                   <a href="#" class="action-btn process-btn submit-btn left15"><span class="p"></span><span class="t">SAVE TEST SUITE</span></a>
                   <a href="/test-suite-coverage" class="action-btn cancel-btn"><span class="p"></span><span class="t">Cancel</span></a>
                   <div class="clear"></div>
               </div>
           </div>
       </div>
       <input type="hidden" name="id" value="<?php echo $suite->id?>" />
       <input type="hidden" name="action" value="cp-suite-save" />
       <?php
           wp_nonce_field('save-suite');
       ?>
     </form>
    </div>
    <div class="clear space25"></div>
            
</div> <!--end content-->

<script type="text/javascript">
    jQuery(document).ready(function(){
        //Add Loading Div
        jQuery('#edit_test_suite_wrapper .grid-box-body').append('<div class="loading1"></div>');
        jQuery('#add-conformance-level').click(function(){
            jQuery('#conf-level-box .btn-row').before('<div class="field-row">' + 
                       '<div class="grid-cell">' +
                           '<label>Conformance Level Code:</label>' +
                           '<input type="text" class="input" name="lvl_code[]" value="" />' +
                       '</div>' +
                       '<div class="grid-cell">' + 
                           '<label>Conformance Level Description:</label>' +
                           '<textarea cols="" rows="" class="textarea" name="lvl_desc[]"></textarea>' +
                       '</div>' +
                       '<div class="grid-cell">' +
                           '<label>&nbsp;</label>' +
                           '<a href="#" class="action-btn blue-delete-btn icon-btn"><span class="p"></span></a>' +
                       '</div>' + 
                       '<div class="clear"></div>' +
                   '</div>');
            return false;
        });
        jQuery('#add-related-suite').click(function(){
            jQuery('#related-suites-box .btn-row').before('<div class="field-row">' + 
                       '<div class="grid-cell">' +
                           '<label>Related Suites:</label>' +
                           '<div class="styled_select">' +
                           jQuery('#brother-suites').html() +
                           '</div>' +
                       '</div>' +
                       '<div class="grid-cell">' +
                           '<label>Description:</label>' +
                           '<textarea cols="" rows="" class="textarea" name="ts_desc[]"></textarea>' +
                       '</div>' +
                       '<div class="grid-cell">' +
                           '<label>&nbsp;</label>' +
                           '<a href="#" class="action-btn blue-delete-btn icon-btn"><span class="p"></span></a>' +
                       '</div>' +
                       '<div class="clear"></div>' +
                   '</div>');
            return false;
        })
        jQuery('#add-new-role').click(function(){
            jQuery('#roles-box .btn-row').before('<div class="field-row">' + 
                       '<div class="grid-cell">' +
                           '<label>Name:</label>' +
                           '<input type="text" class="input" name="role_names[]" value="" />' +
                       '</div>' +
                       '<div class="grid-cell">' +
                           '<label>Description:</label>' +
                           '<input type="text" class="input medium-input" name="role_descs[]" value="" />' +
                       '</div>' +
                       '<div class="grid-cell">' +
                           '<label>&nbsp;</label>' +
                           '<a href="#" class="action-btn blue-delete-btn icon-btn"><span class="p"></span></a>' +
                       '</div>' +
                       '<div class="clear"></div>' +
                   '</div>');
            return false;
        })
        jQuery('#add-spec-doc').click(function(){
            jQuery('#specs-box .btn-row').before('<div class="field-row">' + 
                       '<div class="grid-cell">' + 
                           '<label>Document Name:</label>' + 
                           '<input type="text" class="input" name="doc_name[]" value="" />' +
                       '</div>' +
                       '<div class="grid-cell">' +
                           '<label>Document Description:</label>' +
                           '<textarea cols="" rows="" name="doc_desc[]" class="textarea"></textarea>' +
                           '<label>Document Location:</label>' +
                           '<input type="text" class="input medium-input" name="doc_loc[]" value="" />' +
                           '<label>Or Upload Document</label>' +
                           '<input type="file" class="input" name="doc_file[]" value="" />' +
                       '</div>' +
                       '<div class="grid-cell">' +
                           '<label>&nbsp;</label>' +
                           '<a href="#" class="action-btn blue-delete-btn icon-btn"><span class="p"></span></a>' +
                       '</div>' +
                       '<div class="clear"></div>' +
                   '</div>');
            return false;
        })
        
        //Delete
        jQuery('#conf-level-box, #related-suites-box, #roles-box, #specs-box').on('click', '.blue-delete-btn', function(){
            jQuery(this).parents('.field-row').fadeOut('fast', function(){
                jQuery(this).remove();                
            })
            return false;
        })
        jQuery('#test-cases-box .blue-delete-btn').click(function(){
            jQuery(this).parents('.grid-cell').fadeOut('fast', function(){
                jQuery(this).remove();                
            })
            return false;
        })
        
        //Update Related Test Suites 
        jQuery('#community_id').change(function(){
            jQuery('#community-box .loading1, #related-suites-box .loading1').show();
            jQuery.ajax({
                url: '<?php echo get_site_url()?>',
                data: {
                    'community_id': jQuery(this).val(),
                    'id': '<?php echo $suite->id ?>',
                    '_wpnonce': '<?php echo wp_create_nonce('get-brother-suites')?>'
                },
                type: 'POST',
                complete: function(){
                    jQuery('#community-box .loading1, #related-suites-box .loading1').hide();    
                },
                success: function(rsp)
                {
                    jQuery('#related-suites-box select').replaceWith(rsp);
                }
            })
        })
        
        jQuery('#suiteForm').submit(function(){
            if(jQuery('#ts_name').val() == '')
            {
                jQuery('#suite-info-box').find('.message').remove();
                jQuery('#suite-info-box .column').prepend('<div class="message error">Test Suite name should not be empty!</div>');
                jQuery('#ts_name').focus();
                return false;
            }
            jQuery('#brother-suites').remove();
        })
        
        jQuery('#community-box .grid-box-body, #suite-type-box .grid-box-body').height(Math.max(jQuery('#community-box .grid-box-body').height(), jQuery('#suite-type-box .grid-box-body').height()));
    })
</script>
<?php
get_footer();
?>
