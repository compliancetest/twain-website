<div class="modal-header">
    <button type="button" class="close-modal" data-tooltip="tooltip" title="Close popup" data-placement="left" data-dismiss="modal" aria-label="Close">Close</button>
    Edit Surveys Results Links
</div>
<div class="modal-body block-loading-wrapper">
    <div class="edit-profile-form-wrapper" id="createProfileBox">

        {!! Form::open(['id' => 'linksForm', 'class' => 'standard-form', 'data-save-method' => 'ajax', 'method' => 'POST', 'url' => getSiteUrl() . '/communitysurveys/'.$community->slug.'/surveyresults']) !!}
            <table class="table table-bordered">
                <tr>
                    <th>Title</th>
                    <th>Link</th>
                </tr>
                @foreach($surveys as $survey)
                    <tr>
                        <td>{{ $survey['title'] }}</td>
                        <td>
                            <input type="text" name="links[{{ $survey['id'] }}]"
                                   @if(isset($links[$survey['id']])) value="{{ $links[$survey['id']]->link }}" @endif >
                        </td>
                    </tr>
                @endforeach
            </table>
        </form>
    </div>

</div>
<div class="modal-footer">
    <a href="#" class="btn btn-success btn-with-icon btn-confirm">Save</a>
    <a href="#" class="btn btn-default btn-with-icon btn-cancel" data-dismiss="modal">Cancel</a>
</div>
<div class="block-loading" id="CreateProfileLoading"><div class="loading-content"><span class="loader"></span><div class="loading-text">LOADING</div><div class="loading-wait">Please wait...</div></div></div>

<script>
    jQuery('.btn-confirm').on('click', function(e){
        jQuery('.message').hide();
        e.preventDefault();
        jQuery('#CreateProfileLoading').show();
        jQuery('#linksForm').ajaxSubmit({
            success: function(rsp)
            {
                if(rsp.status == 'success')
                {
                    jQuery('#modalEditSurveys .modal-footer').prepend('<p class="message success-message">Successfully saved!</p>');
                    jQuery('.close-modal').delay('2000').click();
                }else{
                    jQuery('#modalEditSurveys .modal-footer').prepend('<p class="message error-message">' + rsp.message + '</p>');
                }
            },
            error: function(rsp){
                jQuery('#modalEditSurveys .modal-footer').prepend('<p class="message error-message">' + rsp.responseJSON.message + '</p>');
            },
            complete: function(rsp){
                jQuery('#CreateProfileLoading').hide();
            }
        })
    });
</script>
