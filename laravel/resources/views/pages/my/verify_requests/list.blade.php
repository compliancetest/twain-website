@foreach($userSuites as $userSuite)
    <?php $userSuite = \App\Post::find($userSuite->suite_family_mark);?>
    <div class="colored-box">
        <div class="colored-box-header"><a href="/test-suite/{{ $userSuite->post_name }}/">{{ $userSuite->post_title }}</a></div>
        <div class="colored-box-body">
            <div class="table-responsive">
                <table class="table colored-table">
                    <thead>
                    <tr>
                        <th class="text-left">Product</th>
                        <th class="col-sm-1">Level</th>
                        {{--<th class="col-sm-1 text-left">Role</th>--}}
                        <th>Test Cases</th>
                        <th>Status</th>
                        <th>Requestor</th>
                        <th>Assignee</th>
                        <th>Submitted<br>Updated</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    <a href="/verify-requests/{{ $userSuite->ID }}/create/" data-toggle="modal" data-remote="true" data-ajax-modal data-target="#createVerifyRequestModal"
       class="btn btn-success btn-with-icon btn-add" style="margin-bottom: 20px;">Add</a>
@endforeach