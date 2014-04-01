<?php
/*
 * Template Name: PDF
 */
 // get_header();
?>
<?php
    // Include the main TCPDF library (search for installation path).
    require_once('functions/tcpdf/tcpdf.php');
    require_once('functions/tcpdf/config/tcpdf_config.php');

    // Include 2D barcode class (search for installation path)
    require_once('functions/tcpdf/tcpdf_barcodes_2d.php');

// Extra header voor background color
class CPPDF extends TCPDF {
    //Page header
    public function Header() {
        // Background color
        $this->Rect(0,0,210,20,'F','',$fill_color = array(91, 117, 182));

        $header_logo = K_PATH_IMAGES."header-logo.png";
        $this->Image($header_logo, 4, 4, 45, 0, 'PNG', 'https://www.compliancetest.net/', 'N', false, $dpi=300, '', false, false, 0, false, false, false, false);

        $drummond_group = K_PATH_IMAGES."drummond-group.png";
        $this->Image($drummond_group, 165, 3, 40, 0, 'PNG', '', 'N', false, $dpi=300, '', false, false, 0, false, false, false, false);

    }

    public function Footer() {
        // Fill footer with background color
        $this->Rect(0,278,210,40,'F','',$fill_color = array(91, 117, 182));

        // Position at 19 mm from bottom
        $this->SetY(-14);

        // Set font
        $this->SetFont('helvetica', '', 15);
        $this->SetTextColor(255,255,255);
        $this->setTextShadow(array('enabled'=>true, 'depth_w'=>0.2, 'depth_h'=>0.2, 'color'=>array(67,110,179), 'opacity'=>1, 'blend_mode'=>'Normal'));

        // Left link
        $this->Write(10, 'www.compliancetest.net', 'https://www.compliancetest.net/', false, 'L', true);

        // Right logo
        $image_file = K_PATH_IMAGES."powered-by-gosource.png";
        $this->Image($image_file, 180, 283, 38, 0, 'PNG', 'https://www.gosource.com.au/', 'N', true, $dpi=300, 'R', false, false, 0, false, false, false, false);
    }

}

// create new PDF document
$pdf = new CPPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);


// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('ComplianceTest');
$pdf->SetTitle('ComplianceTest Certificate');
$pdf->SetSubject('ComplianceTest Certificate');

// set margins
$pdf->SetMargins(11, 25, 11, true);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
    require_once(dirname(__FILE__).'/lang/eng.php');
    $pdf->setLanguageArray($l);
}

// ---------------------------------------------------------

// Set font
$pdf->SetFont('helvetica', '', 14, '', true);

// Set line-height
$pdf->setCellHeightRatio(1);

// Add a page
// This method has several options, check the source code documentation for more information.
$pdf->AddPage();

$css_style = '
<style>
    table.certificate-info th {
        border-bottom: 0.2em solid #959595;
        font-weight: normal;
        margin-left: 2pt;
        width: 35%;
    }
    table.certificate-info td {
        border-bottom: 0.2em solid #959595;
        font-weight: bold;
    }
</style>';

$title = '<h1 style="color: #000; font-size: 40pt; line-height: 42pt; text-transform: uppercase;">CERTIFICATE</h1>';
$description = '<p style="font-size: 13.5pt; line-height:18pt;"><br>This certificate confirms that the holder has successfully completed the indicated test suite using the specified product or service version. The test suite has been designed to meet the compliance requirements of the reference specification issuer.<br></p>';

$certificate_data_info = '
<style>
    table.certificate-info th {
        border-bottom: 0.2em solid #959595;
        font-weight: normal;
        margin-left: 2pt;
        width: 35%;
        font-size:16pt;
        color:#262626;
    }
    table.certificate-info td {
        border-bottom: 0.2em solid #959595;
        font-weight: bold;
        width:63%;
        font-size:16pt;
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
        <th>Claim ID</th>
        <td>SS-CTR-00768475</td>
    </tr>
    <tr>
        <th>Date of Claim</th>
        <td>20 March 2014</td>
    </tr>
</table>
';


// Print text using writeHTMLCell()
//$pdf->writeHTMLCell(0, 0, '', '', $css_style, 0, 1, 0, true, '', true);

// Print text using writeHTMLCell()
$pdf->writeHTMLCell(0, 0, '', '', $title, 0, 1, 0, false, 'C', true);

// Print text using writeHTMLCell()
$pdf->writeHTMLCell(0, 0, '', '', $description, 0, 1, 0, false, '', false);

// Print text using writeHTMLCell()
$compliance_tested_image = K_PATH_IMAGES."compliance-tested.png";
$pdf->Image($compliance_tested_image, '', '', 120, '', 'PNG', '', 'N', false, 300, 'C', false, false, 1, false, false, false);

// Print text using writeHTMLCell()
$pdf->writeHTMLCell('', '', '', '', $certificate_data_info, 0, 1, 0, true, '', true);

// new style
$style = array(
    'border' => false,
    'padding' => 0,
    'vpadding' => 10,
    'fgcolor' => array(0,0,0),
    'position' => 'C'
);

// QRCODE,H : QR-CODE Best error correction
$pdf->write2DBarcode('http://www.compliancetest.net/product/ebms3-messenger/', 'QRCODE,H', '', '', 40, 40, $style, 'N');

$link = '<div style="text-align:center;"><a href="http://www.compliancetest.net/product/ebms3-messenger/" target="_blank" style="color:#5b75b6; font-size:13pt; text-decoration:none;">http://www.compliancetest.net/product/ebms3-messenger/</a></div>';

$pdf->writeHTMLCell(0, 0, '', '', $link, 0, 1, 0, true, '', true);
// ---------------------------------------------------------

// Close and output PDF document
// This method has several options, check the source code documentation for more information.
$pdf->Output('ComplianceTest-certificate.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+



?>