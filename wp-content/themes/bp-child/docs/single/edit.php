<div id="buddypress">

	<?php include( bp_docs_locate_template( 'single/sidebar.php' ) ) ?>

	<?php include( apply_filters( 'bp_docs_header_template', bp_docs_locate_template( 'docs-header.php' ) ) ) ?>
    
    <div  class="column">
        <div class="tabs_wrap light_gray_bcg radius6">        
             <?php include ( bp_docs_locate_template( 'single/doc-tab-nav.php' ) ) ?>
	        <?php
	        // No media support at the moment. Want to integrate with something like BP Group Documents
	        // include_once ABSPATH . '/wp-admin/includes/media.php' ;

	        if ( !function_exists( 'wp_editor' ) ) {
		        require_once ABSPATH . '/wp-admin/includes/post.php' ;
		        wp_tiny_mce();
	        }

	        ?>

	        <?php do_action( 'template_notices' ) ?>

	        <div class="tab-content white_bcg padding15-20">

	            <div id="idle-warning" style="display:none">
		            <p><?php _e( 'You have been idle for <span id="idle-warning-time"></span>', 'bp-docs' ) ?></p>
	            </div>

	            <form action="" method="post" class="standard-form" id="doc-form">
	                <div class="doc-header">
		            <?php if ( bp_docs_is_existing_doc() ) : ?>
			            <input type="hidden" id="existing-doc-id" value="<?php the_ID() ?>" />
		            <?php endif ?>
	                </div>
	                <div class="doc-content-wrapper">                    		                
                        <div class="grid-box grid-box-expandable grid-box-opened">
                           <div class="grid-box-header">
<!--                               <a class="gbh-btn gbh-btn-expandable left" href="javascript: void(0);">Ex</a>-->
                               <h5 class="left"><b>Information</b></h5>
                               <div class="clear"></div>
                           </div>
                           <div class="grid-box-body">
                               <div class="column">
                                   <div class="field-row">
                                       <div class="grid-cell width100P" id="doc-content-title">
                                           <label for="doc[title]"><?php _e( 'Title', 'bp-docs' ) ?></label>
                                           <input type="text" id="doc-title" name="doc[title]" class="long" value="<?php bp_docs_edit_doc_title() ?>" />                                      
                                       </div>                                   
                                       <div class="clear"></div>
                                   </div>
                                   <?php if ( bp_docs_is_existing_doc() ) : ?>
                                   <div class="field-row">
                                        <div id="doc-content-permalink" class="grid-cell width100P">
                                            <label for="doc[permalink]"><?php _e( 'Permalink', 'bp-docs' ) ?>: </label>
                                            <code><?php echo trailingslashit( bp_get_root_domain() ) . BP_DOCS_SLUG . '/' ?></code><input type="text" id="doc-permalink" name="doc[permalink]" class="long" value="<?php bp_docs_edit_doc_slug() ?>" />
                                        </div>
                                        <div class="clear"></div>
                                   </div>
                                   <?php endif ?>
                                   <div class="field-row">
                                        <div id="doc-content-textarea">
                                            <label id="content-label" for="doc_content"><?php _e( 'Content', 'bp-docs' ) ?></label>
                                            <div id="editor-toolbar">
                                            <?php
                                                if ( function_exists( 'wp_editor' ) ) {
                                                    wp_editor( bp_docs_get_edit_doc_content(), 'doc_content', array(
                                                        'media_buttons' => false,
                                                        'dfw'        => false
                                                    ) );
                                                } else {
                                                    the_editor( bp_docs_get_edit_doc_content(), 'doc_content', 'doc[title]', false );
                                                }
                                            ?>
                                            </div>
                                        </div>
                                   </div>
                                </div>
                           </div>
                       </div>
		                <div class="space20"></div>
		                <?php if ( apply_filters( 'bp_docs_enable_attachments', true ) ) : ?>
                            <div class="grid-box grid-box-expandable grid-box-closed">
                               <div class="grid-box-header">
