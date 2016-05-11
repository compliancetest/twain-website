<?php

namespace App\Http\Controllers;

use App\Community;
use App\CommunityDownloads;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;

class CommunityDownloadsController extends Controller
{

    protected $community;

    public function __construct(Request $request)
    {
        $this->community = Community::findBySlug($request->route('community'));
    }

    /**
     * Save new download
     * @param Requests\CommunityDownloadsRequest $request
     * @return array
     */
    public function store(Requests\CommunityDownloadsRequest $request)
    {
        $community = $this->community;
        $data = $request->all();

        $data['token'] = createClaimToken();

        $s3FilePath = config('env.env') . '/communities/downloads/' . $community->id . '/' . $data['token'] . '.'.$request->file('file')->getClientOriginalExtension();

        $data['title'] = $request->file('file')->getClientOriginalName();
        $data['location'] = $s3FilePath;
        $data['size'] = $request->file('file')->getSize();
        $community->downloads()->create($data);

        Storage::put($s3FilePath, file_get_contents($request->file('file')));

        return ['redirect_to' => $community->getUrl() . 'downloads'];

 }

    /**
     * Redirect to download s3 url
     * @param $slug - community slug
     * @param $id - download id
     * @param Request $request
     * @return mixed
     */
    public function getfile($slug, $id, Request $request)
    {
        $download = CommunityDownloads::find($id);
        return Redirect::to($download->getS3Link());
    }

    /**
     * get edit download form view
     * @param $slug - community slug
     * @param $id - download id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($slug, $id)
    {
        $download = CommunityDownloads::find($id);
        $community = $this->community;
        return view('pages.communities.downloads.edit', compact('community', 'download'));
    }

    /**
     * Update download
     * @param Request $request
     * @param $slug - community slug
     * @param $id - download id
     * @return array
     */
    public function update(Request $request, $slug, $id)
    {
        $community = $this->community;
        $data = $request->all();
        $download = $community->downloads()->find($id);
        if($request->file('file')) {
            $s3FilePath = config('env.env') . '/communities/downloads/' . $community->id . '/' . $download->token . '.'.$request->file('file')->getClientOriginalExtension();
            $data['title'] = $request->file('file')->getClientOriginalName();
            $data['location'] = $s3FilePath;
            $data['size'] = $request->file('file')->getSize();
            Storage::delete($download->location);
            Storage::put($s3FilePath, file_get_contents($request->file('file')));
        }

        $download->update($data);

        return ['redirect_to' => $community->getUrl() . 'downloads'];
    }

    /**
     * Remove Download
     * @param $slug - Community Slug
     * @param $id - download id
     * @return array
     */
    public function destroy($slug, $id)
    {
        $download = CommunityDownloads::find($id);
        Storage::delete($download->location);
        $download->delete();
        return ['status' => 'success'];
    }
}
