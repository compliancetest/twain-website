<?php
/**
* Doc Tab Nav
*/
?>

<?php 
    $doc_id = is_single() ? get_the_ID() : 0;
    $group_id = bp_docs_get_associated_group_id( $doc_id ); 
?>
<?php if ( bp_docs_is_existing_doc() ) : ?>
<div id="item-nav">
    <div class="item-list-tabs no-ajax" id="object-nav" role="navigation">
        <ul class="tabs no-ajax">
            <li<?php if ( bp_docs_is_doc_read() ) : ?> class="active"<?php endif ?>>
                <a href="<?php bp_docs_doc_link() ?>" <?php if ( bp_docs_is_doc_read() ) : ?> class="selected"<?php endif ?>>
                    <span class="left icon" id="icon_read"></span>
                    <span class="right text"><?php _e( 'Read', 'bp-docs' ) ?></span>
                    <span class="tabactive"></span>
                    <span class="clear"></span>
                </a>
            </li>  
            <?php if ( bp_docs_current_user_can( 'edit' ) && groups_is_user_admin(get_current_user_id(), $group_id) ) : ?>
                <li<?php if ( bp_docs_is_doc_edit() ) : ?> class="active"<?php endif ?>>
                    <a href="<?php bp_docs_doc_edit_link() ?>"<?php if ( bp_docs_is_doc_edit() ) : ?> class="selected"<?php endif ?>>                                    
                        <span class="left icon" id="icon_edit"></span>
                        <span class="right text"><?php _e( 'Edit', 'bp-docs' ) ?></span>
                        <span class="tabactive"></span>
                        <span class="clear"></span>
                    </a>
                </li>
            <?php endif ?>     
            <?php if ( bp_docs_current_user_can( 'edit' ) && groups_is_user_admin(get_current_user_id(), $group_id) ) : ?>
                <li<?php if ( bp_docs_is_doc_history() ) : ?> class="active"<?php endif ?>>
                    <a href="<?php echo bp_docs_get_doc_link() . BP_DOCS_HISTORY_SLUG ?>" <?php if ( bp_docs_is_doc_history() ) : ?> class="selected"<?php endif ?>>
                        <span class="left icon" id="icon_history"></span>
                        <span class="right text"><?php _e( 'History', 'bp-docs' ) ?></span>
                        <span class="tabactive"></span>
                        <span class="clear"></span>                                    
                    </a>
                </li>
            <?php endif;  ?>               
        </ul>
        <div class="clear"></div>
    </div>
</div>
<a href="<?php echo bp_get_group_permalink(groups_get_group(array('group_id' => $group_id))) . 'wiki'; ?>" class="right action-btn back-btn has-tooltip" style="margin-top: -35px;">
    <span class="p"></span>
    <span class="t">Back</span>
    <span class="simple_tooltip radius6">Back to Articles<span></span></span>
</a>
<?php endif; ?>