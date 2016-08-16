<div class="modal-header">
    <button type="button" class="close-modal" data-tooltip="tooltip" title="Close popup" data-placement="left" data-dismiss="modal" aria-label="Close">Close</button>
    Message Data
</div>
<div class="modal-body">
    <div id="data"></div>
</div>
<div class="modal-footer">
    <a href="{{ $link }}" target="_blank" class="btn btn-success btn-with-icon btn-download">Download</a>
    <button type="button" data-dismiss="modal" aria-label="Close" class="btn btn-default btn-with-icon btn-cancel">Close</button>
</div>
<script type="text/javascript">
    var t_data = Jsonary.create({!! $data !!}).readOnlyCopy();
    var t_element = document.getElementById('data');
    Jsonary.render(t_element, t_data);
</script>