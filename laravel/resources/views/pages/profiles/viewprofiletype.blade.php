<div class="modal-header">
    <button type="button" class="close-modal" data-tooltip="tooltip" title="Close popup" data-placement="left" data-dismiss="modal" aria-label="Close">Close</button>
    Profile Type Detail
</div>
    <div class="modal-body">

        <div class="form-group row">

            <div id="json-view-panel{{ $profileType->id }}" class="json-view-panel"></div>

            <script type="text/javascript">
                var t_data = Jsonary.create({!! $content !!}).readOnlyCopy();
                var t_element = document.getElementById('json-view-panel{{ $profileType->id }}');
                Jsonary.render(t_element, t_data);
            </script>
        </div>
    </div>
<div class="modal-footer">
    <a href="{{ getSiteUrl() }}/profiletypes/{{ $community->slug }}/downloadprofiletype/{{ $profileType->id }}" class="btn btn-success btn-with-icon btn-confirm">Download</a>
    <a href="#" class="btn btn-default btn-with-icon btn-cancel" data-dismiss="modal">Cancel</a>
</div>