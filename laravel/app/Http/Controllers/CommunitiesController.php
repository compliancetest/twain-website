<?php

namespace App\Http\Controllers;

use App\Community;
use App\CommunityMembers;
use App\CommunityMeta;
use App\Post;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;

class CommunitiesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $communities = Community::all();
        return view('pages.communities.index', compact('communities'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.communities.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Requests\CommunityRequest $request)
    {
        $modelData = $request->all();
        $modelData['creator_id'] = get_current_user_id();
        $modelData['slug'] = Community::getUniqueSlug(new Community(), $modelData['title']);
        $model = Community::create($modelData);

        $this->handleImage($request, $model);

        $model->members()->create([
            'community_id' => $model->id,
            'user_id' => Auth::user()->ID,
            'is_admin' => true,
            'is_confirmed' => true
        ]);


        return Redirect::to('communities');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($slug, $action = 'testsuites')
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
         if ($action == 'wiki') {
            $where = $community->articles()->where('creator_id', Auth::user()->ID)
            ->where('visibility', 'creator')
            ->orWhere('visibility', 'members');
             if($community->isAdmin()){
                 $where ->orWhere('visibility', 'admins');
             }
            $data['articles'] = $where->with('attachments')->orderBy('updated_at')->get();
        }
        if ($action == 'testdata') {
            $data['instances'] = getCommunityProfileInstatnces($community->id);
        }
        if ($action == 'downloads') {
            $data['downloads'] = $community->downloads;
        }
        if ($action == 'admin') {
            $data['communityMeta'] = $community->meta->keyBy('meta_key');
            $data['profileTypes'] = getCommunityProfileTypes($community->id);
            $data['membershipRequests'] = $community->getMembershipRequests();
        }
        return view('pages.communities.show')->with($data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($slug, $step)
    {
        $community = Community::findBySlug($slug);
        $communityMeta = $community->getMeta();
        $viewPath = 'pages.communities.edit.steps.' . $step;
        $submitButtonText = 'Edit Community and Continue';
        if (view()->exists($viewPath)) {
            return view($viewPath, compact('community', 'step', 'submitButtonText', 'communityMeta'));
        }
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
                $community->update(['articles_status' => $request->get('articles_status')]);
            } else {
                $community->update(['articles_status' => '']);
            }
        }

        if ($request->get('redirect')) {
            $redirect = $request->get('redirect');
        }
        return Redirect::to('/communities/' . $community->slug . '/admin');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function terms($slug)
    {
        $community = Community::findBySlug($slug);
        $communityMeta = $community->getMeta();
        return view('pages.communities.popups.terms', compact('community', 'communityMeta'));
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
        return Redirect::to('/communities/' . $slug . '/admin');
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
