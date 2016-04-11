@extends('app')

@section('content')
    <div class="container main-container">

        {{--<div class="error-message lg">Error message</div>--}}
        {{--<div class="success-message lg">Success message</div>--}}

        <div class="main-content">

            <div class="create-community">
                <div class="page-title">
                    <h1>Create a Community </h1>
                </div>

                <div class="community-admin">

                    @include('pages.communities.partials.errors')

                    {{ Form::open(['file' => true, 'enctype' => 'multipart/form-data', 'class' => 'form', 'url' => '/communities', 'data-validate' => 'validate' ]) }}
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="colored-box">
                                <div class="colored-box-header">Details</div>
                                <div class="colored-box-body">
                                    <div class="colored-box-content">
                                        <div class="form-group">
                                            <label for="community-name">Community Name (required):</label>
                                            {!! Form::text('title', false,  [
                                                'class' => 'form-control',
                                                'id' => 'community-name',
                                                'size' => '80',
                                                'required' => 'required',
                                                'data-msg-required' => 'Please fill this field',
                                            ]) !!}
                                        </div>
                                        <div class="form-group">
                                            <label for="community-desc">Community Description (required): </label>

                                            {!! Form::textarea('description', false,  [
                                                'class' => 'form-control',
                                                'id' => 'community-desc',
                                                'rows' => '5',
                                                'required' => 'required',
                                                'data-msg-required' => 'Please fill this field',
                                            ]) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="colored-box">
                                <div class="colored-box-header">Display Image</div>
                                <div class="colored-box-body">
                                    <div class="colored-box-content community-image-management">
                                        <div class="community-image">
                                            <img src="/laravel/resources/assets/images/gravatar.jpg" alt="">
                                        </div>
                                        <div class="community-avatar-description">
                                            <p>Upload an image to use as an avatar for this community. The image
                                                will be shown on the main community page, and in search results.</p>

                                            <p>Click below to select a JPG, GIF or PNG format photo from your
                                                computer and then click 'Upload Image' to proceed.</p>

                                            <div class="upload-file-field">
                                                <input type="file" name="image" class="input-file"
                                                       data-file-type="image"
                                                       data-file-extensions="(.jpg, .png, .gif or .jpeg file)"/>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="col-sm-6">
                            <div class="colored-box">
                                <div class="colored-box-header">Community Articles</div>
                                <div class="colored-box-body">
                                    <div class="colored-box-content">
                                        <div class="form-group">
                                            <label for="articles_enabled">
                                                {!! Form::checkbox('articles_enabled', false,  [
                                                   'id' => 'bp-docs-group-enable',
                                               ]) !!}
                                                Enable Articles for this community</label>
                                        </div>
                                        <div class="form-group">
                                            <label for="minimum_role">Minimum role to associate
                                                Article with this community:</label>
                                            {!! Form::select('articles_status',
                                               [
                                                   'admin' => 'Community Admin',
                                                   'mod' => 'Community Support',
                                                   'member' => 'Community Member'
                                               ],
                                               'mod',
                                               [
                                                   'class' => 'form-control',
                                                   'id' => 'minimum_role',
                                               ]) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="colored-box">
                                <div class="colored-box-header">Privacy Options</div>
                                <div class="colored-box-body">
                                    <div class="colored-box-content">
                                        <div class="form-group">
                                            <label>
                                                {!! Form::radio('visibility_status', 'public') !!}
                                                <strong>This is a public community</strong>
                                            </label>
                                            <ul class="privacy-options-list">
                                                <li>Any site member can join this community.</li>
                                                <li>This community will be listed in the communities directory and
                                                    in search results.
                                                </li>
                                                <li>Community content and activity will be visible to any site
                                                    member.
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="form-group">
                                            <label>
                                                {!! Form::radio('visibility_status', 'private', true) !!}
                                                <b>This is a private community</b>
                                            </label>
                                            <ul class="privacy-options-list">
                                                <li>Only users who request membership and are accepted can join the
                                                    community.
                                                </li>
                                                <li>This community will be listed in the communities directory and
                                                    in search results.
                                                </li>
                                                <li>Community content and activity will only be visible to members
                                                    of the community.
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="form-group">
                                            <label>
                                                {!! Form::radio('visibility_status', 'hidden') !!}
                                                <b>This is a hidden community</b>
                                            </label>
                                            <ul class="privacy-options-list">
                                                <li>Only users who are invited can join the community.</li>
                                                <li>This community will not be listed in the communities directory
                                                    or search results.
                                                </li>
                                                <li>Community content and activity will only be visible to members
                                                    of the community.
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    {{ csrf_field() }}

                    <button type="submit" class="btn btn-success btn-lg">Create Community</button>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
@stop