<?php
/**
* Manage Compliance Claim
*/
if(!defined('TABLE_CLAIM'))
    define('TABLE_CLAIM', 'wp_compliance_claims');

add_action('template_redirect', 'ct_claim_certification_view');
//Display Claim Certificate
function ct_claim_certification_view()
{
    if(get_query_var('pagename') == 'claim-certificate')
    {
        global $wpdb;
        
        //Display Claim
        $token = get_query_var('claim');
        //Remove .pdf from the token
        $token = str_replace(".pdf", "", $token);
        wp_redirect( S3Wrapper::getProductClaimLink( $token ), 301 );exit;
    }
}
    
add_action('init', 'process_claim_actions', 100);
function process_claim_actions()
{
    if(wp_verify_nonce($_REQUEST['_claimnonce'], 'edit-claim'))
    {
        editClaim();
    }else if(wp_verify_nonce($_REQUEST['_claimnonce'], 'get-suite-info-for-claim')){
        getTestSuiteInfoForClaim();
    }else if(wp_verify_nonce($_REQUEST['_claimnonce'], 'make-claim')){
        makeClaim();
    }else if(wp_verify_nonce($_REQUEST['_claimnonce'], 'delete-claim')){
        deleteClaim();
    }else if(isset($_GET['download-certificate'])){
        ct_download_certificate();
    }
}

function ct_download_certificate()
{    
    global $wpdb;
    
    //Display Claim
    $token = $_GET['claim'];
    
    //Remove .pdf from the token
    $token = str_replace(".pdf", "", $token);
    $certificate = S3Wrapper::getProductClaim( $token );
    if(!$certificate)
    {
        echo "Invalid Request!";
        exit;
    }
    
//    header("Content-type: application/pdf");
    header("Expires: Mon, 26 Nov 1962 00:00:00 GMT");
    header("Last-Modified: " . gmdate("D,d M Y H:i:s") . " GMT");
    header("Cache-Control: no-cache, must-revalidate");
    header("Pragma: no-cache");
    header("Content-Type: Application/octet-stream");
    header("Content-disposition: attachment; filename=" . $token . ".pdf");
    
    echo $certificate;
    
    //Echo PDF file
    exit;
    
}

function deleteClaim()
{
    global $wpdb;
    
    $productID = $_REQUEST['product_id'];
    $claimID = isset($_REQUEST['id']) ? $_REQUEST['id'] : null;
    
    $user_id = get_current_user_id();
    
    $claim = new ComplianceClaim($claimID);
    $claim->load();

    $redirectUrl = get_site_url() . '/' . base64_decode($_REQUEST['return']);

    $return = isset($_REQUEST['return']) ? $redirectUrl : "/";
    
    if(!can_delete_compliance_claim($claimID))
    {
        addMessage('Permission Denied!', 'error');
        wp_redirect($return);
        exit;
    }
    
    if(!$wpdb->delete($wpdb->prefix . "compliance_claims", array('id' => $claimID)))
    {
        addMessage($wpdb->last_error, 'error');
    }else{
        //delete S3 files
        $s3 = new S3Wrapper();
        $s3->deleteObject( '/claims/products/'. $claim->token . '.pdf' );

        $cloud_search = new CloudSearch();
        $cloud_search->cloud_search_delete_item( $claimID, 'claim' );
        addMessage("The claim was deleted.");
    }
    wp_redirect($return);
    exit;
}

function makeClaim()
{
    global $wpdb;
    
    $productID = $_POST['product_id'];
    $claimID = isset($_POST['id']) ? $_POST['id'] : null;
    $suiteId = $_POST['suite_id'];
    $confLevel = $_POST['level'];
    $role = $_POST['role'];
    
    if(_saveClaim($productID, $suiteId, $confLevel, $role, 'Self Assessed', $claimID))
    {
        addMessage('Compliance Claim was saved successfully!');
    }
    
    wp_redirect('/my-products');
    exit;    
}

