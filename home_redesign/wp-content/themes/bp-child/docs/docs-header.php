<?php /* Subnavigation on user pages is handled by BP's core functions */ ?>
<div class="page-title-block">
    <div class="column four_fifths left">        
        <?php if ( bp_docs_is_existing_doc() ) : ?>           
	        <?php
                cp_wiki_header();
            ?>
        <?php elseif ( bp_docs_is_doc_create() ) : ?>

	        <h2><?php _e( 'Create New Article', 'bp-docs' ); ?></h2>

        <?php endif ?>
    </div>
    <div class="fifth right">
        <div id="item-buttons" class="page-title-buttons">
        <?php 
            $doc_id = is_single() ? get_the_ID() : 0;
            $group_id = bp_docs_get_associated_group_id( $doc_id ); 
            $group = groups_get_group( 'group_id=' . $group_id );
        ?>
        <?php if (bp_docs_current_user_can('create') && groups_is_user_admin(get_current_user_id(), $group_id)): ?>
            <?php //bp_docs_create_button() ?>
            <a class="button button_red button_medium white_txt" id="cp-create-doc-button" href="<?php echo bp_docs_get_create_link(); ?>?group=<?php echo $group->slug; ?>"><?php echo __( "Create New Doc", 'bp-docs' ) ?></a>
        <?php endif ?>
        <div class="clear"></div>
        <div class="space15"></div>
        
        </div><!-- #item-buttons -->
        
    </div>
    <div class="clear"></div>
</div>
