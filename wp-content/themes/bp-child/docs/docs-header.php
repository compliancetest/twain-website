<?php /* Subnavigation on user pages is handled by BP's core functions */ ?>
<div class="page-title-block">
    <div class="column four_fifths left">        
        <?php if ( bp_docs_is_existing_doc() ) : ?>           
	        <?php
                cp_wiki_header();
            ?>
        <?php elseif ( bp_docs_is_doc_create() ) : ?>

	        <h2><?php _e( 'New Doc', 'bp-docs' ); ?></h2>

        <?php endif ?>
    </div>
    <div class="fifth right">
        <div id="item-buttons" class="page-title-buttons">
        <?php if ( !bp_is_user() ) : ?>
            <?php bp_docs_create_button() ?>
        <?php endif ?>
        <div class="clear"></div>
        <div class="space15"></div>
        
        </div><!-- #item-buttons -->
        
    </div>
    <div class="clear"></div>
</div>
