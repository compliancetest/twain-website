<?php
/*
 * Template Name: My Test Data
 */


if(!is_user_logged_in()){
    wp_redirect(home_url());
    exit;
}
get_header();
$profileInstances = getCustomerProfileInstances();
$subscriptions =  getUserSubscriptions(null, true);
$sqs_validation_enabled = get_option( 'validate_via_sqs' ) == 'yes' ? true : false;
$expandable_profile_types = ProfileType::getExpandableTypes();
?>
<div class="content" id="my_testdata">
    <div class="dashboard-tabs">
        <?php get_sidebar('dashboard'); ?>
    </div>
    <div class="container">
        <div class="column">
            <a href="#" id="delete-profile-link" class="action-btn delete-btn icon-btn right left5 has-tooltip"><span class="p"></span><span class="simple_tooltip radius6">Delete Selected Rows<span></span></span></a>
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
                   ?>
                        <div class="tr">
                           <div class="td td-chk tocenter"><input type="checkbox" name="id[]" id="id<?php echo  $instance->id?>" value="<?php echo $instance->id?>" /></div>
                           <div class="td td-profile-name">
                               <a href="<?php echo get_site_url()?>?td-action=<?php echo wp_create_nonce('view-profile-instance')?>&id=<?php echo $instance->id?>" class="view-profile-instance-link" ><?php echo $instance->profile_name; ?></a>
                               <br />
                               <b>Purpose: </b> <?php echo $instance->purpose; ?>
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
                                    <a href="#edit-profile-box" data-id="<?php echo $instance->id?>" data-type-id="<?php echo $instance->type_id?>" data-type="upload" class="edit-profile-instance-link action-btn icon-btn upload-btn has-tooltip"><span class="p"></span><span class="simple_tooltip radius6">Upload Profile<span></span></span></a>
                                    <a href="<?php echo S3Wrapper::getProfileLink( $instance->token, $instance->profile_name, true );?>" class="left10 action-btn icon-btn download-btn has-tooltip"><span class="p"></span><span class="simple_tooltip radius6">Download Profile<span></span></span></a>
                                    <?php if( $sqs_validation_enabled && $instance->is_expanded == 0 && $instance->validation_status == 'valid' && in_array( str_replace( ' ', '', $instance->profile_role ), $expandable_profile_types ) ): ?>
                                        <a href="#create-expanded-version" data-id="<?php echo $instance->id;?>" class="action-btn icon-btn blue-btn expand-btn left10 has-tooltip create_expanded_version"><span class="p"></span><span class="simple_tooltip radius6">Create Expanded Version of this profile<span></span></span></a>
                                    <?php endif; ?>
                                    <a href="#edit-profile-box" data-id="<?php echo $instance->id?>" data-type-id="<?php echo $instance->type_id?>" class="left10 edit-profile-instance-link action-btn icon-btn edit-btn has-tooltip"><span class="p"></span><span class="simple_tooltip radius6">Edit Profile<span></span></span></a>
                                <?php endif; ?>
                                <a href="<?php echo get_site_url()?>/my-profile?td-action=<?php echo wp_create_nonce('delete-profile-instance')?>&id=<?php echo $instance->id?>&return=<?php echo base64_encode(get_site_url() . "/my-test-data")?>" class="action-btn icon-btn delete-btn left10 has-tooltip delete-profile-btn"><span class="p"></span><span class="simple_tooltip radius6">Delete Profile<span></span></span></a>
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
    
    jQuery('#delete-profile-link').click(function(){
        var checked = jQuery('#my_test_data_profiles .tbody .td-chk input[type="checkbox"]:checked').length;            
        if(checked == 0)
        {
            alert("Please select a row.");
            return false;
        }else{
            
            var ids = new Array();
            jQuery('#my_test_data_profiles .tbody .td-chk input[type="checkbox"]:checked').each(function(){
                ids.push(this.value);
            })           
            if(!confirm('Are you sure you want to delete?'))
            {
                return false;
            }
            
            //jQuery('#my_testdata').append('<div class="loading1"></div>');
            jQuery('#my_testdata .loading1').show();
            
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
                    document.location.reload();

                }
            })    
            return false;
        }
    });
})
</script>
<?php
get_footer();
?>
