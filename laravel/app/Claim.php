<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Claim extends Model
{

    use UuidTrait;

    public $incrementing = false;
    
    protected $fillable = [
        'product_id',
        'organisation_id',
        'suite_minor_family_mark',
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

    public function product()
    {
        return $this->belongsTo('App\Product');
    }

    public function testSuite()
    {
        return $this->belongsTo('App\LaravelTestSuite', 'suite_minor_family_mark');
    }

    /**
     * List of transactions attached to claim via claim_transactions table
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transactions()
    {
        return $this->hasMany('App\ClaimTransactions');
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

        // define active area for signature appearance
        $pdf->setSignatureAppearance(45, 72, 121, 29);

        $testSuite = LaravelTestSuite::find($this->suite_minor_family_mark);
        $testPlan = TestPlan::find($this->test_plan_id);

        $successCases = $testPlan->getSuccessCases($this->product_id);
        $optionalCases = $testPlan->getOptionalCases();
        $excCases = $testPlan->getExcludedCases();

        //Classify the results by Scenario
        $excludedCases = $skippedCases = $generalCases = array();
        foreach ($testSuite->getTestCases($testPlan->role, $testPlan->level) as $case) {
            if (in_array($case->id, $successCases)) {
                if (!isset($generalCases[$case->scenarioCode])) {
                    $generalCases[$case->scenarioCode] = array();
                }
                $tempTransaction = Transaction::where([
                    'product_id' => $this->product_id,
                    'test_case_id' => $case->id,
                    'test_outcome_status_id' => \App\TestOutcomeStatus::getIdByCode('PASS'),
                    'audit_record' => true,
                    'suite_minor_family_mark' => $this->suite_minor_family_mark
                ])->first();
                $this->transactions()->create(['transaction_id' => $tempTransaction->id]);
                $case->link = $tempTransaction->s3_link;
                $generalCases[$case->scenarioCode][] = $case;
            } elseif (!in_array($case->id, $optionalCases)) {
                if (!isset($excludedCases[$case->scenarioCode])) {
                    $excludedCases[$case->scenarioCode] = array();
                }
                $case->reason = $excCases[$case->id]['reason'];
                if($excCases[$case->id]['is_skipped'] == 1){
                    $skippedCases[$case->scenarioCode][] = $case;
                } else {
                    $excludedCases[$case->scenarioCode][] = $case;
                }
            }

        }

        $countPassCases = $this->countCases($generalCases);
        $countSkipCases = $this->countCases($skippedCases);
        $countExcludeCases = $this->countCases($excludedCases);
        // Print text using writeHTMLCell()
        $pdf->writeHTMLCell('', '', '', '', view('pages.my.claims._cert_info')->with([
            'product' => Product::find($this->product_id),
            'testSuite' => LaravelTestSuite::getLatestSuiteForMinorFamilyMark($this->suite_minor_family_mark),
            'claim' => $this,
            'passCount' => $countPassCases,
            'excludeCount' => $countExcludeCases,
            'skipCount' => $countSkipCases,
            'totalCount' => $countPassCases + $countSkipCases + $countExcludeCases,
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

        $pdf->SetFont('opensans', '', 13, '', true);

        if ($countPassCases) {
            $pdf->writeHTMLCell(0, 0, '', '', view('pages.my.claims._general_cases')->with(['generalCases' => $generalCases])->render(), 0, 1, 0, true, '', true);
        }
        if ($countSkipCases) {
            if ($countPassCases) {
                $pdf->AddPage();
            }
            $pdf->writeHTMLCell(0, 0, '', '', view('pages.my.claims._excluded_cases')->with(['cases' => $skippedCases, 'message' => 'Skipped Test Cases'])->render(), 0, 1, 0, true, '', true);
        }

        if ($countExcludeCases) {
            if ($countPassCases || $countSkipCases) {
                $pdf->AddPage();
            }
            $pdf->writeHTMLCell(0, 0, '', '', view('pages.my.claims._excluded_cases')->with(['cases' => $excludedCases, 'message' => 'Excluded Test Cases'])->render(), 0, 1, 0, true, '', true);
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
        $testSuite = LaravelTestSuite::getLatestSuiteForMinorFamilyMark($this->suite_minor_family_mark);
        $product = Product::find($this->product_id);
        $emailData = array(
            '[claim_id]' => $this->id,
            '[product_name]' => $product->full_name,
            '[product_url]' => getSiteUrl() . '/product/' . $product->slug,
            '[suite_name]' => $testSuite->full_name,
            '[suite_url]' => getSiteUrl() . '/test-suite/' . $testSuite->slug,
            '[issuer]' => $testSuite->issuer,
            '[conformance_level]' => $this->conformance_level,
            '[role]' => $this->role,
            '[status]' => $this->status,
            '[date]' => $this->created_at,
            '[username]' => $user->getFullName(),
            '[useremail]' => $user->user_email,
            '[certificate]' => '<a href="' . getSiteUrl() . '/claims/' . $this->id . '" target="_blank">View PDF</a>'
        );

        cp_send_email_to_community_admin($testSuite->community_id, 'claim_created_admin', $emailData);
    }

    /**
     * S3 url for generated claim
     * @return string
     */
    public function getPdfUrl()
    {
        return 'https://s3-' . config('env.bucket.region') . '.amazonaws.com/' . config('env.bucket.website') . '/claims/products/' . $this->id . '.pdf';
    }

    /**
     * Count test cases
     * @param $cases
     * @return int
     */
    private function countCases($cases)
    {
        $count = 0;
        foreach ($cases as $case) {
            if (is_array($case) && !empty($case)) {
                foreach ($case as $subCases) {
                    $count++;
                }
            }
        }
        return $count;
    }
}
