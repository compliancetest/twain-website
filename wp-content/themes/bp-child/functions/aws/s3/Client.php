<?
require_once(THE_FUNCTION . '/aws/aws.phar');
use Aws\S3\S3Client;

class S3Wrapper{

    private $_client;

    public function __construct(){
        $this->_bucket = get_option( 'aws_s3_url' );
        $this->_client = S3Client::factory(array(
            'key'    => get_option( 'aws_s3_key' ),
            'secret' => get_option( 'aws_s3_secret' ),
            'region' => 'ap-southeast-2'
        ));
    }

    /**
     * @param $filename - path to file + file name
     * @param $content - file content
     * @return bool
     */
    public function putObject( $filename, $content, $contentType = 'application/json' ){
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
    public static function getProductClaimLink( $token ){
        return self::getLink( 'claims/products/' . $token.'.pdf');
    }

    public static function getLink( $filepath ){
        return 'https://s3-ap-southeast-2.amazonaws.com/'.get_option( 'aws_s3_url' ).'/'.$filepath;
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
}