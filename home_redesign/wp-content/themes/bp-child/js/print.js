(function($){
    $(document).ready(function(){
        $('.print-page-btn').click(function(){            
            var pWin = window.open(jQuery(this).attr('href'), 'printpage', 'location=no,menubar=0,resizable=1,scrollbars=1,width=900,height=500');
            return false;
        })
    })
})(jQuery)