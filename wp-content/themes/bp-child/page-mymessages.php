<?php
  /**
  * Template Name: My Messages
  */
if(!is_user_logged_in()){
    wp_redirect(home_url());
    exit;
}  
  get_header();
  
  
?>
<div class="content" id="my_messages">
    <div class="column fifth left nopaddingleft nopaddingright sidebar">
        <?php get_sidebar('dashboard'); ?>
    </div>        
    <div class="four_fifths right container">
        <div id="my_messages_list" class="column">
            <ul class="messages-sub-nav">
                <li <?php echo bp_is_messages_inbox() ? 'class="current"' : '' ?>><a href="/my-messages">Inbox</a></li>
                <li <?php echo bp_is_messages_sentbox() ? 'class="current"' : '' ?>><a href="/my-messages/sentbox">Sent</a></li>
                <li <?php echo bp_is_messages_compose_screen() ? 'class="current"' : '' ?>><a href="/my-messages/compose">Compose</a></li>
            </ul>
            <div class="clear"></div>
            <?php if( (bp_is_messages_inbox() || bp_is_messages_sentbox()) && bp_has_message_threads(bp_ajax_querystring( 'messages' ))){ ?>
            <div class="messages-navigations">
                
                <div class="message-search"><?php bp_message_search_form(); ?></div>
                    
                <div class="messages-options-nav">
                    
                    <label class="left"><?php _e( 'Select:', 'buddypress' ) ?>&nbsp;</label>
                    
                    <select name="message-type-select" id="message-type-select" class="left select" autocomplete="off">
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
            <?php } ?>
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
    </div>
    <div class="clear"></div>
</div>

<?php
    get_footer();
