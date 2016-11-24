@extends('app')

@section('content')

    <div class="container main-container">
        <div class="main-content user-info-page">
            <div class="page-title">
                <img src="{{ $user->getAvatar() }}" class="avatar" alt="">
                <div class="user-info-data">
                    <h1>{{ $user->getFullName()}}</h1>
                    @if($isSupport)
                        <p>
                            <strong>Email Address:</strong> <a href="mailto:{{ $user->user_email }}">{{ $user->user_email }}</a><br/>
                            @if($phoneNumber)
                                <strong>Phone Number:</strong> {{ $user->getMetaByKey('phone_number')}}
                            @endif
                        </p>
                    @endif
                </div>
            </div>

            @if($organisation && $isSupport)
                <div class="colored-box">
                    <div class="colored-box-header">Organisation</div>
                    <div class="colored-box-body">
                        <div class="colored-box-content">
                            <dl class="definition-list">
                                <dt>Name</dt>
                                <dd>{{ $organisation->organisation_name }}</dd>
                            </dl>
                            <dl class="definition-list">
                                <dt>Website</dt>
                                <dd>{{ $organisation->organisation_website }}</dd>
                            </dl>
                            <dl class="definition-list">
                                <dt>Description</dt>
                                <dd>{{ $organisation->organisation_description }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@stop