<!--                                   <a class="gbh-btn gbh-btn-expandable left" href="javascript: void(0);">Ex</a>-->
                                   <h5 class="left"><b>Attachments</b></h5>
                                   <div class="clear"></div>
                               </div>
                               <div class="grid-box-body">
                                   <div class="column">
                                       <div class="field-row"  id="doc-attachments">
                                        <?php include ( bp_docs_locate_template( 'single/attachments.php' ) ) ?>
                                       </div>
                                   </div>
                               </div>
                            </div>			    
                            <div class="space20"></div>            
		                <?php endif ?>
		                <div id="doc-meta">
			                <?php if ( bp_is_active( 'groups' ) && bp_docs_current_user_can( 'manage' ) && apply_filters( 'bp_docs_allow_associated_group', true ) ) : ?>
                                <div class="grid-box grid-box-expandable grid-box-opened">
                                   <div class="grid-box-header">
<!--                                       <a class="gbh-btn gbh-btn-expandable left" href="javascript: void(0);">Ex</a>-->
                                       <h5 class="left"><b><?php _e( 'Associated Group', 'bp-docs' ) ?></b></h5>
                                       <div class="clear"></div>
                                   </div>
                                   <div class="grid-box-body">
                                       <div class="column">
                                           <div class="field-row" id="doc-associated-group">
                                                <table class="toggle-table" id="toggle-table-associated-group">
                                                    <?php bp_docs_doc_associated_group_markup() ?>
                                                </table>
                                           </div>
                                       </div>
                                   </div>
                                </div>
				                <div class="space20"></div>
			                <?php endif ?>
			                
                            <?php if ( bp_docs_current_user_can( 'manage' ) && apply_filters( 'bp_docs_allow_access_settings', true ) ) : ?>
				                <div class="grid-box grid-box-expandable grid-box-opened" <?php if(!bp_current_user_can( 'bp_moderate' )){ ?> style="display: none" <?php } ?>>
                                   <div class="grid-box-header">
<!--                                       <a class="gbh-btn gbh-btn-expandable left" href="javascript: void(0);">Ex</a>-->
                                       <h5 class="left"><b><?php _e( 'Access', 'bp-docs' ) ?></b></h5>
                                       <div class="clear"></div>
                                   </div>
                                   <div class="grid-box-body">
                                       <div class="column">
                                           <div class="field-row" id="toggle-table-settings">
							                    <table class="toggle-table" >
								                    <?php bp_docs_doc_settings_markup() ?>
							                    </table>
                                           </div>
						                </div>
					                </div>
				                </div>
                                <div class="space20"></div>
			                <?php endif ?>
                            
			                <div class="grid-box grid-box-expandable grid-box-opened">
                               <div class="grid-box-header">
<!--                                   <a class="gbh-btn gbh-btn-expandable left" href="javascript: void(0);">Ex</a>-->
                                   <h5 class="left"><b>Tags</b></h5>
                                   <div class="clear"></div>
                               </div>
                               <div class="grid-box-body">
                                   <div class="column">
                                       <div class="field-row" id="toggle-table-settings">
                                            <div class="grid-cell left width40P">
									            <label for="bp_docs_tag"><?php _e( 'Tags are words or phrases that help to describe and organize your Docs.', 'bp-docs' ) ?></label>
									            <span class="description"><?php _e( 'Separate tags with commas (for example: <em>orchestra, snare drum, piccolo, Brahms</em>)', 'bp-docs' ) ?></span>
                                            </div>
                                            <div class="grid-cell left width50P left15">
									            <?php bp_docs_post_tags_meta_box() ?>
                                            </div>
                                            <div class="clear"></div>
                                       </div>
								              
					                </div>
				                </div>
			                </div>
                            <div class="space20"></div>
                            <div class="grid-box grid-box-expandable grid-box-opened">
                               <div class="grid-box-header">
