<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDefaultProtocolVersionToProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach( \App\Post::where(['post_type' => 'product-service'])->get() as $product){
            $product->postmeta()->create(['meta_key' => 'protocol_version', 'meta_value' => '2.2']);
        }
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
