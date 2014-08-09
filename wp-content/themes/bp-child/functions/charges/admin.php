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
        <h2>Charges</h2>
        <?php flushMessages(); ?>
        <br clear="all" />
        <form name="adminform" action="users.php?page=invoices" method="post">
            <?php
            echo $listTable->display();
            ?>
        </form>
    </div>
    <?php
        $chargesObject = new CT_Charge();
    ?>
    <div class="wrap">
        <a href="#" id="custom_invoices_show">Generate Invoices for specific organisations</a>
        <div class="clear"></div>
        <form id="charges_for_custom_org" name="charges_for_custom_org" action="<?php echo admin_url()?>admin.php?page=manage-charges&org-action=<?php echo wp_create_nonce( 'update-specific' );?>" method="post" style="display: none;">
            <table class="widefat" style="width: auto;">
                <tr>
                    <th>Organisation</th>
                    <td>
                        <select name="org_id[]">
                            <?php foreach( $chargesObject->getOrganisationsList() AS $organisation ):?>
                                <?php
                                    $orgObject = new CT_Organisation( $organisation->organisation_id );
                                ?>
                                   <option value="<?php echo $organisation->organisation_id;?>"><?php echo $orgObject->organisation_name;?></option>
                            <?php endforeach;?>
                        </select>
                    </td>
                </tr>
                <tr><td colspan="2"><input type="submit" value="Generate" class="button button-primary" /></td></tr>
            </table>
        </form>
        <div class="clear"></div>
        <a href="<?php echo admin_url()?>admin.php?page=manage-charges&org-action=<?php echo wp_create_nonce( 'update-all' );?>">Generate Invoices for ALL organisations</a>
        <div class="clear"></div>
        <a href="<?php echo admin_url()?>admin.php?page=manage-charges&org-action=<?php echo wp_create_nonce( 'update-status' );?>">Update "Invoice Me" Invoice Status</a>
        <div class="clear"></div>
        <a href="#" id="monthly_custom_invoices_show">Generate Monthly Charges</a>
        <div class="clear"></div>
        <form id="monthly_charges_for_custom_org" name="monthly_charges_for_custom_org" action="<?php echo admin_url()?>admin.php?page=manage-charges&org-action=<?php echo wp_create_nonce( 'generate_monthly_charges' );?>" method="post" style="display: none;">
            <table class="widefat" style="width: auto;">
                <tr>
                    <th>Organisation</th>
                    <td>
                        <?php
                            global $wpdb;
                            $organisationsWithSubscriptions = $wpdb->get_results("SELECT organisation_id, organisation_name
                                                                                    FROM `wp_organisations_subscriptions` AS s
                                                                                    JOIN wp_organisations AS o ON o.id = s.organisation_id
                                                                                    WHERE o.no_billing = 0
                                                                                    GROUP BY organisation_id");
                        ?>
                        <select name="org_id">
                            <option value=""></option>
                            <?php foreach( $organisationsWithSubscriptions AS $organisation ):?>
                                <option value="<?php echo $organisation->organisation_id;?>"><?php echo $organisation->organisation_name;?></option>
                            <?php endforeach;?>
                        </select>
                    </td>
                </tr>
                <tr><td colspan="2"><input type="submit" value="Generate" class="button button-primary" /></td></tr>
            </table>
        </form>
    </div>
    <script>
        jQuery(document).ready(function(){
            jQuery('#custom_invoices_show').on('click', function(e){
                e.preventDefault();
                jQuery('#charges_for_custom_org').toggle();
            });
            jQuery('#monthly_custom_invoices_show').on('click', function(e){
                e.preventDefault();
                jQuery('#monthly_charges_for_custom_org').toggle();
            });
        });
    </script>
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
    $organisations = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}organisations WHERE no_billing = 0 ORDER BY organisation_name ASC", ARRAY_A);
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
                            <option></option>
                            <?php foreach( $organisations AS $organisation ):?>
                                <option value="<?php echo $organisation['id'];?>" <?php if(  $data['organisation_id'] != '' && $organisation['id'] == $data['organisation_id']):?>selected="selected" <?php endif;?>><?php echo $organisation['organisation_name'];?></option>
                            <?php endforeach;?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th>Payment Method</th>
                    <td>
                        <select name="payment_id" id="payment_id">
                            <?php
                                if( ! empty( $data['organisation_id'] ) ){
                                    $payment_method = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}organisations_payment_methods WHERE organisation_id = %s", $data['organisation_id']), ARRAY_A );
                                    foreach( $payment_method AS $p_m ){ ?>
                                        <option value="<?php echo $p_m['id'];?>" <?php if( $p_m['id'] == $data['payment_id']):?>selected="selected" <?php endif;?>><?php echo $p_m['nickname'].' ('. ($p_m['invoice_me'] == 1 ? 'Invoice Me' : 'Credit Card' ).')';?></option>
                                    <?php }
                                }
                            ?>
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
                <?php if( isset( $data['start_date'] ) && $data['start_date'] != '0000-00-00 00:00:00' ): ?>
                    <tr>
                        <th>Start Date</th>
                        <td><input type="text" name="start_date" id="start_date" value="<?php echo date( 'Y-m-d', strtotime( $data['start_date'] ) );?>" disabled="disabled"/></td>
                    </tr>
                    <tr>
                        <th>End Date</th>
                        <td><input type="text" name="end_date" id="end_date" value="<?php echo date( 'Y-m-d', strtotime( $data['end_date'] ) );?>" disabled="disabled"/></td>
                    </tr>
                <?php endif;?>
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
    <script>
        jQuery(document).ready( function(){
            jQuery('#organisation_id').on('change', function(){
                jQuery.ajax({
                    type : 'post',
                    dataType: 'json',
                    url: '/wp-admin/admin-ajax.php',
                    data : { 'action' : 'get_payment_methods', 'org_id' : jQuery(this).val() },
                    success: function( data ){
                        jQuery('#payment_id').find('option')
                            .remove()
                            .end();
                        if( data && data.length ){
                            jQuery('#payment_id').find('option')
                                .remove()
                                .end();
                            jQuery.each(data, function( key, value ) {
                                jQuery('#payment_id')
                                    .append(jQuery("<option/>", {
                                    value: value.id,
                                    text: ( value.nickname + ' (' + ( value.invoice_me == '0' ? 'Credit Card' : 'Invoice Me' )+')' )
                                }));
                            });
                        }
                    }
                });
            })
        })
    </script>
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
                redirect_then_exit();
            }else{
                $_GET['org-message'] = $wpdb->last_error;
                return;
            }
        } elseif( wp_verify_nonce( $action, 'update-all' ) ){
            /**
             * 1) We get organisations IDs with empty 'invoice_identifier' field
             */
            $organisations = $wpdb->get_results("SELECT organisation_id FROM {$wpdb->prefix}organisations_charge WHERE invoice_identifier = '' GROUP BY organisation_id", ARRAY_A);
            $counter = 0;
            if( $organisations ){
                foreach( $organisations AS $organisation ){
                    if( $wpdb->get_var( $wpdb->prepare("SELECT no_billing FROM {$wpdb->prefix}organisations WHERE id = %d", $organisation['organisation_id']) ) === '1' ){
                        continue;
                    }
                    /**
                     * 2) We get organisation's payments types list and for each payment type create invoice
                     */
                    $paymentTypes = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}organisations_charge WHERE invoice_identifier = '' AND  organisation_id = %s GROUP BY payment_id", $organisation['organisation_id'] ), ARRAY_A);
                    foreach( $paymentTypes AS $paymentType ){
                        $xero = new CT_Xero();
                        $paymentID = $paymentType['payment_id'];
                        $invoice = $xero->upsertInvoice( $paymentType, $paymentID );
                        if( isset( $invoice['Invoices']['Invoice']['InvoiceID'] ) ){
                            $wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->prefix}organisations_charge SET invoice_identifier = %s, start_date = %s, end_date = %s WHERE invoice_identifier = '' AND payment_id = %d AND organisation_id = %d ", $invoice['Invoices']['Invoice']['InvoiceID'], date( 'Y-m-d', strtotime( $invoice['Invoices']['Invoice']['Date'] ) ), date( 'Y-m-d', strtotime( $invoice['Invoices']['Invoice']['DueDate'] ) ), $paymentID, $paymentType['organisation_id'] ) );
                            $counter++;
                        }
                    }
                }
            }
            echo 'Created '.$counter.' invoices';
            redirect_then_exit();
        } elseif( wp_verify_nonce( $action, 'update-specific' ) ){
            if( ! empty( $_POST ) && isset( $_POST['org_id'] ) ){
                $organisations = $_POST['org_id'];
                $counter = 0;
                if( $organisations ){
                    foreach( $organisations AS $organisation ){
                        if( $wpdb->get_var( $wpdb->prepare("SELECT no_billing FROM {$wpdb->prefix}organisations WHERE id = %d", $organisation) ) === '1' ){
                            continue;
                        }
                        $paymentTypes = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}organisations_charge WHERE invoice_identifier = '' AND  organisation_id = %s GROUP BY payment_id", $organisation ), ARRAY_A);
                        foreach( $paymentTypes AS $paymentType ){
                            $xero = new CT_Xero();
                            $paymentID = $paymentType['payment_id'];
                            $invoice = $xero->upsertInvoice( $paymentType, $paymentID );
                            if( isset( $invoice['Invoices']['Invoice']['InvoiceID'] ) ){
                                $wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->prefix}organisations_charge SET invoice_identifier = %s, start_date = %s, end_date = %s WHERE invoice_identifier = '' AND payment_id = %d AND organisation_id = %d ", $invoice['Invoices']['Invoice']['InvoiceID'], date( 'Y-m-d', strtotime( $invoice['Invoices']['Invoice']['Date'] ) ), date( 'Y-m-d', strtotime( $invoice['Invoices']['Invoice']['DueDate'] ) ), $paymentID, $paymentType['organisation_id'] ) );
                                $counter++;
                            }
                        }
                    }
                }
                echo 'Created '.$counter.' invoices';
            } else {
                echo 'Please select at least 1 organisation';
            }
            redirect_then_exit();
        } elseif( wp_verify_nonce( $action, 'update-status' ) ){
            $counter = 0;
            $xero = new CT_Xero();
            $invoicesIdList = $wpdb->get_results("SELECT invoice_identifier FROM {$wpdb->prefix}organisations_charge WHERE is_paid = 0 GROUP BY invoice_identifier", ARRAY_A);
            if( $invoicesIdList ){
                foreach( $invoicesIdList AS $invoice ){
                    $invoiceData = $xero->getInvoice( $invoice['invoice_identifier'] );
                    if( $invoiceData['Invoices']['Invoice']['Status'] === 'PAID' ){
                        $wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->prefix}organisations_charge SET is_paid = 1 WHERE invoice_identifier = %s ", $invoice['invoice_identifier'] ) );
                        $counter++;
                    }
                }
            }
            echo 'Updated '.$counter.' invoices';
            redirect_then_exit();
        } else if( wp_verify_nonce( $action, 'generate_monthly_charges' ) ){
            $org_id_where = '';
            if( isset( $_POST['org_id'] ) && ! empty( $_POST['org_id'] ) ){
                $org_id_where = $wpdb->prepare( ' AND organisation_id = %d', $_POST['org_id'] );
            }
            $newChargesCounter = 0;
            $subscriptionsList = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}organisations_subscriptions WHERE status = 'Active'".$org_id_where);
            foreach( $subscriptionsList AS $subscription ){
                $organisation = new CT_Organisation( $subscription->organisation_id );
                if( $organisation->no_billing != '1' ){
                    $suite = new TestSuite( $subscription->suite_family_mark );
                    $suite->load();
                    if( ! in_array( $suite->monthlySubscriptionPriceValue, array( 0, -1 ) ) ){
                        if( $subscription->last_charge_date == '0000-00-00' ){
                            $data = array(
                                'organisation_id' => $subscription->organisation_id,
                                'payment_id'      => $subscription->payment_method,
                                'item_code'       => $suite->monthlySubscriptionPrice,
                                'quantity'        => '1.00',
                                'reference_type'  => 'Subscription',
                                'comment'         => $wpdb->get_var($wpdb->prepare("SELECT description FROM {$wpdb->prefix}xeroitems WHERE code = %s", $suite->monthlySubscriptionPrice))
                            );
                            $chargeClass = new CT_Charge();
                            $chargeClass->bind($data);
                            $chargeClass->save();
                            $newChargesCounter++;
                        } else {
                            $monthesCounter = 0;
                            if( date( 'm', strtotime( $subscription->last_charge_date ) ) != date( 'm' ) ){
                                while( date( 'm', strtotime( $subscription->last_charge_date ) ) != date('m', strtotime('-'.$monthesCounter.' month' ) ) ){
                                    $data = array(
                                        'organisation_id' => $subscription->organisation_id,
                                        'payment_id'      => $subscription->payment_method,
                                        'item_code'       => $suite->monthlySubscriptionPrice,
                                        'quantity'        => '1.00',
                                        'reference_type'  => 'Subscription',
                                        'comment'         => $wpdb->get_var($wpdb->prepare("SELECT description FROM {$wpdb->prefix}xeroitems WHERE code = %s", $suite->monthlySubscriptionPrice))
                                    );
                                    $chargeClass = new CT_Charge();
                                    $chargeClass->bind($data);
                                    $chargeClass->save();
                                    $newChargesCounter++;
                                    $monthesCounter++;
                                }
                            }
                        }
                        $wpdb->update("{$wpdb->prefix}organisations_subscriptions",
                            array('last_charge_date' => date('Y-m-d') ),
                            array('id' => $subscription->id)
                        );
                    }
                }
            }
            addMessage('<b>Added: '.$newChargesCounter.' entries</b>', 'success');
            wp_redirect('admin.php?page=manage-charges');
            exit();
        }
    }

}
add_action( 'wp_ajax_get_payment_methods', 'get_payment_methods_callback' );
function get_payment_methods_callback() {
    global $wpdb;
    $organisationID = filter_var( $_POST['org_id'], FILTER_SANITIZE_NUMBER_INT );
    if( empty( $organisationID ) ){
        exit();
    }
    $paymentMethods = $wpdb->get_results( $wpdb->prepare( "SELECT id, nickname, invoice_me FROM {$wpdb->prefix}organisations_payment_methods WHERE organisation_id = %d AND status = 'Active'", $organisationID ), ARRAY_A );
    exit( json_encode( $paymentMethods ) );
}
function redirect_then_exit(){
    ?>
    <script type="text/javascript">
        setTimeout(function(){
            document.location.href = '<?php echo admin_url()?>admin.php?page=manage-charges';
        }, 1000);
    </script>
<?php
    exit;
}
?>
