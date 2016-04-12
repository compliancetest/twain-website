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

$html_render_limit = get_option('s3_xml_max_size');

$tSubscriptions = ct_get_user_viewable_subscriptions($user->ID);

//Set Default Subscription
if (is_super_admin() || ct_is_group_admin_or_support($user_id)) {
    $tOrganisations = ct_get_user_viewable_organisations();

    $default_organisation = isset($_GET['organisation']) ? htmlspecialchars($_GET['organisation']) : 'all';

    $default_subscription = "all";
    $filterSubscription = isset($_GET['subscription']) ? htmlspecialchars($_GET['subscription']) : $default_subscription;

    if ($filterSubscription == 'all') {
        $filterOrganisation = $default_organisation;
    } else {
        foreach ($tSubscriptions as $s) {
            if ($s->id == $filterSubscription) {
                $filterOrganisation = $s->organisation_id;
                break;
            }
        }
    }
} else {
    if ($tSubscriptions)
        $default_subscription = 'my';
    else
        $default_subscription = -1;

    $filterSubscription = isset($_GET['subscription']) ? htmlspecialchars($_GET['subscription']) : $default_subscription;
}


$filterProduct = isset($_GET['product']) ? htmlspecialchars($_GET['product']) : null;
$filterSuite = isset($_GET['suite']) ? htmlspecialchars($_GET['suite']) : null;
$filterCase = isset($_GET['case']) ? htmlspecialchars($_GET['case']) : null;
$filterService = isset($_GET['serv']) ? htmlspecialchars(urldecode($_GET['serv'])) : null;
$filterAction = isset($_GET['action']) ? htmlspecialchars($_GET['action']) : null;
$filterPartyId = isset($_GET['partyid']) ? htmlspecialchars($_GET['partyid']) : null;
$filterDate = isset($_GET['date']) ? htmlspecialchars($_GET['date']) : null;
$filterCustomer = isset($_GET['customer']) ? htmlspecialchars($_GET['customer']) : null;
$filterTags = isset($_GET['tag']) && $_GET['tag'] != 'all' ? htmlspecialchars($_GET['tag']) : null;

$esb = new ManageESB();

$limit = isset($_GET['limit']) ? intval(htmlspecialchars($_GET['limit'])) : getItemsPerPage('transactions');
setItemsPerPage($limit, 'transactions');

$orderBy = isset($_GET['orderby']) ? htmlspecialchars($_GET['orderby']) : 'date';
if (!in_array($orderBy, array('product', 'case', 'suite', 'test_outcome', 'audit', 'serv', 'action', 'message', 'date', 'from')))
    $orderBy = 'product';

$order = isset($_GET['order']) ? htmlspecialchars($_GET['order']) : ($orderBy == 'date' ? 'desc' : 'asc');


$page = get_query_var('paged') ? get_query_var('paged') : 1;

$esb->prepareTransactionWhereQuery(isset($filterOrganisation) ? $filterOrganisation : null, $filterSubscription, $filterProduct, $filterSuite, $filterCase, $filterService, $filterAction, $filterPartyId, $filterDate, $filterTags);

