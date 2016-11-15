<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCloudsearchFulltext extends Migration
{

    use \App\CloudSearchDomainTrait;

    public function __construct()
    {
        $this->_client = $this->getFulltextEndpointCloudSearchClient();
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $results = $this->_client->search([
            'size' => 10000,
            'query' => 'matchall',
            'queryParser' => 'structured',
        ]);

        $data = $response_data = array();

        foreach ($results['hits']['hit'] as $row) {
            array_push($data, array('type' => 'delete', 'id' => $row['id']));
        }
        if (!empty($data)) {
            $this->_client->uploadDocuments(array('documents' => json_encode($data), 'contentType' => 'application/json'));
        }
        $entries = \App\LaravelTestCase::all();
        foreach ($entries AS $entry) {
            $entry->timestamps = false;
            $entry->save();
        }
        $entries = \App\LaravelTestSuite::all();
        foreach ($entries AS $entry) {
            $entry->timestamps = false;
            $entry->save();
        }
        $entries = \App\Product::all();
        foreach ($entries AS $entry) {
            $entry->timestamps = false;
            $entry->save();
        }

        $entries = \App\Post::whereIn('post_type', ['press-release', 'blog', 'event', 'page', 'link'])->get();
        foreach ($entries AS $entry) {
            $entry->timestamps = false;
            $entry->save();
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
