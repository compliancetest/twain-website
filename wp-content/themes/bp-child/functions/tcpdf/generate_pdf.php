<?php

require_once('cppdf.php');
require_once('config/tcpdf_config.php');
// Include 2D barcode class
require_once('tcpdf_barcodes_2d.php');


function generateClaimPdf() {

    // Create new PDF document
    $pdf = new CPPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    // Set document meta information
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('ComplianceTest');
    $pdf->SetTitle('ComplianceTest Certificate');
    $pdf->SetSubject('ComplianceTest Certificate');

    // Set margins
    $pdf->SetMargins(12, 29, 12, true);
    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

    // Set auto page breaks
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

    // Set image scale factor
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

    // Set some language-dependent strings (optional)
    if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
        require_once(dirname(__FILE__) . '/lang/eng.php');
        $pdf->setLanguageArray($l);
    }


/*
    // set certificate file
    $certificate = 'file://c:\ssl\cptest.crt';

    // set additional information
    $info = array(
        'Name' => 'ComplianceTest',
        'Location' => 'Australia',
        'Reason' => 'ComplianceTest Testing',
        'ContactInfo' => 'http://www.compliancetest.net',
    );

    // set document signature
    $pdf->setSignature($certificate, $certificate, '', '', 2, $info);
*/
    // ---------------------------------------------------------

    // Set font
    $pdf->SetFont('opensans', '', 13, '', true);

    // Set line-height
    $pdf->setCellHeightRatio(1);

    // Add a page
    // This method has several options, check the source code documentation for more information.
    $pdf->AddPage();

    $title = '<h1 style="color: #000; font-size: 48pt; font-weight: bold; line-height: 42pt; text-transform: uppercase;">CERTIFICATE</h1>';
    $description = '<p style="font-size: 13pt; line-height:16pt;"><br>This certificate confirms that the holder has successfully completed the indicated test suite using the specified product or service version. The test suite has been designed to meet the compliance requirements of the reference specification issuer.<br></p>';

    $certificate_data_info = '
<style>
    table.certificate-info th {
        border-bottom: 0.2em solid #959595;
        font-weight: normal;
        margin-left: 2pt;
        width: 35%;
        font-size:13pt;
        color:#262626;
    }
    table.certificate-info td {
        border-bottom: 0.2em solid #959595;
        font-weight: bold;
        width:63%;
        font-size:13pt;
        color:#000;
    }
</style>
<br><br>
<table cellspacing="5" cellpadding="5" class="certificate-info" width="100%">
    <tr>
        <th>Issued To</th>
        <td>ACME Pty Ltd</td>
    </tr>
    <tr>
        <th>Product or Service</th>
        <td>Financial Wizard</td>
    </tr>
    <tr>
        <th>Product Version</th>
        <td>11.2</td>
    </tr>
    <tr>
        <th>Test Suite</th>
        <td>SuperStream Contributions</td>
    </tr>
    <tr>
        <th>Test Suite Version</th>
        <td>1.1</td>
    </tr>
    <tr>
        <th>Specification Issuer</th>
        <td>Australian Tax Office</td>
    </tr>
    <tr>
        <th>Conformance Level(s)</th>
        <td>B, A</td>
    </tr>
    <tr>
        <th>Role(s)</th>
        <td>Fund</td>
    </tr>
    <tr>
        <th>Status</th>
        <td>Self Assessed</td>
    </tr>
    <tr>
        <th>Claim ID</th>
        <td>SS-CTR-00768475</td>
    </tr>
    <tr>
        <th>Date of Claim</th>
        <td>20 March 2014</td>
    </tr>
</table>
';

    $pdf->SetFont('opensansb', '', 13, '', true);

    $pdf->setHtmlLinksStyle(array(91, 117, 182));

    // Print text using writeHTMLCell()
    $pdf->writeHTMLCell(0, 0, '', '', $title, 0, 1, 0, false, 'C', true);


    $pdf->SetFont('opensans', '', 13, '', true);
    // Print text using writeHTMLCell()
    $pdf->writeHTMLCell(0, 0, '', '', $description, 0, 1, 0, false, '', false);

    // Print text using writeHTMLCell()
    $compliance_tested_image = K_PATH_IMAGES . "compliance-tested.png";
    $pdf->Image($compliance_tested_image, '', '', 120, '', 'PNG', '', 'N', false, 300, 'C', false, false, 1, false, false, false);

    // define active area for signature appearance
    $pdf->setSignatureAppearance(45, 72, 121, 29);

    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

    // *** set an empty signature appearance ***
