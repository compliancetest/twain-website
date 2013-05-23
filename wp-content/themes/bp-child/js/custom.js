$(document).ready(function() {

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
	
	$('.expandable').click(function(){
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
	
	/* Placeholders */
	
	/* User & Email: */
	$("#user_login").attr("onblur", "if (this.value == '') this.value = 'E-mail or User';");
	$("#user_login").attr("onfocus", "if (this.value == 'E-mail or User') this.value = '';");
	$("#user_login").attr("value", "E-mail or User");
	/* Pass */
	$("#user_pass").attr("onblur", "if (this.value == '') this.value = 'Password';");
	$("#user_pass").attr("onfocus", "if (this.value == 'Password') this.value = '';");
	$("#user_pass").attr("value", "Password");
	
	
	/* User & Email: */
	$("#user_login2").attr("onblur", "if (this.value == '') this.value = 'E-mail or User';");
	$("#user_login2").attr("onfocus", "if (this.value == 'E-mail or User') this.value = '';");
	$("#user_login2").attr("value", "E-mail or User");
	/* Pass */
	$("#user_pass2").attr("onblur", "if (this.value == '') this.value = 'Password';");
	$("#user_pass2").attr("onfocus", "if (this.value == 'Password') this.value = '';");
	$("#user_pass2").attr("value", "Password");
	
	/* Menu Level - 2 */
	
	jQuery('.has_dd_1 , .what_is').hover(function() {
				jQuery('.submenu').addClass('block');
				jQuery('.what_is').addClass('block');
				//alert ('1111');
			}, function() {
					jQuery('.what_is').removeClass('block');
					jQuery('.submenu').removeClass('block');
					});

	jQuery('.has_dd_2 , .why_compliance').hover(function() {
				jQuery('.submenu').addClass('block');
				jQuery('.why_compliance').addClass('block');
				//alert ('1111');
			}, function() {
					jQuery('.why_compliance').removeClass('block');
					jQuery('.submenu').removeClass('block');
					});
					
	jQuery('.has_dd_3 , .compliancetest_serv').hover(function() {
				jQuery('.submenu').addClass('block');
				jQuery('.compliancetest_serv').addClass('block');
				//alert ('1111');
			}, function() {
					jQuery('.compliancetest_serv').removeClass('block');
					jQuery('.submenu').removeClass('block');
					});		
							
	jQuery('.has_dd_4 , .help_faq').hover(function() {
				jQuery('.submenu').addClass('block');
				jQuery('.help_faq').addClass('block');
				//alert ('1111');
			}, function() {
					jQuery('.help_faq').removeClass('block');
					jQuery('.submenu').removeClass('block');
					});						
					
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
		
    //register form validation    
	jQuery('#reg_user').on('click', function(){
		var firstname = jQuery("#first_name_id").val();
		var lastname =	jQuery("#last_name_id").val();
		var email = jQuery("#email_id").val();
		var user = jQuery("#user_login_id").val();
		var organisation = jQuery("#organisation_id").val();
		var contact_phone = jQuery("#contact_phone_id").val();
		var user_pass = jQuery("#user_pass_id").val();
		var user_pass_confirm = jQuery("#user_pass_confirm_id").val();
		var captcha_reg = jQuery("#captcha_reg").val();
		var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
		var errors = true;
        
        
		if(firstname && lastname && email && user && organisation && contact_phone && user_pass && user_pass_confirm && captcha_reg) {
            
			if (user_pass != user_pass_confirm){
				jQuery('.err').text('Passwords don\'t match! ');
				jQuery('.err').show('slow');
				errors = false;
			}
			if(!emailReg.test(email)){  
				jQuery('.err').text('Please enter a valid e-mail address!');
				jQuery('.err').show('slow');
				errors = false;
			}
			
            if (!jQuery('#acc_tc_id').is(':checked')) {
				jQuery('.err').text('You must agree to our Terms & Conditions first!');
				jQuery('.err').show('slow');
				errors = false;
			}
            
            if(errors){

                $.ajax({
                    url: '?check_captcha=1',
                    type: "POST",
                    async: false,
                    data: {captcha: $('#captcha_reg').val(), check_captcha: 1},
                    success: function(data) {
                        if (data == 'success') {
                            
                            jQuery('.loader').fadeIn();
                            
                            var options = {
                                url:'',
                                success: function() { 
                                    //alert('Thanks for your comment!'); 
                                    jQuery('#wrap_forms').hide();
                                    jQuery('.reg_message').fadeIn();
                                } 
                            };
                            $('#formreg').ajaxSubmit(options);
                            
                        } else {
                            jQuery('.err').text('Captcha not correct!');
                            jQuery('.err').show('slow');
                        }
                    }
                });
                
            }else{
                //alert('error');
            }
                 
		}else{
			jQuery('.err').text('Please fill in all fields!');
			jQuery('.err').show('slow');
			return false;
		}
        
        
    });	
		
	/*$('#formreg').submit(function(){
		var allow_submit = false;
		$.ajax({
			url: '?check_captcha=1',
			type: "POST",
			async: false,
			data: {captcha: $('#captcha_reg').val(), check_captcha: 1},
			success: function(data) {
				if (data == 'success') {
					allow_submit = true;
				} else {
					jQuery('.err').text('Captcha not correct!');
					jQuery('.err').show('slow');
				}
			}
		});
        
        if(allow_submit){
            $('#formreg').ajaxForm();
        }else{
            
            return false;
        }
        
		//return allow_submit;
	});*/
	
    
    //replace login button
    jQuery('#logform .login-submit').remove();
    jQuery('#logform .login-password').after('<div id="wp-submit">Login</div><div class="loader2"></div>');
    jQuery('#logform .login-password').after('<div class="reg_message log_msg wr_log_msg">Wrong username or password, please try again!</div>');
    
    jQuery('#wp-submit').on('click', function(){
        jQuery('.loader2').fadeIn();
        
        var options = {
            url:'', 
            data: {user_log: 1},
            success: function(data) {
                if(data == 'active'){
                    jQuery('#logform').submit();
                }else if(data == 'inactive'){
                    jQuery('#wrap_forms, .loader2').hide();
                    jQuery('.log_msg').fadeIn();
                    
                }else if(data == 'wrong'){
                    jQuery('.loader2').hide();
                    jQuery('.wr_log_msg').fadeIn();
                }
            } 
        };
        $('#logform').ajaxSubmit(options);

    });
    
    jQuery('#wp-submit2').on('click', function(){
        
        var options = {
            url:'', 
            data: {user_log: 1},
            success: function(data) {
                if(data == 'active'){
                    jQuery('#top_access').submit();
                }else if(data == 'inactive'){
                    jQuery('.log_err2').fadeIn();
                    
                }else if(data == 'wrong'){
                    jQuery('.log_err1').fadeIn();
                }
            } 
        };
        
        $('#top_access').ajaxSubmit(options);
        
        //do not submit the form
        return false;
    });
    
    
	/* Search */
	jQuery('#s').focus(function() {
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
	
    //search validate
    
    jQuery("#hidden_value").val('');
    
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
	
	jQuery("#user_pass").focus(function () {
		jQuery(".simple_tooltip_pop").fadeOut(1000);
	});

});
