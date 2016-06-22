<style>
    .test-cases-table th {
        background-color:#5a75b6;
        color:#fff;
        font-size:7pt;
        vertical-align:middle;
        line-height:18pt;
        text-align:center;
        font-weight:bold;
    }
    .test-cases-table tr td {
        height: 100px !important;
    }
    .test-cases-table th.test-outcome{
        line-height:10px;
    }
    .test-cases-table th.test-scenario{
        text-align:left;
    }
    .test-cases-table td {
        font-size:6pt;
        line-height:6pt;
        color:#000;
    }
    .test-cases-table .even td{
        background-color:#f3f4f5;
    }
    .test-cases-table .odd td{
        background-color:#ececed;
    }
    .test-cases-table td a{
        font-size:10pt;
    }
    .test-cases-table td.test-scenario{
        background-color:#e2e2e2;
    }

    .issued, .test-outcome, .supporting-evidence{
        text-align:center;
    }
</style>

<table cellspacing="1" cellpadding="3" class="test-cases-table" width="100%">
    <tr>
        <th colspan="5">{{ $message }}</th>
    </tr>
    <tr>
        <th class="test-scenario" style="width:25%; vertical-align:middle;">Test Scenario</th>
        <th class="test-case" style="width:12%;">Test Case</th>
        <th class="issued" style="width:8%;">Issued</th>
        <th class="test-intent" style="width:30%;">Test Intent Description</th>
        <th class="test-reason" style="width:25%;">Reason</th>
    </tr>
    @foreach($cases as $scenarioID => $testCases)
        <?php $counter = 0;?>
        @foreach($testCases as $testCase)
            <tr class="{{ $counter++%2 == 0 ? 'odd' : 'even' }}">
                @if($counter == 1)
                    <td class="test-scenario" rowspan="{{ count($testCases) }}">
                        <strong>{{ $testCase->scenarioCode }}:</strong><br>
                        {!! $testCase->scenarioDescription !!}
                    </td>
                @endif
                <td class="test-case">{{ $testCase->post_title }}</td>
                <td class="issued">{{ date('Y-m-d', strtotime(getPostMeta($testCase->ID, 'published'))) }}</td>
                <td class="test-intent">{!! getPostMeta($testCase->ID, 'test_intent_description') !!}</td>
                <td class="test-reason">{{ $testCase->reason }}</td>
            </tr>
        @endforeach
    @endforeach
</table>