//    $pdf->addEmptySignatureAppearance(0, 20, 210, 50);


    // Print text using writeHTMLCell()
    $pdf->writeHTMLCell('', '', '', '', $certificate_data_info, 0, 1, 0, true, '', true);

    // Styles for QR code
    $style = array('border' => false, 'padding' => 0, 'vpadding' => 10, 'fgcolor' => array(0, 0, 0), 'position' => 'C');

    // QRCODE,H : QR-CODE Best error correction
    $pdf->write2DBarcode('http://www.compliancetest.net/product/ebms3-messenger/', 'QRCODE,H', '', '', 40, 40, $style, 'N');

    $link = '<div style="text-align:center;"><a href="http://www.compliancetest.net/product/ebms3-messenger/" target="_blank" style="font-size:13pt; text-decoration:none;">http://www.compliancetest.net/product/ebms3-messenger/</a></div>';

    $pdf->writeHTMLCell(0, 0, '', '', $link, 0, 1, 0, true, '', true);
    // ---------------------------------------------------------


    // ---------------------------------------------------------
    $pdf->SetMargins(3.8, 24.5, 3.8, true);

    // Add a page
    // This method has several options, check the source code documentation for more information.
    $pdf->AddPage();


    $pdf->setTextShadow(array('enabled' => false));

    $test_cases_table_html = '
<style>
    .test-cases-table th {
        background-color:#5a75b6;
        color:#fff;
        font-size:7pt;
        vertical-align:middle;
        line-height:18pt;
        text-align:center;
        font-weight:bold;
    }
    .test-cases-table th.test-outcome{
        line-height:10px;
    }
    .test-cases-table th.test-scenario{
        text-align:left;
    }
    .test-cases-table td {
        font-size:6pt;
        line-height:6pt;
        color:#000;
    }
    .test-cases-table .even td{
        background-color:#f3f4f5;
    }
    .test-cases-table .odd td{
        background-color:#ececed;
    }
    .test-cases-table td a{
        font-size:10pt;
    }
    .test-cases-table td.test-scenario{
        background-color:#e2e2e2;
    }

    .issued, .test-outcome, .supporting-evidence{
        text-align:center;
    }
