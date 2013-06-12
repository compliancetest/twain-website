(function($){
    //Join to Community
    $('#item-buttons a.request-membership').on('click', function() {        
        //Show Join Community Popup Box
        $('#community-wrap').cplightbox();        
        return false;
    } );
    
    //Join Community
    $('#join-community-form').on('submit', function(){
        var form = $(this);
        gid = $(this).attr('data-group-id');
        
        var nonce = $(this).action('href');
        nonce = nonce.split('?_wpnonce=');
        nonce = nonce[1].split('&');
        nonce = nonce[0];

        $.post( ajaxurl, {
            action: 'joinleave_group',
            'cookie': encodeURIComponent(document.cookie),
            'gid': gid,
            '_wpnonce': nonce
        },
        function(response)
        {            
            
            alert(response);
            
        });
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
        $('#community-wrap').cplightbox(); 
        return false;
    })
    $('#community-terms-box .cancel-btn').click(function(){
        $('#community-wrap').cplightbox(); 
        return false;
    })
    
    $('#community-license-box .process-btn').click(function(){
        $('#join-community-form #agree_community_license').prop('checked', true);
        $('#community-wrap').cplightbox(); 
        return false;
    })
    $('#community-license-box .cancel-btn').click(function(){
        $('#community-wrap').cplightbox(); 
        return false;
    })
    
})(jQuery)