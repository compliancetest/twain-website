@extends('app')
@section('content')
    <div class="container main-container">
        @include('pages.user-tabs', ['tab' => 'my-profile'])
        <div class="main-content">
            <div class="row">
                <div class="col-sm-6">
                    <div class="colored-box">
                        <div class="colored-box-header">My Details</div>
                        <div class="colored-box-body">
                            {!! Form::model($community, ['id'=> 'profile-details-form', 'data-save-method' => 'ajax', 'data-validate'=>'validate', 'method' => 'PATCH', 'url' => getSiteUrl() . '/my-profile/']) !!}
                            <div class="colored-box-content">
                                <div class="form-group">
                                    <label for="firstName">First Name</label>
                                    <input type="text" name="first_name" class="form-control" id="firstName" required="required"  value="{{ Auth::user()->getMetaByKey('first_name') }}">
                                </div>
                                <div class="form-group">
                                    <label for="lastName">Last Name</label>
                                    <input type="text" name="last_name" class="form-control" id="lastName" required="required"  value="{{ Auth::user()->getMetaByKey('last_name') }}">
                                </div>
                                <div class="form-group">
                                    <label for="profileEmail">Email</label>
                                    <input type="email" name="email" class="form-control" id="profileEmail" required="required"  value="">
                                </div>
                                <div class="form-group">
                                    <label for="phoneNumber">Phone Number</label>
                                    <input type="text" name="phone_number" class="form-control" id="phoneNumber" required="required"  value="{{ Auth::user()->getMetaByKey('phone_number') }}">
                                </div>
                                <div class="form-group">
                                    <label for="password">Password</label>
                                    <input type="password" name="password" class="form-control" id="password" required="required"  value="">
                                </div>
                                <div class="form-group">
                                    <label for="confirmPassword">Confirm Password</label>
                                    <input type="password" name="password" class="form-control" id="confirmPassword" required="required"  value="">
                                </div>
                                <div class="form-group">
                                    <label for="currentPassword">Current Password</label>
                                    <input type="password" name="password" class="form-control" id="currentPassword" required="required"  value="">
                                </div>
                                <div class="form-group">
                                    <label for="profileBiography">About me</label>
                                    <textarea name="biography" class="form-control" id="profileBiography"></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="timezoneSettings">Timezone settings</label>
                                    <?php $timezone_list = DateTimeZone::listIdentifiers(DateTimeZone::ALL); ?>
                                    <select name="timezone_settings" class="form-control" id="timezoneSettings">
                                        <?php
                                            $dateTimeZoneGmt = new DateTimeZone("GMT");
                                            $dateTimeGmt = new DateTime("now", $dateTimeZoneGmt);
                                        ?>
                                        <?php foreach ($timezone_list as $t): ?>
                                        <?php
                                            if($t == 'UTC'){
                                                continue;
                                            }
                                            $dateTimeZone = new DateTimeZone($t);
                                            $dateTime = new DateTime("now", $dateTimeZone);
                                            $timeOffset = $dateTimeZone->getOffset($dateTimeGmt) / 3600;
                                            $timestr = '';
                                            if($timeOffset <= 0){
                                                if($timeOffset == 0){
                                                    $timestr = ' (GMT)';
                                                } else {
                                                    $timestr = ' (GMT-' . sprintf("%02d", abs($timeOffset)) . ':00)';
                                                }
                                            } else {
                                                $timestr = ' (GMT+'.sprintf("%02d", $timeOffset).':00)';
                                            }
                                        ?>
                                        <option value="<?php echo $t; ?>" <?php if($t === Auth::user()->getMetaByKey('timezone')): ?>selected="selected"<?php endif; ?>><?php echo $t . ''.$timestr.''; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="desiredFirstPage">Desired First Page</label>
                                    <select name="first_page" class="form-control" id="desiredFirstPage">
                                        @if(is_organisation_admin())
                                            <option value="{{ getSiteUrl() }}/my-organisation">Organization</option>
                                            <option value="{{ getSiteUrl() }}/my-organisation/users" class="level-1">Users</option>
                                            <option value="{{ getSiteUrl() }}/my-organisation/test-suites" class="level-1">Subscriptions</option>
                                            <option value="{{ getSiteUrl() }}/my-organisation" class="level-1">Profile</option>
                                        @endif
                                        <option value="{{ getSiteUrl() }}/my-communities">Communities</option>
                                        @foreach(Auth::user()->confirmedSubscriptions() as $sub)
                                            <option value="{{ getSiteUrl() }}/communities/{{ $sub->community->slug }}" class="level-1">{{ $sub->community->title }}</option>
                                                <option value="{{ $sub->community->getUrl() }}testsuites" class="level-2">Test Suites</option>
                                                <?php $testsuites = getCommunityTestSuites($sub->community->id);?>
                                                @if(count($testsuites) > 0)
                                                    @foreach ($testsuites as $k => $latestSuite)
                                                        <option value="{{ getSiteUrl() }}/test-suite/{{ $latestSuite->slug }}" class="level-3">{{ $latestSuite->full_name }}</option>
                                                    @endforeach
                                                @endif
                                                <option value="{{ $sub->community->getUrl() }}testdata" class="level-2">Test Data</option>
                                                <option value="{{ $sub->community->getUrl() }}wiki" class="level-2">Articles</option>
                                                <option value="{{ $sub->community->getUrl() }}forum" class="level-2">Forum</option>
                                                <option value="{{ $sub->community->getUrl() }}downloads" class="level-2">Downloads</option>
                                                <option value="{{ $sub->community->getUrl() }}surveys" class="level-2">Surveys</option>
                                                @if($sub->community->isModerator() || $sub->community->isAdmin())
                                                    @if($sub->community->isAdmin())
                                                        <option value="{{ $sub->community->getUrl() }}backups" class="level-2">Test Data Backups</option>
                                                    @endif
                                                    <option value="{{ $sub->community->getUrl() }}settings" class="level-2">Settings</option>
                                                @endif
                                        @endforeach
                                            <option value="{{ getSiteUrl() }}/communities" class="level-1">+ Add</option>

                                        <option value="{{ getSiteUrl() }}/my-test-suites">Test Suites</option>
                                        @foreach(getUserSubscriptions(null, true) as $subscription)
                                            <option value="{{ getSiteUrl() }}/test-suite/{{ $subscription->slug }}" class="level-1">{{ $subscription->full_name }}</option>
                                        @endforeach
                                            <option value="{{ getSiteUrl() }}/test-suites" class="level-1">+ Add</option>
                                        <option value="{{ getSiteUrl() }}/my-products">Products</option>
                                        <option value="{{ getSiteUrl() }}/test-suite-coverage">Coverage</option>
                                        <option value="{{ getSiteUrl() }}/verify-requests">Verify Requests</option>
                                        <option value="{{ getSiteUrl() }}/m{{ getSiteUrl() }}/y-transaction-log">Test Results</option>
                                        <option value="{{ getSiteUrl() }}/my-support-tickets">Support</option>
                                        <option value="{{ getSiteUrl() }}/my-profile">Profile</option>
                                        @if(is_super_admin())
                                            <option value="{{ getSiteUrl() }}/menu-transactions">ApiLogs</option>
                                            <option value="{{ getSiteUrl() }}/test-outcome-logs">Outcome Logs</option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="colored-box-footer">
                                <button type="submit" class="btn btn-success btn-with-icon btn-confirm">Save</button>
                            </div>
                            <div class="color-box-loading"><div class="loading-content"><span class="loader"></span><div class="loading-text">SAVING YOUR DATA</div><div class="loading-wait">Please wait...</div></div></div>
                            {{ Form::close() }}
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="colored-box">
                        <div class="colored-box-header">Display Image</div>
                        <div class="colored-box-body">
                            {!! Form::model($community, ['id'=> 'profile-avatar-form', 'class' => 'standard-form', 'data-validate' => 'validate', 'files' => true, 'method' => 'PATCH', 'url' => getSiteUrl() . '/my-profile/']) !!}
                            <div class="colored-box-content image-management">
                                <div class="avatar-image">
                                    <img width="98" height="98" alt="Admin" class="avatar" src="{{ Auth::user()->getAvatar() }}">
                                </div>
                                <div class="avatar-description">
                                    <p>Your avatar will be used on your profile and throughout the site.</p>
                                    <p>Click below to select a JPG, GIF or PNG format photo from your computer and then click 'Upload Image' to proceed.</p>
                                    <div class="upload-file-field">
                                        <input type="file" name="image" class="input-file" data-file-type="image" data-file-extensions="(.jpg, .png, .gif or .jpeg file)" required data-msg-required="Please choose file" />
                                    </div>
                                    <button type="submit" class="btn btn-success btn-with-icon btn-add">Upload Image</button>
                                </div>
                            </div>
                            {!! Form::close() !!}
                        </div>
                    </div>

                    <div class="colored-box">
                        <div class="colored-box-header">
                            My Organization
                            <ul class="colored-box-header-actions">
                                <li><a href="#" data-tooltip="tooltip" data-container="body" title="Create Organization"><span class="create-icon"></span></a></li>
                                <li><a href="#" data-tooltip="tooltip" data-container="body" title="Join Organization"><span class="join-icon"></span></a></li>
                                <li><a href="#" data-tooltip="tooltip" data-container="body" title="Leave Organization"><span class="leave-icon"></span></a></li>
                            </ul>
                        </div>
                        <div class="colored-box-body">
                            <div class="table-responsive">
                                <table class="table colored-table">
                                    <tbody>
                                        <tr>
                                            <td class="col-sm-3">Name</td>
                                            <td>{{ Auth::user()->getMetaByKey('user_organisation') }}</td>
                                        </tr>
                                        <tr>
                                            <td>Website</td>
                                            <td>{{ Auth::user()->getMetaByKey('user_organisation_web') }}</td>
                                        </tr>
                                        <tr>
                                            <td>Description</td>
                                            <td>{{ Auth::user()->getMetaByKey('user_organisation_desc') }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@stop