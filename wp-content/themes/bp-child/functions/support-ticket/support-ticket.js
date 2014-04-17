jQuery(document).ready(function(){
    jQuery('#submit-ticket-btn').cplightbox({
        type: 'ajax',
        closeWhenClickOveraly: false
    })
    
    //Change Priority and Type
    jQuery('body').on('change', '#ticket-priority', function(){
        if(jQuery(this).val() == '')
        {
            jQuery('#ttresponse').val('');
            jQuery('#ttresolve').val('');
            jQuery("#ticket-price-row").hide();    
            jQuery("#ticket-time-row").hide();    
        }else{                        
            jQuery('#ttresponse').val(jQuery('option:selected', this).attr('ttresponse'));
            jQuery('#ttresolve').val(jQuery('option:selected', this).attr('ttresolve'));            
            jQuery("#ticket-time-row").show();    
            if(jQuery('#ticket-category').val() != '')
            {
                jQuery('#ticket-price').html(jQuery('#ticket-category option:selected').attr('has-fee') == 'no' ? 'Free' : ('$' + jQuery('option:selected', this).attr('price') + '/hr') );
                jQuery('#ticket-price-row').show();
            }
            
        }
    })
    jQuery('body').on('change', '#ticket-category', function(){
        if(jQuery(this).val() == '')
        {
            jQuery("#ticket-price-row").hide();    
        }else if(jQuery('#ticket-priority').val() != ''){            
            jQuery('#ticket-price').html(jQuery('option:selected', this).attr('has-fee') == 'no' ? 'Free' : ('$' + jQuery('#ticket-priority option:selected').attr('price') + '/hr') );
            jQuery('#ticket-price-row').show();                   
        }
    })
    
    
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
        if(form.find('#ticket-category option:selected').attr('has-fee') != 'no' && form.find('#ticket-card-id').val() == '')
        {
            form.find('#ticket-card-id').addClass('select-error');
            isValid = false;
        }
        
        if(isValid)
        {
            form.find('.loading').show()
        }
        
        return isValid;
    });
    
    //Show Change Term Form
    jQuery('#change-term-link').click(function(){
        jQuery('#change-term-contr').fadeIn('fast');
        jQuery('#ticket-term-info').hide();
        return false;
    });
    
    jQuery('#change-term-contr .cancel-btn').click(function(){
        jQuery('#change-term-contr').hide();
        jQuery('#ticket-term-info').fadeIn('fast');
        return  false;
    });
    
    jQuery('#add-attachment-link').click(function(){
        jQuery('#new-message-wrap .attachments-wrap').append('<div class="file-item">' + 
                    '<input type="file" name="attachments[]" />' + 
                    '<a href="#" class="action-btn delete-btn icon-btn"><span class="p"></span></a>' +
                '</div>');
        customizeFileTag();
        return false;
    })
    
    jQuery('#new-message-wrap .attachments-wrap').on('click', '.delete-btn', function(){
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
        if(!jQuery('#changeTermForm #ttpay').val() || isNaN(jQuery('#changeTermForm #ttpay').val()))
        {
            jQuery('#changeTermForm #ttpay').addClass("input-error");
            isValid = false;
        }
        
        if(!jQuery('#changeTermForm #ttresolve').val() || isNaN(jQuery('#changeTermForm #ttresolve').val()))
        {
            jQuery('#changeTermForm #ttresolve').addClass("input-error");
            isValid = false;
        }
        
        if(!jQuery('#changeTermForm #ttresponse').val() || isNaN(jQuery('#changeTermForm #ttresponse').val()))
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