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
echo "<?xml version='1.0' encoding='utf-8'?>";
if($mode == 'html'){
    echo '<?xml-stylesheet type="text/xsl" href="' . $xslt . '"?>';
}
echo $message;

