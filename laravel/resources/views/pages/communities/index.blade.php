@extends('app')

@section('content')
    <div class="container main-container">

        <div class="main-content">

            <div class="table-responsive community-lists">
                <table class="table">
                    <thead>
                    <tr>
                        <th colspan="2" class="text-left">Community Name</th>
                        <th class="col-sm-1">Test Suites</th>
                        <th class="col-sm-1 text-left">Members</th>
                        <th>Compliant Products</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                      @foreach($communities as $community)
                            <tr>
                                <td class="community-image">
                                    <a href="communities-test-suites.php"><img width="50" height="50"
                                                                                                       title="{{ $community->title }}"
                                                                                                       alt="Community logo of {{ $community->title }}"
                                                                                                       src="{{ $community->getImageUrl() }}"></a>
                                </td>
                                <td class="community-name">
                                    <a href="/communities/{{ $community->slug }}">{{ $community->title }}</a>

                                    <p>{{ $community->description }}</p>
                                </td>
                                <td class="text-center">4</td>
                                <td class="text-center">11</td>
                                <td class="text-center">0</td>
                                <td class="text-center"><a class="btn btn-danger btn-lg joinCommunity"
                                                           href="#confirmJoinCommunity" data-community-id="1">Join Community</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="confirmCancelMembership" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close-modal" data-tooltip="tooltip" title="Close popup"
                            data-placement="left" data-dismiss="modal" aria-label="Close">Close
                    </button>
                    Confirm Community Membership Cancellation
                </div>
                <div class="modal-body">
                    This will cancel your membership of the SuperStream community. Are you sure?
                </div>
                <div class="modal-footer">
                    <a class="btn btn-success btn-with-icon btn-confirm">Confirm</a>
                    <button type="button" class="btn btn-default btn-with-icon btn-cancel" data-dismiss="modal">Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal modal-small fade" id="confirmJoinCommunity" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close-modal" data-tooltip="tooltip" title="Close popup"
                            data-placement="left" data-dismiss="modal" aria-label="Close">Close
                    </button>
                    Community Registration
                </div>
                <div class="modal-body">
                    You need to join the community of interest in order to view Test Cases and Participate in the Forum
                    <div class="popup-terms-box">
                        <input type="checkbox" id="agree_community_terms" value="agree" name="agree_terms"> I agree with
                        <a href="#readTermsAndConditions" data-toggle="modal" data-dismiss="modal">Terms &amp;
                            Conditions</a>
                    </div>
                </div>
                <div class="modal-footer">
                    <a class="btn btn-success btn-with-icon btn-confirm" id="registerInCommunity">Register</a>
                    <button type="button" class="btn btn-default btn-with-icon btn-cancel" data-dismiss="modal">Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="readTermsAndConditions" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close-modal" data-tooltip="tooltip" title="Close popup"
                            data-placement="left" data-target="#confirmJoinCommunity" data-toggle="modal"
                            data-dismiss="modal" aria-label="Close">Close
                    </button>
                    Terms and Conditions
                </div>
                <div class="modal-body">
                    <div class="modal-terms-content">
                        <p>These terms and conditions are specific to the SuperStream Community and supplement the
                            general terms and conditions for registration on <a href="#">www.compliancetest.net</a></p>
                        <ol>
                            <li>In order to access the SuperStream Community features, you must become a member of the
                                community.
                            </li>
                            <li>Membership of the SuperStream Community is limited to persons or organisations that have
                                an active role in implementation (or implementation support) of the SuperStream
                                standards (eg Funds, Administrators, Employers, Gateways, Software providers).
                            </li>
                            <li>We reserve the right to terminate your membership at any time if you breach these terms
                                and conditions.
                            </li>
                            <li>You are responsible for all and any Content you contribute via the community forum or
                                wiki. When you provide Content you retain ownership of the intellectual property in that
                                information however you grant all current and future community members unrestricted and
                                royalty free rights to use your content for any purpose that supports the implementation
                                of SuperStream stndards. This licence ends when you cease your membership except for
                                Content which has already been released as part of the Service.
                            </li>
                            <li>We reserve the right but will not have an obligation to remove or refuse to distribute
                                any Content. We also reserve the right to adapt or modify your Content for any reason
                                including for distribution purposes.&nbsp;</li>
                            <li>By posting Content on this website, you provide us with an undertaking that such Content
                                does not infringe the rights of someone else and that it does not violate the law in any
                                other way such as by being defamatory, being of racist content or is
                                threatening.&nbsp;</li>
                            <li>To the extent permitted by law, you release and discharge us from any liability or claim
                                arising out of any loss or damage that may be suffered or incurred as a result of your
                                participation in the SuperStream community.&nbsp;</li>
                            <li>We respect your privacy. Our compliance with privacy legislation is set out in our
                                separate Privacy Policy which may be accessed from our home page.
                            </li>
                        </ol>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-with-icon btn-cancel"
                            data-target="#confirmJoinCommunity" data-toggle="modal" data-dismiss="modal">Cancel
                    </button>
                    <a class="btn btn-success btn-with-icon btn-confirm"
                       onclick="document.getElementById('agree_community_terms').checked = true"
                       data-target="#confirmJoinCommunity" data-toggle="modal" data-dismiss="modal">Agree</a>
                </div>
            </div>
        </div>
    </div>
@stop