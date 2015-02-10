<?php
require_once(THE_FUNCTION . '/aws/sdk/aws-autoloader.php');
use Aws\Sqs\SqsClient;

class SqsWrapper
{

    private $_client;

    private $_queueUrl;

    public function __construct()
    {
        $this->_client = S3Client::factory(array(
            'key' => get_option('aws_s3_key'),
            'secret' => get_option('aws_s3_secret'),
            'region' => 'ap-southeast-2'
        ));
        $this->_queueUrl = get_option( 'sqs_queue_name' );
    }

    public function sendMessage( $message ){
        $this->_client->sendMessage(array(
            'QueueUrl'    => $this->_queueUrl,
            'MessageBody' => $message
        ));
    }
}
