<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MigrateProductsSuitesCases extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->primary('id');
            $table->uuid('id');
            $table->string('slug');
            $table->string('title');
            $table->text('description');
            $table->string('visibility');
            $table->string('type');
            $table->string('version');
            $table->string('manufacturer');
            $table->string('protocol_version');
            $table->string('model');
            $table->integer('organisation_id');
            $table->text('capabilities');
            $table->text('suites');
            $table->timestamps();
        });

        Schema::create('test_suites', function (Blueprint $table) {
            $table->primary('id');
            $table->uuid('id');
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
