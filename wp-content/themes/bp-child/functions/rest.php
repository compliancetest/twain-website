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

class CPRest
{
    var $api_namespace = 'http://esb.test.compliancetest.net:8280/api';
    
    public function doAPI($url, $data, $isPost = true, $isXMLHeader = true)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
        if($isPost)
            curl_setopt($ch, CURLOPT_POST, 1); 
        if($data)
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data); 
        if($isXMLHeader)
            curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/xml")); 
        
        curl_setopt($ch, CURLOPT_ENCODING, 'UTF-8');
        $response = curl_exec($ch);
        
        if(!curl_errno($ch)){ 
            return $response;
        } else { 
            return 'Curl Error:' . curl_error($ch);
        }
    }
    
    /**
    * Do User API
    * 
    * @param String $action
    * @param String $data
    */
    public function doUserAPI($action, $data = '', $isPost = true, $isXMLHeader = true)
    {
        return $this->doAPI($this->api_namespace . "/users/" . $action, $data, $isPost, $isXMLHeader);
    }
    
    /**
    * Do Repository API
    * 
    * @param String $action
    * @param String $data
    */
    
    public function doRepositoryAPI($action, $data = '', $isPost = true, $isXMLHeader = true)
    {
        return $this->doAPI($this->api_namespace . "/repository/" . $action, $isPost, $isXMLHeader);
    }
    
    public function doMessageAPI($action, $data = '', $isPost = true, $isXMLHeader = true)
    {
        return $this->doAPI($this->api_namespace . "/messaging/" . $action, $isPost, $isXMLHeader);
    }    
    
}