<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use app\Classes\RegistrySearch;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;

class RegistrySearchController extends Controller
{

    /**
     * Display search entries list
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $results = (new RegistrySearch())->search($request->toArray());
        $paginator = new Paginator($results->getPath('hits/hit'), $results->getPath('hits/found'), 25, $request->get('page') ? $request->get('page') : 1);
        $pageTitle = 'Registry Search Results';
        $registrySearch = true;
        return view('pages.search.registry.index', compact('results', 'request', 'pageTitle', 'paginator', 'registrySearch'));
    }

    /**
     * Render filters view
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function filters(Request $request)
    {
        $data = [
            'results' => (new RegistrySearch())->search($request->toArray()),
            'request' => $request,
        ];
        return response()->json(['html' => view('pages.search.registry.filters')->with($data)->render()]);
    }

    /**
     * Render search entries list based on filters
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function entries(Request $request)
    {
        $results = (new RegistrySearch())->search($request->toArray());
        $data = [
            'results' => $results,
            'request' => $request,
            'paginator' => new Paginator($results->getPath('hits/hit'), $results->getPath('hits/found'), 25, $request->get('page') ? $request->get('page') : 1)
        ];
        return response()->json(['html' => view('pages.search.registry.list')->with($data)->render()]);
    }

    public function download(Request $request)
    {
        generate_and_download((new RegistrySearch())->search($request->toArray(), true));
    }

    /**
     * Delete Site Search entry
     * @param $entryId
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($entryId)
    {
        (new RegistrySearch())->delete_item($entryId);
        return response()->json(['success']);
    }
}
