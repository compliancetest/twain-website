<?php

/**
 * BuddyPress - Users Messages
 *
 * @package BuddyPress
 * @subpackage bp-default
 */

?>
<div class=" tab-content white_bcg column">
<div class="item-list-tabs no-ajax" id="subnav" role="navigation">
	<ul class="sub-nav">
		<?php bp_get_options_nav(); ?>
	</ul>
	<div class="clear"></div>
	<?php if ( bp_is_messages_inbox() || bp_is_messages_sentbox() ) : ?>

		<div class="messages-navigations">
    
            <div class="message-search"><?php bp_message_search_form(); ?></div>
                
            <div class="messages-options-nav">
                
                <label class="left"><?php _e( 'Select:', 'buddypress' ) ?>&nbsp;</label>
                
                <select name="message-type-select" id="message-type-select" class="left select">
                    <option value="">None</option>
                    <option value="read"><?php _e('Read', 'buddypress') ?></option>
                    <option value="unread"><?php _e('Unread', 'buddypress') ?></option>
                    <option value="all"><?php _e('All', 'buddypress') ?></option>
                </select> &nbsp;

                <?php if ( ! bp_is_current_action( 'sentbox' ) && bp_is_current_action( 'notices' ) ) : ?>

                    <a href="#" class="action-btn process-btn left10" id="mark_as_read"><span class="p"></span><span class="t"><?php _e('Mark as Read', 'buddypress') ?></span></a> &nbsp;
                    <a href="#" id="mark_as_unread" class="action-btn process-btn left10"><span class="p"></span><span class="t"><?php _e('Mark as Unread', 'buddypress') ?></span></a> &nbsp;

                <?php endif; ?>

                <a href="#" id="delete_<?php echo bp_current_action(); ?>_messages" class="action-btn delete-btn left10"><span class="p"></span><span class="t"><?php _e( 'Delete Selected', 'buddypress' ); ?></span></a> &nbsp;
                <div class="clear"></div>
            </div><!-- .messages-options-nav -->
            <div class="clear"></div>
        </div>


	<?php endif; ?>

</div><!-- .item-list-tabs -->

<?php

	if ( bp_is_current_action( 'compose' ) ) :
		locate_template( array( 'members/single/messages/compose.php' ), true );

	elseif ( bp_is_current_action( 'view' ) ) :
		locate_template( array( 'members/single/messages/single.php' ), true );

	else :
		do_action( 'bp_before_member_messages_content' ); ?>

	<div class="messages" role="main">

		<?php
			if ( bp_is_current_action( 'notices' ) )
				locate_template( array( 'members/single/messages/notices-loop.php' ), true );
			else
				locate_template( array( 'members/single/messages/messages-loop.php' ), true );
		?>

	</div><!-- .messages -->

	<?php do_action( 'bp_after_member_messages_content' ); ?>

<?php endif; ?>
</div>