<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Claim extends Model
{

    use UuidTrait;

    protected $fillable = [
        'product_id',
        'organisation_id',
        'test_suite_id',
        'creator_id',
        'conformance_level',
        'role',
        'status',
        'has_exclusions'
    ];

    protected $dates = ['created_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function testPlan()
    {
        return $this->belongsTo('App\TestPlan');
    }

    public function generatePDF()
    {
        require_once(THE_FUNCTION . '/tcpdf/cppdf.php');
        require_once(THE_FUNCTION . '/tcpdf/config/tcpdf_config.php');
        // Include 2D barcode class
        require_once(THE_FUNCTION . '/tcpdf/tcpdf_barcodes_2d.php');

        // Create new PDF document
        $pdf = new \CPPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // Set document meta information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor(get_site_title());
        $pdf->SetTitle(get_site_title() . ' Certificate');
        $pdf->SetSubject(get_site_title() . ' Certificate');

        // Set margins
        $pdf->SetMargins(12, 29, 12, true);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        // Set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, 20);

        // Set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // set certificate file
        //$certificate = 'file://' . THE_FUNCTION . '\tcpdf\claims.pem';
        $certificate = get_option('pdf_certificate');
        $private_key = get_option('pdf_private_key');

        // set additional information
        $info = array(
            'Location' => 'Australia',
            'ContactInfo' => getSiteUrl(),
        );

        // set document signature
        $pdf->setSignature($certificate, $private_key, '', '', 2, $info);

        // ---------------------------------------------------------

        // Set font
        $pdf->SetFont('opensans', '', 13, '', true);

        // Set line-height
        $pdf->setCellHeightRatio(1);

        // Add a page
        $pdf->AddPage();

        $pdf->SetFont('opensansb', '', 13, '', true);

        $pdf->setHtmlLinksStyle(array(91, 117, 182));

        // Print text using writeHTMLCell()
        $pdf->writeHTMLCell(0, 0, '', '', view('pages.my.claims._title')->render(), 0, 1, 0, false, 'C', true);


        $pdf->SetFont('opensans', '', 13, '', true);
        // Print text using writeHTMLCell()
        $pdf->writeHTMLCell(0, 0, '', '', view('pages.my.claims._description')->render(), 0, 1, 0, false, '', false);

        // Print text using writeHTMLCell()
        $compliance_tested_image = K_PATH_IMAGES . "compliance-tested.png";
        $pdf->Image($compliance_tested_image, '', '', 120, '', 'PNG', '', 'N', false, 300, 'C', false, false, 1, false, false, false);

        // define active area for signature appearance
        $pdf->setSignatureAppearance(45, 72, 121, 29);

        // Print text using writeHTMLCell()
        $pdf->writeHTMLCell('', '', '', '', view('pages.my.claims._cert_info')->with([
            'product' => Post::find($this->product_id),
            'testSuite' => Post::find($this->test_suite_id),
            'claim' => $this,
        ])->render(), 0, 1, 0, true, '', true);

        // Styles for QR code
        $style = array('border' => false, 'padding' => 0, 'vpadding' => 10, 'fgcolor' => array(0, 0, 0), 'position' => 'C');

        // QRCODE,H : QR-CODE Best error correction
        $pdf->write2DBarcode(getSiteUrl() . '/claims/' . $this->id . ".pdf", 'QRCODE,H', '', '', 40, 40, $style, 'N');

        $link = '<div style="text-align:center;"><a href="' . $this->getPdfUrl() . '" target="_blank" style="font-size:13pt; text-decoration:none;">' . $this->getPdfUrl() . '</a></div>';

        $pdf->writeHTMLCell(0, 0, '', '', $link, 0, 1, 0, true, '', true);

        $pdf->SetMargins(3.8, 24.5, 3.8, true);

        // Add a page
        $pdf->AddPage();

        $pdf->setTextShadow(array('enabled' => false));

        $testSuite = TestSuite::find($this->test_suite_id);
        $testPlan = TestPlan::find($this->test_plan_id);

        $successCases = $testPlan->getSuccessCases($this->product_id);
        $optionalCases = $testPlan->getOptionalCases();
        $excCases = $testPlan->getExcludedCases();

        //Classify the results by Scenario
        $excludedCases = $generalCases = array();
        foreach ($testSuite->getTestCases() as $case) {
            if (in_array($case->ID, $successCases)) {
                if (!isset($generalCases[$case->scenarioID])) {
                    $generalCases[$case->scenarioID] = array();
                }
                $case->link = Transaction::where(['product_id' => $this->product_id, 'test_case_id' => $case->ID, 'test_suite_id' => $this->test_suite_id, 'audit_record' => true])->first()->s3_link;
                $generalCases[$case->scenarioID][] = $case;
            } elseif (!in_array($case->ID, $optionalCases)) {
                if (!isset($excludedCases[$case->scenarioID])) {
                    $excludedCases[$case->scenarioID] = array();
                }
                $case->reason = $excCases[$case->ID]['reason'];
                $excludedCases[$case->scenarioID][] = $case;
            }

        }

        $pdf->SetFont('opensans', '', 13, '', true);

        if (count($generalCases)) {
            $pdf->writeHTMLCell(0, 0, '', '', view('pages.my.claims._general_cases')->with(['generalCases' => $generalCases])->render(), 0, 1, 0, true, '', true);
        }
        if (count($excludedCases)) {
            if (count($generalCases) > 0) {
                $pdf->AddPage();
            }
            $pdf->writeHTMLCell(0, 0, '', '', view('pages.my.claims._excluded_cases')->with(['excludedCases' => $excludedCases])->render(), 0, 1, 0, true, '', true);
        }

        // Return PDF file string
        return $pdf->Output('twain-certificate.pdf', 'S');

    }

    /**
     * Send notification about new claim to community admins
     */
    public function sendNewClaimNotification()
    {
        $user = \Auth::user();
        $testSuite = Post::find($this->test_suite_id);
        $product = Post::find($this->product_id);
        $emailData = array(
            '[claim_id]' => $this->id,
            '[product_name]' => $product->post_title,
            '[product_url]' => getSiteUrl() . '/product/' . $product->post_name,
            '[suite_name]' => $testSuite->post_title,
            '[suite_url]' => getSiteUrl() . '/test-suite/' . $testSuite->post_name,
            '[issuer]' => $testSuite->getMetaByKey('ts_issuer'),
            '[conformance_level]' => $this->conformance_level,
            '[role]' => $this->role,
            '[status]' => $this->status,
            '[date]' => $this->created_at,
            '[username]' => $user->getFullName(),
            '[useremail]' => $user->user_email,
            '[certificate]' => '<a href="' . getSiteUrl() . '/claims/' . $this->id . '" target="_blank">View PDF</a>'
        );

        cp_send_email_to_community_admin($testSuite->getMetaByKey('community_id'), 'claim_created_admin', $emailData);
    }

    /**
     * S3 url for generated claim
     * @return string
     */
    public function getPdfUrl()
    {
        return 'https://s3-' . config('env.bucket.region') . '.amazonaws.com/' . config('env.bucket.website') . '/claims/products/' . $this->id . '.pdf';
    }
}
