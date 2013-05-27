<?php
/*
Template Name Posts: Documents
*/
?>

<?php
get_header();
?>

<div class="space25"></div>
<div class="content container">
	
	
<?php if (have_posts()) while (have_posts()) : the_post();
if ((get_the_title()) == 'Create a Doc'){
	
	
	}
	else{
	?>
<div id="issuer_title_block">
<div class="column four_fifths left">
	<div id="item-header-avatar">	
		<a href="http://nego-solutions.com/dev-clients/compliance/groups/standard-business-reporting-153144141/" title="Standard Business Reporting">
		<?php
		if ( bp_is_active( 'groups' ) ) {
		$doc_group_ids = bp_docs_get_associated_group_id( get_the_ID(), false, true );
		$doc_groups = array();
		foreach( $doc_group_ids as $dgid ) {
			$maybe_group = groups_get_group( 'group_id=' . $dgid );
			if ( !empty( $maybe_group->name ) ) {
				$doc_groups[] = $maybe_group;
			}
		}

		// First set up the Group snapshot, if there is one
		if ( ! empty( $doc_groups ) ) {
			$group_link = bp_get_group_permalink( $doc_groups[0] );

			$html .= '<span>' . __('Group: ', 'bp-docs') . '</span>';

			$html .= sprintf( __( ' %s', 'bp-docs' ), ' <a href="' . $group_link . '">' . esc_html( $doc_groups[0]->name ) . '</a>' );
			
			$gr_id .= sprintf( __( ' %d', 'bp-docs' ),  esc_html( $doc_groups[0]->id )  );
		}

		// we'll need a list of comma-separated group names
		$group_names = implode( ', ', wp_list_pluck( $doc_groups, 'name' ) );
	
		$avatar_options = array ( 
		'item_id' => (int) $gr_id, 
		'object' => 'group', 
		'type' => 'full', 
		'avatar_dir' => 'group-avatars', 
		'alt' => 'Group avatar', 
		'css_id' => 1234, 
		'class' => 'avatar', 
		'width' => 50, 
		'height' => 50, 
		'html' => false 
		);
		$result = bp_core_fetch_avatar($avatar_options);
		?>
		<img src="<?php echo $result; ?>" class="avatar group-4-avatar avatar- photo" width="150" height="150" alt="Group logo of Standard Business Reporting" title="Standard Business Reporting">
		</a>
	</div><!-- #item-header-avatar -->
	<h3 class="dark_gray_txt"><?php the_title(); ?></h3>
	<p>
<?php
	echo $html;
	$settings = bp_docs_get_doc_settings();
	foreach ( $settings as $l => $v ) {
	if ( 'anyone' == $v || $public_settings[ $l ] == $v ) {

		$anyone_count++;

	} else if ( in_array( $v, array( 'admins-mods', 'creator', 'no-one', 'friends', 'group-members' ) ) ) {

		if ( 'group-members' == $v ) {
			if ( ! isset( $group_status ) ) {
				$group_status = 'foo'; // todo
			}

			if ( 'public' != $group_status ) {
				$private_count++;
			}
		} else {
			$private_count++;
		}
		}
	}

	$settings_count = count( $settings );
	if ( $settings_count == $private_count ) {
		$summary       = 'private';
		$summary_label = __( 'Private', 'bp-docs' );
	} else if ( $settings_count == $anyone_count ) {
		$summary       = 'public';
		$summary_label = __( 'Public', 'bp-docs' );
	} else {
		$summary       = 'limited';
		$summary_label = __( 'Limited', 'bp-docs' );
	}

	$html2 .=   sprintf( __( 'Access: <strong>%s</strong>', 'bp-docs' ), $summary_label );
	
	echo '<br />'.$html2;
		}
					?>
	</p>
	
</div>

										
			<div class="clear"></div>
			</div> <!--end issuer_title_block-->
			<?php } ?>
				
				<div class="content_inner">
					<?php if (has_post_thumbnail()) {
						echo '<a href="'.get_permalink().'">';
						the_post_thumbnail('post-thumb', array('class' => 'page_thumb'));
						echo '</a>';
					}
					the_content(); ?>
				</div>
			<?php endwhile; ?>
		
		
		<div class="clear"></div>

	</div> <!--end content container-->
	
</div>
<div class="space45"></div>
<div class="clear"></div>
</div>
<div class="clear"></div>
<?php
get_footer();
?>
