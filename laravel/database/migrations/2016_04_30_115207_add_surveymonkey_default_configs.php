<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSurveymonkeyDefaultConfigs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \App\WpOptions::create(['option_name' => 'surveymonkey_token', 'option_value' => 'kxQ8BKW6.SnO.uBXBiVw3nEyZnMtsSOJ.7KbJ9ZUP7BPluya.liEjbmnL9bPfLE4O7i-vyt8ERCQO0W1d461sJChhMkbEf2kH6EsiDtu8sFxECbmvwhOrywc4uLjiDJcYsYvhuHodn0YTanQOOGSztTeu5G1SoAo05CjBOYIWiugBWURPKeP4HAhfz3GMZuK04Jw3jFzzeDKM9gIR4yIXpkggUo-5P2iPt5CoYtX9n0=']);
        \App\WpOptions::create(['option_name' => 'surveymonkey_secret', 'option_value' => 'TfhtSEGsTMNEaQsqtvZSeBdhg6fM8rYg']);
        \App\WpOptions::create(['option_name' => 'surveymonkey_key', 'option_value' => 'gme37dbmx434hhwagr3q4ude']);
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
