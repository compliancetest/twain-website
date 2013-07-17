(function($){
    $(document).ready(function(){
        $('#print-static-page-btn').click(function(){
            var pWin = window.open('', 'print-page', 'location=no,menubar=0,resizable=1,scrollbars=1,width=900,height=500');
            pWin.document.write('<!DOCTYPE HTML>' + 
                '<html>' + 
                '<head profile="http://gmpg.org/xfn/11">' + 
                '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />' + 
                '<title>' + $('title').text() + '</title>' + 
                '</head>' + 
                '<body>' + 
                '<style type="text/css">' + 
                "body{font-family: 'Open Sans',Arial,Tahoma,Helevetica,sans-serif; font-size: 12px; line-height: 14px; color: #111;}" + 
                "a{color: #2c80e8; text-decoration: none; }" +                
                "h2{font-size: 20px;}h3{font-size: 18px;}h4{font-size: 16px}h5{font-size: 14px;}" +                
                "table{border: solid 1px #333; border-collapse: collapse; vertical-align: top;}th{text-align: left; font-weight: bolid; border: solid 1px #333; padding: 5px;}td{border: solid 1px #333;  padding: 5px;}" + 
                '</style>' + 
                '</body>' + 
                '</html>'
            );
            $(pWin.document).find('body').append($('.container').html());
            $(pWin.document).find('.print-btn').remove();
            pWin.print();
            return false;
        })
    })
})(jQuery)