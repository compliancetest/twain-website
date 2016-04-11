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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $community = $this->community;
        return view('pages.communities.downloads.create', compact('community'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Requests\CommunityDownloadsRequest|Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Requests\CommunityDownloadsRequest $request)
    {
        $community = $this->community;
        $data = $request->all();
        $data['token'] = createClaimToken();
        $data['title'] = $request->file('file')->getClientOriginalName();
        $data['location'] = $request->file('file')->getClientOriginalName();
        $data['size'] = $request->file('file')->getSize();
        $community->downloads()->create($data);

        $s3FilePath = getenv('ENVIRONMENT') . '/communities/downloads/' . $community->id . '/' . $data['token'] . '.'.$request->file('file')->getClientOriginalExtension();
        Storage::put($s3FilePath, file_get_contents($request->file('file')));

        return ['redirect_to' => $community->getUrl() . 'downloads'];

 }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function agreement($slug, $id)
    {
        $community = $this->community;
        $download = CommunityDownloads::find($id);
        $license = $download->license ? $download->license : $this->community;
        return view('pages.communities.downloads.agreement', compact('license', 'download', 'community'));
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getfile($slug, $id, Request $request)
    {
        $download = CommunityDownloads::find($id);
        return Redirect::to(\S3Wrapper::getAttachmentLink( $download->token, $download->location, 'downloads', true ));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $slug
     * @param $downloadId
     * @return \Illuminate\Http\Response
     * @internal param int $id
     */
    public function edit($slug, $id)
    {
        $download = CommunityDownloads::find($id);
        $community = $this->community;
        return view('pages.communities.downloads.edit', compact('community', 'download'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $slug, $id)
    {
        $community = $this->community;
        $data = $request->all();
        if($request->file('file')) {
            $data['token'] = createClaimToken();
            $data['title'] = $request->file('file')->getClientOriginalName();
            $data['location'] = $request->file('file')->getClientOriginalName();
            $data['size'] = $request->file('file')->getSize();
        }
        $community->downloads()->find($id)->update($data);

        //        $s3 = new S3Wrapper();
//        $s3->putObject('/attachments/downloads/' . $data['token'] . '.'.$request->file('file')->getExtension(), file_get_contents($request->file('file')->getPath()), 'application/'.$ext );

        return ['redirect_to' => $community->getUrl() . 'downloads'];
    }

    public function confirmDelete($slug, $downloadId)
    {
        return view('pages.communities.downloads.confirmDelete', ['downloadId' => $downloadId, 'community' => $this->community]);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($slug, $id)
    {
        CommunityDownloads::find($id)->delete();
        return Redirect::to('communities/'.$this->community->slug.'/downloads/');
    }
}
