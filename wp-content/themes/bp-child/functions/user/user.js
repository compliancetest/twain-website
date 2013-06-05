//Manage User Login And Registration
(function($){
    $(document).ready(function(){
        //Add Placeholder
        $("#user_login, #user_login2").attr("placeholder", "E-mail or User");
        $("#user_pass, #user_pass2").attr("placeholder", "********");
        
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
    })    
})(jQuery);