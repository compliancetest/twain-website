<?php
/**
 * Management Site Emails
 */

$ct_email_templates = array(
    'User Section' => array(
        array(
            'menu' => 'User Registered',
            'title' => 'User Registered',
            'shortcodes' => '[name], [username], [email], [website_url], [env], [password], [link], [organisation], [site_title], [site_org]',
            'fields' => array(
                array(
                    'title' => 'For User',
                    'id' => 'new_user'
                ),
                array(
                    'title' => 'For Admin',
                    'id' => 'new_user_admin',
                )
            )
        ),
        array(
            'menu' => 'User Registered after invitation',
            'title' => 'User Registered after invitation',
            'shortcodes' => '[name], [username], [email], [website_url], [env], [link], [organisation], [site_title], [site_org]',
            'fields' => array(
                array(
                    'title' => 'For User',
                    'id' => 'new_user_after_invitation'
                ),
                array(
                    'title' => 'For Admin',
                    'id' => 'new_user_after_invitation_admin',
                )
            )
        ),
        array(
            'menu' => 'User Verification Success',
            'title' => 'User Verification Success',
            'shortcodes' => '[name], [email], [website_url], [env], [username], [site_title], [site_org]',
            'fields' => array(
                array(
                    'title' => 'For User',
                    'id' => 'user_verify_success'
                ),
                array(
                    'title' => 'For Admin',
                    'id' => 'user_verify_success_admin',
                )
            )
        ),
        array(
            'menu' => 'Re-send Verification Email',
            'title' => 'Re-send Verification Email',
            'shortcodes' => '[name],[username], [email], [website_url], [env], [link], [site_title], [site_org]',
            'fields' => array(
                array(
                    'title' => 'For User',
                    'id' => 'verify'
                )
            )
        ),
        array(
            'menu' => 'Email Address Changed',
            'title' => 'Email Address Changed',
            'shortcodes' => '[name], [username], [email], [website_url], [env], [link], [site_title], [site_org]',
            'fields' => array(
                array(
                    'title' => 'For User',
                    'id' => 'email_changed'
                ),
                array(
                    'title' => 'For Admin',
                    'id' => 'email_changed_admin',
                )
            )
        ),
        array(
            'menu' => 'Changed Email Address<br/> Verification Success',
            'title' => 'Changed Email Address Verification Success',
            'shortcodes' => '[name], [email], [website_url], [env], [username], [site_title], [site_org]',
            'fields' => array(
                array(
                    'title' => 'For User',
                    'id' => 'changed_email_verify_success'
                ),
                array(
                    'title' => 'For Admin',
                    'id' => 'changed_email_verify_success',
                )
            )
        ),
        array(
            'menu' => 'Forgot Password',
            'title' => 'Forgot Password',
            'shortcodes' => '[name], [email], [website_url], [env], [username], [link], [site_title], [site_org]',
            'fields' => array(
                array(
                    'title' => 'For User',
                    'id' => 'forgot_password'
                )
            )
        ),
        array(
            'menu' => 'Password Changed',
            'title' => 'Password Changed',
            'shortcodes' => '[name], [email], [website_url], [env], [username], [site_title], [site_org]',
            'fields' => array(
                array(
                    'title' => 'For User',
                    'id' => 'password_changed'
                ),
                array(
                    'title' => 'For Admin',
                    'id' => 'password_changed_admin',
                )
            )
        )
    ),
    'Organisation Section' => array(
        array(
            'menu' => 'Organisation Record Created',
            'title' => 'Organisation Record Created',
            'shortcodes' => '[requester_name], [requester_email], [organisation], [organisation_website], [organisation_description], [organisation_abn], [website_url], [env], [site_title], [site_org]',
            'fields' => array(
                array(
                    'title' => 'For User',
                    'id' => 'send_organisation_signup_request_to_user'
                ),
                array(
                    'title' => 'For Admin',
                    'id' => 'send_organisation_signup_request_to_admin'
                )
            )
        ),
        array(
            'menu' => 'Request a Subscription<br />to Organisation Admin',
            'title' => 'Send a Request to Organisation Admin',
            'shortcodes' => '[requester_name], [requester_email], [website_url], [env], [suite_name], [suite_url], [admin_name], [admin_email], [organisation_name], [site_title], [site_org]',
            'fields' => array(
                array(
                    'title' => 'For Organisation Admin',
                    'id' => 'request_subscription_to_admin'
                )
            )
        ),

    ),
    'Subscription Section' => array(
        array(
            'menu' => 'Organisation Subscription<br /> Purchased',
            'title' => 'Organisation Subscription Purchased',
            'shortcodes' => '[name], [email], [website_url], [env], [suite_name], [paid_amount],[community_url], [site_title], [site_org]',
            'fields' => array(
                array(
                    'title' => 'For User',
                    'id' => 'purchase_subscription'
                ),
                array(
                    'title' => 'For Admin',
                    'id' => 'purchase_subscription_admin',
                )
            )
        ),
        array(
            'menu' => 'Unsubscribe Subscription',
            'title' => 'Unsubscribe Subscription',
            'shortcodes' => '[name], [email], [website_url], [env], [suite_name], [site_title], [site_org]',
            'fields' => array(
                array(
                    'title' => 'For User',
                    'id' => 'unsubscribing'
                ),
                array(
                    'title' => 'For Admin',
                    'id' => 'unsubscribing_admin',
                )
            )
        ),
        array(
            'menu' => 'Organisation Subscription<br /> Cancelled',
            'title' => 'Organisation Subscription Cancelled',
            'shortcodes' => '[name], [email], [website_url], [env], [suite_name], [site_title], [site_org]',
            'fields' => array(
                array(
                    'title' => 'For User',
                    'id' => 'cancel_subscription'
                ),
                array(
                    'title' => 'For Admin',
                    'id' => 'cancel_subscription_admin',
                )
            )
        ),
        array(
            'menu' => 'Allocate Subscription to User',
            'title' => 'Allocate Subscription to User',
            'shortcodes' => '[name], [email], [website_url], [env], [suite_name], [community_url], [nickname], [organisation], [site_title], [site_org]',
            'fields' => array(
                array(
                    'title' => 'For User',
                    'id' => 'allocate_subscription_to_user'
                ),
                array(
                    'title' => 'For Admin',
                    'id' => 'allocate_subscription_to_user_admin',
                )
            )
        ),
        array(
            'menu' => 'Payment Processing Problem',
            'title' => 'Payment Processing Problem',
            'shortcodes' => '[name], [email], [website_url], [env], [suite_name], [monthly_fee], [method_nickname], [site_title], [site_org]',
            'fields' => array(
                array(
                    'title' => 'For User',
                    'id' => 'payment_processing_problem'
                ),
                array(
                    'title' => 'For Admin',
                    'id' => 'payment_processing_problem_admin',
                )
            )
        )
    ),
    'Membership Section' => array(
        array(
            'menu' => 'Membership Request Received',
            'title' => 'Membership Request Received',
            'shortcodes' => '[name], [email], [website_url], [env], [username], [community], [community_url], [organisation], [site_title], [site_org]',
            'fields' => array(
                array(
                    'title' => 'For Admin',
                    'id' => 'membership_request_received_admin',
                )
            )
        ),
        array(
            'menu' => 'Membership Request Approved',
            'title' => 'Membership Request Approved',
            'shortcodes' => '[name], [email], [website_url], [env], [username], [community], [community_url], [settings_link], [site_title], [site_org]',
            'fields' => array(
                array(
                    'title' => 'For User',
                    'id' => 'membership_request_approved',
                ),
                array(
                    'title' => 'For Admin',
                    'id' => 'membership_request_approved_admin',
                )

            )
        ),
        array(
            'menu' => 'Existing Member Invited',
            'title' => 'Existing Member Invited',
            'shortcodes' => '[name], [email], [website_url], [env], [username], [community], [community_url], [settings_link], [site_title], [site_org]',
            'fields' => array(
                array(
                    'title' => 'For User',
                    'id' => 'membership_existing_member_invited',
                ),
                array(
                    'title' => 'For Admin',
                    'id' => 'membership_existing_member_invited_admin',
                )

            )
        ),
        array(
            'menu' => 'User Invited',
            'title' => 'User Invited',
            'shortcodes' => '[email], [website_url], [env], [name], [community], [community_url]',
            'fields' => array(
                array(
                    'title' => 'For User',
                    'id' => 'membership_member_invited',
                ),
                array(
                    'title' => 'For Admin',
                    'id' => 'membership_member_invited_admin',
                )

            )
        ),
        array(
            'menu' => 'User Registered and Invited',
            'title' => 'User Registered and Invited',
            'shortcodes' => '[email], [password], [website_url], [env], [username], [community], [community_url], [settings_link], [site_title], [site_org]',
            'fields' => array(
                array(
                    'title' => 'For User',
                    'id' => 'membership_member_invited_registered',
                ),
                array(
                    'title' => 'For Admin',
                    'id' => 'membership_member_invited_admin_registered',
                )

            )
        ),
        array(
            'menu' => 'Membership Request Rejected',
            'title' => 'Membership Request Rejected',
            'shortcodes' => '[name], [email], [website_url], [env], [username], [community], [community_url], [site_title], [site_org]',
            'fields' => array(
                array(
                    'title' => 'For User',
                    'id' => 'membership_request_rejected',
                ),
                array(
                    'title' => 'For Admin',
                    'id' => 'membership_request_rejected_admin',
                )

            )
        ),
        array(
            'menu' => 'Member Leave Community',
            'title' => 'Member Leave Community',
            'shortcodes' => '[name], [email], [website_url], [env], [community], [community_url], [site_title], [site_org]',
            'fields' => array(
                array(
                    'title' => 'For Admin',
                    'id' => 'member_leave_community_admin',
                )

            )
        ),
        array(
            'menu' => 'Member Promoted',
            'title' => 'Member Promoted',
            'shortcodes' => '[name], [email], [website_url], [env], [username], [community], [community_url], [settings_link], [member_type], [site_title], [site_org]',
            'fields' => array(
                array(
                    'title' => 'For User',
                    'id' => 'member_promoted',
                ),
                array(
                    'title' => 'For Admin',
                    'id' => 'member_promoted_admin',
                ),

            )
        ),
        array(
            'menu' => 'Remove Member',
            'title' => 'Remove Member From Community',
            'shortcodes' => '[name], [email], [website_url], [env], [username], [community], [community_url], [site_title], [site_org]',
            'fields' => array(
                array(
                    'title' => 'For User',
                    'id' => 'remove_member',
                ),
                array(
                    'title' => 'For Admin',
                    'id' => 'remove_member_admin',
                )
            )
        ),

    ),
    'Suite & Case Section' => array(
        array(
            'menu' => 'Test Suite Changed',
            'title' => 'Test Suite Changed',
            'shortcodes' => '[name], [website_url], [env], [community], [community_url], [suite_name], [suite_url], [editor_name], [site_title], [site_org]',
            'fields' => array(
                array(
                    'title' => 'For User',
                    'id' => 'suite_changed',
                )
            )
        ),
        array(
            'menu' => 'Test Case Changed',
            'title' => 'Test Case Changed',
            'shortcodes' => '[name], [website_url], [env], [suite_name], [suite_url], [case_name], [case_url], [editor_name], [site_title], [site_org]',
            'fields' => array(
                array(
                    'title' => 'For User',
                    'id' => 'case_changed',
                )
            )
        ),
    ),
    'Ticket Section' => array(
        array(
            'menu' => 'Ticket Created',
            'title' => 'Ticket Created',
            'shortcodes' => '[website_url], [env], [customer_name], [customer_email]<br />
                                [ticket_id], [ticket_title], [ticket_url], [ticket_type], [ticket_status], [ticket_priority], [ticket_content] <br />
                                [time_to_pay], [time_to_response], [time_to_resolve], [hourly_price]<small>($/hr)</small>, [ticket_total_price], [site_title], [site_org]',
            'fields' => array(
                array(
                    'title' => 'For User',
                    'id' => 'ticket_created',
                ),
                array(
                    'title' => 'For Support',
                    'id' => 'ticket_created_support',
                ),
                array(
                    'title' => 'For Admin',
                    'id' => 'ticket_created_admin',
                ),

            )
        ),
        array(
            'menu' => 'Ticket Updated',
            'title' => 'Ticket Updated',
            'shortcodes' => '[website_url], [env], [customer_name], [customer_email], [support_name], [support_email]<br />
                                [ticket_id], [ticket_title], [ticket_url], [ticket_type], [ticket_status], [ticket_priority], [ticket_content], [message]<small>(New Message)</small> <br />
                                [time_to_pay], [time_to_response], [time_to_resolve], [hourly_price]<small>($/hr)</small>, [ticket_total_price], [site_title], [site_org]',
            'fields' => array(
                array(
                    'title' => 'For User',
                    'id' => 'ticket_updated',
                ),
                array(
                    'title' => 'For Support',
                    'id' => 'ticket_updated_support',
                ),
                array(
                    'title' => 'For Admin',
                    'id' => 'ticket_updated_admin',
                ),

            )
        ),
        array(
            'menu' => 'Ticket Started',
            'title' => 'Ticket Started',
            'shortcodes' => '[website_url], [env], [customer_name], [customer_email], [support_name], [support_email]<br />
                                [ticket_id], [ticket_title], [ticket_url], [ticket_type], [ticket_status], [ticket_priority], [ticket_content], [message]<small>(New Message)</small> <br />
                                [time_to_pay], [time_to_response], [time_to_resolve], [hourly_price]<small>($/hr)</small>, [ticket_total_price], [site_title], [site_org]',
            'fields' => array(
                array(
                    'title' => 'For User',
                    'id' => 'ticket_started',
                ),
                array(
                    'title' => 'For Support',
                    'id' => 'ticket_started_support',
                ),
                array(
                    'title' => 'For Admin',
                    'id' => 'ticket_started_admin',
                ),

            )
        ),
        array(
            'menu' => 'Ticket Resolved',
            'title' => 'Ticket Resolved',
            'shortcodes' => '[website_url], [env], [customer_name], [customer_email], [support_name], [support_email]<br />
                                [ticket_id], [ticket_title], [ticket_url], [ticket_type], [ticket_status], [ticket_priority], [ticket_content], [message]<small>(New Message)</small> <br />
                                [time_to_pay], [time_to_response], [time_to_resolve], [hourly_price]<small>($/hr)</small>, [ticket_total_price], [site_title], [site_org]',
            'fields' => array(
                array(
                    'title' => 'For User',
                    'id' => 'ticket_solved',
                ),
                array(
                    'title' => 'For Support',
                    'id' => 'ticket_solved_support',
                ),
                array(
                    'title' => 'For Admin',
                    'id' => 'ticket_solved_admin',
                ),

            )
        ),
        array(
            'menu' => 'Ticket Closed',
            'title' => 'Ticket Closed',
            'shortcodes' => '[website_url], [env], [customer_name], [customer_email], [support_name], [support_email]<br />
                                [ticket_id], [ticket_title], [ticket_url], [ticket_type], [ticket_status], [ticket_priority], [ticket_content], [message]<small>(New Message)</small> <br />
                                [time_to_pay], [time_to_response], [time_to_resolve], [hourly_price]<small>($/hr)</small>, [ticket_total_price], [site_title], [site_org]',
            'fields' => array(
                array(
                    'title' => 'For User',
                    'id' => 'ticket_closed',
                ),
                array(
                    'title' => 'For Support',
                    'id' => 'ticket_closed_support',
                ),
                array(
                    'title' => 'For Admin',
                    'id' => 'ticket_closed_admin',
                ),

            )
        )/*,
        array(            
            'menu'          => 'Payment Processed<br />With Credit Card',            
            'title'         => 'Payment processed by using customer credit cards',
            'shortcodes'    => '[website_url], [env], [customer_name], [customer_email], [support_name], [support_email]<br />
                                [ticket_id], [ticket_title], [ticket_url], [ticket_type], [ticket_status], [ticket_priority], [ticket_content], [message]<small>(New Message)</small> <br />
                                [time_to_pay], [time_to_response], [time_to_resolve], [hourly_price]<small>($/hr)</small>, [ticket_total_price]<br />
                                [paid_tokens]<small>($)</small>, [paid_amount]<small>($)</small>',
            'fields' => array(
                            array(
                                'title'         => 'For User',
                                'id'            => 'ticket_payment_processed_by_card',
                            ),
                            array(
                                'title'         => 'For Support',
                                'id'            => 'ticket_payment_processed_by_card_support',
                            ),
                            array(
                                'title'         => 'For Admin',
                                'id'            => 'ticket_payment_processed_by_card_admin',
                            ),
                                                  
            )
        ),
        array(            
            'menu'          => 'Payment Processed<br />With Prepurchased Tokens',            
            'title'         => 'Payment processed by using prepurchased tokens',
            'shortcodes'    => '[website_url], [env], [customer_name], [customer_email], [support_name], [support_email]<br />
                                [ticket_id], [ticket_title], [ticket_url], [ticket_type], [ticket_status], [ticket_priority], [ticket_content], [message]<small>(New Message)</small> <br />
                                [time_to_pay], [time_to_response], [time_to_resolve], [hourly_price]<small>($/hr)</small>, [ticket_total_price]<br />
                                [purchased_tokens], [paid_tokens], [remained_tokens]',
            'fields' => array(
                            array(
                                'title'         => 'For User',
                                'id'            => 'ticket_payment_processed_by_tokens',
                            ),
                            array(
                                'title'         => 'For Support',
                                'id'            => 'ticket_payment_processed_by_tokens_support',
                            ),
                            array(
                                'title'         => 'For Admin',
                                'id'            => 'ticket_payment_processed_by_tokens_admin',
                            ),
                                                  
            )
        )*/,

    ),
    'Forum Section' => array(
        array(
            'menu' => 'New Forum Post',
            'title' => 'Forum New Post',
            'shortcodes' => '[name], [username], [email], [website_url], [env], [topic_author_name], [topic_title], [topic_content], [community], [topic_url], [site_title], [site_org]',
            'fields' => array(
                array(
                    'title' => 'For User',
                    'id' => 'forum_new_post',
                )
            )
        ),
        array(
            'menu' => 'Reply Forum Post',
            'title' => 'Form Reply Post',
            'shortcodes' => '[name], [username], [email], [website_url], [env], [reply_author_name], [topic_title], [reply_content], [community], [reply_url], [site_title], [site_org]',
            'fields' => array(
                array(
                    'title' => 'For User',
                    'id' => 'forum_reply_post',
                )
            )
        ),

    ),
    'Claim Section' => array(
        array(
            'menu' => 'Claim is made',
            'title' => 'Claim is made',
            'shortcodes' => '[claim_id], [product_url],[product_name], [username], [useremail], [issuer], [certificate], [suite_name],[suite_url], [conformance_level], [role], [status], [date], [site_title], [site_org]',
            'fields' => array(
                array(
                    'title' => 'For Admin',
                    'id' => 'claim_created_admin',
                )
            )
        ),
    ),
    'E2E Section' => array(
        array(
            'menu' => 'E2E Test request',
            'title' => 'E2E Test request',
            'shortcodes' => '[test_suite], [env], [sender_owner], [sender_service], [sender_name], [receiver_owner], [receiver_service], [receiver_name], [agreement_url], [message_text], [email_receiver_name], [site_title], [site_org]',
            'fields' => array(
                array(
                    'title' => 'For Sender',
                    'id' => 'e2e_request_sender',
                ),
                array(
                    'title' => 'For Receiver',
                    'id' => 'e2e_request_receiver',
                ),
                array(
                    'title' => 'For Admin',
                    'id' => 'e2e_request_admin',
                )
            )
        ),
        array(
            'menu' => 'E2E test request accepted',
            'title' => 'E2E test request accepted',
            'shortcodes' => '[test_suite], [env], [sender_owner], [sender_service], [sender_name], [receiver_owner], [receiver_service], [receiver_name], [agreement_url], [message_text], [email_receiver_name], [site_title], [site_org]',
            'fields' => array(
                array(
                    'title' => 'For Sender',
                    'id' => 'e2e_request_accepted_sender',
                ),
                array(
                    'title' => 'For Receiver',
                    'id' => 'e2e_request_accepted_receiver',
                ),
                array(
                    'title' => 'For Admin',
                    'id' => 'e2e_request_accepted_admin',
                )
            )
        ),
        array(
            'menu' => 'E2E test request rejected',
            'title' => 'E2E test request rejected',
            'shortcodes' => '[test_suite], [env], [sender_owner], [sender_service], [sender_name], [receiver_owner], [receiver_service], [receiver_name], [agreement_url], [message_text], [email_receiver_name], [site_title], [site_org]',
            'fields' => array(
                array(
                    'title' => 'For Sender',
                    'id' => 'e2e_request_rejected_sender',
                ),
                array(
                    'title' => 'For Receiver',
                    'id' => 'e2e_request_rejected_receiver',
                ),
                array(
                    'title' => 'For Admin',
                    'id' => 'e2e_request_rejected_admin',
                )
            )
        ),
        array(
            'menu' => 'E2E test claim made',
            'title' => 'E2E test claim made',
            'shortcodes' => '[test_suite], [env], [sender_owner], [sender_service], [sender_name], [receiver_owner], [receiver_service], [receiver_name], [agreement_url], [message_text], [email_receiver_name], [site_title], [site_org]',
            'fields' => array(
                array(
                    'title' => 'For Sender',
                    'id' => 'e2e_claim_made_sender',
                ),
                array(
                    'title' => 'For Receiver',
                    'id' => 'e2e_claim_made_receiver',
                ),
                array(
                    'title' => 'For Admin',
                    'id' => 'e2e_claim_made_admin',
                )
            )
        ),
        array(
            'menu' => 'E2E test claim failed',
            'title' => 'E2E test claim failed',
            'shortcodes' => '[test_suite], [env], [sender_owner], [sender_service], [sender_name], [receiver_owner], [receiver_service], [receiver_name], [agreement_url], [message_text], [email_receiver_name], [site_title], [site_org]',
            'fields' => array(
                array(
                    'title' => 'For Sender',
                    'id' => 'e2e_claim_failed_sender',
                ),
                array(
                    'title' => 'For Receiver',
                    'id' => 'e2e_claim_failed_receiver',
                ),
                array(
                    'title' => 'For Admin',
                    'id' => 'e2e_claim_failed_admin',
                )
            )
        ),
        array(
            'menu' => 'E2E test claim confirmed',
            'title' => 'E2E test claim confirmed',
            'shortcodes' => '[test_suite], [env], [sender_owner], [sender_service], [sender_name], [receiver_owner], [receiver_service], [receiver_name], [agreement_url], [message_text], [email_receiver_name], [site_title], [site_org]',
            'fields' => array(
                array(
                    'title' => 'For Sender',
                    'id' => 'e2e_claim_confirmed_sender',
                ),
                array(
                    'title' => 'For Receiver',
                    'id' => 'e2e_claim_confirmed_receiver',
                ),
                array(
                    'title' => 'For Admin',
                    'id' => 'e2e_claim_confirmed_admin',
                )
            )
        ),
    )

);

