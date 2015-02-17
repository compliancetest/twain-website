<?php
/**
* Download Files Page
*/

$is_group_admin = groups_is_user_admin(get_current_user_id(), bp_get_group_id());

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
        ),
        'orderby' => 'title',
        'order' => 'ASC'
    );
$testsuites = get_posts( $args );
?>
<div id="testdata-container" class="tab-content white_bcg padding10">    
    <!-- Files List Page -->
    <div id="testdata-lists">
        <div class="grid-list">
            <div class="grid-list-row grid-list-header">
                <div class="grid-list-cell width40P">Profile Name</div>
                <div class="grid-list-cell width10P">Profile Purpose</div>
                <div class="grid-list-cell width15P tocenter">Profile Type</div>                
                <div class="grid-list-cell width15P tocenter">Created Date</div>
                <div class="grid-list-cell width10P tocenter">Valid?</div>
                <div class="grid-list-cell width10P tocenter">Action</div>                
                <div class="clear"></div>
            </div>                          
            <?php                    
            
                $instances = getCommunityProfileInstatnces(bp_get_group_id());
                foreach( $instances AS $instance ){
            ?>
            <div class="grid-list-row" id="instanceRow<?php echo $file->id?>">
                <div class="grid-list-cell width40P">
                    <a href="<?php echo get_site_url()?>?td-action=<?php echo wp_create_nonce('view-profile-instance')?>&id=<?php echo $instance->id?>" class="view-profile-instance-link"><?php echo $instance->profile_name; ?></a>
                    <br />
                    <p><?php echo $instance->profile_description; ?></p>
                </div>
                <div class="grid-list-cell width10P">
                    <?php echo $instance->purpose; ?>
                </div>
                <div class="grid-list-cell width15P tocenter">
                    <a href="<?php echo get_site_url()?>?td-action=<?php echo wp_create_nonce('view-profile-type')?>&id=<?php echo $instance->type_id?>" rel="custom-popup" cp-type="ajax" class="view-profile-type-link"><?php echo $instance->type_name; ?></a>
                </div>
                <div class="grid-list-cell width15P tocenter">
                    <?php echo formatDate($instance->created_date) ?>                    
                </div>
                <div class="grid-list-cell width10P tocenter">
                    <?php if( $instance->validation_status == 'valid' ):?>
                        <span class="profile-valid"></span>
                    <?php elseif( $instance->validation_status == 'invalid' ):?>
                        <?php
                            $link = empty( $instance->validation_url ) ?  '#' : $instance->validation_url;
                        ?>
                        <a href="<?php echo $link;?>" class="profile-invalid" target="_blank"></a>
                    <?php else:?>
                        <span class="profile-pending"></span>
                    <?php endif;?>
                </div>
                <div class="grid-list-cell width10P tocenter">
                    <?php
                        if(bp_is_group_admin(get_current_user_id()))
                        {
                    ?>
                    <a href="#edit-profile-box" data-id="<?php echo $instance->id?>" data-type-id="<?php echo $instance->type_id?>" class="edit-profile-instance-link action-btn icon-btn blue-edit-btn"><span class="p"></span><span class="simple_tooltip radius6 no-wrap">Edit Profile<span></span></span></a>
                    <a href="<?php bp_group_permalink()?>testdata?td-action=<?php echo wp_create_nonce('delete-harness-instance')?>&id=<?php echo $instance->id?>" class="action-btn icon-btn delete-btn left5 delete-profile-btn"><span class="p"></span><span class="simple_tooltip radius6 no-wrap">Delete Profile<span></span></span></a>
                    <?php
                        }
                    ?>
                    <?php if (count($testsuites) > 0): ?>
                    <a href="<?php bp_group_permalink()?>testdata?td-action=<?php echo wp_create_nonce('copy-harness-instance')?>&id=<?php echo $instance->id?>" class="action-btn icon-btn blue-btn copy-btn left5"><span class="p"></span><span class="simple_tooltip radius6 no-wrap">Copy Profile<span></span></span></a>
                    <?php endif; ?>
                </div>
                <div class="clear"></div>
            </div>                                
        <?php
            }
            if(!$instances)
            {
        ?>
            <div class="grid-list-row">
                <div class="grid-list-cell tocenter width100P">
                    <?php if (count($testsuites) > 0): ?>
                        No instance created yet
                    <?php else: ?>
                        <?php 
                            if (is_user_logged_in()) {
                                echo MESSAGE_WARNING_ANONYMOUS;
                            } else {
                                echo MESSAGE_WARNING_COMMUNITY_MEMBER;
                            }
                        ?>
                    <?php endif; ?>
                </div>
                <div class="clear"></div>
            </div>
        <?php
            }
        ?>
        
            <div class="grid-list-footer grid-list-row">                    
                <div class="grid-list-cell width100P">                    
                    <?php if($is_group_admin && $instances):  ?>
                        <a href="#edit-profile-box" id="add-new-test-data-link" class="action-btn add-new-btn"><span class="p"></span><span class="t">Add New Test Data</span></a>
                    <?php endif; ?>
                </div>
                <div class="clear"></div>
            </div>
        
         </div> 
    </div>
</div>
<input type="hidden" id="sqs_validation_status" value="<?php echo get_option('validate_via_sqs');?>">
<div class="popup-box" id="delete-profile-box" style="display: none; width: 500px">
    <div class="popup-box-header radius6 noradiusbottom">Confirm Deletion</div>
    <div class="popup-box-content"> 
        Are you sure that you want to delete this profile?
    </div>
    <div class="popup-box-footer radius6 noradiustop">                   
        <div class="loading loading-with-text radius6"><div><b>DELETING PROFILE</b><span>Please wait...</span></div></div> 
        <a href="#" class="action-btn process-btn"><span class="p"></span><span class="t">Confirm</span></a>            
        <a href="#" class="action-btn cancel-btn close-popup-btn"><span class="p"></span><span class="t">Cancel</span></a>            
        <div class="clear"></div>
    </div>
    <a class="close_btn"></a>                
