<?php

namespace App;

class CA05 extends TestCaseValidationAbstract
{
    /**
     * Server validation for CA-05 test case
     */
    public function testCaseRules()
    {
        //The first image PixelType = 0
        if ($this->jsonFilesContent[1]->PixelType != 0) {
            $this->reasons[] = 'The first image color mode should be Black & White.';
        }

        //The second image PixelType = 2
        if ($this->jsonFilesContent[2]->PixelType != 2) {
            $this->reasons[] = 'The second image color mode should be Color.';
        }

        //The third image PixelType = 2
        if ($this->jsonFilesContent[3]->PixelType != 2) {
            $this->reasons[] = 'The third image color mode should be Color.';
        }

        //The fourth image PixelType = 2
        if ($this->jsonFilesContent[4]->PixelType != 0) {
            $this->reasons[] = 'The fourth image color mode should be Black & White.';
        }
    }

    public function getFilesNumber()
    {
        return 4;
    }
}