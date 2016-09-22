<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SurveymonkeyCreadentialsChanges extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Illuminate\Support\Facades\DB::statement("UPDATE  `twain`.`wp_options` SET  `option_value` =  'fzh83hsy33njkvupsdj7a8gz' WHERE  `wp_options`.`option_name` ='surveymonkey_key';");
        \Illuminate\Support\Facades\DB::statement("UPDATE  `twain`.`wp_options` SET  `option_value` =  'W9.5P1TRiltxWRdQoyKHwkMLAW5N00OBvY9ppaAWceYoImPAp4Arei6K4LFRA8sAPGRlqW0uPlIxk28TUR9x1QTiexrqwYfnh6r7MIaCz13Yi1.bEnBasNWOeYWFcC6PUeVnLTJ0TNKJHD8hIf4jxwyNctQ1nBcMXWAmJ6kAsFw=' WHERE  `wp_options`.`option_name` ='surveymonkey_token';");
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
