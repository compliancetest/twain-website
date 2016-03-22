jQuery(document).ready(function($) {

    $('[data-tooltip="tooltip"]').tooltip();


    $('#confirmRemoveMembership').on('show.bs.modal', function(e) {
        $(this).find('.btn-ok').attr('href', $(e.relatedTarget).data('href'));
    });

});


var isTouchDevice = 'ontouchstart' in document.documentElement;