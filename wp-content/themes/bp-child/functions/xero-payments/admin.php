<?php
/**
* Manage Xero Payments
*/
require_once (THE_FUNCTION . "/xero-payments/xero-payments-list-table.php");
require_once (THE_FUNCTION . "/xero-payments/class.payments.php");

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
        <?php flushMessages(); ?>
        <form name="adminform" action="users.php?page=processing" method="post">
        <?php
            echo $listTable->display();
        ?>
        </form>
    </div>
    <div class="wrap">
        <a id="query_unpaid_invoices" href="#">Query Unpaid CC Invoices from Xero</a>
        <div class="clear"></div>
        <form id="query_unpaid_invoices_form" name="query_unpaid_invoices_form" action="<?php echo admin_url()?>admin.php?page=manage-xero-payments&org-action=query_unpaid_invoices" method="post" style="display: none;">
            <table class="widefat" style="width: auto;">
                <tr>
                    <th colspan="2"><b>Note: </b>You can skip any organisation selecting  to create</br>payments entries for each non-paid approved invoice</th>
                </tr>
                <?php  $chargesObject = new CT_Charge();?>
                <?php foreach( $chargesObject->getOrganisationsList( true ) AS $organisation ):?>
                    <?php
                    $orgObject = new CT_Organisation( $organisation->organisation_id );
                    ?>
                    <tr>
                        <th><?php echo $orgObject->organisation_name;?></th>
                        <td><input type="checkbox" name="org_id[]" value="<?php echo $organisation->organisation_id;?>" class="org_id"/></td>
                    </tr>
                <?php endforeach;?>
                <tr class="invoices_tr" style="display: none;">
                    <th>Invoices</th>
                    <td>
                        <select name="invoices_numbers" id="invoices_numbers">
                        </select>
                    <td>
                </tr>
                <tr><td colspan="2"><input type="submit" value="Generate" class="button button-primary" /></td></tr>
            </table>
        </form>
        <div class="clear"></div>
        <a href="<?php echo admin_url()?>admin.php?page=manage-xero-payments&org-action=mark_charges">Mark Charges As paid</a>
        <div class="clear"></div>
        <a href="<?php echo admin_url()?>admin.php?page=manage-xero-payments&org-action=update_paid_invoices">Update Paid CC Invoices in Xero</a>
    </div>
    <script>
        jQuery(document).ready(function(){
            jQuery('#query_unpaid_invoices').on('click', function(e){
                e.preventDefault();
                jQuery('#query_unpaid_invoices_form').toggle();
            });
            jQuery('.org_id').on('change', function(){
                if( jQuery('.org_id:checked').length ){
                    var searchIDs = jQuery(".org_id:checkbox:checked").map(function(){
                        return jQuery(this).val();
                    }).get();
                    jQuery.ajax({
                        type : 'post',
                        dataType: 'json',
                        url: '/wp-admin/admin-ajax.php',
                        data : { 'action' : 'get_invoices', 'org_id' : searchIDs },
                        success: function( data ){
                            jQuery('#invoices_numbers').find('option')
                                .remove()
                                .end();
                            if( data && data.length ){
                                jQuery('#invoices_numbers').find('option')
                                    .remove()
                                    .end();
                                jQuery.each(data, function( key, value ) {
                                    jQuery('#invoices_numbers')
                                        .append(jQuery("<option/>", {
                                            value: value.id,
                                            text: ( value.id )
                                        }));
                                });
                            }
                        }
                    });
                    jQuery('.invoices_tr').show();
                } else {
                    jQuery('#invoices_numbers').find('option')
                        .remove()
                        .end();
                    jQuery('.invoices_tr').hide();
                }
            })
        });
    </script>
    <?php       
}
add_action( 'wp_ajax_get_invoices', 'get_invoices' );

