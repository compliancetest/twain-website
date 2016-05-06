<div class="block-loading-wrapper">
    <div class="community-downloads">
        <div class="table-responsive">
            <table class="table downloads-list-table">
                <thead>
                <tr>
                    <th class="text-left">Survey Title</th>
                    <th class="text-center">Open Date</th>
                    <th class="text-center">Close Date</th>
                    <th class="text-center">Number of Responses</th>
                    <th class="text-center">Action</th>
                </tr>
                </thead>
                <tbody>
                @if(count($surveys))
                    @foreach($surveys as $survey)
                        <tr>
                            <td class="text-left">{{ $survey['title'] }}</td>
                            <td class="text-center">{{ $survey['date_created'] }}</td>
                            <td class="text-center">
                                @if($survey['date_close'])
                                    {{ $survey['date_close'] }}
                                @endif
                            </td>
                            <td class="text-center">{{ $survey['responses_number'] }}</td>

                            <td class="text-center">
                                @if($survey['date_close'])
                                    This survey is closed
                                @elseif($survey['user_responded'])

                                    <a data-toggle="modal" href="#viewResults{{ $survey['id'] }}">View results</a>

                                    <div class="modal fade profile-modal" id="viewResults{{ $survey['id'] }}" tabindex="-1" role="dialog">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    {{ $survey['title'] }} Results
                                                    <button type="button" class="close-modal" data-tooltip="tooltip" title="Close popup" data-placement="left" data-dismiss="modal"
                                                            aria-label="Close">Close
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <div id="surveyMonkeyInfo">
                                                        <div>
                                                            <iframe src="https://www.surveymonkey.net/results/SM-7KWQ5JLR/"
                                                                    style="width: 100%; min-width: 1100px; min-height: 500px; border: 0 solid white;"></iframe>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-default btn-with-icon btn-cancel" data-dismiss="modal">Cancel</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                @else
                                    <a data-toggle="modal" href="#downloadLicense{{ $survey['id'] }}">Complete Survey</a>

                                    <div class="modal fade profile-modal" id="downloadLicense{{ $survey['id'] }}" tabindex="-1" role="dialog">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    {{ $survey['title'] }}
                                                    <button type="button" class="close-modal" data-tooltip="tooltip" title="Close popup" data-placement="left" data-dismiss="modal"
                                                            aria-label="Close">Close
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <div id="surveyMonkeyInfo">
                                                        <div>
                                                            <iframe src="{{ $survey['url'] }}"
                                                                    style="width: 100%; min-width: 800px; min-height: 500px; border: 0 solid white;"></iframe>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-default btn-with-icon btn-cancel" data-dismiss="modal">Cancel</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="5" class="empty-row ">No surveys yet</td>
                    </tr>
                @endif
                </tbody>
            </table>
        </div>
    </div>
</div>