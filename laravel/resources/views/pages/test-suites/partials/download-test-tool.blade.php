@if($installer && Auth::check() && (!Auth::user()->suiteSubscriptions->isEmpty() || $isSupport) )
    @if(!empty($installer->license))
        <!--todo-migration onclick popup with license agreement and confirm checkbox(download link will be accessible only if user checked checkbox)-->
        <li>Test Tool: <a href="{{ $installer->getS3Link() }}" @if(!empty($installer->description)) data-tooltip="tooltip" title="{{ $installer->description }}" @endif>{{ $installer->title }}</a></li>
    @else
        <li>Test Tool: <a href="{{ $installer->getS3Link() }}" @if(!empty($installer->description)) data-tooltip="tooltip" title="{{ $installer->description }}" @endif>{{ $installer->title }}</a></li>
    @endif
@endif