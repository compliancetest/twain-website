<?php
/**
* Manage Xero Payments
*/
require_once (THE_FUNCTION . "/organisations-payments/organisations-payments-list-table.php");
require_once (THE_FUNCTION . "/organisations-payments/class.payments.php");

//Create Menus
add_action("admin_menu", "ct_add_manage_xero_payments_menu");
function ct_add_manage_xero_payments_menu()
{
    add_menu_page("Manage Organisation Payments", "Organisation Payments", "manage_options", "manage-organisations-payments", "ct_show_xero_payments_list");
}

function ct_show_xero_payments_list()
{
    $listTable = new CT_Organisations_Payments_Table();
    $listTable->prepare_items();
    ?>
    <div class="wrap">
        <h2>Organisations Payments</h2>
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
        <form id="query_unpaid_invoices_form" name="query_unpaid_invoices_form" action="<?php echo admin_url()?>admin.php?page=manage-organisations-payments&org-action=<?php echo wp_create_nonce('query_unpaid_invoices')?>" method="post" style="display: none;">
            <table class="widefat" style="width: auto;">
                <?php  $chargesObject = new CT_Charge();?>
                <tr>
                    <th>--All--</th>
                    <td><input type="checkbox" name="org_id[]" value="all" class="org_id"/></td>
                </tr>
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
        <a href="<?php echo admin_url()?>admin.php?page=admin-actions&admin-action=<?php echo wp_create_nonce('pay-cc-invoices')?>">Pay CC Invoices via eWay</a>
        <div class="clear"></div>
        <a href="<?php echo admin_url()?>admin.php?page=manage-organisations-payments&org-action=<?php echo wp_create_nonce('update_paid_invoices')?>">Update Paid CC Invoices in Xero</a>
        <div class="clear"></div>
        <a href="<?php echo admin_url()?>admin.php?page=manage-organisations-payments&org-action=<?php echo wp_create_nonce('mark_charges')?>">Mark Charges As paid</a>
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
                    if( jQuery.inArray( 'all', searchIDs ) !== -1 ){
                        jQuery('.org_id').attr( 'checked', false );
                        jQuery( this ).attr( 'checked', true );
                        jQuery('#invoices_numbers').find('option')
                            .remove()
                            .end();
                    }
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
    $results = $wpdb->get_results($wpdb->prepare("SELECT c.invoice_number AS id FROM {$wpdb->prefix}organisations_charge AS c
                                                                  JOIN {$wpdb->prefix}organisations AS o ON o.id = c.organisation_id
                                                                  LEFT JOIN {$wpdb->prefix}organisations_payments AS p ON p.invoice_number = c.invoice_number
                                                                  WHERE c.invoice_number != '' AND c.is_paid = 0 AND o.no_billing = 0 AND o.invoice_me = 0 AND c.organisation_id IN( %d ) AND p.invoice_number IS NULL
                                                                  GROUP BY c.invoice_number", implode(',', $_POST['org_id'])));
    exit( json_encode( $results ) );
}
add_action("admin_init", "ct_process_xero_payment_admin_actions");
function ct_process_xero_payment_admin_actions()
{
    global $wpdb;
    
    if(!is_super_admin())
        return;
    
    if(isset($_REQUEST['org-action']))
    {
        $action = $_REQUEST['org-action'];
        if( wp_verify_nonce($action, 'query_unpaid_invoices') ){
            $paymentsCounter = $nonApprovedPaymentsCounter = 0;
            $xero_payments = new CT_Payments();
            $xero_api = new CT_Xero();
            if( ! isset( $_REQUEST['org_id'] ) || empty( $_REQUEST['org_id'] ) || $_REQUEST['org_id'][0] == 'all' ){
                $chargesObject = new CT_Charge();
                $_REQUEST['org_id'] = array();
                foreach( $chargesObject->getOrganisationsList( true ) AS $org ){
                    array_push( $_REQUEST['org_id'], $org->organisation_id );
                }
            }
            if( isset( $_REQUEST['org_id'] ) ){
                foreach( $_REQUEST['org_id'] AS $org_id ){
                    $results = $wpdb->get_results($wpdb->prepare("SELECT c.*,o.* FROM {$wpdb->prefix}organisations_charge AS c
                                                                  JOIN {$wpdb->prefix}organisations AS o ON o.id = c.organisation_id
                                                                  JOIN {$wpdb->prefix}organisations_payment_methods AS pm ON pm.id = c.payment_id
                                                                  LEFT JOIN {$wpdb->prefix}organisations_payments AS p ON p.invoice_number = c.invoice_number
                                                                  WHERE c.invoice_number != '' AND c.is_paid = 0 AND o.no_billing = 0 AND pm.invoice_me = 0 AND c.organisation_id = %d AND p.invoice_number IS NULL
                                                                  GROUP BY c.invoice_number", $org_id));
                    if( $results ){
                        foreach( $results AS $result ){
                            if( isset( $_REQUEST['invoices_numbers']) && $_REQUEST['invoices_numbers'] != $result->invoice_number ){
                                continue;
                            }
                            $invoiceData = $xero_api->getInvoice( $result->invoice_number );
                            //we add only Approved invoices to Payments table
                            if( isset( $invoiceData['Invoices']['Invoice']['Status'] ) && $invoiceData['Invoices']['Invoice']['Status'] == 'AUTHORISED' ){
                                $data = array(
                                    'invoice_number'    => $result->invoice_number,
                                    'account_code'      => 650,
                                    'date_added'        => gmdate( 'Y-m-d H:i:s' ),
                                    'amount'            => $invoiceData['Invoices']['Invoice']['Total'],
                                    'reference'         => '',
                                    'organisation_id'   => $result->organisation_id,
                                    'payment_method_id' => $result->payment_id,
                                    'is_paid'           => false
                                );
                                $xero_payments->bind( $data );
                                $xero_payments->save();
                                $paymentsCounter++;
                            } else if( isset( $invoiceData['Invoices']['Invoice']['Status'] ) && $invoiceData['Invoices']['Invoice']['Status'] == 'DRAFT' ){
                                $nonApprovedPaymentsCounter++;
                            } else if( isset( $invoiceData['Invoices']['Invoice']['Status'] ) && $invoiceData['Invoices']['Invoice']['Status'] == 'PAID' ){
                                $pid = isset( $invoiceData['Invoices']['Invoice']['Payments']['Payment']['PaymentID'] ) ? $invoiceData['Invoices']['Invoice']['Payments']['Payment']['PaymentID'] : 'No payment ID';
                                $data = array(
                                    'invoice_number'    => $result->invoice_number,
                                    'account_code'      => 650,
                                    'date_added'        => gmdate( 'Y-m-d H:i:s' ),
                                    'amount'            => $invoiceData['Invoices']['Invoice']['Total'],
                                    'reference'         => 'Paid In Xero',
                                    'organisation_id'   => $result->organisation_id,
                                    'payment_method_id' => $result->payment_id,
                                    'is_paid'           => true,
                                    'date_paid'         => date('Y-m-d H:i:s', strtotime($invoiceData['Invoices']['Invoice']['UpdatedDateUTC'])),
                                    'payment_id'        => $pid
                                );
                                $xero_payments->bind( $data );
                                $xero_payments->save();
                                $paymentsCounter++;
                            }
                        }
                    }
                }
            }
            addMessage('Added: '.$paymentsCounter.' payments</br>Not Approved Invoices: '.$nonApprovedPaymentsCounter, 'success');
            wp_redirect('admin.php?page=manage-organisations-payments');
            exit;
        } else if( wp_verify_nonce($action, 'mark_charges') ){
            $wpdb->query("UPDATE {$wpdb->prefix}organisations_charge SET is_paid = 1 WHERE invoice_number IN ( SELECT invoice_number FROM {$wpdb->prefix}organisations_payments WHERE is_paid = 1 )");
            addMessage('Done', 'success');
            wp_redirect('admin.php?page=manage-organisations-payments');
            exit;
        } else if( wp_verify_nonce($action, 'update_paid_invoices') ){
            $counter = 0;
            $results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}organisations_payments WHERE is_paid = 1 AND payment_id = ''", ARRAY_A );
            if( $results ){
                foreach( $results AS $result ){
                    $xero = new CT_Xero();
                    $payment = $xero->createPayment( array(
                        'InvoiceNumber' => $result['invoice_number'],
                        'Amount' => $result['amount'],
                        'Date'   => gmdate('Y-m-d'),
                        'Reference' => $result['reference']
                    ));
                    if( isset( $payment['Payments']['Payment']['PaymentID']) ){
                        $wpdb->update("{$wpdb->prefix}organisations_payments",
                            array(
                                'payment_id' => $payment['Payments']['Payment']['PaymentID'],
                                'is_paid'    => 1,
                                'date_paid'  => gmdate('Y-m-d H:i:s')
                            ),
                            array( 'invoice_number' => $result['invoice_number'] ),
                            array( '%s', '%d', '%s' ),
                            array( '%s' )
                        );
                        $counter++;
                    } else if( $payment === false ){
                        $invoiceData = $xero->getInvoice( $result['invoice_number'] );
                        if( isset( $invoiceData['Invoices']['Invoice']['Status'] ) && $invoiceData['Invoices']['Invoice']['Status'] == 'PAID' ){
                            $pid = isset( $invoiceData['Invoices']['Invoice']['Payments']['Payment']['PaymentID'] ) ? $invoiceData['Invoices']['Invoice']['Payments']['Payment']['PaymentID'] : 'No payment ID';
                            $wpdb->update("{$wpdb->prefix}organisations_payments",
                                array(
                                    'is_paid'    => 1,
                                    'date_paid'  => date('Y-m-d H:i:s', strtotime($invoiceData['Invoices']['Invoice']['UpdatedDateUTC'])),
                                    'payment_id' => $pid,
                                    'reference'  => 'Paid In Xero'
                                ),
                                array( 'invoice_number' => $result['invoice_number'] ),
                                array( '%d', '%s', '%s', '%s' ),
                                array( '%s' )
                            );
                            $counter++;
                        }
                    }
                }
            }
            addMessage('Number of Updated invoices: '.$counter, 'success');
            wp_redirect('admin.php?page=manage-organisations-payments');
            exit;
        }
    }
    
}