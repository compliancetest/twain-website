<?php
/**
* Customize Buddy Press
*/

//Add Terms and License Metabox on the Group Admin page
add_action('bp_groups_admin_meta_boxes', 'add_terms_and_license_metabox_to_group');
function add_terms_and_license_metabox_to_group()
{
    add_meta_box( 'bp_group_terms_and_license', _x( 'Terms and Conditions, License Agreement', 'group admin edit screen', 'buddypress' ), 'bp_groups_admin_edit_metabox_terms_and_license', get_current_screen()->id, 'normal', 'core' );    
}
function bp_groups_admin_edit_metabox_terms_and_license($item)
{
    $terms = groups_get_groupmeta($item->id, 'terms_and_conditions');
    $license = groups_get_groupmeta($item->id, 'license_agreements');
    ?>
    <h4>Terms And Conditions</h4>
    <p><textarea cols="30" rows="5" name="terms_and_conditions" style="width: 100%;" id="terms_and_conditions"><?php echo $terms?></textarea></p>
    <br />
    <h4>License Agreements</h4>
    <p><textarea cols="30" rows="5" name="license_agreements" style="width: 100%;" id="license_agreements"><?php echo $license?></textarea></p>
    
    <?php
}

//Save Terms and License Agreements
add_action('bp_group_admin_edit_after', 'save_group_terms_and_license');
function save_group_terms_and_license($group_id)
{
    if(isset($_POST['terms_and_conditions']))
    {
        groups_update_groupmeta($group_id, 'terms_and_conditions', $_POST['terms_and_conditions']);
    }
    if(isset($_POST['license_agreements']))
    {
        groups_update_groupmeta($group_id, 'license_agreements', $_POST['license_agreements']);
    }
    
}

//Customize Join Group Button
add_filter('bp_get_group_join_button', 'cp_bp_get_group_join_button_filter');

function cp_bp_get_group_join_button_filter($button)
{    
    if(is_user_logged_in())
    {
        if($button['id'] == 'request_membership'){        
            $button['link_class'] .= " popup button button_medium button_red white_txt radius6";    
            $button['link_text'] = "Join Community";
            $button['link_title'] = "Join Community";
        }else{
            $button['link_class'] .= " popup button button_medium button_red white_txt radius6";    
            $button['link_text'] = "Request Sent";
            $button['link_title'] = "Request Sent";
        }
    }else{
        $button['link_class'] .= " popup button button_medium button_red white_txt register radius6";    
        $button['link_text'] = "Join Community";
        $button['link_title'] = "Join Community";
    }
    
    return $button;
}