//Add New Menu
add_action('admin_menu', 'add_email_management_page');
function add_email_management_page()
{
    add_menu_page('Email Template Management', 'Email Templates', 'administrator', 'email-management', 'create_email_management_page');
}

function create_email_management_page()
{
    global $ct_email_templates;
    ?>
    <script type="text/javascript"
            src="<?php echo dirname(get_bloginfo('stylesheet_url')) ?>/js/jquery-ui-1.10.3.custom.js"></script>
    <link href="<?php echo dirname(get_bloginfo('stylesheet_url')) ?>/css/jquery-ui-1.10.3.custom.css" type="text/css"
          rel="stylesheet"/>
    <style type="text/css">
        #emails .ui-tabs-nav {
            padding: 0;
            border-radius: 0;
            background: transparent;

        }

        /*#emails textarea{
            width: 100%;
            height: 200px;
        }*/
        #emails input[type="text"] {
            width: 50%;
        }

        .mceIframeContainer {
            height: 300px;
        }

        .mceIframeContainer iframe {
            height: 100% !important;

        }

        .ui-tabs .ui-tabs-nav li {
            display: block;
            float: none;
            border-radius: 0;
            margin: 0 !important;
            padding: 0 !important;
            border: none !important;
        }

        .ui-tabs .ui-tabs-nav li a {
            float: none;
            display: block;
            border-radius: 0;
            background: #f7f7f7;
            padding: 8px 14px;
            margin: 0;
            border-right: solid 1px #DFDFDF;
            border-left: solid 1px #DFDFDF;
            border-bottom: 1px solid #DFDFDF;
            border-top: 1px solid #F9F9F9;
            outline: none;
        }

        .ui-tabs .ui-tabs-nav li.ui-tabs-active a {
            background: #fff;
            color: #333;
            border-right: solid 1px #fff;
        }

        .ui-tabs .ui-tabs-nav li.tab-separator {
            background: none repeat scroll 0 0 #F0F0F0;
            border: 1px solid #DDD !important;
            color: #333;
            font-size: 13px;
            font-weight: bold;
            padding: 14px !important;
            margin-top: 10px !important;
        }

        .ui-tabs .ui-tabs-nav li.tab-separator:first-child {
            margin-top: 0 !important;
        }

        #emails {
            padding: 0;
            border-radius: 0;
        }

        #email-templates-nav {
            width: 210px;
            float: left;
        }

        #email-templates-wrapper {
            margin-left: 210px;
            border-top: solid 1px #ddd;
        }

        #email-templates-wrapper .widefat {
            clear: none;
        }

        #email-templates-wrapper h3 {
            border-bottom: 1px solid #A0A0A0;
            font-size: 18px;
            margin: 0;
            padding-bottom: 10px;
        }
    </style>
    <form name="adminform" method="post" action="admin.php" onsubmit="return saveEmailTemplates()">
        <input type="hidden" name="tab" id="email-tab-idx" value="0"/>

        <div class="wrap">
            <h2><?php echo get_site_title();?> Email Templates</h2>
            <br/>

            <div>
                <p style="float: left;">
                    <label><b>Support Name:</b> <input type="text" name="support_name" id="support_name"
                                                       value="<?php echo get_option('support_name') ?>"
                                                       size="30"/></label>
                    <label style="margin-left: 20px"><b>Support Email:</b> <input type="text" name="support_email"
                                                                                  id="support_email"
                                                                                  value="<?php echo get_option('support_email') ?>"
                                                                                  size="30"/></label>
                    <label style="margin-left: 20px"><b>Env:</b> <input type="text" name="env" id="env"
                                                                        value="<?php echo get_option('env') ?>"
                                                                        size="30"/></label>
                </p>

                <div style="float: right">
                    <input type="submit" value="Save Changes" name="email-templates" class="button-primary"/>
                    <?php wp_nonce_field('save-email-templates'); ?>
                </div>
                <br clear="all"/>
            </div>
            <div id="emails">
                <div id="email-templates-nav">
                    <ul>
                        <?php
                        $t_idx = 0;
                        foreach ($ct_email_templates as $section => $fields) : ?>
                            <li class="tab-separator"><?php echo $section ?></li>
                            <?php foreach ($fields as $t): ?>
                                <li><a href="#mail-template<?php echo $t_idx++ ?>"><?php echo $t['menu'] ?></a></li>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div id="email-templates-wrapper">
                    <?php
                    $t_idx = 0;
                    foreach ($ct_email_templates as $section => $fields) : ?>
                        <?php foreach ($fields as $t): ?>
                            <div id="mail-template<?php echo $t_idx++ ?>">
                                <h3><?php echo $t['title'] ?></h3>

                                <p><b>Short Codes:</b> <?php echo $t['shortcodes'] ?></p>
                                <table class="widefat">
                                    <?php foreach ($t['fields'] as $tf): ?>
                                        <thead>
                                        <tr>
                                            <th colspan="2"><?php echo $tf['title'] ?></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td class="tdlabel"><b>Title</b></td>
                                            <td>
                                                <input type="text" size="50" name="<?php echo $tf['id'] ?>_email_title"
                                                       id="<?php echo $tf['id'] ?>_email_title"
                                                       value="<?php echo get_option($tf['id'] . '_email_title') ?>"/>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="tdlabel"><b>Content</b></td>
                                            <td>
                                                <?php wp_editor(get_option($tf['id'] . '_email_content'), $tf['id'] . '_email_content', array('media_buttons' => false, 'editor_height' => 150)) ?>
                                            </td>
                                        </tr>
                                        </tbody>
                                    <?php endforeach; ?>
                                </table>
                            </div>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

    </form>
    <script type="text/javascript">
        jQuery(document).ready(function () {
            jQuery('#emails').tabs({"active": "<?php echo isset($_REQUEST['tab']) ? $_REQUEST['tab'] : 0?>"});
        })
        function saveEmailTemplates() {
            //Getting Actived Tabs
            var idx = jQuery('#email-templates-nav li.ui-state-default').index(jQuery('#email-templates-nav li.ui-tabs-active').get(0));
            jQuery('#email-tab-idx').val(idx);
            return true;
        }

    </script>
    <?php
}

