<?php
/**
* Manage wp_batch_jobs table
*/
require_once(THE_FUNCTION . '/batch-jobs/batch-jobs-table.php');


//Create Menus
add_action("admin_menu", "ct_add_manage_batch_jobs");
function ct_add_manage_batch_jobs()
{
    add_menu_page("Batch Jobs", "Batch Jobs", "manage_options", "manage-batch-jobs", "ct_manage_batch_jobs");
}

function ct_manage_batch_jobs()
{
    $listTable = new CT_Batch_Jobs_Table();
    $listTable->prepare_items();
    ?>
    <div class="wrap">
        <h2>Batch Jobs</h2>
        <?php flushMessages(); ?>
        <br clear="all" />
        <form name="adminform" action="users.php?page=invoices" method="post">
            <?php
            echo $listTable->display();
            ?>
        </form>
    </div>
    <script>
        jQuery(document).ready( function(){
            jQuery('.batch_status').on('change', function(){
                jQuery.ajax({
                    type : 'post',
                    dataType: 'json',
                    url: '/wp-admin/admin-ajax.php',
                    data : { 'action' : 'set_batch_status', 'status' : jQuery(this).is(':checked'), 'id' : jQuery(this).closest('tr').find('td:first').text() },
                    success: function( data ){

                        if( data && data.length ){
                            jQuery('#payment_id').find('option')
                                .remove()
                                .end();
                        }
                    }
                });
            })
        });
    </script>
<?php
}


add_action( 'wp_ajax_set_batch_status', 'set_batch_status' );
function set_batch_status() {
    global $wpdb;
    $status = $_POST['status'] == 'true' ? 1 : 0;
    $wpdb->query($wpdb->prepare("UPDATE wp_batch_jobs SET is_active = %d WHERE id = %d", $status, intval($_POST['id'])));
    exit();
}