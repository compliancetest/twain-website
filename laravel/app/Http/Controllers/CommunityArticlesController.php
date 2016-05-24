<?php

namespace App\Http\Controllers;

use App\CommunityArticle;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Community;
use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;

class CommunityArticlesController extends Controller
{

    protected $community;

    public function __construct(Request $request)
    {
        $this->community = Community::findBySlug($request->route('community'));
    }

    /**
     * Create new article form
     * @param $communitySlug
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create($communitySlug)
    {
        $community = $this->community;
        return view('pages.communities.articles.create', compact('community'));
    }

    /**
     * Edit article form
     * @param $communitySlug
     * @param $articleSlug
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($communitySlug, $articleSlug)
    {
        $article = CommunityArticle::findBySlug($articleSlug);
        $community = $this->community;
        return view('pages.communities.articles.edit', compact('community', 'article'));
    }

    public function show($communitySlug, $articleSlug)
    {
        $article = CommunityArticle::findBySlug($articleSlug);
        $community = $this->community;
        return view('pages.communities.articles.show', compact('community', 'article'));
    }

    public function store(Requests\ArticleRequest $request)
    {
        $modelData = $request->all();
        $modelData['creator_id'] = Auth::user()->ID;
        $modelData['slug'] = CommunityArticle::getUniqueSlug(new CommunityArticle(), $modelData['title']);

        $article = $this->community->articles()->create($modelData);

        $this->_handleAttachments($request, $article);
        addMessage('You successfully added new article. ');
        return Redirect::to(getSiteUrl() . '/communities/'.$this->community->slug.'/wiki');
    }

     public function update($communitySlug, $articleSlug, Requests\ArticleRequest $request)
    {
        $article = CommunityArticle::findBySlug($articleSlug);
        $article->fill($request->all());
        $article->save();

        $this->_handleAttachments($request, $article);
        addMessage('You successfully updated article. ');
        return Redirect::to(getSiteUrl() . '/articles/' . $communitySlug .'/'.$articleSlug);
    }

    public function destroyattachment($communitySlug, $articleSlug, $attachmentId)
    {
        CommunityArticle::findBySlug($articleSlug)->attachments()->find($attachmentId)->delete();
        return JsonResponse::create(['status' => 'success']);
    }

    private function _handleAttachments($request, $article)
    {
        if($request->file('attachments')) {
            foreach ($request->file('attachments') as $file) {
                if($file) {
                    $data = [
                        'filename' => $file->getClientOriginalName(),
                        'location' => 'communities/articles/' . $article->id . '/' . $file->getClientOriginalName(),

                    ];
                    Storage::put($data['location'], file_get_contents($file));
                    $article->attachments()->create($data);
                }
            }
        }
    }
}
