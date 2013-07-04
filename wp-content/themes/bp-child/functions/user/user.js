//Manage User Login And Registration
(function($){
    $(document).ready(function(){
        //Terms and Conditions
        $('#agree_terms').cplightbox({});
        $('#site-terms-box .cancel-btn').cplightbox({});
        $('#site-terms-box .process-btn').cplightbox({
            onLoad: function(){
                $('#acc_tc_id').prop('checked', true);
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
            $.ajax({
                type: 'post', 
                data: form.serialize() + '&cp-action=login',
                success: function(rsp)
                {
                    if(rsp == 'success') //Login Success
                    {
                        //Goto Profile Page
                        document.location.href = "/my-profile";
                    }else{ //Error                    
                        //Show Error Message
                        $('#header_login_error_msg').html('<span></span>Wrong username or password, please try again!');
                        $('#header_login_error_msg').fadeIn('fast');
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
                type: 'post', 
                data: form.serialize() + '&cp-action=login',
                success: function(rsp)
                {
                    if(rsp == 'success') //Login Success
                    {
                        //Goto Profile Page
                        document.location.href = "/my-profile";
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
            var form = $('#formreg');
            var firstname = $("#first_name_id").val();
            var lastname =    $("#last_name_id").val();
            var email = $("#email_id").val();
            var user = $("#user_login_id").val();
            var organisation = $("#organisation_id").val();
            var contact_phone = $("#contact_phone_id").val();
            var user_pass = $("#user_pass_id").val();
            var user_pass_confirm = $("#user_pass_confirm_id").val();
            var captcha_reg = $("#captcha_reg").val();
            var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
            var noError = true;
            
            var msgObj = $('#registration-popup #reg .message');
            if(firstname && lastname && email && user && organisation && contact_phone && user_pass && user_pass_confirm && captcha_reg) {
                
                if (user_pass != user_pass_confirm){
                    msgObj.removeClass('success').addClass('error').html('Passwords don\'t match!').fadeIn('fast');
                    return false;
                    noError = false;
                }
                if(!emailReg.test(email)){  
                    msgObj.removeClass('success').addClass('error').html('Please enter a valid e-mail address!').fadeIn('fast');
                    noError = false;
                    return false;
                }
                
                if (!$('#acc_tc_id').is(':checked')) {
                    msgObj.removeClass('success').addClass('error').html('You must agree to our Terms & Conditions first!').fadeIn('fast');
                    return false;
                    noError = false;
                }
                
                if(noError){
                    //Send Ajax
                    $('.loading').show();
                    msgObj.hide();
                    $.ajax({
                        url: '/',
                        type: "POST",
                        data: form.serialize(),
                        success: function(data) {
                            $('.loading').hide();
                            if (data == 'success') {                                
                                msgObj.removeClass('error').addClass('success').html('Thanks for your registration. An email with the verification link sent to your email address. Please verify your email address using it.').fadeIn('fast');                             
                                setTimeout(function(){
                                    location.reload();
                                },4000);                                
                            } else if(data == 'captcha_error') {
                                msgObj.removeClass('success').addClass('error').html('Captcha not correct!').fadeIn('fast');
                            } else {
                                msgObj.removeClass('success').addClass('error').html(data).fadeIn('fast');
                            }
                        }
                    });
                    
                }
                     
            }else{
                msgObj.removeClass('success').addClass('error').html('Please fill in all fields!').fadeIn('fast');
                return false;
            }
            
            
        });
        
        //resend email verification
        $('#resend_email_verification').on('click', function(){
            
            var getThis = $(this);
            
            $.ajax({
                url: window.location.href,
                type: 'POST',
                data: {'cp-action': 'resend_email_verification', uemail: $('input[name="uemail"]').val(), uname: $('input[name="uname"]').val()},
                success: function(data){
                    //alert(data);
                    getThis.parent().parent().html('Email successfully sent! Please check your email address to verify your email');
                }
            });
        });
        
       //transform divs in inputs at click on edit button
       $(document).on('click', '.gbh-btn-edit', function(){           
            var thisParentId = '#'+$(this).parents('.grid-box').attr('id');
            var findInputs = $(thisParentId+' .grid-row input:visible').size();

            //if( findInputs == 0){

            $(thisParentId+' .btn-row').fadeIn();
             $(thisParentId).addClass('grid-box-editing');
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
               
               $(this).replaceWith('<input type="' + dataType + '" name="'+thisNameVal+'" value="'+thisTextVal+'" placeholder="' + thisPlaceholderValue + '" />');

            });        
            
        });
        
        //save my details updates
        $('#my_profile').on('click', '.process-btn', function(){
            
            var form = $(this).parents('form');            
            form.find('.errors_msg').hide();
            showGridBoxLoadingWrapper(form);
            hideGridBoxResultMessage(form);
            $.ajax({
                url: '/my-profile',
                data: form.serialize(),
                type: 'POST',
                success: function(rsp)
                {
                    hideGridBoxLoadingWrapper(form);
                    if(rsp == 'success')
                    {
                        showGridBoxResultMessage(form, 'Successfuly saved!', 'success');
                        document.location.reload(); 
                    }else{
                        showGridBoxResultMessage(form, rsp, 'error');
                    }
                }
            })
        });
        
        //Add Payment Method
        $('#add-payment-method').click(function(){
            $('#cards-list').hide();
            $('#edit-card-form').fadeIn('fast');
            $('#edit-card-form #id').val('');
            $('#edit-card-form input[type="text"]').val('');
            $('#my_payment').addClass('grid-box-editing');
            return false;
        });
        $('#cards-list .edit-payment-method').click(function(){
            var pRow = $(this).parents('.grid-row');
            var form = $('#edit-card-form');
            form.find('#card_number').val(pRow.find('#cnumber').val());
            form.find('#name_on_card').val(pRow.find('#cname').val());
            form.find('#card_expiry').val(pRow.find('#cexpiry').val());
            form.find('#card_cvc').val(pRow.find('#ccvc').val());
            form.find('#id').val($(this).attr('data-id'));
            $('#cards-list').hide();
            $('#edit-card-form').fadeIn('fast');            
            $('#my_payment').addClass('grid-box-editing');
            return false;
        });
        $('#cards-list .delete-payment-method').click(function(){
            var link = $(this);
            var pRow = $(this).parents('.grid-row');
            pRow.append('<div class="loading1"></div>');
            pRow.find('.loading1').show();
            $.ajax({
                url: link.attr('href'),
                type: 'get',
                success: function(rsp){
                    pRow.find('.loading1').remove();
                    if(rsp == 'success')
                    {
                        pRow.fadeOut('fast', function(){
                            pRow.remove();
                            if($('#cards-list .grid-row').length == 0)
                            {
                                $('#cards-list').append('<div class="grid-row">' + 
                                        '<div class="grid-cell width100P">No Payment Method Found! Please add new one.</div>' + 
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
                        }, 2000);
                    }
                }
            })            
            return false;
        });
        
        $('#edit-card-form .cancel-btn').click(function(){
            $('#my_payment').removeClass('grid-box-editing');
            $('#edit-card-form').hide();
            $('#cards-list').fadeIn('fast');                        
            return false;
        })
    })    
})(jQuery);