var profileData = null;
var profileType = null;    
var zclipTimer = null;

jQuery(document).ready(function(){
    function initEditProfileBox()
    {
//            jQuery('#create_profile_panel').html('');
//            jQuery('#edit_profile_instance_panel').hide();
//            jQuery('#edit-profile-box #profile-type-id').val('');
        jQuery('#edit-profile-box #instance-id').val('');
        jQuery('#edit-profile-box .message').remove();
//        jQuery('#edit-profile-box .btn-row .process-btn').hide();
//            jQuery('#edit-profile-box').width(500);
        profileData = null;
        profileType = null;
        jQuery('#editProfileErrorForm').hide();
        jQuery('#editProfileForm').show();
    }
    
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
                        jQuery('#profile-name').val(fileName.substring(0, fileName.lastIndexOf('.')));
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
            $result = tv4.validateMultiple(data.result, profileType);
            if(!$result.valid)
            {
                var errors = '';
                for (var i in $result.errors) {
                    errors += $result.errors[i].dataPath + '<br/>';
                    errors += $result.errors[i].message + '<br/>';
                }
                jQuery('#editProfileErrorForm #profile-errors').html(errors.replace(new RegExp('<br/>', 'g'), '\n'));
                jQuery('#editProfileErrorForm #profile-error-content').html(errors);
                jQuery('#editProfileForm').hide();
                jQuery('#editProfileErrorForm').show();
                
                jQuery('#download-error').click(function(){
                    /*jQuery.ajax({
                        url: "/?td-action="  + jQuery('#download-error-action').val(),
                        data: jQuery('#editProfileErrorForm').serialize(),
                        type: 'post',
                        dataType: 'html',
                        success: function(rsp){
                        }
                    });*/
                    jQuery('#editProfileErrorForm').submit();
                });
                jQuery('#copy-error').clipboard({
                    path: '/wp-content/themes/bp-child/functions/test-data/jquery.clipboard.swf',
                    copy: function(){ 
                        return jQuery('#editProfileErrorForm #profile-errors').html(); 
                    }
                });
                jQuery('#close-error').click(function(){
                    jQuery('#editProfileErrorForm').hide();
                    jQuery('#editProfileForm').show();
                });
                //jQuery('#edit-profile-box .popup-box-content').prepend('<p class="message error">The entered values do not match with the profile type.</p>');
                return false;
            }
            //Saving Data
            jQuery('#edit-profile-box .loading b').html('SAVING DATA');
            jQuery('#edit-profile-box .loading').show();                
            
            jQuery.ajax({
                url: "/?td-action="  + jQuery('#save-instance-action').val() + "&" + jQuery('#editProfileForm').serialize(),
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
    
    jQuery('#add-new-test-data-link').cplightbox({
        inline: true,
        closeWhenClickOveraly: false,
        onStart: function(){
            initEditProfileBox();            
            jQuery('#edit-profile-box .popup-box-header').html('Create Profile Instance');
            jQuery('#profile_instance_txt').val('');
            renderJsonUI();
        }
    });
    
    //View Profile Link
    jQuery('.view-profile-instance-link').cplightbox({
        type: 'ajax',
        onLoad: function()
        {                
            jQuery('.popup-box:visible .zcliplink').each(function(){
                if(!jQuery(this).data('zclipId'))
                {
                    jQuery('.popup-box:visible .zcliplink').zclip({
                        path: '/wp-content/themes/bp-child/js/ZeroClipboard.swf',
                        copy: function(){
                            return jQuery('#' + jQuery(this).attr('data-id')).val();    
                        },
                        afterCopy: function(){
                            jQuery('.popup-box:visible .zclipsucces-msg').fadeIn();
                            if(zclipTimer != null)
                            {
                                clearTimeout(zclipTimer);
                            }
                            zclipTimer = setTimeout(function(){
                                jQuery('.popup-box:visible .zclipsucces-msg').fadeOut('fast');
                            }, 2000);
                        }
                    })        
                }
            })
            
        }
    })    
    
    //Edit Profile Link
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
                jQuery('#edit_profile_instance_panel').hide();
                jQuery('#edit-profile-box #profile-type-id').val(type_id);
                jQuery('#edit-profile-box #profile-type-id').change();
            }
        });
    })    
    
    //Disable auto submit to save it by ajax
    jQuery('#editProfileForm').submit(function(){
        return false;
    });
    
    //Edit Profile Box
    jQuery('#edit-profile-box .submit-btn').click(function(){        
        jQuery('#edit-profile-box .message').remove();
        if(profileData == null || profileType == null)
        {
            jQuery('#edit-profile-box .popup-box-content').prepend('<p class="message error">Please choose a profile type.</p>');
            return false;
        }            
        $result = tv4.validateMultiple(profileData.value(), profileType);
        if(!$result.valid)
        {
            var errors = '';
            for (var i in $result.errors) {
                errors += $result.errors[i].dataPath + '<br/>';
                errors += $result.errors[i].message + '<br/>';
            }
            jQuery('#editProfileErrorForm #profile-errors').html(errors.replace(new RegExp('<br/>', 'g'), '\n'));
            jQuery('#editProfileErrorForm #profile-error-content').html(errors);
            jQuery('#editProfileForm').hide();
            jQuery('#editProfileErrorForm').show();
            
            jQuery('#download-error').click(function(){
                /*jQuery.ajax({
                    url: "/?td-action="  + jQuery('#download-error-action').val(),
                    data: jQuery('#editProfileErrorForm').serialize(),
                    type: 'post',
                    dataType: 'html',
                    success: function(rsp){
                    }
                });*/
                profile_value = profileData.value();
                if (profile_value.Profile && profile_value.Profile.Title != '') {
                    jQuery('#profile-name').val(profile_value.Profile.Title);
                } else {
                    jQuery('#profile-name').val('profile');
                }
                jQuery('#editProfileErrorForm').submit();
            });
            jQuery('#copy-error').clipboard({
                path: '/wp-content/themes/bp-child/functions/test-data/jquery.clipboard.swf',
                copy: function(){ 
                    return jQuery('#editProfileErrorForm #profile-errors').html(); 
                }
            });
            jQuery('#close-error').click(function(){
                jQuery('#editProfileErrorForm').hide();
                jQuery('#editProfileForm').show();
            });
            //jQuery('#edit-profile-box .popup-box-content').prepend('<p class="message error">The entered values do not match with the profile type.</p>');
            return false;
        }
        //Saving Data
        jQuery('#edit-profile-box .loading b').html('SAVING DATA');
        jQuery('#edit-profile-box .loading').show();
        
        
        jQuery.ajax({
            url: "/?td-action=" + jQuery('#save-instance-action').val() + "&" + jQuery('#editProfileForm').serialize(),
            data: 'data=' + encodeURIComponent(JSON.stringify(profileData.value())),
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
    
    //Edit Profile Box
    jQuery('#edit-profile-box #profile-type-id').change(function(){
        if(this.value == '')
        {
            initEditProfileBox();
        }else{
            jQuery('#edit-profile-box .loading b').html('LOADING DATA');
            jQuery('#edit-profile-box .loading').show();
            jQuery('#edit-profile-box .message').remove();
            jQuery.ajax({
                url: '/?td-action=' + jQuery('#get-profile-ui-action').val(),
                data: jQuery('#editProfileForm').serialize(),
                dataType: 'xml',
                success: function(rsp)
                {
                    if(jQuery(rsp).find('status').text() == 'success')   
                    {
                        jQuery('#profile_type_txt').val(jQuery(rsp).find('schema').text());
                        jQuery('#profile_instance_txt').val(jQuery(rsp).find('data').text());
                        renderJsonUI();
                    }else{
                        jQuery('#edit-profile-box .popup-box-content').prepend('<p class="message error">' + jQuery(rsp).find('message').text() + '</p>');
                        jQuery('#edit_profile_instance_panel').hide();
                        jQuery('#edit-profile-box #profile-type-id').val('');
                        jQuery('#profile_type_txt').val('');
                        jQuery('#profile_instance_txt').val('');
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
    
    
    function renderJsonUI()
    {
        jQuery('#edit_profile_instance_panel').show();
        
        var targetElement = document.getElementById('create_profile_panel');
        if(jQuery('#profile_type_txt').val())
        {
            profileType = jQuery.parseJSON(jQuery('#profile_type_txt').val());
            profileType.additionalProperties = false;                            
        
            var schema = Jsonary.createSchema(profileType);                            
            
            if(jQuery('#profile_instance_txt').val())
            {    
                profileData = Jsonary.create(jQuery.parseJSON(jQuery('#profile_instance_txt').val())).addSchema(schema);                
            }else{
                profileData = Jsonary.create({}).addSchema(schema);
            }
            Jsonary.render(targetElement, profileData);                            
        }   
    }
});

/**
* Customize the popup box
* 
*/
function afterJsonRender()
{
    var maxWidth = jQuery('#create_profile_panel table:eq(0)').width();
    
    //Resize The box width
    if(jQuery('#edit-profile-box').width() < maxWidth + 20 + 22)
    {
        jQuery('#edit-profile-box').width(maxWidth + 20 + 22);                
        jQuery('.mask-wrapper').width(jQuery(document).width());
        jQuery('.mask-wrapper').height(jQuery(document).height());           
    }        
}