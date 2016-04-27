<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ExampleTest extends TestCase
{

    function setUp(){
        @session_start();
        parent::setUp();
    }

   /** @test */
    public function communities()
    {
        $this->visit( home_url() . '/communities');
    }
}
