<div id="buddypress">

<div class="tab-content white_bcg padding20-10 nopaddingtop">

    <div class="docs-info-header" style="display: none">
	    <?php bp_docs_info_header() ?>
    </div>

    <?php bp_docs_inline_toggle_js() ?>

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
                    <a href="<?php echo bp_core_get_user_domain( get_the_author_meta( 'ID' ) ) ?>" title="<?php echo bp_core_get_user_displayname( get_the_author_meta( 'ID' ) ) ?>"><?php echo bp_core_get_user_displayname( get_the_author_meta( 'ID' ) ) ?></a>
                </div>
                <div class="grid-list-cell created-date-cell<?php bp_docs_is_current_orderby_class( 'created' ) ?>">
                    <?php echo get_the_date() ?>
                </div>
                <div class="grid-list-cell edited-date-cell<?php bp_docs_is_current_orderby_class( 'modified' ) ?>">
                    <?php echo get_the_modified_date() ?>
                </div>
                <div class="grid-list-cell action-cell">
                    <div class="quick_actions radius3">
                        <ul>
                            <li>
                                <a href="<?php echo bp_docs_get_doc_link() ?>">
                                    <img src="<?php echo get_bloginfo('stylesheet_directory') ?>/images/view_doc.png" class="top3" />
                                    <span class="simple_tooltip radius6">Read<span></span></span>
                                </a>
                            </li>
                            <?php if ( bp_docs_current_user_can( 'edit', get_the_ID() ) ) { ?>
                            <li>
                                <a href="<?php echo bp_docs_get_doc_link() . BP_DOCS_EDIT_SLUG?>">
                                    <img src="<?php echo get_bloginfo('stylesheet_directory') ?>/images/edit_doc.png" class="top1"/>
                                    <span class="simple_tooltip radius6">Edit<span></span></span>
                                </a>
                            </li>
                            <?php } ?>
                            <?php
                            if ( bp_docs_current_user_can( 'view_history', get_the_ID() ) && defined( 'WP_POST_REVISIONS' ) && WP_POST_REVISIONS ) {
                            ?>                      
                            <li>
                                <a href="<?php echo bp_docs_get_doc_link() . BP_DOCS_HISTORY_SLUG ?>">
                                    <img src="<?php echo get_bloginfo('stylesheet_directory') ?>/images/history_doc.png" class="top1"/>
                                    <span class="simple_tooltip radius6">Hystory<span></span></span>
                                </a>
                            </li>          
                            <?php
                            }
                            ?>
                        </ul>
                        <div class="clear"></div>
                    </div>
                </div>
                
                <div class="clear"></div>
            </div>
             <?php endwhile ?>
             <div class="grid-list-footer grid-list-row">                    
                <div class="grid-list-cell width100P">
                    <div id="bp-docs-pagination" class="width80P left">
                        <div id="bp-docs-pagination-count">
                            <?php printf( __( 'Viewing %1$s-%2$s of %3$s docs', 'bp-docs' ), bp_docs_get_current_docs_start(), bp_docs_get_current_docs_end(), bp_docs_get_total_docs_num() ) ?>
                        </div>

                        <div id="bp-docs-paginate-links">
                            <?php bp_docs_paginate_links() ?>
                        </div>
                    </div>
                    <div class="right width15P">
                        <?php if ( bp_docs_current_user_can( 'create' ) ) : ?>
                        <a href="<?php echo bp_docs_get_create_link()?>" class="action-btn add-new-btn"><span class="p"></span><span class="t">Create New Wiki</span></a>
                        <?php endif; ?>
                    </div>
                    <div class="clear"></div>
                </div>
                <div class="clear"></div>
            </div>        
        </div>	  
        

    <?php else: ?>

            <?php if ( bp_docs_current_user_can( 'create' ) ) : ?>
                    <p class="no-docs"><?php printf( __( 'There are no docs for this view. Why not <a href="%s">create one</a>?', 'bp-docs' ), bp_docs_get_create_link() ) ?>
	    <?php else : ?>
		    <p class="no-docs"><?php _e( 'There are no docs for this view.', 'bp-docs' ) ?></p>
            <?php endif ?>

    <?php endif ?>
    
    </div>
</div><!-- /#buddypress -->
