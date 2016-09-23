<?php

namespace App;

class CA01 extends TestCaseValidationAbstract
{

    /**
     * Server validation for CA-03 test case
     */
    public function testCaseRules()
    {
        if (($this->jsonFilesContent[1]->ImageWidth + $this->jsonFilesContent[1]->ImageLength) <=
            ($this->jsonFilesContent[2]->ImageWidth + $this->jsonFilesContent[2]->ImageLength)
        ) {
            $this->reasons[] = 'Condition "The dimensions of the first image bigger than the dimensions of the second one." was not met.';
        }
    }
}