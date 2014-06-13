<?php
/**
 * @author Ivan Solowjew
 * @date: 1/19/14
 */

class FvsGenerator {

    private $_excel = null;
    private $_excel_path = '';
    private $_excel_name = '';

    public function __construct() {
        include_once __DIR__ . '/../generate-json/phpExcel/Classes/PHPExcel.php';
        include_once __DIR__ . '/../generate-json/phpExcel/Classes/PHPExcel/IOFactory.php';
    }
    
    public function saveToDatabase() {
        if (!empty($_FILES) && is_uploaded_file($_FILES['fvs_file']['tmp_name'])) {
            $fvs_file = base64_encode(file_get_contents($_FILES['fvs_file']['tmp_name']));
            update_option('fvs_file', $fvs_file);
            update_option('fvs_file_name', $_FILES['fvs_file']['name']);
        }
    }
    
    public function init() {
        $fvs_file = base64_decode(get_option('fvs_file'));
        $this->_excel_name = get_option('fvs_file_name');
        
        $this->_excel_path = ABSPATH . 'wp-content/uploads/' . $this->_excel_name;
        $fvs_handle = fopen($this->_excel_path, 'wa');
        fclose($fvs_handle);
        
        file_put_contents($this->_excel_path, $fvs_file);
        
        $this->_excel = PHPExcel_IOFactory::load( $this->_excel_path );
        error_reporting(0);
    }

    public function process() {
        
        $gateway_list = $this->getGatewayList();
        
        $contribution_prod_urls = array();
        $rollover_prod_urls = array();
        
        foreach ($gateway_list as $gateway) {
            $contribution_prod_urls[] = $gateway['contribution_prod_url'];
            $contribution_test_urls[] = $gateway['contribution_test_url'];
            $rollover_prod_urls[] = $gateway['rollover_prod_url'];
            $rollover_test_urls[] = $gateway['rollover_test_url'];
        }
        
        // N: Primary Destination - Electronic Service Address
        // R: Secondary Destination - Electronic Service Address
        // G: Unique Superannuation Identifier
        
        $activeSheet = $this->_excel->getActiveSheet();
        $activeSheet->setCellValue('A2', 'New Data');
        $count = count($activeSheet->toArray());
        
        for ($i=2; $i<=$count; $i++) {
            $p_esa_url = $activeSheet->getCell('N'.$i)->getValue();
            $s_esa_url = $activeSheet->getCell('R'.$i)->getValue();
            if (array_search($p_esa_url, $contribution_prod_urls) !== FALSE) {
                $activeSheet->setCellValue('N'.$i, $contribution_test_urls[array_search($p_esa_url, $contribution_prod_urls)]);
            } else if (array_search($p_esa_url, $rollover_prod_urls) !== FALSE) {
                $activeSheet->setCellValue('N'.$i, $rollover_test_urls[array_search($p_esa_url, $rollover_prod_urls)]);
            }
            if (array_search($s_esa_url, $contribution_prod_urls) !== FALSE) {
                $activeSheet->setCellValue('R'.$i, $contribution_test_urls[array_search($s_esa_url, $contribution_prod_urls)]);
            } else if (array_search($s_esa_url, $rollover_prod_urls) !== FALSE) {
                $activeSheet->setCellValue('R'.$i, $rollover_test_urls[array_search($s_esa_url, $rollover_prod_urls)]);
            }
        }
        
        // Associated USI list
        $usi_list = $this->getAssociatedUSIList();
        
        // FVS USI list
        $fvs_usi_list = array();
        for ($i=2; $i<=$count; $i++) {
            $fvs_usi_list[] = $activeSheet->getCell('G'.$i)->getValue();
        }
        
        $new_row_index = $count + 1;
        
        foreach ($usi_list as $usi) {
            // If associated usi does exist in FSV, add new row for USI of profile
            if (!in_array($usi, $fvs_usi_list)) {
                $activeSheet->setCellValue('G'.$new_row_index, $usi);
                $new_row_index++;
            }
        }
        
    }
    
    public function download() {
        // redirect output to client browser
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="new_fvs.xls"');

        $objWriter = PHPExcel_IOFactory::createWriter($this->_excel, 'Excel5');
        
        $objWriter->save('php://output');
        
        if (file_exists($this->_excel_path)) {
            unlink($this->_excel_path);
        }
    }
    
    private function getGatewayList() {
        
        global $wpdb;
        $gateways = $wpdb->get_results('Select * From ' . $wpdb->prefix . 'gateways', ARRAY_A);
        
        return $gateways;
    }
    
    private function getAssociatedUSIList() {
        
        global $wpdb;        
        $profile_id_list = $wpdb->get_col('SELECT profile_id FROM ' . $wpdb->prefix . 'users_subscriptions Where gateway_id > 0 AND profile_id > 0');
        $usi_list = $wpdb->get_col('SELECT meta_value FROM ' . $wpdb->prefix . 'community_profile_meta Where profile_id IN (' . implode(',', $profile_id_list) . ') AND meta_key = \'Entity_USI\'');
        
        return $usi_list;
    }
}