<?php
    $communityLicense = @$community->getAllMeta()['license_agreements'];
?>
<div id="downloads-container" class="tab-content white_bcg padding10">
    <div id="uploaded-files">
        <div class="grid-list">
            <div class="grid-list-row grid-list-header">
                <div class="grid-list-cell width40P">File Name</div>
                <div class="grid-list-cell width15P tocenter">Size</div>
                <div class="grid-list-cell grid-list-cell-line2 tocenter width15P">License<br/>Agreement</div>
                <div class="grid-list-cell width20P tocenter">Last Updated</div>
                <div class="clear"></div>
            </div>

            @foreach($community->downloads as $file)

                <div class="grid-list-row" id="fileRow{!! $file->id !!}">
                    <div class="grid-list-cell width40P">

                        @if($file->license || $communityLicense)

                            <a href="/downloads/{{ $community->slug }}/agreement/{{ $file->id }}/"
                               class="download-link" rel="has-license">{{ $file->title }}</a>

                        @else

                            <a href="/downloads/{{ $community->slug }}/getfile/{{ $file->id }}"
                               class="download-link">{{ $file->name }}</a>

                        @endif

                        <br/>

                        @if($file->version)
                            Version:
                            <b>{{ $file->version }}</b>
                            @if($file->version_description) ({{ $file->version_description }}) @endif
                            <br/>

                        @endif

                        {!! $file->description !!}

                    </div>

                    <div class="grid-list-cell width15P tocenter">
                        {{ formatBytes($file->size) }}
                    </div>

                    <div class="grid-list-cell grid-list-cell-line2 tocenter width15P">

                        @if($file->license || $communityLicense)

                            <a href="/downloads/{{ $community->slug }}/agreement/{{ $file->id }}/"
                               class="license-link has-license download-link" rel="has-license">License<br/>Agreement<span
                                        class="simple_tooltip"><span></span>To download this file<br/>you have to read &<br/>agree Licence Agreement</span></a>
                        @else
                            <a href="/downloads/{{ $community->slug }}/getfile/{{ $file->id }}"
                               class="license-link download-link">License<br/>Agreement</a>

                        @endif

                    </div>
                    <div class="grid-list-cell width20P tocenter">{!! formatDate($file->updated_at) !!}</div>

                    <div class="grid-list-cell width10P">

                        @if($community->isAdmin())
                            <a href="/downloads/{{ $community->slug }}/edit/{{ $file->id }}/"
                               class="action-btn blue-edit-btn icon-btn edit-file-link">
                                <span class="p"></span><span class="simple_tooltip"><span></span>Edit</span>
                            </a>

                            <a href="/downloads/{{ $community->slug }}/confirmdelete/{{ $file->id }}"
                               class="action-btn delete-btn icon-btn delete-file-link">
                                <span class="p"></span><span class="simple_tooltip"><span></span>Delete</span>
                            </a>

                        @endif
                    </div>
                    <div class="clear"></div>
                </div>

            @endforeach
            @if(! count($community->downloads))
                <div class="grid-list-row">
                    <div class="grid-list-cell tocenter width100P">
                        No file uploaded yet
                    </div>
                    <div class="clear"></div>
                </div>
            @endif

            @if($community->isAdmin())

                <div class="grid-list-footer grid-list-row">
                    <div class="grid-list-cell width100P">
                        <a href="#" id="add-new-download" class="large-plus-link">Upload New File(s)</a>
                    </div>
                    <div class="clear"></div>
                </div>

            @endif

        </div>

    </div>

    @if($community->isAdmin())
        <div id="new-downloads">

        </div>
    @endif
</div>

<script>
    jQuery(document).ready(function(){
        jQuery('#add-new-download').on('click', function(){
            jQuery("#new-downloads").load("/downloads/{{ $community->slug }}/create");
        });
        jQuery('.edit-file-link').on('click', function(e){
            e.preventDefault();
            if(jQuery('.grid-file-edit-row')){
                jQuery('.grid-file-edit-row').remove();
            }
            var link = jQuery(this).attr('href');
            jQuery(this).closest('div.grid-list-row').after('<div class="grid-list-row grid-file-edit-row"></div>');
            jQuery('.grid-file-edit-row').load(link);
            return false;
        });
    });
</script>
