jQuery(document).ready(function($) {

    $('[data-tooltip]').tooltip({
        trigger: 'hover'
    });

    $('[data-validate="validate"]').each(function () {
        $(this).validate({
            errorElement: "span",
            errorPlacement: function ( error, element ) {
                // Add the `help-block` class to the error element
                error.addClass( "help-block" );

                if ( element.prop( "type" ) === "checkbox" ) {
                    error.insertAfter( element.parent( "label" ) );
                } else {
                    error.insertAfter( element );
                }

                if ( element.prop( "type" ) === "file" && element.parents('.upload-file-field').length )  {
                    error.appendTo( element.parents('.upload-file-field') );
                }

            },
            highlight: function ( element, errorClass, validClass ) {
                $( element ).parents( ".form-group" ).addClass( "has-error" );

                if ( $( element ).prop( "type" ) === "file" && $( element ).parents('.upload-file-field').length) {
                    $( element ).parents('.upload-file-field').addClass( "has-error" );
                }
            },
            unhighlight: function ( element, errorClass, validClass ) {
                $( element ).parents( ".form-group" ).removeClass( "has-error" );
            }

        });
    });

    $.ajaxSetup({
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
    });

    $('[data-save-method="ajax"]').submit(function (e) {
        e.preventDefault();
        Page.coloredBoxAjaxSaveForm($(this));
    });

    $('[data-ajax-modal]').click(function(e){
        e.preventDefault();
        Page.loadModalContent($(this));
    });

    $('#confirmRemoveMembership').on('show.bs.modal', function(e) {
        $(this).find('.btn-ok').attr('href', $(e.relatedTarget).data('href'));
    });

    $('[data-toggle-block]').click(function(e){
        e.preventDefault();
        $($(this).data('toggle-block')).toggle();
    });

    if ($.fn.redactor){
        $('.redactor_editor').redactor({
            minHeight: 80
        });
    }


    Page.header.init();

});

