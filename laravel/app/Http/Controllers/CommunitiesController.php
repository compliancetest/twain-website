<?php

namespace App\Http\Controllers;

use App\Post;
use App\PostMeta;
use Aws\Laravel\AwsFacade;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class CommunitiesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $fileName = '1_v2.1.3_2016-04-04_032442.zip';//basename($this->fileName);
        $filePath = base_path() . '/storage/app/public/transactions/';
//        $s3 = AwsFacade::createClient('s3');
//        $s3->getObject(array(
//            'Bucket' => 'data.twain.gosource.com.au',
//            'Key' => 'local/transactions/4962/1234567889/1_v2.1.3_2016-04-04_032442.zip',
//            'SaveAs' => $filePath . $fileName
//        ))['Body'];
//        $za = new \ZipArchive();
//        $za->open($filePath . $fileName);
//        $za->extractTo($filePath);
//        $za->close();
//        @unlink($filePath.$fileName);

        $rootFolder = $this->getFolderName($filePath);
        $productIdentifier = explode('_', $rootFolder, 2)[1];

        $where = [
            'meta_key' => 'product_id',
            'meta_value' => $productIdentifier
        ];
        $productId = PostMeta::where($where)->first()->post_id;
        $product = Post::find($productId);
        var_dump($product);

        $testSuiteFolder = $this->getFolderName($filePath . $rootFolder);
        $testSuite = Post::where(['post_name' => $testSuiteFolder, 'post_type' => 'test-suite'])->first()->toArray();
        var_dump($testSuite);

        $testCaseFolder = $this->getFolderName($filePath . $rootFolder. '/'. $testSuiteFolder);
        $testCase = Post::where(['post_name' => $testCaseFolder, 'post_type' => 'test-case'])->first()->toArray();
        var_dump($testCase);

        $executionIdFolder = $this->getFolderName($filePath . $rootFolder. '/'. $testSuiteFolder . '/'.$testCaseFolder);
        $executionId = explode('_', $executionIdFolder, 2)[1];
        var_dump($executionId);


        //execution log
        $executionLogData = json_decode(file_get_contents($filePath . $rootFolder. '/'. $testSuiteFolder . '/'.$testCaseFolder.'/'.$executionIdFolder.'/execution_log/execution_log.json'), true);
        var_dump($executionLogData);
        die;
        $objects = new \RecursiveDirectoryIterator($filePath . $object->getFileName(), \RecursiveDirectoryIterator::SKIP_DOTS);
        $rootPath = $filePath . $object->getFileName();
        foreach ($objects as $name => $object) {
            if ($object->getFileName() == '..' || $object->getFileName() == '.' || $object->getFileName() == '.gitignore') {
                continue;
            }
            $objects = new \RecursiveDirectoryIterator($filePath . $object->getFileName(), \RecursiveDirectoryIterator::SKIP_DOTS);
            foreach ($objects as $name => $object) {
                if ($object->getFileName() == '..' || $object->getFileName() == '.' || $object->getFileName() == '.gitignore') {
                    continue;
                }
                var_dump($object->getFileName());
            }
        }
        die;
    }

    private function getFolderName($path)
    {
        $objects = new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS);
        foreach ($objects as $name => $object) {

            if ($object->getFileName() == '..' || $object->getFileName() == '.' || $object->getFileName() == '.gitignore') {
                continue;
            }
            return $object->getFileName();
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
