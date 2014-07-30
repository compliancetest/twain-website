<?php
/**
* Process Actions   
*/
add_action("init", "ct_process_message_actions");
function ct_process_message_actions()
{
    if(!is_user_logged_in())
        return;
        
    $action = isset($_REQUEST['ct-message-action']) ? $_REQUEST['ct-message-action'] : null;
    if(wp_verify_nonce($action, 'trigger-message'))
    {
        showTriggerMessageBox();
    }else if(wp_verify_nonce($action, 'save-message')){
        saveMessageTemplate();
    }else if(wp_verify_nonce($action, 'remove-message')){
        removeMessageTemplate();
    }else if(wp_verify_nonce($action, 'send-message')){
        sendMessage();
    }else if($action == 'get-test-cases'){
        getTestCases();    
    }else if($action == 'get-case-templates-and-profiles'){
        getCaseTemplatesAndProfiles();    
    }else if($action == 'load-message-template'){
        loadMessageTemplate();    
    }
    else if(wp_verify_nonce($action, 'upload-message'))    
    {
        showUploadMessageBox();
    }
    else if(wp_verify_nonce($action, 'send-uploaded-message'))
    {
        uploadMessage();
    }
}

function sendMessage()
{
    global $wpdb, $CPRest;
    
    $user_id = get_current_user_id();
    
    $suite_id = $_POST['test-suite'];
    $product_id = $_POST['product'];
    $case_id = $_POST['test-case'];
    $template = $_POST['template'];
    
    $harness_profile = $_POST['harness_profile'];
    $tester_profile = $_POST['tester_profile'];
    
    header('content-type: application/xml');
    echo '<result>';
    
    if(!$suite_id || !$case_id || !$template || !$harness_profile || !$tester_profile)
    {
        echo '<status>error</status>';
        echo '<error>Invalid Request!</error>';
    }else{
        //Getting Subscription Ino
        $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "users_subscriptions WHERE user_id=%d AND suite_id=%d AND `status`='Active'", $user_id, $suite_id);
        $subscription = $wpdb->get_row($query);
        
        $cases = get_post_meta($case_id, 'test_suite');
        
        if(!$subscription || !in_array($suite_id, $cases)) //Test Suite And Case Validation
        {
            echo '<status>error</status>';
            echo '<error>Invalid Request!</error>';    
        }else{
            $suiteObj = new TestSuite($suite_id);            
            $caseObj = new TestCase($case_id);
            $caseObj->load();
//            <api:testSuiteId>' . $suite_id . '</api:testSuiteId>
            //Create XML
            $xmlData = '<api:invokeMessageRequest xmlns:api="http://compliancetest.net/api">
                            <api:testCase>                                
                                <api:testCaseId>' . $caseObj->testCaseID . "_V" . $caseObj->version . '</api:testCaseId>                                
                                <api:testSuiteId>' . $suiteObj->getSuiteID() . '</api:testSuiteId>';
            if($product_id)
            {
                $xmlData .= '<api:productName>' . get_post_meta($product_id, 'product_name', true) . '</api:productName>
                                <api:productId>' . get_post_meta($product_id, 'product_id', true) . '</api:productId>';
            }
                                
            $xmlData .= '<api:messageTemplate  templateURI="' . $template . '">
                                    <api:profile namespace="Tester">' ; 
            //Getting Tester Profiles
            $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "community_profile_instances WHERE id=%d", $tester_profile);
            $testerProfileInstance = $wpdb->get_row($query);            
            $xmlData .= '<api:profileURL>' . get_site_url(null, '', 'https') . "/get-profile?id=" . $testerProfileInstance->token . '</api:profileURL>';
            
            $xmlData .= '</api:profile>
                            <api:profile namespace="Harness">';
                            
            $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "community_profile_instances WHERE id = %d", $harness_profile);
            $harnessProfileInstance = $wpdb->get_row($query);            
            $xmlData .= '<api:profileURL>' . get_site_url(null, '', 'https') . "/get-profile?id=" . $harnessProfileInstance->token . '</api:profileURL>';
            
            $xmlData .= '</api:profile> 
                    </api:messageTemplate>
                    <api:identity>
                        <api:username>' . $subscription->harness_username . '</api:username>
                        <api:password>' . $subscription->harness_password . '</api:password>
                    </api:identity>
                </api:testCase>
            </api:invokeMessageRequest>';
            
            
            $result = $CPRest->doMessageAPI('message/invoke', $xmlData);
            
            $resultDoc = new DOMDocument();
            
            if(!$result || !$resultDoc->loadXML($result))
            {
                echo '<status>error</status>';
                echo '<error><![CDATA[There was an error while sending this message' . (!$result ? '' : ": $result") . ']]></error>';
            }else{                
                if($resultDoc->getElementsByTagName('code')->length > 0 && $resultDoc->getElementsByTagName('code')->item(0)->nodeValue == 'ACCEPTED'){
                    echo '<status>success</status>';
                    //Save Previous Data To Tmp Table
                    $wpdb->insert($wpdb->prefix . "message_tmp", 
                        array(
                            'user_id' => $user_id,
                            'suite_id' => $suite_id,
                            'case_id' => $case_id,
                            'product_id' => $product_id, 
                            'template' => $case_template,
                            'harness_profile_id' => serialize(!$harness_profiles ? array() : $harness_profiles),
                            'tester_profile_id' => serialize(!$tester_profiles ? array() : $tester_profiles),
                            'created_date' => date('Y-m-d H:i:s')
                        )
                    );
                }else if($resultDoc->getElementsByTagName('faultstring')->length > 0){
                    echo '<status>error</status>';
                    echo '<error><![CDATA[' . $resultDoc->getElementsByTagName('faultstring')->item(0)->nodeValue . ']]></error>';
                }else if($resultDoc->getElementsByTagName('error')->length > 0){
                    echo '<status>error</status>';
                    echo '<error><![CDATA[' . $resultDoc->getElementsByTagName('error')->item(0)->nodeValue . ']]</error>';
                }else{
                    echo '<status>error</status>';
                    echo '<error>There was an error while sending this message.</error>';    
                }
                
            }
            
        }
        
    }
    
    echo '</result>';
    exit;
}

