jQuery(document).ready(function(){
    
    jQuery('#trigger-message-link').cplightbox({
        type: 'ajax',
        closeWhenClickOveraly: false,
        removeBoxAfterClose: true,
        onAjaxSuccess: function(obj){
            //Custom Popup Box
            jQuery(obj).find("a[rel='custom-popup']").cplightbox({
                closeWhenClickOveraly: false,
                onLoad: function(obj){
                    if(jQuery('#trigger-message-box .loading').length < 1)
                        jQuery('#trigger-message-box').append('<div class="loading loading-with-text radius6"><div><b>LOADING DATA</b><span>Please wait...</span></div></div>');
                    jQuery('#trigger-message-box .loading b').html('LOADING DATA');
                },
                onAjaxSuccess: function(obj){
                    jQuery(obj).find("a[rel='custom-popup']").cplightbox();        
                },
                onClose: function(obj){
                    if(jQuery('#trigger-message-box .loading').length < 1)
                        jQuery('#trigger-message-box').append('<div class="loading loading-with-text radius6"><div><b>LOADING DATA</b><span>Please wait...</span></div></div>');                    
                }
            });
        },
        onStart: function(){            
            jQuery('#trigger-message-box .input-error').removeClass('input-error');
            jQuery('#trigger-message-box .select-error').removeClass('select-error');
            jQuery('#trigger-message-box p.error').remove();
        }
    })
    
    var triggerMessageTimer = null;
    
    function showTriggerMessageResultMessage(msg, type)
    {
        if(triggerMessageTimer != null)
        {
            clearTimeout(triggerMessageTimer);                    
        }
        jQuery('#trigger-message-box .popup-box-content').find('.message').remove();
        jQuery('#trigger-message-box .popup-box-content').prepend('<p class="message ' + type +  '">' + msg + '</p>');
        triggerMessageTimer = setTimeout(function(){
            jQuery('#trigger-message-box .popup-box-content').find('.message').fadeOut('fast');
            triggerMessageTimer = null;
        }, 2000);
    }
    
    /**
    * Update Test Case, Template Variables and Message Templates when Test Suite is changed
    */
    jQuery('body').on('change', '#tm-test-suite', function(){
        var suite_id = this.value;
        jQuery('#trigger-message-box .loading-with-text b').html('LOADING DATA');
        jQuery('#trigger-message-box .loading-with-text').show();
        jQuery('#trigger-message-box .popup-box-content .message').fadeOut('fast');
        jQuery.ajax({
            url: '/',
            data: {'ct-message-action': 'get-test-cases', 'suite_id': suite_id},
            type: 'post',
            dataType: 'xml',
            complete: function(){
                jQuery('#trigger-message-box .loading-with-text').hide();
            },
            success: function(rsp){                
                
                jQuery('#tm-test-case').find('option').remove();                
                jQuery(rsp).find('case').each(function(idx){
                    jQuery('#tm-test-case').append('<option value="' + jQuery(this).attr('id') + '">' + jQuery(this).text() + '</option>');
                });
                
                jQuery('#tm-template').find('option:gt(0)').remove();                
                jQuery(rsp).find('template').each(function(idx){
                    jQuery('#tm-template').append('<option value="' + jQuery(this).find('uri').text() + '">' + jQuery(this).find('name').text() + '</option>');
                });
                if (jQuery(rsp).find('template').length == 1) {
                    jQuery('#tm-template').find('option:eq(1)').attr('selected', true);
                }
                
                jQuery('#trigger-message-box .harness-profiles-section').html(jQuery(rsp).find('harness').text());                
                jQuery('#trigger-message-box .tester-profiles-section').html(jQuery(rsp).find('tester').text());
                jQuery("#trigger-message-box .harness-profiles-section a[rel='custom-popup'], #trigger-message-box .tester-profiles-section a[rel='custom-popup']").cplightbox({
                    closeWhenClickOveraly: false,
                    onAjaxSuccess: function(obj){
                        jQuery(obj).find("a[rel='custom-popup']").cplightbox();        
                    }
                });  
                if(jQuery('#trigger-message-box .loading-with-text').length < 1)
                    jQuery('#trigger-message-box form').append('<div class="loading loading-with-text radius6"><div><b>PROCESSING YOUR PAYMENT</b><span>Please wait...</span></div></div>');      
                
            },
            error: function(err){
                showTriggerMessageResultMessage('Sorry, there was an error while getting data.', 'error');                
            }
        })
    })
    
    /**
    * Update Message Templates When Test Case is changed
    */
    jQuery('body').on('change', '#tm-test-case, #show_my', function(){
        var case_id = jQuery('#tm-test-case').val();
        var suite_id = jQuery('#tm-test-suite').val();
        var show_my_profiles = jQuery('#show_my').is(':checked');
        var is_bulk = ( jQuery('#tm-test-case').find(':selected').attr('data-bulk') == 'Yes' );
        if( is_bulk ) {
            jQuery( '#copy_count').removeClass('input-error');
            jQuery( '#copy_count').val('1');
            jQuery( '.copy_count_container').show();
        } else{
            jQuery( '.copy_count_container').hide();
        }
        jQuery('#trigger-message-box .loading-with-text b').html('LOADING DATA');
        jQuery('#trigger-message-box .loading-with-text').show();
        jQuery('#trigger-message-box .popup-box-content .message').fadeOut('fast');
        jQuery.ajax({
            url: '/',
            data: {'ct-message-action': 'get-case-templates-and-profiles', 'suite_id': suite_id, 'case_id': case_id, 'show_my': show_my_profiles },
            type: 'post',
            dataType: 'xml',
            complete: function(){
                jQuery('#trigger-message-box .loading-with-text').hide();
            },
            success: function(rsp){                
                jQuery('#tm-template').find('option:gt(0)').remove();
                var selected_str = '';
                if( jQuery(rsp).find('template').length === 1 ){
                    selected_str = 'selected="selected"'
                }
                jQuery(rsp).find('template').each(function(idx){
                    jQuery('#tm-template').append('<option '+ selected_str +' value="' + jQuery(this).find('uri').text() + '">' + jQuery(this).find('name').text() + '</option>');
                });
                jQuery('#trigger-message-box .harness-profiles-section').html(jQuery(rsp).find('harness').text());
                jQuery('#trigger-message-box .tester-profiles-section').html(jQuery(rsp).find('tester').text());
                jQuery("#trigger-message-box .harness-profiles-section a[rel='custom-popup'], #trigger-message-box .tester-profiles-section a[rel='custom-popup']").cplightbox({
                    closeWhenClickOveraly: false,
                    onAjaxSuccess: function(obj){
                        jQuery(obj).find("a[rel='custom-popup']").cplightbox();        
                    }
                });        
                if(jQuery('#trigger-message-box .loading-with-text').length < 1)
                    jQuery('#trigger-message-box form').append('<div class="loading loading-with-text radius6"><div><b>PROCESSING YOUR PAYMENT</b><span>Please wait...</span></div></div>');
            },
            error: function(err){
                showTriggerMessageResultMessage('Sorry, there was an error while getting data.', 'error');
            }
        })
    });
    
    /**
    * Load Previous Message Data
    */
    jQuery('body').on('change', '#tm-prev-message', function(){
        if(!jQuery('#tm-prev-message').val())
            return;
        var template_id = this.value;
        var suite_id = jQuery('#tm-test-suite').val();
        var case_id = jQuery('#tm-test-case').val();
        jQuery('#trigger-message-box .loading-with-text b').html('LOADING DATA');
        jQuery('#trigger-message-box .loading-with-text').show();
        jQuery('#trigger-message-box .popup-box-content .message').fadeOut('fast');
        jQuery.ajax({
            url: '/',
            data: {'ct-message-action': 'load-message-template', 'current_suite_id': suite_id, 'current_case_id': case_id, 'template_id': template_id},
            type: 'post',
            dataType: 'xml',
            complete: function(){
                jQuery('#trigger-message-box .loading-with-text').hide();
            },
            success: function(rsp){                
                if(jQuery(rsp).find('status').text() == 'error')
                {
                    showTriggerMessageResultMessage(jQuery(rsp).find('error').text(), 'error');
                }else{
                    jQuery('#trigger-message-box .tester-profiles-section input').prop('checked', false);
                    var new_suite_id = jQuery(rsp).find('suiteid').text();
                    var new_case_id = jQuery(rsp).find('caseid').text();
                    var new_product_id = jQuery(rsp).find('productid').text();
                    var new_template_id = jQuery(rsp).find('templateid').text();
                    
                    jQuery('#tm-test-suite').val(new_suite_id);
                    jQuery('#tm-product').val(new_product_id);
                    //Update Test Cases
                    if(suite_id != new_suite_id)
                    {
                        jQuery('#tm-test-case').find('option').remove();                
                        jQuery(rsp).find('case').each(function(idx){
                            jQuery('#tm-test-case').append('<option value="' + jQuery(this).attr('id') + '">' + jQuery(this).text() + '</option>');
                        });                  
                    }
                    jQuery('#tm-test-case').val(new_case_id);
                    
                    //Update Message Template
                    if(case_id != new_case_id)
                    {
                        jQuery('#tm-template').find('option:gt(0)').remove();                
                        jQuery(rsp).find('template').each(function(idx){
                            jQuery('#tm-template').append('<option value="' + jQuery(this).find('uri').text() + '">' + jQuery(this).find('name').text() + '</option>');
                        });
                        
                        //Update Harness Profiles
                        jQuery('#trigger-message-box .harness-profiles-section').html(jQuery(rsp).find('harness').text());   
                    }
                    jQuery('#tm-template').val(new_template_id);
                    
                    jQuery(rsp).find('harness_id').each(function(){
                        var id = jQuery(this).text();
                        jQuery('#harness_profile' + id).prop('checked', true);
                    })
                    jQuery(rsp).find('tester_id').each(function(){
                        var id = jQuery(this).text();
                        jQuery('#tester_profile' + id).prop('checked', true);
                    })
                    
                }
            },
            error: function(err){
                showTriggerMessageResultMessage('Sorry, there was an error while getting data.', 'error');
            }
        })
    });
    
    /**
    * Save Message
    */
    jQuery('body').on('click', '#save-message-template-link', function(){
        var url = jQuery(this).attr('href');
        if(jQuery('#message-template-name').val() == '')
        {
            jQuery('#message-template-name').addClass('input-error').focus();
            return false;
        }
        jQuery('#trigger-message-box .loading-with-text b').html('SAVING DATA');
        jQuery('#trigger-message-box .loading-with-text').show();
        jQuery('#trigger-message-box .popup-box-content .message').fadeOut('fast');
        var postData = {
            name: jQuery('#message-template-name').val(),
            suite_id: jQuery('#tm-test-suite').val(),
            case_id: jQuery('#tm-test-case').val(),
            product_id: jQuery('#tm-product').val(),
            case_template: jQuery('#tm-template').val(),
            harness_profiles: jQuery('.harness-profiles-section input[type="checkbox"]:checked').map(function(){ return jQuery(this).val()}).get(),
            tester_profiles: jQuery('.tester-profiles-section input[type="checkbox"]:checked').map(function(){ return jQuery(this).val()}).get()
        };
        
        jQuery.ajax({
            url: url,
            data: postData,
            type: 'post',
            dataType: 'xml',
            complete: function(){
                jQuery('#trigger-message-box .loading-with-text').hide();
            },
            success: function(rsp){                
                if(jQuery(rsp).find('status').text() == 'error')
                {
                    showTriggerMessageResultMessage(jQuery(rsp).find('error').text(), 'error');
                }else{
                    jQuery('#tm-prev-message').append('<option value="' + jQuery(rsp).find('newid').text() + '">' + jQuery('#message-template-name').val() + '</option>');
                    jQuery('#message-template-name').val('');
                    showTriggerMessageResultMessage('Your message has been saved.', 'success');
                }
                
            },
            error: function(err){
                showTriggerMessageResultMessage('Sorry, there was an error while saving message.', 'error');
            }
        });
        
        return false;
    });
    
    /**
    * Remove Message
    */
    jQuery('body').on('click', '#remove-message-template-link', function(){
        if(jQuery('#tm-prev-message').val() == '')
            return false;
            
        var url = jQuery(this).attr('href');
        
        jQuery('#trigger-message-box .loading-with-text b').html('REMOVING DATA');
        jQuery('#trigger-message-box .loading-with-text').show();
        jQuery('#trigger-message-box .popup-box-content .message').fadeOut('fast');
        
        var template_id = jQuery('#tm-prev-message').val();
        
        jQuery.ajax({
            url: url,
            data: {'template_id': template_id},
            type: 'post',
            dataType: 'xml',
            complete: function(){
                jQuery('#trigger-message-box .loading-with-text').hide();
            },
            success: function(rsp){                
                if(jQuery(rsp).find('status').text() == 'error')
                {
                    showTriggerMessageResultMessage(jQuery(rsp).find('error').text(), 'error');                    
                }else{
                    jQuery('option:selected', '#tm-prev-message').remove();                    
                    showTriggerMessageResultMessage('The message has been removed.', 'success');
                }
                
            },
            error: function(err){
                showTriggerMessageResultMessage('Sorry, there was an error while removing message.', 'error');
            }
        });
        
        return false;
    });
    
    /**
    * Send Message
    */    
    jQuery('body').on('submit', '#messageForm', function(){
        
        jQuery('#trigger-message-box .popup-box-content').find('.message').remove();
        jQuery('#trigger-message-box .popup-box-content').find('.input-error').removeClass('input-error');
        jQuery('#trigger-message-box .popup-box-content').find('.select-error').removeClass('select-error');
        var isValid = true;

        if(!jQuery('#tm-test-suite').val())
        {
            jQuery('#tm-test-suite').addClass('select-error');
            isValid = false;
        }
        if(!jQuery('#tm-test-case').val())
        {
            jQuery('#tm-test-case').addClass('select-error');
            isValid = false;
        }        
        if(!jQuery('#tm-template').val())
        {
            jQuery('#tm-template').addClass('select-error');
            isValid = false;
        }
        if(!isValid)
        {
            showTriggerMessageResultMessage('Please complete fields in red.', 'error');
            return false;
        }
        
        if(jQuery('.harness-profiles-section input[type="radio"]:checked').length < 1 || jQuery('.tester-profiles-section input[type="radio"]:checked').length < 1)
        {
            showTriggerMessageResultMessage('Please select profile(s).', 'error');
            return false;
        }

        /**
         * @copy_count_limits defined in message.php::showTriggerMessageBox()
         */
        var min_copy_count = copy_count_limits.min_non_bulk;
        var max_copy_count = copy_count_limits.max_non_bulk;

        if( jQuery('.harness-profiles-section input[type="radio"]:checked').attr('data-isbulk') == 'yes' || jQuery('.tester-profiles-section input[type="radio"]:checked').attr('data-isbulk') == 'yes'){
            min_copy_count = copy_count_limits.min_bulk;
            max_copy_count = copy_count_limits.max_bulk;
        }
        var copy_count = jQuery('#copy_count').val().replace(' ', '');
        if( jQuery('#tm-test-case').find(':selected').attr('data-bulk') == 'Yes' && ( parseInt( copy_count ) != copy_count || parseInt(copy_count) > parseInt(max_copy_count) || parseInt(copy_count) < parseInt(min_copy_count)  ))
        {
            jQuery('#copy_count').addClass('input-error');
            showTriggerMessageResultMessage( 'Please provide valid number between '+min_copy_count+' and '+max_copy_count+'.', 'error');
            return false;
        }
        jQuery('#trigger-message-box .loading-with-text b').html('SENDING MESSAGE');
        jQuery('#trigger-message-box .loading-with-text').show();
        
        
        jQuery.ajax({
            url: "/",
            data: jQuery('#messageForm').serialize(),
            type: 'post',
            dataType: 'xml',
            complete: function(){
                jQuery('#trigger-message-box .loading-with-text').hide();
            },
            success: function(rsp){                
                if(jQuery(rsp).find('status').text() == 'error')
                {
                    showTriggerMessageResultMessage(jQuery(rsp).find('error').text(), 'error');
                }else{
                    jQuery('option:selected', '#tm-prev-message').remove();
                    showTriggerMessageResultMessage('The message has been sent.', 'success');
                }
                
            },
            error: function(err){
                showTriggerMessageResultMessage('Sorry, there was an error while sending message.', 'error');
            }
        });
        return false;
    })
    
    
    //Upload Message
    jQuery('#upload-message-link').cplightbox({
        type: 'ajax',
        closeWhenClickOveraly: false,
        removeBoxAfterClose: true,        
        onAjaxSuccess: function(){
            customizeFileTag();   
        },
        onStart: function(){            
            jQuery('#upload-message-box .input-error').removeClass('input-error');
            jQuery('#upload-message-box .select-error').removeClass('select-error');
            jQuery('#upload-message-box p.error').remove();
        }
    })
    
    /**
    * Update Test Case
    */
    jQuery('body').on('change', '#um-test-suite', function(){
        var suite_id = this.value;
        jQuery('#upload-message-box .loading-with-text b').html('LOADING DATA');
        jQuery('#upload-message-box .loading-with-text').show();
        jQuery('#upload-message-box .popup-box-content .message').fadeOut('fast');
        jQuery.ajax({
            url: '/',
            data: {'ct-message-action': 'get-test-cases', 'suite_id': suite_id, 'only-cases': 1, 'case-type' :'tester'},
            type: 'post',
            dataType: 'xml',
            complete: function(){
                jQuery('#upload-message-box .loading-with-text').hide();
            },
            success: function(rsp){                
                
                jQuery('#um-test-case').find('option').remove();                
                jQuery(rsp).find('case').each(function(idx){
                    jQuery('#um-test-case').append('<option value="' + jQuery(this).attr('id') + '">' + jQuery(this).text() + '</option>');
                });
                
                if(jQuery('#upload-message-box .loading-with-text').length < 1)
                    jQuery('#upload-message-box form').append('<div class="loading loading-with-text radius6"><div><b>SENDING MESSAGE</b><span>Please wait...</span></div></div>');      
                
            },
            error: function(err){
                showTriggerMessageResultMessage('Sorry, there was an error while getting data.', 'error');                
            }
        })
    })
    
    jQuery('body').on('click', '#upload-message-link', function(){
        jQuery('#uploadMessageForm').submit();        
        return false;
    })
    
    /**
    * Upload Message
    */
    jQuery('body').on('submit', '#uploadMessageForm', function(){
        jQuery('#upload-message-box .popup-box-content').find('.message').remove();
        jQuery('#upload-message-box .popup-box-content').find('.input-error').removeClass('input-error');
        jQuery('#upload-message-box .popup-box-content').find('.select-error').removeClass('select-error');
        var isValid = true;
        if(!jQuery('#um-test-suite').val())
        {
            jQuery('#um-test-suite').addClass('select-error');
            isValid = false;
        }
        if(!jQuery('#um-test-case').val())
        {
            jQuery('#um-test-case').addClass('select-error');
            isValid = false;
        }
        /*if(!jQuery('#um-product').val())
        {
            jQuery('#um-product').addClass('select-error');
            isValid = false;
        }*/
        if(!isValid)
        {
            showUploadMessageResultMessage('Please complete fields in red.', 'error');
            return false;
        }
        if(!jQuery('#um-file').val())
        {
            showUploadMessageResultMessage('Please choose a file that you want to upload.', 'error');
            return false;
        }
        
        jQuery('#upload-message-box .loading-with-text b').html('SENDING MESSAGE');
        jQuery('#upload-message-box .loading-with-text').show();
        
        return true;
    })
    
    
    var uploadMessageTimer = null;
    
    function showUploadMessageResultMessage(msg, type)
    {
        if(uploadMessageTimer != null)
        {
            clearTimeout(uploadMessageTimer);                    
        }
        jQuery('#upload-message-box .popup-box-content').find('.message').remove();
        jQuery('#upload-message-box .popup-box-content').prepend('<p class="message ' + type +  '">' + msg + '</p>');
        uploadMessageTimer = setTimeout(function(){
            jQuery('#upload-message-box .popup-box-content').find('.message').fadeOut('fast');
            uploadMessageTimer = null;
        }, 2000);
    }
    
    
})