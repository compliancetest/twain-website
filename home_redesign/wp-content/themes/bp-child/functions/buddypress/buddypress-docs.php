<?php
/**
* Buddypress Docs Customize Functions
*/
function cp_wiki_header( ) {
    $group_id = 0;
    $hasGroup = false;

    if ( ! $group_id ) {
        if ( isset( $_GET['group'] ) ) {
            $group_slug = $_GET['group'];
            $group_id   = BP_Groups_Group::get_id_from_slug( $group_slug );
        } else {
            $doc_id = is_single() ? get_the_ID() : 0;
            $group_id = bp_docs_get_associated_group_id( $doc_id );
        }
    }

    $group_id = intval( $group_id );
    if ( $group_id ) {
        $group = groups_get_group( 'group_id=' . $group_id );

        if ( ! empty( $group->name ) ) {
            $hasGroup = true;
            $group_link = esc_url( bp_get_group_permalink( $group ) );
            $group_name = bp_get_group_name($group);
            $group_avatar = bp_core_fetch_avatar( array(
                'item_id' => $group_id,
                'object' => 'group',
                'type' => 'thumb',
                'width' => '150',
                'height' => '150',
            ) );
            $group_member_count = sprintf( 1 == $group->total_member_count ? __( '%s member', 'bp-docs' ) : __( '%s members', 'bp-docs' ), intval( $group->total_member_count ) );

            switch ( $group->status ) {
                case 'public' :
                    $group_type_string = __( 'Public Group', 'bp-docs' );
                    break;

                case 'private' :
                    $group_type_string = __( 'Private Group', 'bp-docs' );
                    break;

                case 'hidden' :
                    $group_type_string = __( 'Hidden Group', 'bp-docs' );
                    break;

                default :
                    $group_type_string = '';
                    break;
            }
        }

    }
    
    $settings = bp_docs_get_doc_settings();
    $anyone_count  = 0;
    $private_count = 0;
    $public_settings = array(
        'read'          => 'anyone',
        'edit'          => 'loggedin',
        'read_comments' => 'anyone',
        'post_comments' => 'loggedin',
        'view_history'  => 'anyone'
    );

    foreach ( $settings as $l => $v ) {
        if ( 'anyone' == $v || $public_settings[ $l ] == $v ) {

            $anyone_count++;

        } else if ( in_array( $v, array( 'admins-mods', 'creator', 'no-one', 'friends', 'group-members' ) ) ) {

            if ( 'group-members' == $v ) {
                if ( ! isset( $group_status ) ) {
                    $group_status = 'foo'; // todo
                }

                if ( 'public' != $group_status ) {
                    $private_count++;
                }
            } else {
                $private_count++;
            }

        }
    }

    $settings_count = count( $settings );
    if ( $settings_count == $private_count ) {
        $summary       = 'private';
        $summary_label = __( 'Private', 'bp-docs' );
    } else if ( $settings_count == $anyone_count ) {
        $summary       = 'public';
        $summary_label = __( 'Public', 'bp-docs' );
    } else {
        $summary       = 'limited';
        $summary_label = __( 'Limited', 'bp-docs' );
    }
    
    if($hasGroup)
    {
        ?>
        <div class="page-title-avatar">
            <a href="<?php echo $group_link?>"><?php echo $group_avatar?></a>
        </div>
        <div id="item-header-content" class="page-title-content">
            <h3 class="dark_gray_txt"><?php the_title(); ?></h3>            
            <b><?php _e("Group", "bp-docs")?>: </b><a href="<?php echo $group_link?>"><?php echo $group_name ?></a><br />
            <b>Access: </b><?php echo $summary_label?>
        </div><!-- #item-header-content -->
        <?php
    }else{
        ?>
        <?php
    }
}

add_filter('bp_docs_create_button', 'cp_bp_docs_create_button', 10, 1);
function cp_bp_docs_create_button($button)
{
    return '<a class="button button_red button_medium white_txt" id="cp-create-doc-button" href="' . bp_docs_get_create_link() . '">' . __( "Create New Doc", 'bp-docs' ) . '</a>';
}