</style>
<table cellspacing="1" cellpadding="3" class="test-cases-table" width="100%">
    <tr>
        <th class="test-scenario" style="width:25%; vertical-align:middle;">Test Scenario</th>
        <th class="test-case" style="width:12%;">Test Case</th>
        <th class="issued" style="width:8%;">Issued</th>
        <th class="test-intent" style="width:30%;">Test Intent Description</th>
        <th class="test-outcome" style="width:8%;">Test<br/>Outcome</th>
        <th class="supporting-evidence" style="width:17%;">Supporting Evidence</th>
    </tr>
    <tr class="odd">
        <td class="test-scenario" rowspan="4"><strong>CTR-01 Default Contribution:</strong><br>Joanna and William are employees of Artmet Pty Ltd and are registered members of Artmets default fund, ACME Flexi-Super, a basic accumulation fund. In this scenario, Artmet makes a successful contribution. Includes cases with and without positive (Information) response message.</td>
        <td class="test-case">SS-CTR-01a v1.0</td>
        <td class="issued">2014-01-20</td>
        <td class="test-intent">As a fund, test successful receipt of contributions message from an employer - with no response</td>
        <td class="test-outcome">Pass</td>
        <td class="supporting-evidence" style="vertical-align:top;"><a href="/message-envelope?id=1423">/message-envelope?id=1423</a><a href="/message-envelope?id=1422">/message-envelope?id=1422</a></td>
    </tr>
    <tr class="even">
        <td class="test-case">SS-CTR-01a v1.0</td>
        <td class="issued">2014-01-20</td>
        <td class="test-intent">As a fund, test successful receipt of contributions message from an employer - with no response</td>
        <td class="test-outcome">Pass</td>
        <td class="supporting-evidence" style="vertical-align:top;"><a href="/message-envelope?id=1423">/message-envelope?id=1423</a><a href="/message-envelope?id=1422">/message-envelope?id=1422</a></td>
    </tr>
    <tr class="odd">
        <td class="test-case">SS-CTR-01a v1.0</td>
        <td class="issued">2014-01-20</td>
        <td class="test-intent">As a fund, test successful receipt of contributions message from an employer - with no response</td>
        <td class="test-outcome">Pass</td>
        <td class="supporting-evidence" style="vertical-align:top;"><a href="/message-envelope?id=1423">/message-envelope?id=1423</a><a href="/message-envelope?id=1422">/message-envelope?id=1422</a></td>
    </tr>
    <tr class="even">
        <td class="test-case">SS-CTR-01a v1.0</td>
        <td class="issued">2014-01-20</td>
        <td class="test-intent">As a fund, test successful receipt of contributions message from an employer - with no response</td>
        <td class="test-outcome">Pass</td>
        <td class="supporting-evidence" style="vertical-align:top;"><a href="/message-envelope?id=1423">/message-envelope?id=1423</a><a href="/message-envelope?id=1422">/message-envelope?id=1422</a></td>
    </tr>
    <tr class="odd">
        <td class="test-scenario" rowspan="4"><strong>CTR-01 Default Contribution:</strong><br>Joanna and William are employees of Artmet Pty Ltd and are registered members of Artmets default fund, ACME Flexi-Super, a basic accumulation fund. In this scenario, Artmet makes a successful contribution. Includes cases with and without positive (Information) response message.</td>
        <td class="test-case">SS-CTR-01a v1.0</td>
        <td class="issued">2014-01-20</td>
        <td class="test-intent">As a fund, test successful receipt of contributions message from an employer - with no response</td>
        <td class="test-outcome">Pass</td>
        <td class="supporting-evidence" style="vertical-align:top;"><a href="/message-envelope?id=1423">/message-envelope?id=1423</a><a href="/message-envelope?id=1422">/message-envelope?id=1422</a></td>
    </tr>
    <tr class="even">
        <td class="test-case">SS-CTR-01a v1.0</td>
        <td class="issued">2014-01-20</td>
        <td class="test-intent">As a fund, test successful receipt of contributions message from an employer - with no response</td>
        <td class="test-outcome">Pass</td>
        <td class="supporting-evidence" style="vertical-align:top;"><a href="/message-envelope?id=1423">/message-envelope?id=1423</a><a href="/message-envelope?id=1422">/message-envelope?id=1422</a></td>
    </tr>
    <tr class="odd">
        <td class="test-case">SS-CTR-01a v1.0</td>
        <td class="issued">2014-01-20</td>
        <td class="test-intent">As a fund, test successful receipt of contributions message from an employer - with no response</td>
        <td class="test-outcome">Pass</td>
        <td class="supporting-evidence" style="vertical-align:top;"><a href="/message-envelope?id=1423">/message-envelope?id=1423</a><a href="/message-envelope?id=1422">/message-envelope?id=1422</a></td>
    </tr>
    <tr class="even">
        <td class="test-case">SS-CTR-01a v1.0</td>
        <td class="issued">2014-01-20</td>
        <td class="test-intent">As a fund, test successful receipt of contributions message from an employer - with no response</td>
        <td class="test-outcome">Pass</td>
        <td class="supporting-evidence" style="vertical-align:top;"><a href="/message-envelope?id=1423">/message-envelope?id=1423</a><a href="/message-envelope?id=1422">/message-envelope?id=1422</a></td>
    </tr>
</table>
';

    $pdf->SetFont('opensans', '', 13, '', true);
    $pdf->writeHTMLCell(0, 0, '', '', $test_cases_table_html, 0, 1, 0, true, '', true);

    // Close and output PDF document
    // This method has several options, check the source code documentation for more information.
    $pdf->Output('ComplianceTest-certificate.pdf', 'I');

    //============================================================+
    // END OF FILE
    //============================================================+

}

generateClaimPdf();

?>