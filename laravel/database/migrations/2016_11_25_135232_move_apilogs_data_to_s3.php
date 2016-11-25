<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MoveApilogsDataToS3 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!file_exists(storage_path() . "/apilogs")) {
            mkdir(storage_path() . "/apilogs", 0777);
        }
        if(!file_exists(storage_path() . "/apilogs/apilogs")) {
            mkdir(storage_path() . "/apilogs/apilogs", 0777);
        }
        $path = storage_path() . "/apilogs/";
        $s3 = \Aws\Laravel\AwsFacade::createClient('s3');
        foreach (\App\ApiLog::all() as $apilog){
            if(!file_exists(storage_path() . "/apilogs/apilogs/" . $apilog->id)) {
                mkdir(storage_path() . "/apilogs/apilogs/" . $apilog->id, 0777);
            }
            file_put_contents($path . 'apilogs/'. $apilog->id . '/request.json', $apilog->request);
            file_put_contents($path .  'apilogs/'. $apilog->id . '/response.json', $apilog->response);
        }
        try {
            $status = $s3->uploadDirectory($path, config('env.bucket.website'));

        } catch (Exception $e) {
        }

        Schema::table('api_logs', function ($table) {
            $table->dropColumn(['request', 'response']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
