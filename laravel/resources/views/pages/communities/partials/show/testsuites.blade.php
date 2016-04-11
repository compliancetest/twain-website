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
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>

                @foreach($testSuites as $testSuite)
                    <?php $suiteMeta = \App\Post::find($testSuite->ID)->postmeta->keyBy('meta_key');?>
                    @if(!is_admin() && $suiteMeta->hide_suite == 1) <?php continue;?> @endif
                    <tr>
                        <td>
                            <a href="{!! get_permalink($testSuite->ID) !!}"
                               class="test-suite-name">{{ $testSuite->post_title }}</a>

                            <p class="test-suite-description">{{ $testSuite->post_excerpt }}</p>
                        </td>
                        <td class="text-center">{{ formatDate($postMeta->ts_issue_date) }}</td>
                        <td class="text-center"><span
                                    class="status status-{{ strtolower($suiteMeta->get('ts_status')->meta_value) }}">{{ $suiteMeta->get('ts_status')->meta_value }}</span>
                        </td>
                        <td class="text-center">
                            <input class="notify-changes" type="checkbox" value="{{ $testSuite->ID }}"
                                   name="notify_changes{{ $testSuite->ID }}"
                                   @if (get_user_meta(get_current_user_id(), 'notify_suite_changes' . $testSuite->ID, true)) checked="checked" @endif
                            >
                            <img src="<?php echo CHILD_TEMPLATE_DIRECTORY ?>/images/loading-small.gif" alt=""
                                 style="display: none;"/>
                        </td>
                        <td class="text-center td-actions">
                            @if($community->isAdmin())
                                <a href="/edit-test-suite?id={{ $testSuite->ID }}"
                                   class="btn btn-icon btn-primary btn-edit" data-tooltip="tooltip"
                                   title="Edit Suite"></a>
                                <a href="/?suite_id={{ $testSuite->ID }}&_wpnonce={{ wp_create_nonce('delete-suite') }}&return=<?php echo base64_encode('/communities/' . $community->slug) ?>"
                                   class="btn btn-icon btn-danger btn-delete"
                                   onclick="return confirm('Are you sure to delete this test suite?')"
                                   data-tooltip="tooltip" title="Delete Suite"></a>
                            @endif
                        </td>
                    </tr>

                @endforeach

                </tbody>
            </table>
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
                url: '/?cp-action={{  wp_create_nonce('suite-notify-changes') }}',
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
