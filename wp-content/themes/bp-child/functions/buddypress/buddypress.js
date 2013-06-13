(function($){
    //Join to Community
    $('#item-buttons a.request-membership').on('click', function() {        
        //Show Join Community Popup Box
        $('#community_registration').cplightbox();        
        return false;
    } );
    
    //Join Community
    $('#community_registration .process-btn').on('click', function(){
        var form = $('#join-community-form');
        if(!form.find('#agree_community_terms').prop('checked') || !form.find('#agree_community_license').prop('checked'))
        {
            $('#join-community-form .message').html('You must agree the community Terms & Conditions and License Agreement.').addClass('error').fadeIn('fast');
            return false;
        }
        $('#join-community-form .message').hide();
        gid = form.attr('data-group-id');
        
        var nonce = form.attr('action');
        nonce = nonce.split('?_wpnonce=');
        nonce = nonce[1].split('&');
        nonce = nonce[0];

        $.post( ajaxurl, {
                action: 'joinleave_group',
                'cookie': encodeURIComponent(document.cookie),
                'gid': gid,
                '_wpnonce': nonce
            },
            function(response){            
                if(response == 'Error joining group' || response == 'Error requesting membership')
                {
                    $('#join-community-form .message').html(response).addClass('error').fadeIn('fast');
                }else{
                    $('#community_registration .close_btn').click();            
                    //Change the Request Membership Button
                    $('#item-buttons a.request-membership').unbind('click').attr('href', '#').attr('class', 'group-button pending membership-requested button button_medium status_btn_on_hold white_txt radius6').html('Request Sent');
                    alert('Your request has been sent successfully!');
                }
            }
        );
        return false;
    })
    
    $('#community_registration .cancel-btn').click(function(){
        $('#community_registration .close_btn').click();
    })
    
    //Show Community Terms and License Boxes
    $('#show-community-terms').click(function(){
        $('#community-terms-box').cplightbox({closeWhenClickOveraly: false});
        return false;
    })
    $('#show-community-license').click(function(){
        $('#community-license-box').cplightbox({closeWhenClickOveraly: false});
        return false;
    })
    $('#community-terms-box .process-btn').click(function(){
        $('#join-community-form #agree_community_terms').prop('checked', true);
        $('#community_registration').cplightbox(); 
        return false;
    })
    $('#community-terms-box .cancel-btn').click(function(){
        $('#community_registration').cplightbox(); 
        return false;
    })
    
    $('#community-license-box .process-btn').click(function(){
        $('#join-community-form #agree_community_license').prop('checked', true);
        $('#community_registration').cplightbox(); 
        return false;
    })
    $('#community-license-box .cancel-btn').click(function(){
        $('#community_registration').cplightbox(); 
        return false;
    })
    
})(jQuery)