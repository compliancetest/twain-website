<?php
require_once(THE_FUNCTION . '/aws/sdk/aws-autoloader.php');
use Aws\Sqs\SqsClient;

class SqsWrapper{

    private $_client;

    private $_queueName;

    public function __construct()
    {
        $this->_client = SqsClient::factory(array(
            'key' => get_option('aws_s3_key'),
            'secret' => get_option('aws_s3_secret'),
            'region' => 'ap-southeast-2'
        ));
        $this->_queueName = get_option( 'sqs_queue_name' );
        $this->_bulkQueueName = get_option( 'bulk_sqs_queue_name' );
        if( empty( $this->_bulkQueueName ) ) $this->_bulkQueueName = $this->_queueName;
    }

    public function sendMessage( $message, $is_bulk = false, $delay = 0 ){
        $queueName = $is_bulk ? $this->_bulkQueueName : $this->_queueName;
        $url = $this->_client->getQueueUrl(array(
            'QueueName' => $queueName,
        ));
        $message['correlationID'] = $url->getPath( 'ResponseMetadata/RequestId' );
        try{
            if( $delay !== 0 ) {
                sleep(5);
            }
            $this->_client->sendMessage(array(
                'QueueUrl'    => $url->get( 'QueueUrl' ),
                'MessageBody' => json_encode( $message ),
                'DelaySeconds' => $delay
            ));

        } catch( Exception $e ){
            return false;
        }
        return true;
    }
}
