<?php

/**
 * Search 
 *
 * @package bbPress
 * @subpackage Theme
 */

?>

<form role="search" method="get" id="bbp-search-form" action="<?php bbp_search_url(); ?>">
	<div>
        <?php $forum_id = bbp_get_forum_id(); ?>
        <?php $community_id = bp_get_group_id(); ?>
        <?php if( $forum_id ): ?>
            <input class="button" type="hidden" name="bbp_search_forum_id" value="<?php echo $forum_id; ?>" />
        <?php endif; ?>
        <?php if( $community_id ): ?>
            <input class="button" type="hidden" name="bbp_search_community_id" value="<?php echo $community_id; ?>" />
        <?php endif; ?>
		<label class="screen-reader-text" for="bbp_search"><?php _e( 'Search for:', 'bbpress' ); ?>
		<input tabindex="<?php bbp_tab_index(); ?>" type="text" value="<?php echo esc_attr( bbp_get_search_terms() ); ?>" name="bbp_search" id="bbp_search" />
        </label>
        <button tabindex="<?php bbp_tab_index(); ?>" class="action-btn file-btn inline-btn" type="submit" id="bbp_search_submit">
            <span class="p"></span><span class="t"><?php esc_attr_e( 'Search', 'bbpress' ); ?></span>
        </button>
	</div>
</form>
