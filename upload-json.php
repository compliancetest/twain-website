<?php
/**
* Upload json files
*/
$file = $_FILES['profile_instance_file'];
if($file['error'] == UPLOAD_ERR_OK)
{
    //Reading file
    $fp = fopen($file['tmp_name'], 'r');
    $data = fread($fp, filesize($file['tmp_name']));
    fclose($fp);
    
    //Strip Tags
    $data = strip_tags($data);
    
    if(json_decode($data))
    {
        echo $data;    
    }else{
        json_encode(array('status' => 'error', 'msg' => 'Invalid json format!'));
    }
}
exit;