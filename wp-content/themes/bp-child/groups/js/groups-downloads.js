(function($){
  $(document).ready(function(){
      $('#add-new-download').click(function(){
          $('#new-downloads').fadeIn('fast');
          $('#uploaded-files .grid-list-footer').hide();
          $('#new-downloads').fadeIn('fast');
          $('#new-downloads').find('textarea').redactor({
              air: true,
              minHeight: 80
          })
          return false;
      })
      $('#add-more-file').click(function(){
          $('#new-downloads .grid-list-footer').before('<div class="grid-list-row">' + 
                    '<div class="grid-list-cell left15 grid-field-cell">' +
                        '<label>File Version:</label>' +
                        '<input type="text" class="input" name="file_version[]" value="" />' +
                    '</div>' +
                    '<div class="grid-list-cell width35P">' +
                        '<input type="file" name="file[]" class="input-file" />' +
                        '<a href="#" class="action-btn delete-btn" id="cancel-download"><span class="p"></span><span class="t">Remove</span></a>    ' +
                    '</div>' +
                    '<div class="clear"></div>' +
                    '<div class="grid-field-cell grid-list-cell width85P">                        ' +
                        '<label>Description:</label>' +
                        '<textarea cols="20" rows="5" name="file_description[]" class="text"></textarea><br clear="all">' +
                        '<label>File License Agreement:</label>' +
                        '<textarea cols="20" rows="5" name="file_license[]" class="text"></textarea>' +
                    '</div>' +
                    '<div class="clear"></div>' +
                '</div>');
          $('#new-downloads .grid-list-footer').prev().find('textarea').redactor({
              air: true,
              minHeight: 80
          })
          customizeFileTag();
          return false;
      })
      $('#new-downloads .delete-btn').click(function(){
          if($('#new-downloads .grid-list-row').length == 2)
          {
              $('#new-downloads .grid-list-row').find('input[type="text"], textarea').val('');
              $('#new-downloads').hide();
              $('#uploaded-files .grid-list-footer').fadeIn('fast');
              $('#new-downloads .message').hide();
          }else{
              $(this).parents('.grid-list-row').fadeOut('fast', function(){
                  $(this).remove();
              })
          }
          return false;
      })
      $('#new-downloads .cancel-btn').click(function(){
          $('#new-downloads .grid-list-row:not(.grid-list-footer)').each(function(idx){
              if(idx > 0)
                $(this).remove();
          })
          $('#new-downloads .grid-list-row').find('input[type="text"], textarea').val('');
//          $('#new-downloads .grid-list-row').find('input[name="file_version[]"]').val('1.0');
          $('#new-downloads').hide();
          $('#uploaded-files .grid-list-footer').fadeIn('fast');
          $('#new-downloads .message').hide();
          
          return false;
      })
      
      $('#newfileform').submit(function(){
          //Check jif there is a selected file or not
          var form = $(this);
          form.find('.message').hide();
          if(form.find('input[type="file"]').length < 1)
          {
              form.find('.message').html('There is no file to upload.').addClass('error').fadeIn();
              return false;
          }
          var isValid = true;
          form.find('input[type="file"]').each(function(){
              if($(this).val() == '')
              {
                  isValid = false;
              }
          })
          if(!isValid)
          {
              form.find('.message').html('Please select file(s) that you need to upload.').addClass('error').fadeIn();
              return false;
          }
      })
      $('#save-download').click(function(){
        $('#newfileform').submit();    
        return false;
      })

      $('.download-link[rel="has-license"]').cplightbox({
          type: 'ajax',
          removeBoxAfterClose: true,
          onLoad: function(){
              $('#agree-file-license .cancel-btn').click(function(){
                  $('#agree-file-license .close_btn').click();
                  return false;
              });
              //$('#agree-file-license .download-btn').click(function(){
              //    $('#agree-file-license form').submit();
              //    return false;
              //});
              $('#agree-file-license form').submit(function(){
                  form = $(this);
                  $('#agree-file-license .message').hide();
                  if($(form).find('#agree_community_license').length > 0 && $(form).find('#agree_community_license:checked').length < 1)
                  {
                      $('#agree-file-license .message').html('Please agree with the License Agreement.').fadeIn();
                      return false;
                  }
                  
                  $.ajax({
                      url: $(form).attr('action'),
                      type: 'post',
                      data: $(form).serialize(),
                      success: function(rsp)
                      {
                          if(rsp == 'error')
                          {
                              $('#agree-file-license .message').html('Invalid Request!').fadeIn();
                          }else{
                              $('#agree-file-license .close_btn').click();
                              document.location.href = rsp;
                          }
                      }
                  })
                  return false;
              })
          }
      })


      $('#uploaded-files').on('click', '.cancel-btn', function(){
          var fID = $(this).attr('data-id');
          $('#fileEditRow' + fID).hide();
          $('#fileRow' + fID).fadeIn('fast');
          return false;
      })
      $('#delete-file-box form').submit(function(){
          $('#delete-file-box form .loading').show();
      })
      $('#uploaded-files .delete-file-link').each(function(){
          var link = $(this).attr('href');
          jQuery(this).cplightbox({
              type: 'ajax',
              href: link
          })
      });
  })
})(jQuery)