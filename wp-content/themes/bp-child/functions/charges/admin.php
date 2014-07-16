<?php
/**
* Manage wp_organisations_charge Items
*/
require_once(THE_FUNCTION . '/charges/charges-table.php');
require_once(THE_FUNCTION . '/charges/class.charge.php');

//require_once (THE_FUNCTION . "/xero-api/CT_Xero.php");
//require_once (THE_FUNCTION . "/xero-items/xero-items-list-table.php");
//require_once (THE_FUNCTION . "/xero-items/xero-accounts-list-table.php");
//require_once(THE_FUNCTION . "/xero-items/class.xeroitem.php");

//Create Menus
add_action("admin_menu", "ct_add_manage_charges_menu");
function ct_add_manage_charges_menu()
{
    add_menu_page("Manage Charges Items", "Charges", "manage_options", "manage-charges", "ct_manage_invoices");
    add_submenu_page("manage-charges", "Add Charge Entry", "Add Charge Entry", "manage_options", "add-charge", "ct_add_charge");
    
    
}

function ct_manage_invoices()
{
    $listTable = new CT_Organisations_Charge_Table();
    $listTable->prepare_items();
    ?>
    <div class="wrap">
        <h2 style="float: left">Charges</h2>
        <br clear="all" />
        <form name="adminform" action="users.php?page=invoices" method="post">
            <?php
            echo $listTable->display();
            ?>
        </form>
    </div>
    <div class="wrap">
        <a href="<?php echo admin_url()?>admin.php?page=charges&org-action=update-all">Generate / Update Invoices</a>
    </div>
<?php
}
/**
 * Add or Edit Charge table entry
 *
 */
