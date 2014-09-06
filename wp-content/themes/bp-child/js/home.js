jQuery(function($) {
    $('.customer-types-section').tabs();
    $('.customers-carousel').lemmonSlider({
        'infinite' : false
    });

    /*---Community slider---*/
    $('.communities-list li:first-child').show();
    $('.community-pager li:first-child').addClass('active');


    $('.community-pager li').on('click',function(){
        $('.community-pager li').removeClass('active');
        var active_community = $(this).index();
        console.log(active_community);
        $(this).addClass('active');
        $('.communities-list li').hide();
        $('.communities-list li:eq('+ active_community +')').show();
    });

});