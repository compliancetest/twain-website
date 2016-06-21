<?php
/**
 * Template Name:My Transaction Log
 */

if (!function_exists('is_user_logged_in') || !is_user_logged_in()) {
    wp_redirect(home_url());
    exit;
}
global $current_user;

$userInfo = get_user_meta($current_user->ID);

$user = get_userdata($current_user->ID);
$user_status = $user->user_status;

if ($user_status == 3) {
    //Goto My Profile Page
    addMessage('Please verify your email address.', 'warning');
    wp_redirect('/my-profile');
    exit;
}

$tSubscriptions = ct_get_user_viewable_subscriptions($user->ID);

$filterSubscription = isset($_GET['subscription']) ? htmlspecialchars($_GET['subscription']) : null;

$filterProduct = isset($_GET['product']) ? htmlspecialchars($_GET['product']) : null;
$filterSuite = isset($_GET['suite']) ? htmlspecialchars($_GET['suite']) : null;
$filterCase = isset($_GET['case']) ? htmlspecialchars($_GET['case']) : null;
$filterOutcome = isset($_GET['outcome']) ? htmlspecialchars(urldecode($_GET['outcome'])) : null;
$filterAudit = isset($_GET['audit']) ? htmlspecialchars($_GET['audit']) : null;
$filterScenario = isset($_GET['scenario']) ? htmlspecialchars($_GET['scenario']) : null;
$filterDate = isset($_GET['date']) ? htmlspecialchars($_GET['date']) : null;

$orderBy = isset($_GET['orderby']) ? htmlspecialchars($_GET['orderby']) : 'updated_at';
if (!in_array($orderBy, array('product', 'case', 'suite', 'test_outcome', 'audit')))
    $orderBy = 'updated_at';

$order = isset($_GET['order']) ? htmlspecialchars($_GET['order']) : ($orderBy == 'updated_at' ? 'desc' : 'asc');

$page = get_query_var('paged') ? get_query_var('paged') : 1;

$sIds = [];
foreach($tSubscriptions as $tSubscription){
    $sIds[] = $tSubscription->id;
}
$transactionsLog = new TransactionLogs();

$selectedFilters = [
    'product_id' => $filterProduct,
    'test_case_id' => $filterCase,
    'test_suite_id' => $filterSuite,
    'outcome' => $filterOutcome,
    'audit' => $filterAudit,
    'scenario' => $filterScenario,
    'date' => $filterDate,
    'subscription_id' => $filterSubscription,
];
$transactionsLog->setWhereQuery($sIds, $selectedFilters);

$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;

$log_results = $transactionsLog->getUserTransactionLog($page, $limit);
$total = $log_results['total'];
$log_results = $log_results['results'];
$filters = $transactionsLog->getFilters($sIds);
$params = array();

$tbodyHTML = '';

if ($filterProduct) {
    $params[] = 'product=' . $filterProduct;
}
if ($filterSuite) {
    $params[] = 'suite=' . $filterSuite;
}
if ($filterCase) {
    $params[] = 'case=' . $filterCase;
}
if ($filterOutcome) {
    $params[] = 'outcome=' . $filterOutcome;
}
if ($filterAudit) {
    $params[] = 'audit=' . $filterAudit;
}
if ($filterScenario) {
    $params[] = 'message=' . $filterScenario;
}
if ($filterDate) {
    $params[] = 'date=' . $filterDate;
}
if (isset($filterSubscription)) {
    $params[] = 'subscription=' . $filterSubscription;
}

get_header();
?>
<div class="content" id="my_transaction_log">
    <div class="dashboard-tabs">
        <?php get_sidebar('dashboard'); ?>
    </div>
    <div class="container">
        <div class="filter-box column">
            <?php
//            if (isset($tOrganisations)) {
//                require_once(THE_FUNCTION . '/views/my-transaction-log/filters-admin.phtml');
//            } else {
                require_once(THE_FUNCTION . '/views/my-transaction-log/filters-all.phtml');
