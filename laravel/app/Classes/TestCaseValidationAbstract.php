<?php

namespace App;

abstract class TestCaseValidationAbstract
{

    /**
     * Decoded Images metadata files
     * @var array
     */
    protected $jsonFilesContent = [];

    /**
     * Here we save transaction's error reasons
     * @var array
     */
    protected $reasons = [];
    /**
     * Expected number of images in scans folder
     * @var
     */
    protected $filesCount;

    public function __construct($transactionFilesFolder, $filesNumber, Transaction &$transaction, $userId)
    {
        $this->rootFolder = $transactionFilesFolder;
        $this->transaction = $transaction;
        $this->filesCount = $filesNumber;
        $this->userId = $userId;
    }

    public function validate()
    {
        $this->validateFilesExistence();
        if (empty($this->reasons)) {
            $this->getJsonFilesContent();
            $this->testCaseRules();
        }
        $this->handleErrors();
    }

    /**
     * List of test case rules
     * @return mixed
     */
    abstract function testCaseRules();

    /**
     * Check that all needed files exist.
     * Set error message if any file is missed
     */
    protected function validateFilesExistence()
    {
        $missingMetaFile = $this->getMissingMetaFile();
        if (!$this->allScanFilesExists()) {
            $this->reasons[] = sprintf('Scan result is missing, expected to be 4, but actual %d images have been scanned.', $this->countImages());
        } else if ($missingMetaFile) {
            $this->reasons[] = sprintf('A metadata file is missing for the following scan result: image_%s.png.json.', $missingMetaFile);
        }
    }

    /**
     * Update transaction error messages if it contains any validation error
     */
    protected function handleErrors()
    {
        if (!empty($this->reasons)) {
            if ($this->transaction->test_outcome_status_id != TestOutcomeStatus::getIdByCode('FAIL')) {
                TransactionChangeLog::addLog($this->transaction, $this->userId, 'FAIL', true);
            }
            $this->transaction->test_outcome_status_id = TestOutcomeStatus::getIdByCode('FAIL');
            $this->transaction->reason = implode('<br>', $this->reasons);
        }
    }

    /**
     * Decode all files metadata
     */
    public function getJsonFilesContent()
    {
        if ($this->filesCount > 0) {
            for ($i = 1; $i <= $this->filesCount; $i++) {
                $this->jsonFilesContent[$i] = json_decode(file_get_contents($this->rootFolder . '/scan_result/image_' . $i . '.png.json'));
            }
        }
    }

    /**
     * Ensure that all images / json files exists
     * @return bool|int
     */
    public function allScanFilesExists()
    {
        if ($this->filesCount > 0) {
            for ($i = 1; $i <= $this->filesCount; $i++) {
                if (!file_exists($this->rootFolder . '/scan_result/image_' . $i . '.png')
                ) {
                    return $i;
                }
            }
        }
        return true;
    }

    /**
     * Ensure that all json files exists
     * @return bool|int - returns true or number of non existing file
     */
    public function getMissingMetaFile()
    {
        if ($this->filesCount > 0) {
            for ($i = 1; $i <= $this->filesCount; $i++) {
                if (!file_exists($this->rootFolder . '/scan_result/image_' . $i . '.png.json')) {
                    return $i;
                }
            }
        }
        return false;
    }

    /**
     * Count number of images in scan folder
     * @return bool|int
     */
    protected function countImages()
    {
        $imagesCount = 0;
        for ($i = 1; $i <= $this->filesCount; $i++) {
            if (!file_exists($this->rootFolder . '/scan_result/image_' . $i . '.png')) {
                return false;
            }
            $imagesCount++;
        }
        return $imagesCount;
    }

    /**
     * Calculate diff between 2 images
     * @param $firstNumber
     * @param $secondNumber
     * @return number
     */
    public function getTwoNumbersDiffInPercents($firstNumber, $secondNumber)
    {
        return abs(number_format((1 - $firstNumber / $secondNumber) * 100, 2));
    }
}