<?php
/**
* Profile - My Test Data Profiles
*/
if(!defined('ABSPATH'))
    die('Invalid Request!');    

$profileInstances = getCustomerProfileInstances();
?>
<div class="column left three_fifths nopadding">
    <div class="grid-box table-box" id="my_test_data_profiles">
        <div class="grid-box-header">
            <h5 class="left">My Test Data Management</h5>
            <?php
                if(count($subscriptions) > 0){
            ?>
                <a class="gbh-btn gbh-btn-add right" id="add-new-test-data-link" href="#edit-profile-box">Add<span class="simple_tooltip radius6">Add Test Data<span></span></span></a>
            <?php }else{ ?>
                <a class="gbh-btn gbh-btn-add right" href="#need-subscription-box" rel="custom-popup" cp-type="inline" >Add<span class="simple_tooltip radius6">Add Test Data<span></span></span></a>
            <?php } ?>
            <div class="clear"></div>
        </div>
        <div class="grid-box-body">
            <div class="thead tr">
               <div class="td td-profile-name">Profile Name</div>
               <div class="td td-profile-type">Type</div>
               <div class="td td-action">Action</div>
               <div class="clear"></div>
           </div>
           <div class="tbody">
           <?php
           if(!$profileInstances){
               ?>
               <div class="tr">
                   <div class="td td-full">No data found.</div>
                   <div class="clear"></div>
               </div>
               <?php
           }else{
               foreach($profileInstances as $instance)
               {
                   $instanceObj = json_decode(base64_decode($instance->content));
           ?>
                <div class="tr">
                   <div class="td td-profile-name">
                       <a href="<?php echo get_site_url()?>?td-action=<?php echo wp_create_nonce('view-profile-instance')?>&id=<?php echo $instance->id?>" rel="custom-popup" cp-type="ajax"><?php echo $instance->profile_name?></a>  <br />
                       <b>Purpose: </b> <?php echo $instanceObj->Profile->Purpose?>
                       <p><?php echo $instanceObj->Profile->Description?></p>                   
                   </div>
                   <div class="td td-profile-type">
                       <a href="<?php echo get_site_url()?>?td-action=<?php echo wp_create_nonce('view-profile-type')?>&id=<?php echo $instance->type_id?>" rel="custom-popup" cp-type="ajax" class="view-profile-type-link"><?php echo $instance->profile_type_title; ?></a>                    
                   </div>
                   <div class="td td-action">
                        <?php
                            if(bp_is_group_admin(get_current_user_id()))
                            {
                        ?>
                        <a href="#edit-profile-box" data-id="<?php echo $instance->id?>" data-type-id="<?php echo $instance->type_id?>" class="edit-profile-instance-link action-btn icon-btn grey-edit-btn"><span class="p"></span></a>
                        <a href="<?php echo get_site_url()?>/my-profile?td-action=<?php echo wp_create_nonce('delete-profile-instance')?>&id=<?php echo $instance->id?>&return=<?php echo base64_encode(get_site_url() . "/my-profile")?>" class="action-btn icon-btn grey-delete-btn left10"><span class="p"></span></a>
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
</div>
<?php $my_test_data_profiles_desc = get_post_meta($post->ID, 'my_test_data_profiles_desc', true);?>
<?php if ($my_test_data_profiles_desc): ?>
<div class="right two_fifths">
    <div class="gray_message_box radius9 light_gray_txt">
        <div class="indicator"></div>
        <?php echo $my_test_data_profiles_desc;?>
    </div>
</div>
<?php endif; ?>
<?php
if(count($subscriptions) > 0){
?>

<?php 
    $profileTypes = getCustomerProfileTypes(get_current_user_id());
?>
<div class="popup-box" id="edit-profile-box" style="display: none; width: 900px;">
    <form name="editProfileForm" id="editProfileForm" action="">
        <div class="popup-box-header radius6 noradiusbottom">Create Profile Instance</div>        
        <div class="popup-box-content grid-box-body">                    
            <div class="field-row">
                <label class="left right10 lineheight22px">Profile Type:</label>
                <select class="select left" name="profile-type-id" id="profile-type-id">
                    <option value="">- Select -</option>
                    <?php foreach($profileTypes as $p){ ?>
                    <option value="<?php echo $p->id?>"><?php echo $p->title?></option>
                    <?php } ?>
                </select>
                <div class="clear"></div>
            </div>
            <div id="edit_profile_instance_panel">
                <div id="upload_profile_panel">                    
                    <div class="field-row">
                        <label class="padding5-10-5-0"> Upload Json file</label> 
                        <div class="grid-cell">                                        
                            <span class="file-placeholder action-btn add-new-btn left nomarginleft">
                                <span class="p"></span>
                                <span class="t">Select File</span>
                                <input type="file" name="profile_instance_file" class="input-file" id="profile_instance_file" />
                            </span>
                            <small class="left lineheight22px">&nbsp;&nbsp;(.txt or .json file)</small>
                            <div class="clear"></div>
                            <p id="file-name-list"></p>
                        </div>
                        <div class="grid-cell">
                            <a href="#" class="action-btn process-btn plus" id="profile_instance_upload_btn"><span class="p"></span><span class="t">Upload</span></a>
                        </div>
                        <div class="clear"></div>
                    </div>
                </div>
                <div class="enter-values"><span>Or enter values</span></div>
                <div id="create_profile_panel">                
                    <div class="clear"></div>
                </div>
                
            </div>
        </div>
        <div class="popup-box-footer radius6 noradiustop">                            
            <a href="#" class="action-btn process-btn submit-btn"><span class="p"></span><span class="t">SAVE</span></a>            
            <a href="#" class="action-btn cancel-btn close-popup-btn"><span class="p"></span><span class="t">Cancel</span></a>            
            <div class="clear"></div>
        </div>                        
        <a class="close_btn"></a>                        
        <div class="loading loading-with-text radius6"><div><b>LOADING DATA</b><p>Please wait...</p></div></div>
        <input type="hidden" name="instance-id" id="instance-id" value="" />
    </form>
</div>
<script type="text/javascript">
    var profileData = null;
    var profileType = null;
    jQuery(document).ready(function(){
        //Ajax File Uploader
        jQuery('#profile_instance_file').fileupload({
            url: '/upload-json.php',
            dataType: 'json',
            add: function (e, data) {
                jQuery('#edit-profile-box .message').remove();                       
                jQuery('#upload_profile_panel #file-name-list').html('<span>' + data.files[0].name + '</span>');
                data.context = jQuery('#profile_instance_upload_btn')
                    .click(function () {
                        jQuery('#edit-profile-box .message').remove();  
                        if(data.files.length < 1)
                        {
                            jQuery('#edit-profile-box .popup-box-content').prepend('<p class="message error">Please select a file to upload</p>');
                        }else{
                            //Check File Extension Validation
                            fileName = data.files[0].name;
                            var ext = fileName.substr(fileName.lastIndexOf('.') + 1).toLowerCase();                            
                            if(ext != 'json' && ext != 'txt')
                            {
                                jQuery('#edit-profile-box .popup-box-content').prepend('<p class="message error">Please select a valid file.</p>');
                                return false;
                            }
                            jQuery('#edit-profile-box .loading b').html('UPLOADING DATA');
                            jQuery('#edit-profile-box .loading').show();
                            data.submit();    
                        }
                        
                    });
            },
            done: function (e, data) {
                jQuery('#edit-profile-box .message').remove();
                jQuery('#edit-profile-box .loading').hide();
                //Check Validation
                if(!tv4.validate(data.result, profileType))
                {
                    jQuery('#edit-profile-box .popup-box-content').prepend('<p class="message error">The entered values do not match with the profile type.</p>');
                    return false;
                }
                //Saving Data
                jQuery('#edit-profile-box .loading b').html('SAVING DATA');
                jQuery('#edit-profile-box .loading').show();                
                
                jQuery.ajax({
                    url: "/?td-action=<?php echo wp_create_nonce('save-tester-instance')?>&" + jQuery('#editProfileForm').serialize(),
                    data: 'data=' + encodeURIComponent(JSON.stringify(data.result)),
                    type: 'post',
                    dataType: 'xml',
                    success: function(rsp)
                    {
                        if(jQuery(rsp).find('status').text() == 'success')   
                        {
                            jQuery('#edit-profile-box .popup-box-content').prepend('<p class="message success">Successfully saved!</p>');
                            document.location.reload();
                        }else{                    
                            jQuery('#edit-profile-box .popup-box-content').prepend('<p class="message error">' + jQuery(rsp).find('msg').text() + '</p>');
                        }
                    },
                    error: function(rsp){
                        jQuery('#edit-profile-box .popup-box-content').prepend('<p class="message error">' + rsp.reponseText + '</p>');
                    },
                    complete: function(rsp){
                        jQuery('#edit-profile-box .loading').hide();
                    }
                })
                return false;
            },
            fail: function(e, data){
                jQuery('#edit-profile-box .loading').hide();
                jQuery('#edit-profile-box .popup-box-content').prepend('<p class="message error">Uploaded file is not a valid json format.</p>');
            }
        });
        function initEditProfileBox()
        {
            jQuery('#create_profile_panel').html('');
            jQuery('#edit_profile_instance_panel').hide();
            jQuery('#edit-profile-box #profile-type-id').val('');
            jQuery('#edit-profile-box #instance-id').val('');
            jQuery('#edit-profile-box .message').remove();
            profileData = null;
            profileType = null;
        }
        jQuery('#add-new-test-data-link').cplightbox({
            inline: true,
            closeWhenClickOveraly: false,
            onStart: function(){
                initEditProfileBox();
                jQuery('#edit-profile-box #instance-id').val('');
                jQuery('#edit-profile-box .popup-box-header').html('Create Profile Instance');
            }
        });
        jQuery('.edit-profile-instance-link').each(function(){
            var id = jQuery(this).attr('data-id');
            var type_id = jQuery(this).attr('data-type-id');
            jQuery(this).cplightbox({
                inline: true,
                closeWhenClickOveraly: false,
                onStart: function(){
                    initEditProfileBox();
                    jQuery('#edit-profile-box #instance-id').val(id);
                    jQuery('#edit-profile-box .popup-box-header').html('Edit Profile Instance');
                    jQuery('#edit-profile-box #profile-type-id').val(type_id);
                    jQuery('#edit-profile-box #profile-type-id').change();
                }
            });
        })
        
        jQuery('#edit-profile-box #profile-type-id').change(function(){
            if(this.value == '')
            {
                initEditProfileBox();
            }else{
                jQuery('#edit-profile-box .loading b').html('LOADING DATA');
                jQuery('#edit-profile-box .loading').show();
                jQuery('#edit-profile-box .message').remove();
                jQuery.ajax({
                    url: '<?php echo get_site_url()?>?td-action=<?php echo wp_create_nonce('get-tester-profile-ui')?>',
                    data: jQuery('#editProfileForm').serialize(),
                    dataType: 'xml',
                    success: function(rsp)
                    {
                        if(jQuery(rsp).find('status').text() == 'success')   
                        {
                            jQuery('#edit_profile_instance_panel').show();
                            var targetElement = document.getElementById('create_profile_panel');
                            profileType = jQuery.parseJSON(jQuery(rsp).find('schema').text());
                            profileType.additionalProperties = false;                            
                            var schema = Jsonary.createSchema(profileType);
                            profileData = Jsonary.create(jQuery.parseJSON(jQuery(rsp).find('data').text())).addSchema(schema);
                            Jsonary.render(targetElement, profileData);                            
                        }else{
                            jQuery('#edit-profile-box .popup-box-content').prepend('<p class="message error">' + jQuery(rsp).find('message').text() + '</p>');
                            jQuery('#edit-profile-box .loading b').html('LOADING DATA');
                            jQuery('#edit_profile_instance_panel').hide();
                            jQuery('#edit-profile-box #profile-type-id').val('');
                            profileData = null;
                            profileType = null;
                        }
                        jQuery(window).resize();
                    },
                    error: function(rsp){
                        jQuery('#edit-profile-box .popup-box-content').prepend('<p class="message error">' + rsp.reponseText + '</p>');
                    },
                    complete: function(rsp){
                        jQuery('#edit-profile-box .loading').hide();
                    }
                })
            }
        })
                
        jQuery('#editProfileForm').submit(function(){
            return false;
        });
        
        jQuery('#edit-profile-box .submit-btn').click(function(){        
            jQuery('#edit-profile-box .message').remove();
            if(profileData == null || profileType == null)
            {
                jQuery('#edit-profile-box .popup-box-content').prepend('<p class="message error">Please choose a profile type.</p>');
                return false;
            }
            if(!tv4.validate(profileData.value(), profileType))
            {
                jQuery('#edit-profile-box .popup-box-content').prepend('<p class="message error">The entered values do not match with the profile type.</p>');
                return false;
            }
            //Saving Data
            jQuery('#edit-profile-box .loading b').html('SAVING DATA');
            jQuery('#edit-profile-box .loading').show();            
            jQuery.ajax({
                url: "/?td-action=<?php echo wp_create_nonce('save-tester-instance')?>&" + jQuery('#editProfileForm').serialize(),
                data: "data=" + encodeURIComponent(JSON.stringify(profileData.value())),
                type: 'post',
                dataType: 'xml',
                success: function(rsp)
                {
                    if(jQuery(rsp).find('status').text() == 'success')   
                    {
                        jQuery('#edit-profile-box .popup-box-content').prepend('<p class="message success">Successfully saved!</p>');
                        document.location.reload();
                    }else{                    
                        jQuery('#edit-profile-box .popup-box-content').prepend('<p class="message error">' + jQuery(rsp).find('msg').text() + '</p>');
                    }
                },
                error: function(rsp){
                    jQuery('#edit-profile-box .popup-box-content').prepend('<p class="message error">' + rsp.reponseText + '</p>');
                },
                complete: function(rsp){
                    jQuery('#edit-profile-box .loading').hide();
                }
            })
            return false;
        })
        
    })    

</script>
<?php
}else{ 
?>
<div class="popup-box" id="need-subscription-box" style="display: none; width: 500px;">
    <div class="popup-box-header radius6 noradiusbottom">Need a subscription</div>        
    <div class="popup-box-content grid-box-body">                    
        <p class="message notice">You must subscribe to a test suite before you can create a test data profile.</p>
    </div>
    <div class="popup-box-footer radius6 noradiustop">                                    
        <div class="clear"></div>
    </div>                        
    <a class="close_btn"></a>                        
</div>
<?php
}
?>