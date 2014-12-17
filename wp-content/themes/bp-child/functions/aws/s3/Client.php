<?php
require_once(THE_FUNCTION . '/aws/sdk/aws-autoloader.php');
use Aws\S3\S3Client;

class S3Wrapper{

    private $_client;

    public function __construct(){
        $this->_bucket = get_option( 'aws_s3_url' );
        $this->_client = S3Client::factory(array(
            'key'    => get_option( 'aws_s3_key' ),
            'secret' => get_option( 'aws_s3_secret' ),
            'region' => 'ap-southeast-2',
            'signature' => 'v4'
        ));
    }

    /**
     * @param $filename - path to file + file name
     * @param $content - file content
     * @return bool
     */
    public function putObject( $filename, $content, $contentType = 'application/json' ){
        if( empty( $content ) ){
            return false;
        }
        // Upload data.
        $result =  $this->_client->putObject(array(
            'Bucket'       => $this->_bucket,
            'Key'          => $filename,
            'Body'         => $content,
            'ContentType'  => $contentType,
            'Metadata'     => array(
                'size' => strlen( $content ),
            )
        ));
        if( isset( $result['ObjectURL'] ) && ! empty( $result['ObjectURL'] ) ) return true;
        return false;
    }

    /**
     * Use this function to get file object
     * @param $path
     * @return bool|null
     */
    public function getObject( $path ){
        try {
            $result = $this->_client->getObject(array(
                'Bucket' => $this->_bucket,
                'Key'    => $path
            ));
        } catch( Exception $e ){
            return false;
        }
        if (get_class($result['Body']) == 'Guzzle\Http\EntityBody') return $result['Body'];
        return false;
    }

    public static function isObjectExists( $key ){
        $s3 = new S3Wrapper();
        return $s3->_client->if_object_exists( $s3->_bucket, $key );
    }
    /**
     * Use this function to delete file object
     * @param $path
     * @return bool|null
     */
    public function deleteObject( $path ){
        return $this->_client->deleteObject(array(
            'Bucket' => $this->_bucket,
            'Key'    => $path
        ));
    }

    /**
     * @param $token - profile token from profiles table
     * @param bool $returnJson - set this flag to true if you want to get data as JSON
     * @return array|bool|mixed|null
     */
    public static function getProfile( $token, $returnJson = false ){
        $s3 = new S3Wrapper();
        if( $returnJson ) return $s3->getObject( 'profiles/user/' . $token.'.json' );
        return json_decode( $s3->getObject( 'profiles/user/' . $token.'.json' ) );
    }

    /**
     * @param $token - profile token
     * @return array|bool|mixed|null
     */
    public static function getProfileLink( $token, $isDownloadLink = false ){
        if( $isDownloadLink ){
            $profile = self::getProfile( $token );
            $v = '';
            if( $profile->Profile->Version ){
                $version = array();
                foreach( get_object_vars( $profile->Profile->Version ) AS $k => $v ){
                    $version[] = $v;
                }
                $v = " v" . implode(".", $version);
            }
            return self::getDownloadLink( 'profiles/user', $token.'.json', $profile->Profile->Title.$v.'.json' );
        }
        return self::getLink( 'profiles/user', $token.'.json' );
    }

    /*
     * Products Claims section
     */

    /**
     * @param $token - claim token from wp_compliance_claims table
     * @return array|bool|mixed|null
     */
    public static function getProductClaim( $token ){
        $s3 = new S3Wrapper();
        return $s3->getObject( 'claims/products/' . $token.'.pdf' );
    }

    /**
     * @param $token - claim token from wp_compliance_claims table
     * @return array|bool|mixed|null
     */
    public static function getProductClaimLink( $token, $isDownloadLink = false ){
        if( $isDownloadLink ){
            return self::getDownloadLink( 'claims/products', $token.'.pdf' );
        }
        return self::getLink( 'claims/products', $token.'.pdf' );
    }

