<?php
require_once(THE_FUNCTION . '/aws/sdk/aws-autoloader.php');
use Aws\Ec2\Ec2Client;

class Ec2Wrapper{

    private $_client;

    public function __construct()
    {
        $this->_client = Ec2Client::factory(array(
            'key'    => get_option('aws_s3_key'),
            'secret' => get_option('aws_s3_secret'),
            'region' => 'ap-southeast-2'
        ));
    }

    public function changeStatus( $action, $servers )
    {
        try{
            if (in_array($action, array('start', 'stop'))) {
                $functionName = $action . 'Instances';
            }
            $result = $this->_client->$functionName(array(
                'InstanceIds' => $servers,
            ));

        } catch( Exception $e ){
            return array('status' => 'error', 'message' => $e->getMessage());
        }
        return array('status' => 'success', 'message' => $result->toArray());
    }

    public function assignIp( $instanceId, $ip )
    {
        try{
            $result = $this->_client->associateAddress(array(
                'InstanceId' => $instanceId,
                'PublicIp'   => $ip
            ));

        } catch( Exception $e ){
            return array('status' => 'error', 'message' => $e->getMessage());
        }
        return array('status' => 'success', 'message' => $result->toArray());
    }
}
