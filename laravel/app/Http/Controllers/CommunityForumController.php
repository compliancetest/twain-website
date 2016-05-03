<?php

namespace App\Http\Controllers;

use App\Community;
use App\ForumThread;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Auth;

class CommunityForumController extends Controller
{
    public function addThread($communitySlug, Request $request)
    {
        $community = Community::findBySlug($communitySlug);
        $this->validate($request, [
            'title' => 'required|min:5'
        ]);

        $community->threads()->create([
            'author_id' => Auth::user()->ID,
            'title' => $request->get('title'),
            'content' => $request->get('content')
        ]);
        return ['redirect_to' => $community->getUrl() . 'forum'];
    }

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
        return ['redirect_to' => $community->getUrl() . 'forum/' . $threadSlug];
    }
}