$log_results = $wpdb->get_results($wpdb->prepare("SELECT * FROM transactions WHERE customer_id = %d", get_current_user_id()));
//$tProducts = $esb->getFilterOptionsForProduct();
//$tSuites = $esb->getFilterOptionsForSuite();
//$tCases = $esb->getFilterOptionsForCase();
//$tServices = $esb->getFilterOptionsForService();
//$tActions = $esb->getFilterOptionsForAction();
//$tPartyIDs = $esb->getFilterOptionsForPartId();
//$tTagsIDs = $esb->getFilterOptionsForTags();

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
if ($filterService) {
    $params[] = 'serv=' . urlencode($filterService);
}
if ($filterAction) {
    $params[] = 'action=' . $filterAction;
}
if ($filterPartyId) {
    $params[] = 'partyid=' . $filterPartyId;
}
if ($filterDate) {
    $params[] = 'date=' . $filterDate;
}
if ($filterCustomer) {
    $params[] = 'customer=' . $filterCustomer;
}
if (isset($filterSubscription)) {
    $params[] = 'subscription=' . $filterSubscription;
}
if (isset($filterOrganisation)) {
    $params[] = 'organisation=' . $filterOrganisation;
}
if (isset($filterTags)) {
    $params[] = 'tag=' . $filterTags;
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
            if (isset($tOrganisations)) {
                require_once(THE_FUNCTION . '/views/my-transaction-log/filters-admin.phtml');
            } else {
                require_once(THE_FUNCTION . '/views/my-transaction-log/filters-all.phtml');
            }
            ?>
        </div>
        <div class="padding10">
            <a href="/testingdetails/"
               id="trigger-message-link" class="action-btn icon-btn blue-btn expand-btn trigger-btn left"
               onclick="javascript: void(0)"><span class="p"></span><span class="t">Select Test Case</span></a>

            <a href="#" id="delete-log-link" class="action-btn delete-btn icon-btn right left5 has-tooltip"><span
                    class="p"></span><span class="simple_tooltip radius6">Delete Selected Rows<span></span></span></a>
            <a href="#" id="edit-log-link" class="action-btn edit-btn icon-btn right has-tooltip"><span
                    class="p"></span><span class="simple_tooltip radius6">Edit Selected Rows<span></span></span></a>

            <div class="clear"></div>
            <div class="space10"></div>
            <div class="grid-box table-box" id="log-result-table">
                <div class="grid-box-body">
                    <div class="thead tr">
                        <div class="td td-chk tocenter"><input type="checkbox" class="chk-all" autocomplete="off"/>
                        </div>
                        <div class="td td-product td-sortable">
                            <a href="<?php echo get_permalink() ?>?<?php echo implode("&", $params) ?>&orderby=product&order=<?php echo $orderBy == 'product' && $order == 'asc' ? 'desc' : 'asc' ?>"
                               <?php if ($orderBy == 'product'){ ?>class="<?php echo $order ?>"<?php } ?>>Product Name
                            </a>
                        </div>
                        <div class="td td-case td-sortable td-two-lines tocenter">
                            <a href="<?php echo get_permalink() ?>?<?php echo implode("&", $params) ?>&orderby=suite&order=<?php echo $orderBy == 'suite' && $order == 'asc' ? 'desc' : 'asc' ?>"
                               <?php if ($orderBy == 'suite'){ ?>class="<?php echo $order ?>"<?php } ?>>Test Suite <span
                            </a>
                            <a href="<?php echo get_permalink() ?>?<?php echo implode("&", $params) ?>&orderby=case&order=<?php echo $orderBy == 'case' && $order == 'asc' ? 'desc' : 'asc' ?>"
                               <?php if ($orderBy == 'case'){ ?>class="<?php echo $order ?>"<?php } ?>>Test Case <span
                            </a>
                        </div>
                        <div class="td td-outcome td-two-lines tocenter td-sortable">
                            <a href="<?php echo get_permalink() ?>?<?php echo implode("&", $params) ?>&orderby=test_outcome&order=<?php echo $orderBy == 'test_outcome' && $order == 'asc' ? 'desc' : 'asc' ?>"
                               <?php if ($orderBy == 'test_outcome'){ ?>class="<?php echo $order ?>"<?php } ?>>Test<br/>Outcome
                                <!--                                <span class="sort"></span>-->
                            </a>
                        </div>
                        <div class="td td-audit td-two-lines tocenter td-sortable">
                            <a href="<?php echo get_permalink() ?>?<?php echo implode("&", $params) ?>&orderby=audit&order=<?php echo $orderBy == 'audit' && $order == 'asc' ? 'desc' : 'asc' ?>"
                               <?php if ($orderBy == 'audit'){ ?>class="<?php echo $order ?>"<?php } ?>>Audit<br/>Record
                                <!--                                <span class="sort"></span>-->
                            </a>
                        </div>
                        <div
                            class="td td-convsn td-sortable tocenter td-two-lines">
                            Organisation<br>
                            Subscription Nickname<br>
                            Conversation ID<br>
                            <a href="<?php echo get_permalink() ?>?<?php echo implode("&", $params) ?>&orderby=message&order=<?php echo $orderBy == 'message' && $order == 'asc' ? 'desc' : 'asc' ?>"
                               <?php if ($orderBy == 'message'){ ?>class="<?php echo $order ?>"<?php } ?>>
                            </a>
                        </div>
                        <div class="td td-date td-sortable tocenter">
                            <a href="<?php echo get_permalink() ?>?<?php echo implode("&", $params) ?>&orderby=date&order=<?php echo $orderBy == 'date' && $order == 'asc' ? 'desc' : 'asc' ?>"
                               <?php if ($orderBy == 'date'){ ?>class="<?php echo $order ?>"<?php } ?>>Date/Time <span
                                    class="sort"></span></a>
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
                                <div class="tr">
                                    <div class="td td-chk tocenter">
                                        <!--                                        --><?php //if (\ClaimsConversations\ClaimsConversations::doesConversationExists($row->ID)): ?>
                                        <!--                                            <span class="has-tooltip">-->
                                        <!--                                            <input type="checkbox" disabled="disabled" class="field-tooltip" name="id[]"-->
                                        <!--                                                   id="id-->
                                        <?php //echo $row->ID ?><!--" value="--><?php //echo $row->ID ?><!--"/>-->
                                        <!--                                            <span class="simple_tooltip radius6"-->
                                        <!--                                                  style="top: -14px; left: -48px; width: 200px;">This transaction cannot be modified as it is included in a claim.<span></span></span>-->
                                        <!--                                        </span>-->
                                        <!--                                        --><?php //else: ?>
                                        <input type="checkbox" name="id[]" id="id<?php echo $row->id ?>"
                                               value="<?php echo $row->id ?>"/>
                                        <!--                                        --><?php //endif; ?>
                                    </div>
                                    <div class="td td-product">
                                        <a href="#" class="view-messages-link has-tooltip">
                                            <span class="simple_tooltip radius6" style="top: -14px; left: -12px;">Show message details<span></span></span>
                                        </a>
                                        <a href="<?php echo get_permalink($row->product_id) ?>"><?php echo get_post_meta($row->product_id, 'product_name', true) ?></a>
                                    </div>
                                    <div class="td td-case tocenter">
                                        <a href="<?php echo get_permalink($row->test_suite_id) ?>"><?php echo cp_wrap(get_the_title($row->test_suite_id), 25) ?></a>
                                        </br>
                                        <a href="<?php echo get_permalink($row->test_case_id) ?>"><?php echo cp_wrap(get_the_title($row->test_case_id), 22) ?></a>

                                    </div>
                                    <div class="td td-outcome">
                                        <a href="#" class="view-messages-link has-tooltip">
                                            <span class="simple_tooltip radius6" style="top: -14px; left: -12px;">Show message details<span></span></span>
                                        </a>
                                        <?php $status = ($row->status == '1' ? 'success' : 'error'); ?>
                                        <span
                                            class="status-<?php echo $status; ?>"><?php echo strtoupper($status); ?></span>
                                        <br/>
                                    </div>
                                    <div
                                        class="td td-audit tocenter"><?php echo !$row->audit_record ? "No" : "Yes" ?></div>
                                    <div
                                        class="td td-convsn tocenter td-two-lines">
                                        <a href="javascript:void(0)">
                                            <?php
                                            $organisation = ct_get_organisation_by_subscription_id($row->subscription_id);
                                            echo $organisation ? $organisation->organisation_name : ' - ';
                                            echo '<br>';
                                            $subscription = ct_get_organisation_subscription_by_id($row->subscription_id);
                                            echo $subscription ? $subscription->nickname : ' - ';
                                            echo '<br>';
                                            if (strlen($row->execution_id) > 38) {
                                                echo '<span title="' . $row->execution_id . '">' . substr($row->execution_id, 0, 15) . "....." . substr($row->execution_id, -15) . '</span>';
                                            } else {
                                                echo $row->execution_id;
                                            }
                                            ?>
                                        </a>
                                        <input type="text" value="<?php echo $row->execution_id; ?>"
                                               readonly="readonly">
                                    </div>
                                    <div class="td td-date tocenter">
                                        <?php echo formatDate($row->updated_at, 'Y-m-d H:i:s') ?><br/>
                                    </div>
                                    <div class="clear"></div>

                                    <?php $logs = $wpdb->get_results($wpdb->prepare("SELECT * FROM transactions_logs WHERE transaction_id = %s ORDER by execution_order", $row->id)); ?>
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
                                                    <div class="td td-message-view td-two-lines" style="width: 8%;">TWAIN Session
                                                        ID
                                                    </div>
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
                                                                    <?php echo implode('<br>', json_decode($message->test_step));?>
                                                                </div>
                                                                <div class="td td-service td-two-lines tocenter" style="width: 36%;">
                                                                    <?php echo implode(' / ', json_decode($message->operation_triplet, true));?> </br>
                                                                    <?php if($message->return_code == 'TWRC_ENDOFLIST'):?>
                                                                        <span><?php echo $message->return_code;?></span>
                                                                    <?php elseif($message->return_code == 'TWRC_SUCCESS'):?>
                                                                        <span style="color: green;"><?php echo $message->return_code;?></span>
                                                                    <?php else:?>
                                                                        <span style="color: red;"><?php echo $message->return_code;?></span>
                                                                    <?php endif;?>
                                                                </div>
                                                                <div class="td td-message-date" style="width: 4%;"><?php echo $message->session_state;?></div>
                                                                <div class="td td-message-part tocenter" style="width: 7%;">
                                                                    <?php if(!empty($message->log_output)):?>
                                                                        <a href="/testingdetails/<?php echo $message->id;?>/output" class="s3output">View</a>
                                                                    <?php endif;?>
                                                                </div>
                                                                <div class="td td-message-view" style="width: 7%;"><?php echo $message->updated_at;?></div>
                                                                <div class="td td-message-view" style="width: 8%;"><?php echo $message->twain_session_id;?></div>
                                                                <div class="td td-service td-two-lines tocenter" style="width: 9%;">
                                                                    -
                                                                </div>
                                                                <div class="td td-service td-two-lines tocenter" style="width: 9%;">
                                                                    -
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
            <div class="space9"></div>
            <?php if ($log_results['total'] > 0) { ?>
                <div class="pagination-wrapper">
                    <div class="pagination-limit">
                        <form method="get" action="<?php echo get_permalink() ?>" name="pform">
                            Display #
                            <select name="limit" class="select" onchange="document.pform.submit()">
                                <option value="10" <?php echo $limit == 10 ? 'selected="selected"' : '' ?>>10</option>
                                <option value="20" <?php echo $limit == 20 ? 'selected="selected"' : '' ?>>20</option>
                                <option value="50" <?php echo $limit == 50 ? 'selected="selected"' : '' ?>>50</option>
                                <option value="100" <?php echo $limit == 100 ? 'selected="selected"' : '' ?>>100
                                </option>
                                <option value="-1" <?php echo $limit == -1 ? 'selected="selected"' : '' ?>>All</option>
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
                            <?php if ($filterService) { ?>
                                <input type="hidden" name="serv" value="<?php echo urlencode($filterService) ?>"/>
                            <?php } ?>
                            <?php if ($filterAction) { ?>
                                <input type="hidden" name="action" value="<?php echo $filterAction ?>"/>
                            <?php } ?>
                            <?php if ($filterPartyId) { ?>
                                <input type="hidden" name="partyid" value="<?php echo $filterPartyId ?>"/>
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
                            'total' => $limit > 0 ? ceil($log_results['total'] / $limit) : 1,
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
    <script type="text/javascript">

        jQuery(document).ready(function () {

            jQuery('a.html-view-error').each(function () {
                jQuery(this).click(function () {
                    return false;
                })

                var s_url = jQuery(this).attr("href");

                jQuery(this).cplightbox({
                    type: 'inline',
                    href: '#html-render-limit-error-box',
                    onStart: function () {
                        jQuery('#html-render-limit-error-box .popup-box-content a').attr("href", s_url);
                    }
                })
            })

            <?php if (isset($tOrganisations)): ?> //Has Organisation Filter
            function update_subscription_filter() {
                //Remove Old Values
                jQuery('#filterForm #subscription option').remove();

                //Selected Org Id
                var org_id = jQuery('#filterForm #organisation').val();

                jQuery('#filterForm #all_subscriptions option').each(function () {
                    if (org_id == 'all' || jQuery(this).val() == 'all' || jQuery(this).val() == 'my' || jQuery(this).attr('data-org-id') == org_id) {
                        jQuery('#filterForm #subscription').append(jQuery(this).clone());
                    }
                })

            }

            jQuery('#filterForm #organisation').change(update_subscription_filter);
            <?php endif; ?>

            fixTdHeight(jQuery('#my_transaction_log .table-box'));

            jQuery('.td-convsn a, .td-message-part a').click(function () {
//                jQuery(this).hide();
                jQuery(this).next().show();
                jQuery(this).next().click();
            });

            jQuery('.td-convsn input[type=text], .td-message-part input[type=text]').click(function () {
                jQuery(this).select();
            });

            jQuery('.td-convsn input[type=text], .td-message-part input[type=text]').blur(function () {
//                jQuery(this).hide();
                jQuery(this).prev().show();
            });

            jQuery('.td-convsn input[type=text], .td-message-part input[type=text]').keyup(function (e) {
                if (e.keyCode == 27) {
//                    jQuery(this).hide();
                    jQuery(this).prev().show();
                } else {
                    jQuery(this).select();
                    return false;
                }
            });

            //Edit Log
            jQuery('#edit-log-link').click(function () {
                var checked = jQuery('#log-result-table .tbody input[type="checkbox"]:checked').length;

                if (checked == 0) {
                    alert("Please select a row.");
                    return false;
                } else {
                    var ids = new Array();
                    jQuery('#log-result-table .tbody input[type="checkbox"]:checked').each(function () {
                        ids.push(this.value);
                    })
                    jQuery('#my_transaction_log').append('<div class="loading1"></div>');
                    jQuery('#my_transaction_log .loading1').show();

                    jQuery.ajax({
                        url: '/',
                        data: {
                            'cp-action': '<?php echo wp_create_nonce('edit-transaction-log')?>',
                            'id': ids
                        },
                        type: 'post',
                        dataType: 'html',
                        success: function (rsp) {
                            jQuery('#my_transaction_log .loading1').remove();
                            jQuery('#edit-transaction-log-box .tbody').html(rsp);
                            jQuery('#edit-transaction-log-box').showPopupBox({
                                closeWhenClickOveraly: false,
                                onClose: function () {
                                    jQuery('#edit-transaction-log-box .tbody').html("");
                                    jQuery('#edit-transaction-log-box .message').remove();
                                },
                                onLoad: function () {
                                    fixTdHeight(jQuery('#edit-transaction-log-box .table-box'));
                                    jQuery('#edit-transaction-log-box .table-box .tbody .td-suite').each(function () {
                                        if (jQuery(this).find('select').length > 0) {
                                            jQuery(this).removeClass('td-fixed');
                                        }
                                    });
                                    jQuery('#edit-transaction-log-box .table-box .tbody .td-case select').change(function () {
                                        var sids = jQuery(this).find('option:selected').attr('data-suites').split(",");
                                        var tdSuite = jQuery(this).parent().parent().find('.td-suite');
                                        if (sids.length >= 1) {
                                            tdSuite.removeClass('td-fixed');
                                            tdSuite.html('<select name="suite' + tdSuite.attr('data-id') + '" class="select"></select>');
                                            jQuery('#edit-transaction-log-box #all-suites option').each(function () {
                                                if (sids.indexOf(jQuery(this).val()) > -1) {
                                                    tdSuite.find('select').append(jQuery(this).clone());
                                                }
                                            })
                                            /*}else if(sids.length == 1){
                                             tdSuite.addClass('td-fixed');
                                             jQuery('#edit-transaction-log-box #all-suites option').each(function(){
                                             if(jQuery(this).val() == sids[0])
                                             {
                                             tdSuite.html('<a href="' + jQuery(this).attr('data-permalink') + '">' + jQuery(this).text() + '</a>')
                                             }
                                             })*/
                                        } else {
                                            tdSuite.addClass('td-fixed');
                                            tdSuite.html('');
                                        }
                                    })
                                }
                            });
                        }
                    })
                }

                return false;
            })
            jQuery('.view-message-validation-log').click(function () {
                var link = jQuery(this);
                jQuery('#my_transaction_log').append('<div class="loading1"></div>');
                jQuery('#my_transaction_log .loading1').show();
                jQuery.ajax({
                    url: '/',
                    data: {
                        'cp-action': '<?php echo wp_create_nonce('view-validation-log')?>',
                        'id': link.attr('data-id')
                    },
                    type: 'post',
                    dataType: 'html',
                    success: function (rsp) {
                        jQuery('#my_transaction_log .loading1').remove();
                        jQuery('#view-validation-log-box .tbody').html(rsp);
                        jQuery('#view-validation-log-box').showPopupBox({
                            closeWhenClickOveraly: false,
                            onClose: function () {
                                jQuery('#view-validation-log-box .tbody').html("");
                            },
                            onLoad: function () {
                                fixTdHeight(jQuery('#view-validation-log-box .table-box'));
                                jQuery('#view-validation-log-box a.html-view-error').each(function () {
                                    jQuery(this).click(function () {
                                        return false;
                                    })

                                    var s_url = jQuery(this).attr("href");

                                    jQuery(this).cplightbox({
                                        type: 'inline',
                                        href: '#html-render-limit-error-box',
                                        onStart: function () {
                                            jQuery('#html-render-limit-error-box .popup-box-content a').attr("href", s_url);
                                        }
                                    })
                                })
                            }
                        });
                    }
                })

                return false;
            })
            jQuery('.show_transaction_receipts').click(function (e) {
                e.preventDefault();
                jQuery('#view-transaction_details_box').width(1200);
                jQuery('#receipt_compliancetest').text(jQuery(this).data('ctreceipt'));

                //If the status is Receipt, display the message id, otherwise display the ResponseStatus
                if (jQuery(this).data('responcestatus') == 'Receipt') {
                    if (jQuery(this).data('location')) {
                        jQuery('#receipt_gateway').html('<a href="' + jQuery(this).data('location') + '" target="_blank">' + jQuery(this).data('gateway') + '</a>');
                    } else {
                        jQuery('#receipt_gateway').text(jQuery(this).data('gateway'));
                    }
                }
                else if (jQuery(this).data('responcestatus') == 'Timeout') {
                    jQuery('#receipt_gateway').html('<a href="' + jQuery(this).data('location') + '" target="_blank">' + jQuery(this).data('responcestatus') + '</a>');
                } else {
                    if (jQuery(this).data('location')) {
                        jQuery('#receipt_gateway').html('<a href="' + jQuery(this).data('location') + '" target="_blank">' + jQuery(this).data('responcestatus') + '</a>');
                    } else {
                        jQuery('#receipt_gateway').text(jQuery(this).data('responcestatus'));
                    }
                }
                jQuery('#view-transaction_details_box').showPopupBox({
                    closeWhenClickOveraly: false
                });
                var additional_width = jQuery('#receipt_compliancetest').width();
                if (jQuery('#receipt_gateway').width() > additional_width) additional_width = jQuery('#receipt_gateway').width();
                jQuery('#view-transaction_details_box').width(180 + additional_width);
            });

            jQuery('#editLogForm').submit(function () {
                jQuery('#edit-transaction-log-box .loading').show();
                jQuery.ajax({
                    url: "/",
                    type: 'post',
                    data: jQuery('#editLogForm').serialize(),
                    success: function (rsp) {
                        jQuery('#edit-transaction-log-box .loading').hide();
                        if (rsp == 'success') {
                            jQuery('#edit-transaction-log-box .popup-box-footer').prepend('<p class="message success">Successfully Saved!</p>');
                            document.location.reload();
                        } else
                            jQuery('#edit-transaction-log-box .popup-box-footer').prepend(rsp);


                    }
                })
                return false;
            })

            jQuery('#delete-log-link').click(function () {
                var checked = jQuery('#log-result-table .tbody input[type="checkbox"]:checked').length;
                if (checked == 0) {
                    alert("Please select a row.");
                    return false;
                } else {
                    var isAudit = false;

                    var ids = new Array();
                    jQuery('#log-result-table .tbody input[type="checkbox"]:checked').each(function () {
                        ids.push(this.value);
                        if (jQuery(this).parents('.tr').find('.td-audit').html() == 'Yes')
                            isAudit = true;
                    })
                    if (isAudit && !confirm('Are you sure you want to delete? Some of the rows you have selected are marked as audit records')) {
                        return false;
                    }

                    jQuery('#my_transaction_log').append('<div class="loading1"></div>');
                    jQuery('#my_transaction_log .loading1').show();

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
                }
            });

            jQuery('#my_transaction_log .clear-filter').click(function () {
                jQuery(this).parent().parent().find('input, select').val('');
                jQuery('#filterForm').submit();
                return false;
            })

            jQuery('.chk-all').click(function () {
                jQuery('#log-result-table .tbody input[type="checkbox"]:not(:disabled)').prop('checked', this.checked);
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
        })
    </script>
</div> <!--end content-->

<div class="popup-box" id="edit-transaction-log-box" style="display: none; width: 1000px">
    <form name="editLogForm" id="editLogForm" action="">
        <div class="popup-box-header radius6 noradiusbottom">Edit Transaction Log</div>
        <div class="popup-box-content">
            <div class="space10"></div>
            <div class="grid-box table-box">
                <div class="grid-box-body">
                    <div class="thead tr">
                        <div class="td td-product">Product Name</div>
                        <div class="td td-case tocenter">Test Case</div>
                        <div class="td td-suite tocenter">Test Suite</div>
                        <div class="td td-outcome td-two-lines tocenter">Test<br/>Outcome</div>
                        <div class="td td-audit td-two-lines tocenter">Audit<br/>Record</div>
                        <div class="td td-convsn tocenter">Conversation ID</div>
                        <div class="td td-date tocenter">Date / Time</div>
                        <!--                   <div class="td td-to tocenter">To</div>-->
                        <div class="clear"></div>
                    </div>
                    <div class="tbody">

                    </div>
                </div>
            </div>
            <div class="space10"></div>
        </div>
        <div class="popup-box-footer radius6 noradiustop">
            <a href="#make-claim-box" cp-type="inline" class="action-btn process-btn submit-btn"><span class="p"></span><span
                    class="t">Confirm</span></a>
            <a href="#make-claim-box" cp-type="inline" class="action-btn cancel-btn close-popup-btn"><span
                    class="p"></span><span class="t">Cancel</span></a>

            <div class="clear"></div>
        </div>
        <div class="loading"></div>
        <a class="close_btn"></a>
        <input type="hidden" name="cp-action" value="<?php echo wp_create_nonce('save-transaction-log') ?>"/>
    </form>
</div>

<div class="popup-box" id="view-validation-log-box" style="display: none; width: 410px">
    <div class="popup-box-header radius6 noradiusbottom">ESB validation log</div>
    <div class="popup-box-content">
        <div class="space10"></div>
        <div class="grid-box table-box">
            <div class="grid-box-body">
                <div class="thead tr">
                    <div class="td td-phase">Phase Name</div>
                    <div class="td td-status tocenter">Status</div>
                    <div class="td td-result tocenter">Result</div>
                    <div class="clear"></div>
                </div>
                <div class="tbody">

                </div>
            </div>
        </div>
        <div class="space10"></div>
    </div>
    <div class="popup-box-footer radius6 noradiustop">
        <a href="#" cp-type="inline" class="action-btn cancel-btn close-popup-btn"><span class="p"></span><span
                class="t">Close</span></a>

        <div class="clear"></div>
    </div>
    <div class="loading"></div>
    <a class="close_btn"></a>
</div>

<div class="popup-box" id="view-transaction_details_box" style="display: none; width: 410px">
    <div class="popup-box-header radius6 noradiusbottom">Receipt Identifiers</div>
    <div class="popup-box-content">
        <div class="space10"></div>
        <div class="grid-box-body">
            <div class="tbody">
                <div><span class="bold"><?php echo get_site_organisation(); ?>: </span><span
                        id="receipt_compliancetest"></span></div>
                <div class="space10"></div>
                <div><span class="bold">Gateway Network: </span><span id="receipt_gateway"></span></div>
            </div>
        </div>
        <div class="space10"></div>
    </div>
    <div class="popup-box-footer radius6 noradiustop">
        <a href="#" cp-type="inline" class="action-btn cancel-btn close-popup-btn"><span class="p"></span><span
                class="t">Close</span></a>

        <div class="clear"></div>
    </div>
    <div class="loading"></div>
    <a class="close_btn"></a>
</div>

<div class="popup-box" id="html-render-limit-error-box" style="display: none; width: 410px">
    <div class="popup-box-header radius6 noradiusbottom">HTML View Not Available</div>
    <div class="popup-box-content">
        HTML View is not available due to content size of 25 kB. Click <a href="#" target="_blank">here</a> to download
        and process locally
        <div class="space10"></div>
    </div>
    <div class="popup-box-footer radius6 noradiustop">
        <a href="#" cp-type="inline" class="action-btn cancel-btn close-popup-btn"><span class="p"></span><span
                class="t">Close</span></a>

        <div class="clear"></div>
    </div>
    <div class="loading"></div>
    <a class="close_btn"></a>
</div>
<!--<script src="--><?php //echo '/wp-content/themes/bp-child/js/jquery-ui-1.10.3.custom.js';?><!--" type="text/javascript"></script>-->
<script src="<?php echo '/wp-content/themes/bp-child/js/jquery-ui-timepicker-addon.js'; ?>"
        type="text/javascript"></script>
<?php
get_footer();
?>