function _saveClaim($organisation_id, $productID, $suite_id, $confLevel, $role, $status, $claimID = null, $planID = 0, $has_exclusions )
{
    global $wpdb;
    
    $isNew = !$claimID ? true : false;
    
    $user_id = get_current_user_id();
    
    $claim = new ComplianceClaim($claimID);
    $claim->load();
    
    $is_allowed = false;
    if(!$claimID && can_make_compliance_claim($organisation_id))
        $is_allowed = true;
    else if(can_edit_compliance_claim($claimID))
        $is_allowed = true;
    
    if(!$is_allowed)
    {
        addMessage('Permission Denied!', 'error');
        return false;
    }
    $cloud_search = new CloudSearch();

    if(!$claimID) //Make Claim
    {
        $wpdb->insert(TABLE_CLAIM, array(
            'product_id'    =>  $productID,
            'organisation_id'    =>  $organisation_id,
            'suite_id'    =>  $suite_id,
            'conformance_level'    =>  $confLevel,
            'role'    =>  $role,
            'status'    =>  $status,
            'created_date'    =>  date('Y-m-d H:i:s'),
            'last_updated'    =>  date('Y-m-d H:i:s'),
            'token' => createClaimToken(),
            'certificate' => '', //This is empty for now. Ilia will need to update this
            'audit'    =>  '',
            'has_exclusions' => $has_exclusions
        ));
        
        $claimID = $wpdb->insert_id;
        
        if (!$claimID) {
            addMessage($wpdb->last_error, 'error');
            return false;
        }
        

        $wpdb->update(TABLE_CLAIM, array(
            'claim_id'    =>  getClaimID($wpdb->insert_id, $suite_id)
        ), array('id' => $claimID));
        $cloud_search->cloud_search_update_claim( $claimID );


    }else{  //Edit Claim
        $wpdb->update(TABLE_CLAIM, array(
            'suite_id'    =>  $suite_id,
            'conformance_level'    =>  $confLevel,
            'role'    =>  $role,
            'status'    =>  $status,
            'last_updated'    =>  date('Y-m-d H:i:s')
        ), array('id' => $claim->id));
        
        $cloud_search->cloud_search_update_claim( $claim->id );
    }
    
    //Update DPF
    $pdfString = createClaimPDF($claimID, $planID);
    $s3 = new S3Wrapper();
    $s3->putObject('/claims/products/' . $wpdb->get_var( $wpdb->prepare("SELECT token FROM wp_compliance_claims WHERE id = %d ", $claimID ) ) . '.pdf', $pdfString, 'application/pdf');

    if($isNew)
    {
        //Send Email to Community Admins
        $claim = new ComplianceClaim($claimID);
        $claim->load();
        
        $userInfo = get_userdata($user_id);
        
        $emailData = array(
            '[claim_id]' => $claim->claim_id,
            '[product_name]' => get_the_title($claim->product_id),
            '[product_url]' => get_permalink($claim->product_id),
            '[suite_name]' => get_the_title($claim->suite_id),
            '[suite_url]' => get_permalink($claim->suite_id),
            '[issuer]' => $claim->issuer,
            '[conformance_level]' => $claim->conformance_level,
            '[role]' => $claim->role,
            '[status]' => $claim->status,
            '[date]' => $claim->created_date,            
            '[username]' => $userInfo->first_name . " " . $userInfo->last_name,            
            '[useremail]' => $userInfo->user_email,            
            '[certificate]' => '<a href="' . get_site_url()  . '/claims/' . $claim->token . '" target="_blank">View PDF</a>' 
        );
        
        cp_send_email_to_community_admin(get_post_meta($claim->suite_id, 'community_id', true), 'claim_created_admin', $emailData);        
    }
    
    
    return true;
}