    /*
     * Agreements section
     */
    /**
     * @param $token - claim token from wp_compliance_claims table
     * @return array|bool|mixed|null
     */
    public static function getAgreementClaim( $token ){
        $s3 = new S3Wrapper();
        return $s3->getObject( 'claims/agreements/' . $token.'.pdf' );
    }

    /**
     * @param $token - claim token from wp_e2e_agreement table
     * @return array|bool|mixed|null
     */
    public static function getAgreementClaimLink( $token, $isDownloadLink = false ){
        if( $isDownloadLink ){
            return self::getDownloadLink( 'claims/agreements', $token.'.pdf' );
        }
        return self::getLink( 'claims/agreements', $token.'.pdf' );
    }

    /*
     * Support Tickets / Agreements logs section
     */

    /**
     * @param $token - attachment token
     * @return array|bool|mixed|null
     */
    public static function getAttachment( $token, $ext, $type = 'tickets' ){
        $s3 = new S3Wrapper();
        return $s3->getObject( 'attachments/'.$type . $token.'.'.$ext );
    }

    /**
     * @param $token - claim token from wp_compliance_claims table
     * @return array|bool|mixed|null
     */
    public static function getAttachmentLink( $token, $fileName, $type = 'tickets', $isDownloadLink = false ){
        if( $isDownloadLink ){
            return self::getDownloadLink( 'attachments/'.$type, $token.'/'.$fileName, $fileName );
        }
        return self::getLink( 'attachments/'.$type, $token.'/'.$fileName );
    }

    /**
     * @param $token - token
     * @return array|bool|mixed|null
     */
    public static function getDownloadAttachmentLink( $token, $isDownloadLink = false, $fileName ){
        if( $isDownloadLink ){
            return self::getDownloadLink( 'attachments/downloads', $token.'.'. pathinfo( $fileName, PATHINFO_EXTENSION ), $fileName );
        }
        return self::getLink( 'attachments/downloads', $token.'.'.pathinfo( $fileName, PATHINFO_EXTENSION ) );
    }

    public static function getLink( $bucket, $fileName ){
        $s3 = new S3Wrapper();
        return urldecode( $s3->_client->getObjectUrl( get_option( 'aws_s3_url' ).'/'.$bucket, $fileName ) );
    }

    public static function getDownloadLink( $bucket, $fileName, $name = false ){
        $s3 = new S3Wrapper();
        $name = $name ? $name : $fileName;
        $command = $s3->_client->getCommand('GetObject', array(
            'Bucket' => get_option( 'aws_s3_url' ),
            'Key'    => $bucket.'/'.$fileName,
            'ResponseContentDisposition' => 'attachment; filename="'.$name.'"'
        ));
        return ( $command->createPresignedUrl('+1 hour') );
    }

}

class BlobsMigration{

    /**
     * Use this function to upload profiles to S3 from database
     * Note that existing S3 files will be overwritten
     * @return int - number of uploaded profiles
     */
    public static function uploadProfiles(){
        global $wpdb;

        $s3 = new S3Wrapper();
        $counter = 0;
        $profiles = $wpdb->get_results("SELECT * FROM wp_community_profile_instances" );
        foreach( $profiles AS $profile ){
            if( $profile->content ) {
                $s3->putObject('/profiles/user/' . $profile->token . '.json', base64_decode($profile->content), 'application/json');
                $counter++;
            }
        }
        return $counter;
    }

    /**
     ** Use this function to upload claims certificates to S3 from database
     * Note that existing S3 files will be overwritten
     * @return int - number of uploaded certificates
     */
    public static function uploadProductClaims(){
        global $wpdb;

        $s3 = new S3Wrapper();
        $counter = 0;
        $certificates = $wpdb->get_results("SELECT * FROM wp_compliance_claims" );
        foreach( $certificates AS $certificate ){
            if( $certificate->certificate ) {
                $s3->putObject('/claims/products/' . $certificate->token . '.pdf', $certificate->certificate, 'application/pdf');
                $counter++;
            }
        }
        return $counter;
    }

