@extends('app')

@section('content')

    <div class="container main-container">
            <div class="main-content">

                <div class="page-title">
                    <h1>Edit Article</h1>
                    <div class="page-title-actions">
                        <a href="/articles/{{ $community->slug }}/create" class="btn btn-success btn-with-icon btn-add">Add new article</a>
                    </div>
                </div>

                <div class="community-tabs article-create">

                     @include('pages.communities.partials.errors')

                    {{ Form::model($article, ['name' => 'article-create-form', 'file' => true, 'method' => 'PATCH', 'enctype' => 'multipart/form-data', 'url' => '/articles/' . $community->slug .'/' . $article->slug, 'data-validate' => 'validate']) }}


                        <div class="colored-box">
                            <div class="colored-box-header">Information</div>
                            <div class="colored-box-body">
                                <div class="colored-box-content">
                                    <div class="form-group">
                                         {!! Form::label('title', 'Title:') !!}
                                         {!! Form::text('title', null, ['class' => 'form-control', 'data-msg-required' => 'Title is required']) !!}
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
                                    <a href="#" id="addNewArticleFile" class="btn btn-primary btn-with-icon btn-add">Add Files</a>

                                    @if(count($article->attachments))
                                        <ul class="attached-file-list">

                                            @foreach($article->attachments as $attachment)
                                                <li>
                                                    <a href="{{ $attachment->getUrl() }}" class="doc-attachment-mime-icon doc-attachment-mime-zip" target="_blank">{{ $attachment->filename }}</a>
                                                    <a href="/articles/{{ $community->slug }}/{{ $article->slug }}/{{ $attachment->id }}" class="btn btn-danger btn-with-icon btn-delete removeFileLink" data-target="#modalDeleteAttachment-{{ $attachment->id }}">Remove</a>
                                                </li>

                                            @endforeach
                                        </ul>
                                    @endif

                                    <script>
                                        jQuery(document).ready(function($) {
                                            Page.communityArticleManage.init();
                                        });
                                    </script>
                                </div>
                            </div>
                        </div>

                    <div class="colored-box">
                            <div class="colored-box-header">Access</div>
                            <div class="colored-box-body">
                                <div class="colored-box-content form-horizontal">
                                    <div class="form-group">
                                        {!! Form::label('visibility', 'Who can read this article?', ['class' => 'col-sm-4 control-label']) !!}
                                        <div class="col-sm-8">
                                           {!! Form::select('visibility',
                                               [
                                                   'members' => 'Community Members',
                                                   'admins' => 'Community Admins',
                                                   'creator' => 'The Article author only'
                                               ],
                                               null,
                                               [
                                                   'class' => 'form-control',
                                                   'id' => 'articleReadAccess',
                                           ]) !!}
                                        </div>
                                    </div>
                                    </div>
                                </div>
                        </div>
                        <div class="form-actions clearfix">
                            <a href="#" class="btn btn-danger btn-with-icon btn-delete pull-right">Delete</a>
                            <div class="pull-left">
                                <button type="submit" class="btn btn-success btn-with-icon btn-confirm">Save</button>
                                <button type="button" class="btn btn-default btn-with-icon btn-cancel">Cancel</button>
                            </div>
                        </div>

                    </form>
                </div>

            </div>
        </div>

@stop