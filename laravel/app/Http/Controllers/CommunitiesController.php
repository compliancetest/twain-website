<?php

namespace App\Http\Controllers;

use App\Community;
use App\CommunityMembers;
use App\CommunityMeta;
use App\ForumThread;
use App\ForumThreadRead;
use App\Post;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;

class CommunitiesController extends Controller
{
    /**
     * Show communities list
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $communities = Community::all();
        return view('pages.communities.index', compact('communities'));
    }

    /**
     * Create new community form
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('pages.communities.create');
    }

    /**
     * Save new community handler
     * @param Requests\CommunityRequest $request
     * @return mixed
     */
    public function store(Requests\CommunityRequest $request)
    {
        $modelData = $request->all();
        $modelData['creator_id'] = Auth::user()->ID;
        $modelData['slug'] = Community::getUniqueSlug(new Community(), $modelData['title']);
        $modelData['articles_status'] = true;
        if(!$request->has('articles_enabled')){
            $modelData['articles_status'] = false;
        }

        $model = Community::create($modelData);

        $this->handleImage($request, $model);

        $model->members()->create([
            'community_id' => $model->id,
            'user_id' => Auth::user()->ID,
            'is_admin' => true,
            'is_confirmed' => true
        ]);

        return Redirect::to(getSiteUrl() . '/communities');
    }

    /**
     * @param $slug community slug
     * @param string $action - tab name
     * @return $this
     */
    public function show($slug, $action = 'testsuites', $threadSlug = false)
    {
        $community = Community::findBySlug($slug);
        $data = [
            'community' => $community,
            'action' => $action,
            'isAdmin' => $community->isAdmin(),
        ];
        if ($action == 'testsuites') {
            $data['testSuites'] = Post::getCommunityTestSuites($community->id);
        }
        if ($action == 'forum') {
            $data['threads'] = $community->threads()->with('user')->get();
            if($threadSlug){
                $thread = ForumThread::findBySlug($threadSlug);
                $data['thread'] = ForumThread::findBySlug($threadSlug);
                $data['threadPosts'] = $thread->replies()->with('user')->get();

                ForumThreadRead::firstOrNew(['user_id' => Auth::user()->ID, 'thread_id' => $thread->id]);
            }
        }
         if ($action == 'wiki') {
            $data['articles'] = $community->articles()->with('attachments')->orderBy('updated_at')->get();
        }
        if ($action == 'testdata') {
            $data['instances'] = getCommunityProfileInstatnces($community->id);
        }
        if ($action == 'downloads') {
            $data['downloads'] = $community->downloads;
        }
        if ($action == 'surveys') {
            $surveys = [];
            $surveyMonkey = new \SurveyMonkey(get_option('surveymonkey_key'), get_option('surveymonkey_token'));
            foreach($surveyMonkey->getSurveyList()['data'] as $survey){
                $collectors = $surveyMonkey->getCollectorList($survey['id']);
                if($collectors['data']){
                    foreach($collectors['data'] as $col) {
                        $collector = $surveyMonkey->getCollector($col['id']);
                        if($collector['data']['type'] == 'weblink') {
                            $collectorCounter = $surveyMonkey->getCollectorResponses($col['id']);
                            $userResponse = $surveyMonkey->getCollectorResponses($col['id'], ['ip' => getClientIP()]);
                            $surveys[] = [
                                'title' => $survey['title'],
                                'id' => $survey['id'],
                                'url' => $collector['data']['url'],
                                'date_created' => date('Y-m-d', strtotime($collector['data']['date_created'])),
                                'date_close' => isset($collector['data']['status']) && $collector['data']['status'] == 'closed' ? date('Y-m-d', strtotime($collector['data']['date_modified'])) : false,
                                'is_active' => strtotime($collector['data']['close_date']) < mktime(),
                                'responses_number' => $collectorCounter['total'],
                                'user_responded' => $userResponse['total'] > 0 ? true : false,
                            ];
                        }
                    }
                }
            }
            $data['surveys'] = $surveys;
        }
        if ($action == 'admin') {
            if(!$community->isAdmin()){
                return Redirect::to(getSiteUrl() . '/communities');
            }
            $data['communityMeta'] = $community->meta->keyBy('meta_key');
            $data['profileTypes'] = getCommunityProfileTypes($community->id);
            $data['invitedUsers'] = $community->invitations;
            $data['membershipRequests'] = $community->getMembershipRequests();
        }
        return view('pages.communities.show')->with($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param $slug
     * @param Requests\CommunityRequest|Request $request
     * @return \Illuminate\Http\Response
     * @internal param int $id
     */
    public function update($slug, Request $request)
    {
        $community = Community::findBySlug($slug);

        if ($request->file('image')) {
            $this->validate($request, ['image' => 'image']);
            $this->handleImage($request, $community);
        }
        if ($request->has('title') && $request->has('description')) {
            $community->update(['title' => $request->get('title'), 'description' => $request->get('description')]);
        }

        if ($request->has('status')) {
            $community->update(['status' => $request->get('status')]);
        }

        $textFields = ['terms_and_conditions', 'license_agreements', 'obligation_for_claim', 'notification_email_of_changes'];
        foreach ($textFields as $textField) {
            if ($request->has($textField)) {
                CommunityMeta::updateOrCreate(['community_id' => $community->id, 'meta_key' => $textField], ['meta_value' => $request->get($textField)]);
            }
        }
        if($request->has('visibility_status')){
            $community->update(['visibility_status' => $request->get('visibility_status')]);
        }
        if ($request->has('change_article_status')) {
            if($request->has('articles_enabled')){
                $community->update(['articles_status' => true]);
            } else {
                $community->update(['articles_status' => false]);
            }
        }

        if ($request->get('redirect')) {
            $redirect = $request->get('redirect');
        }
        return Redirect::to(getSiteUrl() . '/communities/' . $community->slug . '/admin');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($communitySlug)
    {
        Community::findBySlug($communitySlug)->delete();
        addMessage('You successfully deleted the community. ');
        return redirect()->to(getSiteUrl() . '/communities');
    }

    /**
     * Generate JSON file from uploaded Excel
     * Used on communite admin page
     * @param $slug
     * @param Request $request
     * @return mixed
     */
    public function generateJson($slug, Request $request)
    {
        $folders = glob(ABSPATH . 'wp-content/uploads/json_zips/*');
        foreach ($folders as $folder) {
            if (file_exists($folder)) {
                $between = date_diff(date_create(date('Y-m-d H:i:s')), date_create(date('Y-m-d H:i:s', filemtime($folder))))->format('%a');
                if ($between >= 2) {
                    array_map('unlink', glob($folder . '/*.*'));
                    rmdir($folder);
                }
            }
        }

        require_once(ABSPATH . 'wp-content/themes/bp-child/functions/generate-json/JsonGenerator.php');

        if ($request->file('upload')) {
            $jg = new JsonGenerator($request->file('upload'));
            $zipLink = $jg->checkSheets();
            if (!empty($zipLink)) {
                $request->session()->set('zipLink', $zipLink);
            }
        }
        return Redirect::to(getSiteUrl() . '/communities/' . $slug . '/admin');
    }

    /**
     * @param $request
     * @param $model
     */
    private function handleImage($request, $model)
    {
        if ($request->file('image')) {
            $model->image = getenv('ENVIRONMENT') . '/communities/avatars/' . $model->id . '/avatar.' . $request->file('image')->getClientOriginalExtension();
            Storage::put($model->image, file_get_contents($request->file('image')));
            $model->save();
        }
    }
}
