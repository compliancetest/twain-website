<?php
/**
* Manage Xero Payments
*/
require_once (THE_FUNCTION . "/xero-payments/xero-payments-list-table.php");

//Create Menus
add_action("admin_menu", "ct_add_manage_xero_payments_menu");
function ct_add_manage_xero_payments_menu()
{
    add_menu_page("Manage Xero Payments", "Xero Payments", "manage_options", "manage-xero-payments", "ct_show_xero_payments_list");
}

function ct_show_xero_payments_list()
{
    $listTable = new CT_Xero_Payments_Table();
    $listTable->prepare_items();
    ?>
    <div class="wrap">
        <h2>Xero Payments</h2>
        <br clear="all" />
        <form name="adminform" action="users.php?page=processing" method="post">
        <?php
            echo $listTable->display();
        ?>
        </form>
    </div>
    <div class="wrap">
    </div>
    <?php       
}

add_action("admin_init", "ct_process_xero_payment_admin_actions");
function ct_process_xero_payment_admin_actions()
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