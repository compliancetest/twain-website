<?php

namespace App;


trait TransactionS3LinkTrait
{

    /**
     * Generate s3 link to transaction file
     * @param $fileName
     * @return string
     */
    public function getS3Link($fileName)
    {
        return 'https://s3-'.config('env.bucket.region').'.amazonaws.com/'.config('env.bucket.transactions').'/' . $fileName;
    }
}