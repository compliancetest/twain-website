@extends('app')

@section('content')
    <div id="content">
        <div class="padder">

            {!! Form::model($community, ['id'=> 'create-group-form', 'class' => 'standard-form', 'method' => 'PATCH', 'action' => ['CommunitiesController@update', 'id' => $community->slug]]) !!}

            <div class="page-title-block column">
                <h2 class="nomarginbottom left">
                    Create a Community
                </h2>
                <a class="right top10" href="/communities/"><b>Communities</b></a>

                <div class="clear"></div>
            </div>

            <div class="column">
                <div class="tabs_wrap radius6 light_gray_bcg system-section">

                    @include('pages.communities.edit.partials.tabs')

                    <div class="item-body tab-content white_bcg padding10" id="group-create-body">


                        <h2>ComplianceTest  Articles</h2>

                        <p>ComplianceTest Articles is a powerful tool for collaboration with members of your group. A cross between document editor and articles, ComplianceTest Articles allows you to co-author and co-edit documents with your fellow community members, which you can then sort and tag in a way that helps your community to get work done.</p>

                        <p>
                            <label for="wiki-enabled">
                            <input name="wiki-enabled" id="wiki-enabled" value="{{ @$communityMeta['wiki-status'] }}" @if(@$communityMeta['wiki-status']) checked="checked" @endif type="checkbox"> Enable Articles for this community</label>
                        </p>

                        <div id="group-doc-options" @if(! @$communityMeta['wiki-status']) style="display: none;" @endif>
                            <h3>Options</h3>

                            <table class="group-docs-options">
                                <tbody><tr>
                                    <td class="label">
                                        <label for="create-wiki-roles">Minimum role to associate Docs with this group:</label>
                                    </td>
                                    <td>
                                        <select name="create-wiki-roles">
                                            <option value="admin" @if(@$communityMeta['wiki-roles'] == 'admin') selected="selected" @endif>Community admin</option>
                                            <option value="mod" @if(@$communityMeta['wiki-roles'] == 'mod') selected="selected" @endif>Community moderator</option>
                                        </select>
                                    </td>
                                </tr>

                                </tbody>
                            </table>
                        </div>

                        <div class="submit" id="previous-next">
                            <button type="submit" class="action-btn next-btn" id="group-creation-next" name="save">
                                <span class="p"></span>
                                <span class="t">Next</span>
                            </button>
                            <div class="clear"></div>
                        </div>

                    </div>

                </div>
            </div>

            {!! Form::hidden('redirect', '/communities/edit/' .$community->slug. '/step/group-avatar/') !!}

            {!! Form::close() !!}

            @include('pages.communities.partials.errors')

        </div>
    </div>
    <script>
        jQuery(function ($){
            $('#wiki-enabled').on('change', function(){
                if($('#wiki-enabled').attr("checked")){
                    $('#group-doc-options').show();
                    $('#wiki-enabled').val('1');
                } else {
                    $('#group-doc-options').hide();
                    $('#wiki-enabled').val('0');
                }
            });
        });
    </script>
@stop