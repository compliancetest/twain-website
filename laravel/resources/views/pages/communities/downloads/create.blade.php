{{ Form::open(['file' => true, 'id' => 'newfileform', 'action' => 'CommunityDownloadsController@store']) }}

    <h3>Upload New File(s)</h3>

    @include('pages.communities.downloads.partials.form')

{{ Form::close() }}

{{--<script>--}}
    {{--$(document).on('submit', 'form.AjaxForm', function() {--}}
        {{--$.ajax({--}}
            {{--type: 'post',--}}
            {{--url: '/communities/downloads',--}}
            {{--data: data,--}}
            {{--dataType: 'json',--}}
            {{--success: function(data){--}}
                {{--location.href(data.redirect_to);--}}
            {{--},--}}
            {{--error: function(data){--}}
                {{--var errors = data.responseJSON;--}}
                {{--console.log(errors);--}}
            {{--}--}}
        {{--});--}}
        {{--return false;--}}
    {{--});--}}
{{--</script>--}}