<?php

namespace App\Http\Controllers;

use App\Community;
use App\ForumThread;
use App\ForumThreadPost;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

class CommunityForumController extends Controller
{

    /**
     * Create new thread on community forum page
     * @param $communitySlug
     * @param Request $request
     * @return array
     */
    public function addThread($communitySlug, Request $request)
    {
        $community = Community::findBySlug($communitySlug);
        $this->validate($request, [
            'title' => 'required|min:5'
        ]);

        $thread = $community->threads()->create([
            'author_id' => Auth::user()->ID,
            'title' => $request->get('title'),
            'content' => $request->get('content')
        ]);

        addMessage('Thread was added successfully');

        $this->_uploadToCloudSearch($thread, $community);

        return ['redirect_to' => $community->getUrl() . 'forum'];
    }

    public function editThread($communitySlug, $threadId)
    {
        $community = Community::findBySlug($communitySlug);
        $thread = ForumThread::find($threadId);
        return view('pages.communities.forum.edit_thread', compact('community', 'thread'));
    }

    public function updateThread($communitySlug, $threadId, Request $request)
    {
        $community = Community::findBySlug($communitySlug);
        $this->validate($request, [
            'title' => 'required|min:5'
        ]);

        $thread = $community->threads()->find($threadId);
        if($thread){
            if ($thread->author_id != Auth::user()->ID && !$community->isAdmin()) {
                return response()->json(array('success' => false), 403);
            }
            $thread->update([
                'title' => $request->get('title'),
                'content' => $request->get('content')
            ]);

            $this->_uploadToCloudSearch($thread, $community);
            addMessage('Thread was updated successfully');
        }

        return ['redirect_to' => $community->getUrl() . 'forum'];
    }

    /**
     * Delete thread
     * @param $communitySlug
     * @param $threadID
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteThread($communitySlug, $threadID)
    {
        $community = Community::findBySlug($communitySlug);
        $thread = ForumThread::find($threadID);
        if ($thread->author_id != Auth::user()->ID && !$community->isAdmin()) {
            return response()->json(array('success' => false), 403);
        }

        $this->_deleteFromCloudSearch($thread->id);
        $thread->delete();
        return response()->json(array('success' => true));
    }

    /**
     * Add reply to thread
     * @param $communitySlug
     * @param $threadSlug
     * @param Request $request
     * @return array
     */
    public function addThreadPost($communitySlug, $threadSlug, Request $request)
    {
        $community = Community::findBySlug($communitySlug);
        $this->validate($request, [
            'content' => 'required|min:5'
        ]);

        $thread = ForumThread::findBySlug($threadSlug);

        $thread->replies()->create([
            'author_id' => Auth::user()->ID,
            'content' => $request->get('content')
        ]);

        $thread->updated_at = Carbon::now();
        $thread->save();

        $this->_uploadToCloudSearch($thread, $community);

        addMessage('Post was added successfully');

        return ['redirect_to' => $community->getUrl() . 'forum/' . $threadSlug];
    }

    public function editThreadPost($communitySlug, $postId)
    {
        $community = Community::findBySlug($communitySlug);
        $post = ForumThreadPost::find($postId);
        return view('pages.communities.forum.edit_post', compact('community', 'post'));
    }

    public function updateThreadPost($communitySlug, $postId, Request $request)
    {
        $community = Community::findBySlug($communitySlug);
        $this->validate($request, [
            'content' => 'required|min:5'
        ]);

        $post = ForumThreadPost::find($postId);
        if($post){
            if ($post->author_id != Auth::user()->ID && !$community->isAdmin()) {
                return response()->json(array('success' => false), 403);
            }
            $post->update([
                'content' => $request->get('content')
            ]);

            addMessage('Post was updated successfully');
        }
        $thread = $post->thread;
        $this->_uploadToCloudSearch($thread, $community);

        return ['redirect_to' => $community->getUrl() . 'forum/' . $thread->slug];
    }

    /**
     * Delete post from thread page
     * @param $communitySlug
     * @param $postId
     * @return \Illuminate\Http\JsonResponse
     */
    public function deletePost($communitySlug, $postId)
    {
        $community = Community::findBySlug($communitySlug);
        $post = ForumThreadPost::find($postId);
        if ($post->author_id != Auth::user()->ID && !$community->isAdmin()) {
            return response()->json(array('success' => false), 403);
        }

        $threadId = $post->thread->id;
        $post->delete();
        $this->_uploadToCloudSearch(ForumThread::find($threadId), $community);

        return response()->json(array('success' => true));
    }

    /**
     * Upload thread data to cloudSearch domain
     * @param $thread
     * @param $community
     * @return array
     */
    private function _uploadToCloudSearch($thread, $community)
    {
        $domain = App::make('aws')->createClient('cloudSearchDomain', ['endpoint' => Config::get('aws.domain.' . getenv('ENVIRONMENT'))]);

        $messages = '';
        foreach ($thread->replies as $reply) {
            $messages .= ' ' . $reply->content . ' ';
        }
        $tempData = array(
            'community' => [$community->title],
            'last_updated_date' => date('Y-m-d\TH:i:s') . 'Z',
            'post_author_name' => cp_get_user_fullname($thread->author_id),
            'post_author_id' => $thread->author_id,
            'post_content' => $thread->content,
            'post_status' => 'publish',
            'post_title' => $thread->title,
            'post_type' => 'Forum Post',
            'post_id' => 0,
            'visibility' => 1,
            'community_id' => [$community->id],
            'for_search' => $thread->content . ' ' . 'Forum post' . ' ' . $thread->title . ' ' . $messages .  ' ' . cp_get_user_fullname($thread->author_id),
            'link' => getSiteUrl() . '/communities/' . $community->slug . '/forum/' . $thread->slug
        );
        $results = [array('type' => 'add', 'id' => 'forum_post' . '_' . str_replace('-', '_', $thread->id), 'fields' => $tempData)];

        $data = $domain->uploadDocuments(array('documents' => json_encode($results), 'contentType' => 'application/json'));
        return array(
            'Status' => $data->getPath('status'),
            'Added' => $data->getPath('adds'),
            'Deleted' => $data->getPath('deletes')
        );
    }

    /**
     * Delete thread from cloudSearch
     * @param $threadId
     * @return array
     */
    private function _deleteFromCloudSearch($threadId)
    {
        $domain = App::make('aws')->createClient('cloudSearchDomain', ['endpoint' => Config::get('aws.domain.' . getenv('ENVIRONMENT'))]);
        $results = [array('type' => 'delete', 'id' => 'forum_post' . '_' . str_replace('-', '_', $threadId))];
        $data = $domain->uploadDocuments(array('documents' => json_encode($results), 'contentType' => 'application/json'));

        return array(
            'Status' => $data->getPath('status'),
            'Added' => $data->getPath('adds'),
            'Deleted' => $data->getPath('deletes')
        );
    }
}