function cp_bp_docs_attachment_item_markup( $attachment_id, $format = 'full' ) {
    $markup = '';

    $attachment = get_post( $attachment_id );
    $attachment_ext = preg_replace( '/^.+?\.([^.]+)$/', '$1', $attachment->guid );

    $attachment_url = $attachment->guid;
    $attachment_filename = basename( $attachment->guid );

    if ( 'full' === $format ) {
        $attachment_delete_html = '';
        if ( bp_docs_current_user_can( 'edit' ) && ( bp_docs_is_doc_edit() || bp_docs_is_doc_create() ) ) {
            $doc_url = bp_docs_get_doc_link( $attachment->post_parent );
            $attachment_delete_url = wp_nonce_url( $doc_url, 'bp_docs_delete_attachment_' . $attachment_id );
            $attachment_delete_url = add_query_arg( array(
                'delete_attachment' => $attachment_id,
            ), $attachment_delete_url );
            $attachment_delete_html = sprintf(
                '<a href="%s" class="doc-attachment-delete confirm action-btn delete-btn"><span class="p"></span><span class="t">%s</span></a> ',
                $attachment_delete_url,
                __( 'Delete', 'buddypress' )
            );
        }

        $markup = sprintf(
            '<li id="doc-attachment-%d"><span class="doc-attachment-mime-icon doc-attachment-mime-%s"></span><a href="%s" title="%s">%s</a>%s</li>',
            $attachment_id,
            $attachment_ext,
            $attachment_url,
            esc_attr( $attachment_filename ),
            esc_html( $attachment_filename ),
            $attachment_delete_html
        );
    } else {
        $markup = sprintf(
            '<li id="doc-attachment-%d"><a href="%s" title="%s">%s</a></li>',
            $attachment_id,
            $attachment_url,
            esc_attr( $attachment_filename ),
            esc_html( $attachment_filename )
        );
    }

    return $markup;
}

add_filter('bp_docs_get_access_options', 'cp_bp_docs_get_access_options', 20, 4);
function cp_bp_docs_get_access_options($options, $settings_field, $doc_id = 0, $group_id = 0)
{
    $options = BP_Docs_Groups_Integration::get_access_options($options, $settings_field, $doc_id = 0, $group_id = 0);
    //Unset Old Defaults
    foreach($options as $k=>$v)
    {
        if(isset($options[$k]['default']))
            unset($options[$k]['default']);
    }
    $options[20] = array('name' => 'anyone', 'label' => 'Anyone');
    //Set New Values
    foreach($options as $k=>$v)
    {
        if($settings_field == 'read' && $v['name'] == 'loggedin') //Login User For Read
            $options[$k]['default'] = 1;
        if(($settings_field == 'read_comments' || $settings_field == 'post_comments' || $settings_field == 'view_history') && $v['name'] == 'group-members') //Group Members for Comments and History
            $options[$k]['default'] = 1;
        if($settings_field == 'edit' && $v['name'] == 'admins-mods') //Admins and Mods User For Edit
            $options[$k]['default'] = 1;
    }
    
    return $options;
}

//Customize Wiki Tag URL
add_filter('bp_docs_get_tag_link_url', 'cp_bp_docs_get_tag_link_url', 90, 3);
function cp_bp_docs_get_tag_link_url($url, $tag, $type){
    if(bp_is_group())
    {
        if(is_array($tag))
            $tag = $tag[0];
        return add_query_arg( 'bpd_tag', urlencode( $tag ), bp_get_group_permalink() . "wiki" );
    }
    return $url;
}

add_filter('bp_docs_get_tag_link', 'cp_bp_docs_get_tag_link', 90, 4);
function cp_bp_docs_get_tag_link($html, $url, $tag, $type){
    if(bp_is_group())
    {
        if(is_array($tag))
            $tag = $tag[0];
        $html = '<a href="' . add_query_arg( 'bpd_tag', urlencode( $tag ), bp_get_group_permalink() . "wiki" ) . '" title="' . sprintf( __( 'Docs tagged %s', 'bp-docs' ), esc_attr( $tag ) ) . '">' . esc_html( $tag ) . '</a>';
    }
    return $html;
}

