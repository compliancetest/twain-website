@if(Auth::check())
    @if(Auth::user()->getSuiteSubscription($testSuite))
        <div class="success-message lg">
            You have currently been allocated a subscription to this test suite.
            If you want to release this subscription, click <a href="#confirmReleaseSubscriptionModal" data-toggle="modal">here</a>.
        </div>
        @include('pages.test-suites.partials.release-subscription')
    @else
        @if(Auth::user()->doesUserOrganisationApproved($testSuite))
            @include('pages.test-suites.partials.assign-subscription')
        @else
            @include('pages.test-suites.partials.contact-us-button')
        @endif
    @endif
@else
    @include('pages.test-suites.partials.contact-us-button')
@endif