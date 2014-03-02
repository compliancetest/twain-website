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
	
	// Terms And Conditions Pop-Up
	
	$('#terms_co').click(function(){
		alert('test');
		
	});
		
	///////////////////////////////////////////////////////
	// TABS
	///////////////////////////////////////////////////////
		 
	$('.tabs:not(.no-ajax) a').click(function(){
		switch_tabs($(this));
		$(this).parent().addClass('active');
		$(this).parent().siblings().removeClass('active');
        return false;
	});
 
    if($('.defaulttab').size() > 0)
	    switch_tabs($('.defaulttab'));
	 
	function switch_tabs(obj)
	{
		$('#item-body > div').hide();
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
    if(isMobile()){
        mEvent = 'touchstart';
    }
                
	jQuery('.has_dd_1').on(mEvent, function(e){
        $('.normal_dd:not(.what_is)').removeClass('block');
        $('#menu-header_menu .menu-item:not(.has_dd_1) a').removeClass('hover');
        $('.what_is').toggleClass('block');
        $('.has_dd_1 a').toggleClass('hover');
        e.stopImmediatePropagation();
        return false;
        
    });
    jQuery('.has_dd_2').on(mEvent, function(e){
        $('.normal_dd:not(.why_compliance)').removeClass('block');
        $('#menu-header_menu .menu-item:not(.has_dd_2) a').removeClass('hover');
        $('.why_compliance').toggleClass('block');        
        $('.has_dd_2 a').toggleClass('hover');        
        e.stopImmediatePropagation();
        return false;
        
    });
    jQuery('.has_dd_3').on(mEvent, function(e){
        $('.normal_dd:not(.compliancetest_serv)').removeClass('block');
        $('#menu-header_menu .menu-item:not(.has_dd_3) a').removeClass('hover');
        $('.compliancetest_serv').toggleClass('block');
        $('.has_dd_3 a').toggleClass('hover');
        e.stopImmediatePropagation();
        return false;
        
    });
    jQuery('.has_dd_4').on(mEvent, function(e){
        $('.normal_dd:not(.help_faq)').removeClass('block');
        $('#menu-header_menu .menu-item:not(.has_dd_4) a').removeClass('hover');        
        $('.help_faq').toggleClass('block');
        $('.has_dd_4 a').toggleClass('hover');
        e.stopImmediatePropagation();
        return false;
        
    });
    if(isMobile())
    {
        jQuery('.has_dd_1, .has_dd_2, .has_dd_3, .has_dd_4, .what_is, .why_compliance, .compliancetest_serv, .help_faq').on('touchend', function(e){
           e.stopImmediatePropagation(); 
           return false;
        });
        jQuery('html').on('touchend', function(e){
            $('.normal_dd').removeClass('block');
            $('#menu-header_menu .menu-item a').removeClass('hover');        
        })
    }else{
        jQuery('html').on('click', function(e){
            $('.normal_dd').removeClass('block');
            $('#menu-header_menu .menu-item a').removeClass('hover');        
        })
    }
    
					
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
		jQuery('#s').removeClass('inactive_s').addClass('active_s');
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
		
		jQuery('#hidden_value').attr('value', sel_id);
		
		var sel_name=jQuery(this).text();
		jQuery('.current_chosen').replaceWith('<a id="'+sel_id+'" class="current_chosen">'+sel_name+'</a>');
	});
	
    
    
    /*---------------------------------------------
    search validate
    ---------------------------------------------*/
    //jQuery("#hidden_value").val('');
    
	jQuery('#searchform').on('submit', function(event) {
        var eventType = event.type;
        var search_value =  jQuery("#hidden_value").val();
        var search_text =  jQuery("#choose_one").text();
        
        if(search_value == ''){
            jQuery('#choose_one').addClass('err_red').text('Choose a category!');
            return false; 
        }
        
        if(search_value == 'test-suite')
        {
            jQuery(this).attr('action', '/test-suites');
        }else if(search_value == 'product-service'){
            jQuery(this).attr('action', '/products-and-services');
        }else if(search_value == 'communities'){
            jQuery(this).attr('action', '/communities');
            jQuery(this).find('#q').attr('name', 's');
        }
        
        return true;
    });

	

	// Dashboard Add New Test Suite 	
    jQuery('.dashboard-tabs li a').addClass('has-tooltip');
    
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
				jQuery('.err_new_suite').text('Please fill in all fields!').show('slow');
				return false;
				}
				else return true;

	});
    
    //change popups according to what button is pressed
    jQuery(document).on('click', '.add_user_btn', function(){
        jQuery('#dinamic_pop').show();
        jQuery('#registration').hide();
    });
    
    //Custom Popup Box
    jQuery("a[rel='custom-popup']").cplightbox();
    
    //Append loading div to grid boxes
    jQuery('.grid-box .btn-row').append('<div class="loading"><div><b>SAVING YOUR DATA</b><span>Please wait...</span></div></div>');
    
    //Datepicker
    if(jQuery('input.datepicker').length > 0)
    {
        jQuery('input.datepicker').datepicker({
            showOn: "both",
            buttonImage: "/wp-content/themes/bp-child/images/calendar-icon.png",
            buttonImageOnly: true,
            dateFormat: 'yy-mm-dd',
            option: "disabled"
        })
    }
    
    jQuery('a.submit-btn').click(function(){        
        jQuery(this).parents('form').submit();
        return false;
    });
    
    $('body').find('.grid-box-expandable').each(function(){            
        var table = $(this);
        table.find('.gbh-btn-expandable').click(function(){          
            if(table.hasClass('grid-box-closed'))      
                table.removeClass('grid-box-closed').addClass('grid-box-opened');
            else
                table.removeClass('grid-box-opened').addClass('grid-box-closed');
            table.find('.grid-box-body').animate({'height': 'toggle'});
        })
    })
    
    //Tab
    jQuery('.tabs-contr .tab-nav a').click(function(){
        if($(this).parent().hasClass('active'))
            return false;
        var rid = jQuery(this).attr('rel');
        jQuery('.tabs-contr > .tab-content:visible').hide();
        jQuery('.tabs-contr .tab-nav .active').removeClass('active');
        $(this).parent().addClass('active');
        $('#' + rid).show();
        return false;
    })
    
    //Focus Tooltip
    jQuery('.has-focus-tooltip input[type="text"], .has-focus-tooltip textarea').focus(function(){
        jQuery(this).parents('.has-focus-tooltip').find('.focus-tooltip').fadeIn('fast');
    })
    jQuery('.has-focus-tooltip input[type="text"], .has-focus-tooltip textarea').blur(function(){
        jQuery(this).parents('.has-focus-tooltip').find('.focus-tooltip').fadeOut('fast');
    })
    
    //Fix the footer Position
    if(jQuery('body').height() > jQuery('#wrapper').height())
    {
        jQuery('#content-pattern #content-wrapper').css('padding-bottom', jQuery('body').height() - jQuery('#wrapper').height());
    }        
    
    customizeFileTag();
    
    jQuery('.has-tooltip').each(function(){
        var tooltip_obj;
        if (jQuery(this).find('.simple_tooltip').length == 0) {
            tooltip_obj = '<span class="simple_tooltip radius6">' + jQuery(this).attr('title') + '<span></span></span>';
            jQuery(this).append(tooltip_obj);
            jQuery(this).attr('title', '');
        }
    });
    
    jQuery('.simple_tooltip').each(function(){
        jQuery(this).css({'top': -1 * jQuery(this).outerHeight() - 6});
    });
    
    // Browse Dashboard Pages
    jQuery('#browse-dashboard-pages').click(function(e){
        e.stopPropagation();
        jQuery(this).parent().addClass('open');
    });
    
    jQuery('body').click(function(e){
        jQuery('#browse-dashboard-pages').parent().removeClass('open');
    });
    
    // Dropdown Menus
    jQuery(window).resize(function(){
        if (jQuery('#wrapper').width() - 1000 < 800) {
            jQuery('#top_loged_actions .dropdown').addClass('leftmenu');
        } else {
            jQuery('#top_loged_actions .dropdown').removeClass('leftmenu');
        }
    });
    if (jQuery('#wrapper').width() - 1000 < 800) {
        jQuery('#top_loged_actions .dropdown').addClass('leftmenu');
    }
    jQuery('.dropdown-menu li').each(function(){
        if (jQuery(this).find('.dropdown-menu').length > 0) {
            jQuery(this).addClass('dropdown-submenu');
        }
    });
    
    jQuery('.dropdown').hover(function(){
        jQuery(this).addClass('open');
    }, function(){
        jQuery(this).removeClass('open');
    });
    
    jQuery('.dropdown-menu li').hover(function(){
        jQuery(this).find('> a').addClass('over');
    }, function(){
        jQuery(this).find('> a').removeClass('over');
    });
    
    jQuery('.dropdown-menu').each(function(){
        jQuery(this).find('>:first-child').addClass('first')
        jQuery(this).find('>:last-child').addClass('last')
    });
    
    // Redactor Textarea
    jQuery('textarea.redactor_area').redactor({
          air: true,
          minHeight: 80
    });
    jQuery('textarea.redactor_area_full').redactor({
          
          minHeight: 80
    });
    
});

