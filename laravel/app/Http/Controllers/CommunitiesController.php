<?php

namespace App\Http\Controllers;

use App\Community;
use App\CommunityMembers;
use App\CommunityMeta;
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
        if($request->file('image')){
            $model->image = getenv('ENVIRONMENT') . '/communities/avatars/' . $model->id . '/' . $request->file('image')->getClientOriginalName();
            Storage::put($model->image, file_get_contents($request->file('image')));
            $model->save();
        }

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
        $communityMeta = $community->getMeta();
        return view('pages.communities.show', compact('community', 'communityMeta', 'action'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($slug, $step)
    {
        $community = Community::findBySlug($slug);
        $communityMeta = $community->getMeta();
        $viewPath = 'pages.communities.edit.steps.' . $step;
        $submitButtonText = 'Edit Community and Continue';
        if(view()->exists($viewPath)) {
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
    public function update($slug, Requests\CommunityRequest $request)
    {
        $community = Community::findBySlug($slug);

        if($request->has('title') && $request->has('description')){
            $community->update(['title' => $request->get('title'), 'description' => $request->get('description')]);
        }

        if($request->has('status')) {
            $community->update(['status' => $request->get('status')]);
        }

        if($request->has('group-invite-status')) {
            CommunityMeta::updateOrCreate(['community_id' => $community->id, 'meta_key' => 'invite_status'], ['meta_value' => $request->get('group-invite-status')]);
        }
        if($request->get('community-forum')) {
            CommunityMeta::updateOrCreate(['community_id' => $community->id, 'meta_key' => 'forum_id'], ['meta_value' => 'yes']);
        }
        if($request->has('wiki-enabled')) {
            $wikiEnabled = $request->get('wiki-enabled');
            if ($wikiEnabled) {
                CommunityMeta::updateOrCreate(['community_id' => $community->id, 'meta_key' => 'wiki-status'], ['meta_value' => $request->get('wiki-enabled')]);
                CommunityMeta::updateOrCreate(['community_id' => $community->id, 'meta_key' => 'wiki-roles'], ['meta_value' => $request->get('create-wiki-roles')]);
            } else {
                CommunityMeta::updateOrCreate(['community_id' => $community->id, 'meta_key' => 'wiki-status'], ['meta_value' => 0]);
            }
        }

        if ($request->file('image')) {
            $imageName = 'community_' . $community->id . '.' .
                $request->file('image')->getClientOriginalExtension();
            $request->file('image')->move(
                base_path() . '/resources/assets/images/', $imageName
            );
            CommunityMeta::updateOrCreate(['community_id' => $community->id, 'meta_key' => 'logo'], ['meta_value' => '/laravel/resources/assets/images/' . $imageName]);
        }

         if($request->get('redirect')){
             $redirect = $request->get('redirect');
         }
        return Redirect::to($redirect);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
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

        require_once( ABSPATH . 'wp-content/themes/bp-child/functions/generate-json/JsonGenerator.php' );

        if ($request->file('upload')) {
            $jg = new JsonGenerator( $request->file('upload') );
            $zipLink = $jg->checkSheets();
            if (!empty($zipLink)) {
                $request->session()->set('zipLink', $zipLink);
            }
        }
        return Redirect::to('/communities/' . $slug . '/admin');
    }
}
