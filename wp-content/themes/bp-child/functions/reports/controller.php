<?php
add_action('init', 'process_download_reports');
function process_download_reports()
{
    $action = isset($_REQUEST['_download_report_nonce']) ? $_REQUEST['_download_report_nonce'] : null;
    if( wp_verify_nonce( $action, 'download_community_report') ) {
        downloadReport();
    }
}

function downloadReport(){
    global $wpdb;
    error_reporting(E_ALL);
    include_once __DIR__ . '/../generate-json/phpExcel/Classes/PHPExcel.php';
    include_once __DIR__ . '/../generate-json/phpExcel/Classes/PHPExcel/IOFactory.php';

    $excel2 = PHPExcel_IOFactory::createReader('Excel2007');
    $excel2 = $excel2->load(  __DIR__ . '/../../groups/templates/SuperStreamTestProgress.xlsx' ); // Empty Sheet

    $community_name = $wpdb->get_var( $wpdb->prepare( "SELECT  name FROM wp_bp_groups WHERE id = %d ", $_REQUEST['cid'] ) );
    $excel2->setActiveSheetIndex(0);
    $excel2->getActiveSheet()->setCellValue('C1', $community_name .' Community' );
    $excel2->setActiveSheetIndex(1);
    $excel2->getActiveSheet()->setCellValue('C1', $community_name .' Community' );
    $excel2->setActiveSheetIndex(2);
    $excel2->getActiveSheet()->setCellValue('C1', $community_name .' Community' );
    $excel2->setActiveSheetIndex(0);
    $row_number_sheet1 = $row_number_sheet2 = $row_number_sheet3 = 4;

    $all_data = array();
    $products = $wpdb->get_results( "SELECT * FROM wp_posts WHERE post_type = 'product-service'" );
    $s3 = new S3Wrapper();
    foreach( $products AS $pr ){
        $claims = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM wp_compliance_claims WHERE product_id = %d ORDER BY product_id", $pr->ID ) );
        if( $claims ) {
            foreach( $claims AS $claim ) {
                $product = new ProductAndService($claim->product_id);
                $product->load();
                $organisation = new CT_Organisation( $claim->organisation_id );
                if( $product->visibility == 'Private' && $claim->organisation_id != $wpdb->get_var( $wpdb->prepare("SELECT organisation_id FROM wp_organisations_subscriptions WHERE user_id = %d ", get_current_user_id() ) ) ){
                    continue;
                }
                $com_id = $wpdb->get_var( $wpdb->prepare("SELECT meta_value FROM wp_postmeta WHERE post_id = %d AND meta_key = 'community_id'", $claim->suite_id ) );
                if( $_REQUEST['cid'] != $com_id ){
                    continue;
                }
                $agreement_data = getProductLastAgreement($claim->product_id);
                $data = array(
                    'product_owner' => $organisation->organisation_name,
                    'product_id' => $organisation->abn,
                    'product_name' => $product->name,
                    'product_version' => "$product->version",
                    'product_release_date' => date('Y-m-d', strtotime($product->release_date)),
                    'suite_name' => get_the_title($claim->suite_id),
                    'level' => process_level(str_replace(';;', '', $claim->conformance_level)),
                    //this used for multusorting by level
                    'level_weight' => process_level_weight( process_level(str_replace(';;', '', $claim->conformance_level)) ),
                    'claim_id' => $claim->claim_id,
                    'claim_token' => $claim->token,
                    'claim_url' => $s3->getProductClaimLink($claim->token),
                    'claim_status' => $claim->status,
                    'e2e_company' => $agreement_data !== false ? $agreement_data['partner_company'] : '',
                    'e2e_product' => $agreement_data !== false ? $agreement_data['partner_product'] : '',
                    'status' => $agreement_data !== false ? $agreement_data['status'] : '',
                    'certificate_id' => $agreement_data !== false ? $agreement_data['certificate_id'] : '',
                    'certificate_link' => $agreement_data !== false ? $agreement_data['certificate_link'] : '',
                );
                if (strpos($claim->role, 'Employer') !== false) {
                    $data['type'] = 'Employer';
                }
                if (strpos($claim->role, 'Fund') !== false) {
                    $data['type'] = 'Fund';
                }
                if (strpos($claim->role, 'Employer') === false && strpos($claim->role, 'Fund') === false) {
                    $data['type'] = str_replace(';;', '', $claim->role);
                }
                $all_data[] = $data;
            }
        }
        $test_plans = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM wp_test_plans WHERE product_id = %d AND is_deleted = 0 ", $pr->ID ) );
        if( $test_plans ) {
            foreach ($test_plans AS $test_plan) {
                $organisation_id = $wpdb->get_var( $wpdb->prepare("SELECT organisation_id FROM wp_organisations_subscriptions WHERE id = %d ", $test_plan->organisation_subscription_id ) );
                $organisation = new CT_Organisation( $organisation_id );
                $product = new ProductAndService( $test_plan->product_id );
                $product->load();
                if( $product->visibility == 'Private' && $organisation_id != $wpdb->get_var( $wpdb->prepare("SELECT organisation_id FROM wp_organisations_subscriptions WHERE user_id = %d ", get_current_user_id() ) ) ){
                    continue;
                }
                $com_id = $wpdb->get_var( $wpdb->prepare("SELECT meta_value FROM wp_postmeta WHERE post_id = %d AND meta_key = 'community_id'", $test_plan->suite_id ) );
                if( $_REQUEST['cid'] != $com_id ){
                    continue;
                }
                $data = array(
                    'product_owner' => $organisation->organisation_name,
                    'product_id' => $organisation->abn,
                    'product_name' => $product->name,
                    'product_version' => "$product->version",
                    'product_release_date' => date('Y-m-d', strtotime($product->release_date)),
                    'suite_name' => get_the_title($test_plan->suite_id),
                    'level' => process_level(str_replace(';;', '', $test_plan->level)),
                    //this used for multusorting by level
                    'level_weight' => process_level_weight( process_level(str_replace(';;', '', $test_plan->level)) ),
                    'claim_id' => '',
                    'claim_token' => '',
                    'claim_url' => '',
                    'claim_status' => 'In Progress',
                    'e2e_company' => '',
                    'e2e_product' => '',
                    'status' => '',
                    'certificate_id' => '',
                    'certificate_link' => '',
                );
                if (strpos($test_plan->role, 'Employer') !== false) {
                    $data['type'] = 'Employer';
                }
                if (strpos($test_plan->role, 'Fund') !== false) {
                    $data['type'] = 'Fund';
                }
                if (strpos($test_plan->role, 'Employer') === false && strpos($test_plan->role, 'Fund') === false) {
                    $data['type'] = str_replace(';;', '', $test_plan->role);
                }
                $all_data[] = $data;
            }
        }
    }
    $data = sortData( $all_data );
    foreach( $data AS $row ){
        if( $row['type'] == 'Employer' ){
            add_entry_to_excel( $excel2, $row, $row_number_sheet1, 0 );
            $row_number_sheet1++;
        }
        if( $row['type'] == 'Fund' ){
            add_entry_to_excel( $excel2, $row, $row_number_sheet2, 1 );
            $row_number_sheet2++;
        }
        if ( $row['type'] != 'Fund'  && $row['type'] != 'Employer' ) {
            add_entry_to_excel( $excel2, $row, $row_number_sheet3, 2 );
            $row_number_sheet3++;
        }
    }
    $excel2->setActiveSheetIndex(0);
    $objWriter = PHPExcel_IOFactory::createWriter($excel2, 'Excel2007');
    header('Content-type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="'.$community_name.'TestProgress.xls"');
    $objWriter->save('php://output');
    exit();
}
function add_entry_to_excel( &$excel2, $data, $row_number, $sheet_number ){
    $excel2->setActiveSheetIndex( $sheet_number );
    $excel2->getActiveSheet()
        ->setCellValue('A'.$row_number, $data['product_owner'] )
        ->setCellValue('B'.$row_number, $data['product_id'] )
        ->setCellValue('C'.$row_number, $data['type'] )
        ->setCellValue('D'.$row_number, $data['product_name'] )
        ->setCellValue('E'.$row_number, $data['product_version'] )
        ->setCellValue('F'.$row_number, $data['product_release_date'] )
        ->setCellValue('G'.$row_number, $data['suite_name'] )
        ->setCellValue('H'.$row_number, $data['level'] )
        ->setCellValue('I'.$row_number, $data['claim_status'] )
        ->setCellValue('K'.$row_number, $data['e2e_company'] )
        ->setCellValue('L'.$row_number, $data['e2e_product'] )
        ->setCellValue('M'.$row_number, $data['status'] );
    if( ! empty( $data['claim_url'] ) && ! empty( $data['claim_id'] ) ) {
        $excel2->getActiveSheet()->getCell('J' . $row_number)->getHyperlink()->setUrl($data['claim_url']);
        $excel2->getActiveSheet()->setCellValue('J' . $row_number, $data['claim_id']);
    }
    if( ! empty( $data['certificate_link'] ) && ! empty( $data['certificate_id'] ) ) {
        $excel2->getActiveSheet()->getCell('N' . $row_number)->getHyperlink()->setUrl($data['certificate_link']);
        $excel2->getActiveSheet()->setCellValue('N' . $row_number, $data['certificate_id']);
    }
}
function sortData( $data ){
    $sort = array();
    foreach( $data as $k => $v ) {
        $sort['org_name'][$k]     = $v['product_owner'];
        $sort['product_name'][$k] = $v['product_name'];
        $sort['test_suite'][$k]   = $v['suite_name'];
        $sort['level_name'][$k]   = $v['level_weight'];
    }
    array_multisort( $sort['org_name'], SORT_ASC, $sort['product_name'], SORT_ASC, $sort['test_suite'], SORT_ASC, $sort['level_name'], SORT_ASC,  $data );
    return $data;
}
function getProductLastAgreement( $product_id ){
    global $wpdb;
    $responder = false;
    $agreements = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM wp_e2e_agreement WHERE requester_service_id IN( SELECT wp_post_id FROM wp_services WHERE product_id = %d ) AND status = 'Verified' ORDER BY id DESC", $product_id ) );
    if( ! $agreements ){
        $responder = true;
        $agreements = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM wp_e2e_agreement WHERE responder_service_id IN( SELECT wp_post_id FROM wp_services WHERE product_id = %d ) AND status = 'Verified' ORDER BY id DESC", $product_id ) );
    }
    if( ! $agreements ){
        $agreements = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM wp_e2e_agreement WHERE requester_service_id IN( SELECT wp_post_id FROM wp_services WHERE product_id = %d ) ORDER BY id DESC", $product_id ) );
    }
    if( ! $agreements ){
        $responder = true;
        $agreements = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM wp_e2e_agreement WHERE responder_service_id IN( SELECT wp_post_id FROM wp_services WHERE product_id = %d ) ORDER BY id DESC", $product_id ) );
    }
    if( $agreements ){
        foreach( $agreements AS $agreement ) {
            if ($responder) {
                $service = new Service($agreement->requester_service_id);
                $token = $agreement->responder_token;
            } else {
                $service = new Service($agreement->responder_service_id);
                $token = $agreement->requester_token;
            }
            $service->load();
            $product = new ProductAndService( $service->service_product_id );
            $product->load();
            if( $product->visibility == 'Private' ){
                continue;
            }
            $s3 = new S3Wrapper();
            $org_id = $wpdb->get_var($wpdb->prepare("SELECT organisation_id FROM wp_organisations_subscriptions WHERE user_id = %d ", $service->service_user_id));
            $org = new CT_Organisation($org_id);
            return array(
                'agreement_id' => $agreement->id,
                'partner_company' => $org->organisation_name,
                'partner_product' => get_the_title($service->service_product_id),
                'partner_product_id' => $service->service_product_id,
                'status' => $agreement->status,
                'certificate_link' => $agreement->status == 'Verified' ? $s3->getAgreementClaimLink($token) : '',
                'certificate_id' => $agreement->claim_id
            );
        }
    }
    return false;
}


function process_level( $level ){
    switch ( strtolower( $level ) ){
        case 'b':
            $report_level = 'silver';
            break;
        case 'a':
            $report_level = 'gold';
            break;
        default:
        case 'saff':
            $report_level = 'bronze';
            break;
    }
    return $report_level;
}
function process_level_weight( $level ){
    switch ( strtolower( $level ) ){
        case 'bronze':
            $weight = 1;
            break;
        case 'silver':
            $weight = 2;
            break;
        default:
        case 'gold':
        $weight = 3;
            break;
    }
    return $weight;
}