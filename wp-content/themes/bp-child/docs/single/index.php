<div id="buddypress">
    <?php if ($wpdb->get_row($wpdb->prepare("SELECT * FROM wp_bp_groups_members WHERE user_id = %d", get_current_user_id())) &&  bp_docs_current_user_can( 'edit', get_the_ID() ) && groups_is_user_admin(get_current_user_id(), bp_get_current_group_id())): ?>
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

                    <?php include ( bp_docs_locate_template( 'single/doc-tab-nav.php' ) ) ?>

                        <div class="tab-content white_bcg padding15-20">
                            <?php if (bp_docs_is_doc_edit_locked() && bp_docs_current_user_can( 'edit' ) ) : ?>
                                <?php bp_docs_inline_toggle_js() ?>
                                <div class="message warning">
                                    <?php printf( __( 'This doc is currently being edited by %1$s. In order to prevent edit conflicts, only one user can edit a doc at a time.', 'bp-docs' ), bp_docs_get_current_doc_locker_name() ) ?><br />

                                    <?php if ( is_super_admin() || bp_group_is_admin() ) : ?>
                                        <?php printf( __( 'Please try again in a few minutes. Or, as an admin, you can <a href="%s">force cancel</a> the edit lock.', 'bp-docs' ), bp_docs_get_force_cancel_edit_lock_link() ) ?>
                                    <?php else : ?>
                                        <?php _e( 'Please try again in a few minutes.', 'bp-docs' ) ?>
                                    <?php endif ?>
                                </div>
                                <div class="space15"></div>
                            <?php endif ?>
                            <div class="redactor_editor">
                                <?php bp_docs_the_content() ?>
                            </div>

                            <?php if ( bp_docs_doc_has_attachments() ) : ?>
                                <div class="doc-attachments">
                                    <h3><?php _e( 'Attachments', 'bp-docs' ) ?></h3>
                                    <?php include ( bp_docs_locate_template( 'single/attachments.php' ) ) ?>
                                </div>
                            <?php endif ?>

                            <div class="doc-meta">
                                <?php do_action( 'bp_docs_single_doc_meta' ) ?>
                            </div>
                            <?php if ( apply_filters( 'bp_docs_allow_comment_section', true ) ) : ?>
                                <?php comments_template( '/docs/single/comments.php' ) ?>
                        </div>
                    <?php endif;?>
                <?php elseif (is_user_logged_in()): ?>
                    <p style="padding: 0 10px;"><?php echo MESSAGE_WARNING_REGISTERED; ?></p>
                <?php else: ?>
                    <p style="padding: 0 10px;"><?php echo MESSAGE_WARNING_ANONYMOUS; ?></p>
                <?php endif; ?>
            </div>
        </div>

</div>

