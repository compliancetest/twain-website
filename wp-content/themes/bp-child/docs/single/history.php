<div id="buddypress">
    <?php if ($wpdb->get_row($wpdb->prepare("SELECT * FROM wp_bp_groups_members WHERE user_id = %d", get_current_user_id()))): ?>
        <?php include( apply_filters( 'bp_docs_header_template', bp_docs_locate_template( 'docs-header.php' ) ) ) ?>
    <?php else:?>
        <script>
            jQuery(document).ready(function(){
                jQuery('#print-static-page-btn').remove();
            });
        </script>
    <?php endif;?>
    <div  class="column">
        <div class="tabs_wrap light_gray_bcg radius6">
            <?php if ($wpdb->get_row($wpdb->prepare("SELECT * FROM wp_bp_groups_members WHERE user_id = %d", get_current_user_id()))): ?>
                <div id="item-nav">
                    <?php include ( bp_docs_locate_template( 'single/doc-tab-nav.php' ) ) ?>
                </div>
                <div class="tab-content white_bcg padding15-20">
                    <?php if ( bp_docs_history_is_latest() ) : ?>

                        <p><?php _e( "Click on a revision date from the list below to view that revision.", 'bp-docs' ) ?></p>

                        <p><?php _e( "Alternatively, you can compare two revisions by selecting them in the 'Old' and 'New' columns, and clicking 'Compare Revisions'.", 'bp-docs' ) ?></p>

                    <?php endif ?>

                    <table class="form-table ie-fixed">
                        <col class="th" />
                        <?php if ( 'diff' == bp_docs_history_action() ) : ?>
                            <tr id="revision">
                                <th scope="row"></th>
                                <th scope="col" class="th-full">
                                    <span class="alignleft"><?php printf( __( 'Older: %s', 'bp-docs' ), bp_docs_history_post_revision_field( 'left', 'post_title' ) ); ?></span>
                                    <span class="alignright"><?php printf( __( 'Newer: %s', 'bp-docs' ), bp_docs_history_post_revision_field( 'right', 'post_title' ) ); ?></span>
                                </th>
                            </tr>
                        <?php elseif ( !bp_docs_history_is_latest() ) : ?>
                            <tr id="revision">
                                <th scope="row"></th>
                                <th scope="col" class="th-full">
                                    <span class="alignleft"><?php printf( __( 'You are currently viewing a revision titled "%1$s", saved on %2$s by %3$s', 'bp-docs' ), bp_docs_history_post_revision_field( false, 'post_title' ), bp_format_time( strtotime( bp_docs_history_post_revision_field( false, 'post_date' ) ) ), bp_core_get_userlink( bp_docs_history_post_revision_field( false, 'post_author' ) ) ); ?></span>
                                </th>
                            </tr>
                        <?php endif ?>

                        <?php foreach ( _wp_post_revision_fields() as $field => $field_title ) : ?>
                            <?php if ( 'diff' == bp_docs_history_action() ) : ?>
                                <tr id="revision-field-<?php echo $field; ?>">
                                    <th scope="row"><?php echo esc_html( $field_title ); ?></th>
                                    <td><div class="pre"><?php echo wp_text_diff( bp_docs_history_post_revision_field( 'left', $field ), bp_docs_history_post_revision_field( 'right', $field ) ) ?></div></td>
                                </tr>
                            <?php elseif ( !bp_docs_history_is_latest() ) : ?>
                                <tr id="revision-field-<?php echo $field; ?>">
                                    <th scope="row"><?php echo esc_html( $field_title ); ?></th>
                                    <td><div class="pre"><?php echo bp_docs_history_post_revision_field( false, $field ) ?></div></td>
                                </tr>

                            <?php endif ?>

                        <?php endforeach ?>

                        <?php if ( 'diff' == bp_docs_history_action() && bp_docs_history_revisions_are_identical() ) : ?>
                            <tr><td colspan="2"><div class="updated"><p><?php _e( 'These revisions are identical.', 'bp-docs' ); ?></p></div></td></tr>
                        <?php endif ?>

                    </table>

                    <br class="clear" />

                    <?php bp_docs_list_post_revisions( get_the_ID() ) ?>

                    </div>
            <?php elseif (is_user_logged_in()): ?>
                <p style="padding: 0 10px;"><?php echo MESSAGE_WARNING_REGISTERED; ?></p>
            <?php else: ?>
                <p style="padding: 0 10px;"><?php echo MESSAGE_WARNING_ANONYMOUS; ?></p>
            <?php endif; ?>
        </div>
    </div>
	

</div><!-- /#buddypress -->
