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
                            {!! Form::model($community, ['id'=> 'profile-details-form', 'data-save-method' => 'ajax', 'data-validate'=>'validate', 'method' => 'POST', 'url' => getSiteUrl() . '/my-profile']) !!}
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
                                    <input type="email" name="email" class="form-control" id="profileEmail" required="required"  value="{{ Auth::user()->user_email }}">
                                </div>
                                <div class="form-group">
                                    <label for="phoneNumber">Phone Number</label>
                                    <input type="text" name="phone_number" class="form-control" id="phoneNumber" required="required"  value="{{ Auth::user()->getMetaByKey('phone_number') }}">
                                </div>
                                <div class="form-group">
                                    <label for="password">Password</label>
                                    <input type="password" name="password" class="form-control" id="password"  value="">
                                </div>
                                <div class="form-group">
                                    <label for="confirmPassword">Confirm Password</label>
                                    <input type="password" name="confirm_password" class="form-control" id="confirmPassword"  value="">
                                </div>
                                <div class="form-group">
                                    <label for="currentPassword">Current Password</label>
                                    <input type="password" name="current_password" class="form-control" id="currentPassword"  value="">
                                </div>
                                <div class="form-group">
                                    <label for="profileBiography">About me</label>
                                    <textarea name="biography" class="form-control" id="profileBiography">{{ Auth::user()->getMetaByKey('description') }}</textarea>
                                </div>
                                <div class="form-group">
                                    <label for="timezoneSettings">Timezone settings</label>
                                    <?php $timezone_list = DateTimeZone::listIdentifiers(DateTimeZone::ALL); ?>
                                    <select name="timezone_settings" class="form-control" id="timezoneSettings">
                                        <?php
                                            $dateTimeZoneGmt = new DateTimeZone("GMT");
                                            $dateTimeGmt = new DateTime("now", $dateTimeZoneGmt);
                                            $userTimezone = Auth::user()->getMetaByKey('timezone');
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
                                        <option value="{{ $t }}" @if($t == $userTimezone) selected="selected" @endif>{{ $t . ''.$timestr }}</option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                     <?php
                                        $userFirstPage = Auth::user()->getMetaByKey('dashboard_page_url');
                                    ?>
                                    <label for="desiredFirstPage">Desired First Page</label>
                                    <select name="first_page" class="form-control" id="desiredFirstPage">
                                        @if(is_organisation_admin())
                                            <option value="{{ getSiteUrl() }}/my-organisation">Organization</option>
                                        @endif
                                        <option value="/my-communities" @if($userFirstPage == '/my-communities') selected="selected" @endif>Communities</option>
                                        <option value="/my-test-suites" @if($userFirstPage == '/my-test-suites') selected="selected" @endif>Test Suites</option>
                                        <option value="/my-products" @if($userFirstPage == '/my-products') selected="selected" @endif>Products</option>
                                        <option value="/test-suite-coverage" @if($userFirstPage == '/test-suite-coverage') selected="selected" @endif>Coverage</option>
                                        <option value="/verify-requests" @if($userFirstPage == '/verify-requests') selected="selected" @endif>Verify Requests</option>
                                        <option value="/my-transaction-log" @if($userFirstPage == '/my-transaction-log') selected="selected" @endif>Test Results</option>
                                        <option value="/my-support-tickets" @if($userFirstPage == '/my-support-tickets') selected="selected" @endif>Support</option>
                                        <option value="/my-profile" @if($userFirstPage == '/my-profile') selected="selected" @endif>Profile</option>
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
                            {!! Form::model($community, ['id'=> 'profile-avatar-form', 'data-redirect-after-submit' => '/my-profile/', 'class' => 'standard-form', 'data-save-method' => 'ajax', 'data-validate' => 'validate', 'files' => true, 'method' => 'POST', 'url' => getSiteUrl() . '/my-profile/avatar']) !!}
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
                            <div class="colored-box-footer">
                            </div>
                            <div class="color-box-loading"><div class="loading-content"><span class="loader"></span><div class="loading-text">SAVING YOUR DATA</div><div class="loading-wait">Please wait...</div></div></div>
                            {!! Form::close() !!}
                        </div>
                    </div>

                    <?php
                        $isOrganisationMember = !empty(Auth::user()->organisation[0]);
                    ?>
                    <div class="colored-box">
                        <div class="colored-box-header">
                            My Organization
                            <ul class="colored-box-header-actions">
                                @if(!$isOrganisationMember)
                                <li><a href="#createOrganizationModal" data-toggle="modal" data-tooltip="tooltip" data-container="body" title="Create Organization"><span class="create-icon"></span></a></li>
                                <li><a href="#joinOrganizationModal" data-toggle="modal" data-tooltip="tooltip" data-container="body" title="Join Organization"><span class="join-icon"></span></a></li>
                                @else
                                    @if(!is_organisation_admin())
                                        <li><a href="#leaveOrganizationModal" data-toggle="modal" data-tooltip="tooltip" data-container="body" title="Leave Organization"><span class="leave-icon"></span></a></li>
                                    @endif
                                @endif
                            </ul>
                        </div>
                        <div class="colored-box-body">
                            <div class="table-responsive">
                                <table class="table colored-table">
                                    <tbody>
                                        @if($isOrganisationMember)
                                            <tr>
                                                <td class="col-sm-3">Name</td>
                                                <td>{{ Auth::user()->organisation[0]->organisation_name }}</td>
                                            </tr>
                                            <tr>
                                                <td>Website</td>
                                                <td>{{ Auth::user()->organisation[0]->organisation_website }}</td>
                                            </tr>
                                            <tr>
                                                <td>Description</td>
                                                <td>{{ Auth::user()->organisation[0]->organisation_description }}</td>
                                            </tr>
                                        @else
                                            <tr>
                                                <td colspan="2">
                                                    At present you are not part of any organization known to Drummond Group TWAIN Testing Platform.
                                                    If you plan to undertake testing with Drummond Group TWAIN Testing Platform, you either need to
                                                    <a href="#joinOrganizationModal" data-toggle="modal" data-tooltip="tooltip" data-container="body">join an existing organization</a> or
                                                    <a href="#createOrganizationModal" data-toggle="modal" data-tooltip="tooltip" data-container="body">create a new organization</a>
                                                     and become its administrator.
                                                    To join an existing organization, you will need to know its organization key, which your organization administrator can provide.
                                                    To create a new organization, you will need its name as a minimum.
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>


    <div class="modal fade" id="createOrganizationModal" tabindex="-1" role="dialog" data-backdrop="static">
        <div class="modal-dialog" role="document">
            <div class="modal-content block-loading-wrapper">
                {!! Form::model($community, ['id'=> 'createOrganizationForm', 'data-ajax-form'=>'true', 'data-notification-container'=>'.create-error-box', 'data-redirect-after-submit' => '/my-profile/', 'data-validate' => 'validate', 'method' => 'POST', 'url' => getSiteUrl() . '/my-organisation/create/']) !!}
                    <div class="modal-header">
                        <button type="button" class="close-modal" title="Close popup" data-dismiss="modal" aria-label="Close">Close</button>
                        Create Organization
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="organizationName">Name</label>
                            <input type="text" name="organization_name" class="form-control" id="organizationName" required="required" value="">
                        </div>
                        <div class="form-group">
                            <label for="organizationWebsite">Website</label>
                            <input type="text" name="organization_website" class="form-control" id="organizationWebsite" value="">
                        </div>
                        <div class="form-group">
                            <label for="organizationDescription">Description</label>
                            <textarea name="organization_description" class="form-control" id="organizationDescription"></textarea>
                        </div>
                        <div class="create-error-box"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success btn-with-icon btn-confirm">Confirm</button>
                        <button class="btn btn-default btn-with-icon btn-cancel" data-dismiss="modal">Cancel</button>
                    </div>
                    <div class="block-loading form-loading">
                        <div class="loading-content">
                            <span class="loader"></span>
                            <div class="loading-text">CREATING</div>
                            <div class="loading-wait">Please wait...</div>
                        </div>
                    </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>

    <div class="modal fade" id="joinOrganizationModal" tabindex="-1" role="dialog" data-backdrop="static" data-save-method="ajax">
        <div class="modal-dialog" role="document">
            <div class="modal-content block-loading-wrapper">
                {!! Form::model('', ['id'=> 'joinOrganizationForm', 'data-ajax-form'=>'true', 'data-notification-container'=>'.join-error-box', 'data-redirect-after-submit' => '/my-profile/', 'data-validate' => 'validate', 'method' => 'POST', 'url' => getSiteUrl() . '/my-organisation/join/']) !!}
                    <div class="modal-header">
                        <button type="button" class="close-modal" title="Close popup" data-dismiss="modal" aria-label="Close">Close</button>
                        Join Organization
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="organizationKey">Organization Key</label>
                            <input type="text" name="organization_key" class="form-control" id="organizationKey" required="required" value="">
                        </div>
                        <div class="join-error-box"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success btn-with-icon btn-confirm">Confirm</button>
                        <button class="btn btn-default btn-with-icon btn-cancel" data-dismiss="modal">Cancel</button>
                    </div>
                    <div class="block-loading form-loading">
                        <div class="loading-content">
                            <span class="loader"></span>
                            <div class="loading-text">LOADING</div>
                            <div class="loading-wait">Please wait...</div>
                        </div>
                    </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>

    <div class="modal fade" id="leaveOrganizationModal" tabindex="-1" role="dialog" data-backdrop="static">
        <div class="modal-dialog" role="document">
            <div class="modal-content block-loading-wrapper">
                {!! Form::model('', ['id'=> 'leaveOrganizationForm', 'data-ajax-form'=>'true', 'data-notification-container'=>'.leave-error-box', 'data-redirect-after-submit' => '/my-profile/', 'data-validate' => 'validate', 'method' => 'POST', 'url' => getSiteUrl() . '/my-organisation/leave']) !!}
                    <div class="modal-header">
                        <button type="button" class="close-modal" title="Close popup" data-dismiss="modal" aria-label="Close">Close</button>
                        Leave Organization
                    </div>
                    <div class="modal-body">
                        Are you sure that you want to leave this organization?
                        <div class="leave-error-box"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success btn-with-icon btn-confirm">Confirm</button>
                        <button class="btn btn-default btn-with-icon btn-cancel" data-dismiss="modal">Cancel</button>
                    </div>
                    <div class="block-loading form-loading">
                        <div class="loading-content">
                            <span class="loader"></span>
                            <div class="loading-text">CREATING</div>
                            <div class="loading-wait">Please wait...</div>
                        </div>
                    </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>

@stop

@section('page-scripts')
    <script>
        jQuery(document).ready(function ($) {
            $('.orgPopups').on('submit', function (e) {
                e.preventDefault();
            })
        });
    </script>
@stop