function get_invoices() {
    global $wpdb;
    $results = $wpdb->get_results($wpdb->prepare("SELECT c.invoice_identifier AS id FROM {$wpdb->prefix}organisations_charge AS c
                                                                  JOIN {$wpdb->prefix}organisations AS o ON o.id = c.organisation_id
                                                                  LEFT JOIN {$wpdb->prefix}xero_payments AS p ON p.invoice_id = c.invoice_identifier
                                                                  WHERE invoice_identifier != '' AND c.is_paid = 0 AND o.no_billing = 0 AND o.invoice_me = 0 AND c.organisation_id IN( %d ) AND p.invoice_id IS NULL
                                                                  GROUP BY invoice_identifier", implode(',', $_POST['org_id'])));
    exit( json_encode( $results ) );
}
add_action("admin_init", "ct_process_xero_payment_admin_actions");
function ct_process_xero_payment_admin_actions()
{
    global $wpdb;
    
    if(isset($_REQUEST['org-action']))
    {
        $action = $_REQUEST['org-action'];
        if( $action == 'query_unpaid_invoices' ){
            $paymentsCounter = $nonApprovedPaymentsCounter = 0;
            $xero_payments = new CT_Payments();
            $xero_api = new CT_Xero();
            if( isset( $_REQUEST['org_id'] ) ){
                foreach( $_REQUEST['org_id'] AS $org_id ){
                    $results = $wpdb->get_results($wpdb->prepare("SELECT c.*,o.* FROM {$wpdb->prefix}organisations_charge AS c
                                                                  JOIN {$wpdb->prefix}organisations AS o ON o.id = c.organisation_id
                                                                  LEFT JOIN {$wpdb->prefix}xero_payments AS p ON p.invoice_id = c.invoice_identifier
                                                                  WHERE invoice_identifier != '' AND c.is_paid = 0 AND o.no_billing = 0 AND o.invoice_me = 0 AND c.organisation_id = %d AND p.invoice_id IS NULL
                                                                  GROUP BY invoice_identifier", $org_id));
                    if( $results ){
                        foreach( $results AS $result ){
                            if( isset( $_REQUEST['invoices_numbers']) && $_REQUEST['invoices_numbers'] != $result->invoice_identifier ){
                                continue;
                            }
                            $invoiceData = $xero_api->getInvoice( $result->invoice_identifier );
                            //we add only Approved invoices to Payments table
                            if( isset( $invoiceData['Invoices']['Invoice']['Status'] ) && $invoiceData['Invoices']['Invoice']['Status'] == 'AUTHORISED' ){
                                $data = array(
                                    'invoice_id'        => $result->invoice_identifier,
                                    'account_code'      => 650,
                                    'date_added'        => date( 'Y-m-d' ),
                                    'amount'            => $invoiceData['Invoices']['Invoice']['Total'],
                                    'reference'         => '',
                                    'is_reconciled'     => true,
                                    'organisation_id'   => $result->organisation_id,
                                    'payment_method_id' => $result->payment_id,
                                    'is_paid'           => false
                                );
                                $xero_payments->bind( $data );
                                $xero_payments->save();
                                $paymentsCounter++;
                            } else if( isset( $invoiceData['Invoices']['Invoice']['Status'] ) && $invoiceData['Invoices']['Invoice']['Status'] == 'DRAFT' ){
                                $nonApprovedPaymentsCounter++;
                            }
                        }
                    }
                }
            }
            addMessage('Added: '.$paymentsCounter.' payments</br>Not Approved Invoices: '.$nonApprovedPaymentsCounter, 'success');
            wp_redirect('admin.php?page=manage-xero-payments');
            exit;
        } else if( $action == 'mark_charges' ){
            $wpdb->query("UPDATE {$wpdb->prefix}organisations_charge SET is_paid = 1 WHERE invoice_identifier IN ( SELECT invoice_id FROM {$wpdb->prefix}xero_payments WHERE is_paid = 1 )");
            addMessage('Done', 'success');
            wp_redirect('admin.php?page=manage-xero-payments');
            exit;
        } else if( $action == 'update_paid_invoices' ){
            $xero = new CT_Xero();
            $results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}xero_payments WHERE is_paid = 1 AND payment_id = ''", ARRAY_A );
            if( $results ){
                foreach( $results AS $result ){
                    $payment = $xero->createPayment( array(
                        'InvoiceID' => $result['invoice_id'],
                        'Amount' => $result['amount'],
                        'Date'   => date('Y-m-d')
                    ));
                    if( isset( $payment['Payments']['Payment']['PaymentID']) ){
                        $wpdb->update("{$wpdb->prefix}xero_payments",
                            array(
                                'payment_id' => $payment['Payments']['Payment']['PaymentID'],
                                'is_paid'    => 1,
                                'date_paid'  => date('Y-m-d H:i:s')
                            ),
                            array( 'invoice_id' => $result['invoice_id'] ),
                            array( '%s', '%d', '%s' ),
                            array( '%s' )
                        );
                        $wpdb->update("{$wpdb->prefix}organisations_charge",
                            array(
                                'is_paid'    => 1
                            ),
                            array( 'invoice_identifier' => $result['invoice_id'] ),
                            array( '%d' ),
                            array( '%s' )
                        );
                    }
                }
            }
            addMessage('Done', 'success');
            wp_redirect('admin.php?page=manage-xero-payments');
            exit;
        }
    }
    
}