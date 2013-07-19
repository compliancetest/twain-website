<?php
/***
* Manage Page Print
* 
*/

add_action('template_redirect', 'cp_print_page', 1);
function cp_print_page()
{
    if(isset($_GET['is_print']))
    {
        $pageType = isset($_GET['print_type']) ? $_GET['print_type'] : 'static';
        //if static page
        if($pageType == 'static')        
        {
            require_once(THE_FUNCTION . "/print/print-static-page.php");
            exit;
        }else if($pageType == 'static-no-header'){
//            $hideHeader = true;
            require_once(THE_FUNCTION . "/print/print-static-page.php");
            exit;
        }else if($pageType == 'test-suite'){
            require_once(THE_FUNCTION . "/print/print-test-suite.php");
            exit;
        }else if($pageType == 'test-case'){
            require_once(THE_FUNCTION . "/print/print-test-case.php");
            exit;
        }else if($pageType == 'wiki'){
            require_once(THE_FUNCTION . "/print/print-wiki.php");
            exit;
        }
    }
}

function addPrintParams($baseURL, $type)
{
    if(strpos($baseURL, "?") === false)
    {
        $baseURL .= "?is_print=1&print_type=" . $type;
    }else{
        $baseURL .= "&is_print=1&print_type=" . $type;
    }
    
    return $baseURL;
}