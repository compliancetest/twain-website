<?php

namespace App;

class PostObserver
{
    use CloudSearchDomainTrait;

    /**
     * Listen to the Post saved event.
     * @param Post $post
     */
    public function saved(Post $post)
    {
        if (in_array($post->post_type, ['press-release', 'blog', 'event', 'page', 'link'])) {

            if (in_array($post->post_title, array('add-new-service', 'Edit Test Case', 'Edit Product and Service', 'Edit Test Suite',
                'Message Envelope', 'View Validation Error', 'My Messages', 'Inbox', 'Sentbox', 'Compose', 'View', 'Members', 'Get Profile', 'View Message Template',
                'My Organization', 'Test Suites', 'get-profile-meta', 'Users', 'Edit Service', 'Add New Product and Service', 'Reset Password', 'Sitemap',
                'Add New Test Case', 'Add New Test Suite', 'Add new service', 'search-registry', 'My Test Data', 'Agreements', 'communities_old', 'License Agreement',
                'More Reasons', 'Forum', 'login', 'My Organisation', 'My Profile', 'My Test Results', 'Test Suite Coverage', 'My Products', 'My Support Tickets',
                'My Test Suites', 'My Communities', 'Search Results'))) {
                return;
            }
            //save product data to Fulltext search domain
            $cloudSearchClient = $this->getFulltextEndpointCloudSearchClient();

            $visibility = 1;
            if (in_array($post->post_type, array('event', 'blog', 'press-release', 'link'))) {
                $communityId = PostMeta::where(['post_id' => $post->ID, 'meta_key' => 'blog_community_id'])->first();
                if (!$communityId || !Community::find($communityId)) {
                    $twain = Community::findBySlug('twain');
                    $communityNames = [$twain->title];
                    $communityIds = [$twain->id];
                    $visibility = 1;
                } else {
                    $communityNames = [Community::find($communityId)->title];
                    $communityIds = [$communityId];
                    $visibility = 2;
                }
            } else {
                $twain = Community::findBySlug('twain');
                $communityNames = [$twain->title];
                $communityIds = [$twain->id];
                $visibility = 2;
            }

            //this pages should be visible only to logged in users
            if (in_array($post->post_title, array('Add New Product and Service', 'Add New Test Case', 'Add New Test Suite', 'My Profile',
                'My Transaction Log', 'Test Suite Coverage', 'My Products', 'My Support Tickets', 'My Test Suites', 'My Communities',
                'Add new service', 'Add New Test Case', 'Add New Test Suite'))) {
                $visibility = 2;
            }
            $data = array(
                'community' => $communityNames,
                'last_updated_date' => date('Y-m-d\TH:i:s', strtotime($post->post_date)) . 'Z',
                'post_author_name' => User::find($post->post_author)->getFullName(),
                'post_author_id' => $post->post_author,
                'post_content' => trim(str_replace(array('Back to Documentation Home', 'Back to documentation home'), '', $post->post_content)),
                'post_status' => $post->post_status,
                'post_title' => $post->post_title,
                'post_type' => $this->processPostType($post->post_type),
                'post_id' => $post->ID,
                'visibility' => $visibility,
                'community_id' => $communityIds,
                'for_search' => trim(str_replace(array('Back to Documentation Home', 'Back to documentation home'), '', $post->post_content)),
                'link' => getSiteUrl() . '/' . $post->post_name
            );

            $cloudSearchClient->uploadDocuments([
                'documents' => json_encode([['type' => 'add', 'id' => $post->ID, 'fields' => $data]]),
                'contentType' => 'application/json'
            ]);
        }
    }

    public function processPostType($postType)
    {
        switch ($postType) {
            case 'page':
                $name = 'Page';
                break;
            case 'event':
                $name = 'Event';
                break;
            case 'blog':
                $name = 'Blog';
                break;
            case 'press-release':
                $name = 'Press Release';
                break;
            case 'link':
                $name = 'Link';
                break;
        }
        return $name;
    }
}
