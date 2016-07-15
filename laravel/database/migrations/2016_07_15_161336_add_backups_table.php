<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBackupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('communities_profiles_backups', function (Blueprint $table) {
            $table->primary('id');
            $table->uuid('id');
            $table->uuid('community_id');
            $table->integer('user_id')->unsigned();
            $table->string('s3_key');
            $table->timestamps();
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
