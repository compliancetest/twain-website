<div class="modal-header">
    <button type="button" class="close-modal" data-tooltip="tooltip" title="Close popup" data-placement="left" data-dismiss="modal" aria-label="Close">Close</button>
    Edit Profile Instance
</div>
<div class="modal-body block-loading-wrapper">
    <div class="edit-profile-form-wrapper" id="editProfileBox">
        <h3>Please Select Profile Type</h3>

        <form name="editProfileForm" id="editProfileForm" action="">
            <fieldset class="edit-profile-fieldset">
                <select class="form-control profile-type-drowdown" name="profile-type-id" id="profile-type-id">
                    @foreach($community->profileTypes as $type)
                        <?php error_log($profileType->id === $type->id);?>
                        <option value="{{ $type->id }}" @if($profileType->id === $type->id) selected="selected" @endif>{{ $type->getTitle() }}</option>
                    @endforeach
                </select>

                <div class="upload-json-file-box">
                    <h4>Upload Json file</h4>

                    <div class="upload-file-field">
                        <input type="file" name="profile_instance_file" class="input-file" id="profile_instance_file" data-file-type="doc"
                               data-file-extensions="(.txt or .json file)"/>
                    </div>
                    <div class="upload-file-field-additional-btn">
                        <a href="#" class="btn btn-success btn-with-icon btn-upload" id="editProfileUpload">Upload</a>
                    </div>
                </div>

                <div class="or-delimiter-wrap">
                    <div class="or-delimiter"><span class="or-text">or</span></div>
                </div>
                <div class="edit-profile-form">
                    <h4>Enter Values</h4>

                    <div id="create_profile_panel"></div>
                </div>


            </fieldset>
            <textarea id="profile_type_txt" style="display: none;">{!! base64_decode($profileType->schema) !!}</textarea>
            <textarea id="profile_instance_txt" style="display: none;">{!! json_encode($profile->getProfileFromS3()) !!}</textarea>

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
                jQuery('#<?php echo $profile->id;?>Loading').show();
                jQuery.ajax({
                    url: '/communityprofiles/{{ $community->slug }}/edit/{{ $profile->id }}/' + jQuery('#editProfileBox #profile-type-id').val(),
                    success: function (rsp) {
                        jQuery("#modalEditProfile .modal-content ").html(rsp);
                        renderJsonUI();
                        jQuery(window).resize();
                    },
                    error: function (rsp) {
                        jQuery('#editProfileForm').prepend('<p class="message error">' + rsp.reponseText + '</p>');
                        jQuery('#<?php echo $profile->id;?>Loading').hide();
                    },
                    complete: function (rsp) {
                        jQuery('#<?php echo $profile->id;?>Loading').hide();
                    }
                })
            }
        });

        renderJsonUI();

        function initEditProfileBox() {

            jQuery('#editProfileBox #instance-id').val('');
            jQuery('#editProfileBox .message').remove();

            profileData = null;
            profileType = null;
            jQuery('#editProfileErrorForm').hide();
            jQuery('#editProfileForm').show();
        }

        function showUploadDialog(rsp) {
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
            if (jQuery('#profile_type_txt').val()) {
                profileType = jQuery.parseJSON(jQuery('#profile_type_txt').val());
                profileType.additionalProperties = false;

                var schema = Jsonary.createSchema(profileType);

                if (jQuery('#profile_instance_txt').val()) {
                    profileData = Jsonary.create(jQuery.parseJSON(jQuery('#profile_instance_txt').val())).addSchema(schema);
                } else {
                    profileData = Jsonary.create({}).addSchema(schema);
                }
                Jsonary.render(targetElement, profileData);
            }
        }

        jQuery('#editProfileUpload').on('click', function(e){
            e.preventDefault();
            jQuery('#editProfileForm .error-message').remove();
            if(!jQuery('#profile_instance_file').val()){
                jQuery('#profile-type-id').after('<p class="message error-message">Please select file first!</p>');
                return false;
            }
            jQuery('#editProfileForm').ajaxSubmit({
                url: "/communityprofiles/{{ $community->slug }}/{{ $profile->id }}",
                type: 'POST',
                success: function(rsp)
                {
                    if(rsp.status == 'success')
                    {
                        jQuery('#profile-type-id').after('<p class="message success-message">Successfully saved!</p>');
                        location.reload();
                    }else{
                        jQuery('#profile-type-id').after('<p class="message error-message">' + jQuery(rsp).find('msg').text() + '</p>');
                    }
                },
                error: function(rsp){
                    console.log(rsp);
                    jQuery('#profile-type-id').after('<p class="message error-message">' + rsp.responseJSON.message + '</p>');
                },
                complete: function(rsp){
                    jQuery('#edit-profile-box .loading').hide();
                }
            })
        });

        jQuery('.btn-confirm').on('click', function(e){
            e.preventDefault();
            jQuery.ajax({
                url: "/communityprofiles/{{ $community->slug }}/{{ $profile->id }}",
                data: {
                    'data': encodeURIComponent(JSON.stringify(profileData.value())),
                    'profile-type-id': '{{ $profileType->id }}'
                },
                type: 'POST',
                success: function(rsp)
                {
                    if(rsp.status == 'success')
                    {
                        jQuery('#modalEditProfile .modal-footer').prepend('<p class="message success-message">Successfully saved!</p>');
                        location.reload();
                    }else{
                        jQuery('#modalEditProfile .modal-footer').prepend('<p class="message error-message">' + rsp.message + '</p>');
                    }
                },
                error: function(rsp){
                    jQuery('#modalEditProfile .modal-footer').prepend('<p class="message error-message">' + rsp.responseJSON.message + '</p>');
                },
                complete: function(rsp){
                    jQuery('#edit-profile-box .loading').hide();
                }
            })
        });

        $('#modalEditProfile').on('hidden.bs.modal', function (e) {
            $('#modalEditProfile .modal-body').html('<div class="block-loading"><div class="loading-content"><span class="loader"></span><div class="loading-text">LOADING DATA</div><div class="loading-wait">Please wait...</div></div></div>');
        })


    </script>
</div>
<div class="modal-footer">
    <a href="#" class="btn btn-success btn-with-icon btn-confirm">Confirm</a>
    <a href="#" class="btn btn-default btn-with-icon btn-cancel" data-dismiss="modal">Cancel</a>
</div>
<div class="block-loading" id="{{ $profile->id }}Loading"><div class="loading-content"><span class="loader"></span><div class="loading-text">LOADING</div><div class="loading-wait">Please wait...</div></div></div>
