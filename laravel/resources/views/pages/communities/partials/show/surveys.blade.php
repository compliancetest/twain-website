<div class="block-loading-wrapper">
    <div class="community-downloads">
        <div class="table-responsive">
            <table class="table downloads-list-table">
                <thead>
                <tr>
                    <th class="text-left">Survey Name</th>
                    <th class="text-left">Date Created</th>
                </tr>
                </thead>
                <tbody>
                @if(count($surveys))
                    @foreach($surveys as $survey)
                        <tr>
                            <td>
                                <a data-toggle="modal" href="#downloadLicense{{ $survey['id'] }}">{{ $survey['title'] }}</a>

                                <div class="modal fade profile-modal" id="downloadLicense{{ $survey['id'] }}" tabindex="-1" role="dialog">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
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
                            </td>
                            <td>{{ $survey['date_created'] }}</td>
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