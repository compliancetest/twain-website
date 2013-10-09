jQuery(document).ready(function(){
    
    jQuery('#trigger-message-link').cplightbox({
        type: 'ajax',
        closeWhenClickOveraly: false,
        onStart: function(){
            jQuery('#trigger-message-box .input-error').removeClass('input-error');
            jQuery('#trigger-message-box .select-error').removeClass('select-error');
            jQuery('#trigger-message-box .message').remove();
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
                    jQuery('#tm-template').append('<option value="' + jQuery(this).text() + '">' + jQuery(this).text() + '</option>');
                });
                
                jQuery('#template-variables-contr .field-row:gt(0)').remove();
                jQuery(rsp).find('variable').each(function(){
                    jQuery('#template-variables-contr').append('<div class="field-row">' + 
                                '<div class="grid-cell width45P"><label>' + jQuery(this).find('name').text() + '</label></div>' + 
                                '<div class="grid-cell width55P"><input type="text" name="variable' + jQuery(this).attr('id') + '" class="input" value="' + jQuery(this).find('value').text() + '" /></div>' +
                                '<div class="clear"></div>' +
                            '</div>');
                })
            },
            error: function(err){
                showTriggerMessageResultMessage('Sorry, there was an error while getting data.', 'error');                
            }
        })
    })
    
    /**
    * Update Message Templates When Test Case is changed
    */
    jQuery('body').on('change', '#tm-test-case', function(){
        var case_id = this.value;
        var suite_id = jQuery('#tm-test-suite').val();
        jQuery('#trigger-message-box .loading-with-text b').html('LOADING DATA');
        jQuery('#trigger-message-box .loading-with-text').show();
        jQuery('#trigger-message-box .popup-box-content .message').fadeOut('fast');
        jQuery.ajax({
            url: '/',
            data: {'ct-message-action': 'get-case-templates', 'suite_id': suite_id, 'case_id': case_id},
            type: 'post',
            dataType: 'xml',
            complete: function(){
                jQuery('#trigger-message-box .loading-with-text').hide();
            },
            success: function(rsp){                
                jQuery('#tm-template').find('option:gt(0)').remove();                
                jQuery(rsp).find('template').each(function(idx){
                    jQuery('#tm-template').append('<option value="' + jQuery(this).text() + '">' + jQuery(this).text() + '</option>');
                });
                
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
                        
                        //Update Template Variables
                        jQuery('#template-variables-contr .field-row:gt(0)').remove();
                        jQuery(rsp).find('variable').each(function(){
                            jQuery('#template-variables-contr').append('<div class="field-row">' + 
                                        '<div class="grid-cell width45P"><label>' + jQuery(this).find('name').text() + '</label></div>' + 
                                        '<div class="grid-cell width55P"><input type="text" name="variable' + jQuery(this).attr('id') + '" class="input" value="' + jQuery(this).find('value').text() + '" /></div>' +
                                        '<div class="clear"></div>' +
                                    '</div>');
                        })
                    }else{
                        //Update Template Variables
                        jQuery(rsp).find('variable').each(function(){
                            jQuery('input[name="variable' + jQuery(this).attr('id') + '"]').val(jQuery(this).find('value').text());                            
                        })
                    }
                    jQuery('#tm-test-case').val(new_case_id);
                    
                    //Update Message Template
                    if(case_id != new_case_id)
                    {
                        jQuery('#tm-template').find('option:gt(0)').remove();                
                        jQuery(rsp).find('template').each(function(idx){
                            jQuery('#tm-template').append('<option value="' + jQuery(this).text() + '">' + jQuery(this).text() + '</option>');
                        });
                    }
                    jQuery('#tm-template').val(new_template_id);
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
            case_template: jQuery('#tm-template').val()
        };
        jQuery('#template-variables-contr input[type="text"]').each(function(){
            postData[jQuery(this).attr('name')] = jQuery(this).val()
        })
        
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
    jQuery('body').on('click', '#trigger-message-link', function(){
        jQuery('#messageForm').submit();
        return false;
    })
    jQuery('body').on('submit', '#messageForm', function(){
        
        jQuery('#trigger-message-box .popup-box-content').find('.message').remove();
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
        if(!jQuery('#tm-product').val())
        {
            jQuery('#tm-product').addClass('select-error');
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
})