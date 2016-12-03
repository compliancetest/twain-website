{{-- Test Data --}}

<div class="colored-box collapsible-box">
    <div class="colored-box-header"><a href="#testDataBox" class="collapse-arrow" data-toggle="collapse"><span class="glyphicon glyphicon-triangle-bottom"></span><span
                    class="glyphicon glyphicon-triangle-right"></span></a>Profile Types
    </div>
    <div class="colored-box-body collapse in" id="testDataBox">
        <div class="colored-box-content test-data-profile-types">
            <div class="checkboxes-group clearfix">
                <?php $testSuiteProfileType = $testSuite ? $testSuite->profileTypes->keyBy('profile_type_id') : [];?>
                @if(count($suiteCommunity->profileTypes))
                    @foreach($suiteCommunity->profileTypes as $profileType)
                        <div class="checkbox">
                            <label>
                                <input name="profile_types[{{ $profileType->id }}]" type="checkbox" @if(isset($testSuiteProfileType[$profileType->id])) checked="checked"@endif>
                                <a href="{{ getSiteUrl() }}/profiletypes/{{ $suiteCommunity->slug }}/viewprofiletype/{{ $profileType->id }}" data-toggle="modal" data-remote="true"
                                           data-ajax-modal data-target="#modalViewProfile">{{ $profileType->getTitle() }}</a>
                            </label>
                        </div>
                    @endforeach
                    @include('pages.communities.popups.view-profile-modal')
                @else
                    <div class="text-center">
                        <span>Profile types not found</span>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="block-loading profileTypes">
        <div class="loading-content"><span class="loader"></span>
            <div class="loading-text">LOADING DATA</div>
            <div class="loading-wait">Please wait...</div>
        </div>
    </div>
</div>
