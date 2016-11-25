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
        foreach (\App\ApiLog::all() as $apilog){
            \Illuminate\Support\Facades\Storage::put('apilogs/' . $apilog->id . '/request.json', $apilog->request);
            \Illuminate\Support\Facades\Storage::put('apilogs/' . $apilog->id . '/response.json', $apilog->response);
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
