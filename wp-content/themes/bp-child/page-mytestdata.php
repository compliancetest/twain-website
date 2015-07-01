<?php
/*
 * Template Name: My Test Data
 */


if (!is_user_logged_in()) {
    wp_redirect(home_url());
    exit;
}
get_header();
$profileInstances = getCustomerProfileInstances(null, true);
$subscriptions =  getUserSubscriptions(null, true);
$sqs_validation_enabled = get_option('validate_via_sqs') == 'yes' ? true : false;
$filters = getCustomerProfileInstancesFilters($profileInstances);
?>
<div class="content" id="my_testdata">
    <div class="dashboard-tabs">
        <?php get_sidebar('dashboard'); ?>
    </div>
    <div class="container">
       <div class="column">
           <form name="form_filter" id="form_filter" action="/my-test-data/" method="get">
               <div class="search-results-filter">
                   <h4 class="filter-head">Filter By:</h4>
                   <div class="search-filters-box">
                       <ul class="search-filters-list clearfix">
                           <li class="first">
                               <label for="content-type-filter">Tag</label>
                               <select name="tag" id="content-type-filter" class="select">
                                   <option>All</option>
                                   <?php if( is_array( $filters['tags'] ) ):?>
                                       <?php foreach ( $filters['tags'] AS $k => $v ): ?>
                                           <option value="<?php echo $k;?>" <?php if( isset( $_GET['tag'] ) && $_GET['tag'] == $k ):?> selected="selected" <?php endif;?>><?php echo $v;?></option>
                                       <?php endforeach; ?>
                                   <?php endif;?>
                               </select>
                           </li>
                           <li>
                               <label for="community-filter">Type</label>
                               <select name="type" id="community-filter" class="select">
                                   <option>All</option>
                                   <?php if (is_array($filters['type'])) :?>
                                        <?php foreach ($filters['type'] as $v) : ?>
                                           <option value="<?php echo $v;?>" <?php if (isset($_GET['type']) && $_GET['type'] == $v ):?> selected="selected" <?php endif;?>>
                                                <?php echo $v;?>
                                           </option>
                                        <?php endforeach; ?>
                                    <?php endif;?>
                               </select>
                           </li>
                           <li>
                               <label for="community-filter">Validity</label>
                               <select name="validity" id="community-filter" class="select">
                                   <option>All</option>
                                   <?php if( is_array( $filters['validity'] ) ):?>
                                       <?php foreach ($filters['validity'] AS $v): ?>
                                           <option value="<?php echo $v;?>" <?php if( isset( $_GET['validity'] ) && $_GET['validity'] == $v ):?> selected="selected" <?php endif;?>><?php echo ucfirst( $v );?></option>
                                       <?php endforeach; ?>
                                   <?php endif;?>
                               </select>
                           </li>
                       </ul>
                       <div class="search-filter-actions">
                           <a class="action-btn process-btn submit-btn" href="#"><span class="p"></span><span class="t">Confirm</span></a>
                           <a href="<?php echo get_permalink(); ?>" class="action-btn clear-btn" id="clear-search-filter-btn"><span class="p"></span><span class="t">Clear</span></a>
                       </div>
                   </div>
               </div>
           </form>
       </div>
        <div class="column">
            <a href="#" id="delete-profile-link" class="action-btn delete-btn icon-btn right left5 has-tooltip"><span class="p"></span><span class="simple_tooltip radius6">Delete Selected Rows<span></span></span></a>
            <a href="#" class="action-btn view-log-btn icon-btn right left5 has-tooltip"><span class="p"></span><span class="simple_tooltip radius6">Include/exclude multiple profiles<span></span></span></a>
            <div class="clear"></div>
            <div class="space10"></div>
            <div class="grid-box table-box" id="my_test_data_profiles">
                <div class="grid-box-body">
                   <div class="thead tr">
                        <div class="td td-chk tocenter"><input type="checkbox" id="chk-profile-all" autocomplete="off" /></div>
                        <div class="td td-profile-name">Profile Name</div>
                        <div class="td td-profile-type">Type</div>
                        <div class="td td-profile-status">Valid?</div>
                        <div class="td td-profile-lookup">Include In Lookup</div>
                        <div class="td td-action">Action</div>
                        <div class="clear"></div>
                   </div>
                   <div class="tbody">
                   <?php
                   if(!$profileInstances){
                       ?>
                       <div class="tr">
                           <div class="td td-full">You have currently not created any data profiles.</div>
                           <div class="clear"></div>
                       </div>
                       <?php
                   }else{
                       foreach($profileInstances as $instance)
                       {
                           $tags = Tag::getItemTags( $instance->id );
                   ?>
                        <div class="tr">
                           <div class="td td-chk tocenter"><input type="checkbox" name="id[]" id="id<?php echo  $instance->id?>" value="<?php echo $instance->id?>" /></div>
                           <div class="td td-profile-name">
                               <a href="<?php echo get_site_url()?>?td-action=<?php echo wp_create_nonce('view-profile-instance')?>&id=<?php echo $instance->id?>" class="view-profile-instance-link" ><?php echo $instance->profile_name; ?></a>
                               <br />
                               <b>Purpose: </b> <?php echo $instance->purpose; ?>
                               <br />
                               <?php if( $tags ):?>
                                   <b>Tags: </b>
                                   <?php
                                   $t = array();
                                   foreach( $tags AS $tag ){
                                       $t[] = '<a href="/my-test-data/?tag='.$tag->id.'">'.$tag->name.'</a>';
                                   }
                                   echo implode( ', ', $t );
                                   ?>
                               <?php endif;?>
                               <p><?php echo $instance->profile_description; ?></p>
                           </div>
                           <div class="td td-profile-type">
                               <a href="<?php echo get_site_url()?>?td-action=<?php echo wp_create_nonce('view-profile-type')?>&id=<?php echo $instance->type_id?>" rel="custom-popup" cp-type="ajax" class="view-profile-type-link"><?php echo $instance->type_name; ?></a>
                           </div>
                            <div class="td td-profile-status">
                                <?php if( $instance->validation_status == 'valid' ):?>
                                    <span class="profile-valid"></span>
                                <?php elseif( $instance->validation_status == 'invalid' ):?>
                                <?php
                                    $link = empty( $instance->validation_url ) ?  '#' : $instance->validation_url;
                                ?>
                                    <a href="<?php echo S3Wrapper::getUrlWithDomain( $link );?>" class="profile-invalid" target="_blank"></a>
                                <?php else:?>
                                    <span class="profile-pending"></span>
                                <?php endif;?>
                            </div>
                           <div class="td td-profile-lookup">
                                <input type="checkbox" name="lookup" value="<?php echo $instance->id; ?>" <?php echo ($instance->lookup)?('checked'):(''); ?>>
                           </div>

                           <div class="td td-action">
                                <?php
                                    if($instance->creator_id == get_current_user_id())
                                    {
                                ?>
                                <?php if(count($subscriptions) > 0): ?>
                                    <?php if( ! ProfileInstance::isRun( $instance->profile_role ) ):?>
                                        <a href="#edit-profile-box" data-id="<?php echo $instance->id?>" data-type-id="<?php echo $instance->type_id?>" data-type="upload" class="edit-profile-instance-link action-btn icon-btn upload-btn has-tooltip"><span class="p"></span><span class="simple_tooltip radius6">Upload Profile<span></span></span></a>
                                    <?php else: ?>
                                        <a href="javascript:void(0);" class="action-btn icon-btn upload-btn has-tooltip greyed-out-btn"><span class="p"></span><span class="simple_tooltip radius6">Upload Profile<span></span></span></a>
                                    <?php endif;?>
                                    <a href="<?php echo S3Wrapper::getProfileLink( $instance->token, $instance->profile_name, true );?>" class="left10 action-btn icon-btn download-btn has-tooltip"><span class="p"></span><span class="simple_tooltip radius6">Download Profile<span></span></span></a>
                                    <?php if( ProfileInstance::isSchedule( $instance->profile_role ) && $instance->validation_status == 'valid' ):?>
                                            <a href="/my-profile?td-action=<?php echo wp_create_nonce('prepare_schedule')?>&id=<?php echo $instance->id?>" rel="custom-popup" cp-type="ajax" cp-removeBoxAfterClose=1 class="action-btn icon-btn blue-btn expand-btn left10 has-tooltip prepare_schedule trigger-btn"><span class="p"></span><span class="simple_tooltip radius6" style="width: 150px; left: -24px;">Create a Run profile from this Schedule<span></span></span></a>
                                    <?php elseif( ProfileInstance::isRun( $instance->profile_role ) && ProfileInstance::isRunCanBeTriggered( $instance->token ) ):?>
                                        <a href="<?php echo the_permalink() ?>?td-action=<?php echo wp_create_nonce('trigger_run')?>&id=<?php echo $instance->id?>" rel="custom-popup" cp-type="ajax" cp-removeBoxAfterClose=1 class="action-btn icon-btn blue-btn expand-btn left10 has-tooltip trigger-btn"><span class="p"></span><span class="simple_tooltip radius6" style="width: 150px; left: -24px;">Trigger Run<span></span></span></a>
                                    <?php elseif( $sqs_validation_enabled && $instance->is_expanded == 0 && $instance->validation_status == 'valid' && in_array( str_replace( ' ', '', $instance->profile_role ), ProfileType::getExpandableTypes() ) ): ?>
                                        <a href="#create-expanded-version" data-id="<?php echo $instance->id;?>" class="action-btn icon-btn blue-btn expand-btn left10 has-tooltip create_expanded_version"><span class="p"></span><span class="simple_tooltip radius6">Create Expanded Version of this profile<span></span></span></a>
                                    <?php else: ?>
                                            <?php if( ProfileInstance::isRun( $instance->profile_role ) ):?>
                                                <a href="javascript:void(0);" class="action-btn icon-btn blue-btn expand-btn left10 has-tooltip trigger-btn greyed-out-btn"><span class="p"></span><span class="simple_tooltip radius6" style="width: 150px; left: -24px;">Trigger Run<span></span></span></a>
                                            <?php elseif( ProfileInstance::isSchedule( $instance->profile_role ) ):?>
                                                <a href="javascript:void(0);" class="action-btn icon-btn blue-btn expand-btn left10 has-tooltip trigger-btn greyed-out-btn"><span class="p"></span><span class="simple_tooltip radius6" style="width: 150px; left: -24px;">Create a Run profile from this Schedule<span></span></span></a>
                                            <?php else:?>
                                                <a href="javascript:void(0);" class="action-btn icon-btn blue-btn expand-btn left10 has-tooltip greyed-out-btn"><span class="p"></span><span class="simple_tooltip radius6">Create Expanded Version of this profile<span></span></span></a>
                                            <?php endif;?>
                                    <?php endif; ?>
                                    <div class="clear space5"></div>
                                    <?php if( ! ProfileInstance::isRun( $instance->profile_role ) ):?>
                                        <a href="#edit-profile-box" data-id="<?php echo $instance->id?>" data-type-id="<?php echo $instance->type_id?>" class="edit-profile-instance-link action-btn icon-btn edit-btn has-tooltip"><span class="p"></span><span class="simple_tooltip radius6">Edit Profile<span></span></span></a>
                                    <?php else: ?>
                                        <a href="javascript:void(0);" class="action-btn icon-btn edit-btn has-tooltip greyed-out-btn"><span class="p"></span><span class="simple_tooltip radius6">Edit Profile<span></span></span></a>
                                    <?php endif; ?>
                                    <a href="<?php echo get_site_url()?>?td-action=<?php echo wp_create_nonce('edit-tags')?>&id=<?php echo $instance->id?>" rel="custom-popup" cp-type="ajax" data-id="<?php echo $instance->id?>" data-type-id="<?php echo $instance->type_id?>" class="left10 action-btn icon-btn tags-btn has-tooltip"><span class="p"></span><span class="simple_tooltip radius6">Edit Tags<span></span></span></a>
                                <?php endif; ?>
                                <?php if( ProfileInstance::canBeDeleted( $instance ) ):?>
                                    <a href="<?php echo get_site_url()?>/my-profile?td-action=<?php echo wp_create_nonce('delete-profile-instance')?>&id=<?php echo $instance->id?>&return=<?php echo base64_encode(get_site_url() . "/my-test-data")?>" class="action-btn icon-btn delete-btn left10 has-tooltip delete-profile-btn"><span class="p"></span><span class="simple_tooltip radius6">Delete Profile<span></span></span></a>
                                <?php else: ?>
                                        <a href="javascript:void(0);" class="action-btn icon-btn delete-btn left10 has-tooltip greyed-out-btn"><span class="p"></span><span class="simple_tooltip radius6">Delete Profile<span></span></span></a>
                                <?php endif;?>
                                <?php
                                    }
                                ?>
                           </div>
                           <div class="clear"></div>
                           
                        </div>
                        
                   <?php
                       }
                   ?>
                   <?php
                   }
                   ?>
                   <div class="loading1"></div>
                   </div>
                   
                </div>             
            </div>
            <div class="space10"></div>
            <input type="hidden" id="update-lookup-action" value="<?php echo wp_create_nonce('update-profile-lookup')?>">
            <?php if(count($subscriptions) > 0) { ?>                
                <a class="action-btn add-new-btn has-tooltip" id="add-new-test-data-link" href="#edit-profile-box">
            <?php } else { ?>
                <a class="action-btn add-new-btn has-tooltip" href="#need-subscription-box" rel="custom-popup" cp-type="inline" >
            <?php } ?>
                <span class="p"></span>
                <span class="t">Add</span>
                <span class="simple_tooltip radius6">Add Test Data<span></span></span>
            </a>
            <div class="space20"></div>
        </div>
        <div class="clear"></div>
    </div>
    <div class="clear"></div>
