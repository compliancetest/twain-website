(function($){
  $(document).ready(function(){
      $('#add-new-download').click(function(){
          $('#new-downloads').fadeIn('fast');
          $('#uploaded-files .grid-list-footer').hide();
          return false;
      })
      $('#add-more-file').click(function(){
          var newRow = $('#new-downloads .grid-list-row:eq(0)').clone(true);
          newRow.find('input[type="text"], textarea').val('');
          newRow.hide();
          $('#new-downloads .grid-list-footer').before(newRow);
          newRow.fadeIn('fast');
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
          $('#new-downloads').hide();
          $('#uploaded-files .grid-list-footer').fadeIn('fast');
          $('#new-downloads .message').hide();
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
              $('#agree-file-license .process-btn').click(function(){
                  $('#agree-file-license form').submit();
                  return false;
              });
              $('#agree-file-license form').submit(function(){
                  form = $(this);
                  $('#agree-file-license .message').hide();
                  if($(form).find('#agree_community_license').length > 0 && $(form).find('#agree_community_license:checked').length < 1)
                  {
                      $('#agree-file-license .message').html('Please aggree the License Agreement.').fadeIn();
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

      
      //Show Edit File Form
      $('#uploaded-files .blue-edit-btn').click(function(){
          var link = $(this);
          var fID = link.attr('data-id');
          var rowObj = $('#fileRow' + fID);
          rowObj.find('.message').remove();
          if($('#fileEditRow' + fID).length < 1)
          {
              rowObj.append('<div class="loading"></div>');
              rowObj.find('.loading').show();              
              $.ajax({
                  url: link.attr('href'),
                  type: 'get',
                  dataType: 'html',
                  success: function(rsp){
                      rowObj.find('.loading').remove();
                      if($(rsp).find('form').length > 0){
                          rowObj.after(rsp);
                          $('#fileRow' + fID).hide();
                          $('#fileEditRow' + fID).fadeIn('fast');
                      }else{
                          rowObj.append(rsp);
                          rowObj.find('.message').fadeIn('fast');
                          
                      }
                  }
              })

          }else{
              $('#fileRow' + fID).hide();
              $('#fileEditRow' + fID).fadeIn('fast');
          }
          return false;
      })
      
      $('#uploaded-files').on('click', '.cancel-btn', function(){
          var fID = $(this).attr('data-id');
          $('#fileEditRow' + fID).hide();
          $('#fileRow' + fID).fadeIn('fast');
          return false;
      })
  })
})(jQuery)