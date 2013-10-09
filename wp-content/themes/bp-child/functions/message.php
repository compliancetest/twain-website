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
    }else if($action == 'get-case-templates'){
        getCaseTemplates();    
    }else if($action == 'load-message-template'){
        loadMessageTemplate();    
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
    
    header('content-type: application/xml');
    echo '<result>';
    
    if(!$suite_id || !$product_id || !$case_id || !$template)
    {
        echo '<status>error</status>';
        echo '<error>Invalid Request!</error>';
    }else{
        //Getting Subscription Ino
        $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "users_purchases WHERE user_id=%d AND suite_id=%d AND `status`='Active'", $user_id, $suite_id);
        $subscription = $wpdb->get_row($query);
        if(!$subscription || get_post_meta($case_id, 'test_suite', true) != $suite_id) //Test Suite And Case Validation
        {
            echo '<status>error</status>';
            echo '<error>Invalid Request!</error>';    
        }else{
            $suiteObj = new TestSuite($suite_id);
            $variables = $suiteObj->loadVariables();
            
            //Create XML
            $xmlData = '<api:invokeMessageRequest xmlns:api="http://compliancetest.net/api">
                            <api:testCase>
                                <api:testCaseId>' . get_post_meta($case_id, 'test_case_id', true) . '</api:testCaseId>                                
                                <api:testSuiteId>' . $suite_id . '</api:testSuiteId>                                
                                <api:productName>' . get_post_meta($product_id, 'product_name', true) . '</api:productName>                                                                
                                <api:productId>' . $product_id . '</api:productId>
                                <api:messageTemplate url="' . $template . '">
                                    <api:templateProperties>';
            foreach($variables as $v)
            {
                $xmlData .= '<api:property name="' . $v->variable_name . '" value="' . $_POST['variable' . $v->id] . '"/>';
            }
            
            $xmlData .= '</api:templateProperties>
                    </api:messageTemplate>
                    <api:identity>
                        <api:username>' . $subscription->harness_username . '</api:username>
                        <api:password>' . $subscription->harness_password . '</api:password>
                    </api:identity>
                </api:testCase>
            </api:invokeMessageRequest>';
            
            $result = $CPRest->doMessageAPI('message/invoke', $xmlData);
            echo $xmlData;
            $resultDoc = new DOMDocument();
            
            if(!$result || !$resultDoc->loadXML($result))
            {
                echo '<status>error</status>';
                echo '<error>There was an error while sending this message</error>';
            }else{                
                if($resultDoc->getElementsByTagName('code')->length > 0 && $resultDoc->getElementsByTagName('code')->item(0)->nodeValue == 'ACCEPTED'){
                    echo '<status>success</status>';
                }else if($resultDoc->getElementsByTagName('faultstring')->length > 0){
                    echo '<status>error</status>';
                    echo '<error><![CDATA[' . $resultDoc->getElementsByTagName('faultstring')->item(0)->nodeValue . ']]></error>';
                }else if($resultDoc->getElementsByTagName('error')->length > 0){
                    echo '<status>error</status>';
                    echo '<error><![CDATA[' . $resultDoc->getElementsByTagName('error')->item(0)->nodeValue . ']]</error>';
                }else{
                    echo '<status>error</status>';
                    echo '<error>There was an error while sending this message</error>';    
                }
                
            }
            echo $result;
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
            $cases = $suiteObj->loadTestCases();
            foreach($cases as $case)
            {
                echo '<case id="' . $case->ID . '">' . get_post_meta($case->ID, 'test_case_id', true) . '</case>'; 
            }
            echo '</cases>';                   
            
        }
        
        //Getting Template Variables            
        $variables = $suiteObj->loadVariables();
        $userDefaults = unserialize(base64_decode($row->params));
        echo '<variables>';
        foreach($variables as $v)
        {
            echo '<variable id="' . $v->id . '">';
            echo '<name><![CDATA[' . $v->variable_name . ']]></name>';
            echo '<value><![CDATA[' . (isset($userDefaults[$v->id]) ? $userDefaults[$v->id] : $v->variable_default) . ']]></value>';
            echo '</variable>';
        }
        echo '</variables>';
        
        if($current_case_id != $row->case_id)
        {
            //Getting Case Message Templates
            $caseTemplates = getTestCaseTemplates($row->case_id);
            echo '<templates>';
            foreach($caseTemplates as $t)
            {
                echo '<template>' . $t . '</template>';
            }
            echo '</templates>';
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
    //Getting Template Variables
    $values = array();
    foreach($_POST as $k=>$v)
    {
        if(strpos($k, 'variable') !== false)
        {
            $values[str_replace('variable', '', $k)] = $v;
        }
    }
    
    $r = $wpdb->insert($wpdb->prefix . "message_templates", 
        array(
            'user_id' => $user_id,
            'name' => $name,
            'suite_id' => $suite_id,
            'case_id' => $case_id,
            'product_id' => $product_id, 
            'template' => $case_template,
            'params' => base64_encode(serialize($values)),
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



function getCaseTemplates()
{
    global $wpdb;
    
    $user_id = get_current_user_id();
    $suite_id = $_POST['suite_id'];
    $case_id = $_POST['case_id'];
    
    $query = $wpdb->prepare("SELECT suite_id FROM " . $wpdb->prefix . "users_purchases WHERE user_id=%d AND suite_id=%d AND `status`='Active'", $user_id, $suite_id);
    $suite_id = $wpdb->get_var($query);
    header('Content-type: application/xml');
    echo '<results>';
    if($suite_id)
    {
        if(get_post_meta($case_id, 'test_suite', true) == $suite_id)
        {
            //Getting Case Message Templates
            $caseTemplates = getTestCaseTemplates($case_id);
            echo '<templates>';
            foreach($caseTemplates as $t)
            {
                echo '<template>' . $t . '</template>';
            }
            echo '</templates>';
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
    
    $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "users_purchases WHERE user_id=%d AND suite_id=%d AND `status`='Active'", $user_id, $suite_id);
    $subscription = $wpdb->get_row($query);
    header('Content-type: application/xml');
    echo '<results>';
    if($subscription)
    {
        echo '<cases>';
        $suiteObj = new TestSuite($subscription->suite_id);
        $cases = $suiteObj->loadTestCases();
        foreach($cases as $case)
        {
            echo '<case id="' . $case->ID . '">' . get_post_meta($case->ID, 'test_case_id', true) . '</case>'; 
        }
        echo '</cases>';
        
        if($cases)
        {
            //Getting Case Message Templates
            $caseTemplates = getTestCaseTemplates($cases[0]->ID);
            echo '<templates>';
            foreach($caseTemplates as $t)
            {
                echo '<template>' . $t . '</template>';
            }
            echo '</templates>';
        }
        
        //Getting Template Variables
        $variables = $suiteObj->loadVariables();
        $userDefaults = unserialize(base64_decode($subscription->params));
        echo '<variables>';
        foreach($variables as $v)
        {
            echo '<variable id="' . $v->id . '">';
            echo '<name><![CDATA[' . $v->variable_name . ']]></name>';
            echo '<value><![CDATA[' . (isset($userDefaults[$v->id]) ? $userDefaults[$v->id] : $v->variable_default) . ']]></value>';
            echo '</variable>';
        }
        echo '</variables>';
    }
    
    echo '</results>';
    
    exit;
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
            $lastData = getUserLastDataForMessage($user_id);
            $products = getUserProductsAndServices($user_id);
            
            $current_suite_id = !$lastData ? $suites[0]->suite_id : $lastData->suite_id;
            $current_product_id = !$lastData ? $products[0]->ID : $lastData->product_id;
            
            //Getting Test Cases
            $suiteObj = new TestSuite($current_suite_id);
            $cases = $suiteObj->loadTestCases();
            $templateVariables = $suiteObj->loadVariables();
            
            $current_case_id = !$lastData ? $cases[0]->ID : $lastData->case_id;
                        
            //Getting User Previous Message Templates
            $prevMessages = getUserPreviousMessageTemplates($user_id);            
            
            $caseTemplates = getTestCaseTemplates($current_case_id);
            $current_template = !$lastData ? $caseTemplates[0] : $lastData->template;
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
                                        <option value="<?php echo $s->suite_id?>" <?php echo $s->suite_id == $current_suite_id ? 'selected="selected"' : '' ?>><?php echo get_post_meta($s->suite_id, 'ts_name', true) ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="grid-cell width250 left15">
                                    <label for="tm-product">Product</label>
                                    <select name="product" id="tm-product" class="select">
                                        <?php foreach($products as $p){ ?>
                                        <option value="<?php echo $p->ID?>" <?php echo $p->ID == $current_product_id ? 'selected="selected"' : '' ?>><?php echo get_post_meta($p->ID, 'product_name', true) ?></option>
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
                                        <option value="<?php echo $c->ID?>" <?php echo $c->ID == $current_case_id ? 'selected="selected"' : '' ?>><?php echo get_post_meta($c->ID, 'test_case_id', true) ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="grid-cell width250 left15">
                                    <label for="tm-template">Message Template</label>
                                    <select name="template" id="tm-template" class="select">
                                        <option value="">- Select -</option>
                                        <?php foreach($caseTemplates as $t){ ?>
                                        <option value="<?php echo $t?>"><?php echo $t?></option>
                                        <?php } ?>
                                    </select>
                                </div>                    
                                <div class="clear"></div>
                            </div> 
                        </div> 
                        <div class="info-section">
                            <h5>Or Choose / Remove a Saved Message</h5>
                            <div class="field-row">
                                <div class="grid-cell">
                                    <select name="prev-message" id="tm-prev-message" class="select">
                                        <option value="">- Select -</option>
                                        <?php foreach($prevMessages as $m){ ?>
                                        <option value="<?php echo $m->id?>"><?php echo $m->name ?></option>
                                        <?php } ?>
                                    </select>
                                </div>                
                                <a href="<?php echo get_site_url()?>?ct-message-action=<?php echo wp_create_nonce('remove-message')?>" class="action-btn delete-btn left" id="remove-message-template-link"><span class="p"></span><span class="t">Remove</span></a>
                                <div class="clear"></div>
                            </div>
                        </div>
                        <?php
                            //Getting User Default Values
                            $userDefaults = array();
                            foreach($suites as $s)
                            {
                                if($s->suite_id == $current_suite_id)
                                {
                                    $userDefaults = unserialize(base64_decode($s->params));
                                }
                            }
                        ?>
                        <div id="template-variables-contr" class="info-section">
                            <h5>Template Variables</h5>
                            <div class="field-row field-label-row">
                                <div class="grid-cell width45P">Name</div>
                                <div class="grid-cell width55P">Value</div>
                                <div class="clear"></div>
                            </div>
                            <?php foreach($templateVariables as $v){ ?>
                            <?php
                                $value = $v->variable_default;                                
                                
                                if(isset($userDefaults[$v->id]))
                                    $value = $userDefaults[$v->id];
                                
                                if($lastData && isset($lastData->params[$v->id]))
                                    $value = $lastData->params[$v->id];
                            ?>
                            <div class="field-row">
                                <div class="grid-cell width45P"><label><?php echo $v->variable_name?></label></div>
                                <div class="grid-cell width55P"><input type="text" name="variable<?php echo $v->id?>" class="input" value="<?php echo $value ?>" /></div>
                                <div class="clear"></div>
                            </div>
                            <?php } ?>                            
                        </div>
                    </div>
                    <div class="popup-box-footer radius6 noradiustop">
                        <div class="grid-cell left">                            
                            <a href="<?php echo get_site_url()?>?ct-message-action=<?php echo wp_create_nonce('save-message')?>" class="action-btn process-btn left" id="save-message-template-link"><span class="p"></span><span class="t">Save</span></a>
                            <input type="text" class="input left" name="message-template-name" id="message-template-name" placeholder="Message Name" />
                        </div>
                        <a href="#" class="action-btn process-btn submit-btn" id="trigger-message-link"><span class="p"></span><span class="t">Send Message</span></a>
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