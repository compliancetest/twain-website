<?php
if( isset( $_POST['create-moover'] ) || isset( $_POST['save-edited-moover'] ) ) {
	include_once dirname( __FILE__ ) . "/moover-admin-form-handler.php";
}
?>
<?php
if(!empty ( $_GET['remove_moover'] ) ) {
	include_once dirname( __FILE__ ) . "/moover-admin-remove.php";
}
?>
<?php
if(!empty ( $_POST['moover-action'] ) ) {
	include_once dirname( __FILE__ ) . "/moover-bulk-actions.php";
}
?>
<?php
global $wpdb;
$moover_result = $wpdb->get_results('SELECT * FROM ' . MOOVER_TABLE_NAME . ' ORDER BY moover_id DESC'); 
$wpdb->flush();
?>

<div class="wrap">
  <h2>mOover slider</h2>
  <?php if ( !empty( $moover_status ) ) : ?>
  <div class="moover-status <?php echo $moover_status_class ?>">
    <h3><?php echo $moover_status ?></h3>
    <div class="moover-status-close">close</div>
  </div>
  <?php endif; ?>
  <?php if ( $moover_result ) { ?>
  <p style="text-align:left"><a class="button-primary" href="?page=moover-add">Add New mOover</a></p>
  <form id="moover-ba-form" method="post" action="">
    <table cellpadding="0" cellspacing="0" class="wp-list-table widefat">
      <thead>
        <tr>
          <th class="check-column"><input type="checkbox" value="all" name="moover-id[]" /></th>
          <th scope="col">Title</th>
          <th>Shortcode</th>
          <th>Template Tag</th>
          <th>Version</th>
          <th>Created / Updated</th>
          <th>Edit</th>
          <th>Remove</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($moover_result as $single_moover) : ?>
        <tr>
          <td style="vertical-align:top"><input type="checkbox" value="<?php echo $single_moover -> moover_id ?>" name="moover-id[]" /></td>
          <td class="title column-title"><?php echo $single_moover -> title ?></td>
          <td>[moover id="<?php echo $single_moover -> moover_id ?>"]</td>
          <td>&lt;?php moover(<?php echo $single_moover -> moover_id ?>) ?&gt;</td>
          <td><?php echo $single_moover->version ?></td>
          <td><?php echo $single_moover->created ?></td>
          <td><a href="?page=moover-edit&amp;id=<?php echo $single_moover -> moover_id ?>" class="button-primary">Edit</a></td>
          <td><a href="?page=moover&amp;remove_moover=<?php echo $single_moover -> moover_id ?>" class="button-secondary moover_remove" style="color:red">X</a></td>
        </tr>
        <?php endforeach ?>
      </tbody>
      <tfoot>
        <tr>
          <td><input type="checkbox" value="all" name="moover-id[]" /></td>
          <td colspan="9"><select name="moover-action" style="width:150px">
              <option value="" selected="selected">Bulk Actions</option>
              <option value="copy">Copy</option>
              <option value="delete">Delete</option>
            </select>
            <input id="submit-moover-bulk-actions" name="moover-bulk-actions" type="submit" class="button-secondary" value="Apply" /></td>
        </tr>
      </tfoot>
    </table>
  </form>
  <p style="text-align:left"><a class="button-primary" href="?page=moover-add">Add New mOover</a></p>
  <?php }
  else {
  ?>
  <p>Seems to be you have not created any mOover. Click the button bellow to create your first mOover and get ready for fun:</p>
  <p style="text-align:left"><a class="button-primary" href="?page=moover-add">Add New mOover</a></p>
  <?php } ?>
  <h2>Dashboard</h2>
  <div class="metabox-holder">
    <div class="postbox-container" style="width:49.5%; margin-right:1%"> 
      <!-- Twitter -->
      <div class="postbox" style="margin-top:0">
         <h3 class="hndle"><span>Usage</span> </h3>  
		 <div class="inside">
			  <p><span class="moover-help"></span><strong>Shortcode</strong> - You can use this shortcode to insert created Slider into the Post or on some Page. Just Copy and Paste it to the Text Editor.</p>
			  <p><span class="moover-help"></span><strong>Template Tag</strong> - If you want to integrate mOover into the Theme code, you need to call the PHP function <strong>moover($id)</strong> with the ID number of required mOover.</p>
			  <p><span class="moover-help"></span><strong>Version</strong> - Version of created mOover. It is used to refresh caching of the generated scripts.</p>
			  <br/>
			  <p><strong>Example:</strong> 
			  <ol>
			  <li>Create your slider.</li>
			  <li>Add new page.</li>
			  <li>Select Custom Header Element in Page Options.</li>
			  <li>Add slider shortcode, for example [moover id="1"], to the Custom Header HTML field below the page editor.</li>
			  <li>All done.</li>
			  </ol>
			  </p>
		</div>
	  </div>
    </div>
    <div class="postbox-container" style="width:49.5%; margin-right:1"> 
      <!-- Transitions State -->
      <div class="postbox" style="margin-top:0px;">
        <h3 class="hndle"><span>Settings</span> </h3>
        <div class="inside">
          
          <form action="options.php" method="post">
            <div class="moover-settings">
				<?php settings_fields( 'moover_settings' );?>
                <?php do_settings_sections( 'general' ); ?>
                </div>
                <p class="submit" style="padding:0; margin-top:0">
                  <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
                </p>
          </form>
          
        </div>
      </div>
    </div>
  </div>
</div>
