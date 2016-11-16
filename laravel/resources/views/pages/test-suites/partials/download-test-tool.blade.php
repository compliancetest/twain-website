@if($installer && Auth::check() && (!Auth::user()->suiteSubscriptions->isEmpty() || $isSupport) )
    @if(!empty($installer->license))
        <li class="noprint">
            Test Tool: <a href="#licenseAgreementModal{{$installer->id}}" data-toggle="modal" @if(!empty($installer->description)) data-tooltip="tooltip" title="{{ $installer->description }}" @endif>{{ $installer->title }}</a>
            {{-- License agreement popup --}}
            <div class="modal fade" id="licenseAgreementModal{{$installer->id}}" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
                <div class="modal-dialog" role="document">
                    <div class="modal-content block-loading-wrapper">
                        <div class="modal-header">
                            <button type="button" class="close-modal" title="Close popup" data-dismiss="modal" aria-label="Close">Close</button>
                            License Agreement
                        </div>
                        <div class="modal-body">
                            {{ $installer->license }}
                            <br>
                            <div class="checkbox">
                                <label><input type="checkbox" class="downloadAgreeLicenseCheck" data-installer-id="{{$installer->id}}" name="agree_license"> I agree with the License Agreement</label>
                            </div>
                            <div class="error-message hide">
                                Please agree with the License Agreement.
                            </div>
                        </div>
                        <div class="modal-footer">
                            <form method="get" action="{{ $installer->getS3Link() }}">
                                <button type="submit" class="btn btn-success btn-with-icon btn-download licenseDownloadBtn{{$installer->id}}" disabled="disabled">DOWNLOAD</button>
                                <button type="button" class="btn btn-default btn-with-icon btn-cancel" data-dismiss="modal">Cancel</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </li>
    @else
        <li>
            Test Tool: <a href="{{ $installer->getS3Link() }}" download="{{ $installer->title }}" @if(!empty($installer->description)) data-tooltip="tooltip" title="{{ $installer->description }}" @endif>{{ $installer->title }}</a>
        </li>
    @endif
@endif