    /**
     ** Use this function to upload service claims certificates to S3 from database
     * Note that existing S3 files will be overwritten
     * @return int - number of uploaded certificates
     */
    public static function uploadServiceClaims(){
        global $wpdb;

        $s3 = new S3Wrapper();
        $counter = 0;
        $certificates = $wpdb->get_results("SELECT * FROM wp_e2e_agreement" );
        foreach( $certificates AS $certificate ){
            //fix empty tokens
            if( empty( $certificate->requester_token ) ){
                $wpdb->update( 'wp_e2e_agreement',
                    array( 'requester_token' => createClaimToken() ),
                    array( 'id' => $certificate->id ),
                    array( '%s' ),
                    array( '%d' )

                );
            }
            if( empty( $certificate->responder_token ) ){
                $wpdb->update( 'wp_e2e_agreement',
                    array( 'responder_token' => createClaimToken() ),
                    array( 'id' => $certificate->id ),
                    array( '%s' ),
                    array( '%d' )

                );
            }
            $certificate = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM wp_e2e_agreement WHERE id = %d", $certificate->id ) );
            if( $certificate->requester_certificate && $certificate->requester_token ) {
                //upload pdf certificate
                $s3->putObject('/claims/agreements/' . $certificate->requester_token . '.pdf', $certificate->requester_certificate, 'application/pdf');
                $counter++;
            }
            if( ! empty( $certificate->requestor_audit_log ) ){
                //upload audit log file
                $s3->putObject( '/attachments/agreements/' . $certificate->requester_token . '/'.$certificate->requestor_audit_log_name, $certificate->requestor_audit_log, $certificate->requestor_audit_log_type );
            }
            if( $certificate->responder_certificate && $certificate->responder_token ) {
                //upload pdf certificate
                $s3->putObject('/claims/agreements/' . $certificate->responder_token . '.pdf', $certificate->responder_certificate, 'application/pdf');
                $counter++;
            }
            if( ! empty( $certificate->responder_audit_log ) ){
                //upload audit log file
                $s3->putObject( '/attachments/agreements/' . $certificate->responder_token . '/'. $certificate->responder_audit_log_name, $certificate->responder_audit_log, $certificate->responder_audit_log_type );
            }
        }
        return $counter;
    }
    /**
     ** Use this function to upload support tickets attachments to S3 from database
     * Note that existing S3 files will be overwritten
     * @return int - number of uploaded attachments
     */
    public static function uploadTicketsAttachments(){
        global $wpdb;

        $s3 = new S3Wrapper();
        $counter = 0;
        $attachments = $wpdb->get_results("SELECT * FROM wp_ticket_attachments" );
        foreach( $attachments AS $attachment ){
            $file = TICKET_ATTACHMENTS_DIR . "/" . $attachment->ticket_id . "/" . $attachment->file_name;
            if( file_exists( $file ) ) {
                $ext = pathinfo( $attachment->file_name, PATHINFO_EXTENSION);
                $s3->putObject('/attachments/tickets/' . $attachment->token . '/'.$attachment->file_name, file_get_contents( $file ), 'application/'.$ext );
                $counter++;
            }
        }
        return $counter;
    }
    /**
     ** Use this function to upload download attachments to S3 from database
     * Note that existing S3 files will be overwritten
     * @return int - number of uploaded attachments
     */
    public static function uploadDownloadAttachments(){
        global $wpdb;

        $s3 = new S3Wrapper();
        $counter = 0;
        $attachments = $wpdb->get_results("SELECT * FROM wp_bp_groups_downloads" );
        foreach( $attachments AS $attachment ){
            if( ! $attachment->token ){
                $wpdb->update('wp_bp_groups_downloads',
                    array( 'token' => createClaimToken() ),
                    array( 'id'    => $attachment->id )
                );
            }
            $ext = pathinfo( $attachment->location, PATHINFO_EXTENSION );
            $s3->putObject('/attachments/downloads/' . $attachment->token . '/'.$attachment->name, $attachment->download_file, 'application/'.$ext );
            $counter++;
        }
        return $counter;
    }
}