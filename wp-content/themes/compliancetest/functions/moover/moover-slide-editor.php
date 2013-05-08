<!-- Editor -->

<div class="moover-slide-editor">
  <div class="moover-layer moover-hidden-layer"></div>
  <div class="moover-toolbar">
    <ul>
      <li style="font-size:13px; line-height:50px">mOover Slide-Editor</li>
      <li class="tb-button mv-tb-bg"><a href="#"><span></span>
        <div>Background</div>
        </a>
        <ul class="mv-sub">
          <li class="mv-removeBG">[x] Remove Image</li>
        </ul>
      </li>
      <li class="tb-button mv-tb-text"><span></span>
        <div>Add Text</div>
      </li>
      <li class="tb-button mv-tb-image"><span></span>
        <div>Add Image</div>
      </li>
      <li class="tb-button mv-tb-fx"><span></span>
        <div>Choose Effect</div>
        <div class="mv-effects">
          <div class="mv-effects-left"> <a href="#" class="active-fx" data-fx="slide">Slide Effect<span>Text lines slide over the slide</span></a> <a href="#" data-fx="pusher">Pusher<span>Text lines fall down</span></a> <a href="#" data-fx="typewriter">Typewriter<span>Text printed char by char</span></a> </div>
          <div class="mv-effects-right">
            <div class="active-fx" data-fx='slide'>
              <p><strong>Slide Effect</strong></p>
              <p>Move Width:
                <input id="slide-moveWidth" value="100" size="4" type="text" />
                px </p>
              <p>Move Time:
                <input id="slide-moveTime" value="3000" size="4" type="text" />
                ms </p>
              <p>Delay between lines:
                <input id="textLineDelay" value="0" size="2" type="text" />
                ms</p>
              <p>Cross-side lines :
                <input id="crossSideLines" type="checkbox" checked="checked" />
              </p>
              <p>First line from:
                <select id="lineFrom">
                  <option selected="selected" value="right">Right</option>
                  <option value="left">Left</option>
                </select>
              </p>
              <p>Transform effect:
                <select id="transformPresetSlide">
                  <option selected value="none">None</option>
                  <option value="skew">Skew</option>
                  <option value="flippers-h">Flip Horizontal</option>
                  <option value="flippers-v">Flip Vertical</option>
                  <option value="scale-full">Scale</option>
                  <option value="scale-x">Scale X</option>
                  <option value="scale-y">Scale Y</option>
                  <option value="rotation">Rotation</option>
                  <option value="hurricane">Hurricane</option>
                </select>
              </p>
              <p>
                <button class="button-secondary">Save Effect's Settings</button>
                <span class="mv-saved">...Saved</span> </p>
            </div>
            <div data-fx='pusher'>
              <p><strong>Pusher Effect</strong></p>
              <p>Delay between lines:
                <input id="pushDelay" value="300" size="2" type="text" />
                ms</p>
              <p>Duration of falling:
                <input id="pushTime" value="100" size="2" type="text" />
                ms</p>
              <p>Hold slide after falling:
                <input id="afterPushHoldTime" value="3000" size="4" type="text" />
                ms</p>
              <p>
                <button class="button-secondary">Save Effect's Settings</button>
                <span class="mv-saved">...Saved</span> </p>
            </div>
            <div data-fx='typewriter'>
              <p><strong>Typewriter Effect</strong></p>
              <p>Transform effect:
                <select id="transformPresetTW">
                  <option value="default" selected>Default</option>
                  <option value="flippers-h">Flip Horizontal</option>
                  <option value="flippers-v">Flip Vertical</option>
                  <option value="rotators">Rotators</option>
                  <option value="fall">Fall</option>
                  <option value="fall-elastic">Fall elastic</option>
                  <option value="fall-reverse">Fall with reverse</option>
                  <option value="rain-elastic">Elastic Rain</option>
                  <option value="hurricane">Hurricane</option>
                </select>
              </p>
              <p>Timing preset:
                <select id="timingPreset">
                  <option value="very-fast">Very Fast</option>
                  <option selected value="fast">Fast</option>
                  <option value="medium">Medium</option>
                  <option value="slow">Slow</option>
                  <option value="slow-short">Slow/Short Delay</option>
                  <option value="slow-long">Slow/Long Delay</option>
                  <option value="very-slow">Very Slow</option>
                </select>
              </p>
              <p>Hold slide after printing:
                <input id="textHoldTime" value="2000" size="4" type="text" />
                ms</p>
              <p>
                <button class="button-secondary">Save Effect's Settings</button>
                <span class="mv-saved">...Saved</span> </p>
            </div>
          </div>
          <div style="clear:both"></div>
        </div>
      </li>
      <li class="tb-button mv-tb-inverse"><span></span>
        <div>Inverse View</div>
      </li>
      <li class="tb-button mv-tb-play"><span></span>
        <div>Preview Slide</div>
      </li>
      <li class="tb-button mv-tb-save"><span></span>
        <div>Save and Close</div>
      </li>
      <li class="tb-button mv-tb-discard"><span></span>
        <div>Discard</div>
      </li>
    </ul>
  </div>
  <div class="moover-slider moover">
    <div class="moover-slide">
      <div class="moover-text"> </div>
    </div>
  </div>
  <div class="mv-tb-controls">
    <div class="mv-tb-edit" title="Edit"></div>
    <div class="mv-tb-copy" title="Copt"></div>
    <div class="mv-tb-remove" title="Remove"></div>
  </div>
  <div class="mv-tb-editor">
    <p>
      <textarea name="mv-html"></textarea>
    </p>
    <p> <span>font-size</span>
      <input name="mv-fs" value="15px" type="text" />
    </p>
    <p> <span>color</span>
      <input name="mv-color" value="#333333" type="text" />
    </p>
    <p> <span>BG-color</span>
      <input name="mv-bg" value="" size="7" type="text" />
    </p>
    <p> <span>font-weight</span>
      <input name="mv-fw" value="normal" type="text" />
    </p>
    <p> <span>padding</span>
      <input name="mv-p" value="0px" type="text" />
    </p>
    <p style="overflow:visible"><a class="button-primary">Apply</a> <a class="button-secondary">Cancel</a></p>
  </div>
</div>
