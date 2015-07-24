<?php

/**
 * Adds MC4WP_Widget widget.
 */
class MC4WP_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'MC4WP_Widget', // Base ID
			__( 'MailChimp Sign-Up Form', 'mc4wp' ), // Name
			array( 'description' => __( 'Displays one of your MailChimp for WordPress sign-up forms', 'mc4wp' ), ) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array   $args     Widget arguments.
	 * @param array   $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		$title = apply_filters( 'widget_title', $instance['title'] );
		$form_id = (isset($instance['form_id'])) ? $instance['form_id'] : get_option('mc4wp_default_form_id', 0);

		if(empty($form_id)) {
			if(current_user_can('manage_options')) {
				$form = 'Please select the sign-up form you\'d like to show here in the <a href="'. admin_url('widgets.php') .'">widget settings</a>.';
			} else {
				$form = '';
			}
		} else {
			$form = mc4wp_get_form($form_id);
		}

		
		

		echo $args['before_widget'];
		if ( ! empty( $title ) )
			echo $args['before_title'] . $title . $args['after_title'];
		echo $form;
		echo $args['after_widget'];
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array   $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		$title = isset($instance['title']) ? $instance['title'] : __('Newsletter', 'mc4wp');
		$form_id = isset($instance['form_id']) ? $instance['form_id'] : 0;

		$forms = get_posts(array(
			'post_type' => 'mc4wp-form',
			'posts_per_page' => -1
		));
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'form_id' ); ?>"><?php _e( 'Form:' ); ?></label>
			<select class="widefat" name="<?php echo $this->get_field_name('form_id'); ?>" id="<?php echo $this->get_field_id('form_id'); ?>">
				<option value="0" disabled <?php selected($form_id, 0); ?>>Select the form to show</option>
				<?php foreach($forms as $f) { ?>
					<option value="<?php echo esc_attr($f->ID); ?>" <?php selected($form_id, $f->ID); ?>><?php echo $f->post_title; ?></option>
				<?php } ?>
			</select>
		</p>

		<?php if(empty($forms)) { ?>
			<p class="help">You don't have any sign-up forms. <a href="<?php echo admin_url('post-new.php?post_type=mc4wp-form'); ?>">Create one now?</a></p>
		<?php } ?>

		<?php
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array   $new_instance Values just sent to be saved.
	 * @param array   $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['form_id'] = absint($new_instance['form_id']);
		return $instance;
	}

} // class MC4WP_Widget
