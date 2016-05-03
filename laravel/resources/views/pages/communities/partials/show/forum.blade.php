@if(!empty($thread))
    @include('pages.communities.partials.show.forum.thread')
@else
    @include('pages.communities.partials.show.forum.threads')
@endif