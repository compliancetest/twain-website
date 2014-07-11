<?php
/**
 * @author Ivan Solowjew
 * @date: 7/10/14
 */

require 'lib/XeroOAuth.php';

class CT_Xero {

    const APP_TYPE = 'Private';

    const OAUTH_CALLBACK = 'oob';

    const USER_AGENT = 'ComplianceTest Pty Ltd (Test)';

    public function __construct(){

        $consumerKey    = get_option( 'xero_consumer_key' );
        $consumerSecret = get_option( 'xero_consumer_secret' );
        
        if( ! $consumerKey || ! $consumerSecret ){
            throw new \Exception( 'Consumer key OR secret not setted up correctly! ');
        }
        $this->xero = new XeroOAuth ( array_merge ( array (
                    'application_type' => self::APP_TYPE,
                    'oauth_callback'   => self::OAUTH_CALLBACK,
                    'user_agent'       => self::USER_AGENT
                ), array (
                    'consumer_key'    => $consumerKey,
                    'shared_secret'   => $consumerSecret,
                    // API versions
                    'core_version'    => '2.0',
                    'payroll_version' => '1.0',
                    'rsa_private_key' => dirname(__FILE__).'/certs/privatekey.pem',
                    'rsa_public_key'  => dirname(__FILE__).'/certs/publickey.cer'
                )
        ) );
        $this->xero->config['access_token']        = $this->xero->config ['consumer_key'];
        $this->xero->config['access_token_secret'] = $this->xero->config ['shared_secret'];
        $this->xero->config['session_handle']      = $this->_oauthSession['oauth_session_handle'];
    }

    /**
     * Function used to update all local item with data from Xero API.
     * @return bool
     */
    public function updateItems(){
        global $wpdb;
        $this->xero->request('GET', $this->xero->url('Items', 'core'), array( 'order' => 'code asc'));
        
        $invoices = $this->responseToArray();
        if( isset( $invoices['Items']['Item'] ) && is_array( $invoices['Items']['Item'] ) ){
            $wpdb->query("TRUNCATE {$wpdb->prefix}xeroitems");
            foreach( $invoices['Items']['Item'] AS $item ){
                $wpdb->replace( "{$wpdb->prefix}xeroitems",
                    array(
                        'id'           => $item['ItemID'],
                        'code'         => $item['Code'],
                        'description'  => $item['Description'],
                        'unit_price'   => $item['SalesDetails']['UnitPrice'],
                        'account_code' => $item['SalesDetails']['AccountCode'],
                    ),
                    array(
                        '%s', '%s', '%s', '%d', '%d'
                    )
                );
            }
        }
        return true;
    }

    /**
     * Function used to add OR update Xero item.
     * If $xeroItem['code'] exists, then item updates, otherwise we create new item
     * @param $xeroItem - Item data
     * @return array|bool - Array with updated data on success and false on error
     */
    public function addXeroItem( $xeroItem ){
        $xml = "<Item>";
        if( isset( $xeroItem['id'] ) ) $xml.= "<ItemID>{$xeroItem['id']}</ItemID>";
        $xml.=   "<Code>{$xeroItem['code']}</Code>
                  <Description>{$xeroItem['description']}</Description>
                  <SalesDetails>
                    <UnitPrice>{$xeroItem['unit_price']}.0000</UnitPrice>
                    <AccountCode>{$xeroItem['account_code']}</AccountCode>
                  </SalesDetails>
                </Item>";
        $this->xero->request('POST', $this->xero->url('Items', 'core'), array(), $xml);
        if ( $this->xero->response['code'] == 200 ) {
            return $this->responseToArray();
        }
        return false;
    }
    
    public function updateAccounts()
    {
        global $wpdb;
        
        $this->xero->request('GET', $this->xero->url('Accounts', 'core'));
        
        $accounts = $this->responseToArray();
        
        //Remove All Data
        $wpdb->query("DELETE FROM {$wpdb->prefix}xero_accounts");
        
        foreach($accounts['Accounts'] as $account)
        {         
            $wpdb->insert($wpdb->prefix . "xero_accounts", $account);
            var_dump($account);
            echo $wpdb->last_error;
        }
        exit;
        return true;
        
    }

