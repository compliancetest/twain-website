@extends('app')

@section('content')

    <div class="container main-container">
            <div class="main-content">

                <div class="page-title">
                    <h1>Create New Article</h1>
                </div>

                <div class="community-tabs article-create">

                    @include('pages.communities.partials.errors')

                    {{ Form::open(['name' => 'article-create-form', 'file' => true, 'method' => 'post', 'enctype' => 'multipart/form-data', 'url' => getSiteUrl() . '/articles/' . $community->slug]) }}

                        <div class="colored-box">
                            <div class="colored-box-header">Information</div>
                            <div class="colored-box-body">
                                <div class="colored-box-content">
                                    <div class="form-group">
                                         {!! Form::label('title', 'Title:') !!}
                                         {!! Form::text('title', null, ['class' => 'form-control', 'required', 'data-msg-required' => 'Title is required']) !!}
                                    </div>

                                    <div class="form-group">
                                        {!! Form::label('content', 'Content:') !!}
                                        {!! Form::textarea('content', null, ['cols' => '20', 'rows' => 5, 'class' => 'redactor_editor']) !!}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="colored-box">
                            <div class="colored-box-header">Attachments</div>
                            <div class="colored-box-body">
                                <div class="colored-box-content article-attachments-box">
                                    <div class="upload-file-field">
                                        <input type="file" name="attachments[]" class="input-file"  />
                                    </div>
                                    <a href="#" id="addNewArticleFile" class="btn btn-primary btn-with-icon btn-add">Add Files</a>

                                    <script>
                                        jQuery(document).ready(function($) {
                                            Page.communityArticleManage.init();
                                        });
                                    </script>
                                </div>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-success btn-with-icon btn-confirm">Save</button>
                            <a href="{{ getSiteUrl() }}/communities/{{ $community->slug }}/wiki" class="btn btn-default btn-with-icon btn-cancel">Cancel</a>
                        </div>

                    {!! Form::close() !!}
                </div>

            </div>
        </div>

@stop