add_filter('bp_docs_user_can', 'cp_bp_docs_user_can', 90, 4);
function cp_bp_docs_user_can($user_can, $action, $user_id, $doc_id = false){
    global $bp, $post;

        // If a doc_id is provided, check it against the current post before querying
        if ( $doc_id && isset( $post->ID ) && $doc_id == $post->ID ) {
            $doc = $post;
        }

        if ( empty( $post->ID ) )
            $doc = !empty( $bp->bp_docs->current_post ) ? $bp->bp_docs->current_post : false;

        // Keep on trying to set up a post
        if ( empty( $doc ) )
            $doc = bp_docs_get_current_doc();

        // If we still haven't got a post by now, query based on doc id
        if ( empty( $doc ) )
            $doc = get_post( $doc_id );

        if ( ! empty( $doc ) ) {
            $doc_settings = get_post_meta( $doc->ID, 'bp_docs_settings', true );

            // Manage settings don't always get set on doc creation, so we need a default
            if ( empty( $doc_settings['manage'] ) )
                $doc_settings['manage'] = 'creator';

            // Likewise with view_history
            if ( empty( $doc_settings['view_history'] ) )
                $doc_settings['view_history'] = 'anyone';

            // Likewise with read_comments
            if ( empty( $doc_settings['read_comments'] ) )
                $doc_settings['read_comments'] = 'anyone';
        } else if ( bp_docs_is_doc_create() && 'manage' == $action ) {
            // Anyone can do anything during doc creation
            return true;
        }

        // Default to the current group, but get the associated doc if not
        $group_id = 0;
        if ( ! empty( $doc ) ) {
            $group_id = bp_docs_get_associated_group_id( $doc->ID, $doc );
            $group = groups_get_group( array( 'group_id' => $group_id ) );
        }

        if ( ! $group_id ) {
            return $user_can;
        }

        switch ( $action ) {
            case 'create' :
                $group_settings = groups_get_groupmeta( $group_id, 'bp-docs' );

                // Provide a default value for legacy backpat
                if ( empty( $group_settings['can-create'] ) ) {
                    $group_settings['can-create'] = 'member';
                }

                if ( !empty( $group_settings['can-create'] ) ) {
                    switch ( $group_settings['can-create'] ) {
                        case 'admin' :
                            if ( groups_is_user_admin( $user_id, $group_id ) )
                                $user_can = true;
                            break;
                        case 'mod' :
                            if ( groups_is_user_mod( $user_id, $group_id ) || groups_is_user_admin( $user_id, $group_id ) )
                                $user_can = true;
                            break;
                        case 'member' :
                        default :
                            if ( groups_is_user_member( $user_id, $group_id ) )
                                $user_can = true;
                            break;
                    }
                }

                break;

            case 'read' :
            case 'delete' : // Delete and Edit are the same for the time being
            case 'edit' :
            default :
                // Delete defaults to Edit for now
                if ( 'delete' == $action ) {
                    $action = 'edit';
                }

                // Make sure there's a default
                if ( empty( $doc_settings[$action] ) ) {
                    if ( ! empty( $group_id ) ) {
                        $doc_settings[ $action ] = 'group-members';
                    } else {
                        $doc_settings[ $action ] = 'anyone';
                    }
                }

                switch ( $doc_settings[$action] ) {
                    case 'anyone' :
                        $user_can = true;
                        break;

                    case 'creator' :
                        if ( $doc->post_author == $user_id )
                            $user_can = true;
                        break;

                    case 'group-members' :
                        if ( groups_is_user_member( $user_id, $group_id ) )
                            $user_can = true;
                        break;

                    case 'admins-mods' :
                        if ( groups_is_user_admin( $user_id, $group_id ) || groups_is_user_mod( $user_id, $group_id ) )
                            $user_can = true;
                        break;

                    case 'no-one' :
                    default :
                        break; // In other words, other types return false
                }

                break;
        }

        return $user_can;
}

