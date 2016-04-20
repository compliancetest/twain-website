<div class="modal-header">
    <button type="button" class="close-modal" data-tooltip="tooltip" title="Close popup" data-placement="left" data-dismiss="modal" aria-label="Close">Close</button>
    Profile Instance Detail
</div>
    <div class="modal-body">
        <div class="form-group row">
            <div class="col-sm-9">
                <input type="text" class="form-control" readonly id="profile-link-{{ $profile->id }}" value="{{ url()->to('/', [], true) }}/get-profile?id={{ $profile->token }}" />
            </div>
            <div class="col-sm-3">
                <button class="btn btn-success btn-with-icon btn-confirm copyProfileLink" data-clipboard-target="#profile-link-{{ $profile->id }}">Copy URL</button>
            </div>
        </div>

        <div class="form-group row">
            <div id="json-view-panel{{ $profile->id }}" class="json-view-panel"></div>

            <script type="text/javascript">
                var t_data = Jsonary.create({!! $content !!}).readOnlyCopy();
                var t_element = document.getElementById('json-view-panel{{ $profile->id }}');
                Jsonary.render(t_element, t_data);
            </script>
        </div>
    </div>
<div class="modal-footer">
    <a href="{{ $profile->getS3Link() }}" class="btn btn-success btn-with-icon btn-confirm">Download</a>
    <a href="#" class="btn btn-default btn-with-icon btn-cancel" data-dismiss="modal">Cancel</a>
</div>