<div class="popup-box" id="view-validation-log-box" style="display: none; width: 500px;">
    <div class="popup-box-header radius6 noradiusbottom">Message Data</div>
    <div class="popup-box-content">
        <div class="space10"></div>
        <div class="grid-box table-box">
           <div class="grid-box-body">
               <div id="data"></div>
           </div>
       </div>
       <div class="space10"></div>
    </div>
    <div class="popup-box-footer radius6 noradiustop">
        <a href="#" cp-type="inline"  class="action-btn cancel-btn close-popup-btn"><span class="p"></span><span class="t">Close</span></a>
        <a href="{{ $link }}" target="_blank" class="action-btn download-btn"><span class="p"></span><span class="t">Download</span></a>

        <div class="clear"></div>
    </div>
    <a class="close_btn"></a>
</div>

<script type="text/javascript">
    var t_data = Jsonary.create({!! $data !!}).readOnlyCopy();
    var t_element = document.getElementById('data');
    Jsonary.render(t_element, t_data);
</script>