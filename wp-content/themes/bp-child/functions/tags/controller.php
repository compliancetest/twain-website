<?php
require_once(THE_FUNCTION . '/tags/Tag.php');

add_action('init', 'cp_process_tags_actions');

function cp_process_tags_actions()
{
    $action = isset($_REQUEST['td-action']) ? $_REQUEST['td-action'] : null;
    if ($action) {
        if (wp_verify_nonce($action, 'edit-tags')) {
            editProfileTags();
        } else if (wp_verify_nonce($action, 'save-tags')) {
            saveProfileTags();
        }
    }
}

function saveProfileTags(){
    $data = $_POST['tags-data'];
    $profile_id = intval( $_POST['profile_id'] );
    Tag::clearItemTags( $profile_id );
    if( $data && is_iterable( $data ) ){
        foreach( $data AS $tag ){
            Tag::assignTag( $tag, $profile_id );
        }
    }
    exit('success');
}
function editProfileTags(){
    global $wpdb;

    $profile_id = intval( $_REQUEST['id'] );

    $tags = Tag::getItemTags( $profile_id );
    if( ! $profile_id):?>
        <div class="popup-box view-profile-type-box" style="display: none; width: 450px;" id="edit_tags">
            <div class="popup-box-header radius6 noradiusbottom">Error</div>
            <div class="popup-box-content grid-box-body">
                <p class="message error">Invalid Request!</p>
            </div>
            <div class="popup-box-footer radius6 noradiustop">
                <a href="#" class="action-btn cancel-btn close-popup-btn"><span class="p"></span><span class="t">Close</span></a>
                <div class="clear"></div>
            </div>
            <a class="close_btn"></a>
        </div>
    <?php else: ?>
        <div class="popup-box view-profile-type-box" style="display: none; width: 450px;" id="edit_tags">
            <div class="popup-box-header radius6 noradiusbottom">Edit tags</div>
            <div class="popup-box-content grid-box-body">
                <table id="tags_table">
                    <tr style="font-weight: bold;">
                        <td style="border-right: 1px solid #b7b7b7; width: 85%;">Value</td>
                        <td>Delete</td>
                    </tr>
                    <?php foreach( $tags AS $tag ):?>
                        <tr>
                            <td style="border-right: 1px solid #b7b7b7; width: 85%;"><?php echo $tag->name;?></td>
                            <td style="text-align: center;"><input type="checkbox" data-id="<?php echo $tag->id;?>" data-name="<?php echo $tag->name;?>"></td>
                        </tr>
                    <?php endforeach;?>
                </table>
                <hr>
                <div class="field-row">
                    <label class="padding5-10-5-0"> Add tag</label>
                    <div class="grid-cell" style="width: 100%;">
                        <input type="text" name="new_tag" id="new_tag" style="width: 85%;" class="">

                        <a href="#" id="assign_tag" class="action-btn add-new-btn has-tooltip" style="margin-right: 0; float: right;">
                            <span class="t">Add</span>
                            <span class="simple_tooltip radius6" style="top: -27px;">Assign tag to item<span></span></span>
                        </a>
                        <div class="validation_error" style="color: red; font-size: smaller; display: none; max-width: 85%;">Tags may only use upper and lower case letters, numbers, underscores, dashes, dots and spaces. They may be a maximum of 30 characters in length.</div>
                        <div class="assigned_error" style="color: red; font-size: smaller; display: none;">This tag already assigned!</div>
                        <div class="clear"></div>
                    </div>
                    <div class="clear"></div>
                </div>

            </div>
            <div class="popup-box-footer radius6 noradiustop">
                <a class="action-btn process-btn save-tags" href="#"><span class="p"></span><span class="t">Confirm</span></a>
                <a href="#" class="action-btn cancel-btn close-popup-btn"><span class="p"></span><span class="t">Close</span></a>
                <div class="clear"></div>
            </div>
            <a class="close_btn"></a>
            <div class="loading loading_tags"></div>
        </div>
        <script type="text/javascript">
            jQuery('document').ready( function($){
                $('#assign_tag').on('click', function(){
                    $('#new_tag').removeClass('input-error');
                    $('.validation_error, .assigned_error').hide();
                    var tag = $('#new_tag').val().trim();
                    if( ! /^[a-zA-Z0-9_. -]+$/g.test( tag ) || tag.length > 30 ){
                        $('#new_tag').addClass('input-error');
                        $('.validation_error').show();
                        return false;
                    }
                    if( ! $("#tags_table").find('td').filter(function() {
                            return $(this).text().toLowerCase().replace(/ /g, '' ) == tag.toLowerCase().replace(/ /g, '' );
                        }).length ) {
                        $("#tags_table").find('tbody')
                            .append($('<tr>')
                                .append($('<td>')
                                    .css('border-right', '1px solid #b7b7b7')
                                    .css('width', '80%')
                                    .text( tag )
                            )
                                .append($('<td>')
                                    .css('text-align', 'center')
                                    .html('<input type="checkbox" data-id="0" data-name="' + tag + '">')
                            )
                        );
                        $('#new_tag').val('')
                    } else{
                        $('.assigned_error').show();
                    }
                });
                $('.save-tags').on('click', function(e){
                    e.preventDefault();
                    var data = new Array();
                    $("#tags_table").find(':checkbox:not(:checked)').each( function(){
                        data.push( $(this).attr('data-name') );
                    });
                    $('.loading_tags').show();
                    $.ajax({
                        url: '/',
                        type: 'post',
                        data: { 'td-action' : '<?php echo wp_create_nonce('save-tags');?>', 'tags-data' : data, 'profile_id' : '<?php echo $profile_id;?>' },
                        success: function(){
                            location.reload();
                        }
                    })
                });
            });
        </script>
    <?php endif;
    exit;
}