function loadMessageTemplate()
{
    global $wpdb;
    
    $user_id = get_current_user_id();
    
    $template_id = $_POST['template_id'];
    $current_case_id = $_POST['current_case_id'];
    $current_suite_id = $_POST['current_suite_id'];
    
    $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "message_templates WHERE user_id=%d AND id=%d", $user_id, $template_id);
    $row = $wpdb->get_row($query);
    
    header('content-type: application/xml');
    echo '<result>';
    if(!$row)
    {
        echo '<status>error</status>';
        echo '<error>Invalid Request!</error>';
    }else{
        
        echo '<status>success</status>';
        echo '<suiteid>' . $row->suite_id . '</suiteid>';
        echo '<productid>' . $row->product_id . '</productid>';
        echo '<templateid>' . $row->template . '</templateid>';
        echo '<caseid>' . $row->case_id . '</caseid>';
        
        $suiteObj = new TestSuite($row->suite_id);            
        
        if($current_suite_id != $row->suite_id)
        {
            //Getting Cases
            echo '<cases>';            
            $cases = $suiteObj->loadHarnessInitiatedTestCases();
            foreach($cases as $case)
            {
                echo '<case id="' . $case->ID . '">' . get_post_meta($case->ID, 'test_case_id', true) . '</case>'; 
            }
            echo '</cases>';                   
            
        }
        
        if($current_case_id != $row->case_id)
        {
            //Getting Case Message Templates
            $caseObj = new TestCase($row->case_id);
            
            $caseTemplates = $caseObj->loadMessageTemplates();
            echo '<templates>';
            foreach($caseTemplates as $t)
            {
                echo '<template>';
                echo '<name><![CDATA[' . $t['name'] . ']]></name>';
                echo '<uri><![CDATA[' . $t['url'] . ']]></uri>';
                echo '</template>';
            }
            echo '</templates>';
            
            //Getting Profile Instances
            echo '<harness><![CDATA[';
            echo _getHarnessProfilesHTML($row->case_id);
            echo ']]></harness>';
            
            echo '<tester><![CDATA[';
            echo _getTesterProfilesHTML($row->case_id);
            echo ']]></tester>';
        }        
        foreach(unserialize($row->harness_profile_id) as $i)
        {
            echo '<harness_id>' . $i . '</harness_id>';   
        }
        foreach(unserialize($row->tester_profile_id) as $i)
        {
            echo '<tester_id>' . $i . '</tester_id>';   
        }
        
    }
    echo '</result>';
    exit;
}

function saveMessageTemplate()
{
    global $wpdb, $CPRest;
    
    $user_id = get_current_user_id();
    
    $suite_id = $_POST['suite_id'];
    $case_id = $_POST['case_id'];
    $product_id = $_POST['product_id'];
    $case_template = $_POST['case_template'];
    $name = $_POST['name'];
    
    
    $r = $wpdb->insert($wpdb->prefix . "message_templates", 
        array(
            'user_id' => $user_id,
            'name' => $name,
            'suite_id' => $suite_id,
            'case_id' => $case_id,
            'product_id' => $product_id, 
            'template' => $case_template,
            'harness_profile_id' => serialize(!$_POST['harness_profiles'] ? array() : $_POST['harness_profiles']),
            'tester_profile_id' => serialize(!$_POST['tester_profiles'] ? array() : $_POST['harness_profiles']),
            'created_date' => date('Y-m-d H:i:s')
        )
    );
    header('content-type: application/xml');
    echo '<result>';
    if(!$r)
    {
        echo '<status>error</status>';
        echo '<error><![CDATA[' . $wpdb->last_error . ']]></error>';
    }else{
        echo '<status>success</status>';
        echo '<newid>' . $wpdb->insert_id . '</newid>';
    }
    echo '</result>';
    exit;
}

