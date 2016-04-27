@extends('app')

@section('content')

    <div class="container main-container">
            <div class="main-content">

                <div class="page-title">
                    <h1>{{ $article->title }}</h1>
                    <div class="page-title-actions">
                        @if($isAdmin)
                            <a href="{{ getSiteUrl() }}/articles/{{ $community->slug }}/{{ $article->slug }}/edit" class="btn btn-success btn-with-icon btn-edit">Edit</a>
                        @endif
                        <a href="{{ getSiteUrl() }}{{ '/communities/' . $community->slug .'/wiki' }}" class="btn btn-default btn-with-icon btn-back">Back</a>
                        {{--<a href="#" class="btn btn-primary btn-icon btn-print">Print</a>--}}
                    </div>
                </div>

                <div class="article-view">
                    <div class="static-content">
                        {!! $article->content !!}
                    </div>

                    @if(count($article->attachments) > 0)
                        <div class="article-attachments-box">
                            <h2>Attachments</h2>
                            <ul class="attached-file-list">
                                @foreach($article->attachments as $attachment)
                                    <li><a href="{{ $attachment->getUrl() }}" class="doc-attachment-mime-icon doc-attachment-mime-zip" target="_blank">{{ $attachment->filename }}</a></li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                </div>

            </div>
        </div>

@stop