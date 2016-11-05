@extends('app')
@section('content')
    <div class="container main-container">
        @include('pages.user-tabs', ['tab' => 'my-test-suites'])
        <div class="main-content">
            <div>
                <table>
                    <tr>
                        <th>Community</th>
                        <th>Test Suite</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                    @if($userSubscriptions)
                        @foreach($userSubscriptions as $userSubscription)
                            <td><a href="/communities/{{ $userSubscription->testSuite->community->slug }}">{{ $userSubscription->testSuite->community->title }}</a></td>
                            <td><a target="_blank" href="/laravel-test-suites/{{ $userSubscription->testSuite->slug }}">{{ $userSubscription->testSuite->full_name }}</a></td>
                            <td>{{ $userSubscription->status }}</td>
                            <td>
                                <a href="#confirmReleaseSubscriptionModal" data-toggle="modal">Delete button</a>.
                                @include('pages.test-suites.partials.release-subscription')
                            </td>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="4" class="text-center">You do not have any subscriptions yet</td>
                        </tr>
                    @endif
                </table>
            </div>
        </div>
    </div>
@stop