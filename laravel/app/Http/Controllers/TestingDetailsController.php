<?php

namespace App\Http\Controllers;

use App\TestingDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;

class TestingDetailsController extends Controller
{


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $suites = getUserSubscriptions();
        if(!$suites){
            return view('pages.testingdetails.nosubscription', compact('cases', 'products', 'suites', 'currentTestingDetails'));
        }
        $userId = get_current_user_id();
        $products = getUserProductsAndServices($userId);
        $current_suite_id = $suites[0]->suite_id;

        $suiteObj = new \TestSuite($current_suite_id);
        $cases = $suiteObj->loadTesterInitiatedTestCases();

        $currentTestingDetails = TestingDetail::where(['user_id' => $userId, 'is_running' => 1])->first();
        return view('pages.testingdetails.show', compact('cases', 'products', 'suites', 'currentTestingDetails'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $model = TestingDetail::firstOrNew(['user_id' => get_current_user_id()]);
        $model->fill($request->all());
        if($request->get('is_running')){
            $model->start_time = Carbon::now();
        } else {
            $model->end_time = Carbon::now();
        }
        $model->save();
        addMessage('Testing details has been saved successfully.', 'success');
        return redirect()->secure('my-transaction-log');
    }

}
