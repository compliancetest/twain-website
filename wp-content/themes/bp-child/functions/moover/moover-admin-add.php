<div class="wrap moover-form">
<div class="metabox-holder">
<h2>Add New mOover</h2>
<form method="post" action="admin.php?page=moover" id="moover-addnew-form">
  <div class="postbox">
    <h3 class="hndle"><span>Title</span></h3>
    <div class="inside">
      <table class="form-table">
        <tbody>
          <tr>
            <th scope="row"><label for="moover-title">Title <span class="description">(required) :</span></label></th>
            <td><input type="text" name="moover-title" id="moover-title" aria-required="true" value="My slider" style="width:300px" /></td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
  <div class="postbox">
    <h3 class="hndle"><span>Dimension</span></h3>
    <div class="inside">
      <table class="form-table">
        <tbody>
          <tr class="form-field">
            <th scope="row"><label>Dimension <span class="description">(required) :</span><br />
                <span class="cs-tip">If you want to make slider responsive, set width field in percents, e.g. <strong>100%</strong></span></label>
            <td><input type="text" name="moover-width" id="moover-d-width" aria-required="true" value="960" style="width:50px" />
              x
              <input type="text" name="moover-height" id="moover-d-height" aria-required="true" value="430" style="width:50px" />
              in px</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
  <h3 class="chop-slider-form-title">Slides</h3>
  <p style="margin-left:10px;">Use Drag &amp; Drop for re-ordering.</p>
  <ul id="moover-sortable" class="moover-slides">
    <li title="Add Slide" class="ui-state-default sort-disabled manage-add">Add</li>
  </ul>
  <div style="clear:both"></div>
  <div class="postbox">
    <h3 class="hndle"><span>Settings</span></h3>
    <div class="inside">
      <table class="form-table">
        <tbody>
          <tr>
            <th scope="row"><label>Move Background Image :<br />
                <span class="cs-tip">If 'move', than background image will be moving during the selected text effect</span></label></th>
            <td><select id="moveImage" name="moveImage">
                <option value="false" selected="selected">Do not move</option>
                <option value="true">Move</option>
              </select></td>
          </tr>
          <tr>
            <th scope="row"><label>Move Time :<br />
                <span class="cs-tip">Duration of background image movement. This value is also used by "Slide" text effect</span></label></th>
            <td><input type="text" name="moveTime" id="moveTime" value="3000" />
              ms</td>
          </tr>
          <tr>
            <th scope="row"><label>Move Width :<br />
                <span class="cs-tip">Background image will be moved during the text effect on this value/2. This value is also used by "Slide" text effect</span></label></th>
            <td><input type="text" name="moveWidth" id="moveWidth" value="40" />
              px</td>
          </tr>
          <tr>
            <th scope="row"><label>Slide In/Out Time :<br />
                <span class="cs-tip">Duration of slide-in/out animation between slides</span></label></th>
            <td><input id="slideTime" type="text" name="slideTime" value="400" />
              ms</td>
          </tr>
          
          <tr>
            <th scope="row"><label>Scale Background Image :<br />
                <span class="cs-tip">Scale multiplier for the background image. It will be scaled to selected value during the text effect</span></label></th>
            <td><select id="scaleImage" name="scaleImage">
                <option value="false" selected="selected">Do not scale</option>
                <option value="0.5">0.5</option>
                <option value="0.6">0.6</option>
                <option value="0.7">0.7</option>
                <option value="0.8">0.8</option>
                <option value="0.9">0.9</option>
                <option value="1.1">1.1</option>
                <option value="1.2">1.2</option>
                <option value="1.3">1.3</option>
                <option value="1.4">1.4</option>
                <option value="1.5">1.5</option>
              </select></td>
          </tr>
          <tr>
            <th scope="row"><label>Images Preloader :<br />
                <span class="cs-tip">If "Enabled", then slideshow will start only after all images will be loaded. Loading process will be indicated with preloader image</span></label></th>
            <td><select name="preloader">
                <option value="true" selected="selected">Enabled</option>
                <option value="false">Disabled</option>
              </select></td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
  <div class="postbox">
    <h3 class="hndle"><span>Pagination / Custom Controllers &nbsp;&nbsp;&nbsp; <a class="moover-expand-tg" href="#">Open</a></span></h3>
    <div class="inside">
      <div class="moover-tg-content">
      <table class="form-table">
        <tbody>
          <tr>
            <th scope="row"><label>Pagination :</label></th>
            <td><select name="navigation">
                <option value="false" selected="selected">Disabled</option>
                <option value="true">Enabled</option>
              </select></td>
          </tr>
          <tr>
            <th scope="row"><label>Active Pagination :</label>
              <br />
              <span class="cs-tip">Set to "Enabled" if you want to make pagination buttons 'clickable'. In this case clicking on some pagination button will cause transition to appropriate slide</span></th>
            <td><select name="navigationActive">
                <option value="false" selected="selected">Disabled</option>
                <option value="true">Enabled</option>
              </select></td>
          </tr>
          <tr>
              <th scope="row"><label>Custom "Stop" button :</label>
                <br />
                <span class="cs-tip">CSS selector of the element which will stop mOover.<br />For example: <strong>a.stop-moover</strong></span> </th>
              <td><input type="text" name="stopButton" value="a.slider_pause" style="width:300px"></td>
            </tr>
          <tr>
              <th scope="row"><label>Custom "Play" button :</label>
                <br />
                <span class="cs-tip">CSS selector of the element which will launch mOover if it was stopped.<br />For example: <strong>a.play-moover</strong></span> </th>
              <td><input type="text" name="playButton" value="a.slider_play" style="width:300px"></td>
            </tr>
            <tr>
              <th scope="row"><label>Custom "Next" button :</label>
                <br />
                <span class="cs-tip">CSS selector of the element which will cause transition to the next slide.<br />For example: <strong>a.next-slide</strong></span> </th>
              <td><input type="text" name="nextButton" value="" style="width:300px"></td>
            </tr>
            <tr>
              <th scope="row"><label>Custom "Previous" button :</label>
                <br />
                <span class="cs-tip">CSS selector of the element which will cause transition to the previous slide..<br />For example: <strong>a.prev-slide</strong></span> </th>
              <td><input type="text" name="prevButton" value="" style="width:300px"></td>
            </tr>
        </tbody>
      </table>
      </div>
    </div>
  </div>
  <div class="postbox">
    <h3 class="hndle"><span>Additional Parameters (Fro Pro Users Only) &nbsp;&nbsp;&nbsp; <a class="moover-expand-tg" href="#">Open</a></span></h3>
    <div class="inside">
      <div class="moover-tg-content">
        <table class="form-table">
          <tbody>
          	<tr>
              <th scope="row"><label>CSS3 Effects:</label>
                <br />
                <span class="cs-tip">By default mOover uses CSS3 transforms where possible (where possible). You can set to 'Disabled' to disable CSS3 transforms</span> </th>
              <td><select id="disableCSS3" name="disableCSS3"><option selected="selected" value="false">Enabled</option><option value="true">Disabled</option></select></td>
            </tr>
            <tr>
              <th scope="row"><label><strong>onStart</strong> Callback Function :</label>
                <br />
                <span class="cs-tip">Name of Javascript function that will be executed on mOover's initialization<br />
                <strong>For example : myOnStartFunction</strong></span> </th>
              <td><input type="text" name="onStart" value="" style="width:300px"></td>
            </tr>
            <tr>
              <th scope="row"><label><strong>onSlideSwitch</strong> Callback Function :</label>
                <br />
                <span class="cs-tip">Function will be executed in the beginning of transition to next slide<br />
                <strong>For example : myOnSlideSwitchFunction</strong></span> </th>
              <td><input type="text" name="onSlideSwitch" value="" style="width:300px"></td>
            </tr>
            <tr>
              <th scope="row"><label><strong>onSlideSwitchEnd</strong> Callback Function :</label>
                <br />
                <span class="cs-tip">Function will be executed right after transition to next slide <br />
                <strong>For example : myOnSlideSwitchFunction</strong></span> </th>
              <td><input type="text" name="onSlideSwitchEnd" value="" style="width:300px"></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  
  <h2>Save mOover</h2>
  <input type="submit" value="Save mOover" class="button-primary" name="create-moover">
  <a class="button-secondary" href="admin.php?page=moover">Cancel</a>
</form>

<!--New Slide Template-->
<ul class="moover-slide-template">
  <li class="ui-state-default">
    <div class="manage-controls">
      <div class="manage-edit" title="Edit Slide"></div>
      <div class="manage-copy" title="Copy Slide"></div>
      <div class="manage-remove" title="Remove Slide"></div>
    </div>
    <div class="moover-slide-settings">
    <div class="moover-html">
      <div class="moover-slide moover-absolute">
        <div class="moover-text"></div>
      </div>
    </div>
    <textarea name="moover-html[]"><div class="moover-slide moover-absolute"><div class="moover-text"></div></div>
</textarea>
    <div class="moover-js">{effect:'slide', moveWidth:100, moveTime:3000, textLineDelay:0, crossSideLines:true, lineFrom:'right', transformPreset:'none'}</div>
      <textarea name="moover-js[]">{'effect':'slide', moveWidth:100, moveTime:3000, textLineDelay:0, crossSideLines:true, lineFrom:'right', transformPreset:'none'}</textarea>
    </div>
  </li>
</ul>
</div>
</div>
<?php
include('moover-slide-editor.php');
?>
