<?php

namespace App;

class IP01 extends TestCaseValidationAbstract
{

    /**
     * Server validation for CA-03 test case
     */
    public function testCaseRules()
    {
        //The ratio between length and width or width and length should be more than 2.
        if (($this->jsonFilesContent[1]->ImageWidth / $this->jsonFilesContent[1]->ImageLength < 2) &&
            ($this->jsonFilesContent[1]->ImageLength / $this->jsonFilesContent[1]->ImageWidth < 2)
        ) {
            $this->reasons[] = 'Condition "Ratio between length and width or width and length is more than 2" was not met. Please check that a correct sample was scanned.';
        }
    }

    public function getFilesNumber()
    {
        return 1;
    }
}