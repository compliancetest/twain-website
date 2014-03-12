<?php
/**
 * @author Ivan Solowjew
 * @date: 1/19/14
 */
include_once 'JsonSchema/jsv4.php';

class JsonGenerator {

    private $_excel = null;

    private $jsonArrays = array();
    
    private $folder_name = '';
    private $folder_path = '';

    CONST BRACKETS_REG = '#\[((?>[^\[\]]+)|(?R))*\]#x';


    public function __construct( $excelPath) {
        include_once 'phpExcel/Classes/PHPExcel.php';
        include_once 'phpExcel/Classes/PHPExcel/IOFactory.php';
        include_once 'phpExcel/Classes/PHPExcel/Writer/Excel2007.php';
        $this->_excel = PHPExcel_IOFactory::load( $excelPath );
        error_reporting(0);
    }

    public function checkSheets() {
        $this->folder_name = time();
        $this->folder_path = ABSPATH . 'wp-content/uploads/json_zips/' . $this->folder_name;
        if (!mkdir($this->folder_path)) {
            die('Failed to create folders...');
        }
        
        $this->generateProfileJson( 'Profile.Products' );
        $this->generateProfileJson( 'Profile.Employers' );
        
        return $this->_createProfilesZip();
    }

    public function generateProfileJson( $sheetName ) {
        $this->excludeProfiles = array();
        $this->jsonArrays = array();
        $profileFields = array('Type', 'Purpose', 'Title', 'Description', 'Version.Major', 'Version.Minor');
        PHPExcel_Calculation::getInstance()->cyclicFormulaCount = 100000;
        try{
            $sheetData = $this->_excel->getSheetByName($sheetName)->toArray();
            foreach ( $sheetData AS $key => $row) {
                if( $key !== 0 && ($row[1] !== 'Void' && $row[1] !== '')) {
                    if($row[1] === 'Void' || $row[1] === 'void') {
                        continue;
                    }
                    foreach ( $row AS $rowKey => $rowValue ) {
                        if( $rowKey > 2 ){
                            if($rowValue == '' && $rowValue !== 0){
                                continue;
                            }
                            if( in_array( $row[0], $profileFields)) {
                                $this->_addValue( $rowKey-3 , 'Profile.'.$row[0], trim($rowValue), $row[1], $row[2]);
                            } else {
                                $this->_addValue( $rowKey-3 , $row[0], trim($rowValue), $row[1], $row[2]);
                            }
                        }
                    }
                }
            }
        } catch (Exception $e){
        }

        $this->_createProfiles($this->jsonArrays);        
    }

    /**
     * @param $keyNumber
     * @param $keyName
     * @param $value
     * @param $optionality
     */
    private function _addValue ( $keyNumber, $keyName, $value, $optionality, $entryType ) {
        if( $entryType == 'boolean' ){
            if( is_bool( $value) ){
                @settype( $value, $entryType );
            } else {
                $value = $value == 'false' ? false : true;
                @settype( $value, $entryType );
            }
        } else {
            @settype( $value, $entryType );
        }
        $arrayKeys = explode('.', $keyName);
        $keysLength = count($arrayKeys);
        if(strpos($arrayKeys[0], 'Employees') !== FALSE || strpos($arrayKeys[0], 'Members') !== FALSE){
            $value = array( 'value' => $value, 'optionality' => $optionality, 'entryType' => $entryType);
        }
        $temp = 1;
        $str = '$this->jsonArrays['.$keyNumber.']';
        foreach( $arrayKeys AS $key ){
            if( preg_match( self::BRACKETS_REG, $key, $match) ){
                $str .= '["'.$this->replaceBrackets($key).'"]'.$match[0].'';
            } else {
                if( $key != '')
                    $str .= '["'.$key.'"]';
            }
        }
        $str = $str . ' = & $temp';
        $temp = $value;
        eval($str.';');
        return;
    }

    private function replaceBrackets( $string ){
        return preg_replace(self::BRACKETS_REG, '', $string);
    }
    /**
     * @param $data
     */
    private function _createProfiles($data) {
        
        foreach( $data AS $profileKey => $profileData){
            $profileData = $this->_validateData( $profileData);
            //all fields should be not empty
            $json = json_decode(json_encode($profileData));
            $filename = @$profileData['Profile']['Type'].'.'.@$profileData['Profile']['Title'].'.'.@$profileData['Profile']['Version']['Major'].'.'.@$profileData['Profile']['Version']['Minor'].'.json';
            if( $filename == '}.}.}.}.json' || $filename == '....json'){
                continue;
            }
            $validation = $this->_validateJson(($json), $profileData['Profile']['Type']);
            if( ! $validation->valid){
                $filename = 'ErrorLog.'.$filename;
                $errorFile = fopen(  $this->folder_path . '/' . $filename, "w+");
                if (version_compare(phpversion(), '5.4.0', '>')) {
                    fwrite($errorFile, json_encode($validation->errors, JSON_PRETTY_PRINT));
                } else {
                    fwrite($errorFile, json_encode($validation->errors ));
                }
                fclose($errorFile);
            } else {
                $fp = fopen(  $this->folder_path . '/' . $filename, "w+");
                if (version_compare(phpversion(), '5.4.0', '>')) {
                    fwrite($fp, json_encode($json, JSON_PRETTY_PRINT) );
                } else {
                    fwrite($fp, json_encode( $json ) );
                }
                fclose($fp);
            }
        }     
    }
    
