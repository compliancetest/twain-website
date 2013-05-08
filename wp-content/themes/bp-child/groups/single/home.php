<?php 
get_header( 'buddypress' ); 
do_action( 'bp_before_group_header' );
?>
<div class="space25"></div>
<div class="content container" id="search">		
	<?php if ( bp_has_groups() ) : while ( bp_groups() ) : bp_the_group(); ?>
	<?php do_action( 'bp_before_group_home_content' ); ?>
	
	<div id="issuer_title_block">
			<div class="column four_fifths left">
				<div id="item-header-avatar">
					<a href="<?php bp_group_permalink(); ?>" title="<?php bp_group_name(); ?>">

						<?php bp_group_avatar(); ?>

					</a>
				</div><!-- #item-header-avatar -->
				<h3 class="dark_gray_txt"><?php bp_group_name(); ?></h3>
				<p class="nomarginbottom"><?php bp_group_description(); ?></p>
			</div>
			<div class="column fifth right">
				<?php
				if (!is_user_logged_in() ){ ?>
					<a class="popup button button_medium red_bcg white_txt radius6 community_popup">Join Comunity</a>
				<?php 
				}
				else{
					 do_action( 'bp_group_header_actions' );
					}
					?>
           
	
			</div>
                        
			<div class="clear"></div>
	</div>
	
	<?php //locate_template( array( 'groups/single/group-header.php' ), true ); ?>


			<div id="item-nav">
				<div class="item-list-tabs no-ajax" id="object-nav" role="navigation">
					<ul>

						<?php //bp_get_options_nav(); ?>

						<?php //do_action( 'bp_group_options_nav' ); ?>

					</ul>
				</div>
			</div><!-- #item-nav -->

			<div id="item-body" style="display:none;">

				<?php //do_action( 'bp_before_group_body' );

				/**
				 * Does this next bit look familiar? If not, go check out WordPress's
				 * /wp-includes/template-loader.php file.
				 *
				 * @todo A real template hierarchy? Gasp!
				 */

				// Group is visible
				if ( bp_group_is_visible() ) : 

					// Looking at home location
					if ( bp_is_group_home() ) :

						// Use custom front if one exists
						$custom_front = locate_template( array( 'groups/single/front.php' ) );
						if     ( ! empty( $custom_front   ) ) : load_template( $custom_front, true );
						
						// Default to activity
						elseif ( bp_is_active( 'activity' ) ) : locate_template( array( 'groups/single/activity.php' ), true );

						// Otherwise show members
						elseif ( bp_is_active( 'members'  ) ) : locate_template( array( 'groups/single/members.php'  ), true );

						endif;

					// Not looking at home
					else :

						// Group Admin
						if     ( bp_is_group_admin_page() ) : locate_template( array( 'groups/single/admin.php'        ), true );

						// Group Activity
						elseif ( bp_is_group_activity()   ) : locate_template( array( 'groups/single/activity.php'     ), true );

						// Group Members
						elseif ( bp_is_group_members()    ) : locate_template( array( 'groups/single/members.php'      ), true );

						// Group Invitations
						elseif ( bp_is_group_invites()    ) : locate_template( array( 'groups/single/send-invites.php' ), true );

						// Old group forums
						elseif ( bp_is_group_forum()      ) : locate_template( array( 'groups/single/forum.php'        ), true );

						// Anything else (plugins mostly)
						else                                : locate_template( array( 'groups/single/plugins.php'      ), true );

						endif;
					endif;

				// Group is not visible
				elseif ( ! bp_group_is_visible() ) :

					// Membership request
					if ( bp_is_group_membership_request() ) :
						locate_template( array( 'groups/single/request-membership' ), true );

					// The group is not visible, show the status message
					else :

						do_action( 'bp_before_group_status_message' ); ?>

						<div id="message" class="info">
							<p><?php // bp_group_status_message(); ?></p>
						</div>

						<?php // do_action( 'bp_after_group_status_message' );

					endif;
				endif;			

				// do_action( 'bp_after_group_body' ); ?>

			</div><!-- #item-body -->

			<?php // do_action( 'bp_after_group_home_content' ); ?>
			
			<?php
			$group_id = bp_get_group_id();
			 endwhile; endif; ?>
			<div id="issuer_content_block">
			<div class="column">
				<div class="tabs_wrap light_gray_bcg radius6">
					<ul class="tabs">
				        <li class="active">
							<a href="javascript: void(0)" rel="tabs1" class="defaulttab selected">
								<span class="left icon" id="icon_test_suites"></span>
								<span class="right text">TEST SUITES</span>
								<div class="tabactive"></div>
								<span class="clear"></span>
							</a>
						</li>
				        <li class="">
							<a href="javascript: void(0)" rel="tabs2" class="selected">
								<span class="left icon" id="icon_wiki"></span>
								<span class="right text">WIKI</span>
								<div class="tabactive"></div>
								<span class="clear"></span>
							</a>
						</li>
				        <li class="">
							<a href="javascript: void(0)" rel="tabs3" class="selected">
								<span class="left icon" id="icon_forum"></span>
								<span class="right text">FORUM</span>
								<div class="tabactive"></div>
								<span class="clear"></span>
							</a>
						</li>
				        <li class="">
							<a href="javascript: void(0)" rel="tabs4" class="selected">
								<span class="left icon" id="icon_downloads"></span>
								<span class="right text">DOWNLOADS</span>
								<div class="tabactive"></div>
								<span class="clear"></span>
							</a>
						</li>
				    </ul>
				    <div class="clear"></div>
				    
				    <div class="tab-content white_bcg" id="tabs1" style="display: block; ">
						<div class="column four_fifths left padding20-10">
							<div class="grid dark_gray_txt" id="test_suites_tab_grid">
								<div class="grid_head grid_head_border">
									<div class="padding10 nopaddingtop">
										<div class="grid_cell nopaddingtop width50P">Name</div>
										<div class="grid_cell nopaddingtop width20P">Date</div>
										<div class="grid_cell nopaddingtop width15P">Status</div>
										<div class="grid_cell nopaddingtop width15P"></div>
											<div class="clear"></div>
									</div>
								</div>
								<div class="grid_body noborder">
								<?php
								$testsuites_result = $wpdb->get_row( "SELECT * FROM " . $wpdb->prefix . "bp_groups_testsuites WHERE group_id={$group_id}");
								$testsuites = $testsuites_result->ts_ids;
								$current_test_suites = explode(',', $testsuites);
								$args = array( 'post_type' => 'test-suite', 'posts_per_page' => 10, 'post__in' =>$current_test_suites);
								$loop = new WP_Query( $args );
								while ( $loop->have_posts() ) : $loop->the_post();
									$status = get_post_meta( get_the_ID(),'ts_status',true );
									$date_field=get_post_meta($post->ID, 'ts_issue_date', true); 
									$date_formated = date("M Y", strtotime($date_field)); // 2012
									echo '<div class="grid_row grid_row_border">
											<div class="grid_cell width50P">
												<a href="'.get_permalink().'" class="blue_txt"><h5>'.get_the_title().'</h5></a>
											</div>
											<div class="grid_cell width20P">'.$date_formated.'</div>';	
									
									if($status == 'Active') {
									?>
									<div class="grid_cell width15P"><a class="button green_bcg white_txt button_small radius3">ACTIVE</a></div>
									<?php } else if($status == 'On Hold') { ?>
									<div class="grid_cell width15P"><a class="button orange_bcg white_txt button_small radius3">ON HOLD</a></div>
									<?php }	
									else {
										echo '<div class="grid_cell width15P">undefined</div>';
										}
									//echo '<div class="grid_cell width15P"><a class="button green_bcg white_txt button_small radius3">'.$status.'</a></div>';		
									echo '<div class="grid_cell width15P">
											<div class="quick_actions radius3 alignright">
												<ul>
													<li><a href="#"><img src="'.get_bloginfo('stylesheet_directory').'/images/qa_doc_icon.png"><span class="simple_tooltip radius6">View Documents<span></span></span></a></li>
													<li><a href="#"><img src="'.get_bloginfo('stylesheet_directory').'/images/qa_msg_icon.png"><span class="simple_tooltip radius6">View Messages<span></span></span></a></li>
												</ul>
											</div>
										</div>
										<div class="clear"></div>';
										echo '</div>';
								endwhile;
								?>					
									
								</div>
							</div>
						</div>
						<div class="column fifth right expendables">
							
							<div class="expandable open">
								<h6 class="exp_title open">Issuer</h6>
								<div class="exp_content" style="display: block; ">
									<label for="gov_au" class="blue_txt"><input type="checkbox" name="gov_au" id="gov_au"> Gov-AU (33)</label>
										<div class="clear"></div>
									<label for="gov_uk" class="blue_txt"><input type="checkbox" name="gov_uk" id="gov_uk"> Gov-UK (20)</label>
										<div class="clear"></div>
									<label for="iso" class="blue_txt"><input type="checkbox" name="iso" id="iso"> ISO (34)</label>
										<div class="clear"></div>
									<label for="oasis" class="blue_txt"><input type="checkbox" name="oasis" id="oasis"> OASIS (25)</label>
										<div class="clear"></div>
								</div>
							</div>
							
							<div class="expandable open">
								<h6 class="exp_title open">Year of Issue</h6>
								<div class="exp_content" style="display: block; ">
									<label for="yoi_2013" class="blue_txt"><input type="checkbox" name="yoi_2013" id="yoi_2013"> 2013 (5)</label>
										<div class="clear"></div>
									<label for="yoi_2012" class="blue_txt"><input type="checkbox" name="yoi_2012" id="yoi_2012"> 2012 (152)</label>
										<div class="clear"></div>
									<label for="yoi_2011" class="blue_txt"><input type="checkbox" name="yoi_2011" id="yoi_2011"> 2011 (16)</label>
										<div class="clear"></div>
								</div>
							</div>
							
							<div class="expandable open">
								<h6 class="exp_title open">Status</h6>
								<div class="exp_content" style="display: block; ">
									<label for="status_current" class="blue_txt"><input type="checkbox" name="status_current" id="status_current"> Current (38)</label>
										<div class="clear"></div>
									<label for="status_deprecated" class="blue_txt"><input type="checkbox" name="status_deprecated" id="status_deprecated"> Deprecated (14)</label>
										<div class="clear"></div>
									<label for="status_obsolete" class="blue_txt"><input type="checkbox" name="status_obsolete" id="status_obsolete"> Obsolete (12)</label>
										<div class="clear"></div>
								</div>
							</div>
				
						</div>
						<div class="clear"></div>
				    </div>
				    <div class="tab-content white_bcg" id="tabs2" style="display: block; ">
						<!--wiki -->		
						<?php if ( bp_docs_has_docs() ) : ?>
						<div class="column padding20-10">
							<div class="grid dark_gray_txt" id="wiki_tab_grid">
								<div class="grid_head grid_head_border">
											<div class="padding10 nopaddingtop">
												<div class="grid_cell width45P">
													<?php _e( 'Name', 'bp-docs' ); ?>
												</div>
												<div class="grid_cell nopaddingtop width15P tocenter">
													<?php _e( 'Author', 'bp-docs' ); ?>
												</div>
												<div class="grid_cell nopaddingtop width15P tocenter">
													<?php _e( 'Created', 'bp-docs' ); ?>
												</div>
												<div class="grid_cell nopaddingtop width10P tocenter">
													<?php _e( 'Last Edit', 'bp-docs' ); ?>
												</div>
												<div class="grid_cell nopaddingtop width15P"></div>
												<div class="clear"></div>
											</div>
								</div> <!--end grid_head-->
								<div class="clear"></div>
								<?php while ( bp_docs_has_docs() ) : bp_docs_the_doc() ?>
								<div class="grid_row grid_row_border">
									<div class="grid_cell width45P">
										<a href="<?php bp_docs_doc_link() ?>"><h5><?php the_title() ?></h5></a>
										<?php the_excerpt() ?>
									</div>
									<div class="grid_cell nopaddingtop width15P tocenter">
										<a href="<?php echo bp_core_get_user_domain( get_the_author_meta( 'ID' ) ) ?>" title="<?php echo bp_core_get_user_displayname( get_the_author_meta( 'ID' ) ) ?>">
										<h5><?php echo bp_core_get_user_displayname( get_the_author_meta( 'ID' ) ) ?></h5>
										</a>
									</div>
									<!--end -->
									<div class="grid_cell nopaddingtop width15P tocenter">
										<?php echo get_the_date() ?>
									</div>
									<div class="grid_cell nopaddingtop width10P tocenter">
										<?php echo get_the_modified_date() ?>
									</div> 
									<div class="grid_cell nopaddingtop width15P">
										<div class="quick_actions radius3 alignright">
											<ul>
											<?php bp_docs_doc_action_links() ?>
											</ul>
										</div>
									</div>
									<div class="clear"></div>
								</div>
								
								<?php do_action( 'bp_docs_loop_additional_td' ) ?>
								
								
							<?php endwhile ?>
						
					
							<div id="bp-docs-pagination">
								<div id="bp-docs-pagination-count">
									<?php printf( __( 'Viewing %1$s-%2$s of %3$s docs', 'bp-docs' ), bp_docs_get_current_docs_start(), bp_docs_get_current_docs_end(), bp_docs_get_total_docs_num() ) ?>
								</div>

								<div id="bp-docs-paginate-links">
									<?php bp_docs_paginate_links() ?>
								</div>
							</div>
						<!--end grid-->	
						
						<?php else: ?>

								<?php if ( bp_docs_current_user_can( 'create' ) ) : ?>
										<p class="no-docs"><?php printf( __( 'There are no docs for this view. Why not <a href="%s">create one</a>?', 'bp-docs' ), bp_docs_get_create_link() ) ?>
							<?php else : ?>
								<p class="no-docs"><?php _e( 'There are no docs for this view.', 'bp-docs' ) ?></p>
								<?php endif ?>

						<?php endif ?>
						<!--END Wiki -->
						
						</div> </div> </div>
					<!--end tabs2-->
				    <div class="tab-content white_bcg" id="tabs3" style="display: block; ">
						<div class="column left">
							<?php if(bp_group_is_member()){
								?>
							<!--Forum -->
							<form action="<?php bp_activity_post_form_action(); ?>" method="post" id="whats-new-form" name="whats-new-form" role="complementary">

								<?php do_action( 'bp_before_activity_post_form' ); ?>

								<div id="whats-new-avatar">
									<a href="<?php echo bp_loggedin_user_domain(); ?>">
										<?php bp_loggedin_user_avatar( 'width=' . bp_core_avatar_thumb_width() . '&height=' . bp_core_avatar_thumb_height() ); ?>
									</a>
								</div>
								
								<p class="activity-greeting"><?php if ( bp_is_group() )
									printf( __( "What's new in %s, %s?", 'buddypress' ), bp_get_group_name(), bp_get_user_firstname() );
								else
									printf( __( "What's new, %s?", 'buddypress' ), bp_get_user_firstname() );
								?></p>

								<div id="whats-new-content">
									<div id="whats-new-textarea">
										<textarea name="whats-new" id="whats-new" cols="50" rows="10"><?php if ( isset( $_GET['r'] ) ) : ?>@<?php echo esc_attr( $_GET['r'] ); ?> <?php endif; ?></textarea>
									</div>

									<div id="whats-new-options">
										<div id="whats-new-submit">
											<input type="submit" name="aw-whats-new-submit" id="aw-whats-new-submit" value="<?php _e( 'Post Update', 'buddypress' ); ?>" />
										</div>

										<?php if ( bp_is_active( 'groups' ) && !bp_is_my_profile() && !bp_is_group() ) : ?>

											<div id="whats-new-post-in-box">

												<?php _e( 'Post in', 'buddypress' ); ?>:

												<select id="whats-new-post-in" name="whats-new-post-in">
													<option selected="selected" value="0"><?php _e( 'My Profile', 'buddypress' ); ?></option>

													<?php if ( bp_has_groups( 'user_id=' . bp_loggedin_user_id() . '&type=alphabetical&max=100&per_page=100&populate_extras=0' ) ) :
														while ( bp_groups() ) : bp_the_group(); ?>

															<option value="<?php bp_group_id(); ?>"><?php bp_group_name(); ?></option>

														<?php endwhile;
													endif; ?>

												</select>
											</div>
											<input type="hidden" id="whats-new-post-object" name="whats-new-post-object" value="groups" />

										<?php elseif ( bp_is_group_home() ) : ?>

											<input type="hidden" id="whats-new-post-object" name="whats-new-post-object" value="groups" />
											<input type="hidden" id="whats-new-post-in" name="whats-new-post-in" value="<?php bp_group_id(); ?>" />

										<?php endif; ?>

										<?php do_action( 'bp_activity_post_form_options' ); ?>

									</div><!-- #whats-new-options -->
								</div><!-- #whats-new-content -->

								<?php wp_nonce_field( 'post_update', '_wpnonce_post_update' ); ?>
								<?php do_action( 'bp_after_activity_post_form' ); ?>

							</form><!-- #whats-new-form -->
							
							<!--end Forum -->
							<?php
								}
								else echo 'You must Join Community first! ';
							?>
						</div>
						<div class="clear"></div>
				    </div>
				    <div class="tab-content white_bcg" id="tabs4" style="display: block; ">
						<div class="column padding20-10">
							<?php if(bp_group_is_member()){
								?>
							<div class="grid dark_gray_txt">
								<div class="grid_head grid_head_border">
											<div class="padding10 nopaddingtop">
												<div class="grid_cell width50P">
													Name												
												</div>
												<div class="grid_cell nopaddingtop width15P"></div>
												<div class="clear"></div>
											</div>
								</div>
								<div class="grid_body noborder">
								<?php
									global $wpdb;
									$rows = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "bp_groups_downloads WHERE group_id={$group_id}");
									foreach ($rows as $key => $row){
									echo '
									<div class="grid_row grid_row_border">
										  <div class="grid_cell width50P">';
									echo '<a href="'.$row->url.'" class="blue_txt"><h5>'.$row->name.'</h5></a>';
									echo '</div>
										  <div class="grid_cell width15P">';
									echo '<a data-id="'.$row->id.'" class="remove_file" style="cursor:pointer; margin-left:10px;">Remove File</a>';	  
									echo '</div>
										  <div class="clear">
									</div>
								</div><div class="clear"></div>';
									}
								?>
									<form id="attach_files" name="attach_new_file" id="attach_files_id" action="" method="post" enctype="multipart/form-data">
										<div class="space10"></div>
										<div class="elem-file-to-clone">
											<input type="file" name="attachment_group[]" id="attachment_group_id"> <br />
											<div class="clear"></div>
											<div class="space5"></div>
										</div> 
										<div class="copy-correct-file">	
										</div>
										<a class="add_new_file radius3 left quick_actions">Add New</a>
										<br /> <br />
										 
										 <script type="text/javascript">
											jQuery(document).ready(function() {
												jQuery(".add_new_file").click(function(data) {
													jQuery('.copy-correct-file').append(jQuery('.elem-file-to-clone').html());
													//jQuery('.copy-correct input, .copy-correct select').val('');
												});
												jQuery(".remove_file").live('click', function() {
													var id = jQuery(this).attr('data-id');
													var elem = this;
													jQuery.post(HOMEURL + '?file_id=' + id + '&action=deletefile',
														{}, function(data){
															jQuery(elem).parent().parent().remove();
													});
												}); 
										});
										</script>	
										<div class="space10"></div>
										<p class="error_files">Attach Files First!</p>
										<input type="submit" name="file_attach" value="SAVE" id="save_attachment">
									</form>
									<?php
									if(isset($_POST['file_attach'])) {
										//Process attachments
										$uploads = wp_upload_dir();
										$uploads_dir = $uploads['basedir'].DIRECTORY_SEPARATOR.'groups_attachments';
										$url_dir = $uploads['baseurl'].'/groups_attachments/';
										//echo $uploads_dir;
										if(!file_exists($uploads_dir)){
											mkdir($uploads_dir);
											}
										
										global $wpdb;
										foreach ($_FILES['attachment_group']['name'] as $key => $val) {
											if ($_FILES['attachment_group']['error'][$key] == 0) {
												if (file_exists($uploads_dir.DIRECTORY_SEPARATOR.$val)) {
													$i = 1;
													while (file_exists($uploads_dir.DIRECTORY_SEPARATOR.$i.'-'.$val)) {
														$i++;
													}
													$dest = $uploads_dir.DIRECTORY_SEPARATOR.$i.'-'.$val;
													$url = $url_dir.$i.'-'.$val;
												} else {
													$dest = $uploads_dir.DIRECTORY_SEPARATOR.$val;
													$url = $url_dir.$val;
												}
												move_uploaded_file($_FILES['attachment_group']['tmp_name'][$key], $dest);
												$wpdb->insert( 
													$wpdb->prefix.'bp_groups_downloads', 
													array( 
														'group_id' => $group_id, 
														'name' => $val ,
														'location' => $dest,
														'url' => $url
													), 
													array( 
														'%d', 
														'%s' ,
														'%s',
														'%s'
													) 
												);
											}
										}
										}
									?>
								</div>
							</div>
						<?php
					}
					else echo 'You must Join Community first! ';
						?>
						</div>
						<div class="clear"></div>
				    </div>
 				</div>
			</div>
			
		</div>
		
			<!--end issuer_content_block-->	
			<div class="clear"></div>
			
</div>

	<div class="space45"></div>

<?php get_sidebar( 'buddypress' ); ?>
<?php get_footer( 'buddypress' ); ?>
