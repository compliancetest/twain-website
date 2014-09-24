jQuery(document).ready(function($) {

    $('.plans-title-list li label').click(function(){
        run($(this));
        updateURL();
        return false;
    });


    $('.plans-header-nav-prev').click(function(){
        var active = $('.plans-title-list li.active');
        if (active.index != 0){
            updateURL();
            active.prev().find('label').click();

            return false;
        }

    });

    $('.plans-header-nav-next').click(function(){
        var active = $('.plans-title-list li.active');
        if (active.index != 0){
            updateURL();
            active.next().find('label').click();

            return false;

        }

    });

});

function run(el){
    var previous = jQuery('.plans-title-list li.active');
    var current = el.parent();

    previous.removeClass('active');
    current.addClass('active');

    setSiblings(current.index());

    var shift_size = getShiftedSize(current);

    moveSlider(shift_size);
}

function setSiblings(index){
    jQuery('.plans-title-list li').removeClass('sibling_1 sibling_2');

    jQuery('.plans-title-list li:eq(' + (index+1) + ')').addClass('sibling_1');
    if (index != 0){
        jQuery('.plans-title-list li:eq(' + (index-1) + ')').addClass('sibling_1');
    }
    jQuery('.plans-title-list li:eq(' + (index+2) + ')').addClass('sibling_2');

    if (index > 1){
        jQuery('.plans-title-list li:eq(' + (index-2) + ')').addClass('sibling_2');
    }

}
/**
 * el is current element
 */
function getShiftedSize(el){
    var size = 0;
    jQuery('.plans-title-list li').each(function(){
        if (jQuery(this).index() < el.index()){
            size = size + jQuery(this).outerWidth();
        }
    });

    size = (-size + 361) - el.outerWidth()/2 ;
    return size;

}

function moveSlider(size){
    jQuery('.plans-title-list').animate({
        left: size
    }, 100, function() {
        showPlanDetails(jQuery('.plans-title-list li.active label'));
    });

}

function showPlanDetails(el){
    jQuery('.plan-content').hide();
    jQuery('#' + el.data('plan-container')).show().css({ opacity: 0.5 }).animate({ opacity: 1 });
}

function updateURL(){
    if( jQuery('.submit_all').attr('href').indexOf( 'plan_id' ) == -1 ){
        var new_url = jQuery('.submit_all').attr('href') + '&plan_id='+jQuery( '.plans-title-list li.active label').attr( 'data-plan-id');
    } else {
        var new_url = jQuery('.submit_all').attr('href').split('&plan_id');
        new_url = new_url[0] + '&plan_id='+jQuery( '.plans-title-list li.active label').attr( 'data-plan-id');
    }
    jQuery('.submit_all').attr( 'href', new_url );
    jQuery("a[rel='custom-popup']").off("click").cplightbox( {'href': new_url});
}