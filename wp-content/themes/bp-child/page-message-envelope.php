<?php
  /***
  * Template Name: Message Envelope
  */

$mode = isset($_GET['mode']) ? $_GET['mode'] : 'xml';

$id = isset($_GET['id']) ? $_GET['id'] : null;

if(!$id){
    echo '<p>Invalid Request!</p>';
    exit;
}

$esb = new ManageESB();

$message = $esb->getMessageEnvelope($id);

if(!$message){
    echo '<p>Invalid Request!</p>';
    exit;
}
header("Content-type: application/xml");
if($mode != 'html'){
    
    echo $message;
}else{
    $xslt = 'http://lc.compliancetest.com/message-envelope.xsl';
    $message = str_replace('?>', '?><?xml-stylesheet type="text/xsl" href="' . $xslt . '"?>', $message);
    echo $message;
}

