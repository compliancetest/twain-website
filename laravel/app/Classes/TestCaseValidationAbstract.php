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

    public function __construct($transactionFilesFolder, $filesNumber, Transaction &$transaction)
    {
        $this->rootFolder = $transactionFilesFolder;
        $this->transaction = $transaction;
        $this->filesCount = $filesNumber;
    }

    abstract public function validate();

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
        $imagesCount = 0;
        if ($this->filesCount > 0) {
            for ($i = 1; $i <= $this->filesCount; $i++) {
                if (!file_exists($this->rootFolder . '/scan_result/image_' . $i . '.png') ||
                    !file_exists($this->rootFolder . '/scan_result/image_' . $i . '.png.json')
                ) {
                    return false;
                }
                $imagesCount++;
            }
        }
        return $imagesCount;
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