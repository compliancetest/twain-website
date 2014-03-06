
jQuery(document).ready(function($){
    // Forum
    $('#ct_forum_subscription').change(function(){
        var action = 'cp_forum_notify_all_active';
        if ($(this).attr('checked') != 'checked') {
            action = 'cp_forum_notify_all_deactive';    
        }
    });
});
