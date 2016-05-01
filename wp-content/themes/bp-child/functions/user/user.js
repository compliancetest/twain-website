//Manage User Login And Registration
(function($){
    $(document).ready(function(){
        //Terms and Conditions
        $('#agree_terms').cplightbox({});
        $('#site-terms-box .cancel-btn').cplightbox({
            onLoad: function(){
                $('#acc_tc_id').prop('checked', false);
                $(window).scrollTop(0);
            }
        });
        $('#site-terms-box .process-btn').cplightbox({
            onLoad: function(){
                $('#acc_tc_id').prop('checked', true);
                $(window).scrollTop(0);
            }
        });
        
        //Add Placeholder
        $("#user_login, #user_login2").attr("placeholder", "E-mail or User");
        $("#user_pass, #user_pass2").attr("placeholder", "********");
        $("#user_login").focus(function () {
            $(".simple_tooltip_pop").fadeOut('fast');
        });
        
        $("#user_pass").focus(function(){
            $(".simple_tooltip_pop").fadeOut('fast');
        });
        
        
        //Add Loading Wrapper to Top Login Form
        $('#top_access').append('<div class="loading1"></div>');
        //Header Login Form
        $('#top_access').on('submit', function(){                        
            var form = $(this);
            if(form.find('#user_login').val() == '' || form.find('#user_pass').val() == '')
            {
                $('#header_login_error_msg').html('<span></span>Please fill in all fields.');
                $('#header_login_error_msg').fadeIn('fast');
                return false;
            }
            $('#header_login_error_msg').hide();
            $('#top_access .loading1').show();
            $.ajax({
                url: "/",
                type: 'post', 
                data: form.serialize() + '&cp-action=login',
                dataType: 'json',
                success: function(rsp)
                {
                    $('#top_access .loading1').hide();
                    if(rsp['status'] == 'success') //Login Success
                    {
                        //Goto Profile Page
                        //document.location.href = form.find('input[name="redirect_to"]').val();
                        document.location.href = rsp['redirect_to'];
                    }else{ //Error                    
                        //Show Error Message
                        if(rsp['message'] == 'Attempts limit has been reached') {
                            $('#header_login_error_msg').html('<span></span>Attempts limit has been reached');
                            $('#header_login_error_msg').fadeIn('fast');
                            document.location.href = '/login';
                        }
                        else if(rsp['message'] == 'FAILED_CAPTCHA'){
                            $('#header_login_error_msg').html('<span></span>Captch value is incorrect!');
                            $('#header_login_error_msg').fadeIn('fast');
                        } else {
                            $('#header_login_error_msg').html('<span></span>Wrong username or password, please try again!');
                            $('#header_login_error_msg').fadeIn('fast');
                        }
                        if(jQuery('#recaptcha_reload')){
                            jQuery('#recaptcha_reload').click();
                        }
                    }
                }
            })
            
            return false;
        });
        
        //Login Form on the Popup
        $('#logform .login-submit').after('<div class="loader2"></div>');
        $('#logform .login-password').after('<div class="reg_message log_msg wr_log_msg">Wrong username or password, please try again!</div>');
        
        $('#logform').on('submit', function(){                        
            var form = $(this);
            var msgObj = $('#registration-popup #log .message');
            if(form.find('#user_login2').val() == '' || form.find('#user_pass2').val() == '')
            {
                msgObj.removeClass('success').addClass('error').html('Please fill in all fields.').fadeIn('fast');
                return false;
            }
            msgObj.hide();
            $('.loading').show();
            $.ajax({
                url: "/",
                type: 'post', 
                data: form.serialize() + '&cp-action=login',
                dataType: 'json',
                success: function(rsp)
                {
                    if(rsp['status'] == 'success') //Login Success
                    {
                        //Goto Profile Page
                        //document.location.href = form.find('input[name="redirect_to"]').val();
                        document.location.href = rsp['redirect_to'];
                    }else{ //Error                    
                        //Show Error Message
                        msgObj.removeClass('success').addClass('error').html('Wrong username or password, please try again!').fadeIn('fast');

                    }  
                    $('.loading').hide();
                }
            })
            
            return false;
        });
        
        //User Register
        $('#reg_user').on('click', function(){
            
            $('#registration-popup #reg input.input-error').removeClass('input-error');
            
            var form = $('#formreg');
            var firstname = $("#first_name_id").val();
            var lastname =    $("#last_name_id").val();
            var email = $("#email_id").val();
            var email_confirm = $("#confirm_email_id").val();
            var user = $("#user_login_id").val();
            var organisation = $("#organisation_key").val();
            var contact_phone = $("#contact_phone_id").val();
            var user_pass = $("#user_pass_id").val();
            var user_pass_confirm = $("#user_pass_confirm_id").val();
            var captcha_reg = $("#recaptcha_response_field").val();
            var emailReg = /^([\w-+\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
            var noError = true;
            
            var msgObj = $('#registration-popup #reg .message');
            if(firstname && lastname && email && user /*&& organisation*/ && contact_phone && user_pass && user_pass_confirm && captcha_reg) {
                if (email != email_confirm) {
                    msgObj.hide();
                    msgObj.removeClass('success').addClass('error').html('Emails don\'t match!').fadeIn('fast');
                    noError = false;
                    $("#email_id, #confirm_email_id").addClass('input-error');
                    return false;                    
                }
                if (user_pass != user_pass_confirm){
                    msgObj.hide();
                    msgObj.removeClass('success').addClass('error').html('Passwords don\'t match!').fadeIn('fast');
                    noError = false;
                    $("#user_pass_id, #user_pass_confirm_id").addClass('input-error');
                    return false;                    
                }
                if(!emailReg.test(email)){  
                    msgObj.hide();
                    msgObj.removeClass('success').addClass('error').html('Please enter a valid e-mail address!').fadeIn('fast');
                    noError = false;
                    $("#email_id, #confirm_email_id").addClass('input-error');
                    return false;
                }

                if(!is_password_valid(user_pass)){
                    msgObj.hide();
                    msgObj.removeClass('success').addClass('error').html('Invalid password!').fadeIn('fast');
                    noError = false;
                    $("#user_pass_id, #user_pass_confirm_id").addClass('input-error');
                    return false;
                }
                
                if (!$('#acc_tc_id').is(':checked')) {
                    msgObj.hide();
                    msgObj.removeClass('success').addClass('error').html('You must agree to our Terms & Conditions first!').fadeIn('fast');
                    return false;
                    noError = false;
                }
                
                if(noError){
                    //Send Ajax
                    $('.loading').show();
                    msgObj.hide();
                    $.ajax({
                        url: "/",
                        type: "POST",
                        data: form.serialize(),
                        success: function(data) {
                            $('.loading').hide();
                            if (data == 'success') {
                                msgObj.removeClass('error').addClass('success').html('Thanks for your registration. A confirmation email has been sent to your email account. Please verify your email address using the link it contains.').fadeIn('fast');
                                setTimeout(function () {
                                    location.reload();
                                }, 4000);
                            } else if (data == 'success1') {
                                msgObj.removeClass('error').addClass('success').html('Thanks for your registration.').fadeIn('fast');
                                setTimeout(function(){
                                    location.href = '/';
                                },3000);
                            } else if(data == 'captcha_error') {
                                jQuery('#recaptcha_reload').click();
                                msgObj.removeClass('success').addClass('error').html('Captcha not correct!').fadeIn('fast');
                            } else {
                                jQuery('#recaptcha_reload').click();
                                msgObj.removeClass('success').addClass('error').html(data).fadeIn('fast');
                            }
                        }
                    });
                    
                }
                     
            }else{
                $('#registration-popup #reg input.required').each(function(){
                    if (this.value == '') {
                        $(this).addClass('input-error');
                    }
                })
                if ($("#recaptcha_response_field").val() == '') {
                    $("#recaptcha_response_field").addClass('input-error');
                }
                msgObj.hide();
                msgObj.removeClass('success').addClass('error').html('Please fill in all fields!').fadeIn('fast');
                return false;
            }
            
            
        });

        function is_password_valid(password){
            var e = /.{8,}/g.test(password); //At least 8 chars
            var a = /[0-9]+/g.test(password); //numeric
            var b = /[A-Z]+/g.test(password); //Capitals
            var c = /[a-z]+/g.test(password); //small letters
            var d = /[~`!#$%\^&*+=\-\[\]\\';,|\\":<>\?]/g.test(password); //special chars
            if (a && b && c && d && e ) {// Success
                return true;
            }
            return false;
        }

        //resend email verification
        $('#resend_email_verification').on('click', function(){            
            var getThis = $(this);
            $.ajax({
                url: getThis.attr('href'),
                type: 'POST',                
                success: function(data){
                    getThis.parent().parent().html('Email successfully sent! Please check your email address to verify your email');
                }
            });
            return false;
        });
        
       //transform divs in inputs at click on edit button
       $('#my_profile').on('click', '.gbh-btn-edit', function(){           
            var thisParentId = '#'+$(this).parents('.grid-box').attr('id');
            var findInputs = $(thisParentId+' .grid-row input:visible').size();
            var timezoneText = $(".timezone-text");
            jQuery(thisParentId).find('.message').remove();
           $('#my_profile .gbh-btn-edit').hide();
            if( findInputs == 0){

                $(thisParentId+' .btn-row').fadeIn();
                 $(thisParentId).addClass('grid-box-editing');
                $('.current_password').show();
                //transform all divs in inputs
                $(thisParentId+' .grid-cell.in_input').each(function(){                    
                   var thisTextVal = $(this).attr('data-value'); 
                   var thisNameVal = $(this).attr('data-name'); 
                   if($(this).attr('data-placeholder'))
                       var thisPlaceholderValue = $(this).attr('data-placeholder');
                   else
                       var thisPlaceholderValue = '';
                   
                   if($(this).attr('data-type'))
                       var dataType = $(this).attr('data-type');
                   else
                       var dataType = 'text';

                    if($(this).attr('data-title'))
                        var dataTitle = $(this).attr('data-title');
                    else
                        var dataTitle = '';

                   if(dataType == 'textarea')
                       $(this).after('<textarea name="'+thisNameVal+'" placeholder="' + thisPlaceholderValue + '" class="textarea">' + thisTextVal + '</textarea>');
                   else if(dataType == 'readonly')
                       $(this).after('<input type="text" value="'+thisTextVal+'" readonly="readonly" disabled="disabled" />');
                   else {
                       if(dataTitle){
                           $(this).after('<div class="has-field-tooltip">' +
                               '<input type="' + dataType + '" name="' + thisNameVal + '" value="' + thisTextVal + '" data-tooltip-content="' + dataTitle + '" class="field-tooltip" autocomplete="off">' +
                               '<span class="simple_tooltip" style="width: 380px; margin-left: -115px; bottom: 30px; display: none;">'+dataTitle+'<span></span></span>' +
                           '</div>');
                           $('.field-tooltip').on('focus', function(){
                               $(this).parent().find('.simple_tooltip').show();
                           }).blur(function(){
                               $(this).parent().find('.simple_tooltip').hide();
                           });
                       } else {
                           $(this).after('<input type="' + dataType + '" name="' + thisNameVal + '" value="' + thisTextVal + '" placeholder="' + thisPlaceholderValue + '" autocomplete="off"/>');
                       }
                   }

                   $(this).hide();
                });        
                $(thisParentId).find('.grid-hidden-row').show();
            }
            
            if ($(thisParentId).find('#timezone').length > 0) {
                $("#timezone").val(timezoneText.attr('data-value'));
                timezoneText.hide();
                $("#timezone").show();
            }
            
            if ($(thisParentId).find('#dashboard-pages').length > 0) {
                $("#dashboard-pages").show();
                
                $('#dashboard-page-path').data('title', $('#dashboard-page-path').html());
                $('input[name=dashboard_page_url]').data('title', $('input[name=dashboard_page_url]').val());
                $('input[name=dashboard_page_title]').data('title', $('input[name=dashboard_page_title]').val());
                
                $('#dashboard-pages .dashboard-pages-dropdown a').click(function(){
                    $('input[name=dashboard_page_url]').val($(this).attr('href'));
                    $('input[name=dashboard_page_title]').val($(this).data('title'));
                    $('#dashboard-page-path').html($(this).data('title'));
                    $('#dashboard-pages').removeClass('open');
                    
                    return false;
                });
            }
           return false;
        });

        //Edit Cancel
        $('#my_profile').on('click', '.edit-cancel-btn', function(){
            var thisParentId = '#'+$(this).parents('.grid-box').attr('id');
            var findInputs = $(thisParentId+' .grid-row input:visible').size();            
            $(thisParentId).find('.message').remove();
            //if( findInputs == 0){
            $('#my_profile .gbh-btn-edit').show();
            $('.current_password').hide();
            $(thisParentId+' .btn-row').fadeIn();
             $(thisParentId).addClass('grid-box-editing');
            //transform all divs in inputs
            $(thisParentId+' .grid-cell.in_input').each(function(){
                if ($(this).attr('data-type') == 'skip') {
                    $(this).parent().show();                    
                } else {
                    $(this).next().remove();                
                    $(this).show();
                }                
            });
            
            $(thisParentId).find('.grid-hidden-row').hide();
            
            $(thisParentId+' .btn-row').hide();

            $("#timezone").hide();
            $(".timezone-text").show();
            $("#dashboard-pages").hide();
            
            $('#dashboard-page-path').html($('#dashboard-page-path').data('title'));
            $('input[name=dashboard_page_url]').val($('input[name=dashboard_page_url]').data('title'));
            $('input[name=dashboard_page_title]').val($('input[name=dashboard_page_title]').data('title'));

            return false;
        });


        //save my details updates
        $('#my_profile').on('click', '.process-btn', function(){

            var form = $(this).parents('form');
            form.find('.errors_msg').hide();
            showGridBoxLoadingWrapper(form);
            hideGridBoxResultMessage(form);
            $.ajax({
                url: '/my-profile/',
                data: form.serialize(),
                type: 'POST',
                success: function(rsp)
                {
                    hideGridBoxLoadingWrapper(form);
                    if(rsp == 'success') {
                        $('#my_details .btn-row a').hide();
                        showGridBoxResultMessage(form, 'Successfully Saved!', 'success');
                        document.location.reload();
                    }else{
                        showGridBoxResultMessage(form, rsp, 'error');
                    }
                }
            })
            
            return false;
        });

        //Add Payment Method
        $('#add-payment-method').click(function(){
            jQuery('#cards-list .edit-payment-method').hide();
            jQuery('#add-payment-method').hide();
            $('#cards-list').hide();
            $('#edit-card-form').fadeIn('fast');
            $('#edit-card-form #id').val('');
            $('#edit-card-form input[type="text"]').val('');            
            $('#edit-card-form #email').val($('#edit-card-form #email').attr('data-default'));
            $('#my_payment').addClass('grid-box-editing');
            $('#my_payment').find('.message').remove();
            $('#edit-card-form #invoice_me option:eq(0)').attr('selected', true);
            $('#edit-card-form #is_default option:eq(0)').attr('selected', true);
            $('#payment-cc-section').show();
            $('#organisation_id').val($('#organisation_id').data('organisation-id'));
            return false;
        });
        $('#cards-list .edit-payment-method').click(function(){
            jQuery('#cards-list .edit-payment-method').hide();
            jQuery('#add-payment-method').hide();
            var link = $(this);
            var pRow = $(this).parents('.tr');
            $('#cards-list').find('.loading b').html('LOADING DATA');
            $('#cards-list').find('.loading').show();
            $.ajax({
                url: link.attr('href'),
                type: 'get',
                dataType: 'json',
                success: function(rsp){
                    $('#cards-list').find('.loading').hide();
                    var form = $('#edit-card-form');
                    form.find('#nickname').val(rsp.nickname);
                    form.find('#card_number').val(rsp.CCNumber);
                    form.find('#customer_reference').val(rsp.customer_reference);
                    form.find('#name_on_card').val(rsp.CCName);
                    form.find('#card_expiry').val(rsp.CCExpiryMonth + "/" + rsp.CCExpiryYear);
                    form.find('#email').val(rsp.email);
                    form.find('#card_cvc').val(rsp.CCCvn);
                    form.find('#id').val(link.attr('data-id'));
                    form.find('.cnumber-desc').show();
                    form.find('#organisation_id').val(rsp.organisation_id);
                    if (rsp.invoice_me == 0) {
                        $('#payment-cc-section').show();
                        $('#edit-card-form #invoice_me option:eq(0)').attr('selected', true);
                    } else {
                        $('#payment-cc-section').hide();
                        $('#edit-card-form #invoice_me option:eq(1)').attr('selected', true);
                    }
                    if (rsp.is_default == '0') {
                        $('#edit-card-form #is_default option:eq(0)').attr('selected', true);
                    } else {
                        $('#edit-card-form #is_default option:eq(1)').attr('selected', true);
                    }
                    $('#cards-list').hide();
                    $('#edit-card-form').fadeIn('fast');
                    $('#my_payment').addClass('grid-box-editing');
                },
                error: function(rsp){
                    $('#cards-list').find('.loading').hide();
                    $('#cards-list').append('<div class="message error">' + rsp.responseText + "</div>");
                    setTimeout(function(){
                        $('#cards-list .message').fadeOut('fast', function(){
                            $('#cards-list .message').remove();
                        })
                    }, 2000);
                }
            })
            return false;
        });
        $('#edit-card-form #invoice_me').change(function(){
            if ($(this).val() == 1) {
                $('#payment-cc-section').hide();
            } else {
                $('#payment-cc-section').show();
            }
        });
        $('#cards-list .delete-payment-method').click(function(){
            var link = $(this);
            var pRow = $(this).parents('.tr'); 
            $('#cards-list').find('.loading b').html('DELETING DATA');           
            $('#cards-list').find('.loading').show();
            $.ajax({
                url: link.attr('href'),
                type: 'get',
                success: function(rsp){
                    $('#cards-list').find('.loading').hide();
                    if(rsp == 'success')
                    {
                        pRow.fadeOut('fast', function(){
                            pRow.remove();
                            if($('#cards-list .tbody .tr').length == 0)
                            {
                                $('#cards-list .tbody').append('<div class="tr">' + 
                                           '<div class="td td-full">No Payment Method Found! Please add new one.</div>' +
                                           '<div class="clear"></div>' +
                                       '</div>');
                            }
                        })
                    }else{
                        $('#cards-list').append('<div class="message error">' + rsp + "</div>");
                        setTimeout(function(){
                            $('#cards-list .message').fadeOut('fast', function(){
                                $('#cards-list .message').remove();
                            })
                        }, 4000);
                    }
                }
            })
            return false;
        });

        $('#edit-card-form .cancel-btn').click(function(){
            jQuery('#cards-list .edit-payment-method').show();
            jQuery('#add-payment-method').show();
            $('#my_payment').removeClass('grid-box-editing');
            $('#edit-card-form').hide();
            $('#edit-card-form').find('.cnumber-desc').hide();
            $('#cards-list').fadeIn('fast');
            $('#my_payment').find('.message').remove();            
            return false;
        })
        
        //Remove Membership
        $('#delete-community-box .process-btn').on('click', function(){
            $('#my_community_memberships .message').remove();
            
            var link = $(this);
            $('#my_community_memberships .loading1').show();
            $.ajax({
                url: link.attr('href'),
                type: 'get',
                success: function(rsp){
                    $('#my_community_memberships .loading1').hide();
                    if(rsp == 'success')
                    {
                        link = $('#my_community_memberships a[href="' + $(link).attr('href') + '"]');
                        link.parents('.tr').fadeOut('fast', function(){
                            $(this).remove();
                            if($('#my_community_memberships .tbody .tr').size() < 1)
                                $('#my_community_memberships .tbody').append('<div class="tr">' +
                                               '<div class="td td-full">There is no community that you joined.</div>' +
                                               '<div class="clear"></div>' +
                                           '</div>');
                        })
                        $('#my_community_memberships').append('<div class="message success">You successfully left community</div>');
                    }else{
                        $('#my_community_memberships').append('<div class="message error">' + rsp + '</div>');
                    }
                    $('#my_community_memberships .message').delay(5000).fadeOut()
                },
                error: function(err){
                    $('#my_community_memberships .loading1').hide();
                    $('#my_community_memberships').append('<div class="message error">' + err.responseText + '</div>');
                }
            });
            
            $('#delete-community-box .cancel-btn').click();
            return false;            
        });

        // Organisation - transform divs in inputs at click on edit button
        $('#organisation-container').on('click', '.gbh-btn-edit', function(){
            jQuery('.gbh-btn-edit').hide();
            var thisParentId = '#'+$(this).parents('.grid-box').attr('id');
            var findInputs = $(thisParentId+' .grid-row input:visible').size();
            jQuery(thisParentId).find('.message').remove();
            if( findInputs == 0){

                $(thisParentId+' .btn-row').fadeIn();
                 $(thisParentId).addClass('grid-box-editing');
                //transform all divs in inputs
                $(thisParentId+' .grid-cell.in_input').each(function(){
                    if ($(this).attr('data-type') == 'skip') {
                       $(this).parent().hide();
                       return;
                    }
                    var thisTextVal = $(this).attr('data-value'); 
                    var thisNameVal = $(this).attr('data-name'); 
                    if($(this).attr('data-placeholder'))
                       var thisPlaceholderValue = $(this).attr('data-placeholder');
                    else
                       var thisPlaceholderValue = '';
                   
                   if($(this).attr('data-type'))
                       var dataType = $(this).attr('data-type');
                   else
                       var dataType = 'text';

                   if(dataType == 'textarea')
                       $(this).after('<textarea name="'+thisNameVal+'" placeholder="' + thisPlaceholderValue + '" class="textarea">' + thisTextVal + '</textarea>');
                   else if(dataType == 'readonly')
                       $(this).after('<input type="text" value="'+thisTextVal+'" readonly="readonly" disabled="disabled" />');
                   else
                       $(this).after('<input type="' + dataType + '" name="'+thisNameVal+'" value="'+thisTextVal+'" placeholder="' + thisPlaceholderValue + '" autocomplete="off"/>');

                   $(this).hide();
                });        
            }

            return false;
        });

        //Edit Cancel
        $('#organisation-container').on('click', '.edit-cancel-btn', function(){
            jQuery('.gbh-btn-edit').show();
            var thisParentId = '#'+$(this).parents('.grid-box').attr('id');
            var findInputs = $(thisParentId+' .grid-row input:visible').size();            
            $(thisParentId).find('.message').remove();
            //if( findInputs == 0){

            $(thisParentId+' .btn-row').fadeIn();
             $(thisParentId).addClass('grid-box-editing');
            //transform all divs in inputs
            $(thisParentId+' .grid-cell.in_input').each(function(){
               if ($(this).attr('data-type') == 'skip') {
                    $(this).parent().show();                    
                } else {
                    $(this).next().remove();                
                    $(this).show();
                }  
            });
            $(thisParentId+' .btn-row').hide();

            return false;
        });


        //save my details updates
        $('#organisation-container').on('click', '.process-btn', function(){

            var form = $(this).parents('form');
            form.find('.errors_msg').hide();
            showGridBoxLoadingWrapper(form);
            hideGridBoxResultMessage(form);
            $.ajax({
                url: '/my-profile/',
                data: form.serialize(),
                type: 'POST',
                success: function(rsp)
                {
                    hideGridBoxLoadingWrapper(form);
                    if(rsp == 'success')
                    {
                        jQuery('.btn-row a').hide();
                        showGridBoxResultMessage(form, 'Successfully Saved!', 'success');
                        document.location.reload();
                    }else{
                        showGridBoxResultMessage(form, rsp, 'error');
                    }
                }
            })

            return false;
        });
        $('.create_organisation').on('click', function(e){
            e.preventDefault();
            $('.my_organisation_box').find('.btn-row').show();
            $('.join_org').hide();
            $('.edit_org').toggle();
            if( $('.edit_org').is(':visible') == $('.edit_org_text').is(':visible') ){
                toggle_text();
            }
        });
        $('.join_organisation').on('click', function(e){
            e.preventDefault();
            $('.my_organisation_box').find('.btn-row').show();
            $('.edit_org').hide();
            $('.join_org').toggle();
            if( $('.join_org').is(':visible') == $('.edit_org_text').is(':visible') ){
                toggle_text();
            }
        })
        function toggle_text(){
            if( $('.edit_org_text').attr('data-state') == '1' ){
                $('.edit_org_text').attr('data-state', 0 );
                $('.edit_org_text').hide()
            } else{
                $('.edit_org_text').attr('data-state', 1 );
                $('.edit_org_text').show();
            }
        }

        $('.leave_group_confirm').each(function(data){
            var link = jQuery(this).attr('href');
            jQuery(this).cplightbox({
                type: 'inline',
                href: '#delete-community-box',
                onStart: function(){
                    jQuery('#delete-community-box .process-btn').on('click', function(){
                        location.href = link ;
                    });
                }
            })
        });
        
    });
    

    
    $(document).on('keyup', '#edit-card-form #email', function(){
        var email = $(this).val();
        if (email.length > 50) {
            $(this).val(email.substring(0, 49));
        }
    });

})(jQuery);
function saveHarnessDetails(id)
{
    jQuery('#harness-detail-box' + id + ' .loading').show();
    jQuery('#harness-detail-box' + id + ' .message').remove();

    jQuery.ajax({
        url: '/',
        data: jQuery('#harness-form').serialize(),
        type: 'POST',
        success: function(rsp){
            jQuery('#harness-detail-box' + id + ' .loading').hide();
            if(rsp == 'success')
            {
                jQuery('#my_testsuites').prepend('<div style="margin-bottom:20px;" class="message success">Your data was saved!</div>');                
                jQuery('#harness-detail-box' + id + ' .close-popup-btn').click();
            }
            else
            {
                jQuery('#harness-detail-box' + id + ' .popup-box-footer').prepend('<div class="message error">' + rsp + "</div>");
            }
        },
        error: function(err){
            jQuery('#harness-detail-box' + id + ' .loading').hide();
            jQuery('#harness-detail-box' + id + ' .popup-box-footer').prepend('<div class="message error">' + err.responseText + "</div>");
        }
    });
    
    return false;
}
function saveVariableDefaults(obj)
{
    var parentObj = jQuery(obj).parent().parent();
    parentObj.find(".loading1").show();
    parentObj.find(".message").remove();
    var value = parentObj.find('.td-variable-value input').val();
    jQuery.ajax({
        url: jQuery(obj).attr('href') + "&value=" + value,
        type: 'POST',
        success: function(rsp){
            parentObj.find(".loading1").hide();
            if(rsp == 'success')
            {
                jQuery('.btn-row a').hide();
                parentObj.append('<p class="message success">Successfully Saved!</p>');
            }else{
                parentObj.append('<p class="message error">' + rsp + '</p>');
            }
            parentObj.find('.message').fadeIn('fast');
            setTimeout(function(){
                parentObj.find('.message').fadeOut('fast');
            }, 1500);

        }
    })
    return false;
}