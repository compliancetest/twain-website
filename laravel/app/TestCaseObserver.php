<?php

namespace App;

class TestCaseObserver
{
    use CloudSearchDomainTrait;

    public function saved(LaravelTestCase $testCase)
    {
        $cloudSearchClient = $this->getFulltextEndpointCloudSearchClient();

        $description = $testCase->description;
        $visibility = 'Community';

        $group = Community::find($testCase->community_id);
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
            'last_updated_date' => date('Y-m-d\TH:i:s', strtotime($testCase->updated_at)) . 'Z',
            'post_author_name' => '',
            'post_author_id' => 0,
            'post_content' => (string)$description,
            'post_status' => 'Published',
            'post_title' => (string) $testCase->full_name,
            'post_type' => 'Test Case',
            'post_id' => $testCase->id,
            'visibility' => 2,
            'community_id' => $groups['groups'],
            'for_search' => $description . ' Product ' . $testCase->full_name . ' ' . implode(' ', $communityNames) . ' ' . $visibility,
            'link' => $testCase->getUrl()
        );
        $cloudSearchClient->uploadDocuments([
            'documents' => json_encode([['type' => 'add', 'id' => $testCase->id, 'fields' => $productData]]),
            'contentType' => 'application/json'
        ]);
    }
}
