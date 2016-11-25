<div class="community-test-suites row">
    <div class="col-md-12">

        <div class="filter-panel">
            <div class="collapsible-panel-group" id="accordion">

            </div>
        </div>

        <div class="table-responsive result-content-panel-inner">
            <table class="table">
                <thead>
                <tr>
                    <th class="text-left">Name</th>
                    <th>Published</th>
                    <th>Status</th>
                    <th>Notify Changes</th>
                    @if($isAdmin)
                        <th>Action</th>
                    @endif
                </tr>
                </thead>
                <tbody>

                @if($testSuites)
                    @foreach($testSuites as $kkk => $testSuite)
                        <tr>
                            <td>
                                <a href="{!! $testSuite->geturl() !!}"
                                   class="test-suite-name">{{ $testSuite->full_name }}</a>

                                <p class="test-suite-description">{{ $testSuite->description }}</p>
                            </td>
                            <td class="text-center">{{ formatDate($testSuite->published_at) }}</td>
                            <td class="text-center"><span
                                        class="status status-{{ strtolower($testSuite->status) }}">{{ $testSuite->status }}</span></td>
                            <td class="text-center">
                                <input class="notify-changes" type="checkbox" value="{{ $testSuite->id }}" name="notify_changes{{ $testSuite->id }}"
                                       data-slug="{{ $testSuite->slug }}"
                                       @if ($testSuite->changesSubscriptions()->where('user_id', Auth::user()->ID)->first()) checked="checked" @endif >
                                <img src="<?php echo CHILD_TEMPLATE_DIRECTORY ?>/images/loading-small.gif" alt="" style="display: none;"/>
                            </td>
                            @if($isAdmin)
                                <td class="text-center td-actions text-nowrap">
                                    <a href="{{ getSiteUrl() }}/test-suite/{{ $testSuite->slug }}/edit"
                                       class="btn btn-icon btn-primary btn-edit" data-tooltip="tooltip"
                                       title="Edit Suite"></a>
                                    <a data-target="#deleteTestSuiteModal" class="btn btn-danger btn-icon btn-delete"
                                       data-tooltip="tooltip" href="/test-suite/{{ $testSuite->slug }}/delete" data-toggle="modal"
                                       data-remote="true" data-ajax-modal data-original-title="Delete Test Suite">Delete
                                    </a>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="{{ $isAdmin ? 5 : 4 }}" class="text-center">No test suites yet</td>
                    </tr>
                @endif
                </tbody>
            </table>
        </div>

        @if($isAdmin)
            <div class="col-md-3">
                <div class="page-title-actions">
                    <a href="{{ getSiteUrl() }}/test-suite/create/?community_id={{ $community->id }}" class="btn btn-success btn-with-icon btn-add">Add new Test Suite</a>
                </div>
            </div>
        @endif

    </div>
</div>

<div class="modal fade" id="deleteTestSuiteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document" style="width: 500px;">
        <div class="modal-content block-loading-wrapper">
            <div class="modal-header">
                <button type="button" class="close-modal" title="Close popup" data-dismiss="modal" aria-label="Close">Close</button>
                Delete Test Suite
            </div>
            <div class="modal-body">
                <div class="block-loading">
                    <div class="loading-content"><span class="loader"></span>
                        <div class="loading-text">LOADING DATA</div>
                        <div class="loading-wait">Please wait...</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    jQuery(document).ready(function () {
        jQuery('.notify-changes').click(function () {
            var chObj = jQuery(this);
            chObj.next().show();
            chObj.hide();
            var isChecked = this.checked;
            var sID = chObj.val();
            jQuery.ajax({
                url: '/test-suite/'+chObj.data('slug')+'/suite-notify-changes',
                type: 'post',
                data: {id: sID, checked: isChecked ? 1 : 0},
                complete: function (rsp) {
                    chObj.next().hide();
                    chObj.show();
                }
            });
        })
    })
</script>
