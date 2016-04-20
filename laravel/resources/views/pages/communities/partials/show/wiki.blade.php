<div class="community-articles row">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table">
                <thead>
                <tr>
                    <th class="text-left">Title</th>
                    <th>Author</th>
                    <th class="text-nowrap">Created</th>
                    <th class="text-nowrap">Last Edited</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                    @if(count($articles))
                        @foreach($articles as $article)
                        <tr>
                            <td>
                                <div class="attachment-cell @if(count($article->attachments)) has-attachment @endif">
                                    <a href="{{ '/articles/' . $community->slug .'/'.$article->slug }}">{{ $article->title }}</a>

                                    @if(strlen($article->content) > 200)
                                        <p>{{ substr(strip_tags($article->content), 0, 200).' [...]' }}</p>
                                    @else
                                        <p>{!!  $article->content !!}</p>
                                    @endif
                                </div>
                            </td>
                            <td class="text-center"><a href="#">{{ \App\User::find($article->creator_id)->getFullName() }}</a></td>
                            <td class="text-center">{{ $article->created_at }}</td>
                            <td class="text-center">{{ $article->updated_at }}</td>
                            <td class="text-center text-nowrap">
                                <a href="{{ '/articles/' . $community->slug .'/'.$article->slug }}" class="btn btn-icon btn-primary btn-view" data-tooltip="tooltip" title="View Article"></a>
                                @if($isAdmin)
                                    <a href="{{ '/articles/' . $community->slug .'/'.$article->slug }}/edit/" class="btn btn-icon btn-primary btn-edit" data-tooltip="tooltip" title="Edit Article"></a>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>


    </div>

    @if($isAdmin)
        <div class="col-md-3">
            <div class="page-title-actions">
                 <a href="{{ getSiteUrl() }}/articles/{{ $community->slug }}/create" class="btn btn-success btn-with-icon btn-add">Add new article</a>
            </div>
        </div>
    @endif
</div>