//$(document).ready(function() {
jQuery(document).ready(function($) {
	///////////////////////////////////////////////////////
	//DOCUMENT METRICS
	///////////////////////////////////////////////////////
    
	 var docH = $(document).height();
	 var windowH = $(window).height();
	 var windowW = $(window).width();

	 
	///////////////////////////////////////////////////////
	//CONTENT POPUPS
	///////////////////////////////////////////////////////
	
	 $('.popup').live('click', function() { 
		 //Get the pop-up content
		 var Element = $(this);
		 //var imgSrc = ($(this).find('.popup-content').attr('src').split('src=')[1]).split('&')[0];
		 var imgSrc = (Element.attr('src'));
		 var imgHeight = Element.height();
		 var imgWidth = Element.width();
		 var leftOffset = -Number(Number(windowW /1.5) / 2);
		 var topOffset = Number($(window).scrollTop()) + Number(windowH * 0.15);
	
		//Deploy pop-up
		/*$('#mask').css({'height' : docH , 'width' : windowW}).fadeIn('fast');
		$('#popup-wrap').fadeIn('fast').css({'margin-top': topOffset, 'max-width':  Number(windowH * 0.8) +'px' }).append('<img id="popup-img-hidden" src="'+ imgSrc +'" /><img style="max-height: '+ Number(windowH * 0.8) +'px;" id="popup-content" src="'+ imgSrc +'" /><br /><div><span id="close-popup" class="blue_txt">CLOSE</span> | <span id="expand-popup" class="blue_txt">EXPAND</span></div>');
		*/
		$('#mask').css({'height' : docH , 'width' : windowW}).fadeIn('fast');		
		$('#popup-wrap').fadeIn('fast').css({
														'margin-top': topOffset, 
														'max-width':  '805px' 
														});
	 });
	 
	 /* Test Suite */
	 
	  $('.community_popup').live('click', function() { 
		 //Get the pop-up content
		 var Element = $(this);
		 //var imgSrc = ($(this).find('.popup-content').attr('src').split('src=')[1]).split('&')[0];
		 var imgSrc = (Element.attr('src'));
		 var imgHeight = Element.height();
		 var imgWidth = Element.width();
		 var leftOffset = -Number(Number(windowW /1.5) / 2);
		 var topOffset = Number($(window).scrollTop()) + Number(windowH * 0.15);
		 
		$('#mask_community').css({'height' : docH , 'width' : windowW}).fadeIn('fast');		
		$('#community-wrap').fadeIn('fast').css({
														'margin-top': topOffset, 
														'max-width':  '650px' 
														});
	 });
	 
	 /* Payment Method */
	 
	  $('.payment_popup').live('click', function() { 
		 //Get the pop-up content
		 var Element = $(this);
		 //var imgSrc = ($(this).find('.popup-content').attr('src').split('src=')[1]).split('&')[0];
		 var imgSrc = (Element.attr('src'));
		 var imgHeight = Element.height();
		 var imgWidth = Element.width();
		 var leftOffset = -Number(Number(windowW /1.5) / 2);
		 var topOffset = Number($(window).scrollTop()) + Number(windowH * 0.15);
		 
		$('#mask_payment').css({'height' : docH , 'width' : windowW}).fadeIn('fast');		
		$('#payment-wrap').fadeIn('fast').css({
														'margin-top': topOffset, 
														'max-width':  '650px' 
														});
	 });
	 
	 //Expand pop-up to fith original content size
	 $('#expand-popup').live('click', function() {
		var imgFH = $('#popup-img-hidden').height();
		var imgFW = $('#popup-img-hidden').width();	
		
		$('#popup-content').css('max-height','3000px').animate({'height': imgFH , 'width': imgFW });
		$('#popup-wrap').css('max-width','100%');
	 });
	 
	 //Close pop-up
	  $('#close-popup').live('click', function() {
		$('#mask').fadeOut('fast');
	 });
	 
	  $('#close-popup-community').live('click', function() {
		$('#mask_community').fadeOut('fast');
	 });
	 
	 $('#close-popup-community2').live('click', function() {
		$('#mask_community').fadeOut('fast');
	 });
	 
	 $('#close-popup-payment').live('click', function() {
		$('#mask_payment').fadeOut('fast');
	 });
	 
	///////////////////////////////////////////////////////
	// CLOSE THIS BUTTON
	///////////////////////////////////////////////////////
	
	//nu r inca optimizat pt live
	$('.close_this').live('click', function(){
		$(this).parent().fadeOut();
	});
	
	$('.close_this').parent().hover(function() {
		$(this).find('.close_this').toggle();
	});
	
	///////////////////////////////////////////////////////
	// AUTOHIDE THIS
	///////////////////////////////////////////////////////
	
	var ahDelay = 5000;
	$('.autohide').delay(ahDelay).fadeOut();
	
	
	///////////////////////////////////////////////////////
	// EXPANDABLE
	///////////////////////////////////////////////////////
	
	$('.exp_title').click(function(){
		$(this).toggleClass('open').next().slideToggle('fast');
	});
	
	///////////////////////////////////////////////////////
	// EXPANDABLE GRID
	///////////////////////////////////////////////////////
	
	$('#my_profile .expandable').click(function(){
		$(this).toggleClass('open');
		$(this).parent().parent().next().slideToggle('fast');
	});
	
	///////////////////////////////////////////////////////
	// TABS
	///////////////////////////////////////////////////////
		 
	$('.tabs a').click(function(){
		switch_tabs($(this));
		$(this).parent().addClass('active');
		$(this).parent().siblings().removeClass('active');
	});
 
	switch_tabs($('.defaulttab'));
	 
	function switch_tabs(obj)
	{
		$('.tab-content').hide();
		$('.tabs a').removeClass("selected");
		var id = obj.attr("rel");
	 
		$('#'+id).show();
		obj.addClass("selected");
	}
	
	/* tab Test Suite View */
	
	$('.tabs_sv a').click(function(){
		switch_tabs($(this));
		$(this).parent().addClass('active');
		$(this).parent().siblings().removeClass('active');
	});
 
	switch_tabs_sv($('.defaulttab'));
	 
	function switch_tabs_sv(obj)
	{
		$('.tab-content2').hide();
		$('.tabs_sv a').removeClass("selected");
		var id = obj.attr("rel");
	 
		$('#'+id).show();
		obj.addClass("selected");
	}
	
	/* Menu Level - 2 */
    var mEvent = 'click';
    if(isMobile())
        mEvent = 'touchstart';
                
	jQuery('.has_dd_1').on(mEvent, function(e){
        $('.normal_dd:not(.what_is)').removeClass('block');
        $('#menu-header_menu .menu-item:not(.has_dd_1) a').removeClass('hover');
        $('.what_is').toggleClass('block');
        $('.has_dd_1 a').toggleClass('hover');
        e.stopPropagation();
        return false;
        
    });
    jQuery('.has_dd_2').on(mEvent, function(e){
        $('.normal_dd:not(.why_compliance)').removeClass('block');
        $('#menu-header_menu .menu-item:not(.has_dd_2) a').removeClass('hover');
        $('.why_compliance').toggleClass('block');        
        $('.has_dd_2 a').toggleClass('hover');        
        e.stopPropagation();
        return false;
        
    });
    jQuery('.has_dd_3').on(mEvent, function(e){
        $('.normal_dd:not(.compliancetest_serv)').removeClass('block');
        $('#menu-header_menu .menu-item:not(.has_dd_3) a').removeClass('hover');
        $('.compliancetest_serv').toggleClass('block');
        $('.has_dd_3 a').toggleClass('hover');
        e.stopPropagation();
        return false;
        
    });
    jQuery('.has_dd_4').on(mEvent, function(e){
        $('.normal_dd:not(.help_faq)').removeClass('block');
        $('#menu-header_menu .menu-item:not(.has_dd_4) a').removeClass('hover');        
        $('.help_faq').toggleClass('block');
        $('.has_dd_4 a').toggleClass('hover');
        e.stopPropagation();
        return false;
        
    });
    jQuery('html').on(mEvent, function(e){
        $('.normal_dd').removeClass('block');
        $('#menu-header_menu .menu-item a').removeClass('hover');        
    })
					
	/* Certifications Sorf By */				
	jQuery('.sort_status').change(function() {
		var parts = window.location.search.substr(1).split("&");
		var $_GET = {};
		for (var i = 0; i < parts.length; i++) {
			var temp = parts[i].split("=");
			$_GET[decodeURIComponent(temp[0])] = decodeURIComponent(temp[1]);
		}
		
		var current_sort_status = $_GET['sort_status'];
		var sort_val = jQuery('.sort_status').val();
		var current_url = $(location).attr('href');
	
		var n=current_url.indexOf("sort_status");
		if (n !== -1){
			var replace_val = '&sort_status=' + current_sort_status
			url = current_url.replace(current_sort_status,"");
		  }
		  else{
			url = window.location + '&sort_status='
			  }

		var redirect_url = url + sort_val
		jQuery(location).attr('href',redirect_url);
	});		
	
	jQuery('.input_filter').change(function(){
		jQuery('#form_filter').submit();
		});
	jQuery('.input_filter2').change(function(){
		jQuery('#form_filter2').submit();
		});	
	
	jQuery('#save_attachment').click(function(){
		var files = jQuery('#attachment_group_id').val();
		if(!files){
			jQuery('.error_files').text('First Upload Files!');
				jQuery('.error_files').show('slow');
				errors = true;
			}
		});
        
        jQuery('input').keyup(function(){
            jQuery('.err').fadeOut();
        });
	
    
	/* Search */
	jQuery('#search_bar #s').focus(function() {
		jQuery('#s').removeClass('inactive_s');
		jQuery('#s').addClass('active_s');
	});

	
	jQuery('.search_select_div').click(function(event) {
		jQuery('.search_select ul li > ul').toggleClass('block');
		event.stopPropagation();
	});
	
	 jQuery('body').click(function(){
		$('.search_select ul ul').fadeOut().removeClass('block');	
	});
	
	jQuery('.search_select ul ul li a').click(function(data) {
		var sel_id=jQuery(this).attr('id');
		/*alert(sel_id);*/
		jQuery('#hidden_value').attr('value', sel_id);
		
		var sel_name=jQuery(this).text();
		jQuery('.current_chosen').replaceWith('<a id="'+sel_id+'" class="current_chosen">'+sel_name+'</a>');
	});
	
    
    
    /*---------------------------------------------
    search validate
    ---------------------------------------------*/
    //jQuery("#hidden_value").val('');
    
	jQuery('.search_select, #searchform').on('click submit', function(event) {
        var eventType = event.type;
        var search_value =  jQuery("#hidden_value").val();
        var search_text =  jQuery("#choose_one").text();
        
        var thisElem = jQuery(this);
        
        switch(eventType){
            case'submit':
                jQuery('#choose_one').removeClass('err_red').text('Tests / Products');
                
                if(search_value == ''){
                    //alert('submit');
                    //jQuery('.err_search').fadeIn();
                    jQuery('#choose_one').addClass('err_red').text('Choose a category!');
                    return false;
                }
                
            break;
                
            case'click':
                if(thisElem.hasClass('search_select')){
                    //jQuery('.err_search').fadeOut();
                    //jQuery('#choose_one').removeClass('err_red');
                    
                    jQuery('.search_select ul.block li').on('click', function(){
                        search_value =  jQuery("#hidden_value").val();
                        
                        if(search_value == ''){
                            //alert(search_value);
                            //jQuery('.err_search').fadeIn();
                            jQuery('#choose_one').addClass('err_red').text('Choose a category!');
                            
                            return false;
                        }
                    });
                }
            break;
        }
        
    });



	// Dashboard Add New Test Suite 
	
	jQuery('.add_new_lvl').click(function() {
			 jQuery('.copy-correct-lvl').append(jQuery('.conformance_level').html());
			// jQuery('.copy-correct-docs input').val('');
		});
		jQuery('.remove_lvl').live('click', function() {
			jQuery(this).parents('.conformance_level').remove();
		});
		
	jQuery('.add_new_test_case').click(function() {
			 jQuery('.copy-correct-test-cases').append(jQuery('.test_cases_associated').html());
		});
		jQuery('.remove_test_case').live('click', function() {
			jQuery(this).parents('.test_cases_associated').remove();
		});	
		
	jQuery('.add_new_test_suite').click(function() {
			 jQuery('.copy-correct-test-suites').append(jQuery('.test_suites_related').html());
		});
		jQuery('.remove_test_suite').live('click', function() {
			jQuery(this).parents('.test_suites_related').remove();
		});		
		
	jQuery('.add_new_document').click(function() {
			 jQuery('.copy-correct-documents').append(jQuery('.documents_associated').html());
		});
		jQuery('.remove_document').live('click', function() {
			jQuery(this).parents('.documents_associated').remove();
		});		

	
	jQuery('#save_ts').live('click', function() {
		var errors_ts = false;
		jQuery('input.req_field').each(function(){
			var input_value = jQuery(this).val();   
			if (input_value == ''){
				errors_ts = true;
			}
			
			


			});
			if(errors_ts){
				jQuery('.err_new_suite').text('Please fill in all fields!');
				jQuery('.err_new_suite').show('slow');
				return false;
				}
				else return true;

	});

	jQuery('.request-membership').on('click', function() {
		 var Element = $(this);
		 //var imgSrc = ($(this).find('.popup-content').attr('src').split('src=')[1]).split('&')[0];
		 var imgSrc = (Element.attr('src'));
		 var imgHeight = Element.height();
		 var imgWidth = Element.width();
		 var leftOffset = -Number(Number(windowW /1.5) / 2);
		 var topOffset = Number($(window).scrollTop()) + Number(windowH * 0.15);
		 
		jQuery('#mask_community').css({'height' : docH , 'width' : windowW}).fadeIn('fast');		
		jQuery('#community-wrap').fadeIn('fast').css({
														'margin-top': topOffset, 
														'max-width':  '650px' 
														});
		return false;
	
	});
	
	jQuery("#user_login").focus(function () {
		jQuery(".simple_tooltip_pop").fadeOut(1000);
	});
	
	jQuery("#user_pass").focus(function(){
		jQuery(".simple_tooltip_pop").fadeOut(1000);
	});
    
    
    
    /*---------------------------------------------
    my profile edits
    ---------------------------------------------*/      
   //transform divs in inputs at click on edit button
   jQuery(document).on('click', '.edit_btn', function(){
       
        var thisParentId = '#'+jQuery(this).parents('.default_grid').attr('id');
        var findInputs = jQuery(thisParentId+' .grid_row input:visible').size();

        //if( findInputs == 0){

        jQuery(thisParentId+' .profile_btn').fadeIn();
        
        //transform all divs in inputs
        jQuery(thisParentId+' .grid_cell.in_input').each(function(){
           var thisTextVal = jQuery(this).text(); 
           var thisNameVal = jQuery(this).attr('data-name'); 
           
           if(jQuery(this).hasClass('input_pass')){
                jQuery(this).replaceWith('<input type="password" name="'+thisNameVal+'" value=""/>');
                
           }else if(jQuery(this).hasClass('small_input')){
                if(jQuery(this).attr('data-name')=='card_expiry'){
                    jQuery(this).replaceWith('<input type="text" placeholder="M / Y" class="small_input" name="'+thisNameVal+'" value="'+thisTextVal+'"/>');
                }else{
                    jQuery(this).replaceWith('<input type="text" class="small_input" name="'+thisNameVal+'" value="'+thisTextVal+'"/>'); 
                }
           }else if(jQuery(this).hasClass('card_no')){
               var getthisTextVal = jQuery('input[name="card_no"]').val();
               
               jQuery(this).replaceWith('<input type="text" name="'+thisNameVal+'" value="'+getthisTextVal+'"/>');
           
           }else{
               jQuery(this).replaceWith('<input type="text" name="'+thisNameVal+'" value="'+thisTextVal+'"/>');
           }
        });
        
        
        /*}else{
           
           var countEmptyInputs = $(thisParentId+' .grid_row input:visible').filter(function(){return !$(this).val();}).length;
           //alert(countEmptyInputs);
           
           if(countEmptyInputs == 0){
                jQuery(thisParentId+' .profile_btn').hide();
                //transform all inputs in divs
                jQuery(thisParentId+' .grid_row input:visible').each(function(){
                    var thisTextVal = jQuery(this).val(); 
                    var thisNameVal = jQuery(this).attr('name');

                    if(jQuery(this).hasClass('input_pass')){
                         jQuery(this).replaceWith('<div class="class="grid_cell in_input input_pass"" name="'+thisNameVal+'">'+thisTextVal+'</div>');
                    }else if(jQuery(this).hasClass('small_input')){
                        jQuery(this).replaceWith('<div class="grid_cell in_input small_input" name="'+thisNameVal+'">'+thisTextVal+'</div>');
                    }else{
                         jQuery(this).replaceWith('<div class="grid_cell in_input" name="'+thisNameVal+'">'+thisTextVal+'</div>');
                    }
                });
           }
        }*/
        
    });
    
    //save my details updates
    jQuery(document).on('click', '.profile_btn', function(){
        
        var getThisForm = '#'+jQuery(this).parents('.default_grid').attr('id');
        jQuery('.errors_msg').hide();
        
        var options = {
            url: window.location.href,
            data: {user_id: jQuery('input[name="user_id"]').val()},
            success: function(data){
               if(data=='no_errors'){
                    location.reload(); 
               }else{
                   jQuery(getThisForm+' .errors_msg').text(data).fadeIn();
               }
            } 
        };
        $(getThisForm+' form').ajaxSubmit(options);
    });
    
    
    //change popups according to what button is pressed
    jQuery(document).on('click', '.add_user_btn', function(){
        jQuery('#dinamic_pop').show();
        jQuery('#registration').hide();
    });
    
    jQuery(document).on('click', '.register', function(){
        jQuery('#dinamic_pop').hide();
        jQuery('#registration').show();
    });
    
    /* Append Select Filter from Test-Suite Page
	* */
	jQuery('.append_filter').append(jQuery('.the_filters').html());

});

function isMobile()
{
    if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini|ZuneWP7/i.test(navigator.userAgent) ) {
        return true;
    }else{
        return false;
    }
}
