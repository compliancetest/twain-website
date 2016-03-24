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
    }
};
