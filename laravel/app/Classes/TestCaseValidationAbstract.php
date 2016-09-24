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

    public function __construct($transactionFilesFolder, Transaction &$transaction, $userId)
    {
        $this->rootFolder = $transactionFilesFolder;
        $this->transaction = $transaction;
        $this->userId = $userId;
    }

    /**
     * Validate scanned files
     */
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
     * Get expected number of files
     * @return mixed
     */
    abstract function getFilesNumber();
    /**
     * Check that all needed files exist.
     * Set error message if any file is missed
     */
    protected function validateFilesExistence()
    {
        $missingMetaFile = $this->getMissingMetaFile();
        if (!$this->allScanFilesExists()) {
            $this->reasons[] = sprintf('Scan result is missing, expected to be %s, but actual %d images have been scanned.', $this->getFilesNumber(), $this->countImages());
        } else if ($missingMetaFile) {
            $this->reasons[] = sprintf('A metadata file is missing for the following scan result: image_%s.*.json.', $missingMetaFile);
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
        for ($i = 1; $i <= $this->getFilesNumber(); $i++) {
            $file = glob($this->rootFolder . '/scan_result/image_' . $i . '.*.json')[0];
            $this->jsonFilesContent[$i] = json_decode(file_get_contents($file));
        }
    }

    /**
     * Ensure that all images / json files exists
     * @return bool|int
     */
    public function allScanFilesExists()
    {
        for ($i = 1; $i <= $this->getFilesNumber(); $i++) {
            if (!glob($this->rootFolder . '/scan_result/image_' . $i . '.*')) {
                return $i;
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
        for ($i = 1; $i <= $this->getFilesNumber(); $i++) {
            if (!glob($this->rootFolder . '/scan_result/image_' . $i . '.*.json')) {
                return $i;
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
        for ($i = 1; $i <= $this->getFilesNumber(); $i++) {
            if (!glob($this->rootFolder . '/scan_result/image_' . $i . '.*')) {
                return false;
            }
            $imagesCount++;
        }
        return $imagesCount;
    }

    /**
     * Calculate diff between 2 numbers
     * @param $firstNumber
     * @param $secondNumber
     * @return number
     */
    public function getTwoNumbersDiffInPercents($firstNumber, $secondNumber)
    {
        return abs(number_format((1 - $firstNumber / $secondNumber) * 100, 2));
    }
}