var Page = {
    header: {

        init: function() {
            this.dashboardMenu();
            this.headerLoginForm();
        },

        dashboardMenu:  function() {

            $('.dropdown-submenu').hover(function() {
                $(this).addClass('open');
            }, function() {
                $(this).removeClass('open');
            });

            $('.dropdown-menu li').each(function () {
                if ($(this).find('.dropdown-menu').length > 0) {
                    $(this).addClass('dropdown-submenu');
                }
            });

            var windowWidth = $('#main-wrapper').width();

            if (windowWidth <= 640){
                $('#header-dashboard-menu').find('.dropdown-menu').removeClass('dropdown-menu');
                var dashboardMenu = $('#header-dashboard-menu').detach();
                dashboardMenu.insertBefore('#navbar');

                $('#navbar .menu').wrap('<li id="mainMenuMobile"></li>');
                $('#mainMenuMobile').prepend('<a href="#" class="page-mobile-link">Pages</a>');
                var mainMenu = $('#mainMenuMobile').detach();
                dashboardMenu.append(mainMenu);

                $('.page-mobile-link').click(function(e){
                    e.preventDefault();
                    $(this).siblings('.sub-toggle').click();
                });

                $('#header-dashboard-menu').addClass('slimmenu').slimmenu({
                    collapserTitle: 'Menu'
                });

            }

        },

        headerLoginForm: function(){

            //Show/hide login popup form
            $('#headerLoginBtn').click(function(e){
                e.preventDefault();
                $('#headerLoginBlock').toggle();
            });

            //Hide login popup form if user click outside of the form
            $('html').click(function() {
                $('#headerLoginBlock').hide();
            });
            $('.header-login-block').click(function(e){
                e.stopPropagation();
            });

            //Replace default submit with ajax
            $('#headerLoginForm').submit(function(e){
                e.preventDefault();
                var form = $(this);
                var errorContainer = $('#header_login_error_msg');
                var loader = $('#headerLoginLoading');

                if(form.find('#user_login').val() == '' || form.find('#user_pass').val() == '')
                {
                    errorContainer.html('Please fill in all fields.').fadeIn('fast');
                    return false;
                }
                errorContainer.hide();
                loader.show();
                $.ajax({
                    url: "/",
                    type: 'post',
                    data: form.serialize() + '&cp-action=login',
                    dataType: 'json',
                    success: function(rsp)
                    {
                        loader.hide();
                        //Login Success
                        if(rsp['status'] == 'success')
                        {
                            //Goto Profile Page
                            document.location.href = rsp['redirect_to'];
                        }else{ //Error
                            //Show Error Message
                            if(rsp['message'] == 'Attempts limit has been reached') {
                                errorContainer.html('Attempts limit has been reached').fadeIn('fast');
                                document.location.href = '/login';
                            }
                            else if(rsp['message'] == 'FAILED_CAPTCHA'){
                                errorContainer.html('Captcha value is incorrect!').fadeIn('fast');
                            } else {
                                errorContainer.html('Wrong username or password, please try again!').fadeIn('fast');
                            }
                            if($('#recaptcha_reload')){
                                $('#recaptcha_reload').click();
                            }
                        }
                    }
                })

            });
        }
    },

    communities: {
        init: function(){
            var self = this;

            $('.joinCommunity').click(function(e){
                self.showJoinCommunityModal($(this), e);
            });
            $('.registerInCommunity').click(function(){
                self.submitCommunityRequest($(this));
            });
            $('.cancelMembershipInCommunity').click(function(){
                self.submitCancelMembership($(this));
            });
        },

        showJoinCommunityModal: function(el,e){
            e.preventDefault();
            var elAttr = {
                href: el.attr('href'),
                communityId: el.data('community-id')
            };
            $(elAttr.href).modal({
                backdrop: 'static'
            });
        },
        submitCommunityRequest: function(el){
            var communityId =  el.data('community-id');
            $('.error-message_' + communityId).hide();
            //@todo-ilya: Add loader and actions after response
            if($('#agree_community_terms_' + communityId).is(':checked')){
                $.ajax({
                    type: 'post',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    url : '/membership/'+communityId+'/request',
                    beforeSend: function () {
                        $('#confirmJoinCommunity' +  communityId + ' .block-loading').show();
                    },
                    success: function(){
                        location.reload();
                    },
                    complete: function () {
                        $('#confirmJoinCommunity' +  communityId + ' .block-loading').hide();
                        $('.modal').modal('hide');
                    }
                });
            } else {
                $('.error-message_' + communityId).show();
            }

        },

        submitCancelMembership: function (el) {
            var communityId =  el.data('community-id');
            $.ajax({
                type: 'delete',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                url : '/membership/'+communityId+'/leave',
                beforeSend: function () {
                    $('#confirmCancelMembership' +  communityId + ' .block-loading').show();
                },
                success: function(){
                    location.reload();
                },
                error: function (error, status, exception) {
                    location.href == '/communities/';
                },
                complete: function () {
                    $('.modal').modal('hide');
                }
            });

        }

    },

    communityDownloads: {

        init: function(){
            this.showUploadForms();
            this.hideUploadForms();
            this.addFileSection();
            this.removeFileSection();
            customizeFileTag();
        },

        showUploadForms: function(){
            $('#add-new-download').click(function(e){
                e.preventDefault();
                var showElementId = $(this).attr('href');
                $(this).parent().hide();
                $(showElementId).show();
            });
        },

        hideUploadForms: function(){
            $('#cancel-add-new-files').click(function(e){
                e.preventDefault();
                $('#add-new-item-section').hide();
                $('.add-new-item-default').show();
            });
        },

        addFileSection: function(){
            var self = this;
            var fileDescriptionTemplate = '<div class="file-description-section">' +
                '<div class="upload-file-field">' +
                    '<input type="file" name="file[]" class="input-file" />' +
                '</div>' +
                '<div class="file-description-fields">' +
                '<div class="form-horizontal">' +
                '<div class="form-group">' +
                '<label class="col-sm-3 control-label">File Version:</label>' +
                '<div class="col-sm-9">' +
                '<input type="text" class="form-control" name="file_version[]" value="" />' +
                '</div>' +
                '</div>' +
                '<div class="form-group">' +
                '<label class="col-sm-3 control-label">Description:</label>' +
                '<div class="col-sm-9">' +
                '<textarea cols="20" rows="5" name="file_description[]" class="form-control"></textarea>' +
                '</div>' +
                '</div>' +
                '<div class="form-group">' +
                '<label class="col-sm-3 control-label">File License Agreement:</label>' +
                '<div class="col-sm-9">' +
                '<textarea cols="20" rows="5" name="file_license[]" class="form-control"></textarea>' +
                '</div>' +
                '</div>' +
                '</div>' +
                '</div>' +
                '<a href="#" class="btn btn-danger btn-with-icon btn-delete">Remove</a>' +
                '</div>';

                $('#add-more-file').click(function(e){
                    e.preventDefault();
                    $('.form-actions').before(fileDescriptionTemplate);
                    self.removeFileSection();
                    customizeFileTag();
                });
        },
        removeFileSection: function(){
            $('.file-description-section .btn-delete').off('click').on('click', function(e){
                e.preventDefault();
                $(this).parents('.file-description-section').remove();
            });
        }

    },

    communityAdmin: {
        init: function(){
            var self = this;
            customizeFileTag();

            $('#addAddNewProfileType').click(function(e) {
                self.showAddNewProfileType(e);
            });

            $('#cancelAddingProfile').click(function(e) {
                self.hideAddNewProfileType(e);
            });
            $('#profileTypeForm').submit(function(e) {
                self.submitAddNewProfileType(e);
            });
            $('.profile-type-list .editProfileType').click(function(e) {
                self.editProfileType($(this), e);
            });
            $('.profile-type-list .deleteProfileType').click(function(e) {
                self.deleteProfileType($(this), e);
            });

        },

        showAddNewProfileType: function(e){
            this._clearProfileForm();
            e.preventDefault();
            $('#profileTypeList').hide();
            $('#addNewProfile').show();
        },

        hideAddNewProfileType: function(e){
            e.preventDefault();
            $('#addNewProfile').hide();
            $('#profileTypeList').show();
        },

        submitAddNewProfileType: function(e){
            e.preventDefault();
            var self = this;

            $('#profileTypeForm .error-message').remove();
            if($('#profile_type_file').val() == '' && $('#profile_type_text').val() == '')
            {
                $('#profileTypeForm .colored-box-footer').prepend('<p class="error-message">Please enter schema or select a schema file.</p>');
                return false;
            }

            $('#profileTypesSaving').show();

            jQuery('#profileTypeForm').ajaxSubmit({
                type: 'post',
                success: function (rsp) {
                    self.hideAddNewProfileType(e);
                    location.reload();
                }
            });

        },

        editProfileType: function(element, event){
            var self = this;
            event.preventDefault();
            self.hideAddNewProfileType(event);
            self.showAddNewProfileType(event);

            var link = element.attr('href');
            jQuery('#type_id').val(element.attr('data-id'));
            $.ajax({
                url: link,
                beforeSend: function() {
                    self._showLoader();
                },
                complete: function(){
                    self._hideLoader();
                },
                success: function(response)
                {
                    if (response.status == 'success'){
                        $('#profile_type_text').val(response.schema);
                    } else {
                        jQuery('#profileTypeForm .colored-box-footer').prepend('<p class="error-message">' + response.message + '</p>');
                    }
                },
                error: function(error){
                    jQuery('#profileTypeForm .colored-box-footer').prepend('<p class="error-message">' + error.responseText + '</p>');
                }
            })

        },

        deleteProfileType: function(element, event){
            var self = this;
            event.preventDefault();

            var profile = {
                id: element.data('profile-id'),
                name: element.data('profile-name'),
                version: element.data('profile-version'),
                link: element.attr('href')
            };

            if(!profile.name.length){
                profile.fullName = 'Profile type';
            } else {
                profile.fullName = profile.name + ' ' + profile.version;
            }

            jQuery('#profileTypesRemoving').show();
            jQuery.ajax({
                type: 'delete',
                url: profile.link,
                success: function (data) {
                    if (data.status == 'success') {
                        $('#profile-type-row-' + profile.id).addClass('removing').fadeTo("slow", 0.3, function () {
                            $(this).remove();
                            $('#profileTypeList').prepend('<div class="success-message">' + profile.fullName + ' has been removed</div>');
                            setTimeout(function () {
                                $('#profileTypeList > .success-message').slideUp(function () {
                                    $(this).remove();
                                });
                            }, 2000);
                        });
                    }
                },
                error: function (jqXHR, status) {
                    $('#profileTypeList').prepend('<div class="error-message">' + formatErrorMessage(jqXHR, status) + '</div>');
                    setTimeout(function () {
                        $('#profileTypeList > .error-message').slideUp(function () {
                            $(this).remove();
                        });
                    }, 2000);
                },
                complete: function () {
                    jQuery('#profileTypesRemoving').hide();
                }
            })

        },

        _clearProfileForm: function(){
            $('#profile_type_file').val('');
            $('#profile_type_text').val('');
            $('#type_id').val('');
            $('#profileTypeForm .error-message').remove();
        },

        _showLoader: function(){
            $('#profileTypesLoading').show();
        },

        _hideLoader: function(){
            $('#profileTypesLoading').hide();
        }

    },

    communityCreate: {
        init: function () {
            customizeFileTag();
        }
    },

    communityArticleManage: {
        init: function () {
            customizeFileTag();
            $('#addNewArticleFile').click(function (e) {
                e.preventDefault();
                var fileTemaple = '<div class="upload-file-field"><input type="file" name="attachments[]" class="input-file"  /></div>';
                $(this).before(fileTemaple);
                customizeFileTag();
            });

            $('.removeFileLink').click(function (e) {
                e.preventDefault();
                var elem = $(this);
                if (confirm("Are you sure?")) {
                    $.ajax({
                        url: $(this).attr('href'),
                        type: 'DELETE',
                        success: function(result) {
                            $(elem).parents('li').slideUp('slow', function () {
                               $(elem).remove();
                            });
                        }
                    });
                    return true;
                } else {
                    return false;
                }

            })

        }
    },

    /**
     * Saving submitted form
     * @param {JQuery} form Submitted form element
     */
    coloredBoxAjaxSaveForm: function(form){

        if (form.valid()){
            form.find('.message').remove();
            form.find('.color-box-loading').show();
            $.ajax({
                url: form.attr('action'),
                type: 'post',
                data: form.serialize(),
                error: function(jqXHR, status){
                    form.find('.colored-box-footer').prepend('<div class="message error-message">' + formatErrorMessage(jqXHR, status) + '</div>');
                },
                success: function(rsp, status, jqXHR){
                    if(form.attr('id') == 'invite-user-form'){
                        $('.invitations_table').append('<tr><td>' + rsp.data.invitation_email+ '</td>' +
                                            '<td>' +rsp.data.first_name+ ' ' +rsp.data.last_name+'</td>' +
                                            '<td>' + rsp.data.created_at +'</td>' +
                                            '<td></td>' +
                                        '</tr>');
                    }
                    if(jqXHR.status == 201){
                        form.find('.colored-box-footer').prepend('<div class="message success-message">'+rsp.message+'</div>');
                    } else {
                        form.find('.colored-box-footer').prepend('<div class="message success-message">Changes saved successfully.</div>');
                    }
                },
                complete: function(){
                    form.find('.color-box-loading').hide();
                    setTimeout(function() {
                        form.find('.message').fadeOut("slow", function() { $(this).remove(); });
                    }, 3000);

                }
            })

        } else {
            console.error('Form is not valid');
        }
    },

    /**
     * Load content to modal by ajax
     * @param {JQuery} el Submitted form element
     */
    loadModalContent: function(el) {
        var modalId = el.data('target');
        $(modalId).on("show.bs.modal", function(e) {
            $(modalId).data('bs.modal').options.keyboard = false;
            $(modalId).data('bs.modal').options.backdrop = 'static';
            var link = $(e.relatedTarget);
            $(this).find(".modal-content").load(link.attr("href"), function () {
                $(modalId).data('bs.modal').options.keyboard = true;
                $(modalId).data('bs.modal').options.backdrop = true;
                $(modalId).trigger("modalContentLoaded");
            });
            $(modalId).off("show.bs.modal");
        });
    },


    testCoverage: {
        loadTestCaseDetails: function () {
            Page.testCoverage.toggleExcludedFields($('#caseExclude'));

            $('#caseExclude').change(function () {
                Page.testCoverage.toggleExcludedFields($(this));
            });

        },

        toggleExcludedFields: function (el) {
            if(el.is(':checked')){
                $('.isExcluded').show();
            } else {
                $('.isExcluded').hide();
            }
        },

        validateTestCaseDetailsForm: function () {
            $('#testCaseDetailsForm').on('submit', function (e) {
                e.preventDefault();
                var form = $(this);
                if (form.find('#caseExclude').is(':checked')){
                    if (form.find('#caseExcludeReason').val().length > 0){
                        form.find('.modal-body .message').remove();
                        form.find('.block-loading').show();
                        $.ajax({
                            url: form.attr('action'),
                            type: 'post',
                            data: form.serialize(),
                            error: function(jqXHR, status){
                                form.find('.block-loading').hide();
                                form.find('.modal-body').append('<div class="message error-message">' + formatErrorMessage(jqXHR, status) + '</div>');
                            },
                            success: function(rsp){
                                form.find('.block-loading').hide();
                                form.find('.modal-body').append('<div class="message success-message">Changes saved successfully.</div>');
                                location.reload();
                            },
                            complete: function(){
                            }
                        })
                    } else {
                        form.find('#caseExcludeReason').after('<div class="message error-message">Reason is required</div>');
                    }
                }
            });
        },

        validateEditPlanForm: function () {
            $('#coverageEditPlanForm').on('submit', function (e) {
                e.preventDefault();
                var form = $(this);
                if (form.find('#availableProducts').val() || form.find('.level:checked').length == 0 || form.find('.level:checked').length == 0){
                    form.find('.modal-body .message').remove();
                    form.find('.block-loading').show();
                    $.ajax({
                        url: form.attr('action'),
                        type: 'post',
                        data: form.serialize(),
                        error: function(jqXHR, status){
                            form.find('.block-loading').hide();
                            form.find('.modal-body').append('<div class="message error-message">' + formatErrorMessage(jqXHR, status) + '</div>');
                        },
                        success: function(rsp){
                            form.find('.block-loading').hide();
                            form.find('.modal-body').append('<div class="message success-message">Changes saved successfully.</div>');
                            location.reload();
                        },
                        complete: function(){
                        }
                    })
                } else {
                    form.find('.modal-body').append('<div class="message error-message">Please complete all fields in the form.</div>');
                }
            });
        }

    }


};


