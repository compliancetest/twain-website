<?php

/**
 * Single Forum Content Part
 *
 * @package bbPress
 * @subpackage Theme
 */

?>

<div id="bbpress-forums" class="tab-content white_bcg padding10 forum-homepage">

<?php if(bbp_is_search()){ ?>
    <?php bbp_set_query_name( 'bbp_search' ); ?>

    <?php do_action( 'bbp_template_before_search' ); ?>

    <?php if ( bbp_has_search_results() ) : ?>

         <?php bbp_get_template_part( 'pagination', 'search' ); ?>

         <?php bbp_get_template_part( 'loop',       'search' ); ?>

         <?php bbp_get_template_part( 'pagination', 'search' ); ?>

    <?php elseif ( bbp_get_search_terms() ) : ?>

         <?php bbp_get_template_part( 'feedback',   'no-search' ); ?>

    <?php else : ?>

        <?php bbp_get_template_part( 'form', 'search' ); ?>

    <?php endif; ?>

    <?php do_action( 'bbp_template_after_search_results' ); ?>
<?php }else{ ?>
    <?php bbp_breadcrumb(); ?>
    
	<div class="bbp-search-form">

        <?php bbp_get_template_part( 'form', 'group-search' ); ?>

    </div>

	<?php do_action( 'bbp_template_before_single_forum' ); ?>

	<?php if ( post_password_required() ) : ?>

		<?php bbp_get_template_part( 'form', 'protected' ); ?>

	<?php else : ?>

		<?php bbp_single_forum_description(); ?>
        <div class="clear"></div>
        <?php if(bp_group_is_member()): ?>
        <p>
            <input type="checkbox" name="forum_subscription" id="ct_forum_subscription" value="<?php echo bbp_get_forum_id(); ?>" <?=ct_is_forum_subscriber(bbp_get_forum_id())?('checked="checked"'):('');?>>
            <label for="ct_forum_subscription">Please notify me of all new posts and replies via email</label>
        </p>
        <?php endif; ?>
		<?php if ( bbp_has_forums() ) : ?>

			<?php bbp_get_template_part( 'loop', 'forums' ); ?>

		<?php endif; ?>

		<?php if ( !bbp_is_forum_category() && bbp_has_topics() ) : ?>

			<?php bbp_get_template_part( 'pagination', 'topics'    ); ?>

			<?php bbp_get_template_part( 'loop',       'topics'    ); ?>

			<?php bbp_get_template_part( 'pagination', 'topics'    ); ?>

			<?php bbp_get_template_part( 'form',       'topic'     ); ?>

		<?php elseif ( !bbp_is_forum_category() ) : ?>

			<?php bbp_get_template_part( 'feedback',   'no-topics' ); ?>

			<?php bbp_get_template_part( 'form',       'topic'     ); ?>

		<?php endif; ?>

	<?php endif; ?>

	<?php do_action( 'bbp_template_after_single_forum' ); ?>
<?php } ?>
</div>
