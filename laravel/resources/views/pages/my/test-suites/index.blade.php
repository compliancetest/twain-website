@extends('app')
@section('content')
    <div class="container main-container">
        @include('pages.user-tabs', ['tab' => 'my-test-suites'])
        <div class="main-content">
            <div class="table-responsive">
                <table class="table colored-table">
                    <thead>
                        <tr>
                            <th class="text-left">Community</th>
                            <th class="text-left">Test Suite</th>
                            <th style="width: 100px;">Status</th>
                            <th style="width: 65px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    @if(count($userSubscriptions))
                        @foreach($userSubscriptions as $index => $userSubscription)
                            <?php $latestSuite = \App\LaravelTestSuite::getLatestSuiteForMinorFamilyMark($userSubscription->suite_minor_family_mark);?>
                            <tr class="{{ $userSubscription->testSuite->slug }}">
                                <td><a href="/communities/{{ $userSubscription->testSuite->community->slug }}">{{ $userSubscription->testSuite->community->title }}</a></td>
                                <td><a target="_blank" href="/test-suite/{{ $latestSuite->slug }}">{{ $latestSuite->full_name }}</a></td>
                                <td class="text-center"><span class="status status-{{ strtolower($userSubscription->status)}}">{{ $userSubscription->status }}</span></td>
                                <td class="text-center">
                                    <a class="btn btn-default btn-icon btn-unsubscribe" href="#confirmReleaseSubscriptionModal{{$index}}" data-toggle="modal" data-tooltip="tooltip" title="Release Subscription">Delete button</a>
                                    @include('pages.test-suites.partials.release-subscription', ['index' => $index, 'suiteSlug' => $latestSuite->slug ])
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="4" class="text-center">You do not have any subscriptions yet</td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </div>
            <a href="/test-suites" class="btn btn-success btn-with-icon btn-add">Add Test Suite</a>
        </div>
    </div>
@stop


@section('page-scripts')
    <script>
        jQuery(document).ready(function ($) {

            $('body').on('submit', '.confirmReleaseSubscriptionForm', function (e) {
                var form = $(this);
                var formIndex = $(this).data('form-index');
                var suiteSlug = $(this).attr('action');
                e.preventDefault();
                if (suiteSlug){
                    jQuery('#confirmReleaseSubscriptionSpinner' + formIndex).show();
                    $.ajax({
                        url: '/test-suite/' + suiteSlug + '/subscription',
                        type: 'post',
                        data: {},
                        success: function (rsp) {
                            var confirmModal = $('#confirmReleaseSubscriptionModal' + formIndex);
                            $('#confirmReleaseSubscriptionSpinner' + formIndex).hide();
                            confirmModal.modal('hide');
                            $('.' + suiteSlug).addClass('removing').fadeTo("slow", 0.3, function () {
                                $(this).remove();
                                $('.main-content').prepend('<div class="success-message">Your subscription has been cancelled</div>');
                                setTimeout(function () {
                                    $('.main-content > .success-message').slideUp(function () {
                                        $(this).remove();
                                    });
                                }, 3000);
                            });
                        },
                        complete: function () {
                            $('#confirmReleaseSubscriptionSpinner' + formIndex).hide();
                        }
                    })
                }
            });

        });
    </script>
@stop