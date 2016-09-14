<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ReloadFulltestCloudsearchData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        require_once __DIR__ . '/../../../wp-load.php';
        $fulltextSearchDomain = new FulltextSearch();
        $fulltextSearchDomain->fullDelete();
        $fulltextSearchDomain->fullUpload();
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