    public function upsertContact( $contactData ){
        $requiredFields = array( 'organisation_name', 'contact_first_name', 'contact_last_name', 'contact_email', 'abn', 'phonenumber', 'phonenumber_areacode', 'phonenumber_countrycode', 'billing_address_attention', 'billing_address', 'billing_city', 'billing_state', 'billing_postcode', 'billing_country' );
        foreach( $requiredFields AS $requiredField ){
            if( ! isset( $contactData[$requiredField] ) || empty( $contactData[$requiredField] ) ){
                return 'Some required fields missed or empty';
            }
        }
        $xml = new SimpleXMLElement( '<Contact></Contact>' );
        $xeroData = array(
            'Name'                      => $contactData['organisation_name'],
            'FirstName'                 => $contactData['contact_first_name'],
            'LastName'                  => $contactData['contact_last_name'],
            'EmailAddress'              => $contactData['contact_email'],
            'TaxNumber'                 => $contactData['abn'],
            'AccountsReceivableTaxType' => 'OUTPUT',
            'Phones' => array(
                            'Phone' => array(
                                'PhoneType'        => 'DEFAULT',
                                'PhoneNumber'      => $contactData['phonenumber'],
                                'PhoneAreaCode'    => $contactData['phonenumber_areacode'],
                                'PhoneCountryCode' => $contactData['phonenumber_countrycode']
                            )
                        ),
            'Addresses' => array(
                                'Address' => array(
                                    'AddressType'  => 'POBOX',
                                    'AttentionTo'  => $contactData['billing_address_attention'],
                                    'AddressLine1' => $contactData['billing_address'],
                                    'City'         => $contactData['billing_city'],
                                    'Region'       => $contactData['billing_state'],
                                    'PostalCode'   => $contactData['billing_postcode'],
                                    'Country'      => $contactData['billing_country']
                                )
                            )
        );
        if( isset( $contactData['contact_id'] ) && ! empty( $contactData['contact_id'] ) ) $xeroData['ContactID'] = $contactData['contact_id'];
        $this->array2xml( $xeroData, $xml);
        $xml = '<Contacts>'.str_replace('<?xml version="1.0"?>', '', $xml->asXML()).'</Contacts>';
        $this->xero->request('POST', $this->xero->url('Contacts', 'core'), array(), $xml);
//        var_dump($contactData);
//        var_dump($xeroData);
//        echo ($xml);
//        var_dump($this->xero->response);die;
        if ( $this->xero->response['code'] == 200 ) {
            return $this->responseToArray();
        }
        return $this->xero->response;
    }

    public function getContacts( $countactID = false ){
        $where = array();
        if( $countactID ) $where = array( 'ContactID' => $countactID );
        $this->xero->request('GET', $this->xero->url('Contacts', 'core'), $where );
        $response = $this->responseToArray();
        if( isset( $response['Contacts']['Contact'] ) && is_array( $response['Contacts']['Contact'] ) ){
            return $response['Contacts']['Contact'];
        }
        return false;
    }

    /**
     * @param $data Array with Contact details
     * @param $response - Xml with Contact details
     */
    protected function array2xml( $data, &$response ){
        foreach($data as $key => $value) {
            if(is_array($value)) {
                if(!is_numeric($key)){
                    $subnode = $response->addChild("$key");
                    $this->array2xml($value, $subnode);
                }
                else{
                    $subnode = $response->addChild("item$key");
                    $this->array2xml($value, $subnode);
                }
            }
            else {
                $response->addChild("$key",htmlspecialchars("$value"));
            }
        }
    }
    /**
     * Function used to transform XML response to array
     * @return array
     */
    protected function responseToArray(){
        return json_decode( json_encode( $this->xero->parseResponse($this->xero->response['response'], 'xml' ) ), 1 );
    }


}