(function($){
    $('#group_admin_page .process-btn').click(function(){
        if($(this).hasClass('no-submit'))
            return true;
        $(this).parents('form').submit();
        return false;
    })
    $('#group_admin_page #group_avatar_box #upload-image-btn').click(function(){
        if($('#group_avatar_box #file').val() != '')
        {
            $(this).parents('form').submit();    
        }
        return false;
    })
    $('#group-details-form').submit(function(){
        var form = $(this);
        form.find('.message').remove();
        if(form.find('#group-name').val() == '' || form.find('#group-desc').val() == '')
        {
            form.find('.btn-row').prepend('<div class="message error">Community name and description should not be blank!</div>');
            return false;
        }
        
        form.find('.loading').show();
        $.ajax({
            url: form.attr('action'),
            type: 'post',
            data: form.serialize(),
            dataType: 'json',
            success: function(rsp){                
                form.find('.btn-row').prepend('<div class="message ' + rsp.result + '">' + rsp.message + '</div>');
            },
            complete: function(){
                form.find('.loading').hide();
            }
        })
        
        return false;
    })
    
    //Delete Group
    $('#group_remove_box .delete-btn').click(function(){
        $('#group-remove-form').submit();
        return false;    
    })
    $('#group-remove-form').submit(function(){        
        $(this).find('.btn-row').find('.message').remove();
        if(!$(this).find('#delete-group-understand').prop('checked'))
        {
            $(this).find('.btn-row').append('<div class="message error">Please make sure that you understand the consequences of deleting this group.!</div>');
            return false;
        }
    })
    
    //Settings Box
    $('#group-invitation-form, #group-privacy-form').submit(function(){
        var form = $(this);
        form.find('.message').remove();
        form.find('.loading').show();
        $.ajax({
            url: form.attr('action'),
            type: 'post',
            data: form.serialize(),
            dataType: 'json',
            success: function(rsp){                
                form.find('.btn-row').prepend('<div class="message ' + rsp.result + '">' + rsp.message + '</div>');
            },
            complete: function(){
                form.find('.loading').hide();
            }
        })
        
        return false;
    })
    
    //Settings Box
    $('#group-article-settings-form').submit(function(){
        var form = $(this);
        form.find('.message').remove();
        form.find('.loading').show();
    })
    
    //Manage Members Page
    $('#group-members-form').submit(function(){
        if($('#group-members-form #action').val() == '')    
            return false;
        
        //Checked inputs
        if($('#group-members-form .chk:checked').length < 1)    
            return false;
    })
    $('#group-members-form .nav a').click(function(){
        $('#group-members-form #action').val($(this).attr('data-action'));
        $('#group-members-form').submit();
        return false;
    })
    
    // Generate JSON
    $('#group_generate_json_box #upload-profile-excel-btn').click(function(){
        if($('#group_generate_json_box #profile_excel_file').val() != '')
        {
            $(this).parents('form').submit();    
        }
        return false;
    })
})(jQuery)