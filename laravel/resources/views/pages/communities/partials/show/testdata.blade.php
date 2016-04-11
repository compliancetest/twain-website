<?php
$isAdmin = $community->isAdmin();

//Getting Test Suites
$args = array(
        'post_type' => 'test-suite',
        'posts_per_page' => -1,
        'tax_query' => array('relation' => 'and'),
        'meta_query' => array(
                array(
                        'key' => 'community_id',
                        'value' => $community->id,
                        'compare' => '='
                )
        ),
        'orderby' => 'title',
        'order' => 'ASC'
);
$testsuites = get_posts($args);
$instances = getCommunityProfileInstatnces($community->id);

?>
<div class="community-test-data row">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table">
                <thead>
                <tr>
                    <th class="text-left col-md-6">Profile Name</th>
                    <th class="text-left">Profile Purpose</th>
                    <th>Profile Type</th>
                    <th>Created Date</th>
                    <th>Valid?</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                @foreach( $instances AS $instance )
                <tr>
                    <td>
                        <a href="{!! get_site_url() !!}?td-action={!! wp_create_nonce('view-profile-instance') !!}&id={{ $instance->id }}">{{ $instance->profile_name }}</a>
                        <p>{{ $instance->profile_description }}</p>
                    </td>
                    <td>{{ $instance->purpose }}</td>
                    <td class="text-center"><a href="{!! get_site_url() !!}?td-action={{ wp_create_nonce('view-profile-type') }}&id={{ $instance->type_id }}">{{ $instance->type_name }}</a></td>
                    <td class="text-center">{{ formatDate($instance->created_date) }}</td>
                    <td class="text-center"><span class="item-{{ strtolower($instance->validation_status) }}"></span></td>
                    <td class="text-center">
                        @if($community->isAdmin())
                            <a href="#" class="btn btn-icon btn-primary btn-edit" data-tooltip="tooltip"
                               title="Edit Profile"></a>
                            <a href="#" class="btn btn-icon btn-danger btn-delete" data-tooltip="tooltip"
                               title="Delete Profile"></a>
                        @endif
                        <a href="/?td-action={{  wp_create_nonce('copy-harness-instance') }}&id={{ $instance->id }}" class="btn btn-icon btn-primary btn-copy" data-tooltip="tooltip"
                           title="Copy Profile"></a>
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>