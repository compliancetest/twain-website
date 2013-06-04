jQuery(document).ready(function(){
    jQuery('#si_contact_form1').submit(function(event){
        event.preventDefault()
        var emailValid = true, emailNotEmpty = true, nameNotEmpty = true, messageNotEmpty = true, captchaCode = true
        var errorStyle = "border:1px solid red"
        if (jQuery('#si_contact_name1').val().length <= 0){
            jQuery('#si_contact_name1').attr('placeholder','Type name')
            jQuery('#si_contact_name1').attr('style',errorStyle)
            jQuery('#si_contact_name1').focus()
            nameNotEmpty = false
        }
        if (jQuery('#si_contact_email1').val().length <= 0){
            jQuery('#si_contact_email1').attr('placeholder','Provide E-mail')
            jQuery('#si_contact_email1').attr('style',errorStyle)
            jQuery('#si_contact_email1').focus()
            emailNotEmpty = false
        }
        /*var checkMail = new RegExp('\S+@\S+')
        if (checkMail.test(jQuery('#si_contact_email1').val()) == false){
            jQuery('#si_contact_email1').attr('style',errorStyle)
            jQuery('#si_contact_email1').focus()
            emailValid = false
        }*/
        if (jQuery('#si_contact_ex_field1_1').val().length <= 0){}

        if (jQuery('#si_contact_message1').val().length <= 0){
            jQuery('#si_contact_message1').attr('placeholder','What do you want to write?')
            jQuery('#si_contact_message1').attr('style',errorStyle)
            jQuery('#si_contact_message1').focus()
            messageNotEmpty = false
        }
        if (jQuery('#si_contact_captcha_code1').val().length <= 0){
            var captchaCodeStyle = jQuery('#si_contact_captcha_code1').attr('style')
            jQuery('#si_contact_captcha_code1').attr('style',captchaCodeStyle + errorStyle)
            jQuery('#si_contact_captcha_code1').focus()
            captchaCode = false
        }
        var allFields = new Array (emailValid,emailNotEmpty,nameNotEmpty,messageNotEmpty,captchaCode)
        for (var i = 0; i < allFields.length; i++){
            if (allFields[i] === false){
                return
            }
        }
        jQuery.post('http://www.test.compliancetest.net/wp-content/plugins/super-mail/index.php',jQuery(this).serialize(), function(data, textStatus){
            alert(data+"\n"+textStatus)
            switch(data){
                case 'code':
                    var captchaCodeStyle = jQuery('#si_contact_captcha_code1').attr('style')
                    jQuery('#si_contact_captcha_code1').attr('style',captchaCodeStyle + errorStyle)
                    jQuery('#si_contact_captcha_code1').focus()
                    break
                case 'success':
                    //clear all input fields
                    jQuery('#si_contact_name1').val() = ''
                    jQuery('#si_contact_email1').val() = ''
                    jQuery('#si_contact_captcha_code1').val() = ''
                    jQuery('#si_contact_message1').val() = ''

                    //get new captcha image
                    jQuery('#si_image_ctf1').attr('src','http://www.test.compliancetest.net/wp-content/plugins/super-mail/captcha/securimage_show.php?ctf_form_num=1&sid='
                        + Math.random());
                    break
            }
        })
        
    });
});