function removeMessageTemplate()
{
    global $wpdb, $CPRest;
    
    $user_id = get_current_user_id();
    
    $template_id = $_POST['template_id'];
    
    $r = $wpdb->query($wpdb->prepare("DELETE FROM " . $wpdb->prefix . "message_templates WHERE user_id=%d AND id=%d", $user_id, $template_id));
    
    
    header('content-type: application/xml');
    echo '<result>';
    if(!$r)
    {
        echo '<status>error</status>';
        echo '<error><![CDATA[' . $wpdb->last_error . ']]></error>';
    }else{
        echo '<status>success</status>';
    }
    echo '</result>';
    exit;
}

function getCaseTemplatesAndProfiles()
{
    global $wpdb;
    
    $user_id = get_current_user_id();
    $suite_id = $_POST['suite_id'];
    $case_id = $_POST['case_id'];
    
    $query = $wpdb->prepare("SELECT suite_id FROM " . $wpdb->prefix . "users_subscriptions WHERE user_id=%d AND suite_id=%d AND `status`='Active'", $user_id, $suite_id);
    $suite_id = $wpdb->get_var($query);
    
    header('Content-type: application/xml');
    echo '<results>';
    if($suite_id)
    {
        $cases = get_post_meta($case_id, 'test_suite');
        if(is_array($cases) && in_array($suite_id, $cases))
        {
            //Getting Case Message Templates
            $caseObj = new TestCase($case_id);
            
            $caseTemplates = $caseObj->loadMessageTemplates();
            echo '<templates>';
            foreach($caseTemplates as $t)
            {
                echo '<template>';
                echo '<name><![CDATA[' . $t['name'] . ']]></name>';
                echo '<uri><![CDATA[' . $t['url'] . ']]></uri>';
                echo '</template>';
            }
            echo '</templates>';
            
            //Getting Harness Profile Instances
            echo '<harness><![CDATA[';
            echo _getHarnessProfilesHTML($case_id);
            echo ']]></harness>';
            
            echo '<tester><![CDATA[';
            echo _getTesterProfilesHTML($case_id);
            echo ']]></tester>';
        }
    }
    
    echo '</results>';
    
    exit;
}

function getTestCases()
{
    global $wpdb;
    
    $user_id = get_current_user_id();
    $suite_id = $_POST['suite_id'];
    
    $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "users_subscriptions WHERE user_id=%d AND suite_id=%d AND `status`='Active'", $user_id, $suite_id);
    $subscription = $wpdb->get_row($query);
    header('Content-type: application/xml');
    echo '<results>';
    if($subscription)
    {
        echo '<cases>';
        $suiteObj = new TestSuite($subscription->suite_id);
        if(isset($_POST['case-type']) && $_POST['case-type'] == 'tester')
            $cases = $suiteObj->loadTesterInitiatedTestCases();            
        else
            $cases = $suiteObj->loadHarnessInitiatedTestCases();
        foreach($cases as $case)
        {
            echo '<case id="' . $case->ID . '">' . get_post_meta($case->ID, 'test_case_id', true) . '</case>'; 
        }
        echo '</cases>';
        
        if(!isset($_POST['only-cases']))
        {        
            if($cases)
            {
                //Getting Case Message Templates
                $caseObj = new TestCase($cases[0]->ID);
                
                $caseTemplates = $caseObj->loadMessageTemplates();
                echo '<templates>';
                foreach($caseTemplates as $t)
                {
                    echo '<template>';
                    echo '<name><![CDATA[' . $t['name'] . ']]></name>';
                    echo '<uri><![CDATA[' . $t['url'] . ']]></uri>';
                    echo '</template>';
                }
                echo '</templates>';
            }
            
            echo '<harness><![CDATA[';
            echo _getHarnessProfilesHTML($cases[0]->ID);
            echo ']]></harness>';
            
            echo '<tester><![CDATA[';
            echo _getTesterProfilesHTML($cases[0]->ID);
            echo ']]></tester>';
        }
    }
    
    echo '</results>';
    
    exit;
}

function _getHarnessProfilesHTML($case_id, $defaults = array())
{
    $html = '';
    
    /*$caseObj = new TestCase($case_id);        
    $caseObj->loadProfileInstances();
    $harnessProfiles = $caseObj->getProfileInstanceRows();
    */
    $user_id = get_current_user_id();
    
    $profileInstances = _getTesterAndHarnessProfileInstances($case_id, $user_id);
    
    $html .= '<h5>Harness Profiles</h5>';
    $html .= '<div class="field-row">';
    $html .= '<div class="grid-cell width50P"><b>Name</b></div>';
    $html .= '<div class="grid-cell width20P"><b>Purpose</b></div>';
    $html .= '<div class="grid-cell width30P"><b>Type</b></div>';
    $html .= '<div class="clear"></div>';
    $html .= '</div>';
    $case = new TestCase( $case_id );
    $case->load();
    $suiteObj = new TestSuite();
    $testSuitesRolesProfilesTypes = $suiteObj->loadProfileTypesToRoles( $case->testSuite );
    $testSuitesRoles = array( $case->harnessRole );
    foreach($profileInstances as $instance){
        $profileTypeName = $instance->profile_type_title;
        if( ! $instance->lookup && ! cp_checked($instance->id, $case->profileInstances) ){
            continue;
        }
        if( ! empty( $testSuitesRolesProfilesTypes ) ){
            $isAllowed = false;
            foreach( $testSuitesRolesProfilesTypes AS $cRoleName => $cProfilesTypes ){
                if( in_array( $cRoleName, $testSuitesRoles ) && in_array( $profileTypeName, $cProfilesTypes ) ){
                    $isAllowed = true;
                }
            }
            if( ! $isAllowed ) {
                continue;
            }
        }
        $html .= _getProfileRow($instance, 'harness_profile', $defaults);
    }
        
    return $html;
}

