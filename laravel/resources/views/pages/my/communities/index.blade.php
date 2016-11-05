@extends('app')
@section('content')
    <div class="container main-container">
        @include('pages.user-tabs', ['tab' => 'my-communities'])
        <div class="main-content">
            <div class="table-responsive">
                <table class="table colored-table my-communities-table">
                    <thead>
                        <tr>
                            <th class="text-left">Name</th>
                            <th class="text-left">Since</th>
                            <th class="text-left">Role</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    @if($userCommunities)
                    <tbody>
                        @foreach($userCommunities as $userCommunity)
                            <tr>
                                <td><a href="/communities/{{ $userCommunity->community->slug }}">{{ $userCommunity->community->title }}</a></td>
                                <td class="text-nowrap">{{ formatDate($userCommunity->created_at) }}</td>
                                <td class="text-nowrap">{{ $userCommunity->getRoleName() }}</td>
                                <td class="text-center">
                                    @if(!($userCommunity->community->isAdmin() && count($userCommunity->community->getAdmins()) == 1))
                                        <a class="btn btn-danger btn-icon btn-delete" data-toggle="modal" href="#confirmCancelMembership{{ $userCommunity->community->slug }}" data-tooltip="tooltip" data-container="body" title="Remove Membership">Cancel Membership</a>
                                        @include('pages.communities.partials.show.button.confirm-leave-community', ['community' => $userCommunity->community])
                                    @else
                                        <span class="btn btn-default btn-icon btn-delete" data-tooltip="tooltip" data-container="body" title="This community must have at least one admin"></span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="4" class="text-center">You do not have any community subscriptions yet</td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@stop

@section('page-scripts')
    <script type="application/javascript">
        jQuery(document).ready(function () {
            Page.communities.init();
        });
    </script>
@stop