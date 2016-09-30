<?php
if(\Illuminate\Support\Facades\Auth::check()){
    $message = MESSAGE_WARNING_REGISTERED;
} else {
    $message = MESSAGE_WARNING_ANONYMOUS;
}
?>
<div class="tab-content">
    <div role="tabpanel" class="tab-pane active" id="testsuites">{{ $message }}</div>
    <div role="tabpanel" class="tab-pane" id="testdata">{{ $message }}</div>
    <div role="tabpanel" class="tab-pane" id="wiki">{{ $message }}</div>
    <div role="tabpanel" class="tab-pane" id="forum">{{ $message }}</div>
    <div role="tabpanel" class="tab-pane" id="downloads">{{ $message }}</div>
    <div role="tabpanel" class="tab-pane" id="survey">{{ $message }}</div>
    <div role="tabpanel" class="tab-pane" id="reports">{{ $message }}</div>
</div>