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
        
        
        return false;
    })
})