<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SurveymonkeyCreadentialsChanges1 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Illuminate\Support\Facades\DB::statement("UPDATE  `twain`.`wp_options` SET  `option_value` =  'c9grzrwahnxenyxehnh227eq' WHERE  `wp_options`.`option_name` ='surveymonkey_key';");
        \Illuminate\Support\Facades\DB::statement("UPDATE  `twain`.`wp_options` SET  `option_value` =  'vjERcz.Ejx9PTpRjtBe3YYJNcGyJpgyawX40cSgjnT-.QVXwxO2-rbLqfqGvtHPgdqE.NBexbPmFrsfG3ZIJ4zoXX19k031XcAX9CxFcuDe159p1yqiPY8iGnN6H952MQ9eziU1ZVgV3vZnv2lVhaRsMFApv8gQH3FJaexJaGLQpbFTJSwG0wRyhTZ6J68fhjSbh8HblrfWFqY8DsgLyqUNAN5vWSVWTu-3YTl15sUc=' WHERE  `wp_options`.`option_name` ='surveymonkey_token';");
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
