<?php

sleep(2);

?>
<div id="json-view-panel1460802685" class="json-view-panel">
    {"Profile":{"Type":"TCEF","Purpose":"TCEF for Application test case","Title":"CAP-01a_v1.0 TEFC","Description":"Test Case Execution Flow for CAP-01a test case","Version":{"Major":1,"Minor":0}},"Meta":{"SystemUnderTest":"Application","Capabilities":[{"Cap":"CAP_SUPPORTEDCAPS"}],"InitialState":4},"TestSteps":[[{"Optional":false,"Triplet":{"From":"APP","To":"DS","DataGroup":"DG_CONTROL","DataArgumentType":"DAT_USERINTERFACE","Messages":"MSG_ENABLEDS","pUserinterface":{"ShowUI":true}},"PassConditions":[{"ItemType":"ReturnCode","Operator":"EQ","Value":"TWRC_SUCCESS","Step":1}]}]]}
</div>

<script type="text/javascript">
    var t_data = Jsonary.create({"Profile":{"Type":"TCEF","Purpose":"TCEF for Application test case","Title":"CAP-01a_v1.0 TEFC","Description":"Test Case Execution Flow for CAP-01a test case","Version":{"Major":1,"Minor":0}},"Meta":{"SystemUnderTest":"Application","Capabilities":[{"Cap":"CAP_SUPPORTEDCAPS"}],"InitialState":4},"TestSteps":[[{"Optional":false,"Triplet":{"From":"APP","To":"DS","DataGroup":"DG_CONTROL","DataArgumentType":"DAT_USERINTERFACE","Messages":"MSG_ENABLEDS","pUserinterface":{"ShowUI":true}},"PassConditions":[{"ItemType":"ReturnCode","Operator":"EQ","Value":"TWRC_SUCCESS","Step":1}]}]]}).readOnlyCopy();
    var t_element = document.getElementById('json-view-panel1460802685');
    Jsonary.render(t_element, t_data);
</script>