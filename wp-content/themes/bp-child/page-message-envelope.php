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

$message_content = ($message->FLAG == 'IS_EMPTY' || !$message->S3_PAYLOAD_LOCATION) ? $message->PAYLOAD : ct_read_xml_from_amazon_s3($message->S3_PAYLOAD_LOCATION);

if($mode != 'html'){    
    header("Content-type: application/xml");    
    echo $message_content;
}else{
    if(0 && $message->S3_PAYLOAD_CONTENT_LENGTH > get_option('s3_xml_max_size'))
    {
        if( isset($_GET['download'])) {
            //Download File
            header("Expires: Mon, 26 Nov 1962 00:00:00 GMT");
            header("Last-Modified: " . gmdate("D,d M Y H:i:s") . " GMT");
            header("Cache-Control: no-cache, must-revalidate");
            header("Pragma: no-cache");
            header("Content-Type: Application/octet-stream");
            header("Content-disposition: attachment; filename=xml_" . $id . ".xml");
            
            echo $message_content;
            exit;
        }
        //Display Error Message
        get_header();
?>
            <div class="content container"><!-- Start Content Container-->        
                
                <div class="content_inner column">
                    HTML view is not available due to content size of <?php echo size_format($message->S3_PAYLOAD_CONTENT_LENGTH) ?>. Please download and process locally.
                    Click <a href="<?php echo get_site_url()?>/message-envelope/?id=<?php echo $id?>&mode=html&download=1">here</a> to download.
                </div>
                    
                <div class="clear"></div>
                
            </div> <!--End content container-->    
<?php
        get_footer();

    }else{
        header("Content-type: application/xml");
        
        $xslt = get_site_url() . '/xslt/message-envelope.xsl';
        $message_content = str_replace('?>', '?><?xml-stylesheet type="text/xsl" href="' . $xslt . '"?>', $message_content);
        echo $message_content;
    }
    
}
