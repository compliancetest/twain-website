//Manage User Login And Registration
(function($){
    $(document).ready(function(){
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
            
            if(form.find('#user_login2').val() == '' || form.find('#user_pass2').val() == '')
            {
                $('#popup-login-msg').html('Please fill in all fields.');
                $('#popup-login-msg').fadeIn('fast');
                return false;
            }
            $('#popup-login-msg').hide();
            $('.loader2').show();
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
                        $('#popup-login-msg').html('<span></span>Wrong username or password, please try again!');
                        $('#popup-login-msg').fadeIn('fast');
                        
                    }  
                    $('.loader2').hide();
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
            
            
            if(firstname && lastname && email && user && organisation && contact_phone && user_pass && user_pass_confirm && captcha_reg) {
                
                if (user_pass != user_pass_confirm){
                    $('.err').text('Passwords don\'t match! ');
                    $('.err').show('slow');
                    noError = false;
                }
                if(!emailReg.test(email)){  
                    $('.err').text('Please enter a valid e-mail address!');
                    $('.err').show('slow');
                    noError = false;
                }
                
                if (!$('#acc_tc_id').is(':checked')) {
                    $('.err').text('You must agree to our Terms & Conditions first!');
                    $('.err').show('slow');
                    noError = false;
                }
                
                if(noError){
                    //Send Ajax
                    $('.loader').show();
                    $('.error').hide();
                    $.ajax({
                        url: '/',
                        type: "POST",
                        data: form.serialize(),
                        success: function(data) {
                            $('.loader').hide();
                            if (data == 'success') {                                
                                $('#wrap_forms').hide();
                                $('.reg_message').fadeIn();                                
                                setTimeout(function(){
                                    location.reload();
                                },2000);                                
                            } else if(data == 'captcha_error') {
                                $('.err').text('Captcha not correct!');
                                $('.err').show('slow');
                            } else {
                                $('.err').text(data);
                                $('.err').show('slow');
                            }
                        }
                    });
                    
                }
                     
            }else{
                $('.err').text('Please fill in all fields!');
                $('.err').show('slow');
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
        
        //Products Page
        $('#my_products').find('.grid-box-expandable').each(function(){            
            var table = $(this);
            table.find('.gbh-btn-expandable').click(function(){          
                if(table.hasClass('grid-box-closed'))      
                    table.removeClass('grid-box-closed').addClass('grid-box-opened');
                else
                    table.removeClass('grid-box-opened').addClass('grid-box-closed');
                table.find('.grid-box-body').animate({'height': 'toggle'});
            })
        })
    })    
})(jQuery);