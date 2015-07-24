<?php do_action( 'bp_before_member_messages_loop' ); ?>
<?php if ( bp_has_message_threads( bp_ajax_querystring( 'messages' ) ) ) : ?>
	<?php do_action( 'bp_before_member_messages_threads'   ); ?>    
	<table id="message-threads" class="messages-notices result-table">
        <thead>
            <tr>
                <th colspan="2">
                    <?php
                        if ( 'sentbox' != bp_current_action() )
                            echo 'From';
                        else 
                            echo 'To';
                    ?>
                </th>
                <th>Title</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
		<?php while ( bp_message_threads() ) : bp_message_thread(); ?>
			<tr id="m-<?php bp_message_thread_id(); ?>" class="<?php bp_message_css_class(); ?><?php if ( bp_message_thread_has_unread() ) : ?> unread<?php else: ?> read<?php endif; ?>">
				<td width="1%" class="thread-count">
					<span class="unread-count"><?php //bp_message_thread_unread_count(); ?></span>
                    <input type="checkbox" name="message_ids[]" value="<?php bp_message_thread_id(); ?>" />
				</td>
                <td width="42%" class="thread-from">
                    <?php bp_message_thread_avatar(); ?>
                    <div class="left left15 top10">
			        <?php if ( 'sentbox' != bp_current_action() ) : ?>
						<?php _e( 'From:', 'buddypress' ); ?> <?php bp_message_thread_from(); ?><br />
						<span class="activity"><?php bp_message_thread_last_post_date(); ?></span>
				    <?php else: ?>					
						<?php _e( 'To:', 'buddypress' ); ?> <?php bp_message_thread_to(); ?><br />
						<span class="activity"><?php bp_message_thread_last_post_date(); ?></span>					
				    <?php endif; ?>
                    </div>
                </td>
				<td width="50%" class="thread-info">
					<a href="/my-messages/view/<?php echo bp_message_thread_id() ?>" title="<?php _e( "View Message", "buddypress" ); ?>"><?php bp_message_thread_subject(); ?></a>
					<p class="thread-excerpt"><?php bp_message_thread_excerpt(); ?></p>
				</td>

				<?php do_action( 'bp_messages_inbox_list_item' ); ?>

				<td class="thread-options">					
					<a class="action-btn delete-btn icon-btn confirm" href="<?php bp_message_thread_delete_link(); ?>" title="<?php _e( "Delete Message", "buddypress" ); ?>"><span class="p"></span></span></a>
				</td>
			</tr>

		<?php endwhile; ?>
        </tbody>
	</table><!-- #message-threads -->
    <div class="pag-count" id="messages-dir-count">
        <?php bp_messages_pagination_count(); ?>
    </div>
    <div class="pagination no-ajax" id="user-pag">

        <div class="pagination-links" id="messages-dir-pag">
            <?php bp_messages_pagination(); ?>
        </div>

    </div><!-- .pagination -->
    <div class="clear"></div>
    <?php do_action( 'bp_after_member_messages_pagination' ); ?>

	<?php do_action( 'bp_after_member_messages_threads' ); ?>

	<?php do_action( 'bp_after_member_messages_options' ); ?>

<?php else: ?>

    <table id="message-threads" class="messages-notices result-table">
        <thead>
            <tr>
                <th colspan="2">
                    <?php
                        if ( 'sentbox' != bp_current_action() )
                            echo 'From';
                        else 
                            echo 'To';
                    ?>
                </th>
                <th>Title</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td colspan="4" class="tocenter">
                    <?php _e( 'Sorry, no messages were found.', 'buddypress' ); ?>
                </td>
            </tr>
        </tbody>
    </table>
    <br />
    <br />
    <br />
    <br />
    <br />
    <br />
    <br />
    <br />
    <br />
<?php endif;?>

<?php do_action( 'bp_after_member_messages_loop' ); ?>
