<div class="modal-header">
    <button type="button" class="close-modal" data-tooltip="tooltip" title="Close popup" data-placement="left" data-dismiss="modal" aria-label="Close">Close</button>
    Create Profile Instance
</div>
<div class="modal-body block-loading-wrapper">
    <div class="edit-profile-form-wrapper" id="createProfileBox">
        <h3>Please Select Profile Type</h3>

        <form name="createProfileForm" id="createProfileForm" action="">
            <fieldset class="edit-profile-fieldset">
                <select class="form-control profile-type-drowdown" name="profile-type-id" id="profile-type-id">
                    @foreach($community->profileTypes as $kk => $type)

                        @if($kk == 0 && !$profileType) <?php $profileType = $type;?> @endif

                        <option value="{{ $type->id }}" @if($type->id == $profileType->id) selected="selected" @endif>{{ $type->getTitle() }}</option>
                    @endforeach
                </select>

                <div class="upload-json-file-box">
                    <h4>Upload Json file</h4>

                    <div class="upload-file-field">
                        <input type="file" name="create_profile_instance_file" class="input-file" id="create_profile_instance_file" data-file-type="doc"
                               data-file-extensions="(.txt or .json file)"/>
                    </div>
                    <div class="upload-file-field-additional-btn">
                        <a href="#" class="btn btn-success btn-with-icon btn-upload" id="createProfileUpload">Upload</a>
                    </div>
                </div>

                <div class="or-delimiter-wrap">
                    <div class="or-delimiter"><span class="or-text">or</span></div>
                </div>
                <div class="create-profile-form">
                    <h4>Enter Values</h4>

                    <div id="create_profile_panel"></div>
                </div>


            </fieldset>
            <textarea id="create_profile_type_txt" style="display: none;">{!! base64_decode($profileType->schema) !!}</textarea>
            <textarea id="create_profile_instance_txt" style="display: none;"></textarea>

        </form>
    </div>


    <script type="text/javascript">
        customizeFileTag();

        var profileData = null;
        var profileType = null;

        //Create Profile Box
        jQuery('#modalCreateProfile #profile-type-id').change(function () {
            if (this.value == '') {
                initCreateProfileBox();
            } else {
                jQuery('#CreateProfileLoading').show();
                jQuery.ajax({
                    url: '/communityprofiles/{{ $community->slug }}/create/',
                    data: {
                        'profile_type_id': jQuery('#modalCreateProfile #profile-type-id').val()
                    },
                    success: function (rsp) {
                        jQuery("#modalCreateProfile .modal-content").html(rsp);
                        renderJsonUI();
                        jQuery(window).resize();
                    },
                    error: function (rsp) {
                        jQuery('#modalCreateProfile .modal-body').prepend('<p class="message error">' + rsp.reponseText + '</p>');
                        jQuery('#CreateProfileLoading').hide();
                    },
                    complete: function (rsp) {
                        jQuery('#CreateProfileLoading').hide();
                    }
                })
            }
        });

        renderJsonUI();

        function initCreateProfileBox() {

            jQuery('#createProfileBox #instance-id').val('');
            jQuery('#createProfileBox .message').remove();

            profileData = null;
            profileType = null;
            jQuery('#createProfileErrorForm').hide();
            jQuery('#createProfileForm').show();
        }

        function showUploadDialog(rsp) {
            jQuery('.hide_on_upload').hide();
            jQuery('#create_profile_type_txt').val(jQuery(rsp).find('schema').text());
            jQuery('#create_profile_instance_txt').val('');
            profileData = null;
            jQuery('#create-profile-box .submit-btn').hide();
            renderJsonUI();
            jQuery('#create_profile_panel').hide();
        }

        function renderJsonUI() {
            jQuery('#create_profile_instance_panel').show();

            var targetElement = document.getElementById('create_profile_panel');
            if (jQuery('#create_profile_type_txt').val()) {
                profileType = jQuery.parseJSON(jQuery('#create_profile_type_txt').val());
                profileType.additionalProperties = false;

                var schema = Jsonary.createSchema(profileType);
                profileData = Jsonary.create({}).addSchema(schema);
                Jsonary.render(targetElement, profileData);
            }
        }


        jQuery('#createProfileUpload').on('click', function(e){
            e.preventDefault();
            if(!jQuery('#create_profile_instance_file').val()){
                jQuery('#profile-type-id').after('<p class="message error-message">Please select file first!</p>');
            }
            jQuery('#createProfileForm').ajaxSubmit({
                url: "/communityprofiles/{{ $community->slug }}/",
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
                    jQuery('#profile-type-id').after('<p class="message error-message">' + rsp.responseJSON.message + '</p>');
                },
                complete: function(rsp){
                    jQuery('#create-profile-box .loading').hide();
                }
            })
        });

        jQuery('.btn-confirm').on('click', function(e){
            e.preventDefault();
            jQuery.ajax({
                url: "/communityprofiles/{{ $community->slug }}/",
                data: {
                    'data': encodeURIComponent(JSON.stringify(profileData.value())),
                    'profile_type_id': jQuery('#modalCreateProfile #profile-type-id').val()
                },
                type: 'POST',
                success: function(rsp)
                {
                    if(jQuery(rsp).find('status').text() == 'success')
                    {
                        jQuery('#modalCreateProfile .modal-footer').prepend('<p class="message success-message">Successfully saved!</p>');
                        location.reload();
                    }else{
                        jQuery('#modalCreateProfile .modal-footer').prepend('<p class="message error-message">' + jQuery(rsp).find('msg').text() + '</p>');
                    }
                },
                error: function(rsp){
                    jQuery('#modalCreateProfile .modal-footer').prepend('<p class="message error-message">' + rsp.responseJSON.message + '</p>');
                },
                complete: function(rsp){
                    jQuery('#create-profile-box .loading').hide();
                }
            })
        });

        $('#modalCreateProfile').on('hidden.bs.modal', function (e) {
            $('#modalCreateProfile .modal-body').html('<div class="block-loading"><div class="loading-content"><span class="loader"></span><div class="loading-text">LOADING DATA</div><div class="loading-wait">Please wait...</div></div></div>');
        })


    </script>
</div>
<div class="modal-footer">
    <a href="#" class="btn btn-success btn-with-icon btn-confirm">Confirm</a>
    <a href="#" class="btn btn-default btn-with-icon btn-cancel" data-dismiss="modal">Cancel</a>
</div>
<div class="block-loading" id="CreateProfileLoading"><div class="loading-content"><span class="loader"></span><div class="loading-text">LOADING</div><div class="loading-wait">Please wait...</div></div></div>