function _getTesterProfilesHTML($case_id, $defaults = array())
{
    $html = '';
    
    /*$caseObj = new TestCase($case_id);        
    $caseObj->loadProfileInstances();
    $harnessProfiles = $caseObj->getProfileInstanceRows();

    $customerProfileInstances = getCustomerProfileInstances();
            
    $testerProfiles = array_merge($customerProfileInstances, $harnessProfiles);*/
    
    $user_id = get_current_user_id();
    
    $profileInstances = _getTesterAndHarnessProfileInstances($case_id, $user_id);
    
    $html .= '<h5>Tester Profiles</h5>';
    $html .= '<div class="field-row">';
    $html .= '<div class="grid-cell width50P"><b>Name</b></div>';
    $html .= '<div class="grid-cell width20P"><b>Purpose</b></div>';
    $html .= '<div class="grid-cell width30P"><b>Type</b></div>';
    $html .= '<div class="clear"></div>';
    $html .= '</div>';
    $case = new TestCase( $case_id );
    $case->load();
    $suiteObj = new TestSuite();
    $testSuitesRolesProfilesTypes = $suiteObj->loadProfileTypesToRoles( $case->testSuite );
    $testSuitesRoles = array( $case->testerRole );
    foreach($profileInstances as $instance){
        $profileTypeName = $instance->profile_type_title;
        if( ! $instance->lookup && ! cp_checked($instance->id, $case->profileInstances) ){
            continue;
        }
        if( ! empty( $testSuitesRolesProfilesTypes ) ){
            $isAllowed = false;
            foreach( $testSuitesRolesProfilesTypes AS $cRoleName => $cProfilesTypes ){
                if( in_array( $cRoleName, $testSuitesRoles ) && in_array( $profileTypeName, $cProfilesTypes ) ){
                    $isAllowed = true;
                }
            }
            if( ! $isAllowed ) {
                continue;
            }
        }
        $html .= _getProfileRow($instance, 'tester_profile', $defaults);
    }
    
    return $html;
}

function _getProfileRow($instance, $name, $defaults)
{
    $instanceObj = json_decode(base64_decode($instance->content));
    $schemaObj = json_decode(base64_decode($instance->schema));
    
    $version[] = $instanceObj->Profile->Version->Major;
    $version[] = $instanceObj->Profile->Version->Minor;
    if($instanceObj->Profile->Version->Patch)
        $version[] = $instanceObj->Profile->Version->Patch;
    
    $sVersion[] = $schemaObj->Version->Major;
    $sVersion[] = $schemaObj->Version->Minor;
    if($schemaObj->Version->Patch)
        $sVersion[] = $schemaObj->Version->Patch;

    $html .= '<div class="field-row">';
    $html .= '<div class="grid-cell width50P"><input type="radio" name="' . $name . '" id="' . $name . $instance->id . '" value="' . $instance->id . '"' . cp_checked($instance->id, $defaults) . ' class="right10" /> <a href="' .  get_site_url() . '?td-action=' . wp_create_nonce('view-profile-instance') . '&id=' . $instance->id . '&back=1" rel="custom-popup" cp-type="ajax">' . $instance->profile_name . ' v' . implode('.', $version) . '</a></div>';
    $html .= '<div class="grid-cell width20P">' . $instanceObj->Profile->Purpose . '</div>';
    $html .= '<div class="grid-cell width30P"><a href="' . get_site_url() . '?td-action=' . wp_create_nonce('view-profile-type') . '&id=' . $instance->type_id . '&back=1" rel="custom-popup" cp-type="ajax" class="view-profile-type-link">' . $instance->profile_type_title . ' v' . implode(".", $sVersion) . '</a>  </div>';
    $html .= '<div class="clear"></div>';
    $html .= '</div>';
    
    return $html;
}

function _getTesterAndHarnessProfileInstances($case_id, $user_id)
{
    global $wpdb;
    
    $caseObj = new TestCase($case_id);        
    $caseObj->loadProfileInstances();
    
    $ids = $wpdb->escape($caseObj->profileInstances);        
    
    $query = $wpdb->prepare("SELECT pi.*, pt.title AS profile_type_title, pt.schema FROM " . $wpdb->prefix . "community_profile_instances AS pi LEFT JOIN " . $wpdb->prefix . "community_profile_types AS pt ON pt.id=pi.type_id WHERE (pi.id IN (" . implode(", ", $ids) . ")) OR (pi.creator_id=%d AND pi.type='tester') ORDER BY pi.purpose, pi.profile_name", $user_id);   
    
    $profiles = $wpdb->get_results($query);
    
    return $profiles;
}

