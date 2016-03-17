<?php

namespace App\Api\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpKernel;
class TestCasesController extends Controller
{

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $rules = [
            'product_id' => ['required', 'alpha']
        ];

        $payload = app('request')->only('product_id');

        $validator = app('validator')->make($payload, $rules);
        if ($validator->fails()) {
            throw new \Symfony\Component\HttpKernel\Exception\BadRequestHttpException('product_id field is required');
        }
        dd($id);
    }

}
