(function($){
  $(document).ready(function(){
    //Join to Community
    $('#item-buttons a.request-membership').cplightbox({
        href: '#community_registration'
    });
    
    //Join Community
    $('#community_registration .process-btn').on('click', function(){
        var form = $('#join-community-form');
        if(!form.find('#agree_community_terms').prop('checked') || !form.find('#agree_community_license').prop('checked'))
        {
            $('#community_registration .message').html('You must agree the community Terms & Conditions and License Agreement.').addClass('error').fadeIn('fast');
            return false;
        }
        $('#community_registration .message').hide();
        gid = form.attr('data-group-id');
        
        var nonce = form.attr('action');
        nonce = nonce.split('?_wpnonce=');
        nonce = nonce[1].split('&');
        nonce = nonce[0];
        $('#community_registration .loading').show();
        $.post( ajaxurl, {
                action: 'joinleave_group',
                'cookie': encodeURIComponent(document.cookie),
                'gid': gid,
                '_wpnonce': nonce
            },
            function(response){            
                $('#community_registration .loading').hide();
                if(response == 'Error joining group' || response == 'Error requesting membership')
                {
                    $('#community_registration .message').html(response).addClass('error').fadeIn('fast');
                }else{
                    $('#community_registration .close_btn').click();            
                    //Change the Request Membership Button
                    $('#item-buttons a.request-membership').unbind('click').attr('href', '#').attr('class', 'group-button pending membership-requested button button_medium status_btn_deprecated white_txt radius6').html('Request Sent');
                    $('#community_registration .message').html('Your request has been sent successfully!').removeClass('error').addClass('success').fadeIn('fast');
                    setTimeout(function(){
                        $('#community_registration .close_btn').click();    
                    }, 5000);
                }
            }
        );
        return false;
    })
    
    $('#community_registration .cancel-btn').click(function(){
        $('#community_registration .close_btn').click();
    })
 
    $('#community-terms-box .process-btn').click(function(){
        $('#join-community-form #agree_community_terms').prop('checked', true);
        $('#item-buttons a.request-membership').click(); 
        return false;
    })
    $('#community-terms-box .cancel-btn').click(function(){
        $('#item-buttons a.request-membership').click(); 
        return false;
    })
    
    $('#community-license-box .process-btn').click(function(){
        $('#join-community-form #agree_community_license').prop('checked', true);
        $('#item-buttons a.request-membership').click(); 
        return false;
    })
    $('#community-license-box .cancel-btn').click(function(){
        $('#item-buttons a.request-membership').click(); 
        return false;
    })
  })
})(jQuery)