function showTriggerMessageBox()
{
    global $CPRest;
    
    $user_id = get_current_user_id();
    
    //Getting Subscribed Test Suites
    $suites = getUserSubscriptions();
    
    
    if(!$suites)    
    {
    ?>
        <div class="popup-box" id="trigger-message-box" style="display: none; width: 555px;">    
            <div class="popup-box-header radius6 noradiusbottom">Trigger Message</div>        
                <div class="popup-box-content grid-box-body">
                    <p class="message warning">You need to purchase subscription for at least one test suite to trigger message.</p>                
                </div>
                <div class="popup-box-footer radius6 noradiustop">                
                    <a href="#" class="action-btn cancel-btn close-popup-btn"><span class="p"></span><span class="t">Close</span></a>            
                    <div class="clear"></div>
                </div>
            <a class="close_btn"></a>                        
        </div>    
    <?php    
    }else{
        //Get suites that have harness details
        $rsuites = array();
        foreach($suites as $s)
        {
            if(!$s->harness_endpoint_url || !$s->harness_password || !$s->harness_username)
                continue;
            $rsuites[] = $s;
        }
        
        $suites = $rsuites;
        if(!$suites) //User has not yet defined his profile.
        {
        ?>
            <div class="popup-box" id="trigger-message-box" style="display: none; width: 555px;">    
                <div class="popup-box-header radius6 noradiusbottom">Trigger Message</div>        
                    <div class="popup-box-content grid-box-body">
                        <p class="message notice">You have not defined your profile, yet. Please go to <a href="<?php echo get_site_url()?>/my-profile">My Profile &gt; My Test Suite Subscriptions &gt; Harness Details</a> to define your proifle.</p>                
                    </div>
                    <div class="popup-box-footer radius6 noradiustop">                
                        <a href="#" class="action-btn cancel-btn close-popup-btn"><span class="p"></span><span class="t">Close</span></a>            
                        <div class="clear"></div>
                    </div>
                <a class="close_btn"></a>                        
            </div>    
        <?php    
        }else{
//            $lastData = getUserLastDataForMessage($user_id);
            $lastData = null;
            $products = getUserProductsAndServices($user_id);
            
            $current_suite_id = !$lastData ? $suites[0]->suite_id : $lastData->suite_id;
            $is_valid_suite = false;
            foreach($suites as $s)
            {
                if($s->suite_id == $current_suite_id)
                {
                    $is_valid_suite = true;
                    break;        
                }
            }
            if(!$is_valid_suite)
                $current_suite_id = $suites[0]->suite_id;
                
                
            $current_product_id = !$lastData ? $products[0]->ID : $lastData->product_id;
            
            $current_harness_profile_id = !$lastData ? array() : unserialize($lastData->harness_profile_id);
            $current_tester_profile_id = !$lastData ? array() : unserialize($lastData->tester_profile_id);
            
            //Getting Test Cases
            $suiteObj = new TestSuite($current_suite_id);
            $cases = $suiteObj->loadHarnessInitiatedTestCases();
            
            $current_case_id = !$lastData ? $cases[0]->ID : $lastData->case_id;
            $is_valid_case = false;
            foreach($cases as $c)
            {
                if($c->ID == $current_case_id)
                {
                    $is_valid_case = true;
                    break;
                }
            }
            if(!$is_valid_case)
                $current_case_id = $cases[0]->ID;
            
            //Getting User Previous Message Templates
            $prevMessages = getUserPreviousMessageTemplates($user_id);            
            
            //Getting Harness Profiles
            $caseObj = new TestCase($current_case_id);
            /*$caseObj->loadProfileInstances();
            $harnessProfiles = $caseObj->getProfileInstanceRows();*/
            
            $caseTemplates = $caseObj->loadMessageTemplates();
            
            $current_template = !$lastData ? $caseTemplates[0] : $lastData->template;
            
            /*$customerProfileInstances = getCustomerProfileInstances();
            
            $testerProfiles = array_merge($customerProfileInstances, $harnessProfiles);*/
            
            $profileInstances = _getTesterAndHarnessProfileInstances($current_case_id, $user_id);
            
        ?>
        <div class="popup-box" id="trigger-message-box" style="display: none; width: 555px;">
            <form name="messageForm" id="messageForm" action="">
                <div class="popup-box-header radius6 noradiusbottom">Trigger Message</div>        
                    <div class="popup-box-content grid-box-body">                          
                        <p>Send a message from our test harness to your system. Choose a test case, then select the template and, optionally change the default values for template variables</p>
                        <div class="info-section">
                            <h5>Choose a New Message Template</h5>
                            <div class="field-row">
                                <div class="grid-cell width250">
                                    <label for="tm-test-suite">Test Suite</label>
                                    <select name="test-suite" id="tm-test-suite" class="select">
                                        <?php foreach($suites as $s){ ?>
                                        <option value="<?php echo $s->suite_id?>" <?php echo $s->suite_id == $current_suite_id ? 'selected="selected"' : '' ?>><?php echo $s->suite_title ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="grid-cell width250 left15">
                                    <label for="tm-product">Product</label>
                                    <select name="product" id="tm-product" class="select">
                                        <option value="">Select a Product</option>
                                        <?php foreach($products as $p){ ?>
                                        <option value="<?php echo $p->ID?>" <?php echo 0 && $p->ID == $current_product_id ? 'selected="selected"' : '' ?>><?php echo get_post_meta($p->ID, 'product_name', true) ?></option>
                                        <?php } ?>
                                    </select>
                                </div>                    
                                <div class="clear"></div>
                            </div>     
                            <div class="field-row">
                                <div class="grid-cell width250">
                                    <label for="tm-test-suite">Test Case</label>
                                    <select name="test-case" id="tm-test-case" class="select">
                                        <?php foreach($cases as $c){ ?>
                                        <option value="<?php echo $c->ID?>" <?php echo $c->ID == $current_case_id ? 'selected="selected"' : '' ?>><?php echo $c->post_title ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="grid-cell width250 left15">
                                    <label for="tm-template">Message Template</label>
                                    <select name="template" id="tm-template" class="select">
                                        <option value="">- Select -</option>
                                        <?php foreach($caseTemplates as $t){ ?>
                                        <option value="<?php echo $t['url']?>"><?php echo $t['name']?></option>
                                        <?php } ?>
                                    </select>
                                </div>                    
                                <div class="clear"></div>
                            </div> 
                        </div>                         
                        <div class="harness-profiles-section">
                            <h5>Harness Profiles</h5>
                            <div class="field-row">
                                <div class="grid-cell width5P">&nbsp;</div>
                                <div class="grid-cell width45P"><b>Name</b></div>
                                <div class="grid-cell width20P"><b>Purpose</b></div>
                                <div class="grid-cell width30P"><b>Type</b></div>
                                <div class="clear"></div>
                            </div>
                            <?php
                                $case = new TestCase( $cases[0]->ID );
                                $case->load();
                                $suiteObj = new TestSuite();
                                $testSuitesRolesProfilesTypes = $suiteObj->loadProfileTypesToRoles( array( $suites[0]->suite_id ) );
                                $testSuitesRoles = array( $case->harnessRole );
                                foreach($profileInstances as $instance){
                                    $profileTypeName = $instance->profile_type_title;
                                    if( ! $instance->lookup ){
                                        continue;
                                    }
                                    if( ! empty( $testSuitesRolesProfilesTypes ) ){
                                        $isAllowed = false;
                                        foreach( $testSuitesRolesProfilesTypes AS $cRoleName => $cProfilesTypes ){
                                            if( in_array( $cRoleName, $testSuitesRoles ) && in_array( $profileTypeName, $cProfilesTypes ) ){
                                                $isAllowed = true;
                                            }
                                        }
                                        if( ! $isAllowed ) {
                                            continue;
                                        }
                                    }
                                    echo _getProfileRow($instance, 'harness_profile', $current_harness_profile_id);
                                } 
                            ?>
                        </div>
                        <div class="tester-profiles-section">
                            <h5>Tester Profiles</h5>
                            <div class="field-row">
                                <div class="grid-cell width5P">&nbsp;</div>
                                <div class="grid-cell width45P"><b>Name</b></div>
                                <div class="grid-cell width20P"><b>Purpose</b></div>
                                <div class="grid-cell width30P"><b>Type</b></div>
                                <div class="clear"></div>
                            </div>
                            <?php
                                $testSuitesRoles = array( $case->testerRole );
                                foreach($profileInstances as $instance){
                                    $profileTypeName = $instance->profile_type_title;
                                    if( ! $instance->lookup ){
                                        continue;
                                    }
                                    if( ! empty( $testSuitesRolesProfilesTypes ) ){
                                        $isAllowed = false;
                                        foreach( $testSuitesRolesProfilesTypes AS $cRoleName => $cProfilesTypes ){
                                            if( in_array( $cRoleName, $testSuitesRoles ) && in_array( $profileTypeName, $cProfilesTypes ) ){
                                                $isAllowed = true;
                                            }
                                        }
                                        if( ! $isAllowed ) {
                                            continue;
                                        }
                                    }
                                    echo _getProfileRow($instance, 'tester_profile', $current_tester_profile_id);
                                } 
                            ?>
                        </div>
                    </div>
                    <div class="popup-box-footer radius6 noradiustop">
                        <a href="#" class="action-btn process-btn submit-btn has-tooltip" id="send-message-link"><span class="p"></span><span class="t">Confirm</span><span class="simple_tooltip">Confirm Trigger Message<span></span></span></a>
                        <a href="#" class="action-btn cancel-btn close-popup-btn"><span class="p"></span><span class="t">Cancel</span></a>            
                        <div class="clear"></div>
                    </div>
                <a class="close_btn"></a>                        
                <div class="loading loading-with-text radius6"><div><b>PROCESSING YOUR PAYMENT</b><span>Please wait...</span></div></div>                
                <input type="hidden" name="ct-message-action" value="<?php echo wp_create_nonce('send-message')?>" />
            </form>
        </div>
        <?php
        }
    }
    
    exit;
}


////////////////////////////////// Upload Message

function showUploadMessageBox()
{
    global $CPRest;
    
    $user_id = get_current_user_id();
    
    //Getting Subscribed Test Suites
    $suites = getUserSubscriptions();
    
    
    if(!$suites)    
    {
    ?>
        <div class="popup-box" id="trigger-message-box" style="display: none; width: 555px;">    
            <div class="popup-box-header radius6 noradiusbottom">Upload Message</div>        
                <div class="popup-box-content grid-box-body">
                    <p class="message warning">You need to purchase subscription for at least one test suite to Upload message.</p>                
                </div>
                <div class="popup-box-footer radius6 noradiustop">                
                    <a href="#" class="action-btn cancel-btn close-popup-btn"><span class="p"></span><span class="t">Close</span></a>            
                    <div class="clear"></div>
                </div>
            <a class="close_btn"></a>                        
        </div>    
    <?php    
    }else{        
        if(!$suites) //User has not yet defined his profile.
        {
        ?>
            <div class="popup-box" id="trigger-message-box" style="display: none; width: 555px;">    
                <div class="popup-box-header radius6 noradiusbottom">Trigger Message</div>        
                    <div class="popup-box-content grid-box-body">
                        <p class="message notice">You have not defined your profile, yet. Please go to <a href="<?php echo get_site_url()?>/my-profile">My Profile &gt; My Test Suite Subscriptions &gt; Harness Details</a> to define your proifle.</p>                
                    </div>
                    <div class="popup-box-footer radius6 noradiustop">                
                        <a href="#" class="action-btn cancel-btn close-popup-btn"><span class="p"></span><span class="t">Close</span></a>            
                        <div class="clear"></div>
                    </div>
                <a class="close_btn"></a>                        
            </div>    
        <?php    
        }else{
            $lastData = null;
            $products = getUserProductsAndServices($user_id);

            $current_suite_id = $suites[0]->suite_id;                                
            
            $current_product_id = null;
            
            //Getting Test Cases
            $suiteObj = new TestSuite($current_suite_id);
            $cases = $suiteObj->loadTesterInitiatedTestCases();
            $current_case_id = $cases[0]->ID;
            
        ?>
        <div class="popup-box" id="upload-message-box" style="display: none; width: 555px;">
            <form name="messageForm" id="uploadMessageForm" action="" enctype="multipart/form-data" method="post">
                <div class="popup-box-header radius6 noradiusbottom">Upload Message</div>        
                    <div class="popup-box-content grid-box-body">                          
                        <p>Send a message from our test harness to your system. Choose a test case, then select select a file.</p>
                        <div class="info-section">
                            <div class="field-row">
                                <div class="grid-cell width250">
                                    <label for="tm-test-suite">Test Suite</label>
                                    <select name="test-suite" id="um-test-suite" class="select">
                                        <?php foreach($suites as $s){ ?>
                                        <option value="<?php echo $s->suite_id?>" <?php echo $s->suite_id == $current_suite_id ? 'selected="selected"' : '' ?>><?php echo $s->suite_title ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="grid-cell width250 left15">
                                    <label for="um-test-suite">Test Case</label>
                                    <select name="test-case" id="um-test-case" class="select">
                                        <?php foreach($cases as $c){ ?>
                                        <option value="<?php echo $c->ID?>" <?php echo $c->ID == $current_case_id ? 'selected="selected"' : '' ?>><?php echo $c->post_title ?></option>
                                        <?php } ?>
                                    </select>                                    
                                </div>                    
                                <div class="clear"></div>
                            </div>     
                            <div class="field-row">
                                <div class="grid-cell width100P">
                                    <label for="um-product">Product</label>
                                    <select name="product" id="um-product" class="select">
                                        <option value="">Select a Product</option>
                                        <?php foreach($products as $p){ ?>
                                        <option value="<?php echo $p->ID?>" <?php echo $p->ID == $current_product_id ? 'selected="selected"' : '' ?>><?php echo get_post_meta($p->ID, 'product_name', true) ?></option>
                                        <?php } ?>
                                    </select>
                                </div>                                
                                <div class="clear"></div>
                            </div>                             
                            <h5>Choose File</h5>
                            <div class="field-row">
                                <div class="grid-cell width50P">
                                    <label>&nbsp;</label>
                                    <input type="file" name="file" id="um-file" class="left input-file" />
                                </div>
                                <div class="grid-cell width50P">
                                    <label for="um-filename">File Name</label>
                                    <input type="text" class="input" name="filename" id="um-filename" /><br />
                                    <small>(If it's blank, the uploaded file's name will be used.)</small>
                                </div>
                                <div class="clear"></div>
                            </div>
                        </div>
                    </div>
                    <div class="popup-box-footer radius6 noradiustop">
                        <a href="#" class="action-btn process-btn submit-btn has-tooltip" id="upload-message-link"><span class="p"></span><span class="t">Confirm</span><span class="simple_tooltip">Confirm Trigger Message<span></span></span></a>
                        <a href="#" class="action-btn cancel-btn close-popup-btn"><span class="p"></span><span class="t">Cancel</span></a>            
                        <div class="clear"></div>
                    </div>
                <a class="close_btn"></a>                        
                <div class="loading loading-with-text radius6"><div><b>PROCESSING YOUR PAYMENT</b><span>Please wait...</span></div></div>                
                <input type="hidden" name="ct-message-action" value="<?php echo wp_create_nonce('send-uploaded-message')?>" />
            </form>
        </div>
        <?php
        }
    }
    
    exit;
}

function uploadMessage()
{
    global $wpdb, $CPRest;
    
    $user_id = get_current_user_id();
    
    $suite_id = $_POST['test-suite'];
    $product_id = $_POST['product'];
    $case_id = $_POST['test-case'];
    
    $file = $_FILES['file'];
    
    if(!$suite_id || !$case_id || !$file || $file['error'] != UPLOAD_ERR_OK)
    {
        addMessage('Invalid Request', 'error');
        wp_redirect('/my-transaction-log');
        exit;
    }else{
        //Getting Subscription Ino
        $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "users_subscriptions WHERE user_id=%d AND suite_id=%d AND `status`='Active'", $user_id, $suite_id);
        $subscription = $wpdb->get_row($query);
        
        $cases = get_post_meta($case_id, 'test_suite');
        
        if(!$subscription || !in_array($suite_id, $cases)) //Test Suite And Case Validation
        {
            addMessage('Invalid Request', 'error');
            wp_redirect('/my-transaction-log');
            exit;
        }else{
            $filename = basename($file['name']);
            
            //Getting File Content
            if(trim($_POST['filename']) != '')
            {
                $newfilename = trim($_POST['filename']);
                if(strpos($newfilename, ".") === false)
                {
                    //Getting Extension from the old name
                    $ext = pathinfo($filename, PATHINFO_EXTENSION);
                    $newfilename .= '.' . $ext;
                }
                
                $filename = $newfilename;
            }
            
            
            $filecontent = file_get_contents($file['tmp_name']);            
            $filesize = $file['size'];                    
            
            $suiteObj = new TestSuite($suite_id);            
            $caseObj = new TestCase($case_id);
            $caseObj->load();
            
            $fileId = $caseObj->testCaseID . "_" . $subscription->harness_username;
            
            //Getting Previous Files
            $query = $wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->prefix}users_transactions_files WHERE fileId LIKE %s", $fileId . "%");
            $idx = $wpdb->get_var($query);
            $idx++;
            
            $fileId = $fileId . "_" . $idx;
            
            //Save Data To Transactions Files Table
            $return = $wpdb->insert($wpdb->prefix . "users_transactions_files", 
                            array(
                                'fileId' => $fileId,
                                'fileName' => $filename,
                                'content' => $filecontent,
                                'fileSize' => $filesize,
                                'uploadedDate' => date("Y-m-d H:i:s")
                            )
                         );
            
            if(!$return)
            {
                addMessage('There was an error while saving the file: ' . $wpdb->last_error);
                wp_redirect('/my-transaction-log');
                exit;
            }
            
            
            $xmlData = '<api:processUploadedMessageRequest xmlns:api="http://compliancetest.net/api">
                            <api:testCaseUploadMessage>                              
                                <api:testCaseId>' . $caseObj->testCaseID . "_V" . $caseObj->version . '</api:testCaseId>                                
                                <api:testSuiteId>' . $suiteObj->getSuiteID() . '</api:testSuiteId> ';
            if($product_id)
                $xmlData .= '<api:productId>' . get_post_meta($product_id, 'product_id', true) . '</api:productId>';
                
            $xmlData .= '<api:uploadIdentifier>' . $fileId . '</api:uploadIdentifier>                                
                                <api:identity>
                                    <api:username>' . $subscription->harness_username . '</api:username>
                                    <api:password>' . $subscription->harness_password . '</api:password>
                                </api:identity>
                            </api:testCaseUploadMessage>
                         </api:processUploadedMessageRequest>';
            
            
            $result = $CPRest->doUploadMessageAPI('message/upload/', $xmlData);
            
            $resultDoc = new DOMDocument();
            
            if(!$result || !$resultDoc->loadXML($result))
            {
                addMessage('There was an error while uploading the message' . (!$result ? '.' : ": $result"), 'error');
            }else{                
                if($resultDoc->getElementsByTagName('code')->length > 0 && $resultDoc->getElementsByTagName('code')->item(0)->nodeValue == 'ACCEPTED'){
                    
                    addMessage('The file has been uploaded successfully.', 'success');
                }else if($resultDoc->getElementsByTagName('faultstring')->length > 0){
                    addMessage($resultDoc->getElementsByTagName('faultstring')->item(0)->nodeValue, 'error');
                }else if($resultDoc->getElementsByTagName('error')->length > 0){
                    addMessage($resultDoc->getElementsByTagName('error')->item(0)->nodeValue, 'error');
                }else{
                    addMessage('There was an error while uploading the file.', 'error');
                }
                
            }
            
        }
        
    }
    
    wp_redirect('/my-transaction-log');
    
    exit;
}