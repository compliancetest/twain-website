(function($){
  $(document).ready(function(){
    //Join to Community
    $('.join-community-button').each(function(){
        var cId = jQuery(this).parent().attr('id').substr(12)
        $(this).cplightbox({
            href: '#community-registration' + cId
        });
    })
    
    //Join Community
    $('.community-registration-box .process-btn').on('click', function(){
        var communityId=  $(this).attr('data-id');
        var form = $('#community-registration' + communityId + ' form');        
        
        if(!form.find('#agree_community_terms').prop('checked') /*|| !form.find('#agree_community_license').prop('checked')*/)
        {
            $('.community-registration-box .message').html('You must agree the community Terms & Conditions.').addClass('error').fadeIn('fast');
            return false;
        }
        $('#community-registration' + communityId + ' .message').hide();
        gid = form.attr('data-group-id');
        
        var nonce = form.attr('action');
        nonce = nonce.split('?_wpnonce=');
        nonce = nonce[1].split('&');
        nonce = nonce[0];
        
        $('#community-registration' + communityId + ' .loading').show();
        $.post( ajaxurl, {
                action: 'joinleave_group',
                'cookie': encodeURIComponent(document.cookie),
                'gid': gid,
                '_wpnonce': nonce
            },
            function(response){            
                $('#community-registration' + communityId + ' .loading').hide();
                if(response == 'Error joining group' || response == 'Error requesting membership')
                {
                    $('#community-registration' + communityId + ' .message').html(response).addClass('error').fadeIn('fast');
                }else{
                    //Change the Request Membership Button
                    $('#groupbutton-' + communityId + ' a.join-community-button').unbind('click').attr('href', '#').attr('class', 'group-button pending membership-requested button button_medium status_deprecated white_txt radius6').html('Request Sent');
                    $('#community-registration' + communityId + ' .message').html('Your request has been sent successfully!').removeClass('error').addClass('success').fadeIn('fast');
                    setTimeout(function(){
                        $('#community-registration' + communityId + ' .close_btn').click();
                    }, 2000);
                }
            }
        );
        return false;
    })
    
    /*$('.community-registration-box .cancel-btn').click(function(){
        $('.community-registration-box .close_btn').click();
    })*/
 
    $('.community-terms-box .process-btn').click(function(){
        $('#community-registration' + $(this).attr('data-id') + ' #agree_community_terms').prop('checked', true);
        $('#groupbutton-' + $(this).attr('data-id') + ' a.join-community-button').click(); 
        return false;
    })
    $('.community-terms-box .cancel-btn').click(function(){
        $('#groupbutton-' + $(this).attr('data-id') + ' a.join-community-button').click(); 
        return false;
    })
    
    $('.community-license-box .process-btn').click(function(){
        $('#community-registration' + $(this).attr('data-id') + ' #agree_community_license').prop('checked', true);
        $('#groupbutton-' + $(this).attr('data-id') + ' a.join-community-button').click(); 
        return false;
    })
    $('.community-license-box .cancel-btn').click(function(){
        $('#groupbutton-' + $(this).attr('data-id') + ' a.join-community-button').click(); 
        return false;
    })
  })
})(jQuery)