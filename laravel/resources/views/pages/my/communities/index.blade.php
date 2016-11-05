@extends('app')
@section('content')
    <div class="container main-container">
        @include('pages.user-tabs', ['tab' => 'my-communities'])
        <div class="main-content">
            <div>
                <table>
                    <tr>
                        <th>Name</th>
                        <th>Since</th>
                        <th>Role</th>
                        <th>Action</th>
                    </tr>
                    @if($userCommunities)
                        @foreach($userCommunities as $userCommunity)
                            <tr>
                                <td><a href="/communities/{{ $userCommunity->community->slug }}">{{ $userCommunity->community->title }}</a></td>
                                <td>{{ formatDate($userCommunity->created_at) }}</td>
                                <td>{{ $userCommunity->getRoleName() }}</td>
                                <td>
                                    @if(!($userCommunity->community->isAdmin() && count($userCommunity->community->getAdmins()) == 1))
                                        <a class="btn btn-danger btn-lg joinCommunity" href="#confirmCancelMembership{{ $userCommunity->community->slug }}">Cancel Membership</a>
                                        @include('pages.communities.partials.show.button.confirm-leave-community', ['community' => $userCommunity->community])
                                    @else
                                        <a href="#" class="btn btn-icon greyed-out-btn" title="This community must have at least one admin"></a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="4" class="text-center">You do not have any community subscriptions yet</td>
                        </tr>
                    @endif
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