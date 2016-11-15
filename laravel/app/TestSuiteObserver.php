<?php

namespace App;


class TestSuiteObserver
{
    use CloudSearchDomainTrait;

    public function saved(LaravelTestSuite $testSuite)
    {
        $cloudSearchClient = $this->getFulltextEndpointCloudSearchClient();

        $description = $testSuite->description;
        $visibility = 'Community';

        $group = Community::find($testSuite->community_id);
        $communityNames = array();
        if ($group) {
            $communityNames[] = $group->title;
            $groups['groups'][] = $group->id;
        }
        if (empty($communityNames)) {
            $communityNames = ['TWAIN'];
            $groups['groups'] = [Community::findBySlug('twain')->id];
        }

        $productData = array(
            'community' => $communityNames,
            'last_updated_date' => date('Y-m-d\TH:i:s', strtotime($testSuite->updated_at)) . 'Z',
            'post_author_name' => '',
            'post_author_id' => 0,
            'post_content' => (string)$description,
            'post_status' => 'Published',
            'post_title' => $testSuite->full_name,
            'post_type' => 'Test Suite',
            'post_id' => $testSuite->id,
            'visibility' => 1,
            'community_id' => $groups['groups'],
            'for_search' => $description . ' Product ' . $testSuite->full_name . ' ' . implode(' ', $communityNames) . ' ' . $visibility,
            'link' => $testSuite->getUrl()
        );

        $cloudSearchClient->uploadDocuments([
            'documents' => json_encode([['type' => 'add', 'id' => $testSuite->id, 'fields' => $productData]]),
            'contentType' => 'application/json'
        ]);

        $scenariosData = [];
        foreach ($testSuite->scenarios AS $scenario) {
            if ($scenario->code == 'Default') {
                continue;
            }
            $testSuiteCommunity = Community::find($testSuite->community_id);
            $communityNames = $groups = [];
            if ($testSuiteCommunity) {
                $communityNames[] = $testSuiteCommunity->title;
                $groups['groups'][] = $testSuiteCommunity->id;
            }
            if (empty($communityNames)) {
                $groups['groups'] = [Community::findBySlug('twain')->id];
            }
            $scenarioData = array(
                'community' => $communityNames,
                'last_updated_date' => date('Y-m-d\TH:i:s', strtotime($scenario->updated_at)) . 'Z',
                'post_author_name' => '',
                'post_author_id' => 0,
                'post_content' => $scenario->description,
                'post_status' => 'Published',
                'post_title' => $scenario->code,
                'post_type' => 'Test Scenario',
                'post_id' => $scenario->id,
                'visibility' => 2,
                'community_id' => $groups['groups'],
                'for_search' => $scenario->description . 'Test Scenario' . $scenario->code,
                'link' => $testSuite->getUrl()
            );
            array_push($scenariosData, array('type' => 'add', 'id' => 'scenario_' . $scenario->id, 'fields' => $scenarioData));

            $cloudSearchClient->uploadDocuments([
                'documents' => json_encode($scenariosData),
                'contentType' => 'application/json'
            ]);
        }
    }
}
