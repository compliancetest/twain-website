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

if(!is_user_logged_in())
{
    addMessage('Please login to see the xml', 'error');
    wp_redirect('/');
    exit;
}

$esb = new ManageESB();

$message = $esb->getMessageEnvelope($id);

if(!$message){
    echo '<p>Permission Denied!</p>';
    exit;
}

header("Content-type: application/xml");
if($mode != 'html'){    
    echo $message;
}else{
    $xslt = get_site_url() . '/message-envelope.xsl';
    $message = str_replace('?>', '?><?xml-stylesheet type="text/xsl" href="' . $xslt . '"?>', $message);
    echo $message;
}