    private function _createProfilesZip() {
        // Create Zip file with json data
        $zip = new ZipArchive();
        $zip_name = 'json_profiles.zip'; // Zip name
        $zip->open($this->folder_path . '/' .$zip_name, ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE );
        $files = glob($this->folder_path . '/*.json');
        foreach($files as $file){
            $zip->addFromString(basename($file),  file_get_contents($file));
        }
        $zip->close();
        
        $zip_link = site_url() . '/wp-content/uploads/json_zips/' . $folder_name . '/' . $zip_name;
        
        return $zip_link;   
    }

    private function _validateData( $row ){
        //validate Employees array in Employer Profile type
        $this->_validateSubEntry( $row, 'Employees');
        //validate Members array in Products Profile type
        $this->_validateSubEntry( $row, 'Members');
        return $row;
    }

    /**
     * @param $subArray
     * @param $type - 'Employees' OR 'Members'
     * @param $row  - entry of 'Employees' OR 'Members' array
     * @param $k - number of entry of 'Employees' OR 'Members' array
     * @return array
     */
    private function _checkSubfields ( $subArray, $type, & $row, $k ) {
        $tempArray = array();
        @$tempArray['optionality'] == 'Required';
        foreach($subArray AS $subFieldName => $subFieldValue){
            if(isset($subFieldValue['entryType'])) settype( $subFieldValue['value'], $subFieldValue['entryType'] );
            if( ! isset($subFieldValue['optionality'])) {
                $subFieldValue = $this->_checkSubfields($subFieldValue, $type,  $row, $k );
            }
            if(isset($subFieldValue['optionality']) && $subFieldValue['optionality'] == 'Required' && ($subFieldValue['value'] === '' || $subFieldValue['value'] === 0 ) ){
                unset($row[$type][$k]);
                return false;
            } else {
                if(isset($subFieldValue['value']) && $subFieldValue['value'] !== 0 && $subFieldValue['value'] !== ''){
                    @$tempArray['value'][$subFieldName] = $subFieldValue['value'];
                } else {
                    unset($tempArray['value'][$subFieldName]);
                }
            }
        }
        return  $tempArray;
    }

    /**
     * @param $row - Profile entry
     * @param $entryToValidate - SubEntry type for validation - 'Employees' OR 'Members'
     */
    private function _validateSubEntry( & $row, $entryToValidate){
        if(isset($row[$entryToValidate]) && is_array( $row[$entryToValidate] ) ){
            foreach($row[$entryToValidate] AS $k => $employee){
                foreach( $employee AS $employeeFieldName => $employeeFieldValue ) {
                    if( ! isset($employeeFieldValue['optionality'])) {
                        $employeeFieldValue = $this->_checkSubfields( $employeeFieldValue, 'Employees', $row, $k );
                        if($employeeFieldValue === false) {
                            unset($row[$entryToValidate][$k]);
                            break;
                        }
                    }
                    if( isset($employeeFieldValue['optionality']) && $employeeFieldValue['optionality'] == 'Required' && ($employeeFieldValue['value'] === '' || $employeeFieldValue['value'] === 0) ){
                        unset($row[$entryToValidate][$k]);
                        break;
                    } else {
                        if(isset($employeeFieldValue['value']) && $employeeFieldValue['value'] !== 0 && $employeeFieldValue['value'] !== ''){
                            if(isset($employeeFieldValue['entryType'])) settype( $employeeFieldValue['value'], $employeeFieldValue['entryType'] );
                            $row[$entryToValidate][$k][$employeeFieldName] = $employeeFieldValue['value'];
                        } else {
                            unset($row[$entryToValidate][$k][$employeeFieldName]);
                        }
                    }
                }
                if(count($row[$entryToValidate][$k]) === 1){
                    unset($row[$entryToValidate][$k]);
                    break;
                }
            }
        }
    }

    private function _trace( $data, $exit = false){
        echo '<pre>';
        var_dump($data);
        echo '</pre>';
        if($exit) exit();
    }

    private function _validateJson ($json, $type) {
        $scheme = json_decode(file_get_contents(__DIR__.'/Schemes/'.$type.'.json'));
        return Jsv4::validate($json, $scheme);
    }
}