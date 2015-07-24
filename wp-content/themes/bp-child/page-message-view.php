<?php
  /**
  * Template Name: My Message Detail
  */
if(!is_user_logged_in()){
    wp_redirect(home_url());
    exit;
}  
get_header();
$thread_id = cp_get_message_id_from_uri();
if(!$thread_id || !bp_thread_has_messages(array('thread_id' => $thread_id))){
    wp_redirect('/my-messages');
    exit;
}

//Check Permission
if(!messages_check_thread_access($thread_id))
{
    wp_redirect('/my-messages');
    exit;
}
messages_mark_thread_read($thread_id);
?>
<div class="content" id="my_messages">
    <div class="column fifth left nopaddingleft nopaddingright sidebar">
        <?php get_sidebar('dashboard'); ?>
    </div>        
    <div class="four_fifths right container">
        <div id="my_messages_list" class="column">
            <ul class="messages-sub-nav">
                <li><a href="/my-messages">Inbox</a></li>
                <li><a href="/my-messages/sentbox">Sent</a></li>
                <li><a href="/my-messages/compose">Compose</a></li>
            </ul>
            <div class="clear"></div>
            <div id="message-thread" role="main">

                <?php do_action( 'bp_before_message_thread_content' ); ?>

                <?php if ( bp_thread_has_messages(array('thread_id' => $thread_id)) ) : ?>
                <?php
                    
                ?>
                    <h3 id="message-subject"><?php bp_the_thread_subject(); ?></h3>

                    <p id="message-recipients">
                        <span class="highlight left">

                            <?php if ( !bp_get_the_thread_recipients() ) : ?>

                                <?php _e( 'You are alone in this conversation.', 'buddypress' ); ?>

                            <?php else : ?>

                                <?php printf( __( 'Conversation between %s and you.', 'buddypress' ), bp_get_the_thread_recipients() ); ?>

                            <?php endif; ?>

                        </span>

                        <a class="action-btn delete-btn confirm left15" href="<?php bp_the_thread_delete_link(); ?>" title="<?php _e( "Delete Message", "buddypress" ); ?>"><span class="p"></span><span class="t"><?php _e( 'Delete', 'buddypress' ); ?></span></a>             
                    </p>
                    <div class="clear"></div>

                    <?php do_action( 'bp_before_message_thread_list' ); ?>

                    <?php while ( bp_thread_messages() ) : bp_thread_the_message(); ?>

                        <div class="message-box <?php bp_the_thread_message_alt_class(); ?>">

                            <div class="message-metadata">

                                <?php do_action( 'bp_before_message_meta' ); ?>

                                <?php bp_the_thread_message_sender_avatar( 'type=thumb&width=30&height=30' ); ?>
                                <strong><a href="<?php bp_the_thread_message_sender_link(); ?>" title="<?php bp_the_thread_message_sender_name(); ?>"><?php bp_the_thread_message_sender_name(); ?></a><br /><span class="activity"><?php bp_the_thread_message_time_since(); ?></span></strong>

                                <?php do_action( 'bp_after_message_meta' ); ?>
                                <div class="clear"></div>
                            </div><!-- .message-metadata -->

                            <?php do_action( 'bp_before_message_content' ); ?>

                            <div class="message-content">

                                <?php bp_the_thread_message_content(); ?>

                            </div><!-- .message-content -->

                            <?php do_action( 'bp_after_message_content' ); ?>

                            <div class="clear"></div>

                        </div><!-- .message-box -->

                    <?php endwhile; ?>

                    <?php do_action( 'bp_after_message_thread_list' ); ?>

                    <?php do_action( 'bp_before_message_thread_reply' ); ?>

                    <form id="send-reply" action="<?php bp_messages_form_action(); ?>" method="post" class="standard-form">

                        <div class="message-box">

                            <div class="message-metadata">

                                <?php do_action( 'bp_before_message_meta' ); ?>

                                <div class="avatar-box">
                                    <?php bp_loggedin_user_avatar( 'type=thumb&height=30&width=30' ); ?>

                                    <strong><?php _e( 'Send a Reply', 'buddypress' ); ?></strong>
                                </div>

                                <?php do_action( 'bp_after_message_meta' ); ?>

                            </div><!-- .message-metadata -->

                            <div class="message-content">

                                <?php do_action( 'bp_before_message_reply_box' ); ?>

                                <textarea name="content" id="message_content" rows="15" cols="40"></textarea>

                                <?php do_action( 'bp_after_message_reply_box' ); ?>

                                <div class="submit">                        
                                    <input type="submit" name="send" value="<?php _e( 'Send Reply', 'buddypress' ); ?>" id="send_reply_button" class="green-btn" />
                                </div>

                                <input type="hidden" id="thread_id" name="thread_id" value="<?php bp_the_thread_id(); ?>" />
                                <input type="hidden" id="messages_order" name="messages_order" value="<?php bp_thread_messages_order(); ?>" />
                                <?php wp_nonce_field( 'messages_send_message', 'send_message_nonce' ); ?>

                            </div><!-- .message-content -->

                        </div><!-- .message-box -->

                    </form><!-- #send-reply -->

                    <?php do_action( 'bp_after_message_thread_reply' ); ?>

                <?php endif; ?>

                <?php do_action( 'bp_after_message_thread_content' ); ?>

            </div>
        </div>
    </div>
    <div class="clear"></div>
</div>

<?php
    get_footer();

