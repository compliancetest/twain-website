jQuery(document).ready(function(){
    jQuery('#submit-ticket-btn').cplightbox({
        type: 'ajax',
        closeWhenClickOveraly: false
    })
    
    //Change Priority and Type
    jQuery('body').on('change', '#ticket-priority', function(){
        if(jQuery(this).val() == '')
        {
            jQuery('#ttresponse').html('');
            jQuery('#ttresolve').html('');
            jQuery("#ticket-price-row").hide();    
            jQuery("#ticket-time-row").hide();    
        }else{                        
            jQuery('#ttresponse').html(jQuery('option:selected', this).attr('ttresponse') + ' hours');
            jQuery('#ttresolve').html(jQuery('option:selected', this).attr('ttresolve') + ' hours');            
            jQuery("#ticket-time-row").show();    
            
        }
        ticketUpdatePriceRow();
    })
    jQuery('body').on('change', '#ticket-category', function(){
        ticketUpdatePriceRow();
    })
    
    
    function ticketUpdatePriceRow()
    {
        if(jQuery('#ticket-priority').val() == '' || jQuery('#ticket-category').val() == '')
        {
            jQuery("#ticket-price-row").hide();    
        }else{
            jQuery('#ticket-price').html(jQuery('#ticket-category option:selected').attr('has-fee') == 'no' ? 'Free' : (jQuery('#ticket-priority option:selected').attr('price') + ' Tokens/hr') );            
            if(jQuery('#ticket-category option:selected').attr('has-fee') == 'no')
                    jQuery('#ticket-price').next().hide();
                else
                    jQuery('#ticket-price').next().show();
                    
            jQuery('#ticket-price-row').show();                 
        }
    }
    
    jQuery('body').on('click', '#submit-ticket-link', function(){
        jQuery('#ticketForm').submit();
    });
    jQuery('body').on('focus', '#ticketForm input[type="text"]', function(){
        jQuery(this) .removeClass('input-error');
    });
    jQuery('body').on('focus', '#ticketForm select', function(){
        jQuery(this) .removeClass('select-error');
    });
    jQuery('body').on('focus', '#ticketForm textarea', function(){
        jQuery(this) .removeClass('textarea-error');
    });
    
    var forceCreateTicket = false;
    
    jQuery('body').on('submit', '#ticketForm', function(){
        
        var isValid = true;
        var form = jQuery('#ticketForm');
        form.find('.input-error').removeClass('input-error');
        form.find('.textarea-error').removeClass('textarea-error');
        form.find('.select-error').removeClass('select-error');
        
        //Error if subject is null
        if(form.find('#suite_id').val() == '')
        {
            form.find('#suite_id').addClass('select-error');
            isValid = false;
        }
        
        //Error if subject is null
        if(form.find('#subject').val() == '')
        {
            form.find('#subject').addClass('input-error');
            isValid = false;
        }
        
        //Error if question is null
        if(form.find('#question').val() == '')
        {
            form.find('#question').addClass('textarea-error');
            isValid = false;
        }
        
        //Error if priority is null
        if(form.find('#ticket-priority').val() == '')
        {
            form.find('#ticket-priority').addClass('select-error');
            isValid = false;
        }
        
        //Error if type is null
        if(form.find('#ticket-category').val() == '')
        {
            form.find('#ticket-category').addClass('select-error');
            isValid = false;
        }
        
        //Error if the payment method is not selected
        if(form.find('#ticket-category option:selected').attr('has-fee') != 'no' && form.find('#prepurchased-tokens').val() == 0 && form.find('#ticket-card-id').val() == '')
        {
            form.find('#ticket-card-id').addClass('select-error');
            isValid = false;
        }
        
        if(isValid && !forceCreateTicket)
        {
            //Validate Using Ajax
            form.find('.loading').show();
            jQuery.ajax({
                url: '/',
                type: 'post',
                data: form.serialize(),
                dataType: 'xml',
                success: function(rsp){
                    if(jQuery(rsp).find('status').text() == 'success')
                    {
                        forceCreateTicket = true;
                        form.find('#ct-ticket-validate-action').remove();
                        form.find('#ct-ticket-create-action').attr('name', 'ct-ticket-action');                    
                        form.submit();
                    }else{
                        form.find('.loading').hide();        
                        form.find('.popup-box-content .message').remove();
                        form.find('.popup-box-content').append('<div class="message error" style="display: none">' + jQuery(rsp).find('error').text() + '</div>');
                        form.find('.popup-box-content .message').fadeIn();
                    }
                },
                error: function(error)
                {
                    form.find('.loading').hide();
                }
            })
        }
        
        return forceCreateTicket;
    });
    
    //Show Change Term Form
    jQuery('#change-term-link').click(function(){
        jQuery('#change-term-contr').fadeIn('fast');
        jQuery('#ticket-term-info').hide();
        return false;
    });
    
    jQuery('#changeTermForm #ticket-priority').change(function(){
        
        var ttresolve = jQuery(this).find('option:selected').attr('ttresolve');
        var ttresponse = jQuery(this).find('option:selected').attr('ttresponse');
        var price = jQuery(this).find('option:selected').attr('price');
        
        jQuery('#changeTermForm #term_ttresolve').find('span').html(ttresolve);
        jQuery('#changeTermForm #term_ttresolve').find('input[type="text"]').val(ttresolve);
        jQuery('#changeTermForm #term_ttresponse').find('span').html(ttresponse);
        jQuery('#changeTermForm #term_ttresponse').find('input[type="text"]').val(ttresponse);
        if(jQuery('#changeTermForm #term_price').find('span').html() != 'Free')
        {
            jQuery('#changeTermForm #term_price').find('span').html(price + " Tokens/hr");
            jQuery('#changeTermForm #term_price').find('input[type="text"]').val(price);        
        }
    })
    
    jQuery('#change-term-contr .cancel-btn').click(function(){
        jQuery('#change-term-contr').hide();
        jQuery('#ticket-term-info').fadeIn('fast');
        return  false;
    });
    
    jQuery('body').on('click', '#add-attachment-link', function(){
        jQuery('.attachments-wrap').append('<div class="file-item">' +
                    '<input type="file" name="attachments[]" />' + 
                    '<a href="#" class="action-btn delete-btn icon-btn"><span class="p"></span></a>' +
                '</div><div class="clear"></div>');
        customizeFileTag();
        return false;
    })
    
    jQuery('body').on('click', '.attachments-wrap .delete-btn', function(){
        jQuery(this).parent().fadeOut("fast", function(){
            jQuery(this).remove();
        })
        return false;
    })
    
    //Submit Term Changes
    jQuery('#changeTermForm').submit(function(){
        var isValid = true;
        
        jQuery('#changeTermForm .input-error').removeClass('input-error');
        
        //Validate the input values
        if( jQuery('#changeTermForm #ttpay').length > 0 && (!jQuery('#changeTermForm #ttpay').val() || isNaN(jQuery('#changeTermForm #ttpay').val())) )
        {
            jQuery('#changeTermForm #ttpay').addClass("input-error");
            isValid = false;
        }
        
        if(jQuery('#changeTermForm #ttpay').length > 0 && (!jQuery('#changeTermForm #ttresolve').val() || isNaN(jQuery('#changeTermForm #ttresolve').val())))
        {
            jQuery('#changeTermForm #ttresolve').addClass("input-error");
            isValid = false;
        }
        
        if(jQuery('#changeTermForm #ttpay').length > 0 && (!jQuery('#changeTermForm #ttresponse').val() || isNaN(jQuery('#changeTermForm #ttresponse').val())))
        {
            jQuery('#changeTermForm #ttresponse').addClass("input-error");
            isValid = false;
        }
        
        if(isValid)
        {
            jQuery('#changeTermForm .loading').show();
        }
        
        return isValid;
        
    })
    
    jQuery('#newMessageForm').submit(function(){
        var isValid = true;
        
        jQuery('#newMessageForm .input-error').removeClass('textarea-error');
        
        //Validate the input values
        if(!jQuery('#newMessageForm #message-content').val())
        {
            jQuery('#newMessageForm #message-content').addClass("textarea-error");
            isValid = false;
        }
        
        if(isValid)
            jQuery('#new-message-wrap .loading').show();
            
        return isValid;
    })
    
})