function customizeFileTag()
{
    jQuery('input[type="file"]').each(function(){
        if(!jQuery(this).data('file-customized'))
        {
            jQuery(this).wrap('<span class="custom-file-tag"><span class="btn btn-primary btn-with-icon btn-browse"></span></span>');
            jQuery(this).parent().append('Browse');
            if(jQuery(this).data('file-type')) {
                jQuery(this).parent().before('<span class="file-label-wrap file-ext-restriction file-type-' + jQuery(this).data('file-type') + '"><span class="file-value">Choose File</span><span class="file-extensions">' + jQuery(this).data('file-extensions') + '</span></span>');
            } else {
                jQuery(this).parent().before('<span class="file-label-wrap file-type-default"><span class="file-value">Choose File</span></span>');
            }

            jQuery(this).change(function(){
                var fileName = jQuery(this).val();
                fileName = fileName.replace(/\\/g, "/");
                fName = fileName.substring(fileName.lastIndexOf("/") + 1, fileName.lastIndexOf("."));
                if(!fName)
                {
                    jQuery(this).parent().parent().find('.file-value').html('Choose File');
                }else{
                    fExt = fileName.substr(fileName.lastIndexOf("."));
                    if(fName.length > 16)
                    {
                        fName = fName.substr(0, 7) + "..." + fName.substring(fName.length - 6);
                    }
                    jQuery(this).parent().parent().find('.file-value').html(fName + fExt);
                }
            })
            jQuery(this).data('file-customized', 1);
        }
    })
}

function formatErrorMessage(jqXHR, exception) {

    if (jqXHR.status === 0) {
        return ('Not connected.\nPlease verify your network connection.');
    } else if (jqXHR.status == 404) {
        return ('The requested page not found. [404]');
    } else if (jqXHR.status == 405) {
        return ('Method Not Allowed. [405]');
    } else if (jqXHR.status == 422) {
        var error = jQuery.map(jqXHR.responseJSON, function(v){
            return v;
        }).join('<br>');
        return error;
    } else if (jqXHR.status == 201) {
        var message = jQuery.map(jqXHR.responseJSON, function(v){
            return v;
        }).join('<br>');
        return message;
    } else if (jqXHR.status == 500) {
        return ('Internal Server Error [500].');
    } else if (exception === 'parsererror') {
        return ('Requested JSON parse failed.');
    } else if (exception === 'timeout') {
        return ('Time out error.');
    } else if (exception === 'abort') {
        return ('Ajax request aborted.');
    } else {
        return ('Uncaught Error.\n' + jqXHR.responseText);
    }
}