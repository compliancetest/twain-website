<div class="popup-box" id="upload-message-box" style="display: none; width: 555px;">
    <form name="messageForm" id="saveTestingDetails" action="/testingdetails/" method="post">
        <div class="popup-box-header radius6 noradiusbottom">Select Testing Details</div>
        <div class="popup-box-content grid-box-body">
            <div class="info-section">
                <div class="field-row">
                    <div class="grid-cell width250">
                        <label for="tm-test-suite">Test Suite</label>
                        <select name="test_suite_id" id="um-test-suite" class="select">
                            @foreach($suites as $s)
                                <option value="{{ $s->suite_id }}" {!! is_selected($s->suite_id, $currentTestingDetails->test_suite_id) !!}>{{ $s->suite_title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid-cell width250 left15">
                        <label for="um-test-suite">Test Case</label>
                        <select name="test_case_id" id="um-test-case" class="select">
                            @foreach ($cases as $c)
                                <option value="{{ $c->ID }}" {!! is_selected($c->ID, $currentTestingDetails->test_case_id) !!}>{{ $c->post_title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="clear"></div>
                </div>
                <div class="field-row">
                    <div class="grid-cell width100P">
                        <label for="um-product">Product</label>
                        <select name="product_id" id="um-product" class="select">
                            <option value="">Select a Product</option>
                            @foreach ($products as $p)
                                <?php
                                    $version = get_post_meta($p->ID, 'product_version', true);
                                    if (!empty($version)) $version = ' v' . $version;
                                ?>
                                <option value="{{ $p->ID }}" {!! is_selected($p->ID, $currentTestingDetails->product_id) !!}>{!! get_post_meta($p->ID, 'product_name', true) . $version !!}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="clear"></div>
                </div>
            </div>
        </div>
        <div class="popup-box-footer radius6 noradiustop">

            @if(!$currentTestingDetails)
                <a href="#" class="action-btn process-btn submit-btn has-tooltip" id="saveTestingDetailsButton"><span
                        class="p"></span><span class="t">Start</span><span
                        class="simple_tooltip">Start<span></span></span></a>
            @endif

            @if($currentTestingDetails)
                <a href="#" class="action-btn pause-btn submit-btn has-tooltip" id="saveTestingDetailsButton"><span
                            class="p"></span><span class="t">Finish</span><span
                            class="simple_tooltip">Finish<span></span></span></a>
            @endif

            <a href="#" class="action-btn cancel-btn close-popup-btn"><span class="p"></span><span
                        class="t">Cancel</span></a>



            <div class="clear"></div>
        </div>
        <a class="close_btn"></a>

        <div class="loading loading-with-text radius6">
            <div><b>PROCESSING YOUR PAYMENT</b><span>Please wait...</span></div>
        </div>
        {{ csrf_field() }}
        <input type="hidden" name="is_running" value="{{ $currentTestingDetails ? 0 : 1 }}">
    </form>
</div>
