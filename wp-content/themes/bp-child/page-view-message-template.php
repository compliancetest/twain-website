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
            <api:messageTemplate  templateName="' . $template . '">
                  <api:profile namespace="Tester">
                        <api:profileURL>' . $testerProfileURL . '</api:profileURL>
                  </api:profile>
                  <api:profile namespace="Harness">
                        <api:profileURL>' . $harnessProfileURL . '</api:profileURL>
                  </api:profile>
            </api:messageTemplate>
        </api:renderTemplateRequest>';

$result = $CPRest->doMessageAPI('template/render' . $mode == 'html' ? '/html' : '', $data);

$resultDoc = new DOMDocument();
            
if($result && !$resultDoc->loadXML($result))
{
    header("Content-type: application/xml");    
}
echo $result;

