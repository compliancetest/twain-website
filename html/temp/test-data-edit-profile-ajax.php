<?php

//sleep(2);

?>
<div class="edit-profile-form-wrapper" id="editProfileBox">
    <h3>Please Select Profile Type</h3>
    <form name="editProfileForm" id="editProfileForm" action="">
        <fieldset>
            <select class="select left" name="profile-type-id" id="profile-type-id">
                <option value="7">DataSource v1.0</option>
                <option value="8">TCEF v1.1</option>
            </select>
            <div>
                <h4>Upload Json file</h4>
                <div class="upload-file-field">
                    <input type="file" name="profile_instance_file" class="input-file" id="profile_instance_file" data-file-type="doc" data-file-extensions="(.txt or .json file)" />
                </div>
            </div>

            <div class="or-delimiter-wrap"><div class="or-delimiter"><span>or</span></div></div>
            <div class="edit-profile-form">
                <h4>Enter Values</h4>
                <div id="create_profile_panel"></div>
            </div>


        </fieldset>
        <textarea id="profile_type_txt" style="display: none;"></textarea>
        <textarea id="profile_instance_txt" style="display: none;"></textarea>

    </form>
</div>


<script type="text/javascript">
    customizeFileTag();

    var profileData = null;
    var profileType = null;

    //Edit Profile Box
    jQuery('#editProfileBox #profile-type-id').change(function () {
        if (this.value == '') {
            initEditProfileBox();
        } else {
            jQuery.ajax({
                url: '/html/temp/profile-response.xml',
                data: jQuery('#editProfileForm').serialize(),
                dataType: 'xml',
                success: function (rsp) {
                    if (jQuery(rsp).find('status').text() == 'success') {
                        if (jQuery('#is_upload').val() == '1') {
                            showUploadDialog(rsp);
                        } else {
                            console.log('ajax');
                            jQuery('#create_profile_panel').show();
                            jQuery('.hide_on_upload').show();
                            jQuery('#editProfileBox .submit-btn').show();
                            jQuery('#profile_type_txt').val(jQuery(rsp).find('schema').text());
                            jQuery('#profile_instance_txt').val(jQuery(rsp).find('data').text());
                            renderJsonUI();
                        }
                    } else {
                        if (jQuery('#is_upload').val() == '1') {
                            showUploadDialog(rsp);
                        } else {
                            jQuery('#editProfileBox .submit-btn').hide();
                            jQuery('#editProfileBox .popup-box-content').prepend('<p class="message error">' + jQuery(rsp).find('message').text() + '</p>');
                            jQuery('#edit_profile_instance_panel').hide();
                            jQuery('#editProfileBox #profile-type-id').val('');
                            jQuery('#profile_type_txt').val('');
                            jQuery('#profile_instance_txt').val('');
                            profileData = null;
                            profileType = null;
                        }
                    }
                    jQuery(window).resize();
                },
                error: function (rsp) {
                    jQuery('#editProfileBox .popup-box-content').prepend('<p class="message error">' + rsp.reponseText + '</p>');
                },
                complete: function (rsp) {
                    jQuery('#editProfileBox .loading').hide();
                }
            })
        }
    });

    function initEditProfileBox() {

        jQuery('#editProfileBox #instance-id').val('');
        jQuery('#editProfileBox .message').remove();

        profileData = null;
        profileType = null;
        jQuery('#editProfileErrorForm').hide();
        jQuery('#editProfileForm').show();
    }

    function showUploadDialog( rsp ){
        jQuery('.hide_on_upload').hide();
        jQuery('#profile_type_txt').val(jQuery(rsp).find('schema').text());
        jQuery('#profile_instance_txt').val('');
        profileData = null;
        jQuery('#edit-profile-box .submit-btn').hide();
        renderJsonUI();
        jQuery('#create_profile_panel').hide();
    }

    function renderJsonUI() {
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

</script>