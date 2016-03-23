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

            jQuery('.dropdown-submenu').hover(function() {
                jQuery(this).addClass('open');
            }, function() {
                jQuery(this).removeClass('open');
            });


            // Dropdown Menus
            jQuery(window).resize(function(){
                if (jQuery('#main-wrapper').width() - 1000 < 800) {
                    jQuery('.dashboard-menu').addClass('leftmenu');
                } else {
                    jQuery('.dashboard-menu').removeClass('leftmenu');
                }
            });

            if (jQuery('#main-wrapper').width() - 1000 < 800) {
                jQuery('.dashboard-menu').addClass('leftmenu');
            }

            jQuery('.dropdown-menu li').each(function () {
                if (jQuery(this).find('.dropdown-menu').length > 0) {
                    jQuery(this).addClass('dropdown-submenu');
                }
            });
        }
    }
}