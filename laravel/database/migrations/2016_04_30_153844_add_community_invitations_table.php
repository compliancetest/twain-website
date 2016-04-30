<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCommunityInvitationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('community_invitations', function (Blueprint $table) {
            $table->primary('id');
            $table->uuid('id');
            $table->uuid('community_id');
            $table->integer('invited_by_user_id');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('invitation_email');
            $table->string('registered_email');
            $table->string('registered_user_id');
            $table->boolean('status');
            $table->timestamps();
        });

         Schema::table('community_invitations', function($table) {
            $table->foreign('community_id')->references('id')->on('communities')->onDelete('cascade');
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
