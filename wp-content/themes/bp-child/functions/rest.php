<?php
/**
* Process Rest Actions to Compliance Test Backend
*/

function sendRestUserAction($action, $data = '')
{
    return sendRestAction('http://esb.test.compliancetest.net:8280/api/users' . $action, $data);
}

function sendRestAction($url, $data = '')
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
    curl_setopt($ch, CURLOPT_POST, 1); 
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data); 
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/xml")); 

    $response = curl_exec($ch);
    
    if(!curl_errno($ch)){ 
        return $response;
    } else { 
        return 'Curl Error:' . curl_error($ch);
    } 
}