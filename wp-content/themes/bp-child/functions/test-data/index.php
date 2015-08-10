<?php
/**
* Test Data  
*/

require_once(THE_FUNCTION . '/test-data/functions.php');
require_once(THE_FUNCTION . '/test-data/controller.php');
require_once(THE_FUNCTION . '/test-data/view.php');

add_action('init', 'cp_process_test_data_actions');

function cp_process_test_data_actions()
{
    global $wpdb;
    
    $action = isset($_REQUEST['td-action']) ? $_REQUEST['td-action'] : null;
    if($action)
    {
        if(wp_verify_nonce($action, 'edit-profile-type'))
        {
            readProfileType();            
        }else if(wp_verify_nonce($action, 'save-profile-type')){
            saveProfileType();
        }else if(wp_verify_nonce($action, 'delete-profile-type')){
            deleteProfileType();
        }else if(wp_verify_nonce($action, 'get-harness-profile-ui') || wp_verify_nonce($action, 'get-tester-profile-ui')){
            createUIFromProfileType($action);
        }else if(wp_verify_nonce($action, 'save-harness-instance') || wp_verify_nonce($action, 'save-tester-instance')){
            saveProfileInstance($action);
        }else if(wp_verify_nonce($action, 'view-profile-type')){
            viewProfileType();
        }else if(wp_verify_nonce($action, 'view-profile-instance')){
            viewProfileInstance();
        }else if(wp_verify_nonce($action, 'delete-harness-instance') || wp_verify_nonce($action, 'delete-profile-instance')){
            $redirect = isset($_REQUEST['return']) ? base64_decode($_REQUEST['return']) : $_SERVER['REDIRECT_URL'];
            $result = deleteProfileTypeInstance( $action, $_REQUEST['id'] );
            addMessage( $result['message'], $result['status'] );
            wp_redirect($redirect);
            exit();
        }else if(wp_verify_nonce($action, 'copy-harness-instance')){
            $redirect = isset( $_REQUEST['return'] ) ? base64_decode( $_REQUEST['return'] ) : $_SERVER['REDIRECT_URL'];
            $profileId = intval( $_REQUEST['id'] );
            $result = copyProfileInstance( $profileId );
            if( $result['status'] == 'error' ){
                addMessage( $result['message'], 'error' );
                return;
            }
            addMessage( 'Profile instance was copied.' );
            wp_redirect( $redirect );
            exit;
        }else if(wp_verify_nonce($action, 'download-profile-type')){
            downloadProfileType();
        }else if(wp_verify_nonce($action, 'download-profile-instance')){
            downloadProfileTypeInstance();
        }else if(wp_verify_nonce($action, 'download-profile-error')){
            downloadProfileError();
        }else if(wp_verify_nonce($action, 'update-profile-lookup')){
            updateProfileLookup();
        }else if(wp_verify_nonce($action, 'create-expanded-version')){
            createExpandedVersion( $_REQUEST['id'], $_REQUEST['factor'] );
            exit('success');
        }else if(wp_verify_nonce($action, 'prepare_schedule')){
            $data = (object) $_GET;
            $tags = \Tag::getItemTags($data->id);
            $data->tags = array_map(function( $item ) {
                return $item->name;
            }, $tags);
            $data->profile = ProfileInstance::getProfileBy('id', $data->id);
            render_view( 'test-data/views/schedule-popup.phtml', $data, true );
        }else if(wp_verify_nonce($action, 'trigger_run')){
            $data = (object) $_GET;
            $data->profile = ProfileInstance::getProfileBy('id', $data->id);
            $tags = \Tag::getItemTags($data->id);
            $data->tags = array_map(function( $item ) {
                return $item->name;
            }, $tags);
            render_view( 'test-data/views/trigger.phtml', $data, true );
        }else if(wp_verify_nonce($action, 'save-schedule')){
            $profileId = intval( $_POST['profile_id'] );
            \MicroServices\MicroServices::prepareRunRequest($profileId);
        }else if( wp_verify_nonce($action, 'execute-schedule') ){
            if ( !empty($_POST['datetime']) && getUTCTimeStamp( strtotime( $_POST['datetime'] ) ) < strtotime(gmdate('Y-m-d H:i'))) {
                exit('A run can only be scheduled to start in the future');
            }
            //user cant select date more than 2 hours into the future
//            if (getUTCTimeStamp(strtotime( $_POST['datetime'])) - strtotime(gmdate('Y-m-d H:i')) > 7200) {
//                exit('Selected date/time is more than 2 hours into the future.');
//            }
            $profileId = intval( $_POST['profile_id'] );
            \MicroServices\MicroServices::executeRunRequest( $profileId, date( 'Y-m-d H:i:s', getUTCTimeStamp( strtotime( $_POST['datetime'].':00' ) ) ) );
            $profile = ProfileInstance::getProfileBy('id', $profileId);
            $esb = new ManageESB();
            $esb->updateStatusByProfileS3Url($profile->token, 'STARTING', 'PREPARED');
            exit('success');

        }else if( wp_verify_nonce($action, 'change-schedule-status') ){
            $esb = new ManageESB();
            $s3Url = $esb->updateStatus( $_POST['id'], $_POST['status'], $_POST['prevstatus']);
            if( 'DELETED' == $_POST['status'] ){
                $profileUrl = explode('/', $s3Url->PROFILE_S3_URL );
                $profileToken = str_replace( '.json', '', end( $profileUrl ) );
                //removing all Run profiles which were created from this Schedule
                $runProfile = ProfileInstance::getProfileBy( 'token', $profileToken );
                deleteProfileTypeInstance( wp_create_nonce('delete-profile-instance'), $runProfile->id, true  );
            }
            render_view( 'test-data/views/trigger-schedule.phtml', true, true );

        } else if( wp_verify_nonce($action, 'delete-run-confirm') ){

            render_view( 'test-data/views/confirm-run-delete.phtml', (object) $_GET, true );

        } else if( wp_verify_nonce($action, 'terminate-run-confirm') ){

            render_view( 'test-data/views/confirm-run-terminate.phtml', (object) $_GET, true );

        } else if( wp_verify_nonce($action, 'download_pdf') ){
            $esb = new ManageESB();
            $runId = intval($_REQUEST['runid']);
            $schedule = $esb->getSchedule($runId);
            $profile = ProfileInstance::getProfileBy('id', intval($_REQUEST['profile']));
            header('Content-type: application/vnd.ms-excel');
                header('Content-Disposition: attachment; filename="PerformanceReport-'.$profile->profile_name.'.xlsx"');
            include_once __DIR__ . '/../generate-json/phpExcel/Classes/PHPExcel.php';
            include_once __DIR__ . '/../generate-json/phpExcel/Classes/PHPExcel/Writer/Excel2007.php';

            $objPHPExcel = new PHPExcel();

            $objPHPExcel->getProperties()->setCreator("ComplianceTest");

            $objPHPExcel->getProperties()->setTitle("ComplianceTest");
            $objPHPExcel->setActiveSheetIndex(0)->setTitle('Messages');

            $esb = new ManageESB();

            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(35);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(70);

            $objPHPExcel->getActiveSheet()
                ->setCellValue('A1', 'StartAt' )
                ->setCellValue('B1', 'BuildAt' )
                ->setCellValue('C1', 'SentAt' )
                ->setCellValue('D1', 'ReceiptAt' )
                ->setCellValue('E1', 'ResponseStatus' )
                ->setCellValue('F1', 'ResponseTime' )
                ->setCellValue('G1', 'ConversationID' )
                ->setCellValue('H1', 'RequestMessageID' );
            $results = $esb->getMessages($runId);
            $rowNumber = 2;
            foreach ($results AS $result) {
                if ($result->ResponseStatus == 'RECEIPT') {
                    $objPHPExcel->getActiveSheet()->getStyle('E'.$rowNumber)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('33FF33');
                } else {
                    $objPHPExcel->getActiveSheet()->getStyle('E'.$rowNumber)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('CC0000');
                }
                $objPHPExcel->getActiveSheet()->getStyle('E'.$rowNumber)->getFont()->getColor()->setRGB('ffffff');
                $objPHPExcel->getActiveSheet()
                    ->setCellValue('A'.$rowNumber, $result->StartAt )
                    ->setCellValue('B'.$rowNumber, $result->BuildAt )
                    ->setCellValue('C'.$rowNumber, $result->SentAt )
                    ->setCellValue('D'.$rowNumber, $result->ReceiptAt )
                    ->setCellValue('E'.$rowNumber, ManageESB::getReceiptMapping($result->ResponseStatus, 2))
                    ->setCellValue('F'.$rowNumber, $result->ResponseTime < 1 ? 1 : $result->ResponseTime )
                    ->setCellValue('G'.$rowNumber, $result->ConversationID )
                    ->setCellValue('H'.$rowNumber, $result->MessageID );

                $rowNumber++;
            }
            $objPHPExcel->createSheet(1);
            $objPHPExcel->setActiveSheetIndex(1)->setTitle('Summary');

            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);

            $objPHPExcel->getActiveSheet()
                ->setCellValue('A1', 'Product' )
                ->setCellValue('B1', 'Profile' )
                ->setCellValue('C1', 'Profile Description' )
                ->setCellValue('D1', 'Tags' )
                ->setCellValue('E1', 'Messages Prepared' )
                ->setCellValue('F1', 'Messages Sent' )
                ->setCellValue('G1', 'Start Time' )
                ->setCellValue('H1', 'Status' );
            $objPHPExcel->getActiveSheet()
                ->setCellValue('A2', get_the_title(intval($_REQUEST['product'])) )
                ->setCellValue('B2', $profile->profile_name )
                ->setCellValue('C2', $profile->profile_description )
                ->setCellValue('D2', implode(', ', array_unique(array_map(function($tag){ return $tag->name;},\Tag::getItemTags($profile->id)))))
                ->setCellValue('E2', $schedule->CONVERSATION_PREPARED_COUNT . ' of ' . $schedule->CONVERSATION_COUNT )
                ->setCellValue('F2', $schedule->CONVERSATION_SENT_COUNT . ' of ' . $schedule->CONVERSATION_COUNT )
                ->setCellValue('G2', $schedule->START_AT )
                ->setCellValue('H2', $schedule->SCHEDULE_STATUS_CODE );
            $objPHPExcel->setActiveSheetIndex(0);
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            $objWriter->save('php://output');
            exit();
        }
    }
}