<?php

namespace App;

class CA03 extends TestCaseValidationAbstract
{

    /**
     * Server validation for CA-03 test case
     */
    public function validate()
    {
        if (!$this->allScanFilesExists()) {
            $this->transaction->test_outcome_status_id = TestOutcomeStatus::getIdByCode('FAIL');
            $this->transaction->reason = sprintf('Scan result is missing, expected to be 4, but actual %d images have been scanned.', $this->countImages());
        } else {
            $this->getJsonFilesContent();
            //The length of the first image = the width of the second image with 5% error.
            //The width of the first image = the length of the second image with 5% error.
            if (abs($this->jsonFilesContent[1]->ImageLength / $this->jsonFilesContent[2]->ImageWidth) > 0.05 ||
                abs($this->jsonFilesContent[1]->ImageWidth / $this->jsonFilesContent[2]->ImageLength) > 0.05
            ) {
                $this->reasons[] = 'Either the a5 sample was rotated during the first scanning or was not during the second scanning.';
            }

            //The length of the third image = the width of the fourth image with 5% error.
            //The width of the third image = the length of the fourth image with 5% error.
            if (abs($this->jsonFilesContent[3]->ImageLength / $this->jsonFilesContent[4]->ImageWidth) > 0.05 ||
                abs($this->jsonFilesContent[3]->ImageWidth / $this->jsonFilesContent[4]->ImageLength) > 0.05
            ) {
                $this->reasons[] = 'Either the a4 sample was rotated during the first scanning or was not during the second scanning.';
            }

            if (!empty($this->reasons)) {
                $this->transaction->test_outcome_status_id = TestOutcomeStatus::getIdByCode('FAIL');
                $this->transaction->reason = implode(PHP_EOL, $this->reasons);
            }
        }
    }
}