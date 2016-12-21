@extends('app')
@section('content')
    <div class="container main-container">
        @include('pages.user-tabs', ['tab' => 'my-organisation'])
        <div class="main-content">
            <div class="tabs-menu">
                <ul class="organisation-tabs" role="tablist">
                    <li class="active"><a href="#organisation-users">Users</a></li>
                    <li><a href="#organisation-subscriptions">Subscriptions</a></li>
                    <li><a href="#organisation-profile">Profile</a></li>
                </ul>
            </div>

            <div class="tab-content sub-tabs-content block-loading-wrapper">
                <div role="tabpanel" class="tab-pane active" id="organisation-users">
                    <div class="colored-box org-users-box">
                        <div class="colored-box-header">Users</div>
                        <div class="colored-box-body">
                            <div class="table-responsive">
                                <table class="table colored-table">
                                    <thead>
                                    <tr>
                                        <th class="text-left">Name</th>
                                        <th class="text-left">Email</th>
                                        <th>Role(s)</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach(Auth::user()->organisation[0]->membersList as $member)
                                        <tr>
                                            <td>{{ $member->user ? $member->user->getFullname() : '' }}</td>
                                            <td>{{ $member->user->user_email }}</td>
                                            <td class="text-center">{{ $member->is_admin ? 'Admin' : 'Member' }}</td>
                                            <td class="text-center">
                                                @if($member->user_id != Auth::user()->ID)
                                                    <a href="#modalRemoveMember{{ $member->user_id }}" data-toggle="modal" class="btn btn-danger btn-icon btn-delete"
                                                       data-tooltip="tooltip"
                                                       title="Delete Member"></a>
                                                    {{-- Remove profile Confirmation Modal--}}
                                                    <div class="modal fade profile-modal" id="modalRemoveMember{{ $member->user_id }}" tabindex="-1" role="dialog">
                                                        <div class="modal-dialog" role="document">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <button type="button" class="close-modal" data-tooltip="tooltip" title="Close popup" data-placement="left"
                                                                            data-dismiss="modal" aria-label="Close">Close
                                                                    </button>
                                                                    Confirm Membership Deletion
                                                                </div>
                                                                <div class="modal-body">
                                                                    Are you sure that you want to delete this person as a member of the organization?
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <a href="/my-organisation/delete_member/{{ $member->user_id }}"
                                                                       class="btn btn-success btn-with-icon btn-confirm delete_member">Confirm</a>
                                                                    <a href="#" class="btn btn-default btn-with-icon btn-cancel" data-dismiss="modal">Cancel</a>
                                                                </div>
                                                                <div class="block-loading">
                                                                    <div class="loading-content"><span class="loader"></span>
                                                                        <div class="loading-text">LOADING DATA</div>
                                                                        <div class="loading-wait">Please wait...</div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @else
                                                    <button type="button" class="btn btn-disabled btn-icon btn-delete" data-tooltip="tooltip" data-container="body" title="You can't delete the organisation admin from the organisation"></button>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div role="tabpanel" class="tab-pane" id="organisation-subscriptions">
                    <div class="colored-box org-users-box">
                        <div class="colored-box-header">Subscriptions</div>
                        <div class="colored-box-body">
                            <div class="table-responsive">
                                <table class="table colored-table">
                                    <thead>
                                    <tr>
                                        <th class="text-left">Community</th>
                                        <th class="text-left">Test Suite</th>
                                        <th class="text-left">Nickname</th>
                                        <th class="text-left">Assignee</th>
                                        <th>Status</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach(Auth::user()->organisation[0]->subscriptions as $subscription)
                                        <tr>
                                            <td>{{ $subscription->testSuite->community->title }}</td>
                                            <td>{{ $subscription->testSuite->full_name }}</td>
                                            <td>{{ $subscription->nickname }}</td>
                                            <td>{{ $subscription->user ? $subscription->user->getFullName() : '' }}</td>
                                            <td class="text-center"><span class="status status-active">Active</span></td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div role="tabpanel" class="tab-pane" id="organisation-profile">
                    {!! Form::model($community, ['id'=> 'saveOrganizationForm', 'data-ajax-form'=>'true', 'data-notification-container'=>'.error-box', 'data-validate' => 'validate', 'method' => 'POST', 'url' => getSiteUrl() . '/my-organisation/save']) !!}
                    <div class="row">
                        <div class="col-sm-6">

                            {{-- Details --}}
                            <div class="colored-box">
                                <div class="colored-box-header">Details</div>
                                <div class="colored-box-body">
                                    <div class="colored-box-content">
                                        <div class="form-group">
                                            <label for="organisationName">Name:</label>
                                            <input type="text" id="organisationName" name="organisation_name" class="form-control" value="{{ $organisation->organisation_name }}"
                                                   required>
                                        </div>
                                        <div class="form-group" id="organisation-key-row">
                                            <label for="organisationKey">Key:</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" readonly id="organisationKey" value="{{ $organisation->organisation_key }}"/>
                                                    <span class="input-group-btn">
                                                        <button type="button" class="btn btn-success copyOrgKeyLink" data-clipboard-target="#organisationKey" data-tooltip="tooltip"
                                                                title="Copy to Clipboard">Copy Key</button>
                                                    </span>
                                            </div>
                                            <div id="copiedMessage" style="display: none;" class="success-message">Organization Key has been copied to clipboard.</div>
                                        </div>
                                        <div class="form-group">
                                            <label for="organisationDescription">Description:</label>
                                            <textarea rows="5" class="form-control organisation-description" id="organisationDescription"
                                                      name="organisation_description" required>{{ $organisation->organisation_description }}</textarea>
                                        </div>
                                        <div class="form-group">
                                            <label for="organisationWebsite">Website:</label>
                                            <input type="text" id="organisationWebsite" name="organisation_website" class="form-control"
                                                   value="{{ $organisation->organisation_website }}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Primary Contact --}}
                            <div class="colored-box">
                                <div class="colored-box-header">Primary Contact</div>
                                <div class="colored-box-body">
                                    <div class="colored-box-content">
                                        <div class="form-group">
                                            <label for="primaryContactFirstName">First Name:</label>
                                            <input type="text" id="primaryContactFirstName" name="contact_first_name" class="form-control"
                                                   value="{{ $organisation->contact_first_name }}">
                                        </div>
                                        <div class="form-group">
                                            <label for="primaryContactLastName">Last Name:</label>
                                            <input type="text" id="primaryContactLastName" name="contact_last_name" class="form-control"
                                                   value="{{ $organisation->contact_last_name }}">
                                        </div>
                                        <div class="form-group">
                                            <label for="primaryContactEmail">Email:</label>
                                            <input type="text" id="primaryContactEmail" name="contact_email" class="form-control" value="{{ $organisation->contact_email }}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Secondary Contact --}}
                            <div class="colored-box">
                                <div class="colored-box-header">Secondary Contact</div>
                                <div class="colored-box-body">
                                    <div class="colored-box-content">
                                        <div class="form-group">
                                            <label for="secondaryContactFirstName">First Name:</label>
                                            <input type="text" id="secondaryContactFirstName" name="secondary_contact_first_name" class="form-control"
                                                   value="{{ $organisation->secondary_contact_first_name }}">
                                        </div>
                                        <div class="form-group">
                                            <label for="secondaryContactLastName">Last Name:</label>
                                            <input type="text" id="secondaryContactLastName" name="secondary_contact_last_name" class="form-control"
                                                   value="{{ $organisation->secondary_contact_last_name }}">
                                        </div>
                                        <div class="form-group">
                                            <label for="secondaryContactEmail">Email:</label>
                                            <input type="text" id="secondaryContactEmail" name="secondary_contact_email" class="form-control"
                                                   value="{{ $organisation->secondary_contact_email }}">
                                        </div>
                                    </div>
                                </div>
                            </div>


                        </div>
                        <div class="col-sm-6">

                            {{-- Billing Address --}}
                            <div class="colored-box">
                                <div class="colored-box-header">Billing Address</div>
                                <div class="colored-box-body">
                                    <div class="colored-box-content">
                                        <div class="form-group">
                                            <label for="billingAddressAttention">Attention:</label>
                                            <input type="text" id="billingAddressAttention" name="billing_address_attention" class="form-control"
                                                   value="{{ $organisation->billing_address_attention }}">
                                        </div>
                                        <div class="form-group">
                                            <label for="billingAddressLine1">Address Line 1:</label>
                                            <input type="text" id="billingAddressLine1" name="billing_address1" class="form-control" value="{{ $organisation->billing_address1 }}">
                                        </div>
                                        <div class="form-group">
                                            <label for="billingAddressLine2">Address Line 2:</label>
                                            <input type="text" id="billingAddressLine2" name="billing_address2" class="form-control" value="{{ $organisation->billing_address2 }}">
                                        </div>
                                        <div class="form-group">
                                            <label for="billingAddressLine3">Address Line 3:</label>
                                            <input type="text" id="billingAddressLine3" name="billing_address3" class="form-control" value="{{ $organisation->billing_address3 }}">
                                        </div>
                                        <div class="form-group">
                                            <label for="billingAddressLine4">Address Line 4:</label>
                                            <input type="text" id="billingAddressLine4" name="billing_address4" class="form-control" value="{{ $organisation->billing_address4 }}">
                                        </div>
                                        <div class="form-group">
                                            <label for="billingAddressCity">City:</label>
                                            <input type="text" id="billingAddressCity" name="billing_city" class="form-control" value="{{ $organisation->billing_city }}">
                                        </div>
                                        <div class="form-group">
                                            <label for="billingAddressState">State:</label>
                                            <input type="text" id="billingAddressState" name="billing_state" class="form-control" value="{{ $organisation->billing_state }}">
                                        </div>
                                        <div class="form-group">
                                            <label for="billingAddressPostalCode">Postal Code:</label>
                                            <input type="text" id="billingAddressPostalCode" name="billing_postcode" class="form-control"
                                                   value="{{ $organisation->billing_postcode }}">
                                        </div>
                                        <div class="form-group">
                                            <label for="billingAddressCountry">Country:</label>
                                            <input type="text" id="billingAddressCountry" name="billing_country" class="form-control" value="{{ $organisation->billing_country }}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Telephone --}}
                            <div class="colored-box">
                                <div class="colored-box-header">Telephone</div>
                                <div class="colored-box-body">
                                    <div class="colored-box-content">
                                        <div class="form-group">
                                            <label for="billingTelephoneCountryCode">Country Code:</label>
                                            <input type="text" id="billingTelephoneCountryCode" name="phonenumber_countrycode" class="form-control"
                                                   value="{{ $organisation->phonenumber_countrycode }}">
                                        </div>
                                        <div class="form-group">
                                            <label for="billingTelephoneAreaCode">Area Code:</label>
                                            <input type="text" id="billingTelephoneAreaCode" name="phonenumber_areacode" class="form-control"
                                                   value="{{ $organisation->phonenumber_areacode }}">
                                        </div>
                                        <div class="form-group">
                                            <label for="billingTelephoneNumber">Number:</label>
                                            <input type="tel" id="billingTelephoneNumber" name="phonenumber" class="form-control" value="{{ $organisation->phonenumber }}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    {{-- Manufacturer List --}}
                    <div class="colored-box">
                        <div class="colored-box-header">Manufacturer List Aliases</div>
                        <div class="colored-box-body">
                            <div class="colored-box-content">
                                <div class="row">
                                    <div class="col-sm-7">
                                        <div class="form-group">
                                            <textarea rows="2" id="manufacturerListAliases" name="products_organisations"
                                                      class="form-control">{{ implode(', ', $organisation->products_organisations) }}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-sm-5">
                                        <p class="field-light-description">List of manufacturer aliases for product registration with public visibility(semicolon separated) </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="error-box"></div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-success btn-with-icon btn-confirm">Save</button>
                    </div>
                    @include('loader', ['loaderClass' => 'form-loading', 'loaderMessage' => 'SAVING...'])

                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
