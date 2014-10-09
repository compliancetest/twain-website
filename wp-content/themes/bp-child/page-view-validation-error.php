<?php
/**
* Template Name: View Validation Error
*/

$mode = isset($_GET['mode']) ? $_GET['mode'] : 'xml';

$id = isset($_GET['id']) ? $_GET['id'] : null;

if(!$id){
    echo '<p>Invalid Request!</p>';
    exit;
}

$esb = new ManageESB();

$message = $esb->getValidationError($id);

if(!$message){
    echo '<p>Invalid Request!</p>';
    exit;
}
header("Content-type: application/xml");
$xslt = get_site_url() . '/xslt/validation-result.xsl';

if($mode == 'html'){
    if(strpos($message, '?>') === false)
        echo '<?xml-stylesheet type="text/xsl" href="' . $xslt . '"?>';
    else
        $message = str_replace('?>', '?><?xml-stylesheet type="text/xsl" href="' . $xslt . '"?>', $message);
    
}
echo $message;

