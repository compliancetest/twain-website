jQuery(document).ready(function($) {

    $('[data-tooltip="tooltip"]').tooltip();


    $('#confirmRemoveMembership').on('show.bs.modal', function(e) {
        $(this).find('.btn-ok').attr('href', $(e.relatedTarget).data('href'));
    });

    $('[data-toggle-block]').click(function(e){
        e.preventDefault();
        $($(this).data('toggle-block')).toggle();
    });

    Page.header.dashboardMenu();

});

var isTouchDevice = 'ontouchstart' in document.documentElement;

var Page = {
    header: {

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
                var dashboardMenu = $('#header-dashboard-menu').detach();
                dashboardMenu.insertBefore('#navbar');
            }

        }
    },

    communities: {
        init: function(){
            var self = this;

            $('.joinCommunity').click(function(){
                self.showJoinCommunityModal($(this));
            });

            $('#registerInCommunity').click(function(){
                self.submitCommunityRequest($(this));
            });
        },

        showJoinCommunityModal: function(el){
            var elAttr = {
                href: el.attr('href'),
                communityId: el.data('community-id')
            };
            $('#registerInCommunity').data('community-id', elAttr.communityId);
            $(elAttr.href).modal();
        },
        submitCommunityRequest: function(el){
            var communityId =  el.data('community-id');
            //@todo-ilya: Add loader and actions after response
            alert('todo: Send ajax request');
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
                $('#add-new-download-section').hide();
                $('.add-new-download-default').show();
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
            this.groupMembersActions();
            customizeFileTag();

            $('.redactor_editor').redactor({
                minHeight: 80
            });

        },

        groupMembersActions: function(){
            $('#membersGroupAction a').click(function(e){
                e.preventDefault();
                var action = $(this).data('action');
                $('input[name="action"]').val(action);
                $('#groupMembersForm').submit();
            })
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


function getRedactorSettings(){

}