@stop

@section('page-scripts')
    <script src="/laravel/resources/assets/js/vendor/clipboard.js"></script>
    <script>
        jQuery(document).ready(function () {
            $('.organisation-tabs a').click(function (e) {
                e.preventDefault();
                $(this).tab('show')
            });

            //open needed tab on browser's back / forward click
            $(window).on('hashchange', function (e) {
                var url = document.location.toString();
                if (url.match('#')) {
                    $('.organisation-tabs a[href="#' + url.split('#')[1] + '"]').tab('show');
                }
            });

            var url = document.location.toString();
            if (url.match('#')) {
                $('.organisation-tabs a[href="#' + url.split('#')[1] + '"]').tab('show');
            }

            $('.organisation-tabs a').on('shown.bs.tab', function (e) {
                window.location.hash = e.target.hash;
                window.scrollTo(0, 0);
            })

        });

        var clipboard = new Clipboard('.copyOrgKeyLink');
        clipboard.on('success', function () {
            window.clearTimeout(messageTimeoutHandler);
            var successMessage = jQuery('#organisation-key-row #copiedMessage');
            successMessage.show();
            var messageTimeoutHandler = setTimeout(function () {
                successMessage.hide()
            }, 2000);
        });


        $('.delete_member').on('click', function (e) {
            e.preventDefault();

            var elem = jQuery(this);
            var loading = elem.closest('.modal-dialog').find('.block-loading');
            loading.show();

            jQuery.ajax({
                url: elem.attr('href'),
                type: 'delete',
                dataType: 'json',
                success: function () {
                    $('.modal').modal('hide');
                    loading.hide();
                    $('.org-users-box').prepend('<div class="success-message">User has been removed from your organization!</div>');
                    setTimeout(function () {
                        elem.closest('tr').slideUp(function () {
                            $(this).remove();
                        });
                        $('.success-message').slideUp(function () {
                            $(this).remove();
                        });
                    }, 3000);

                }
            });
        });


    </script>
@stop