jQuery(document).ready(function($) {

    $('.plans-title-list li label').click(function(){
        var previous = $('.plans-title-list li.active');
        var current = $(this).parent();

        previous.removeClass('active');
        current.addClass('active');

        setSiblings(current.index());

        var shift_size = getShiftedSize(current);

        moveSlider(shift_size);

        return false;
    });


    $('.plans-header-nav-prev').click(function(){
        var active = $('.plans-title-list li.active');
        if (active.index != 0){

            active.prev().find('label').click();

            return false;
        }

    });

    $('.plans-header-nav-next').click(function(){
        var active = $('.plans-title-list li.active');
        if (active.index != 0){

            active.next().find('label').click();

            return false;

        }

    });

});

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

    });

}