<!--                                   <a class="gbh-btn gbh-btn-expandable left" href="javascript: void(0);">Ex</a>-->
                                   <h5 class="left"><b>Parent</b></h5>
                                   <div class="clear"></div>
                               </div>
                               <div class="grid-box-body">
                                   <div class="column">
                                       <div class="field-row" id="toggle-table-parent">		
                                           <div class="grid-cell left width40P">     					                
									           <label for="parent_id"><?php _e( 'Select a parent for this Doc.', 'bp-docs' ) ?></label>

									           <span class="description"><?php _e( '(Optional) Assigning a parent Doc means that a link to the parent will appear at the bottom of this Doc, and a link to this Doc will appear at the bottom of the parent.', 'bp-docs' ) ?></span>
								           </div>
                                           <div class="grid-cell left width50P left15"> 
									           <?php bp_docs_edit_parent_dropdown() ?>
                                           </div>
                                           <div class="clear"></div>
								       </div>
					                </div>
				                </div>
			                </div>
		                </div>

		                <div style="clear: both"> </div>

		                <div id="doc-submit-options">

			                <?php wp_nonce_field( 'bp_docs_save' ) ?>

			                <?php $doc_id = bp_docs_is_existing_doc() ? get_the_ID() : 0 ?>
			                <input type="hidden" id="doc_id" name="doc_id" value="<?php echo $doc_id ?>" />
			                <input type="hidden" name="doc-edit-submit" id="doc-edit-submit" value="<?php _e( 'Save', 'bp-docs' ) ?>"> 
                            <a href="#" class="action-btn process-btn submit-btn"><span class="p"></span><span class="t">Save</span></a>
                            <?php
                                $cancel_url = bp_docs_get_doc_link();
                                $selected_group_slug = isset( $_GET['group'] ) ? $_GET['group'] : '';
                                if ($selected_group_slug != '') {
                                    // Support for BP Group Hierarchy
                                    if ( false !== $slash = strrpos( $selected_group_slug, '/' ) ) {
                                        $selected_group_slug = substr( $selected_group_slug, $slash + 1 );
                                    }

                                    $selected_group_id = BP_Groups_Group::get_id_from_slug( $selected_group_slug );
                                    $cancel_url = bp_get_group_permalink(groups_get_group(array("group_id" =>$selected_group_id))) . 'wiki';
                                }
                            ?>
                            <a href="<?php echo $cancel_url ?>" class="action-btn cancel-btn left10"><span class="p"></span><span class="t"><?php _e( 'Cancel', 'bp-docs' ); ?></span></a>

			                <?php if ( bp_docs_is_existing_doc() ) : ?>
				                <?php if ( bp_docs_current_user_can( 'manage' ) ) : ?><a class="delete-doc-button confirm action-btn delete-btn" href="<?php bp_docs_delete_doc_link() ?>"><span class="p"></span><span class="t"><?php _e( 'Delete', 'bp-docs' ) ?></span></a><?php endif ?>
			                <?php endif ?>
		                </div>

		                <div class="clear"> </div>
	                </div>
	            </form>

	        </div><!-- .doc-content -->

	        <?php bp_docs_inline_toggle_js() ?>

	        <?php if ( !function_exists( 'wp_editor' ) ) : ?>
	        <script type="text/javascript">
	        jQuery(document).ready(function($){
		        /* On some setups, it helps TinyMCE to load if we fire the switchEditors event on load */
		        if ( typeof(switchEditors) == 'object' ) {
			        if ( !$("#edButtonPreview").hasClass('active') ) {
				        switchEditors.go('doc_content', 'tinymce');
			        }
		        }
	        },(jQuery));
	        </script>
	        <?php endif ?>

	        <?php /* Important - do not remove. Needed for autosave stuff */ ?>
	        <div style="display:none;">
	        <div id="still_working_content" name="still_working_content">
		        <br />
		        <h3><?php _e( 'Are you still there?', 'bp-docs' ) ?></h3>

		        <p><?php _e( 'In order to prevent overwriting content, only one person can edit a given doc at a time. For that reason, you must periodically ensure the system that you\'re still actively editing. If you are idle for more than 30 minutes, your changes will be auto-saved, and you\'ll be sent out of Edit mode so that others can access the doc.', 'bp-docs' ) ?></p>

		        <a href="#" onclick="jQuery.colorbox.close(); return false" class="button"><?php _e( 'I\'m still editing!', 'bp-docs' ) ?></a>
	        </div>
	        </div>
        </div>
    </div>
</div><!-- /#buddypress -->