add_action('admin_init', 'save_email_templates');
function save_email_templates()
{
    global $ct_email_templates;

    if (isset($_POST['email-templates']) && $_POST['email-templates'] == 'Save Changes') {
        check_admin_referer('save-email-templates');

        update_option('support_name', $_POST['support_name']);
        update_option('support_email', $_POST['support_email']);
        update_option('env', $_POST['env']);

        foreach ($ct_email_templates as $section => $fields) {
            foreach ($fields as $t) {
                foreach ($t['fields'] as $tf) {
                    $email_title = htmlentities(stripslashes_deep($_POST[$tf['id'] . '_email_title']));
                    update_option($tf['id'] . '_email_title', $email_title);

                    $email_content = stripslashes_deep($_POST[$tf['id'] . '_email_content']);
                    update_option($tf['id'] . '_email_content', $email_content);


                }
            }

        }

        wp_redirect("/wp-admin/admin.php?page=email-management&tab=" . (!$_REQUEST['tab'] ? 0 : $_REQUEST['tab']));
    }
}

//Front End Functions
function cp_send_email($to, $template_name, $data = array())
{
    global $phpmailer;

    if (!is_object($phpmailer) || !is_a($phpmailer, 'PHPMailer')) {
        require_once ABSPATH . WPINC . '/class-phpmailer.php';
        require_once ABSPATH . WPINC . '/class-smtp.php';
        $phpmailer = new PHPMailer(true);
    }

    //Clear Values
    $phpmailer->ClearAddresses();
    $phpmailer->ClearAllRecipients();
    $phpmailer->ClearAttachments();
    $phpmailer->ClearCustomHeaders();
    $phpmailer->ClearBCCs();
    $phpmailer->ClearCCs();
    $phpmailer->ClearReplyTos();

    $supportName = get_option('support_name');
    if (!$supportName)
        $supportName = 'Twain';

    $supportEmail = get_option('support_email');
    if (!$supportEmail) {
        $supportEmail = getenv('SMTP_FROM');
    }

    $emailTitle = get_option($template_name . "_email_title");
    $emailContent = get_option($template_name . "_email_content");

    if (!$emailTitle || !$emailContent) {
        return false;
    }

    //Add [env] and [website_url] shortcodes
    $data['[website_url]'] = get_site_url();
    $data['[env]'] = get_option('env');
    $data['[site_title]'] = get_site_title();
    $data['[site_org]'] = get_option('tw_site_organisation');

    $shortCodes = array_keys($data);
    $values = array_values($data);
    $emailTitle = str_replace($shortCodes, $values, $emailTitle);
    $emailContent = str_replace($shortCodes, $values, $emailContent);
    $emailContent = apply_filters('the_content', $emailContent);

    $phpmailer->AddReplyTo($supportEmail, $supportName);
    $phpmailer->FromName = $supportName;
    $phpmailer->From = $supportEmail;

    if (isset($to['name'])) {
        $phpmailer->AddAddress($to['email'], $to['name']);
    } else {
        foreach ($to as $u)
            $phpmailer->AddAddress($u['email'], $u['name']);
    }

    $phpmailer->IsSMTP();

    $phpmailer->Host = getenv('SMTP_HOST');
    $phpmailer->SMTPAuth = true;
    $phpmailer->SMTPSecure = "tls";
    $phpmailer->Port = getenv('SMTP_PORT');
    $phpmailer->Username = getenv('SMTP_USER');
    $phpmailer->Password = getenv('SMTP_PASSWORD');

    $phpmailer->IsHTML(true);
    $phpmailer->Subject = $emailTitle;
    $phpmailer->Body = $emailContent;

    try {
        return $phpmailer->Send();
    } catch (Exception $e) {
        return false;
    }
}