</div> <!--end content-->
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
<div class="popup-box" id="delete-profiles-box" style="display: none; width: 500px">
    <div class="popup-box-header radius6 noradiusbottom">Confirm Deletion</div>
    <div class="popup-box-content">
        <div class="no_rows display_none">Please select a row.</div>
        <div class="selected_rows display_none">Are you sure that you want to delete this profile(s)?</div>
    </div>
    <div class="popup-box-footer radius6 noradiustop">
        <div class="loading loading-with-text radius6 dlt_loading"><div><b>DELETING PROFILE</b><span>Please wait...</span></div></div>
        <a href="#" class="action-btn process-btn dlt_confirm"><span class="p"></span><span class="t">Confirm</span></a>
        <a href="#" class="action-btn cancel-btn close-popup-btn"><span class="p"></span><span class="t">Cancel</span></a>
        <div class="clear"></div>
    </div>
    <a class="close_btn"></a>
</div>
<div class="popup-box" id="change-vis-profiles-box" style="display: none; width: 500px">
    <div class="popup-box-header radius6 noradiusbottom">Confirm include/exclude multiple profiles</div>
    <div class="popup-box-content">
        <div class="no_vis_rows display_none">Please select a row.</div>
        <div class="selected_vis_rows display_none">Are you sure that you want to <span class="vis_action"></span> this profile(s)?</div>
    </div>
    <div class="popup-box-footer radius6 noradiustop">
        <div class="loading loading-with-text radius6 change_vis_loading"><div><b>PROCESSING...</b><span>Please wait...</span></div></div>
        <a href="#" id="chng_confirm" class="action-btn process-btn"><span class="p"></span><span class="t">Confirm</span></a>
        <a href="#" class="action-btn cancel-btn close-popup-btn"><span class="p"></span><span class="t">Cancel</span></a>
        <div class="clear"></div>
    </div>
    <a class="close_btn"></a>
