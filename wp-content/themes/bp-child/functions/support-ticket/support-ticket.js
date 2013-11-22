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
    jQuery('body').on('submit', '#ticketForm', function(){
        
        var isValid = true;
        var form = jQuery('#ticketForm');
        
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
        
        if(isValid)
        {
            form.find('.loading').show()
        }
        
        return isValid;
    });
    
    //Show Change Term Form
    jQuery('#change-term-link').click(function(){
        jQuery('#change-term-contr').fadeIn('fast');
        jQuery('#term-actions').hide();
        return false;
    });
    
    jQuery('#change-term-contr .cancel-btn').click(function(){
        jQuery('#change-term-contr').fadeOut('fast');
        jQuery('#term-actions').show();
        return  false;
    });
    
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
        jQuery('#new-message-wrap .loading').show();
        return isValid;
    })
    
})