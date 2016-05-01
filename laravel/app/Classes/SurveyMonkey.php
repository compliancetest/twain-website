<?php

/**
 * Class for SurveyMonkey API v3
 * @package php-surveymonkey
 */
class SurveyMonkey
{
    /**
     * @var string API key
     * @access protected
     */
    protected $_apiKey;

    /**
     * @var string API access token
     * @access protected
     */
    protected $_accessToken;

    /**
     * @var string API protocol
     * @access protected
     */
    protected $_protocol;

    /**
     * @var string API hostname
     * @access protected
     */
    protected $_hostname;

    /**
     * @var string API version
     * @access protected
     */
    protected $_version;

    /**
     * @var resource $conn The client connection instance to use.
     * @access private
     */
    private $conn = null;

    /**
     * @var array (optional) cURL connection options
     * @access protected
     */
    protected $_connectionOptions;

    /**
     * @const SurveyMonkey Status code:  Success
     */
    const SM_STATUS_SUCCESS = 0;

    public static function successfulHttpResponse($code)
    {
        if ($code >= 200 and $code < 300) {
            return true;
        }
        return false;
    }

    /**
     * SurveyMonkey API Status code definitions
     */
    public static $SM_STATUS_CODES = array(
        0 => "Success",
        1 => "Not Authenticated",
        2 => "Invalid User Credentials",
        3 => "Invalid Request",
        4 => "Unknown User",
        5 => "System Error",
        6 => "Plan Limit Exceeded"
    );

    /**
     * Explain Survey Monkey status code
     * @param integer $code Status code
     * @return string Definition
     */
    public static function explainStatusCode($code)
    {
        return self::$SM_STATUS_CODES[$code];
    }

    /**
     * The SurveyMonkey Constructor.
     *
     * This method is used to create a new SurveyMonkey object with a connection to a
     * specific api key and access token
     *
     * @param string $apiKey A valid api key
     * @param string $accessToken A valid access token
     * @param array $options (optional) An array of options
     * @param array $connectionOptions (optional) cURL connection options
     * @throws SurveyMonkey_Exception If an error occurs creating the instance.
     * @return SurveyMonkey A unique SurveyMonkey instance.
     */
    public function __construct($apiKey, $accessToken, $options = array(), $connectionOptions = array())
    {

        if (empty($apiKey)) throw new SurveyMonkey_Exception('Missing apiKey');
        if (empty($accessToken)) throw new SurveyMonkey_Exception('Missing accessToken');
        $this->_apiKey = $apiKey;
        $this->_accessToken = $accessToken;

        $this->_protocol = (!empty($options['protocol'])) ? $options['protocol'] : 'https';
        $this->_hostname = (!empty($options['hostname'])) ? $options['hostname'] : 'api.surveymonkey.net';
        $this->_version = (!empty($options['version'])) ? $options['version'] : 'v3';

        $this->_connectionOptions = $connectionOptions;
    }

    /**
     * Build the request URI
     * @param string $endpoint API endpoint to call in the form: resource/method
     * @return string Constructed URI
     */
    protected function buildUri($endpoint, $params = array())
    {
        $url = $this->_protocol . '://' . $this->_hostname . '/' . $this->_version . '/' . $endpoint . '?api_key=' . $this->_apiKey;
        if($params){
            error_log(json_encode($params));
            $url .= '&' . http_build_query($params);
        }
        error_log($url);
        return $url;
    }

    /**
     * Get the connection
     * @return boolean
     */
    protected function getConnection()
    {
        $this->conn = curl_init();
        return is_resource($this->conn);
    }

    /**
     * Close the connection
     */
    protected function closeConnection()
    {
        curl_close($this->conn);
    }

    /**
     * Run the
     * @param string $method API method to run
     * @param array $params Parameters array
     * @return array Results
     */
    protected function run($endpoint, $params = array())
    {
        if (!is_resource($this->conn)) {
            if (!$this->getConnection()) return $this->failure('Can not initialize connection');
        }
        $request_url = $this->buildUri($endpoint, $params);
        curl_setopt($this->conn, CURLOPT_URL, $request_url);  // URL to post to
        curl_setopt($this->conn, CURLOPT_RETURNTRANSFER, 1);   // return into a variable
        $headers = array('Content-type: application/json', 'Authorization: Bearer ' . $this->_accessToken);
        curl_setopt($this->conn, CURLOPT_HTTPHEADER, $headers); // custom headers
        curl_setopt($this->conn, CURLOPT_HEADER, false);     // return into a variable
//    $postBody = (!empty($params))? json_encode($params) : "{}";
//    curl_setopt($this->conn, CURLOPT_POSTFIELDS,  $postBody);
        curl_setopt_array($this->conn, $this->_connectionOptions);  // (optional) additional options

        $result = curl_exec($this->conn);
        if ($result === false) return $this->failure('Curl Error: ' . curl_error($this->conn));
        $responseCode = curl_getinfo($this->conn, CURLINFO_HTTP_CODE);
        if (!self::successfulHttpResponse($responseCode)) {
            return $this->failure('Error [' . $responseCode . ']: ' . $result);
        }

        $this->closeConnection();

        $parsedResult = json_decode($result, true);
        $jsonErr = json_last_error();
        if ($parsedResult === null && $jsonErr !== JSON_ERROR_NONE) return $this->failure("Error [$jsonErr] parsing result JSON");

        if (!isset($parsedResult["data"])) {
            return $this->success($parsedResult);
        }
        $status = $parsedResult['status'];
        if ($status != self::SM_STATUS_SUCCESS) {
            return $this->failure("API Error: Status [$status:" . self::explainStatusCode($status) . '].  Message [' . $parsedResult["errmsg"] . ']');
        }
        else {
            return $parsedResult;
        }
    }


    /**
     * Return an error
     * @param string $msg Error message
     * @return array Result
     */
    protected function failure($msg)
    {
        return array(
            'success' => false,
            'message' => $msg
        );
    }

    /**
     * Return a success with data
     * @param string $data Payload
     * @return array Result
     */
    protected function success($data)
    {
        return array(
            'success' => true,
            'data' => $data
        );
    }


    /***************************
     * SurveyMonkey API methods
     ***************************/

    //survey methods

    /**
     * Retrieves a list of surveys in a user's account.
     * @param array $params optional request array
     * @return array Result
     */
    public function getSurveyList($params = array())
    {
        return $this->run('surveys', $params);
    }

    /**
     * Retrieves a list of collectors for a survey in a user's account.
     * @param string $surveyId Survey ID
     * @param array $params optional request array
     * @return array Results
     */
    public function getCollectorList($surveyId, $params = array())
    {
        return $this->run('surveys/' . $surveyId . '/collectors');
    }

    /**
     * Retrieve collector data
     * @param $collectorId
     * @return array
     */
    public function getCollector($collectorId, $params = array())
    {
        return $this->run('collectors/' . $collectorId);
    }

    /**
     * Retrieve responses for a given collector
     * @param $collectorId
     * @param array $params
     * @return array
     */
    public function getCollectorResponses($collectorId, $params = array())
    {
        return $this->run('collectors/'.$collectorId.'/responses/bulk', $params);

    }
}

/**
 * A basic class for SurveyMonkey Exceptions.
 * @package php-surveymonkey
 * @subpackage exception
 */
class SurveyMonkey_Exception extends Exception
{
}