function cp_send_email_to_admin($template_name, $data = array())
{
    //Getting Admin Users    
    $users = get_users(array('role' => 'administrator'));

    $to = array();
    foreach ($users as $u) {
        $fname = get_user_meta($u->ID, 'first_name', true);
        $lname = get_user_meta($u->ID, 'last_name', true);
        $to[] = array('name' => $fname . " " . $lname, 'email' => $u->user_email);
    }

    cp_send_email($to, $template_name, $data);
}

function cp_send_email_to_community_admin($communities, $template_name, $data = array())
{
    global $bp, $wpdb;

    //Getting Community Admins
    if (is_array($communities))
        $query = "SELECT u.* FROM {$bp->groups->table_name_members} AS p LEFT JOIN {$wpdb->users} AS u ON u.ID = p.user_id WHERE group_id IN (" . implode(", ", $communities) . ") AND p.is_admin = 1 AND p.is_banned = 0";
    else
        $query = $wpdb->prepare("SELECT u.* FROM {$bp->groups->table_name_members} AS p LEFT JOIN {$wpdb->users} AS u ON u.ID = p.user_id WHERE group_id = %d AND p.is_admin = 1 AND p.is_banned = 0", $communities);

    $users = $wpdb->get_results($query);

    $to = array();
    foreach ($users as $u) {
        $fname = get_user_meta($u->ID, 'first_name', true);
        $lname = get_user_meta($u->ID, 'last_name', true);
        $to[] = array('name' => $fname . " " . $lname, 'email' => $u->user_email);
    }

    cp_send_email($to, $template_name, $data);
}


function cp_send_email_to_support($communities, $template_name, $data = array())
{
    global $bp, $wpdb;

    //Getting Community Supports(Moderators)
    if (is_array($communities))
        $query = "SELECT u.* FROM {$bp->groups->table_name_members} AS p LEFT JOIN {$wpdb->users} AS u ON u.ID = p.user_id WHERE group_id IN (" . implode(", ", $communities) . ") AND p.is_mod = 1 AND p.is_banned = 0";
    else
        $query = $wpdb->prepare("SELECT u.* FROM {$bp->groups->table_name_members} AS p LEFT JOIN {$wpdb->users} AS u ON u.ID = p.user_id WHERE group_id = %d AND p.is_mod = 1 AND p.is_banned = 0", $communities);

    $users = $wpdb->get_results($query);

    $to = array();
    foreach ($users as $u) {
        $fname = get_user_meta($u->ID, 'first_name', true);
        $lname = get_user_meta($u->ID, 'last_name', true);
        $to[] = array('name' => $fname . " " . $lname, 'email' => $u->user_email);
    }

    cp_send_email($to, $template_name, $data);
}

