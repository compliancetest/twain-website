<?php

namespace App;

class CA03 extends TestCaseValidationAbstract
{

    /**
     * Server validation for CA-03 test case
     */
    public function testCaseRules()
    {
        //The length of the first image = the width of the second image with 5% error.
        //The width of the first image = the length of the second image with 5% error.
        if ($this->getTwoNumbersDiffInPercents($this->jsonFilesContent[1]->ImageLength, $this->jsonFilesContent[2]->ImageWidth) > 5 ||
            $this->getTwoNumbersDiffInPercents($this->jsonFilesContent[1]->ImageWidth, $this->jsonFilesContent[2]->ImageLength) > 5
        ) {
            $this->reasons[] = 'Either the a5 sample was rotated during the first scanning or was not during the second scanning.';
        }

        //The length of the third image = the width of the fourth image with 5% error.
        //The width of the third image = the length of the fourth image with 5% error.
        if ($this->getTwoNumbersDiffInPercents($this->jsonFilesContent[3]->ImageLength, $this->jsonFilesContent[4]->ImageWidth) > 5 ||
            $this->getTwoNumbersDiffInPercents($this->jsonFilesContent[3]->ImageWidth, $this->jsonFilesContent[4]->ImageLength) > 5
        ) {
            $this->reasons[] = 'Either the a4 sample was rotated during the first scanning or was not during the second scanning.';
        }
    }

    public function getFilesNumber()
    {
        return 4;
    }
}