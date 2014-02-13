<?php
/**
* Template Name: View Message Template
*/

$caseID = $_GET['id'];

$case = new TestCase($caseID);
$case->load();

if(!$case->id)
{
    echo '<p>Invalid Request!</p>';
    exit;
}

$mode = isset($_GET['mode']) ? $_GET['mode'] : 'xml';

$tester_profile = $_GET['tester-profile'];
$harness_profile = $_GET['harness-profile'];
$template = base64_decode($_GET['template']);

if(!$tester_profile || !$harness_profile || !$template ){
    echo '<p>Invalid Request!</p>';
    exit;
}

//Template Validation
$isValid = false;
foreach($case->messageTemplates as $row)
{
    if($row['url'] == $template)
    {
        $isValid = true;
        break;
    }
}
if(!$isValid)
{
    echo '<p>Invalid Request!</p>';
    exit;
}

$profileInstances = $case->getProfileInstanceRows();
//Tester Profile Validation
$isValid = false;
$testerProfileURL = '';
foreach($profileInstances as $row)
{
    if($row->id == $tester_profile)
    {
        $isValid = true;
        $testerProfileURL = get_site_url() . '/get-profile?id=' . $row->token;
        break;
    }
}
if(!$isValid)
{
    echo '<p>Invalid Request!</p>';
    exit;
}

//Harness Profile Validation
$isValid = false;
$harnessProfileURL = '';
foreach($profileInstances as $row)
{
    if($row->id == $harness_profile)
    {
        $isValid = true;
        $harnessProfileURL = get_site_url() . '/get-profile?id=' . $row->token;
        break;
    }
}
if(!$isValid)
{
    echo '<p>Invalid Request!</p>';
    exit;
}

$data = '<api:renderTemplateRequest xmlns:api="http://compliancetest.net/api">
            <api:messageTemplate  templateURI="' . $template . '">
                  <api:profile namespace="Tester">
                        <api:profileURL>' . $testerProfileURL . '</api:profileURL>
                  </api:profile>
                  <api:profile namespace="Harness">
                        <api:profileURL>' . $harnessProfileURL . '</api:profileURL>
                  </api:profile>
            </api:messageTemplate>
        </api:renderTemplateRequest>';


/*$data = '<api:renderTemplateRequest xmlns:api="http://compliancetest.net/api">
            <api:messageTemplate  templateURI="SS-CONT/V1/SS-CTR_V1_01a.contribution.simple.ftl">
                  <api:profile namespace="Tester">
                        <api:profileURL>https://www.compliancetest.net/get-profile?id=08a139f1ec0e5986adbd16d8295089cf394fa2c7</api:profileURL>
                  </api:profile>
                  <api:profile namespace="Harness">
                        <api:profileURL>https://www.compliancetest.net/get-profile?id=c4bdc391787963ea8774bd574e8f886ed219eb84</api:profileURL>
                  </api:profile>
            </api:messageTemplate>
        </api:renderTemplateRequest>';*/
$action = 'template/render';// . ($mode == 'html' ? '/HTML' : '');

$result = $CPRest->doRepositoryAPI($action, $data);

$resultDoc = new DOMDocument();

if($result=='ERROR'){
    header("Content-type: text/html");
    $result = '<html><head><title>Sorry!</title><link href="http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800|Oswald:400,300,700" rel="stylesheet" type="text/css"/><link href="https://www.compliancetest.net/wp-content/themes/bp-child/css/xslt.css" type="text/css" rel="stylesheet"/></head><body><div id="wrapper"><div id="header-wrapper"><div class="content"><a href="https://www.compliancetest.net" class="logo left"><img src="https://www.compliancetest.net/wp-content/uploads/2013/03/logo.png"/></a></div></div><div id="menu-wrapper"></div><div id="content-wrapper"><div class="content"><div id="content-inner"><h2>An Error Occurred!</h2><p>We\'re sorry, but an error occurred during request execution. Please try again later or contact support.</p></div></div></div></div></body></html>';
}
elseif ($result != 'ERROR' && $resultDoc->loadXML($result) && $mode=='html'){
    header("Content-type: application/xml");
    $xslt = get_site_url() . '/xslt/message-template-render.xsl';
    echo "<?xml version='1.0' encoding='utf-8'?>";
    echo '<?xml-stylesheet type="text/xsl" href="' . $xslt . '"?>';
}
elseif ($result != 'ERROR' && $resultDoc->loadXML($result) && $mode=='xml'){
    header("Content-type: application/xml");
}

echo $result;

