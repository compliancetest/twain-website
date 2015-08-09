<?php
/**
 * @author Ivan Solowjew
 * @date: 7/10/14
 */

require 'lib/XeroOAuth.php';

class CT_Xero {

    const APP_TYPE = 'Private';

    const OAUTH_CALLBACK = 'oob';

    const USER_AGENT = 'ComplianceTest Pty Ltd';

    public function __construct(){

        $consumerKey    = get_option( 'xero_consumer_key' );
        $consumerSecret = get_option( 'xero_consumer_secret' );
        $publicKey      = get_option( 'xero_public_key_file' );
        $privateKey     = get_option( 'xero_private_key_file' );

        if( ! $consumerKey || ! $consumerSecret || ! $privateKey || ! $publicKey ){
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
                    'rsa_private_key' => $privateKey,
                    'rsa_public_key'  => $publicKey
                )
        ) );
        $this->xero->config['access_token']        = $this->xero->config ['consumer_key'];
        $this->xero->config['access_token_secret'] = $this->xero->config ['shared_secret'];
        $this->xero->config['session_handle']      = $this->_oauthSession['oauth_session_handle'];
    }

    /**
     * Function used to update all local items with data from Xero API.
     * @return bool
     */
    public function updateItems(){
        global $wpdb;
        $this->xero->request('GET', $this->xero->url('Items', 'core'), array( 'order' => 'code asc'));
        
        $invoices = $this->responseToArray();
        if( isset( $invoices['Items']['Item'] ) && is_array( $invoices['Items']['Item'] ) ){
            $wpdb->query("TRUNCATE wp_xeroitems");
            if( ! isset( $invoices['Items']['Item'][0] ) ){
                $temp = $invoices['Items']['Item'];
                $invoices['Items']['Item'] = array();
                $invoices['Items']['Item'][0] = $temp;
            }
            foreach( $invoices['Items']['Item'] AS $item ){
                $wpdb->replace( "wp_xeroitems",
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
        
        foreach($accounts['Accounts']['Account'] as $account)
        {         
            $wpdb->insert($wpdb->prefix . "xero_accounts", $account);
        }
        return true;
        
    }

    /**
     * Function used to add / update Xero Contact
     * @param $contactData Array with CT Organisation data
     * @return array|string
     */
    public function upsertContact( $contactData ){
        if( count( $contactData ) < 2 ){
            return 'Some required fields missed or empty';
        }
        $xml = new SimpleXMLElement( '<Contact></Contact>' );
        $xeroData = array(
            'Name'                      => $contactData['organisation_name'],
            'FirstName'                 => $contactData['contact_first_name'],
            'LastName'                  => $contactData['contact_last_name'],
            'Website'                   => $contactData['organisation_website'],
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
                                    'AddressLine1' => $contactData['billing_address1'],
                                    'AddressLine2' => $contactData['billing_address2'],
                                    'AddressLine3' => $contactData['billing_address3'],
                                    'AddressLine4' => $contactData['billing_address4'],
                                    'City'         => $contactData['billing_city'],
                                    'Region'       => $contactData['billing_state'],
                                    'PostalCode'   => $contactData['billing_postcode'],
                                    'Country'      => $contactData['billing_country']
                                )
                            )
        );
        if( ! empty( $contactData['contact_first_name']) && ! empty( $contactData['contact_last_name'] ) && ! empty( $contactData['contact_email'] ) ){
            if( ! empty( $contactData['secondary_contact_first_name']) && ! empty( $contactData['secondary_contact_last_name'] ) && ! empty( $contactData['secondary_contact_email'] ) ){
                $xeroData['ContactPersons']['ContactPerson'] = array(
                    'FirstName'        => $contactData['secondary_contact_first_name'],
                    'LastName'         => $contactData['secondary_contact_last_name'],
                    'EmailAddress'     => $contactData['secondary_contact_email'],
                    'IncludeInEmails'  => true
                );

            } else {
                $xeroData['ContactPersons']['ContactPerson'] = array(
                    'FirstName'        => '',
                );
            }
        }
        if( isset( $contactData['contact_id'] ) && ! empty( $contactData['contact_id'] ) ) $xeroData['ContactID'] = $contactData['contact_id'];
        $this->array2xml( $xeroData, $xml);
        $xml = '<Contacts>'.str_replace('<?xml version="1.0"?>', '', $xml->asXML()).'</Contacts>';
        $this->xero->request('POST', $this->xero->url('Contacts', 'core'), array(), $xml);
        if ( $this->xero->response['code'] == 200 ) {
            return $this->responseToArray();
        }
        return '<pre>'. htmlentities($this->xero->response['response']). '</pre>';
    }

    /**
     * Used to get Contact( if $countactID variable defined ) / All Contacts
     * @param bool|string $countactID
     * @return bool
     */
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

    public function upsertInvoice( $invoiceData, $paymentType = 1 ){
        global $wpdb;
        $payment_method = $wpdb->get_var($wpdb->prepare("SELECT invoice_me FROM {$wpdb->prefix}organisations_payment_methods WHERE id = %s", $invoiceData['payment_id']) );
        $requiredFields = array( 'organisation_id', 'item_code', 'quantity' );
        foreach( $requiredFields AS $requiredField ){
            if( ! isset( $invoiceData[$requiredField] ) || empty( $invoiceData[$requiredField] ) ){
                return 'Some required fields missed or empty';
            }
        }
        $xml = new SimpleXMLElement( '<Invoice></Invoice>' );
        $xml->addChild( 'Type', 'ACCREC' );
        $payment_method_reference = $wpdb->get_var($wpdb->prepare("SELECT customer_reference FROM {$wpdb->prefix}organisations_payment_methods WHERE id = %s", $invoiceData['payment_id']) );
        if( $payment_method_reference ){
            $xml->addChild( 'Reference', $payment_method_reference );
        }
        $contact = $xml->addChild( 'Contact' );
        $contact->addChild( 'ContactID', $wpdb->get_var( $wpdb->prepare("SELECT contact_id FROM {$wpdb->prefix}organisations WHERE id = %d", $invoiceData['organisation_id']) ) );
        $xml->addChild( 'Date', date('Y-m-d') );
        $xml->addChild( 'DueDate', date('Y-m-d', $payment_method == 0 ? strtotime('+1 day') : strtotime('+30 days') ) );
        $xml->addChild( 'LineAmountTypes', 'Inclusive' );
        $xml->addChild( 'CurrencyCode', 'AUD' );
        $line_items = $xml->addChild( 'LineItems' );
        /**
         * Get organisation charge table entries for current organisation with '$paymentType' payment type
         */
        if( get_option('invoice_in_arrears') == 'yes' ) {
            $charge_entries = $wpdb->get_results($wpdb->prepare("SELECT * FROM wp_organisations_charge
                                                                WHERE organisation_id = %d AND payment_id IN( SELECT id FROM wp_organisations_payment_methods WHERE organisation_id = %d AND id = %d AND status = 'Active' )
                                                                AND invoice_number = '' AND  YEAR(start_date) <= YEAR(CURRENT_DATE - INTERVAL 1 MONTH) AND MONTH(start_date) <= MONTH(CURRENT_DATE - INTERVAL 1 MONTH)", $invoiceData['organisation_id'], $invoiceData['organisation_id'], $invoiceData['payment_id']), ARRAY_A);
        } else{
            $charge_entries = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}organisations_charge WHERE organisation_id = %d AND payment_id IN( SELECT id FROM {$wpdb->prefix}organisations_payment_methods WHERE organisation_id = %d AND id = %d AND status = 'Active' ) AND invoice_number = ''", $invoiceData['organisation_id'], $invoiceData['organisation_id'], $invoiceData['payment_id']), ARRAY_A);
        }
        if( $charge_entries ){
            foreach( $charge_entries AS $entry ){
                $entry['comment'] = $wpdb->get_var($wpdb->prepare("SELECT description FROM {$wpdb->prefix}xeroitems WHERE code = %s", $entry['item_code'])).PHP_EOL.( ! empty( $entry['comment'] ) ? '( '.$entry['comment'].' )' : '' );
                $line_item = $line_items->addChild( 'LineItem' );
                $line_item->addChild( 'ItemCode', $entry['item_code'] );
                $line_item->addChild( 'Quantity', $entry['quantity'] );
                $line_item->addChild( 'Description', $entry['comment'] );
                if( $entry['discount'] > 0 ) $line_item->addChild( 'DiscountRate', $entry['discount'] );

                $suiteFamilyMark = $wpdb->get_var($wpdb->prepare("SELECT suite_family_mark FROM wp_organisations_subscriptions WHERE id = %d", $invoiceData['reference_id']));
                $community_id = get_post_meta($suiteFamilyMark, 'community_id', true);
                $group = groups_get_group(array('group_id' => $community_id));
                $this->_addTrackingCategoryToLineItem($line_item, bp_get_group_name($group));
            }
        }
        if( isset( $invoiceData['invoice_number'] ) && ! empty( $invoiceData['invoice_number'] ) ) $xml->addChild('InvoiceNumber', $invoiceData['invoice_number'] );
        $this->xero->request( 'POST', $this->xero->url('Invoices', 'core'), array(), str_replace( '<?xml version="1.0"?>', '', $xml->asXML() ) );
        if ($this->xero->response['code'] == 200) {
            return  $this->responseToArray();
        } else{
            return array(
                'error' => $this->responseToArray()
            );
        }
        return false;
    }

    public function getTrackingCategory($categoryName)
    {
        //tracking category with name 'Line' is hardcoded for now
        $this->xero->request('GET', $this->xero->url('trackingcategories/Line', 'core'), array() );
        if ($this->xero->response['code'] == 200) {
            $response = $this->responseToArray();
            if (isset($response['TrackingCategories']['TrackingCategory'])) {
                $trackingCategory = $response['TrackingCategories']['TrackingCategory'];
                if (isset($trackingCategory['Options']['Option'])) {
                    foreach ($trackingCategory['Options']['Option'] as $key => $option) {
                        if ($option['Name'] != $categoryName) {
                            unset($trackingCategory['Options']['Option'][$key]);
                        }
                    }
                }
                return $trackingCategory;
            }

        } else{
            return array(
                'error' => $this->responseToArray()
            );
        }
    }

    private function _addTrackingCategoryToLineItem(&$lineItem, $communityName)
    {
        $trackingCategory = $this->getTrackingCategory($communityName);
        if (!isset($trackingCategory['error'])) {
            $tracking = $lineItem->addChild('Tracking');
            $trackingCat = $tracking->addChild('TrackingCategory');

            $optionValues = reset($trackingCategory['Options']['Option']);

            $trackingCat->addChild('TrackingCategoryID', $trackingCategory['TrackingCategoryID']);
            $trackingCat->addChild('Name', $trackingCategory['Name']);
            $trackingCat->addChild('Option', $optionValues['Name']);
        }
    }
    public function createPayment( $paymentData ){
        $xml = new SimpleXMLElement( '<Payment></Payment>' );
        $invoice = $xml->addChild( 'Invoice' );
        $invoice->addChild( 'InvoiceNumber', $paymentData['InvoiceNumber'] );
        $account = $xml->addChild( 'Account' );
        $account->addChild( 'Code', 650 );
        $xml->addChild( 'Date', $paymentData['Date'] );
        $xml->addChild( 'Amount', $paymentData['Amount'] );
        if( ! empty( $paymentData['Reference'] ) ){
            $xml->addChild( 'Reference', $paymentData['Reference'] );
        }
        $this->xero->request( 'POST', $this->xero->url('Payments', 'core'), array(), str_replace( '<?xml version="1.0"?>', '', $xml->asXML() ) );
        if ($this->xero->response['code'] == 200) {
            return  $this->responseToArray();
        }
        return false;
    }

    public function getInvoice( $invoiceId = false ){
        if( $invoiceId ){
            $this->xero->request('GET', $this->xero->url('Invoices/'.$invoiceId, 'core'), array() );
        } else {
            $this->xero->request('GET', $this->xero->url('Invoices', 'core'), array() );
        }
        if ($this->xero->response['code'] == 200) {
            return  $this->responseToArray();
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