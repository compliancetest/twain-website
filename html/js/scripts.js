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

    communityDownloads: {

        init: function(){
            this.showUploadForms();
            this.addFileSection();
            this.removeFileSection();
        },

        showUploadForms: function(){
            $('#add-new-download').click(function(e){
                e.preventDefault();
                var showElementId = $(this).attr('href');
                $(this).parent().hide();
                $(showElementId).show();
            });
        },

        addFileSection: function(){
                var self = this;

                $('#add-more-file').click(function(e){
                    e.preventDefault();
                    var sectionStructure = $('.file-description-section').first().clone();
                    sectionStructure.find("input,textarea").val("");
                    $('.form-actions').before(sectionStructure);
                    self.removeFileSection();
                });
        },
        removeFileSection: function(){
            $('.file-description-section .btn-delete').off('click').on('click', function(e){
                e.preventDefault();
                $(this).parents('.file-description-section').remove();
            });
        }

    }


};
