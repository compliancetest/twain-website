<div class="modal-header">
    <button type="button" class="close-modal" title="Close popup" data-dismiss="modal" aria-label="Close">Close</button>
    Migrate Test Results
</div>
<div class="modal-body">
    @if(count($suitesFrom))
        <div class="form-group">
            <label for="suiteFrom">From suite:</label>
            <select class="form-control" id="suiteFrom" name="suiteFrom">
                <option value="">--Select Test Suite--</option>
                @foreach($suitesFrom as $suiteFrom)
                    <option @if($selectedSuiteFrom == $suiteFrom->testSuite->minor_family_mark) selected="selected"
                            @endif value="{{ $suiteFrom->testSuite->minor_family_mark }}">{{ \App\LaravelTestSuite::getLatestSuiteForMinorFamilyMark($suiteFrom->testSuite->minor_family_mark)->full_name }}</option>
                @endforeach
            </select>
        </div>
    @endif

    @if(count($suitesTo))
        <div class="form-group">
            <label for="suiteFrom">To suite:</label>
            <select class="form-control" id="suiteTo" name="suiteTo">
                <option value="">--Select Test Suite--</option>
                @foreach($suitesTo as $suiteTo)
                    <option @if($selectedSuiteTo == $suiteTo->testSuite->minor_family_mark) selected="selected"
                            @endif value="{{ $suiteTo->testSuite->minor_family_mark }}">{{ \App\LaravelTestSuite::getLatestSuiteForMinorFamilyMark($suiteTo->testSuite->minor_family_mark)->full_name }}</option>
                @endforeach
            </select>
        </div>
    @endif

    <div class="row">
        <div class="col-sm-12">
            @if($transactions)
                <div class="table-responsive">
                    <table class="table colored-table" style="margin-top: 20px;">
                        <thead>
                        <tr>
                            <th></th>
                            <th>Execution ID</th>
                            <th>Date</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($transactions as $testCase => $caseTransactions)
                            <tr>
                                <td colspan="4" class="caseIdList" data-id="{!! $testCase !!}">
                                    {{ \App\LaravelTestCase::find($testCase)->full_name }}
                                    @if(!\App\Transaction::where(['test_case_id' => $testCase, 'suite_minor_family_mark' => $caseTransactions[0]->suite_minor_family_mark])->get()->isEmpty())
                                        <a href="/my-transaction-log/?audit_record=yes&test_case_id={{ $testCase }}&suite_minor_family_mark={{ $caseTransactions[0]->suite_minor_family_mark }}"
                                           target="_blank" style="float: right;">View Log</a>
                                    @endif
                                </td>
                            </tr>
                            @foreach($caseTransactions as $transaction)
                                <tr>
                                    <td class="text-center"><input type="checkbox" checked="checked" name="transaction" class="transaction"
                                                                   value="{{ $transaction->id }}" data-case="{{ $testCase }}"></td>

                                    <td class="text-center">
                                        @if($transaction->s3_link)
                                            <a href="{!! $transaction->s3_link !!}" target="_blank"> {!! $transaction->execution_id !!} </a>
                                        @else
                                            {!! $transaction->execution_id !!}
                                        @endif
                                    </td>
                                    <td class="text-center">{{ formatDate($transaction->created_at, 'Y-m-d H:i:s') }}</td>
                                </tr>
                            @endforeach
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
            @if($selectedSuiteFrom && $selectedSuiteTo && !count($transactions))
                 <div class="alert alert-success text-center" role="alert">
                    <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
                    You can't copy any transaction for selected test suites
                </div>
            @endif
        </div>
    </div>

    @if(!count($suitesTo) && !count($suitesFrom))
        <div class="alert alert-success text-center" role="alert">
            <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
            You do not have any active subscriptions
        </div>
    @endif
</div>
<div class="modal-footer">
    @if($selectedSuiteTo && $selectedSuiteFrom && count($transactions))
        <button type="submit" class="btn btn-success btn-with-icon btn-confirm submit-migration">Submit</button>
    @endif
    <button type="button" class="btn btn-default btn-with-icon btn-cancel" data-dismiss="modal">Cancel</button>
</div>
<div class="block-loading">
    <div class="loading-content"><span class="loader"></span>
        <div class="loading-text">LOADING DATA</div>
        <div class="loading-wait">Please wait...</div>
    </div>
</div>