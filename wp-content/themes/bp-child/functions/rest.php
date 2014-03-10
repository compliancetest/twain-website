<?php
/**
 * Process Rest Actions to Compliance Test Backend
 */
class CPRest
{
    var $external_api_namespace = 'http://esb.test.compliancetest.net:18280/api';
    var $internal_api_namespace = 'http://esb.test.compliancetest.net:8280/api';

    public function __construct()
    {
        if (get_option('eway_payment_mode') == 'live') {
            $this->external_api_namespace = 'http://esb.compliancetest.net/api';
            $this->internal_api_namespace = 'http://esb.compliancetest.net/api';
        } else {
            $this->external_api_namespace = 'http://esb.test.compliancetest.net:18280/api';
            $this->internal_api_namespace = 'http://esb.test.compliancetest.net:8280/api';
        }
    }

    public function doAPI($url, $data, $isPost = true, $isXMLHeader = true)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if ($isPost)
            curl_setopt($ch, CURLOPT_POST, 1);
        if ($data)
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        if ($isXMLHeader)
            curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/xml"));

        curl_setopt($ch, CURLOPT_ENCODING, 'UTF-8');
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,0); 
	    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
		
        $response = curl_exec($ch);

        if (!curl_errno($ch)) {
            return $response;
        } else {
            echo '<html><head><title>Sorry!</title><link href="http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800|Oswald:400,300,700" rel="stylesheet" type="text/css"/><link href="https://www.compliancetest.net/wp-content/themes/bp-child/css/xslt.css" type="text/css" rel="stylesheet"/></head><body><div id="wrapper"><div id="header-wrapper"><div class="content"><a href="https://www.compliancetest.net" class="logo left"><img src="https://www.compliancetest.net/wp-content/uploads/2013/03/logo.png"/></a></div></div><div id="menu-wrapper"></div><div id="content-wrapper"><div class="content"><div id="content-inner"><h2>An Error Occurred!</h2><p>We\'re sorry, but an error occurred during request execution. Please try again later or contact support.</p></div></div></div></div></body></html>';
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
        return $this->doAPI($this->internal_api_namespace . "/users/" . $action, $data, $isPost, $isXMLHeader);
    }

    /**
     * Do Repository API
     *
     * @param String $action
     * @param String $data
     */

    public function doRepositoryAPI($action, $data = '', $isPost = true, $isXMLHeader = true)
    {
        return $this->doAPI($this->external_api_namespace . "/repository/" . $action, $data, $isPost, $isXMLHeader);
    }

    public function doMessageAPI($action, $data = '', $isPost = true, $isXMLHeader = true)
    {
        return $this->doAPI($this->internal_api_namespace . "/messaging/" . $action, $data, $isPost, $isXMLHeader);
    }

    public function doMetadataAPI($action, $data = '', $isPost = true, $isXMLHeader = true)
    {
        return $this->doAPI($this->external_api_namespace . "/metadata/" . $action, $data, $isPost, $isXMLHeader);
    }

    public function getTemplateList($suiteName, $majorVersion)
    {
        $result = $this->doRepositoryAPI('template/list/' . $suiteName . '/V' . $majorVersion, null, false, false);

        $resultDoc = new DOMDocument();

        if (!$result || !$resultDoc->loadXML($result)) {
            return array();
        }

        $availableTemplates = array();

        if ($resultDoc->getElementsByTagName('template')->length > 0) {
            for ($i = 0; $i < $resultDoc->getElementsByTagName('template')->length; $i++)
                $availableTemplates[] = $suiteName . "/V" . $majorVersion . "/" . $resultDoc->getElementsByTagName('template')->item($i)->nodeValue;
        }

        asort($availableTemplates);

        return $availableTemplates;
    }

}