</div>
<div class="popup-box" id="create-expanded-version" style="display: none; width: 300px">
    <div class="popup-box-header radius6 noradiusbottom">Create Expanded Version</div>
    <div class="popup-box-content">
        Factor: <input type="text" name="factor" id="factor" maxlength="6" style="width: 70px;">
        <input type="hidden" name="profile_id" id="profile_id" value="">
        <div class="message error factor_error">This field is required.</div>
        <div class="message error factor_valid_error">Please provide valid number between <?php echo get_option( 'min_expansion_factor');?> and <?php echo get_option( 'max_expansion_factor');?>.</div>
    </div>
    <div class="popup-box-footer radius6 noradiustop">
        <div class="loading loading-with-text radius6"><div><b>CREATING PROFILE</b><span>Please wait...</span></div></div>
        <a href="#" class="action-btn process-btn confirm-expanded-profile"><span class="p"></span><span class="t">Confirm</span></a>
        <a href="#" class="action-btn cancel-btn close-popup-btn"><span class="p"></span><span class="t">Cancel</span></a>
        <div class="clear"></div>
    </div>
    <a class="close_btn"></a>
</div>
<?php
if(count($subscriptions) > 0){
?>

<?php 
    $profileTypes = getCustomerProfileTypes(get_current_user_id());
    
    $lastTypeID = getUserLastUsedProfileType('tester');
    
    $lastType = null;
?>

<div class="popup-box" id="edit-profile-box" style="display: none; width: 500px;">
    <form name="editProfileForm" id="editProfileForm" action="">
        <div class="popup-box-header radius6 noradiusbottom">Create Profile Instance</div>        
        <div class="popup-box-top-nav">            
            <div class="btn-row">      
                <h5 class="left nomarginbottom lineheight22px">Please Select Profile Type</h5>          
                <div class="clear"></div>
            </div>
        </div>
        <div class="popup-box-content grid-box-body noshadow">                    
            <div class="edit-profile-content-inner">
                <select class="select left" name="profile-type-id" id="profile-type-id">
<!--                    <option value="">- Select -</option>-->
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
                            <label class="padding5-10-5-0"> Upload Json file</label> 
                            <div class="grid-cell relative">                                                                    
                                <input type="file" name="profile_instance_file" class="input-file" id="profile_instance_file" file-type="doc" file-extensions="(.txt or .json file)" />                                
                                <a href="#" class="action-btn upload-btn plus" id="profile_instance_upload_btn"><span class="p"></span><span class="t">Upload</span></a>
                                <div class="clear"></div>
                            </div>
                            <div class="clear"></div>
                        </div>
                    </div>
                    <div class="enter-values hide_on_upload"><span>or</span></div>
                    <label class="padding5-10-5-0 hide_on_upload">Enter Values</label>
                    <div id="create_profile_panel">                
                        <div class="clear"></div>
                    </div>
                    <textarea id="profile_type_txt" class="displaynone"><?php echo $lastType ? base64_decode($lastType->schema) : ''?></textarea>                
                    <textarea id="profile_instance_txt" class="displaynone"></textarea>
                    <div class="popup-box-footer radius6 noradiustop">
                        <div class="btn-row">
                            <a href="#" class="action-btn process-btn submit-btn "><span class="p"></span><span class="t">Confirm</span></a>
                            <a href="#" class="action-btn cancel-btn close-popup-btn"><span class="p"></span><span class="t">Cancel</span></a>
                            <div class="clear"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <a class="close_btn"></a>
        <div class="loading loading-with-text radius6"><div><b>LOADING DATA</b><p>Please wait...</p></div></div>
        <input type="hidden" name="instance-id" id="instance-id" value="" />
        <input type="hidden" id="is_upload" value="" />
        <input type="hidden" id="save-instance-action" value="<?php echo wp_create_nonce('save-tester-instance')?>" />
        <input type="hidden" id="get-profile-ui-action" value="<?php echo wp_create_nonce('get-tester-profile-ui')?>" />
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
<?php
}else{ 
?>
<div class="popup-box" id="need-subscription-box" style="display: none; width: 500px;">
    <div class="popup-box-header radius6 noradiusbottom">Need a subscription</div>        
    <div class="popup-box-content grid-box-body">                    
        <p class="message notice"><?php echo MESSAGE_WARNING_COMMUNITY_MEMBER; ?></p>
    </div>
    <div class="popup-box-footer radius6 noradiustop">                                    
        <div class="clear"></div>
    </div>                        
    <a class="close_btn"></a>                        
</div>
<?php
}
?>
<input type="hidden" id="sqs_validation_status" value="<?php echo get_option('validate_via_sqs');?>">
<script type="text/javascript">
    jQuery(document).ready(function($){
        fixTdHeight(jQuery('#my_test_data_profiles'));

        $('.create_expanded_version').each(function(){
            $(this).cplightbox({
                type: 'inline',
                href: '#create-expanded-version'
            })
        })
        $('.create_expanded_version').on('click', function(){
            $('.factor_error, .factor_valid_error').hide();
            $('#factor').removeClass( 'input-error' );
            $('#profile_id').val( $(this).attr('data-id') );

        })
        $( '.confirm-expanded-profile').on('click', function(){
            $('.factor_error, .factor_valid_error').hide();
            var is_valid = true;
            var factor_value = $.trim( $('#factor').val() );
            if( factor_value == '' ) {
                is_valid = false;
                $('#factor').addClass( 'input-error' );
                $('.factor_error').show();
            }
            if( is_valid && ( parseInt( factor_value ) != factor_value || ( factor_value < <?php echo get_option( 'min_expansion_factor');?> || factor_value > <?php echo get_option( 'max_expansion_factor');?> ) ) ){
                is_valid = false;
                $('#factor').addClass( 'input-error' );
                $('.factor_valid_error').show();
            }
            if( is_valid ){
                $('.factor_error').hide();
                $('#factor').removeClass( 'input-error' );
                $('#create-expanded-version .loading').show();
                $.ajax({
                    url: '/my-profile',
                    data: {
                        'td-action': '<?php echo wp_create_nonce('create-expanded-version')?>',
                        'id': $('#profile_id').val(),
                        'factor': $('#factor').val()
                    },
                    type: 'post',
                    success: function (rsp) {
                        document.location.reload();
                    }
                });
            }
        });
        //Fix Simple ToolTips
        jQuery('.td-status .simple_tooltip').each(function(){
            jQuery(this).css({'top': -1 * jQuery(this).outerHeight() - 6, 'margin-left': -1 * jQuery(this).outerWidth() / 2 + jQuery(this).parent().outerWidth() / 2});
        });

        jQuery('#chk-profile-all').click(function(){
            jQuery('#my_test_data_profiles .tbody .td-chk input[type="checkbox"]').prop('checked', this.checked);
        });
        $('#delete-profile-link').cplightbox({
            type: 'inline',
            href: '#delete-profiles-box'
        });
        $('.view-log-btn').cplightbox({
            type: 'inline',
            href: '#change-vis-profiles-box'
        });
        $('.view-log-btn').on('click',function(){
            if( jQuery('#my_test_data_profiles .tbody .td-chk input[type="checkbox"]:checked').length == 0 ){
                $('.no_vis_rows').show();
                $('.selected_vis_rows, #chng_confirm').hide();
                $('#chng_confirm').off('click');
            } else{
                var vis_action = jQuery('#my_test_data_profiles .tbody .td-chk input[type="checkbox"]:checked:first').closest('div.tr').find('input[name="lookup"]').is(':checked');
                var action_name = vis_action ? 'exclude' : 'include';
                $('.vis_action').text( action_name );
                $('.no_vis_rows').hide();
                $('.selected_vis_rows, #chng_confirm').show();
                $('#chng_confirm').on('click', function(){
                    jQuery('.change_vis_loading').show();
                    var ids = new Array();
                    jQuery('#my_test_data_profiles .tbody .td-chk input[type="checkbox"]:checked').each(function(){
                        ids.push(this.value);
                    });
                    jQuery.ajax({
                        url: "/?td-action="  + jQuery('#update-lookup-action').val(),
                        data: { 'id' : ids, 'status' : vis_action ? 0 : 1 },
                        type: 'post',
                        dataType: 'json',
                        complete: function() {
                            jQuery('#my_test_data_profiles .tbody .td-chk input[type="checkbox"]:checked').each(function(){
                                $(this).closest('div.tr').find('input[name="lookup"]').attr('checked', ! vis_action );
                                jQuery('.change_vis_loading').hide();
                                $('#change-vis-profiles-box').find('a.close_btn').click();
                            });
                        }
                    });
                });
            }
        });
        $('#delete-profile-link').on('click',function(){
            if( jQuery('#my_test_data_profiles .tbody .td-chk input[type="checkbox"]:checked').length == 0 ){
                $('.no_rows').show();
                $('.selected_rows, .dlt_confirm').hide();
                $('.dlt_confirm').off('click');
            } else{
                $('.no_rows').hide();
                $('.selected_rows, .dlt_confirm').show();
                $('.dlt_confirm').on('click', function(){
                    jQuery('.dlt_loading').show();
                    var ids = new Array();
                    jQuery('#my_test_data_profiles .tbody .td-chk input[type="checkbox"]:checked').each(function(){
                        ids.push(this.value);
                    });
                    jQuery.ajax({
                        url: '/my-profile',
                        data: {
                            'td-action': '<?php echo wp_create_nonce('delete-profile-instance')?>',
                            'id': ids,
                            'return': '<?php echo base64_encode(get_site_url() . '/my-test-data')?>'
                        },
                        type: 'post',
                        dataType: 'html',
                        success: function(rsp){
                            jQuery('#my_test_data_profiles .tbody .td-chk input[type="checkbox"]:checked').each(function(){
                                $(this).attr('checked', false);
                            });
                            document.location.reload();
                        }
                    })
                });
            }
        });
    });
</script>
<?php get_footer(); ?>
