<div class="modal-header">
    <button type="button" class="close-modal" data-tooltip="tooltip" title="Close popup" data-placement="left" data-dismiss="modal" aria-label="Close">Close</button>
    API Request Data
</div>
<div class="modal-body">
    <div id="data"></div>
</div>
<div class="modal-footer">
    <button type="button" data-dismiss="modal" aria-label="Close" class="btn btn-default btn-with-icon btn-cancel">Close</button>
</div>
<script type="text/javascript">
    var t_data = Jsonary.create({!! $data !!}).readOnlyCopy();
    var t_element = document.getElementById('data');
    Jsonary.render(t_element, t_data);
</script>