</div>
<?php if($is_group_admin){ ?>
<?php 
    $profileTypes = getCommunityProfileTypes(bp_get_group_id());    
    $lastTypeID = getUserLastUsedProfileType('harness', bp_get_group_id());
    
    $lastType = null;
    
?>
<div class="popup-box" id="edit-profile-box" style="display: none; width: 500px;">
    <form name="editProfileForm" id="editProfileForm" action="">
        <div class="popup-box-header radius6 noradiusbottom">Create Profile Instance</div>        
        <div class="popup-box-top-nav">            
            <div class="btn-row">      
                <h5 class="left nomarginbottom lineheight22px">Please Select Profile Type</h5>          
                <a href="#" class="action-btn cancel-btn right close-popup-btn"><span class="p"></span><span class="t">Cancel</span></a>            
                <a href="#" class="action-btn process-btn right submit-btn"><span class="p"></span><span class="t">SAVE</span></a>            
                <div class="clear"></div>
            </div>
        </div>
        <div class="popup-box-content grid-box-body noshadow padding20-10">                    
            <div class="edit-profile-content-inner">                
                <select class="select left" name="profile-type-id" id="profile-type-id">
                    <?php foreach($profileTypes as $p){ ?>
                    <?php 
                        if(!$lastType && !$lastTypeID)
                        {
                            $lastType = $p;
                        }else if(!$lastType && $p->id == $lastTypeID){
                            $lastType = $p;
                        }                                  
                    ?>
                    <option value="<?php echo $p->id?>" <?php echo $lastType && $lastType->id == $p->id ? "selected='selected'" : "" ?>>
                        <?php 
                            echo $p->title;
                            $pJSON = json_decode(base64_decode($p->schema));
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
                    </option>
                    <?php } ?>
                </select>
                <div class="clear"></div>            
                <div id="edit_profile_instance_panel">
                    <div id="upload_profile_panel">                    
                        <div class="field-row">
                            <label class="padding5-10-5-0">Upload Json file</label> 
                            <div class="grid-cell relative">                                                                    
                                <input type="file" name="profile_instance_file" class="input-file" id="profile_instance_file" file-type="doc" file-extensions="(.txt or .json file)" />                                
                                <a href="#" class="action-btn upload-btn plus" id="profile_instance_upload_btn"><span class="p"></span><span class="t">Upload</span></a>
                                <div class="clear"></div>
                            </div>
                            <div class="clear"></div>
                        </div>
                    </div>
                    <div class="enter-values"><span>or</span></div>
                    <label class="padding5-10-5-0">Enter Values</label> 
                    <div id="create_profile_panel">                
                        <div class="clear"></div>
                    </div>
                    <textarea id="profile_type_txt" class="displaynone"><?php echo $lastType ? base64_decode($lastType->schema) : ''?></textarea>                
                    <textarea id="profile_instance_txt" class="displaynone"></textarea>                
                </div>
            </div>
        </div>
        <div class="popup-box-footer radius6 noradiustop">                            
            <div class="btn-row">
                <a href="#" class="action-btn cancel-btn right close-popup-btn"><span class="p"></span><span class="t">Cancel</span></a>            
                <a href="#" class="action-btn process-btn right submit-btn"><span class="p"></span><span class="t">SAVE</span></a>                            
                <div class="clear"></div>
            </div>
        </div>                        
        <a class="close_btn"></a>                        
        <div class="loading loading-with-text radius6"><div><b>LOADING DATA</b><p>Please wait...</p></div></div>
        <input type="hidden" name="instance-id" id="instance-id" value="" />
        <input type="hidden" id="save-instance-action" value="<?php echo wp_create_nonce('save-harness-instance')?>" />
        <input type="hidden" id="get-profile-ui-action" value="<?php echo wp_create_nonce('get-harness-profile-ui')?>" />
    </form>
    <form name="editProfileErrorForm" id="editProfileErrorForm" action="" method="post" style="display: none;">
        <div class="popup-box-header radius6 noradiusbottom">Validation Error(s)</div>        
        <div class="popup-box-top-nav">            
            <div class="btn-row">      
                <h5 class="left nomarginbottom lineheight22px">The profile instance does not match the schema for the selected profile type. Please correct the errors below and try again.</h5>          
                <div class="clear"></div>
            </div>
        </div>
        <div class="popup-box-content grid-box-body noshadow">
            <div id="profile-error-content"></div>
        </div>
        <div class="popup-box-footer radius6 noradiustop">                            
            <div class="btn-row">
                <a href="#" class="action-btn cancel-btn right" id="close-error"><span class="p"></span><span class="t">Close</span></a>
                <a href="#" class="action-btn blue-btn copy-btn right" id="copy-error"><span class="p"></span><span class="t">Copy</span></a>
                <a href="#" class="action-btn download-btn right" id="download-error"><span class="p"></span><span class="t">Download</span></a>
                <div class="clear"></div>
            </div>
        </div>                        
        <a class="close_btn"></a>                        
        <div class="loading loading-with-text radius6"><div><b>LOADING DATA</b><p>Please wait...</p></div></div>
        <textarea name="profile-errors" id="profile-errors" style="display: none;"></textarea>
        <input type="hidden" name="profile-name" id="profile-name" value="" />
        <input type="hidden" name="td-action" id="download-error-action" value="<?php echo wp_create_nonce('download-profile-error')?>" />
    </form>
</div>
<?php } ?>