//            }
            ?>
        </div>
        <div class="padding10">
            <?php if (doesUserAdminInAnyCommunity(get_current_user_id())):?>
                <a href="#" data-outcome="Pass"
                   class="action-btn icon-btn green-btn expand-btn trigger-btn left trigger-message-link"
                   onclick="javascript: void(0)"><span class="p"></span><span class="t">Verify as Pass</span></a>
                 <a href="#" data-outcome="Fail"
                   class="action-btn icon-btn red-btn expand-btn trigger-btn left5 trigger-message-link"
                   onclick="javascript: void(0)"><span class="p"></span><span class="t">Verify as Fail</span></a>
                 <a href="#" data-outcome="Skip"
                   class="action-btn icon-btn grey-btn expand-btn trigger-btn left5 trigger-message-link"
                   onclick="javascript: void(0)"><span class="p"></span><span class="t">Verify as Skip</span></a>
            <?php endif;?>

            <a href="#" id="delete-log-link" class="action-btn delete-btn icon-btn right has-tooltip"><span
                    class="p"></span><span class="simple_tooltip radius6">Delete Selected Rows<span></span></span></a>
<!--            <a href="#" id="edit-log-link" class="action-btn edit-btn icon-btn right has-tooltip"><span-->
<!--                    class="p"></span><span class="simple_tooltip radius6">Edit Selected Rows<span></span></span></a>-->

            <div class="clear"></div>
            <div class="space10"></div>
            <div class="grid-box table-box" id="log-result-table">
                <div class="grid-box-body">
                    <div class="thead tr">
                        <div class="td td-chk tocenter"><input type="checkbox" class="chk-all" autocomplete="off"/>
                        </div>
                        <div class="td td-product td-sortable">
                            Product Name
                        </div>
                        <div class="td td-case td-sortable td-two-lines tocenter">
                            Test Suite<br>
                            Test Case
                        </div>
                        <div class="td td-outcome td-two-lines tocenter td-sortable">
                            Test<br/>Outcome
                        </div>
                        <div class="td td-audit td-two-lines tocenter td-sortable">
                            Audit<br/>Record
                        </div>
                        <div class="td td-convsn td-sortable tocenter td-two-lines">
                            Organisation<br>
                            Subscription Nickname<br>
                            Execution ID<br>
                        </div>
                        <div class="td td-date td-sortable tocenter td-two-lines">
                            Date<br>Time
                        </div>
                        <div class="clear"></div>
                    </div>
                    <div class="tbody">
                        <?php if (!$log_results) { ?>
                            <div class="tr">
                                <div class="td td-full">No Transaction Found.</div>
                                <div class="clear"></div>
                            </div>
                        <?php } else {
                            foreach ($log_results as $row) {
                                ?>
                                <?php $logs = $wpdb->get_results($wpdb->prepare("SELECT * FROM transactions_logs WHERE transaction_id = %s ORDER by execution_order", $row->id)); ?>
                                <div class="tr">
                                    <div class="td td-chk tocenter">
                                        <input type="checkbox" name="id[]" id="id<?php echo $row->id ?>" value="<?php echo $row->id ?>" <?php if($row->audit_record):?> disabled="disabled" <?php endif;?>/>
                                    </div>
                                    <div class="td td-product">
                                        <?php if($logs):?>
                                            <a href="#" class="view-messages-link has-tooltip">
                                                <span class="simple_tooltip radius6" style="top: -14px; left: -12px;">Show message details<span></span></span>
                                            </a>
                                        <?php endif;?>
                                        <?php if($row->product_id):?>
                                            <a href="<?php echo get_permalink($row->product_id) ?>"><?php echo get_post_meta($row->product_id, 'product_name', true) ?></a>
                                        <?php else:?>
                                            Not Assigned
                                        <?php endif;?>
                                    </div>
                                    <div class="td td-case tocenter">
                                        <?php if($row->product_id):?>
                                            <a href="<?php echo get_permalink($row->test_suite_id) ?>"><?php echo cp_wrap(get_the_title($row->test_suite_id), 25) ?></a>
                                        <?php else:?>
                                            Not Assigned
                                        <?php endif;?>
                                        </br>
                                        <?php if($row->product_id):?>
                                            <a href="<?php echo get_permalink($row->test_case_id) ?>"><?php echo cp_wrap(get_the_title($row->test_case_id), 22) ?></a>
                                        <?php else:?>
                                            Not Assigned
                                        <?php endif;?>
                                    </div>
                                    <div class="td td-outcome">
                                         <?php if($logs):?>
                                            <a href="#" class="view-messages-link has-tooltip">
                                                <span class="simple_tooltip radius6" style="top: -14px; left: -12px;">Show message details<span></span></span>
                                            </a>
                                        <?php endif;?>
                                        <?php $outcomeStatus = $wpdb->get_row($wpdb->prepare("SELECT * FROM test_outcome_statuses WHERE id = %s", $row->test_outcome_status_id));?>
                                        <?php $status = getOutcomeStatusClass($outcomeStatus->code); ?>
                                        <span
                                            class="status-<?php echo $status;?>"><?php echo $outcomeStatus ? $outcomeStatus->name : 'FAIL';?></span>
                                        <br/>
                                    </div>
                                    <div class="td td-audit tocenter">
                                        <input type="checkbox" <?php if($row->audit_record):?> checked="checked" <?php endif;?> class="change_audit_record"
                                               data-id="<?php echo $row->id;?>" <?php if($outcomeStatus->code == 'PENDING'):?>disabled="disabled"<?php endif;?>>
                                    </div>
                                    <div
                                        class="td td-convsn tocenter td-two-lines">
                                            <?php
                                            $organisation = ct_get_organisation_by_subscription_id($row->subscription_id);
                                            echo $organisation ? $organisation->organisation_name : ' - ';
                                            echo '<br>';
                                            $subscription = ct_get_organisation_subscription_by_id($row->subscription_id);
                                            echo $subscription ? $subscription->nickname : ' - ';
                                            echo '<br>';
                                            if(!empty($row->s3_link)) {
                                                echo '<a href="' . $row->s3_link . '" target="_blank">' . $row->execution_id . '</a>';
                                            } else {
                                                echo $row->execution_id;
                                            }
                                            ?>
                                        <input type="text" value="<?php echo $row->execution_id; ?>"
                                               readonly="readonly">
                                    </div>
                                    <div class="td td-date tocenter">
                                        <?php echo formatDate($row->updated_at, 'Y-m-d H:i:s') ?><br/>
                                    </div>
                                    <div class="clear"></div>

                                    <?php if ($logs) { ?>
                                        <div class="sub-table">
                                            <div class="table">
                                                <div class="thead tr">
                                                    <div class="td td-from td-two-lines tocenter" style="width: 4%;">
                                                        From</br>To
                                                    </div>

                                                    <div class="td td-service td-two-lines tocenter" style="width: 4%;">
                                                        Test </br>
                                                        Step
                                                    </div>

                                                    <div class="td td-service td-two-lines tocenter"
                                                         style="width: 36%;">Operation Triplet </br>
                                                        Return Code
                                                    </div>
                                                    <div class="td td-message-date td-two-lines" style="width: 4%;">Session State
                                                    </div>
                                                    <div class="td td-message-part tocenter td-two-lines" style="width: 7%;">Message
                                                        Data
                                                    </div>
                                                    <div class="td td-message-view td-two-lines" style="width: 7%;">Date</br>Time</div>
                                                    <div class="td td-message-view td-two-lines" style="width: 8%;">Step Outcome</div>
                                                    <div class="td td-service td-two-lines tocenter" style="width: 9%;">
                                                        Screen </br>
                                                        Capture
                                                    </div>
                                                    <div class="td td-service td-two-lines tocenter" style="width: 9%;">
                                                        Scan </br>
                                                        Result
                                                    </div>
                                                    <div class="clear"></div>
                                                </div>
                                                <div class="tbody">
                                                    <?php if ($logs): ?>
                                                        <?php foreach ($logs as $message) { ?>
                                                            <div class="tr">
                                                                <div class="td td-from td-two-lines tocenter" style="width: 4%;">
                                                                    <?php echo $message->from;?>
                                                                    </br>
                                                                    <?php echo $message->to;?>
                                                                </div>
                                                                <div class="td td-service td-two-lines tocenter" style="width: 4%;">
                                                                    <?php echo $message->test_step;?>
                                                                </div>
                                                                <div class="td td-service td-two-lines tocenter" style="width: 36%;">
                                                                    <?php echo $message->data_group .' / ' . $message->data_argument_type . ' / '. $message->messages;?> </br>
                                                                        <span style="color: <?php echo getReturnCodeColor($message->return_code);?>"><?php echo $message->return_code;?></span>
                                                                </div>
                                                                <div class="td td-message-date" style="width: 4%;"><?php if($message->session_state) echo $message->session_state;?></div>
                                                                <div class="td td-message-part tocenter" style="width: 7%;">
                                                                    <?php if(!empty($message->log_output)):?>
                                                                        <a href="/testingdetails/<?php echo $message->id;?>/output" class="s3output">View</a>
                                                                    <?php endif;?>
                                                                </div>
                                                                <div class="td td-message-view" style="width: 7%;"><?php echo $message->updated_at;?></div>
                                                                <div class="td td-message-view" style="width: 8%;">
                                                                    <?php if(empty($message->reason)):?>
                                                                        <span class="status-<?php echo getOutcomeStatusClass(strtoupper($message->step_outcome));?>"><?php echo $message->step_outcome;?></span>
                                                                    <?php else:?>
                                                                        <a href="/testingdetails/<?php echo $message->id;?>/reason" class="reason">
                                                                            <span class="status-<?php echo getOutcomeStatusClass(strtoupper($message->step_outcome));?>"><?php echo $message->step_outcome;?></span>
                                                                        </a>
                                                                    <?php endif;?>
                                                                </div>
                                                                <div class="td td-service td-two-lines tocenter" style="width: 9%;">
                                                                    -
                                                                </div>
                                                                <div class="td td-service td-two-lines tocenter" style="width: 9%;">
                                                                    <?php if ($scanImages = json_decode($message->scan_results)): ?>
                                                                        <?php foreach ($scanImages as $scanImage): ?>
                                                                            <a href="<?php echo $scanImage; ?>" target="_blank">View</a>
                                                                        <?php endforeach; ?>
                                                                    <?php else: ?>
                                                                        -
                                                                    <?php endif; ?>
                                                                </div>
                                                                <div class="clear"></div>
                                                            </div>
                                                        <?php } ?>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>

                                <?php
                            }

                        }
                        ?>
                    </div>
                </div>
            </div>
            <div class="space20"></div>
            <?php if ($total > 0) { ?>
                <div class="pagination-wrapper">
                    <div class="pagination-limit">
                        <form method="get" action="<?php echo get_permalink() ?>" name="pform">

                             Display #
                            <select name="limit" class="select" onchange="document.pform.submit()">
                                <option value="10" <?php echo $limit == 10 ? 'selected="selected"' : ''?>>10</option>
                                <option value="20" <?php echo $limit == 20 ? 'selected="selected"' : ''?>>20</option>
                                <option value="50" <?php echo $limit == 50 ? 'selected="selected"' : ''?>>50</option>
                                <option value="100" <?php echo $limit == 100 ? 'selected="selected"' : ''?>>100</option>
                                <option value="-1" <?php echo $limit == -1 ? 'selected="selected"' : ''?>>All</option>
                            </select>

                            <?php if ($filterProduct) { ?>
                                <input type="hidden" name="product" value="<?php echo $filterProduct ?>"/>
                            <?php } ?>
                            <?php if ($filterSuite) { ?>
                                <input type="hidden" name="suite" value="<?php echo $filterSuite ?>"/>
                            <?php } ?>
                            <?php if ($filterCase) { ?>
                                <input type="hidden" name="case" value="<?php echo $filterCase ?>"/>
                            <?php } ?>
                            <?php if ($filterOutcome) { ?>
                                <input type="hidden" name="serv" value="<?php echo urlencode($filterOutcome) ?>"/>
                            <?php } ?>
                            <?php if ($filterAudit) { ?>
                                <input type="hidden" name="action" value="<?php echo $filterAudit ?>"/>
                            <?php } ?>
                            <?php if ($filterScenario) { ?>
                                <input type="hidden" name="partyid" value="<?php echo $filterScenario ?>"/>
                            <?php } ?>
                            <?php if ($filterDate) { ?>
                                <input type="hidden" name="date" value="<?php echo $filterDate ?>"/>
                            <?php } ?>
                            <?php if ($filterCustomer) { ?>
                                <input type="hidden" name="customer" value="<?php echo $filterCustomer ?>"/>
                            <?php } ?>
                            <?php if ($filterTags) { ?>
                                <input type="hidden" name="tag" value="<?php echo $filterTags ?>"/>
                            <?php } ?>
                        </form>
                    </div>
                    <div class="pagination">
                        <?php

                        $args = array(
                            'base' => get_permalink() . '%_%?',
                            'format' => 'page/%#%',
                            'total' => ceil($total / $limit),
                            'current' => $page,
                            'show_all' => False,
                            'end_size' => 5,
                            'mid_size' => 5,
                            'prev_next' => True,
                            'prev_text' => __('« Previous'),
                            'next_text' => __('Next »'),
                            'type' => 'plain',
                            'add_args' => false,
                            'add_fragment' => (count($params) > 0 ? '&' : '') . implode('&', $params)
                        );
                        echo paginate_links($args);
                        ?>
                    </div>
                </div>
                <div class="space15"></div>
            <?php } ?>
        </div>
    </div>
    <div class="clear"></div>

    <div class="popup-box" id="delete-log-box" style="display: none; width: 500px">
        <div class="popup-box-header radius6 noradiusbottom">Confirm Delete Transactions</div>
        <div class="popup-box-content">
            <div class="delete_message">
                Are you sure you want delete selected transactions?
            </div>
            <div class="delete_no_messages">
                Please select a row
            </div>
        </div>
        <div class="popup-box-footer radius6 noradiustop">
            <div class="loading loading-with-text radius6">
                <div><b>DELETING TRANSACTIONS</b><span>Please wait...</span></div>
            </div>
            <a href="#" class="action-btn process-btn delete-log-confirm"><span class="p"></span><span class="t">Confirm</span></a>
            <a href="#" class="action-btn cancel-btn close-popup-btn"><span class="p"></span><span class="t">Cancel</span></a>
            <div class="clear"></div>
        </div>
        <a class="close_btn"></a>
    </div>

    <div class="popup-box" id="change_status_box" style="display: none; width: 500px">
        <div class="popup-box-header radius6 noradiusbottom">Confirm Change Outcome Status</div>
        <div class="popup-box-content">
            <div class="change_status_message">
                Are you sure you want change outcome status to "<span class="change_to_status"></span>" for selected transactions?
            </div>
            <div class="change_status_no_messages">
                Please select a row
            </div>
            <input type="hidden" value="" class="change_status_data_type">
        </div>
        <div class="popup-box-footer radius6 noradiustop">
            <div class="loading loading-with-text radius6">
                <div><b>UPDATING TRANSACTIONS</b><span>Please wait...</span></div>
            </div>
            <a href="#" class="action-btn process-btn change_status_log-confirm"><span class="p"></span><span class="t">Confirm</span></a>
            <a href="#" class="action-btn cancel-btn close-popup-btn"><span class="p"></span><span class="t">Cancel</span></a>
            <div class="clear"></div>
        </div>
        <a class="close_btn"></a>
    </div>

    <script type="text/javascript">

        jQuery(document).ready(function () {

            fixTdHeight(jQuery('#my_transaction_log .table-box'));

            jQuery('#delete-log-link').cplightbox({
                type: 'inline',
                href: '#delete-log-box',
                onStart: function(){
                    if(jQuery('#log-result-table .tbody input[type="checkbox"]:checked').length < 1){
                        jQuery('.delete_no_messages').show();
                        jQuery('.delete_message').hide();
                        jQuery('.delete-log-confirm').hide();
                    } else {
                        jQuery('.delete_no_messages').hide();
                        jQuery('.delete_message').show();
                        jQuery('.delete-log-confirm').show();
                    }
                }
            });

            jQuery('.trigger-message-link').click(function(){
                jQuery('.change_status_data_type').val(jQuery(this).attr('data-outcome'));
                jQuery('.change_to_status').text(jQuery(this).attr('data-outcome'));
            });

            jQuery('.trigger-message-link').cplightbox({
                type: 'inline',
                href: '#change_status_box',
                onStart: function(){
                    if(jQuery('#log-result-table .tbody input[type="checkbox"]:checked').length < 1){
                        jQuery('.change_status_no_messages').show();
                        jQuery('.change_status_message').hide();
                        jQuery('.change_status_log-confirm').hide();
                    } else {
                        jQuery('.change_status_no_messages').hide();
                        jQuery('.change_status_message').show();
                        jQuery('.change_status_log-confirm').show();
                    }
                }
            });

            jQuery('.change_status_log-confirm').click(function () {
                var ids = new Array();
                jQuery('#log-result-table .tbody input[type="checkbox"]:checked').each(function () {
                    ids.push(this.value);
                })

                jQuery('#change_status_box .loading').show();

                jQuery.ajax({
                    url: '/',
                    data: {
                        'cp-action': '<?php echo wp_create_nonce('change-transaction-log')?>',
                        'id': ids,
                        'outcome-code': jQuery('.change_status_data_type').val()
                    },
                    type: 'post',
                    dataType: 'html',
                    success: function (rsp) {
                        document.location.reload();

                    }
                })
                return false;
            });

            jQuery('.delete-log-confirm').click(function () {
                var ids = new Array();
                jQuery('#log-result-table .tbody input[type="checkbox"]:checked').each(function () {
                    ids.push(this.value);
                })

                jQuery('#delete-log-box .loading').show();

                jQuery.ajax({
                    url: '/',
                    data: {
                        'cp-action': '<?php echo wp_create_nonce('delete-transaction-log')?>',
                        'id': ids
                    },
                    type: 'post',
                    dataType: 'html',
                    success: function (rsp) {
                        document.location.reload();

                    }
                })
                return false;
            });

            jQuery('#my_transaction_log .clear-filter').click(function () {
                jQuery(this).parent().parent().find('input, select').val('');
                jQuery('#filterForm').submit();
                return false;
            })

            jQuery('.chk-all').click(function () {
                jQuery('#log-result-table .tbody input[type="checkbox"]:not(:disabled):not(.change_audit_record)').prop('checked', this.checked);
            })
            jQuery('#log-result-table .view-messages-link').click(function () {
                jQuery(this).parents('.tr').find('.sub-table').animate({'height': 'toggle'});
                jQuery(this).parents('.tr').find('.view-messages-link').toggleClass('expanded');
                if (!jQuery(this).parents('.tr').find('.view-messages-link').hasClass('expanded')) {
                    jQuery(this).parents('.tr').find('.view-messages-link .simple_tooltip').text('Show message details');
                } else {
                    jQuery(this).parents('.tr').find('.view-messages-link .simple_tooltip').text('Hide message details');
                }
                return false;
            })

            jQuery('#log-result-table .change_audit_record').on('change', function () {
                var auditRecord = jQuery(this);
                jQuery.ajax({
                    url: '/transactions/' + jQuery(this).attr('data-id') + '/updateauditrecord',
                    type: 'post',
                    data: {
                        'audit_record' : jQuery(this).is(':checked')
                    },
                    success: function(){
                        var tableRow = auditRecord.closest('div.tr');
                        if (!auditRecord.is(':checked')) {
                            tableRow.find('.td-chk input').removeAttr('disabled');
                        } else {
                            tableRow.find('.td-chk input').attr('disabled', 'disabled').removeAttr('checked');
                        }
                    }
                });
            })
        })
    </script>
</div> <!--end content-->

<script src="<?php echo '/wp-content/themes/bp-child/js/jquery-ui-timepicker-addon.js'; ?>" type="text/javascript"></script>

<?php get_footer(); ?>
