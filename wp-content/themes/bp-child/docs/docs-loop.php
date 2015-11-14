<div id="buddypress">

<div class="tab-content white_bcg padding20-10 nopaddingtop">

    <div class="docs-info-header" style="display: none">
	    <?php bp_docs_info_header() ?>
    </div>

    <?php bp_docs_inline_toggle_js() ?>
    
    <?php
        global $wpdb;
        $doc_id = is_single() ? get_the_ID() : 0;
        $group_id = bp_docs_get_associated_group_id( $doc_id ); 
        $group = groups_get_group( 'group_id=' . $group_id );
    ?>

    <?php if (bp_group_is_member() || $wpdb->get_row($wpdb->prepare("SELECT * FROM wp_bp_groups_members WHERE user_id = %d", get_current_user_id()))): ?>
    <?php if ( bp_docs_has_docs() ) : ?>
        <div class="grid-list" id='doc-list'>
            <div class="grid-list-row grid-list-header">
                <div class="grid-list-cell attachment-cell">
                    &nbsp;
                </div>
                <div class="grid-list-cell title-cell<?php bp_docs_is_current_orderby_class( 'title' ) ?>">
                    <a href="<?php bp_docs_order_by_link( 'title' ) ?>"><?php _e( 'Title', 'bp-docs' ); ?></a>
                </div>
                <div class="grid-list-cell author-cell<?php bp_docs_is_current_orderby_class( 'author' ) ?>">
                    <a href="<?php bp_docs_order_by_link( 'author' ) ?>"><?php _e( 'Author', 'bp-docs' ); ?></a>
                </div>
                <div class="grid-list-cell created-date-cell<?php bp_docs_is_current_orderby_class( 'created' ) ?>">
                    <a href="<?php bp_docs_order_by_link( 'created' ) ?>"><?php _e( 'Created', 'bp-docs' ); ?></a>
                </div>
                <div class="grid-list-cell edited-date-cell<?php bp_docs_is_current_orderby_class( 'modified' ) ?>">
                    <a href="<?php bp_docs_order_by_link( 'modified' ) ?>"><?php _e( 'Last Edited', 'bp-docs' ); ?></a>
                </div>
                <div class="grid-list-cell action-cell">
                    Action
                </div>
                <div class="clear"></div>
            </div>
            <?php while ( bp_docs_has_docs() ) : bp_docs_the_doc() ?>
            <div class="grid-list-row">
                <div class="grid-list-cell attachment-cell">
                    <?php bp_docs_attachment_icon() ?>
                </div>
                <div class="grid-list-cell title-cell<?php bp_docs_is_current_orderby_class( 'title' ) ?>">
                    <a href="<?php bp_docs_doc_link() ?>"><?php the_title() ?></a>

                    <?php the_excerpt() ?>

                    <!--<div class="row-actions">
                        <?php //bp_docs_doc_action_links() ?>
                    </div>

                    <div class="bp-docs-attachment-drawer" id="bp-docs-attachment-drawer-<?php echo get_the_ID() ?>">
                        <?php //bp_docs_doc_attachment_drawer() ?>
                    </div>-->
                </div>
                <div class="grid-list-cell author-cell<?php bp_docs_is_current_orderby_class( 'author' ) ?>">
                    <a href="<?php echo bp_core_get_user_domain( get_the_author_meta( 'ID' ) ) ?>" title="<?php echo bp_core_get_user_displayname( get_the_author_meta( 'ID' ) ) ?>"><?php echo cp_get_user_display_name( get_the_author_meta( 'ID' ) ) ?></a>
                </div>
                <div class="grid-list-cell created-date-cell<?php bp_docs_is_current_orderby_class( 'created' ) ?>">
                    <?php echo formatDate(get_the_date()); ?>
                </div>
                <div class="grid-list-cell edited-date-cell<?php bp_docs_is_current_orderby_class( 'modified' ) ?>">
                    <?php echo formatDate(get_the_modified_date()); ?>
                </div>
                <div class="grid-list-cell action-cell">
                    
                    <a href="<?php echo bp_docs_get_doc_link() ?>" class="action-btn icon-btn view-btn has-tooltip">
                        <span class="p"></span>
                        <span class="simple_tooltip radius6">Read Article<span></span></span>
                    </a>
                    
                    <?php if ( bp_docs_current_user_can( 'edit', get_the_ID() ) && groups_is_user_admin(get_current_user_id(), bp_get_current_group_id()) ) { ?>
                    
                        <a href="<?php echo bp_docs_get_doc_link() . BP_DOCS_EDIT_SLUG ?>" class="action-btn icon-btn edit-btn left10 has-tooltip">
                            <span class="p"></span>
                            <span class="simple_tooltip radius6">Edit Article<span></span></span>
                        </a>
                    
                    <?php } ?>
                    <?php
                    if ( bp_docs_current_user_can( 'view_history', get_the_ID() ) && defined( 'WP_POST_REVISIONS' ) && WP_POST_REVISIONS ) {
                    ?>                      
                    
                        <a href="<?php echo bp_docs_get_doc_link() . BP_DOCS_HISTORY_SLUG ?>" class="action-btn icon-btn history-btn left10 has-tooltip">
                            <span class="p"></span>
                            <span class="simple_tooltip radius6">History of Article<span></span></span>
                        </a>
                    
                    <?php
                    }
                    ?>
                
                    <div class="clear"></div>
                    
                </div>
                
                <div class="clear"></div>
            </div>
             <?php endwhile ?>
             <div class="grid-list-footer grid-list-row">                    
                <div class="grid-list-cell width100P">
                    <div id="bp-docs-pagination" class="width80P left">
                        <div id="bp-docs-pagination-count">
                            <?php printf( __( 'Viewing %1$s-%2$s of %3$s articles', 'bp-docs' ), bp_docs_get_current_docs_start(), bp_docs_get_current_docs_end(), bp_docs_get_total_docs_num() ) ?>
                        </div>

                        <div id="bp-docs-paginate-links">
                            <?php bp_docs_paginate_links() ?>
                        </div>
                    </div>
                    <div class="right">
                        <?php if ( can_create_community_article( bp_get_current_group_id() ) ) : ?>
                        <a href="<?php echo bp_docs_get_create_link()?>?group=superstream" class="action-btn add-new-btn has-tooltip">
                            <span class="p"></span><span class="t">Add</span>
                            <span class="simple_tooltip radius6">Add Article<span></span></span>
                        </a>
                        <?php endif; ?>
                    </div>
                    <div class="clear"></div>
                </div>
                <div class="clear"></div>
            </div>        
        </div>	  

    <?php else: ?>
            
            <p class="no-docs"><?php _e( 'There are currently no articles available.', 'bp-docs' ) ?></p>
            
            <?php if ( can_create_community_article( bp_get_current_group_id()) ): ?>
            <a href="<?php echo bp_docs_get_create_link(); ?>?group=superstream" class="action-btn add-new-btn has-tooltip">
                <span class="p"></span><span class="t">Add</span>
                <span class="simple_tooltip radius6">Add Article<span></span></span>
            </a>
            <?php endif; ?>

    <?php endif ?>
    <?php elseif (is_user_logged_in()): ?>    
        <p style="padding: 0 10px;"><?php echo MESSAGE_WARNING_REGISTERED; ?></p>
    <?php else: ?>
        <p style="padding: 0 10px;"><?php echo MESSAGE_WARNING_ANONYMOUS; ?></p>
    <?php endif; ?>
        <div class="clear"></div>
    
    </div>
</div><!-- /#buddypress -->
