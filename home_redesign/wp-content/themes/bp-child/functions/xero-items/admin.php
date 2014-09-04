<?php
/**
* Manage Xero Items
*/
require_once (THE_FUNCTION . "/xero-api/CT_Xero.php");
require_once (THE_FUNCTION . "/xero-items/xero-items-list-table.php");

//Create Menus
add_action("admin_menu", "ct_add_manage_xeroitems_menu");
function ct_add_manage_xeroitems_menu()
{
    add_menu_page("Manage Xero Items", "Xero Items", "manage_options", "manage-xeroitems", "ct_show_xeroitems_list");
}

function ct_show_xeroitems_list()
{
    $listTable = new CT_Xeroitems_List_Table();
    $listTable->prepare_items();
    ?>
    <div class="wrap">
        <h2>Xero Items</h2>
        <br clear="all" />
        <form name="adminform" action="users.php?page=processing" method="post">
        <?php
            echo $listTable->display();
        ?>
        </form>
    </div>
    <div class="wrap">
        <a href="<?php echo admin_url()?>admin.php?page=manage-xeroitems&org-action=reload-xeroitems">Update Items From Xero</a>
    </div>
    <?php       
}

add_action("admin_init", "ct_process_xeroitem_admin_actions");
function ct_process_xeroitem_admin_actions()
{
    global $wpdb;
    
    if(isset($_REQUEST['org-action']))
    {
        $action = $_REQUEST['org-action'];
        
        if( $action == 'reload-xeroitems' ){
            $xero = new CT_Xero();
            $xero->updateItems();
            echo 'Done';
            ?>
            <script type="text/javascript">
                setTimeout(function(){
                    document.location.href = '<?php echo admin_url()?>admin.php?page=manage-xeroitems';
                }, 1000);
            </script>
            <?php

            exit;
        }
    }
    
}