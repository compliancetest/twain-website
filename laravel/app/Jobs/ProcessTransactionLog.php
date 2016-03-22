<?php

namespace App\Jobs;

use App\Jobs\Job;
use Aws\Laravel\AwsFacade;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProcessTransactionLog extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    public $fileName;

    /**
     * ProcessTransactionLog constructor.
     * @param $fileName
     */
    public function __construct($fileName)
    {
        $this->fileName = $fileName;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
//        $fileName = '15235285_Document_Imaging.zip';//basename($this->fileName);
//        $filePath = '/var/www/website/laravel/storage/app/public/transactions/';//base_path() . '/storage/app/public/transactions/';
//        $s3 = AwsFacade::createClient('s3');
//        $s3->getObject(array(
//            'Bucket' => 'data.twain.gosource.com.au',
//            'Key' => $this->fileName,
//            'SaveAs' => $filePath . $fileName
//        ))['Body'];
//        $za = new \ZipArchive();
//        $za->open($filePath . $fileName);
//        $za->extractTo($filePath);
//        $za->close();
////        @unlink($filePath.$fileName);
//
//        $objects = $this->scanDir($filePath);
//        foreach ($objects as $name => $object) {
//            if(strpos($name, '/.') || strpos($name, '/..')){
//                continue;
//            }
//            var_dump($object);
//        }
    }

    private function scanDir($path)
    {
        return new \IteratorIterator(new \DirectoryIterator($path));
    }
}
