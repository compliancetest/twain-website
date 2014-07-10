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

    protected function responseToArray(){
        return json_decode( json_encode( $this->xero->parseResponse($this->xero->response['response'], 'xml' ) ), 1 );
    }


}