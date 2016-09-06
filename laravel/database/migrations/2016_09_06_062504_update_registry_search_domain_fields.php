<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateRegistrySearchDomainFields extends Migration
{
    use \App\CloudSearchDomainTrait;
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->_client = $this->getDocumentEndpointCloudSearchClient();

        //delete existing data

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


        $testPlans = \App\TestPlan::all();
        foreach ($testPlans AS $testPlan){
            $testPlan->timestamps = false;
            $testPlan->save();
        }

        //delete existing claims
        $claims = \App\Claim::all();
        foreach ($claims AS $claim){
            $claim->timestamps = false;
            $claim->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //delete existing data

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
    }
}