function customizeFileTag()
{
    jQuery('input[type="file"]').each(function(){
        if(!jQuery(this).data('file-customized'))
        {
            jQuery(this).wrap('<span class="custom-file-tag"><span class="action-btn file-btn"></span></span>');
            jQuery(this).parent().append('<span class="p"></span><span class="t">Browse</span>');
            if(jQuery(this).attr('file-type'))
            {
                jQuery(this).parent().before('<span class="file-label-wrap file-type-' + jQuery(this).attr('file-type') + '"><span class="file-value">Choose File</span><span class="file-extensions">' + jQuery(this).attr('file-extensions') + '</span></span>');    
            }else{
                jQuery(this).parent().before('<span class="file-label-wrap file-label-oneline"><span class="file-value">Choose File</span></span>');                
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

function isMobile()
{
    if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini|ZuneWP7/i.test(navigator.userAgent) ) {
        return true;
    }else{
        return false;
    }
}

function showGridBoxLoadingWrapper(obj)
{
    if(typeof obj == 'string')
        jQuery('#' + obj).find('.loading').show();
    else
        jQuery(obj).find('.loading').show();
}
function hideGridBoxLoadingWrapper(obj)
{
    if(typeof obj == 'string')
        jQuery('#' + obj).find('.loading').hide();
    else
        jQuery(obj).find('.loading').hide();
}

function showGridBoxResultMessage(obj, message, type)
{
    if(typeof obj == 'string')
        obj = jQuery(obj);
    
    if(jQuery(obj).find('.btn-row .message').length > 0)
        jQuery(obj).find('.btn-row .message').remove();
    jQuery(obj).find('.btn-row').prepend('<div class="message ' + type + '">' + message + '</div>');
    jQuery(obj).find('.btn-row').find('.message').fadeIn('fast');
}
function hideGridBoxResultMessage(obj)
{
    if(typeof obj == 'string')
        obj = jQuery(obj);
    
    if(jQuery(obj).find('.btn-row .message').length > 0)
        jQuery(obj).find('.btn-row .message').fadeOut('fast', function(){
            jQuery(this).remove();
        });
    
}

function fixTdHeight(table)
{
    jQuery(table).find('.tr:visible').each(function(){
        var h = 0;
        jQuery(this).find('.td').each(function(){
            h = Math.max(jQuery(this).height(), h);
        })
        jQuery(this).children('.td').height(h);
    });
}

function isValidDate(input) {
    var bits = input.split('-');
    var d = new Date(bits[0], bits[1] - 1, bits[2]);
    return d.getFullYear() == bits[0] && (d.getMonth() + 1) == bits[1] && d.getDate() == Number(bits[2]);
}

function isValidUrl(str) {
    if (str.length == 0) {
        return true;
    }
    var pattern = new RegExp('^(https?:\\/\\/)?'+ // protocol
        '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|'+ // domain name
        '((\\d{1,3}\\.){3}\\d{1,3}))'+ // OR ip (v4) address
        '(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*'+ // port and path
        '(\\?[;&a-z\\d%_.~+=-]*)?'+ // query string
        '(\\#[-a-z\\d_]*)?$','i'); // fragment locator
    if(!pattern.test(str)) {
        return false;
    } else {
        return true;
    }
}