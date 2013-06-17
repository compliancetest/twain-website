(function($){
  $(document).ready(function(){

    /************************************************ Downloads **********************************************************/
    $('#add-new-download').click(function(){
        $(this).parents('.grid_row').before('<div class="grid_row grey-border-bottom">' +         
            '<div class="grid_cell width60P">' +
                '<b>Name(optional)</b><br />' +
                '<input type="text" class="input" name="file_name[]" />' +
                '<b>Description(optional)</b><br />' +
                '<input type="text" class="input" name="file_description[]" class="text" />' + 
                '<b>License Agreement</b><br />' + 
                '<textarea cols="20" rows="5" name="file_license[]" class="text"></textarea>' + 
            '</div>' +
            '<div class="grid_cell width25P"><input type="file" name="file[]" /></div>' +
            '<div class="grid_cell width10P"><a href="#" class="delete-file">Delete</a></div>' +
            '<div class="clear"></div>' +
        '</div>');
        return false;
    })
    
      $('#newfileform').on('click', '.delete-file', function(){
          $(this).parents('.grid_row').fadeOut('fast', function(){
              $(this).remove();
          });
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
      
      $('#uploaded-files .delete-btn').click(function(){
          var link = $(this);
          $.ajax({
              type: 'get',
              url: link.attr('href'),
              success: function(rsp)
              {
                  if(rsp == 'success')
                  {
                      link.parents('.grid_row').fadeOut('fast', function(){
                          $(this).remove();
                      });
                  }else{
                      $('#newfileform').find('.message').html(rsp).addClass('error').fadeIn();
                  }
              }
          });
          return false;
      })
      
      //Download File
      $('#downloadform').submit(function(){
          if($(this).find('#agree_license').length > 0 && !$(this).find('#agree_license').prop('checked'))
          {
              return false;
          }
      })
      
      //Show Edit File Form
      $('#uploaded-files .edit-btn').click(function(){
          var link = $(this);
          var rowObj = link.parents('.grid_row');
          if(rowObj.find('form').length < 1)
          {
              rowObj.append('<div class="loading"></div>');
              rowObj.find('.message').remove();
              $.ajax({
                  url: link.attr('href'),
                  type: 'get',
                  dataType: 'html',
                  success: function(rsp){
                      rowObj.find('.loading').remove();
                      rowObj.append(rsp);
                      if(rowObj.find('.message').length > 0){
                          rowObj.find('.message').fadeIn('fast');
                      }else{
                          rowObj.children(':not(form)').hide();
                          rowObj.find('form').fadeIn('fast');  
                      }
                  }
              })
          }
          return false;
      })
  })
})(jQuery)