function ct_add_charge()
{
    global $wpdb;

    $variables = get_class_vars('CT_Charge');
    $data = array();
    foreach(array_keys($variables) as $_m)
    {
        $data[$_m] = '';
    }

    if(isset($_GET['id']))
    {
        $id = $_GET['id'];
        $xeroitemClass = new CT_Charge($_GET['id']);
        foreach($data as $_m=>$_v)
        {
            $data[$_m] = $xeroitemClass->$_m;
        }
    }
    else
    {
        $id = null;
    }

    //Getting Charges
    $query = "SELECT * FROM {$wpdb->prefix}organisations_charge";
    $accounts = $wpdb->get_results($query);
    $items = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}xeroitems", ARRAY_A);
    $organisations = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}organisations", ARRAY_A);
    ?>
    <div class="wrap">
        <h2><?php echo $id ? 'Edit' : 'New'?> Charge Entry</h2>
        <?php if(isset($_GET['org-message'])){ ?>
            <div id="message" class="updated below-h2"><p><?php echo $_GET['org-message']?></p></div>
            <?php
            $_GET['org-message'] = null;
            unset($_GET['org-message']);
        } ?>
        <br clear="all" />
        <form name="adminform" action="<?php echo admin_url()?>admin.php?page=add-charge<?php echo $id ? ('&id=' . $id) : ''?>" method="post">
            <table class="widefat" style="width: auto;">
                <tr>
                    <th>Organisation</th>
                    <td>
                        <select name="organisation_id" id="organisation_id">
                            <?php foreach( $organisations AS $organisation ):?>
                                <option value="<?php echo $organisation['id'];?>" <?php if( $organisation['contact_id'] == $data['organisation_id']):?>selected="selected" <?php endif;?>><?php echo $organisation['organisation_name'];?></option>
                            <?php endforeach;?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th>Payment Method</th>
                    <td>
                        <select name="payment_id" id="payment_id">
                            <option value="0" <?php if( 0 == $data['payment_id']):?>selected="selected" <?php endif;?>>Credit Card</option>
                            <option value="1" <?php if( 1 == $data['payment_id']):?>selected="selected" <?php endif;?>>Invoice Me</option>
                        </select>
                    <td>
                </tr>
                <tr>
                    <th>Item Code</th>
                    <td>
                        <select name="item_code" id="item_code">
                            <?php foreach( $items AS $item ):?>
                                <option value="<?php echo $item['code'];?>" <?php if( $item['code'] == $data['item_code']):?>selected="selected" <?php endif;?>><?php echo $item['code'];?></option>
                            <?php endforeach;?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th>Quantity</th>
                    <td><input type="text" name="quantity" id="quantity" value="<?php echo $data['quantity'];?>" required="required"/></td>
                </tr>
                <tr>
                    <th>Start Date</th>
                    <td><input type="text" name="start_date" id="start_date" value="<?php echo $data['start_date'] ? $data['start_date'] : date( 'Y-m-d');?>" disabled="disabled"/></td>
                </tr>
                <tr>
                    <th>End Date</th>
                    <td><input type="text" name="end_date" id="end_date" value="<?php echo $data['end_date'] ? $data['end_date'] : date( 'Y-m-d', strtotime('+7 days') );?>" disabled="disabled"/></td>
                </tr>
                <tr>
                    <th>Reference Type</th>
                    <td>
                        <select name="reference_type" id="reference_type">
                            <option value="subscription" <?php if( 'subscription' == $data['reference_type']):?>selected="selected" <?php endif;?>>Subscription</option>
                            <option value="ticket" <?php if( 'ticket' == $data['reference_type']):?>selected="selected" <?php endif;?>>Ticket</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th>Reference ID</th>
                    <td><input type="text" name="reference_id" id="reference_id" value="<?php echo $data['reference_id']?>" required="required"/></td>
                </tr>
                <tr>
                    <th>Invoice Identifier</th>
                    <td><input type="text" name="invoice_identifier" id="invoice_identifier" value="<?php echo $data['invoice_identifier']?>" required="required" readonly="readonly"/></td>
                </tr>
                <tr>
                    <th>Is Paid</th>
                    <td><input type="text" name="is_paid" id="is_paid" value="<?php echo $data['is_paid'] ? 'Yes' : 'No'?>" required="required" readonly="readonly"/></td>
                </tr>
                <tr>
                    <th>Comment</th>
                    <td><input type="text" name="comment" id="comment" value="<?php echo $data['comment']?>" required="required"/></td>
                </tr>
                <tr><td colspan="2"><input type="submit" value="Save Charge Entry" class="button button-primary" /></td></tr>
            </table>
            <input type="hidden" name="id" value="<?php echo $id?>" />
            <input type="hidden" name="org-action" value="save-charge" />
        </form>
    </div>
<?php
}


add_action("admin_init", "ct_process_charge_entry_admin_actions");
function ct_process_charge_entry_admin_actions()
{
    global $wpdb;

    if(isset($_REQUEST['org-action']))
    {
        $action = $_REQUEST['org-action'];

        if($action == 'save-charge')
        {
            //Save Charge Entry
            $chargeClass = new CT_Charge($_POST['id']);
            $chargeClass->bind($_POST);
            $resp = $chargeClass->save();
            if( $resp )
            {
                echo 'Charge entry saved successfully';
                ?>
                <script type="text/javascript">
                    setTimeout(function(){
                        document.location.href = '<?php echo admin_url()?>admin.php?page=manage-charges';
                    }, 1000);
                </script>
                <?php

                exit;
            }else{
                $_GET['org-message'] = $wpdb->last_error;
                return;
            }
        } elseif( $action == 'update-all'){
            $xero = new CT_Xero();
            //get entries without InvoiceID from wp_organisations_charge table
            $entries = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}organisations_charge WHERE invoice_identifier = '' AND payment_id = 1 GROUP BY organisation_id", ARRAY_A);
            if( $entries ){
                foreach( $entries AS $entry ){
                    $invoice = $xero->upsertInvoiceMeInvoice( $entry );
                }
            }
            var_dump($entries);die;
        }
    }

}