function createClaimPDF($claim_id, $planID )
{
    global $wpdb, $post;

    require_once(THE_FUNCTION . '/tcpdf/cppdf.php');
    require_once(THE_FUNCTION . '/tcpdf/config/tcpdf_config.php');
    // Include 2D barcode class
    require_once(THE_FUNCTION . '/tcpdf/tcpdf_barcodes_2d.php');
    
    // Create new PDF document
    $pdf = new CPPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    // Set document meta information
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('ComplianceTest');
    $pdf->SetTitle('ComplianceTest Certificate');
    $pdf->SetSubject('ComplianceTest Certificate');

    // Set margins
    $pdf->SetMargins(12, 29, 12, true);
    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

    // Set auto page breaks
    $pdf->SetAutoPageBreak(TRUE, 20);

    // Set image scale factor
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

    // set certificate file
    //$certificate = 'file://' . THE_FUNCTION . '\tcpdf\claims.pem';
    $certificate = get_option('pdf_certificate');
    $private_key = get_option('pdf_private_key');

    // set additional information
    $info = array(
//        'Name' => 'ComplianceTest',
//        'Reason' => 'ComplianceTest Testing',
        'Location' => 'Australia',
        'ContactInfo' => 'http://www.compliancetest.net',
    );

    // set document signature
    //$pdf->setSignature($certificate, $certificate, '', '', 2, $info);
    $pdf->setSignature($certificate, $private_key, '', '', 2, $info);

    // ---------------------------------------------------------

    // Set font
    $pdf->SetFont('opensans', '', 13, '', true);

    // Set line-height
    $pdf->setCellHeightRatio(1);

    // Add a page
    // This method has several options, check the source code documentation for more information.
    $pdf->AddPage();

    $title = '<h1 style="color: #000; font-size: 48pt; font-weight: bold; line-height: 42pt; text-transform: uppercase;">CERTIFICATE</h1>';
    $description = '<p style="font-size: 13pt; line-height:16pt;"><br>This certificate confirms that the holder has successfully completed the indicated test suite using the specified product or service version. The test suite has been designed to meet the compliance requirements of the reference specification issuer.<br></p>';
    
    //Getting Claim Defaults
    $claim = new ComplianceClaim($claim_id);
    $claim->load();
    
    $suite = new TestSuite($claim->suite_id);
    $suite->load();
    
    $certificate_data_info = '
<style>
    table.certificate-info th {
        border-bottom: 0.2em solid #959595;
        font-weight: normal;
        margin-left: 2pt;
        width: 35%;
        font-size:13pt;
        color:#262626;
    }
    table.certificate-info td {
        border-bottom: 0.2em solid #959595;
        font-weight: bold;
        width:63%;
        font-size:13pt;
        color:#000;
    }
</style>
<br><br>
<table cellspacing="5" cellpadding="5" class="certificate-info" width="100%">
    <tr>
        <th>Issued To</th>
        <td>' . get_post_meta($claim->product_id, 'product_owner', true) . '</td>
    </tr>
    <tr>
        <th>Product or Service</th>
        <td><a href="' . get_permalink($claim->product_id) .'">' . get_the_title($claim->product_id) . '</a></td>
    </tr>
    <tr>
        <th>Product Version</th>
        <td>' . get_post_meta($claim->product_id, 'product_version', true) . '</td>
    </tr>
    <tr>
        <th>Test Suite</th>
        <td><a href="' . get_permalink($claim->suite_id) .'">' . get_the_title($claim->suite_id) . '</a></td>
    </tr>
    <tr>
        <th>Test Suite Version</th>
        <td>' . $suite->version . '</td>
    </tr>
    <tr>
        <th>Specification Issuer</th>
        <td>' . $claim->issuer . '</td>
    </tr>
    <tr>
        <th>Conformance Level(s)</th>
        <td>' . implode(cp_explode($claim->conformance_level), ", ") . '</td>
    </tr>
    <tr>
        <th>Role(s)</th>
        <td>' . implode(cp_explode($claim->role), ", ") . '</td>
    </tr>
    <tr>
        <th>Status</th>
        <td>' . $claim->status . '</td>
    </tr>
    <tr>
        <th>Claim ID</th>
        <td>' . $claim->claim_id .'</td>
    </tr>
    <tr>
        <th>Date of Claim</th>
        <td>' . formatDate($claim->created_date, 'd F Y') . '</td>
    </tr>
</table>
';


    $pdf->SetFont('opensansb', '', 13, '', true);

    $pdf->setHtmlLinksStyle(array(91, 117, 182));

    // Print text using writeHTMLCell()
    $pdf->writeHTMLCell(0, 0, '', '', $title, 0, 1, 0, false, 'C', true);


    $pdf->SetFont('opensans', '', 13, '', true);
    // Print text using writeHTMLCell()
    $pdf->writeHTMLCell(0, 0, '', '', $description, 0, 1, 0, false, '', false);

    // Print text using writeHTMLCell()
    $compliance_tested_image = K_PATH_IMAGES . "compliance-tested.png";
    $pdf->Image($compliance_tested_image, '', '', 120, '', 'PNG', '', 'N', false, 300, 'C', false, false, 1, false, false, false);

    // define active area for signature appearance
    $pdf->setSignatureAppearance(45, 72, 121, 29);

    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

    // *** set an empty signature appearance ***
//    $pdf->addEmptySignatureAppearance(0, 20, 210, 50);


    // Print text using writeHTMLCell()
    $pdf->writeHTMLCell('', '', '', '', $certificate_data_info, 0, 1, 0, true, '', true);

    // Styles for QR code
    $style = array('border' => false, 'padding' => 0, 'vpadding' => 10, 'fgcolor' => array(0, 0, 0), 'position' => 'C');

    // QRCODE,H : QR-CODE Best error correction
    $pdf->write2DBarcode( get_site_url() . '/claims/' . $claim->token . ".pdf", 'QRCODE,H', '', '', 40, 40, $style, 'N');

    $link = '<div style="text-align:center;"><a href="' . S3Wrapper::getProductClaimLink( $claim->token ) . '" target="_blank" style="font-size:13pt; text-decoration:none;">' . S3Wrapper::getProductClaimLink( $claim->token ) .'</a></div>';

    $pdf->writeHTMLCell(0, 0, '', '', $link, 0, 1, 0, true, '', true);
    // ---------------------------------------------------------


    // ---------------------------------------------------------
    $pdf->SetMargins(3.8, 24.5, 3.8, true);

    // Add a page
    // This method has several options, check the source code documentation for more information.
    $pdf->AddPage();


    $pdf->setTextShadow(array('enabled' => false));
    
    $post = get_post($claim->suite_id);
    
    //Getting Test Cases
    $args = array(
            'post_type' => 'test-case',         
            'posts_per_page' => -1,                            
            'orderby'  => 'title',
            'order'     => 'ASC',                            
            'meta_query' => array('relation' => 'and')
    );
    //Add Test Suite ID
    $args['meta_query'][] = array('key' => 'test_suite', 'value' => $claim->suite_id, 'compare' => '=');
    
    $args['meta_query'][] = array(
                                'key' => 'hide_case',
                                'value' => 0,
                                'compare' => '='
                            ); 
    $args['meta_query'][] = array(
                                'key' => 'conformance_level_' . $suite->id,
                                'value' => TEST_SUITE_DEFAULT_CONFORMANCE_LEVEL_CODE,
                                'compare' => '!='
                            );  
          
    $args['meta_query'][] = array('key' => 'choose_tester_role', 'value' => cp_explode($claim->role), 'compare' => 'IN');               
    $args['meta_query'][] = array('key' => 'conformance_level_'. $claim->suite_id, 'value' => cp_explode($claim->conformance_level),'compare' => 'IN');
    
    
    
    $get_query = new WP_Query($args);
    $get_query->post = $post;
    //Add Order by Scenaro 
    $get_query->set('suppress_filters', false);
    add_filter('posts_join_paged', 'add_scenario_join_query', 100, 2);
    add_filter('posts_orderby', 'add_scenario_orderby_query', 100, 2);
    add_filter('posts_fields_request', 'add_scenario_fields_query', 100, 2);
    $testCases = $get_query->get_posts();
    
    //Remove Filters
    remove_filter('posts_join_paged', 'add_scenario_join_query');
    remove_filter('posts_orderby', 'add_scenario_orderby_query');
    remove_filter('posts_fields_request', 'add_scenario_fields_query');
    
    $plan = new TestPlan($planID);
    $plan->load();
    
    $esb = new ManageESB();
    $testCaseStatuses = $esb->getCaseStatus($plan->organisation_subscription_id, $suite->id);
    
    $query = ManageESB::$esbdb->prepare("SELECT m.ID as MSG_ID, m.PAYLOAD AS MSG,m.S3_PAYLOAD_LOCATION AS MSG_URL, ots.MESSAGE_OUTCOME_LABEL AS OUTCOME, cc.TEST_CASE_WP_ID as TEST_CASE_ID FROM " . $esb->table_conversation_metadata . " AS c " .
             "LEFT JOIN " . $esb->table_message_metadata . " AS m ON c.ID=m.MSH_CONVERSATION_ID " .
             "LEFT JOIN " . $esb->table_message_outcome_status . " AS ots ON c.MSH_TEST_OUTCOME_STATUS_ID=ots.ID " .
             "LEFT JOIN " . $esb->table_product_configuration . " AS p ON p.PRODUCT_ID=c.PRODUCT_ID " .
             "LEFT JOIN " . $esb->table_test_suite_configuration . " AS sc ON c.TEST_SUITE_CONFIGURATION_ID=sc.ID " .
             "LEFT JOIN " . $esb->table_test_case_configuration . " AS cc ON c.TEST_CASE_CONFIGURATION_ID=cc.ID " .                 
             " WHERE c.AUDIT_RECORD=1 AND c.ORGANISATION_SUBSCRIPTION_ID=%d AND sc.TEST_SUITE_WP_ID=%d AND p.PRODUCT_WP_ID=%d", $plan->organisation_subscription_id, $suite->id, $plan->product_id);
    
    $esbResults = ManageESB::$esbdb->get_results($query);        

    //Classify the results by Scenario
    $results = array();
    
    foreach($testCases as $row)
    {
        if(isset($esbResults))
        {
            foreach($esbResults as $erow)    
            {
                if($erow->TEST_CASE_ID == $row->ID)
                {
                    $row->OUTCOME = $erow->OUTCOME;
                    $row->MSG_ID = $erow->MSG_ID;
                    if( ! isset( $row->messages ) ){
                        $row->messages = array();
                    }
                    $row->messages[] = array( 'outcome' => $erow->OUTCOME, 'msg_id' => $erow->MSG_ID, 'msg' => $erow->MSG, 'msg_url' => $erow->MSG_URL  );
                }
            }
        }
        if(!isset($results[$row->scenarioId]))
            $results[$row->scenarioId] = array();
        $results[$row->scenarioId][] = $row;
    }
    
    $first = true;
    $idx = 0;
    $rowsCounter = 11;
    $fArr = array();
    
    $cases_table_css = '<style>
                            .test-cases-table th {
                                background-color:#5a75b6;
                                color:#fff;
                                font-size:7pt;
                                vertical-align:middle;
                                line-height:18pt;
                                text-align:center;
                                font-weight:bold;
                            }
                            .test-cases-table tr td {
                                height: 100px !important;
                            }
                            .test-cases-table th.test-outcome{
                                line-height:10px;
                            }
                            .test-cases-table th.test-scenario{
                                text-align:left;
                            }
                            .test-cases-table td {
                                font-size:6pt;
                                line-height:6pt;
                                color:#000;
                            }
                            .test-cases-table .even td{
                                background-color:#f3f4f5;
                            }
                            .test-cases-table .odd td{
                                background-color:#ececed;
                            }
                            .test-cases-table td a{
                                font-size:10pt;
                            }
                            .test-cases-table td.test-scenario{
                                background-color:#e2e2e2;
                            }

                            .issued, .test-outcome, .supporting-evidence{
                                text-align:center;
                            }
                        </style>';
    $cases_table_header = '<tr><th colspan="6">Completed Test Cases</th></tr>
                           <tr>
                                <th class="test-scenario" style="width:25%; vertical-align:middle;">Test Scenario</th>
                                <th class="test-case" style="width:12%;">Test Case</th>
                                <th class="issued" style="width:8%;">Issued</th>
                                <th class="test-intent" style="width:30%;">Test Intent Description</th>
                                <th class="test-outcome" style="width:8%;">Test<br/>Outcome</th>
                                <th class="supporting-evidence" style="width:17%;">Supporting Evidence</th>
                           </tr>';
                           
    $excluded_cases_table_header = '<tr><th colspan="5">Excluded Test Cases</th></tr>
                                <tr>
                                    <th class="test-scenario" style="width:25%; vertical-align:middle;">Test Scenario</th>
                                    <th class="test-case" style="width:12%;">Test Case</th>
                                    <th class="issued" style="width:8%;">Issued</th>
                                    <th class="test-intent" style="width:30%;">Test Intent Description</th>
                                    <th class="test-reason" style="width:25%;">Reason</th>
                                </tr>';
    /*<table cellspacing="1" cellpadding="3" class="test-cases-table" width="100%">*/
    
    //Sorting Results
    $general_cases = array();
    $excluded_cases = array();
    
    foreach($results as $scId => $testCases)
    {
        
        for($i=0; $i < count($testCases); $i++) {
            $is_excluded = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM wp_test_plans_excluded_cases WHERE test_plan_id = %d AND test_case_id = %d ", $planID, $testCases[$i]->ID ) );
            $is_optional = get_post_meta( $testCases[$i]->ID, 'testcase_status', true );
            
            if ( isset($testCaseStatuses[$claim->suite_id][$claim->product_id][$testCases[$i]->ID]) && $testCaseStatuses[$claim->suite_id][$claim->product_id][$testCases[$i]->ID] == 'pass') {
                $is_excluded = false;
                $is_optional = 'No';
            }    
            
            if ($is_excluded ) {
                if (!isset($excluded_cases[$scId]))
                    $excluded_cases[$scId] = array();
                $testCases[$i]->excluded_reason = $is_excluded->reason;
                $excluded_cases[$scId][] = $testCases[$i];
            } else if($is_optional != 'Yes') {
                if (!isset($general_cases[$scId]))
                    $general_cases[$scId] = array();
                $general_cases[$scId][] = $testCases[$i];
            }
        }
    }
    
    $cases_html = '';
    
    $general_cases_table_html = '';
    $excluded_cases_table_html = '';
    
    
    //Exclude Cases
    foreach ($excluded_cases as $scId => $testCases) {
        for($i=0; $i < count($testCases); $i++) {
            $t_desc = get_post_meta($testCases[$i]->ID ,'test_intent_description', true);            
            $excluded_cases_table_html .= '<tr class="' . ($idx %2 ==0 ? 'odd' : 'even') . '">';
            if ($i == 0) {
                $excluded_cases_table_html .= '<td class="test-scenario" rowspan="' . count($testCases) . '"><strong>' . $testCases[$i]->scenarioCode . ':</strong><br>' . $testCases[$i]->scenarioDescription . '</td>';
            }
            $excluded_cases_table_html .= '
                    <td class="test-case">' . get_the_title($testCases[$i]->ID) . '</td>
                    <td class="issued">' . formatDate(get_post_meta($testCases[$i]->ID ,'published', true)) . '</td>
                    <td class="test-intent">' . $t_desc . '</td>
                    <td class="test-reason">' .  apply_filters('the_content', $testCases[$i]->excluded_reason ) . '</td>
                </tr>';
        }
    }
    
    //General Cases
    foreach ($general_cases as $scId => $testCases) {
        
        for($i=0; $i < count($testCases) ; $i++) {
            $rString = get_post_meta($testCases[$i]->ID, 'test_intent_description', true);
            $general_cases_table_html .= '<tr class="' . ($idx % 2 == 0 ? 'odd' : 'even') . '">';
            
            $message_cols = count($testCases[$i]->messages);
            
            if($i == 0) {
                $totalRows = count($testCases);
                //Getting Total Rows
                for($k=0; $k < count($testCases) ; $k++) {
                    if (isset($testCases[$k]->messages) && count($testCases[$k]->messages) > 1) {
                        $totalRows += count($testCases[$k]->messages) -1;
                    }
                }
                $general_cases_table_html .= '<td class="test-scenario" rowspan="' . $totalRows . '"><strong>' . $testCases[$i]->scenarioCode . ':</strong><br>' . $testCases[$i]->scenarioDescription . '</td>';
            }
            
            if (isset($testCases[$i]->messages)) {                
            
                $general_cases_table_html .= '<td class="test-case" rowspan="' . $message_cols . '">' . (isset($testCases[$i]->ID) ? get_the_title($testCases[$i]->ID) : '') . '</td>
                                            <td class="issued" rowspan="' . $message_cols . '">' . formatDate(get_post_meta($testCases[$i]->ID, 'published', true)) . '</td>
                                            <td class="test-intent" rowspan="' .$message_cols . '">' . $rString . '</td>';
                for ($j = 0; $j < $message_cols; $j++) {                    
                    
                    if($j > 0)
                        $general_cases_table_html .= '</tr><tr>';
                        
                    $data = $testCases[$i]->messages[$j];
                    
                    $general_cases_table_html .= '<td class="test-outcome">' . (isset($data['outcome']) ? $data['outcome'] : '-') . '</td>';    
                    
                    $message = $esb->getMessageEnvelope( $data['msg_id'] );
                    
                    $fileName = getcwd() . '/wp-content/uploads/' . get_the_title($testCases[$i]->ID) . '_' . $data['msg_id'] . '.xml';
                    $myfile = fopen($fileName, "w");
                    fwrite($myfile, $message->PAYLOAD);
                    fclose($myfile);
                    
                    $pdf->Annotation(0, $rowsCounter, 0, 0, $scId, array('Subtype' => 'FileAttachment', 'Name' => get_the_title($testCases[$i]->ID) . '_' . $data['msg_id'], 'FS' => $fileName));
                    $pdf->Bookmark('"' . get_the_title($testCases[$i]->ID) . '_' . $data['msg_id'] . '"', 0, 0, $rowsCounter, 'B', array(128, 0, 255), 0, '*' . get_the_title($testCases[$i]->ID) . '_' . $data['msg_id'] . '.xml');
                    $rowsCounter = $rowsCounter + 2;
                    $general_cases_table_html .= '<td class="supporting-evidence" style="vertical-align:top;">
            Click "' . get_the_title($testCases[$i]->ID) . '_' . $data['msg_id'] . '" bookmark to see message (offline) <br> OR
            <a href="' . get_site_url() . '/message-envelope?id=' . $data['msg_id'] . '">' . get_site_url() . '/message-envelope?id=' . $data['msg_id'] . '</a> link to check message on our website
            </td>';
                    array_push($fArr, $fileName);
                    
                }                
 
            } else {
                $general_cases_table_html .= '<td class="test-case">' . (isset($testCases[$i]->ID) ? get_the_title($testCases[$i]->ID) : '') . '</td>
                                        <td class="issued">' . formatDate(get_post_meta($testCases[$i]->ID, 'published', true)) . '</td>
                                        <td class="test-intent">' . $rString . '</td>
                                        <td class="test-outcome">-</td>
                                        <td class="supporting-evidence" style="vertical-align:top;">-</td>';
            }
            
            $general_cases_table_html .= '</tr>';
        }
    }
    
    
    $pdf->SetFont('opensans', '', 13, '', true);

    if( $general_cases_table_html != '' ) {        
        $general_cases_table_html = $cases_table_css . '<table cellspacing="1" cellpadding="3" class="test-cases-table" width="100%">' . $cases_table_header . $general_cases_table_html . '</table>';
        
        $pdf->writeHTMLCell(0, 0, '', '', $general_cases_table_html, 0, 1, 0, true, '', true);                
    }
    
    if( $excluded_cases_table_html != '' ) {
        
        if( $general_cases_table_html != '' ) {        
            $pdf->AddPage();        
        }
        
        $excluded_cases_table_html = $cases_table_css . '<table cellspacing="1" cellpadding="3" class="test-cases-table" width="100%">' . $excluded_cases_table_header . $excluded_cases_table_html . '</table>';
        $pdf->writeHTMLCell(0, 0, '', '', $excluded_cases_table_html, 0, 1, 0, true, '', true);
    }
        
    
    
    // Close and output PDF document
    // This method has several options, check the source code documentation for more information.
    $pdfString = $pdf->Output('ComplianceTest-certificate.pdf', 'S');

    foreach( $fArr AS $f ){
        unlink( $f );
    }

    return $pdfString;
    //============================================================+
    // END OF FILE
    //============================================================+
}

