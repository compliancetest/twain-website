<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use app\Classes\SiteSearch;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;


class SiteSearchController extends Controller
{

    /**
     * Display search entries list
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $results = (new SiteSearch())->search($request->toArray());
        $paginator = new Paginator($results->getPath('hits/hit'), $results->getPath('hits/found'), 25, $request->get('page') ? $request->get('page') : 1);
        $pageTitle = 'Search Results';
        return view('pages.search.site.index', compact('results', 'request', 'pageTitle', 'paginator'));
    }

    /**
     * Render filters view
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function filters(Request $request)
    {
        $data = [
            'results' => (new SiteSearch())->search($request->toArray()),
            'request' => $request,
        ];
        return response()->json(['html' => view('pages.search.site.filters')->with($data)->render()]);
    }

    /**
     * Render search entries list based on filters
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function entries(Request $request)
    {
        $request->session()->put('siteSearch', $request->toArray());
        $results = (new SiteSearch())->search($request->toArray());
        $data = [
            'results' => $results,
            'request' => $request,
            'paginator' => new Paginator($results->getPath('hits/hit'), $results->getPath('hits/found'), 25, $request->get('page') ? $request->get('page') : 1)
        ];
        return response()->json(['html' => view('pages.search.site.list')->with($data)->render()]);
    }

    public function download(Request $request)
    {

        generate_and_download_site((new SiteSearch())->search($request->session()->get('siteSearch', []), true, true));
    }

    /**
     * Delete Site Search entry
     * @param $entryId
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($entryId)
    {
        (new SiteSearch())->delete_item($entryId);
        return response()->json(['success']);
    }

    /**
     * Clear registry search domain data
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeAll()
    {
        $client = (new SiteSearch())->_client;
        $results = $client->search([
            'size' => 10000,
            'query' => 'matchall',
            'queryParser' => 'structured',
        ]);

        $data = $response_data = array();

        foreach ($results['hits']['hit'] as $row) {
            array_push($data, array('type' => 'delete', 'id' => $row['id']));
        }
        if (!empty($data)) {
            $result = $client->uploadDocuments(array('documents' => json_encode($data), 'contentType' => 'application/json'));
        }

        return response()->json($result);
    }

    /**
     * Upload data to cloudsearch
     * todo-refactoring - make bulk uploading instead of triggering observers
     */
    public function uploadAll()
    {
        //update test cases
        $entries = \App\LaravelTestCase::all();
        foreach ($entries AS $entry) {
            $entry->timestamps = false;
            $entry->save();
        }
        //update suites
        $entries = \App\LaravelTestSuite::all();
        foreach ($entries AS $entry) {
            $entry->timestamps = false;
            $entry->save();
        }
        //update products
         //update suites
        $entries = \App\Product::all();
        foreach ($entries AS $entry) {
            $entry->timestamps = false;
            $entry->save();
        }

        $entries = \App\Post::whereIn('post_type', ['press-release', 'blog', 'event', 'page', 'link'])->get();
        foreach ($entries AS $entry) {
            $entry->timestamps = false;
            $entry->save();
        }
    }
}
