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
    if($row->id == $tester_profile)
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
echo $data;
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
$action = 'template/render' . ($mode == 'html' ? '/HTML' : '');

$result = $CPRest->doRepositoryAPI($action, $data);

$resultDoc = new DOMDocument();
            
if($result && $resultDoc->loadXML($result))
{
    header("Content-type: application/xml");    
}

echo $result;