function editClaim()
{
    $productID = $_GET['product_id'];
    $claimID = isset($_GET['id']) ? $_GET['id'] : null;
    
    $user_id = get_current_user_id();
    
    $claim = new ComplianceClaim($claimID);
    $claim->load();
    
    $is_allowed = false;
    if(!$claimID && can_make_compliance_claim($productID))
        $is_allowed = true;
    else if(can_edit_compliance_claim($claimID))
        $is_allowed = true;
    
    if(!$is_allowed)
    {
        ?>
        <div class="popup-box" id="make-claim-box" style="display: none;">
            <div class="popup-box-header radius6 noradiusbottom">Permission Error!</div>
            <div class="popup-box-content">    
                You are not allowed to <?php echo !$claimID ? 'make a claim to the product.' : 'edit the claim.'?>
            </div>
            <div class="popup-box-footer radius6 noradiustop">                        
                <a href="#" class="action-btn cancel-btn close-popup-btn"><span class="p"></span><span class="t">Close</span></a>
                <div class="clear"></div>
            </div>
            <a class="close_btn"></a>                
            <div class="loading" style="display: none;"></div>
        </div>
        <?php
        exit;
    }
    
    
    $product = new ProductAndService($productID);
    $product->load();
    $suites = getUserTestSuites();
    
    $suite = new TestSuite($claim->suite_id);
    $levels = $suite->loadConformanceLevel();
    $roles = $suite->loadRoles();
    ?>
    <div class="popup-box" id="make-claim-box" style="display: none;">
        <form name="makeClaimForm" id="makeClaimForm" action="" method="post">
            <div class="popup-box-header radius6 noradiusbottom">Compliance Claim Form</div>
            <div class="popup-box-content grid-box-body">    
                <div class="field-row">
                    <div class="grid-cell">
                        <label>Suite</label>
                        <select class="select" name="suite_id" id="suite_id">                            
                            <option value="">Select a Suite</option>
                            <?php foreach($suites as $s){ ?>
                            <option value="<?php echo $s->ID?>" <?php echo $claim->suite_id == $s->ID ? 'selected="selected"' : ''?>><?php echo get_the_title($s->ID)?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="grid-cell left15">
                        <label>Level</label>
                        <select class="select" name="level" id="level">
                            <?php if(!$levels){ ?>
                                <option value="">Select a Level</option>
                            <?php 
                                }else{ 
                                    foreach($levels as $l){
                            ?>
                                <option value="<?php echo $l['code']?>" <?php echo $claim->conformance_level == $l['code'] ? 'selected="selected"' : ''?>><?php echo $l['code']?></option>
                            <?php 
                                    }
                                } 
                                
                            ?>
                        </select>
                    </div>
                    <div class="clear"></div>
                </div>
                <div class="field-row">
                    <div class="grid-cell">
                        <label>Role</label>
                        <select class="select" name="role" id="role">
                            <?php if(!$roles){ ?>
                                <option value="">Select a Role</option>
                            <?php 
                                }else{ 
                                    foreach($roles as $r){
                            ?>
                                <option value="<?php echo $r['name']?>" <?php echo $claim->role == $r['name'] ? 'selected="selected"' : ''?>><?php echo $r['name']?></option>
                            <?php 
                                    }
                                } 
                                
                            ?>
                        </select>
                    </div>
                    <div class="grid-cell left15">
                        <label>&nbsp;</label>
                        <input type="checkbox" name="agree_obligation" id="agree_obligation" value="1" <?php echo $claim->id ? 'checked="checked"' : '' ?> /> I agree to the <a href="#obligation-box" id="show-opligation-box" cp-type="inline" rel="custom-popup">Obligation</a>.
                    </div>
                    <div class="clear"></div>
                </div>            
            </div>
            <div class="popup-box-footer radius6 noradiustop">                        
                <a href="#" class="action-btn process-btn"><span class="p"></span><span class="t">SUBMIT</span></a>
                <a href="#" class="action-btn cancel-btn close-popup-btn"><span class="p"></span><span class="t">Close</span></a>
                <div class="clear"></div>
            </div>
            <a class="close_btn"></a>                
            <div class="loading1" style="display: none;"></div>
            <input type="hidden" name="id" value="<?php echo $claimID?>" />
            <input type="hidden" name="product_id" value="<?php echo $productID?>" />
            <?php wp_nonce_field('make-claim', '_claimnonce'); ?>
        </form>
    </div>

    <?php
    exit;
}

function getTestSuiteInfoForClaim()
{
    $suiteID = $_POST['suite_id'];
    
    $suite = new TestSuite($suiteID);
        
    $suite->load();
    
    if(!$suite->id)
    {
        echo '<result><status>error</status></result>';
    }else{
        $confLevelHTML = '';
        ob_start();
        ?>
        <select class="select" name="level" id="level">
            <?php if(!$suite->conformanceLevel){ ?>
                <option value="">Select a Level</option>
            <?php }else{ ?>
                <?php foreach($suite->conformanceLevel as $row){ ?>
                <option value="<?php echo $row['code']?>"><?php echo $row['code']?></option>
               <?php } ?>
           <?php } ?>
        </select>
        
        <?php
        $confLevelHTML = ob_get_clean();
        ob_end_clean();
        ob_start();
        $rolesHTML = '';
        ?>
        <select class="select" name="role" id="role">
            <?php if(!$suite->roles){ ?>
                <option value="">Select a Role</option>
            <?php }else {?> 
                <?php foreach($suite->roles as $row){ ?>
                <option value="<?php echo $row['name']?>"><?php echo $row['name']?></option>
               <?php } ?>
           <?php } ?>
        </select>
       <?php
        $rolesHTML = ob_get_clean();
        ob_end_clean();
        
        header('content-type: application/xml');
        echo '<result>';
        echo '<status>success</status>';
        echo '<conflevel><![CDATA[' . $confLevelHTML . ']]></conflevel>';
        echo '<roles><![CDATA[' . $rolesHTML . ']]></roles>';
        echo '</result>';
       
    }
    exit;
}


function getClaimsByProductId($product_id)
{
    global $wpdb;
        
    $query = $wpdb->prepare("SELECT c.*, pm.meta_value as `issuer` FROM " . TABLE_CLAIM . " AS c LEFT JOIN " . $wpdb->postmeta . " as pm on pm.post_id=c.suite_id AND pm.meta_key='ts_issuer'  WHERE product_id=%d", $product_id);
    $rows = $wpdb->get_results($query);
    
    return $rows;
}
function getClaimByTestPlanData( $data ){
    global $wpdb;
    $family_mark = $wpdb->get_var( $wpdb->prepare("SELECT family_mark FROM wp_test_suites WHERE suite_id = %d ", $data['suite_id'] ) );
    $query = $wpdb->prepare("SELECT * FROM " . TABLE_CLAIM . " WHERE product_id = %d AND suite_id IN ( SELECT suite_id FROM wp_test_suites WHERE family_mark = %d )", $data['product_id'], $family_mark );

    $rows = $wpdb->get_row($query);

    return $rows;
}
function getClaimByRole( $data, $conformance_level, $role ){
    global $wpdb;
    $family_mark = $wpdb->get_var( $wpdb->prepare("SELECT family_mark FROM wp_test_suites WHERE suite_id = %d ", $data['suite_id'] ) );
    $query = $wpdb->prepare("SELECT * FROM " . TABLE_CLAIM . " WHERE product_id = %d AND suite_id IN ( SELECT suite_id FROM wp_test_suites WHERE family_mark = %d ) AND conformance_level = %s AND role = %s", $data['product_id'], $family_mark, $conformance_level, $role );

    $rows = $wpdb->get_row($query);

    return $rows;
}
function getTestPlansByProductId($product_id)
{
    global $wpdb;

    $query = $wpdb->prepare("SELECT c.*, pm.meta_value as `issuer` FROM {$wpdb->prefix}test_plans AS c LEFT JOIN " . $wpdb->postmeta . " as pm on pm.post_id=c.suite_id AND pm.meta_key='ts_issuer'  WHERE product_id=%d", $product_id);
    $rows = $wpdb->get_results($query);

    return $rows;
}
function getTestPlansAndClaimsCounter( $product_id ){
    global $wpdb;

    $query = $wpdb->prepare("SELECT c.*, pm.meta_value as `issuer` FROM {$wpdb->prefix}test_plans AS c LEFT JOIN " . $wpdb->postmeta . " as pm on pm.post_id=c.suite_id AND pm.meta_key='ts_issuer'  WHERE product_id=%d", $product_id);
    $rows = $wpdb->get_results($query);

    return $rows;
}
function getClaimsBySuiteId($suite_id)
{
    global $wpdb;
        
    $query = $wpdb->prepare("SELECT c.*, pm.meta_value as `product_name` FROM " . TABLE_CLAIM . " AS c LEFT JOIN " . $wpdb->postmeta . " as pm on pm.post_id=c.product_id AND pm.meta_key='product_name'  WHERE suite_id=%d", $suite_id);
    $rows = $wpdb->get_results($query);
    
    return $rows;
}

function getTestPlansBySuiteId($suite_id, $user_id)
{
    global $wpdb;
    
    $query = $wpdb->prepare("SELECT p.*, pm.meta_value as `product_name` FROM " . $wpdb->prefix . "test_plans AS p 
                                LEFT JOIN " . $wpdb->postmeta . " as pm on pm.post_id=p.product_id AND pm.meta_key='product_name'  
                                LEFT JOIN " . $wpdb->prefix . "users_subscriptions as um on um.parent_id=p.organisation_subscription_id
                            WHERE p.suite_id=%d AND um.user_id=%d", $suite_id, $user_id);
    $rows = $wpdb->get_results($query);
    
    
    return $rows;
}

function getClaimID($claim_id, $suite_id)
{
    $suite = new TestSuite($suite_id);                               
    $claimID = $suite->getSuiteID() . "_" . substr(str_shuffle('01234567890123456789'), 0, 6);
    cp_generate_password();
    return $claimID;
}

function createClaimToken()
{
    global $wpdb;
    
    do{
        $token = cp_generate_password(20);
        $id = $wpdb->get_var("SELECT id FROM {$wpdb->prefix}compliance_claims WHERE token='$token'");        
    }while($id);
    
    return $token;
}
