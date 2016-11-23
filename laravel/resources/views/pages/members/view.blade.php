@extends('app')

@section('content')

    <div class="container main-container">
        <div class="main-content user-info-page">
            <div class="page-title">
                <img src="{{ Auth::user()->getAvatar() }}" class="avatar" alt="">
                <div class="user-info-data">
                    <h1>{{ Auth::user()->getFullName()}}</h1>
                    <p>
                        <strong>Email Address:</strong> <a href="mailto:preproduction@mailinator.com" target="_blank">preproduction@mailinator.com</a><br/>
                        <strong>Phone Number:</strong> {{ Auth::user()->getMetaByKey('phone_number')}}
                    </p>
                </div>
            </div>

            <div class="colored-box">
                <div class="colored-box-header">Organisation</div>
                <div class="colored-box-body">
                    <div class="colored-box-content">
                        <dl class="definition-list">
                            <dt>Name</dt>
                            <dd>OrgName</dd>
                        </dl>
                        <dl class="definition-list">
                            <dt>Website</dt>
                            <dd>{{ getSiteUrl() }}</dd>
                        </dl>
                        <dl class="definition-list">
                            <dt>Description</dt>
                            <dd>Some long description Some long description Some long description Some long description Some long description Some long description Some long description Some long description Some long description Some long description</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop