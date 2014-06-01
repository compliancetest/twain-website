<?php
/**
* Management Site Emails
*/

//Add New Menu
add_action('admin_menu', 'add_email_management_page');
function add_email_management_page()
{
    add_menu_page('Email Template Management', 'Email Templates', 'administrator', 'email-management', 'create_email_management_page');
}

function create_email_management_page()
{
    ?>
    <script type="text/javascript" src="<?php echo dirname(get_bloginfo('stylesheet_url'))?>/js/jquery-ui-1.10.3.custom.js"></script>
    <link href="<?php echo dirname(get_bloginfo('stylesheet_url'))?>/css/jquery-ui-1.10.3.custom.css"  type="text/css" rel="stylesheet" />
    <style type="text/css">
        #emails .ui-tabs-nav{            
            padding: 0;
            border-radius: 0;
            background: transparent;
            
        }
        /*#emails textarea{
            width: 100%;
            height: 200px;
        }*/
        #emails input[type="text"]{
            width: 50%;
        }
        .mceIframeContainer{
            height: 300px;
        }
        .mceIframeContainer iframe{
            height: 100% !important;
            
        }
        .ui-tabs .ui-tabs-nav li{            
            display: block;
            float: none;
            border-radius: 0;
            margin: 0 !important;
            padding: 0 !important;
            border: none !important;
        }
        .ui-tabs .ui-tabs-nav li a{
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
        
        .ui-tabs .ui-tabs-nav li.ui-tabs-active a{
            background: #fff;
            color: #333;
            border-right: solid 1px #fff;            
        }
        .ui-tabs .ui-tabs-nav li.tab-separator{
            background: none repeat scroll 0 0 #F0F0F0;
            border: 1px solid #DDD !important;
            color: #333;
            font-size: 13px;
            font-weight: bold;
            padding: 14px !important;
            margin-top: 10px !important; 
        }
        .ui-tabs .ui-tabs-nav li.tab-separator:first-child{
            margin-top: 0 !important;
        }
        #emails{
            padding: 0;
            border-radius: 0;
        }
        #email-templates-nav{
            width: 210px;
            float: left;
        }
        #email-templates-wrapper{
            margin-left: 210px;
            border-top: solid 1px #ddd;
        }
        #email-templates-wrapper .widefat{
            clear: none;
        }
        #email-templates-wrapper h3{
            border-bottom: 1px solid #A0A0A0;
            font-size: 18px;
            margin: 0;
            padding-bottom: 10px;
        }
    </style>
      <form name="adminform" method="post" action="admin.php" onsubmit="return saveEmailTemplates()">
          <input type="hidden" name="tab" id="email-tab-idx" value="0" />
          <div class="wrap">
            <h2>ComplianceTest Email Templates</h2>                
            <br />
            <div>
                <p style="float: left;">
                    <label><b>Support Name:</b> <input type="text" name="support_name" id="support_name" value="<?php echo get_option('support_name')?>" size="30" /></label>
                    <label style="margin-left: 20px"><b>Support Email:</b> <input type="text" name="support_email" id="support_email" value="<?php echo get_option('support_email')?>" size="30" /></label>
                    <label style="margin-left: 20px"><b>Env:</b> <input type="text" name="env" id="env" value="<?php echo get_option('env')?>" size="30" /></label>
                </p>
                <div style="float: right">
                    <input type="submit" value="Save Changes" name="email-templates" class="button-primary" />                    
                    <?php wp_nonce_field('save-email-templates'); ?>
                </div>
                <br clear="all" />
            </div>
            <div id="emails">
                <div id="email-templates-nav">
                    <ul>
                        <li class="tab-separator">User Section</li>
                        <li><a href="#new-user">User Registered</a></li>
                        <li><a href="#verification-email">Resend Verification Email</a></li>
                        <li><a href="#verification-success">User Verification Success</a></li>
                        <li><a href="#forgot-password">Forgot Password</a></li>
                        <li><a href="#password-changed">Password Changed</a></li>
                        <li><a href="#email-changed">Email Address Changed</a></li>
                        <li><a href="#changed-email-verification-success">Changed Email Address<br/> Verification Success</a></li>
                        
                        <li class="tab-separator">Subscription Section</li>
                        <li><a href="#purchase-subscription">Purchase Paid Subscription</a></li>
                        <li><a href="#purchase-signup-fee-only-subscription">Purchase Sign-up Fee Only<br />Subscription</a></li>
                        <li><a href="#purchase-free-subscription">Purchase Free Subscription</a></li>                        
                        <li><a href="#purchase-additional-subscription">Purchase Subscription to<br />Additional Version</a></li>                        
                        <li><a href="#purchase-organisational-subscription">Purchase Subscription based<br />on Organisational Pricing</a></li>                        
                        <li><a href="#inarrears-subscription">Active -> InArrears</a></li>
                        <li><a href="#frozen-subscription">InArrears -> Frozen</a></li>
                        <li><a href="#active-subscription">InArrears -> Active</a></li>
                        <li><a href="#active-subscription2">Frozen -> Active</a></li>
                        <li><a href="#unsubscribe-subscription">Unsubscribe Subscription</a></li>
                        <li><a href="#cancel-subscription">Cancel Paid Subscription</a></li>
                        <li><a href="#cancel-free-subscription">Cancel Free Subscription</a></li>
                        <li><a href="#cancel-additional-subscription">Cancel Subscription<br />to Additional Version</a></li>

                        
                        <li class="tab-separator">Membership Section</li>
                        <li><a href="#membership-request-received">Membership Request Received</a></li>       
                        <li><a href="#membership-request-approved">Membership Request Approved</a></li>       
                        <li><a href="#membership-request-rejected">Membership Request Rejected</a></li>       
                        <li><a href="#member-leave-community">Member Leave Community</a></li>                               
                        <li><a href="#member-promoted">Member Promoted</a></li>
                        <li><a href="#remove-member-from-community">Remove Member</a></li>
                        
                        <li class="tab-separator">Suite & Case Section</li>
                        <li><a href="#suite-changed">Test Suite Changed</a></li>
                        <li><a href="#case-changed">Test Case Changed</a></li>                    
                        
                        
                        <li class="tab-separator">Ticket Section</li>                        
                        <li><a href="#ticket-created">Ticket Created</a></li>                    
                        <li><a href="#ticket-updated">Ticket Updated</a></li>                    
                        <li><a href="#ticket-started">Ticket Started</a></li>                    
                        <li><a href="#ticket-solved">Ticket Resolved</a></li>                    
                        <li><a href="#ticket-closed">Ticket Closed</a></li>                    
                        <li><a href="#ticket-payment-processed1">Payment Processed<br />With Credit Card</a></li>
                        <li><a href="#ticket-payment-processed2">Payment Processed<br />With Prepurchased Tokens</a></li>
                        
                        <li class="tab-separator">Forum Section</li>
                        <li><a href="#forum-new-post">New Forum Post</a></li>
                        <li><a href="#forum-reply-post">Reply Forum Post</a></li>
                        
                        <li class="tab-separator">Claim Section</li>
                        <li><a href="#claim-created">Claim is made</a></li>
                    </ul>
                </div>
                <div id="email-templates-wrapper">
                <div id="new-user">
                    <?php
                        $new_user_email_title = get_option('new_user_email_title');
                        $new_user_email_content = get_option('new_user_email_content');
                        $new_user_admin_email_title = get_option('new_user_admin_email_title');
                        $new_user_admin_email_content = get_option('new_user_admin_email_content');
                        
                    ?>       
                    <h3>User Registered</h3>
                    <p><b>Short Codes:</b> [name], [username], [email], [website_url], [env], [password], [link], [organisation]</p>
                    <table class="widefat">
                        <thead>
                            <tr>
                                <th colspan="2">For User</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="tdlabel"><b>Title</b></td>
                                <td>
                                    <input type="text" size="50" name="new_user_email_title" id="new_user_email_title" value="<?php echo $new_user_email_title?>" />
                                </td>
                            </tr>
                            <tr>
                                <td class="tdlabel"><b>Content</b></td>
                                <td>                                    
                                    <?php wp_editor($new_user_email_content, 'new_user_email_content', array('media_buttons' => false,  'editor_height' => 150)) ?>
                                </td>
                            </tr>
                        </tbody>                        
                        <thead>
                            <tr>
                                <th colspan="2">For Admin</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="tdlabel"><b>Title</b></td>
                                <td>
                                    <input type="text" size="50" name="new_user_admin_email_title" id="new_user_admin_email_title" value="<?php echo $new_user_admin_email_title?>" />
                                </td>
                            </tr>
                            <tr>
                                <td class="tdlabel"><b>Content</b></td>
                                <td>
                                    <?php wp_editor($new_user_admin_email_content, 'new_user_admin_email_content', array('media_buttons' => false,  'editor_height' => 150)) ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div id="email-changed">
                    <?php
                        $email_changed_email_title = get_option('email_changed_email_title');
                        $email_changed_email_content = get_option('email_changed_email_content');
                        $email_changed_admin_email_title = get_option('email_changed_admin_email_title');
                        $email_changed_admin_email_content = get_option('email_changed_admin_email_content');
                        
                    ?>       
                    <h3>Email Address Changed</h3>
                    <p><b>Short Codes:</b> [name], [username], [email], [website_url], [env], [link]</p>
                    <table class="widefat">
                        <thead>
                            <tr>
                                <th colspan="2">For User</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="tdlabel"><b>Title</b></td>
                                <td>
                                    <input type="text" size="50" name="email_changed_email_title" id="email_changed_email_title" value="<?php echo $email_changed_email_title?>" />
                                </td>
                            </tr>
                            <tr>
                                <td class="tdlabel"><b>Content</b></td>
                                <td>                                    
                                    <?php wp_editor($email_changed_email_content, 'email_changed_email_content', array('media_buttons' => false,  'editor_height' => 150)) ?>
                                </td>
                            </tr>
                        </tbody>                        
                        <thead>
                            <tr>
                                <th colspan="2">For Admin</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="tdlabel"><b>Title</b></td>
                                <td>
                                    <input type="text" size="50" name="email_changed_admin_email_title" id="email_changed_admin_email_title" value="<?php echo $email_changed_admin_email_title?>" />
                                </td>
                            </tr>
                            <tr>
                                <td class="tdlabel"><b>Content</b></td>
                                <td>
                                    <?php wp_editor($email_changed_admin_email_content, 'email_changed_admin_email_content', array('media_buttons' => false,  'editor_height' => 150)) ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div id="changed-email-verification-success">
                    <?php
                        $changed_email_verify_success_email_title = get_option('changed_email_verify_success_email_title');
                        $changed_email_verify_success_email_content = get_option('changed_email_verify_success_email_content');
                        $changed_email_verify_success_admin_email_title = get_option('changed_email_verify_success_admin_email_title');
                        $changed_email_verify_success_admin_email_content = get_option('changed_email_verify_success_admin_email_content');                        
                    ?>   
                    <h3>User Verification Success</h3>                 
                    <p><b>Short Codes:</b> [name], [email], [website_url], [env], [username]</p>
                    <table class="widefat">
                        <thead>
                            <tr>
                                <th colspan="2">For User</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="tdlabel"><b>Title</b></td>
                                <td>
                                    <input type="text" size="50" name="changed_email_verify_success_email_title" id="changed_email_verify_success_email_title" value="<?php echo $changed_email_verify_success_email_title?>" />
                                </td>
                            </tr>
                            <tr>
                                <td class="tdlabel"><b>Content</b></td>
                                <td>                                    
                                    <?php wp_editor($changed_email_verify_success_email_content, 'changed_email_verify_success_email_content', array('media_buttons' => false,  'editor_height' => 150)) ?>
                                </td>
                            </tr>
                        </tbody>
                        <thead>
                            <tr>
                                <th colspan="2">For Admin</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="tdlabel"><b>Title</b></td>
                                <td>
                                    <input type="text" size="50" name="changed_email_verify_success_admin_email_title" id="changed_email_verify_success_admin_email_title" value="<?php echo $changed_email_verify_success_admin_email_title?>" />
                                </td>
                            </tr>
                            <tr>
                                <td class="tdlabel"><b>Content</b></td>
                                <td>
                                    <?php wp_editor($changed_email_verify_success_admin_email_content, 'changed_email_verify_success_admin_email_content', array('media_buttons' => false,  'editor_height' => 150)) ?>
                                </td>
                            </tr>
                        </tbody>
                        
                    </table>
                </div>
                <div id="verification-email">
                    <?php
                        $verify_email_title = get_option('verify_email_title');
                        $verify_email_content = get_option('verify_email_content');                
                    ?>                
                    <h3>Re-send Verification Email</h3>    
                    <p><b>Short Codes:</b> [name],[username], [email], [website_url], [env], [link]</p>
                    <table class="widefat">
                        <thead>
                            <tr>
                                <th colspan="2">For User</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="tdlabel"><b>Title</b></td>
                                <td>
                                    <input type="text" size="50" name="verify_email_title" id="verify_email_title" value="<?php echo $verify_email_title?>" />
                                </td>
                            </tr>
                            <tr>
                                <td class="tdlabel"><b>Content</b></td>
                                <td>
                                    <?php wp_editor($verify_email_content, 'verify_email_content', array('media_buttons' => false,  'editor_height' => 150)) ?>
                                </td>
                            </tr>
                        </tbody>     
                    </table>
                </div>
                <div id="verification-success">
                    <?php
                        $user_verify_success_email_title = get_option('user_verify_success_email_title');
                        $user_verify_success_email_content = get_option('user_verify_success_email_content');
                        $user_verify_success_admin_email_title = get_option('user_verify_success_admin_email_title');
                        $user_verify_success_admin_email_content = get_option('user_verify_success_admin_email_content');                        
                    ?>   
                    <h3>User Verification Success</h3>                 
                    <p><b>Short Codes:</b> [name], [email], [website_url], [env], [username]</p>
                    <table class="widefat">
                        <thead>
                            <tr>
                                <th colspan="2">For User</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="tdlabel"><b>Title</b></td>
                                <td>
                                    <input type="text" size="50" name="user_verify_success_email_title" id="user_verify_success_email_title" value="<?php echo $user_verify_success_email_title?>" />
                                </td>
                            </tr>
                            <tr>
                                <td class="tdlabel"><b>Content</b></td>
                                <td>                                    
                                    <?php wp_editor($user_verify_success_email_content, 'user_verify_success_email_content', array('media_buttons' => false,  'editor_height' => 150)) ?>
                                </td>
                            </tr>
                        </tbody>
                        <thead>
                            <tr>
                                <th colspan="2">For Admin</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="tdlabel"><b>Title</b></td>
                                <td>
                                    <input type="text" size="50" name="user_verify_success_admin_email_title" id="user_verify_success_admin_email_title" value="<?php echo $user_verify_success_admin_email_title?>" />
                                </td>
                            </tr>
                            <tr>
                                <td class="tdlabel"><b>Content</b></td>
                                <td>
                                    <?php wp_editor($user_verify_success_admin_email_content, 'user_verify_success_admin_email_content', array('media_buttons' => false,  'editor_height' => 150)) ?>
                                </td>
                            </tr>
                        </tbody>
                        
                    </table>
                </div>
                <div id="purchase-subscription">
                    <?php
                    $purchase_subscription_email_title = get_option('purchase_subscription_email_title');
                    $purchase_subscription_email_content = get_option('purchase_subscription_email_content');
                    $purchase_subscription_admin_email_title = get_option('purchase_subscription_admin_email_title');
                    $purchase_subscription_admin_email_content = get_option('purchase_subscription_admin_email_content');
                    ?>
                    <h3>Purchase Paid Subscription</h3>
                    <p><b>Short Codes:</b> [name], [email], [website_url], [env], [suite_name], [suite_url], [paid_amount], [signup_fee], [monthly_fee], [community_url], [payment_email]</p>
                    <table class="widefat">
                        <thead>
                            <tr>
                                <th colspan="2">For User</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="tdlabel"><b>Title</b></td>
                                <td>
                                    <input type="text" size="50" name="purchase_subscription_email_title" id="purchase_subscription_email_title" value="<?php echo $purchase_subscription_email_title?>" />
                                </td>
                            </tr>
                            <tr>
                                <td class="tdlabel"><b>Content</b></td>
                                <td>
                                    <?php wp_editor($purchase_subscription_email_content, 'purchase_subscription_email_content', array('media_buttons' => false,  'editor_height' => 150)) ?>
                                </td>
                            </tr>
                        </tbody>
                        <thead>
                            <tr>
                                <th colspan="2">For Admin</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="tdlabel"><b>Title</b></td>
                                <td>
                                    <input type="text" size="50" name="purchase_subscription_admin_email_title" id="purchase_subscription_admin_email_title" value="<?php echo $purchase_subscription_admin_email_title?>" />
                                </td>
                            </tr>
                            <tr>
                                <td class="tdlabel"><b>Content</b></td>
                                <td>
                                    <?php wp_editor($purchase_subscription_admin_email_content, 'purchase_subscription_admin_email_content', array('media_buttons' => false,  'editor_height' => 150)) ?>
                                </td>
                            </tr>
                        </tbody>
                        
                    </table>
                </div>
                <div id="purchase-free-subscription">
                    <?php
                    $purchase_free_subscription_email_title = get_option('purchase_free_subscription_email_title');
                    $purchase_free_subscription_email_content = get_option('purchase_free_subscription_email_content');
                    $purchase_free_subscription_admin_email_title = get_option('purchase_free_subscription_admin_email_title');
                    $purchase_free_subscription_admin_email_content = get_option('purchase_free_subscription_admin_email_content');
                    ?>
                    <h3>Purchase Free Subscription</h3>
                    <p><b>Short Codes:</b> [name], [email], [website_url], [env], [suite_name], [suite_url], [community_url]</p>
                    <table class="widefat">
                        <thead>
                        <tr>
                            <th colspan="2">For User</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td class="tdlabel"><b>Title</b></td>
                            <td>
                                <input type="text" size="50" name="purchase_free_subscription_email_title" id="purchase_free_subscription_email_title" value="<?php echo $purchase_free_subscription_email_title?>" />
                            </td>
                        </tr>
                        <tr>
                            <td class="tdlabel"><b>Content</b></td>
                            <td>
                                <?php wp_editor($purchase_free_subscription_email_content, 'purchase_free_subscription_email_content', array('media_buttons' => false,  'editor_height' => 150)) ?>
                            </td>
                        </tr>
                        </tbody>
                        <thead>
                        <tr>
                            <th colspan="2">For Admin</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td class="tdlabel"><b>Title</b></td>
                            <td>
                                <input type="text" size="50" name="purchase_free_subscription_admin_email_title" id="purchase_free_subscription_admin_email_title" value="<?php echo $purchase_free_subscription_admin_email_title?>" />
                            </td>
                        </tr>
                        <tr>
                            <td class="tdlabel"><b>Content</b></td>
                            <td>
                                <?php wp_editor($purchase_free_subscription_admin_email_content, 'purchase_free_subscription_admin_email_content', array('media_buttons' => false,  'editor_height' => 150)) ?>
                            </td>
                        </tr>
                        </tbody>

                    </table>
                </div>
                <div id="purchase-signup-fee-only-subscription">
                    <?php
                    $purchase_signup_fee_only_subscription_email_title = get_option('purchase_signup_fee_only_subscription_email_title');
                    $purchase_signup_fee_only_subscription_email_content = get_option('purchase_signup_fee_only_subscription_email_content');
                    $purchase_signup_fee_only_subscription_admin_email_title = get_option('purchase_signup_fee_only_subscription_admin_email_title');
                    $purchase_signup_fee_only_subscription_admin_email_content = get_option('purchase_signup_fee_only_subscription_admin_email_content');
                    ?>
                    <h3>Purchase Signup Fee Only Subscription</h3>
                    <p><b>Short Codes:</b> [name], [email], [website_url], [env], [suite_name], [suite_url], [paid_amount], [signup_fee], [monthly_fee], [community_url], [payment_email]</p>
                    <table class="widefat">
                        <thead>
                        <tr>
                            <th colspan="2">For User</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td class="tdlabel"><b>Title</b></td>
                            <td>
                                <input type="text" size="50" name="purchase_signup_fee_only_subscription_email_title" id="purchase_signup_fee_only_subscription_email_title" value="<?php echo $purchase_signup_fee_only_subscription_email_title?>" />
                            </td>
                        </tr>
                        <tr>
                            <td class="tdlabel"><b>Content</b></td>
                            <td>
                                <?php wp_editor($purchase_signup_fee_only_subscription_email_content, 'purchase_signup_fee_only_subscription_email_content', array('media_buttons' => false,  'editor_height' => 150)) ?>
                            </td>
                        </tr>
                        </tbody>
                        <thead>
                        <tr>
                            <th colspan="2">For Admin</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td class="tdlabel"><b>Title</b></td>
                            <td>
                                <input type="text" size="50" name="purchase_signup_fee_only_subscription_admin_email_title" id="purchase_signup_fee_only_subscription_admin_email_title" value="<?php echo $purchase_signup_fee_only_subscription_admin_email_title?>" />
                            </td>
                        </tr>
                        <tr>
                            <td class="tdlabel"><b>Content</b></td>
                            <td>
                                <?php wp_editor($purchase_signup_fee_only_subscription_admin_email_content, 'purchase_signup_fee_only_subscription_admin_email_content', array('media_buttons' => false,  'editor_height' => 150)) ?>
                            </td>
                        </tr>
                        </tbody>

                    </table>
                </div>
                <div id="purchase-additional-subscription">
                    <?php
                    $purchase_additional_subscription_email_title = get_option('purchase_additional_subscription_email_title');
                    $purchase_additional_subscription_email_content = get_option('purchase_additional_subscription_email_content');
                    $purchase_additional_subscription_admin_email_title = get_option('purchase_additional_subscription_admin_email_title');
                    $purchase_additional_subscription_admin_email_content = get_option('purchase_additional_subscription_admin_email_content');
                    ?>
                    <h3>Purchase a subscription to additional version</h3>
                    <p><b>Short Codes:</b> [name], [email], [website_url], [env], [suite_name], [suite_url], [paid_amount], [signup_fee], [monthly_fee], [community_url], [payment_email]</p>
                    <table class="widefat">
                        <thead>
                            <tr>
                                <th colspan="2">For User</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="tdlabel"><b>Title</b></td>
                                <td>
                                    <input type="text" size="50" name="purchase_additional_subscription_email_title" id="purchase_additional_subscription_email_title" value="<?php echo $purchase_additional_subscription_email_title?>" />
                                </td>
                            </tr>
                            <tr>
                                <td class="tdlabel"><b>Content</b></td>
                                <td>
                                    <?php wp_editor($purchase_additional_subscription_email_content, 'purchase_additional_subscription_email_content', array('media_buttons' => false,  'editor_height' => 150)) ?>
                                </td>
                            </tr>
                        </tbody>
                        <thead>
                            <tr>
                                <th colspan="2">For Admin</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="tdlabel"><b>Title</b></td>
                                <td>
                                    <input type="text" size="50" name="purchase_additional_subscription_admin_email_title" id="purchase_additional_subscription_admin_email_title" value="<?php echo $purchase_additional_subscription_admin_email_title?>" />
                                </td>
                            </tr>
                            <tr>
                                <td class="tdlabel"><b>Content</b></td>
                                <td>
                                    <?php wp_editor($purchase_additional_subscription_admin_email_content, 'purchase_additional_subscription_admin_email_content', array('media_buttons' => false,  'editor_height' => 150)) ?>
                                </td>
                            </tr>
                        </tbody>                        
                    </table>
                </div>
                <div id="purchase-organisational-subscription">
                    <?php
                    $purchase_organisational_subscription_email_title = get_option('purchase_organisational_subscription_email_title');
                    $purchase_organisational_subscription_email_content = get_option('purchase_organisational_subscription_email_content');
                    $purchase_organisational_subscription_admin_email_title = get_option('purchase_organisational_subscription_admin_email_title');
                    $purchase_organisational_subscription_admin_email_content = get_option('purchase_organisational_subscription_admin_email_content');
                    ?>
                    <h3>Purchase a subscription based on organisational pricing</h3>
                    <p><b>Short Codes:</b> [name], [email], [website_url], [env], [suite_name], [suite_url], [paid_amount], [signup_fee], [monthly_fee], [community_url], [payment_email]</p>
                    <table class="widefat">
                        <thead>
                            <tr>
                                <th colspan="2">For User</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="tdlabel"><b>Title</b></td>
                                <td>
                                    <input type="text" size="50" name="purchase_organisational_subscription_email_title" id="purchase_organisational_subscription_email_title" value="<?php echo $purchase_organisational_subscription_email_title?>" />
                                </td>
                            </tr>
                            <tr>
                                <td class="tdlabel"><b>Content</b></td>
                                <td>
                                    <?php wp_editor($purchase_organisational_subscription_email_content, 'purchase_organisational_subscription_email_content', array('media_buttons' => false,  'editor_height' => 150)) ?>
                                </td>
                            </tr>
                        </tbody>
                        <thead>
                            <tr>
                                <th colspan="2">For Admin</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="tdlabel"><b>Title</b></td>
                                <td>
                                    <input type="text" size="50" name="purchase_organisational_subscription_admin_email_title" id="purchase_organisational_subscription_admin_email_title" value="<?php echo $purchase_organisational_subscription_admin_email_title?>" />
                                </td>
                            </tr>
                            <tr>
                                <td class="tdlabel"><b>Content</b></td>
                                <td>
                                    <?php wp_editor($purchase_organisational_subscription_admin_email_content, 'purchase_organisational_subscription_admin_email_content', array('media_buttons' => false,  'editor_height' => 150)) ?>
                                </td>
                            </tr>
                        </tbody>                        
                    </table>
                </div>
                
                <div id="unsubscribe-subscription">
                    <?php
                    $unsubscribing_email_title = get_option('unsubscribing_email_title');
                    $unsubscribing_email_content = get_option('unsubscribing_email_content');
                    $unsubscribing_admin_email_title = get_option('unsubscribing_admin_email_title');
                    $unsubscribing_admin_email_content = get_option('unsubscribing_admin_email_content');
                    ?>
                    <h3>Unsubscribe Paid Subscription</h3>
                    <p><b>Short Codes:</b> [name], [email], [website_url], [env], [suite_name], [suite_url]</p>
                    <table class="widefat">
                        <thead>
                            <tr>
                                <th colspan="2">For User</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="tdlabel"><b>Title</b></td>
                                <td>
                                    <input type="text" size="50" name="unsubscribing_email_title" id="unsubscribing_email_title" value="<?php echo $unsubscribing_email_title?>" />
                                </td>
                            </tr>
                            <tr>
                                <td class="tdlabel"><b>Content</b></td>
                                <td>
                                    <?php wp_editor($unsubscribing_email_content, 'unsubscribing_email_content', array('media_buttons' => false,  'editor_height' => 150)) ?>
                                </td>
                            </tr>
                        </tbody>
                        <thead>
                            <tr>
                                <th colspan="2">For Admin</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="tdlabel"><b>Title</b></td>
                                <td>
                                    <input type="text" size="50" name="unsubscribing_admin_email_title" id="unsubscribing_admin_email_title" value="<?php echo $unsubscribing_admin_email_title?>" />
                                </td>
                            </tr>
                            <tr>
                                <td class="tdlabel"><b>Content</b></td>
                                <td>
                                    <?php wp_editor($unsubscribing_admin_email_content, 'unsubscribing_admin_email_content', array('media_buttons' => false,  'editor_height' => 150)) ?>
                                </td>
                            </tr>
                        </tbody>
                        
                    </table>
                </div>
                <div id="cancel-subscription">
                    <?php
                    $cancel_subscription_email_title = get_option('cancel_subscription_email_title');
                    $cancel_subscription_email_content = get_option('cancel_subscription_email_content');
                    $cancel_subscription_admin_email_title = get_option('cancel_subscription_admin_email_title');
                    $cancel_subscription_admin_email_content = get_option('cancel_subscription_admin_email_content');
                    ?>
                    <h3>Cancel Paid Subscription</h3>
                    <p><b>Short Codes:</b> [name], [email], [website_url], [env], [suite_name], [suite_url], [paid_amount], [signup_fee], [monthly_fee]</p>
                    <table class="widefat">
                        <thead>
                            <tr>
                                <th colspan="2">For User</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="tdlabel"><b>Title</b></td>
                                <td>
                                    <input type="text" size="50" name="cancel_subscription_email_title" id="cancel_subscription_email_title" value="<?php echo $cancel_subscription_email_title?>" />
                                </td>
                            </tr>
                            <tr>
                                <td class="tdlabel"><b>Content</b></td>
                                <td>
                                    <?php wp_editor($cancel_subscription_email_content, 'cancel_subscription_email_content', array('media_buttons' => false,  'editor_height' => 150)) ?>    
                                </td>
                            </tr>
                        </tbody>
                        <thead>
                            <tr>
                                <th colspan="2">For Admin</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="tdlabel"><b>Title</b></td>
                                <td>
                                    <input type="text" size="50" name="cancel_subscription_admin_email_title" id="cancel_subscription_admin_email_title" value="<?php echo $cancel_subscription_admin_email_title?>" />
                                </td>
                            </tr>
                            <tr>
                                <td class="tdlabel"><b>Content</b></td>
                                <td>
                                    <?php wp_editor($cancel_subscription_admin_email_content, 'cancel_subscription_admin_email_content', array('media_buttons' => false,  'editor_height' => 150)) ?>
                                </td>
                            </tr>
                        </tbody>
                        
                    </table>
                </div>

                <div id="cancel-free-subscription">
                    <?php
                    $cancel_free_subscription_email_title = get_option('cancel_free_subscription_email_title');
                    $cancel_free_subscription_email_content = get_option('cancel_free_subscription_email_content');
                    $cancel_free_subscription_admin_email_title = get_option('cancel_free_subscription_admin_email_title');
                    $cancel_free_subscription_admin_email_content = get_option('cancel_free_subscription_admin_email_content');
                    ?>
                    <h3>Cancel Free Subscription</h3>
                    <p><b>Short Codes:</b> [name], [email], [website_url], [env], [suite_name], [suite_url]</p>
                    <table class="widefat">
                        <thead>
                        <tr>
                            <th colspan="2">For User</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td class="tdlabel"><b>Title</b></td>
                            <td>
                                <input type="text" size="50" name="cancel_free_subscription_email_title" id="cancel_free_subscription_email_title" value="<?php echo $cancel_free_subscription_email_title?>" />
                            </td>
                        </tr>
                        <tr>
                            <td class="tdlabel"><b>Content</b></td>
                            <td>
                                <?php wp_editor($cancel_free_subscription_email_content, 'cancel_free_subscription_email_content', array('media_buttons' => false,  'editor_height' => 150)) ?>
                            </td>
                        </tr>
                        </tbody>
                        <thead>
                        <tr>
                            <th colspan="2">For Admin</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td class="tdlabel"><b>Title</b></td>
                            <td>
                                <input type="text" size="50" name="cancel_free_subscription_admin_email_title" id="cancel_free_subscription_admin_email_title" value="<?php echo $cancel_free_subscription_admin_email_title?>" />
                            </td>
                        </tr>
                        <tr>
                            <td class="tdlabel"><b>Content</b></td>
                            <td>
                                <?php wp_editor($cancel_free_subscription_admin_email_content, 'cancel_free_subscription_admin_email_content', array('media_buttons' => false,  'editor_height' => 150)) ?>
                            </td>
                        </tr>
                        </tbody>

                    </table>
                </div>

                <div id="cancel-additional-subscription">
                    <?php
                    $cancel_additional_subscription_email_title = get_option('cancel_additional_subscription_email_title');
                    $cancel_additional_subscription_email_content = get_option('cancel_additional_subscription_email_content');
                    $cancel_additional_subscription_admin_email_title = get_option('cancel_additional_subscription_admin_email_title');
                    $cancel_additional_subscription_admin_email_content = get_option('cancel_additional_subscription_admin_email_content');
                    ?>
                    <h3>Cancel Subscription to Additional Version</h3>
                    <p><b>Short Codes:</b> [name], [email], [website_url], [env], [suite_name], [suite_url], [paid_amount], [signup_fee], [monthly_fee]</p>
                    <table class="widefat">
                        <thead>
                            <tr>
                                <th colspan="2">For User</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="tdlabel"><b>Title</b></td>
                                <td>
                                    <input type="text" size="50" name="cancel_additional_subscription_email_title" id="cancel_additional_subscription_email_title" value="<?php echo $cancel_additional_subscription_email_title?>" />
                                </td>
                            </tr>
                            <tr>
                                <td class="tdlabel"><b>Content</b></td>
                                <td>
                                    <?php wp_editor($cancel_additional_subscription_email_content, 'cancel_additional_subscription_email_content', array('media_buttons' => false,  'editor_height' => 150)) ?>    
                                </td>
                            </tr>
                        </tbody>
                        <thead>
                            <tr>
                                <th colspan="2">For Admin</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="tdlabel"><b>Title</b></td>
                                <td>
                                    <input type="text" size="50" name="cancel_additional_subscription_admin_email_title" id="cancel_additional_subscription_admin_email_title" value="<?php echo $cancel_additional_subscription_admin_email_title?>" />
                                </td>
                            </tr>
                            <tr>
                                <td class="tdlabel"><b>Content</b></td>
                                <td>
                                    <?php wp_editor($cancel_additional_subscription_admin_email_content, 'cancel_additional_subscription_admin_email_content', array('media_buttons' => false,  'editor_height' => 150)) ?>
                                </td>
                            </tr>
                        </tbody>
                        
                    </table>
                </div>

                <div id="active-subscription">
                    <?php
                    $active_subscription_email_title = get_option('active_subscription_email_title');
                    $active_subscription_email_content = get_option('active_subscription_email_content');
                    $active_subscription_admin_email_title = get_option('active_subscription_admin_email_title');
                    $active_subscription_admin_email_content = get_option('active_subscription_admin_email_content');
                    ?>
                    <h3>Subscription is Active From InArrears</h3>
                    <p><b>Short Codes:</b> [name], [email], [website_url], [env], [suite_name], [suite_url], [monthly_fee]</p>
                    <table class="widefat">
                        <thead>
                            <tr>
                                <th colspan="2">For User</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="tdlabel"><b>Title</b></td>
                                <td>
                                    <input type="text" size="50" name="active_subscription_email_title" id="active_subscription_email_title" value="<?php echo $active_subscription_email_title?>" />
                                </td>
                            </tr>
                            <tr>
                                <td class="tdlabel"><b>Content</b></td>
                                <td>
                                    <?php wp_editor($active_subscription_email_content, 'active_subscription_email_content', array('media_buttons' => false,  'editor_height' => 150)) ?>    
                                </td>
                            </tr>
                        </tbody>
                        <thead>
                            <tr>
                                <th colspan="2">For Admin</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="tdlabel"><b>Title</b></td>
                                <td>
                                    <input type="text" size="50" name="active_subscription_admin_email_title" id="active_subscription_admin_email_title" value="<?php echo $active_subscription_admin_email_title?>" />
                                </td>
                            </tr>
                            <tr>
                                <td class="tdlabel"><b>Content</b></td>
                                <td>
                                    <?php wp_editor($active_subscription_admin_email_content, 'active_subscription_admin_email_content', array('media_buttons' => false,  'editor_height' => 150)) ?>
                                </td>
                            </tr>
                        </tbody>
                        
                    </table>
                </div>
                <div id="active-subscription2">
                    <?php
                    $active_subscription2_email_title = get_option('active_subscription2_email_title');
                    $active_subscription2_email_content = get_option('active_subscription2_email_content');
                    $active_subscription2_admin_email_title = get_option('active_subscription2_admin_email_title');
                    $active_subscription2_admin_email_content = get_option('active_subscription2_admin_email_content');
                    ?>
                    <h3>Subscription is Active from Frozen</h3>
                    <p><b>Short Codes:</b> [name], [email], [website_url], [env], [suite_name], [suite_url], [monthly_fee]</p>
                    <table class="widefat">
                        <thead>
                            <tr>
                                <th colspan="2">For User</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="tdlabel"><b>Title</b></td>
                                <td>
                                    <input type="text" size="50" name="active_subscription2_email_title" id="active_subscription2_email_title" value="<?php echo $active_subscription2_email_title?>" />
                                </td>
                            </tr>
                            <tr>
                                <td class="tdlabel"><b>Content</b></td>
                                <td>
                                    <?php wp_editor($active_subscription2_email_content, 'active_subscription2_email_content', array('media_buttons' => false,  'editor_height' => 150)) ?>    
                                </td>
                            </tr>
                        </tbody>
                        <thead>
                            <tr>
                                <th colspan="2">For Admin</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="tdlabel"><b>Title</b></td>
                                <td>
                                    <input type="text" size="50" name="active_subscription2_admin_email_title" id="active_subscription2_admin_email_title" value="<?php echo $active_subscription2_admin_email_title?>" />
                                </td>
                            </tr>
                            <tr>
                                <td class="tdlabel"><b>Content</b></td>
                                <td>
                                    <?php wp_editor($active_subscription2_admin_email_content, 'active_subscription2_admin_email_content', array('media_buttons' => false,  'editor_height' => 150)) ?>
                                </td>
                            </tr>
                        </tbody>
                        
                    </table>
                </div>
                <div id="inarrears-subscription">
                    <?php
                    $inarrears_subscription_email_title = get_option('inarrears_subscription_email_title');
                    $inarrears_subscription_email_content = get_option('inarrears_subscription_email_content');
                    $inarrears_subscription_admin_email_title = get_option('inarrears_subscription_admin_email_title');
                    $inarrears_subscription_admin_email_content = get_option('inarrears_subscription_admin_email_content');
                    ?>
                    <h3>Subscription is in arrears</h3>
                    <p><b>Short Codes:</b> [name], [email], [website_url], [env], [suite_name], [suite_url], [monthly_fee]</p>
                    <table class="widefat">
                        <thead>
                            <tr>
                                <th colspan="2">For User</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="tdlabel"><b>Title</b></td>
                                <td>
                                    <input type="text" size="50" name="inarrears_subscription_email_title" id="inarrears_subscription_email_title" value="<?php echo $inarrears_subscription_email_title?>" />
                                </td>
                            </tr>
                            <tr>
                                <td class="tdlabel"><b>Content</b></td>
                                <td>
                                    <?php wp_editor($inarrears_subscription_email_content, 'inarrears_subscription_email_content', array('media_buttons' => false,  'editor_height' => 150)) ?>    
                                </td>
                            </tr>
                        </tbody>
                        <thead>
                            <tr>
                                <th colspan="2">For Admin</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="tdlabel"><b>Title</b></td>
                                <td>
                                    <input type="text" size="50" name="inarrears_subscription_admin_email_title" id="inarrears_subscription_admin_email_title" value="<?php echo $inarrears_subscription_admin_email_title?>" />
                                </td>
                            </tr>
                            <tr>
                                <td class="tdlabel"><b>Content</b></td>
                                <td>
                                    <?php wp_editor($inarrears_subscription_admin_email_content, 'inarrears_subscription_admin_email_content', array('media_buttons' => false,  'editor_height' => 150)) ?>
                                </td>
                            </tr>
                        </tbody>
                        
                    </table>
                </div>
                <div id="frozen-subscription">
                    <?php
                    $frozen_subscription_email_title = get_option('frozen_subscription_email_title');
                    $frozen_subscription_email_content = get_option('frozen_subscription_email_content');
                    $frozen_subscription_admin_email_title = get_option('frozen_subscription_admin_email_title');
                    $frozen_subscription_admin_email_content = get_option('frozen_subscription_admin_email_content');
                    ?>
                    <h3>Subscription is frozen</h3>
                    <p><b>Short Codes:</b> [name], [email], [website_url], [env], [suite_name], [suite_url], [monthly_fee]</p>
                    <table class="widefat">
                        <thead>
                            <tr>
                                <th colspan="2">For User</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="tdlabel"><b>Title</b></td>
                                <td>
                                    <input type="text" size="50" name="frozen_subscription_email_title" id="frozen_subscription_email_title" value="<?php echo $frozen_subscription_email_title?>" />
                                </td>
                            </tr>
                            <tr>
                                <td class="tdlabel"><b>Content</b></td>
                                <td>
                                    <?php wp_editor($frozen_subscription_email_content, 'frozen_subscription_email_content', array('media_buttons' => false,  'editor_height' => 150)) ?>    
                                </td>
                            </tr>
                        </tbody>
                        <thead>
                            <tr>
                                <th colspan="2">For Admin</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="tdlabel"><b>Title</b></td>
                                <td>
                                    <input type="text" size="50" name="frozen_subscription_admin_email_title" id="frozen_subscription_admin_email_title" value="<?php echo $frozen_subscription_admin_email_title?>" />
                                </td>
                            </tr>
                            <tr>
                                <td class="tdlabel"><b>Content</b></td>
                                <td>
                                    <?php wp_editor($frozen_subscription_admin_email_content, 'frozen_subscription_admin_email_content', array('media_buttons' => false,  'editor_height' => 150)) ?>
                                </td>
                            </tr>
                        </tbody>
                        
                    </table>
                </div>
                
                <div id="membership-request-received">
                <?php
                    $membership_request_received_admin_email_title = get_option('membership_request_received_admin_email_title');
                    $membership_request_received_admin_email_content = get_option('membership_request_received_admin_email_content');
                ?>
                    <h3>Membership Request Received</h3>
                    <p><b>Short Codes:</b> [name], [email], [website_url], [env], [username], [community], [community_url], [organisation]</p>
                    <table class="widefat">
                        <thead>
                            <tr>
                                <th colspan="2">For Admin</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="tdlabel"><b>Title</b></td>
                                <td>
                                    <input type="text" size="50" name="membership_request_received_admin_email_title" id="membership_request_received_admin_email_title" value="<?php echo $membership_request_received_admin_email_title?>" />
                                </td>
                            </tr>
                            <tr>
                                <td class="tdlabel"><b>Content</b></td>
                                <td>
                                    <?php wp_editor($membership_request_received_admin_email_content, 'membership_request_received_admin_email_content', array('media_buttons' => false,  'editor_height' => 150)) ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div id="membership-request-approved">
                <?php
                    $membership_request_approved_email_title = get_option('membership_request_approved_email_title');
                    $membership_request_approved_email_content = get_option('membership_request_approved_email_content');
                    $membership_request_approved_admin_email_title = get_option('membership_request_approved_admin_email_title');
                    $membership_request_approved_admin_email_content = get_option('membership_request_approved_admin_email_content');
                    
                ?>
                    <h3>Membership Request Approved</h3>
                    <p><b>Short Codes:</b> [name], [email], [website_url], [env], [username], [community], [community_url], [settings_link]</p>
                    <table class="widefat">
                        <thead>
                            <tr>
                                <th colspan="2">For User</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="tdlabel"><b>Title</b></td>
                                <td>
                                    <input type="text" size="50" name="membership_request_approved_email_title" id="membership_request_approved_email_title" value="<?php echo $membership_request_approved_email_title?>" />
                                </td>
                            </tr>
                            <tr>
                                <td class="tdlabel"><b>Content</b></td>
                                <td>
                                    <?php wp_editor($membership_request_approved_email_content, 'membership_request_approved_email_content', array('media_buttons' => false,  'editor_height' => 150)) ?>     
                                </td>
                            </tr>
                        </tbody>
                        <thead>
                            <tr>
                                <th colspan="2">For Admin</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="tdlabel"><b>Title</b></td>
                                <td>
                                    <input type="text" size="50" name="membership_request_approved_admin_email_title" id="membership_request_approved_admin_email_title" value="<?php echo $membership_request_approved_admin_email_title?>" />
                                </td>
                            </tr>
                            <tr>
                                <td class="tdlabel"><b>Content</b></td>
                                <td>
                                    <?php wp_editor($membership_request_approved_admin_email_content, 'membership_request_approved_admin_email_content', array('media_buttons' => false,  'editor_height' => 150)) ?>     
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div id="member-promoted">
                    <?php
                        $member_promoted_email_title = get_option('member_promoted_email_title');
                        $member_promoted_email_content = get_option('member_promoted_email_content');
                        $member_promoted_admin_email_title = get_option('member_promoted_admin_email_title');
                        $member_promoted_admin_email_content = get_option('member_promoted_admin_email_content');

                    ?>
                    <h3>Member Promoted</h3>
                    <p><b>Short Codes:</b> [name], [email], [website_url], [env], [username], [community], [community_url], [settings_link], [member_type]</p>
                    <table class="widefat">
                        <thead>
                            <tr>
                                <th colspan="2">For User</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="tdlabel"><b>Title</b></td>
                                <td>
                                    <input type="text" size="50" name="member_promoted_email_title" id="member_promoted_email_title" value="<?php echo $member_promoted_email_title?>" />
                                </td>
                            </tr>
                            <tr>
                                <td class="tdlabel"><b>Content</b></td>
                                <td>
                                    <?php wp_editor($member_promoted_email_content, 'member_promoted_email_content', array('media_buttons' => false,  'editor_height' => 150)) ?>
                                </td>
                            </tr>
                        </tbody>
                        <thead>
                            <tr>
                                <th colspan="2">For Admin</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="tdlabel"><b>Title</b></td>
                                <td>
                                    <input type="text" size="50" name="member_promoted_admin_email_title" id="member_promoted_admin_email_title" value="<?php echo $member_promoted_admin_email_title?>" />
                                </td>
                            </tr>
                            <tr>
                                <td class="tdlabel"><b>Content</b></td>
                                <td>
                                    <?php wp_editor($member_promoted_admin_email_content, 'member_promoted_admin_email_content', array('media_buttons' => false,  'editor_height' => 150)) ?>
                                </td>
                            </tr>
                        </tbody>
                        
                    </table>
                </div>
                
                <div id="member-leave-community">
                <?php
                    $member_leave_community_admin_email_title = get_option('member_leave_community_admin_email_title');
                    $member_leave_community_admin_email_content = get_option('member_leave_community_admin_email_content');
                ?>
                    <h3>Member Leave Community</h3>
                    <p><b>Short Codes:</b> [name], [email], [website_url], [env], [community], [community_url]</p>
                    <table class="widefat">
                        <thead>
                            <tr>
                                <th colspan="2">For Admin</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="tdlabel"><b>Title</b></td>
                                <td>
                                    <input type="text" size="50" name="member_leave_community_admin_email_title" id="member_leave_community_admin_email_title" value="<?php echo $member_leave_community_admin_email_title?>" />
                                </td>
                            </tr>
                            <tr>
                                <td class="tdlabel"><b>Content</b></td>
                                <td>
                                    <?php wp_editor($member_leave_community_admin_email_content, 'member_leave_community_admin_email_content', array('media_buttons' => false,  'editor_height' => 150)) ?>                
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div id="membership-request-rejected">
                    <?php
                    $membership_request_rejected_email_title = get_option('membership_request_rejected_email_title');
                    $membership_request_rejected_email_content = get_option('membership_request_rejected_email_content');
                    $membership_request_rejected_admin_email_title = get_option('membership_request_rejected_admin_email_title');
                    $membership_request_rejected_admin_email_content = get_option('membership_request_rejected_admin_email_content');

                    ?>
                    <h3>Membership Request Rejected</h3>
                    <p><b>Short Codes:</b> [name], [email], [website_url], [env], [username], [community], [community_url]</p>
                    <table class="widefat">
                        <thead>
                        <tr>
                            <th colspan="2">For User</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td class="tdlabel"><b>Title</b></td>
                            <td>
                                <input type="text" size="50" name="membership_request_rejected_email_title" id="membership_request_rejected_email_title" value="<?php echo $membership_request_rejected_email_title?>" />
                            </td>
                        </tr>
                        <tr>
                            <td class="tdlabel"><b>Content</b></td>
                            <td>
                                <?php wp_editor($membership_request_rejected_email_content, 'membership_request_rejected_email_content', array('media_buttons' => false,  'editor_height' => 150)) ?>
                            </td>
                        </tr>
                        </tbody>
                        <thead>
                        <tr>
                            <th colspan="2">For Admin</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td class="tdlabel"><b>Title</b></td>
                            <td>
                                <input type="text" size="50" name="membership_request_rejected_admin_email_title" id="membership_request_rejected_admin_email_title" value="<?php echo $membership_request_rejected_admin_email_title?>" />
                            </td>
                        </tr>
                        <tr>
                            <td class="tdlabel"><b>Content</b></td>
                            <td>
                                <?php wp_editor($membership_request_rejected_admin_email_content, 'membership_request_rejected_admin_email_content', array('media_buttons' => false,  'editor_height' => 150)) ?>
                            </td>
                        </tr>
                        </tbody>

                    </table>
                </div>


                <div id="remove-member-from-community">
                <?php
                    $remove_member_email_title = get_option('remove_member_email_title');
                    $remove_member_email_content = get_option('remove_member_email_content');
                    $remove_member_admin_email_title = get_option('remove_member_admin_email_title');
                    $remove_member_admin_email_content = get_option('remove_member_admin_email_content');
                    
                ?>
                    <h3>Remove Member From Community</h3>
                    <p><b>Short Codes:</b> [name], [email], [website_url], [env], [username], [community], [community_url]</p>
                    <table class="widefat">
                        <thead>
                            <tr>
                                <th colspan="2">For User</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="tdlabel"><b>Title</b></td>
                                <td>
                                    <input type="text" size="50" name="remove_member_email_title" id="remove_member_email_title" value="<?php echo $remove_member_email_title?>" />
                                </td>
                            </tr>
                            <tr>
                                <td class="tdlabel"><b>Content</b></td>
                                <td>
                                    <?php wp_editor($remove_member_email_content, 'remove_member_email_content', array('media_buttons' => false,  'editor_height' => 150)) ?>     
                                </td>
                            </tr>
                        </tbody>
                        <thead>
                            <tr>
                                <th colspan="2">For Admin</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="tdlabel"><b>Title</b></td>
                                <td>
                                    <input type="text" size="50" name="remove_member_admin_email_title" id="remove_member_admin_email_title" value="<?php echo $remove_member_admin_email_title?>" />
                                </td>
                            </tr>
                            <tr>
                                <td class="tdlabel"><b>Content</b></td>
                                <td>
                                    <?php wp_editor($remove_member_admin_email_content, 'remove_member_admin_email_content', array('media_buttons' => false,  'editor_height' => 150)) ?>     
                                </td>
                            </tr>
                        </tbody>
                        
                    </table>
                </div>
                <div id="forgot-password">
                <?php
                    $forgot_password_email_title = get_option('forgot_password_email_title');
                    $forgot_password_email_content = get_option('forgot_password_email_content');
                ?>
                    <h3>Forgot Password</h3>
                    <p><b>Short Codes:</b> [name], [email], [website_url], [env], [username], [link]</p>
                    <table class="widefat">
                        <thead>
                            <tr>
                                <th colspan="2">For User</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="tdlabel"><b>Title</b></td>
                                <td>
                                    <input type="text" size="50" name="forgot_password_email_title" id="forgot_password_email_title" value="<?php echo $forgot_password_email_title?>" />
                                </td>
                            </tr>
                            <tr>
                                <td class="tdlabel"><b>Content</b></td>
                                <td>
                                <?php wp_editor($forgot_password_email_content, 'forgot_password_email_content', array('media_buttons' => false,  'editor_height' => 150)) ?>                
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div id="password-changed">
                    <?php
                    $password_changed_email_title = get_option('password_changed_email_title');
                    $password_changed_email_content = get_option('password_changed_email_content');
                    $password_changed_admin_email_title = get_option('password_changed_admin_email_title');
                    $password_changed_admin_email_content = get_option('password_changed_admin_email_content');
                    ?>
                    <h3>Password Changed</h3>
                    <p><b>Short Codes:</b> [name], [email], [website_url], [env], [username]</p>
                    <table class="widefat">
                        <thead>
                            <tr>
                                <th colspan="2">For User</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="tdlabel"><b>Title</b></td>
                                <td>
                                    <input type="text" size="50" name="password_changed_email_title" id="password_changed_email_title" value="<?php echo $password_changed_email_title?>" />
                                </td>
                            </tr>
                            <tr>
                                <td class="tdlabel"><b>Content</b></td>
                                <td>
                                    <?php wp_editor($password_changed_email_content, 'password_changed_email_content', array('media_buttons' => false,  'editor_height' => 150)) ?>    
                                </td>
                            </tr>
                        </tbody>
                        <thead>
                            <tr>
                                <th colspan="2">For Admin</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="tdlabel"><b>Title</b></td>
                                <td>
                                    <input type="text" size="50" name="password_changed_admin_email_title" id="password_changed_admin_email_title" value="<?php echo $password_changed_admin_email_title?>" />
                                </td>
                            </tr>
                            <tr>
                                <td class="tdlabel"><b>Content</b></td>
                                <td>
                                    <?php wp_editor($password_changed_admin_email_content, 'password_changed_admin_email_content', array('media_buttons' => false,  'editor_height' => 150)) ?>
                                </td>
                            </tr>
                        </tbody>
                        
                    </table>
                </div>
                <div id="suite-changed">
                    <?php
                    $suite_changed_email_title = get_option('suite_changed_email_title');
                    $suite_changed_email_content = get_option('suite_changed_email_content');
                    ?>
                    <h3>Test Suite Changed</h3>
                    <p><b>Short Codes:</b> [name], [website_url], [env], [community], [community_url], [suite_name], [suite_url], [editor_name]</p>
                    <table class="widefat">
                        <thead>
                            <tr>
                                <th colspan="2">For User</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="tdlabel"><b>Title</b></td>
                                <td>
                                    <input type="text" size="50" name="suite_changed_email_title" id="suite_changed_email_title" value="<?php echo $suite_changed_email_title?>" />
                                </td>
                            </tr>
                            <tr>
                                <td class="tdlabel"><b>Content</b></td>
                                <td>
                                    <?php wp_editor($suite_changed_email_content, 'suite_changed_email_content', array('media_buttons' => false,  'editor_height' => 150)) ?>    
                                </td>
                            </tr>
                        </tbody>
                        
                    </table>
                </div>
                <div id="case-changed">
                    <?php
                    $case_changed_email_title = get_option('case_changed_email_title');
                    $case_changed_email_content = get_option('case_changed_email_content');
                    ?>
                    <h3>Test Case Changed</h3>
                    <p><b>Short Codes:</b> [name], [website_url], [env], [suite_name], [suite_url], [case_name], [case_url], [editor_name]</p>
                    <table class="widefat">
                        <thead>
                            <tr>
                                <th colspan="2">For User</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="tdlabel"><b>Title</b></td>
                                <td>
                                    <input type="text" size="50" name="case_changed_email_title" id="case_changed_email_title" value="<?php echo $case_changed_email_title?>" />
                                </td>
                            </tr>
                            <tr>
                                <td class="tdlabel"><b>Content</b></td>
                                <td>
                                    <?php wp_editor($case_changed_email_content, 'case_changed_email_content', array('media_buttons' => false,  'editor_height' => 150)) ?>    
                                </td>
                            </tr>
                        </tbody>
                        
                    </table>
                </div>
                
                <div id="ticket-created">
                    <?php
                    $ticket_created_email_title = get_option('ticket_created_email_title');
                    $ticket_created_email_content = get_option('ticket_created_email_content');
                    $ticket_created_support_email_title = get_option('ticket_created_support_email_title');
                    $ticket_created_support_email_content = get_option('ticket_created_support_email_content');                    
                    $ticket_created_admin_email_title = get_option('ticket_created_admin_email_title');
                    $ticket_created_admin_email_content = get_option('ticket_created_admin_email_content');                    
                    ?>           
                    <h3>Ticket Created</h3>         
                    <table border="0">
                        <tr>
                            <td rowspan="2" valign="top"><b>Short Codes:</b></td>
                            <td>
                                [website_url], [env], [customer_name], [customer_email], [token_price]<small>($/token)</small> <br />
                                [ticket_id], [ticket_title], [ticket_url], [ticket_type], [ticket_status], [ticket_priority], [ticket_content] <br />
                                [time_to_pay], [time_to_response], [time_to_resolve], [hourly_price]<small>(Tokens/hr)</small>, [ticket_total_price]<small>(Total Tokens)</small>
                            </td>
                        </tr>
                    </table>
                    <table class="widefat">
                        <thead>
                            <tr>
                                <th colspan="2">For User</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="tdlabel"><b>Title</b></td>
                                <td>
                                    <input type="text" size="50" name="ticket_created_email_title" id="ticket_created_email_title" value="<?php echo $ticket_created_email_title?>" />
                                </td>
                            </tr>
                            <tr>
                                <td class="tdlabel"><b>Content</b></td>
                                <td>
                                    <?php wp_editor($ticket_created_email_content, 'ticket_created_email_content', array('media_buttons' => false, 'editor_height' => 150)) ?>    
                                </td>
                            </tr>
                        </tbody>
                        <thead>
                            <tr>
                                <th colspan="2">For Support</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="tdlabel"><b>Title</b></td>
                                <td>
                                    <input type="text" size="50" name="ticket_created_support_email_title" id="ticket_created_support_email_title" value="<?php echo $ticket_created_support_email_title?>" />
                                </td>
                            </tr>
                            <tr>
                                <td class="tdlabel"><b>Content</b></td>
                                <td>
                                    <?php wp_editor($ticket_created_support_email_content, 'ticket_created_support_email_content', array('media_buttons' => false,  'editor_height' => 150)) ?>    
                                </td>
                            </tr>
                        </tbody>
                        <thead>
                            <tr>
                                <th colspan="2">For Admin</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="tdlabel"><b>Title</b></td>
                                <td>
                                    <input type="text" size="50" name="ticket_created_admin_email_title" id="ticket_created_admin_email_title" value="<?php echo $ticket_created_admin_email_title?>" />
                                </td>
                            </tr>
                            <tr>
                                <td class="tdlabel"><b>Content</b></td>
                                <td>
                                    <?php wp_editor($ticket_created_admin_email_content, 'ticket_created_admin_email_content', array('media_buttons' => false,  'editor_height' => 150)) ?>    
                                </td>
                            </tr>
                        </tbody>
                        
                    </table>
                </div>
                
                <div id="ticket-updated">
                    <?php
                    $ticket_updated_email_title = get_option('ticket_updated_email_title');
                    $ticket_updated_email_content = get_option('ticket_updated_email_content');                    
                    $ticket_updated_support_email_title = get_option('ticket_updated_support_email_title');
                    $ticket_updated_support_email_content = get_option('ticket_updated_support_email_content');
                    $ticket_updated_admin_email_title = get_option('ticket_updated_admin_email_title');
                    $ticket_updated_admin_email_content = get_option('ticket_updated_admin_email_content');
                    ?>
                    <h3>Ticket Updated</h3>                    
                    <table border="0">
                        <tr>
                            <td rowspan="2" valign="top"><b>Short Codes:</b></td>
                            <td>
                                [website_url], [env], [customer_name], [customer_email], [support_name], [support_email], [token_price]<small>($/token)</small><br />
                                [ticket_id], [ticket_title], [ticket_url], [ticket_type], [ticket_status], [ticket_priority], [ticket_content], [message]<small>(New Message)</small> <br />
                                [time_to_pay], [time_to_response], [time_to_resolve], [hourly_price]<small>(Tokens/hr)</small>, [ticket_total_price]<small>(Total Tokens)</small>
                            </td>
                        </tr>
                    </table>
                    <table class="widefat">
                        <thead>
                            <tr>
                                <th colspan="2">For User</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="tdlabel"><b>Title</b></td>
                                <td>
                                    <input type="text" size="50" name="ticket_updated_email_title" id="ticket_updated_email_title" value="<?php echo $ticket_updated_email_title?>" />
                                </td>
                            </tr>
                            <tr>
                                <td class="tdlabel"><b>Content</b></td>
                                <td>
                                    <?php wp_editor($ticket_updated_email_content, 'ticket_updated_email_content', array('media_buttons' => false,  'editor_height' => 150)) ?>    
                                </td>
                            </tr>
                        </tbody>
                        <thead>
                            <tr>
                                <th colspan="2">For Support</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="tdlabel"><b>Title</b></td>
                                <td>
                                    <input type="text" size="50" name="ticket_updated_support_email_title" id="ticket_updated_support_email_title" value="<?php echo $ticket_updated_support_email_title?>" />
                                </td>
                            </tr>
                            <tr>
                                <td class="tdlabel"><b>Content</b></td>
                                <td>
                                    <?php wp_editor($ticket_updated_support_email_content, 'ticket_updated_support_email_content', array('media_buttons' => false,  'editor_height' => 150)) ?>    
                                </td>
                            </tr>
                        </tbody>
                         <thead>
                            <tr>
                                <th colspan="2">For Admin</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="tdlabel"><b>Title</b></td>
                                <td>
                                    <input type="text" size="50" name="ticket_updated_admin_email_title" id="ticket_updated_admin_email_title" value="<?php echo $ticket_updated_admin_email_title?>" />
                                </td>
                            </tr>
                            <tr>
                                <td class="tdlabel"><b>Content</b></td>
                                <td>
                                    <?php wp_editor($ticket_updated_admin_email_content, 'ticket_updated_admin_email_content', array('media_buttons' => false,  'editor_height' => 150)) ?>    
                                </td>
                            </tr>
                        </tbody>
                        
                    </table>
                </div>
                
                <div id="ticket-started">
                    <?php
                    $ticket_started_email_title = get_option('ticket_started_email_title');
                    $ticket_started_email_content = get_option('ticket_started_email_content');                    
                    
                    $ticket_started_support_email_title = get_option('ticket_started_support_email_title');
                    $ticket_started_support_email_content = get_option('ticket_started_support_email_content');                    
                    
                    $ticket_started_admin_email_title = get_option('ticket_started_admin_email_title');
                    $ticket_started_admin_email_content = get_option('ticket_started_admin_email_content');                    
                    
                    ?>
                    <h3>Ticket Started</h3>
                    <table border="0">
                        <tr>
                            <td rowspan="2" valign="top"><b>Short Codes:</b></td>
                            <td>
                                [website_url], [env], [customer_name], [customer_email], [support_name], [support_email], [token_price]<small>($/token)</small><br />
                                [ticket_id], [ticket_title], [ticket_url], [ticket_type], [ticket_status], [ticket_priority], [ticket_content], [message]<small>(New Message)</small> <br />
                                [time_to_pay], [time_to_response], [time_to_resolve], [hourly_price]<small>(Tokens/hr)</small>, [ticket_total_price]<small>(Total Tokens)</small>
                            </td>
                        </tr>
                    </table>
                    <table class="widefat">
                        <thead>
                            <tr>
                                <th colspan="2">For User</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="tdlabel"><b>Title</b></td>
                                <td>
                                    <input type="text" size="50" name="ticket_started_email_title" id="ticket_started_email_title" value="<?php echo $ticket_started_email_title?>" />
                                </td>
                            </tr>
                            <tr>
                                <td class="tdlabel"><b>Content</b></td>
                                <td>
                                    <?php wp_editor($ticket_started_email_content, 'ticket_started_email_content', array('media_buttons' => false,  'editor_height' => 150)) ?>    
                                </td>
                            </tr>
                        </tbody>
                        <thead>
                            <tr>
                                <th colspan="2">For Support</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="tdlabel"><b>Title</b></td>
                                <td>
                                    <input type="text" size="50" name="ticket_started_support_email_title" id="ticket_started_support_email_title" value="<?php echo $ticket_started_support_email_title?>" />
                                </td>
                            </tr>
                            <tr>
                                <td class="tdlabel"><b>Content</b></td>
                                <td>
                                    <?php wp_editor($ticket_started_support_email_content, 'ticket_started_support_email_content', array('media_buttons' => false,  'editor_height' => 150)) ?>    
                                </td>
                            </tr>
                        </tbody>
                        <thead>
                            <tr>
                                <th colspan="2">For Admin</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="tdlabel"><b>Title</b></td>
                                <td>
                                    <input type="text" size="50" name="ticket_started_admin_email_title" id="ticket_started_admin_email_title" value="<?php echo $ticket_started_admin_email_title?>" />
                                </td>
                            </tr>
                            <tr>
                                <td class="tdlabel"><b>Content</b></td>
                                <td>
                                    <?php wp_editor($ticket_started_admin_email_content, 'ticket_started_admin_email_content', array('media_buttons' => false,  'editor_height' => 150)) ?>    
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <div id="ticket-solved">
                    <?php
                    $ticket_solved_email_title = get_option('ticket_solved_email_title');
                    $ticket_solved_email_content = get_option('ticket_solved_email_content');
                    $ticket_solved_support_email_title = get_option('ticket_solved_support_email_title');
                    $ticket_solved_support_email_content = get_option('ticket_solved_support_email_content');
                    $ticket_solved_admin_email_title = get_option('ticket_solved_admin_email_title');
                    $ticket_solved_admin_email_content = get_option('ticket_solved_admin_email_content');
                    
                    ?>
                    <h3>Ticket Resolved</h3>
                    <table border="0">
                        <tr>
                            <td rowspan="2" valign="top"><b>Short Codes:</b></td>
                            <td>
                                [website_url], [env], [customer_name], [customer_email], [support_name], [support_email], [token_price]<small>($/token)</small><br />
                                [ticket_id], [ticket_title], [ticket_url], [ticket_type], [ticket_status], [ticket_priority], [ticket_content], [message]<small>(New Message)</small> <br />
                                [time_to_pay], [time_to_response], [time_to_resolve], [hourly_price]<small>(Tokens/hr)</small>, [ticket_total_price]<small>(Total Tokens)</small>
                            </td>
                        </tr>
                    </table>
                    <table class="widefat">
                        <thead>
                            <tr>
                                <th colspan="2">For User</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="tdlabel"><b>Title</b></td>
                                <td>
                                    <input type="text" size="50" name="ticket_solved_email_title" id="ticket_solved_email_title" value="<?php echo $ticket_solved_email_title?>" />
                                </td>
                            </tr>
                            <tr>
                                <td class="tdlabel"><b>Content</b></td>
                                <td>
                                    <?php wp_editor($ticket_solved_email_content, 'ticket_solved_email_content', array('media_buttons' => false,  'editor_height' => 150)) ?>    
                                </td>
                            </tr>
                        </tbody>
                        <thead>
                            <tr>
                                <th colspan="2">For Support</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="tdlabel"><b>Title</b></td>
                                <td>
                                    <input type="text" size="50" name="ticket_solved_support_email_title" id="ticket_solved_support_email_title" value="<?php echo $ticket_solved_support_email_title?>" />
                                </td>
                            </tr>
                            <tr>
                                <td class="tdlabel"><b>Content</b></td>
                                <td>
                                    <?php wp_editor($ticket_solved_support_email_content, 'ticket_solved_support_email_content', array('media_buttons' => false,  'editor_height' => 150)) ?>    
                                </td>
                            </tr>
                        </tbody>
                        <thead>
                            <tr>
                                <th colspan="2">For Admin</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="tdlabel"><b>Title</b></td>
                                <td>
                                    <input type="text" size="50" name="ticket_solved_admin_email_title" id="ticket_solved_admin_email_title" value="<?php echo $ticket_solved_admin_email_title?>" />
                                </td>
                            </tr>
                            <tr>
                                <td class="tdlabel"><b>Content</b></td>
                                <td>
                                    <?php wp_editor($ticket_solved_admin_email_content, 'ticket_solved_admin_email_content', array('media_buttons' => false,  'editor_height' => 150)) ?>    
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <div id="ticket-closed">
                    <?php
                    $ticket_closed_email_title = get_option('ticket_closed_email_title');
                    $ticket_closed_email_content = get_option('ticket_closed_email_content');
                    $ticket_closed_admin_email_title = get_option('ticket_closed_admin_email_title');
                    $ticket_closed_admin_email_content = get_option('ticket_closed_admin_email_content');
                    ?>
                    <h3>Ticket Closed</h3>
                    <table border="0">
                        <tr>
                            <td rowspan="2" valign="top"><b>Short Codes:</b></td>
                            <td>
                                [website_url], [env], [customer_name], [customer_email], [support_name], [support_email], [token_price]<small>($/token)</small><br />
                                [ticket_id], [ticket_title], [ticket_url], [ticket_type], [ticket_status], [ticket_priority], [ticket_content], [message]<small>(New Message)</small> <br />
                                [time_to_pay], [time_to_response], [time_to_resolve], [hourly_price]<small>(Tokens/hr)</small>, [ticket_total_price]<small>(Total Tokens)</small>
                            </td>
                        </tr>
                    </table>
                    <table class="widefat">
                        <thead>
                            <tr>
                                <th colspan="2">For User</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="tdlabel"><b>Title</b></td>
                                <td>
                                    <input type="text" size="50" name="ticket_closed_email_title" id="ticket_closed_email_title" value="<?php echo $ticket_closed_email_title?>" />
                                </td>
                            </tr>
                            <tr>
                                <td class="tdlabel"><b>Content</b></td>
                                <td>
                                    <?php wp_editor($ticket_closed_email_content, 'ticket_closed_email_content', array('media_buttons' => false,  'editor_height' => 150)) ?>    
                                </td>
                            </tr>
                        </tbody>
                        <thead>
                            <tr>
                                <th colspan="2">For Admin</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="tdlabel"><b>Title</b></td>
                                <td>
                                    <input type="text" size="50" name="ticket_closed_admin_email_title" id="ticket_closed_admin_email_title" value="<?php echo $ticket_closed_admin_email_title?>" />
                                </td>
                            </tr>
                            <tr>
                                <td class="tdlabel"><b>Content</b></td>
                                <td>
                                    <?php wp_editor($ticket_closed_admin_email_content, 'ticket_closed_admin_email_content', array('media_buttons' => false,  'editor_height' => 150)) ?>    
                                </td>
                            </tr>
                        </tbody>
                        
                    </table>
                </div>
                
                
                <div id="ticket-payment-processed1">
                    <?php
                    $ticket_payment_processed_by_card_email_title = get_option('ticket_payment_processed_by_card_email_title');
                    $ticket_payment_processed_by_card_email_content = get_option('ticket_payment_processed_by_card_email_content');
                    $ticket_payment_processed_by_card_support_email_title = get_option('ticket_payment_processed_by_card_support_email_title');
                    $ticket_payment_processed_by_card_support_email_content = get_option('ticket_payment_processed_by_card_support_email_content');
                    $ticket_payment_processed_by_card_admin_email_title = get_option('ticket_payment_processed_by_card_admin_email_title');
                    $ticket_payment_processed_by_card_admin_email_content = get_option('ticket_payment_processed_by_card_admin_email_content');
                    ?>
                    <h3>Payment processed by using customer credit cards</h3>
                    <table border="0">
                        <tr>
                            <td rowspan="2" valign="top"><b>Short Codes:</b></td>
                            <td>
                                [website_url], [env], [customer_name], [customer_email], [support_name], [support_email], [token_price]<small>($/token)</small><br />
                                [ticket_id], [ticket_title], [ticket_url], [ticket_type], [ticket_status], [ticket_priority], [ticket_content], [message]<small>(New Message)</small> <br />
                                [time_to_pay], [time_to_response], [time_to_resolve], [hourly_price]<small>(Tokens/hr)</small>, [ticket_total_price]<small>(Total Tokens)</small><br />
                                [paid_tokens]<small>($)</small>, [paid_amount]<small>($)</small>
                            </td>
                        </tr>
                    </table>
                    <table class="widefat">
                        <thead>
                            <tr>
                                <th colspan="2">For User</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="tdlabel"><b>Title</b></td>
                                <td>
                                    <input type="text" size="50" name="ticket_payment_processed_by_card_email_title" id="ticket_payment_processed_by_card_email_title" value="<?php echo $ticket_payment_processed_by_card_email_title?>" />
                                </td>
                            </tr>
                            <tr>
                                <td class="tdlabel"><b>Content</b></td>
                                <td>
                                    <?php wp_editor($ticket_payment_processed_by_card_email_content, 'ticket_payment_processed_by_card_email_content', array('media_buttons' => false,  'editor_height' => 150)) ?>    
                                </td>
                            </tr>
                        </tbody>
                        <thead>
                            <tr>
                                <th colspan="2">For Support</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="tdlabel"><b>Title</b></td>
                                <td>
                                    <input type="text" size="50" name="ticket_payment_processed_by_card_support_email_title" id="ticket_payment_processed_by_card_support_email_title" value="<?php echo $ticket_payment_processed_by_card_support_email_title?>" />
                                </td>
                            </tr>
                            <tr>
                                <td class="tdlabel"><b>Content</b></td>
                                <td>
                                    <?php wp_editor($ticket_payment_processed_by_card_support_email_content, 'ticket_payment_processed_by_card_support_email_content', array('media_buttons' => false,  'editor_height' => 150)) ?>    
                                </td>
                            </tr>
                        </tbody>
                        <thead>
                            <tr>
                                <th colspan="2">For Admin</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="tdlabel"><b>Title</b></td>
                                <td>
                                    <input type="text" size="50" name="ticket_payment_processed_by_card_admin_email_title" id="ticket_payment_processed_by_card_admin_email_title" value="<?php echo $ticket_payment_processed_by_card_admin_email_title?>" />
                                </td>
                            </tr>
                            <tr>
                                <td class="tdlabel"><b>Content</b></td>
                                <td>
                                    <?php wp_editor($ticket_payment_processed_by_card_admin_email_content, 'ticket_payment_processed_by_card_admin_email_content', array('media_buttons' => false,  'editor_height' => 150)) ?>    
                                </td>
                            </tr>
                        </tbody>
                        
                    </table>
                </div>
                
                <div id="ticket-payment-processed2">
                    <?php
                    $ticket_payment_processed_by_tokens_email_title = get_option('ticket_payment_processed_by_tokens_email_title');
                    $ticket_payment_processed_by_tokens_email_content = get_option('ticket_payment_processed_by_tokens_email_content');
                    $ticket_payment_processed_by_tokens_support_email_title = get_option('ticket_payment_processed_by_tokens_support_email_title');
                    $ticket_payment_processed_by_tokens_support_email_content = get_option('ticket_payment_processed_by_tokens_support_email_content');
                    $ticket_payment_processed_by_tokens_admin_email_title = get_option('ticket_payment_processed_by_tokens_admin_email_title');
                    $ticket_payment_processed_by_tokens_admin_email_content = get_option('ticket_payment_processed_by_tokens_admin_email_content');
                    ?>
                    <h3>Payment processed by using prepurchased tokens</h3>
                    <table border="0">
                        <tr>
                            <td rowspan="2" valign="top"><b>Short Codes:</b></td>
                            <td>
                                [website_url], [env], [customer_name], [customer_email], [support_name], [support_email], [token_price]<small>($/token)</small><br />
                                [ticket_id], [ticket_title], [ticket_url], [ticket_type], [ticket_status], [ticket_priority], [ticket_content], [message]<small>(New Message)</small> <br />
                                [time_to_pay], [time_to_response], [time_to_resolve], [hourly_price]<small>(Tokens/hr)</small>, [ticket_total_price]<small>(Total Tokens)</small><br />
                                [purchased_tokens], [paid_tokens], [remained_tokens]
                            </td>
                        </tr>
                    </table>
                    <table class="widefat">
                        <thead>
                            <tr>
                                <th colspan="2">For User</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="tdlabel"><b>Title</b></td>
                                <td>
                                    <input type="text" size="50" name="ticket_payment_processed_by_tokens_email_title" id="ticket_payment_processed_by_tokens_email_title" value="<?php echo $ticket_payment_processed_by_tokens_email_title?>" />
                                </td>
                            </tr>
                            <tr>
                                <td class="tdlabel"><b>Content</b></td>
                                <td>
                                    <?php wp_editor($ticket_payment_processed_by_tokens_email_content, 'ticket_payment_processed_by_tokens_email_content', array('media_buttons' => false,  'editor_height' => 150)) ?>    
                                </td>
                            </tr>
                        </tbody>
                        <thead>
                            <tr>
                                <th colspan="2">For Support</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="tdlabel"><b>Title</b></td>
                                <td>
                                    <input type="text" size="50" name="ticket_payment_processed_by_tokens_support_email_title" id="ticket_payment_processed_by_tokens_support_email_title" value="<?php echo $ticket_payment_processed_by_tokens_support_email_title?>" />
                                </td>
                            </tr>
                            <tr>
                                <td class="tdlabel"><b>Content</b></td>
                                <td>
                                    <?php wp_editor($ticket_payment_processed_by_tokens_support_email_content, 'ticket_payment_processed_by_tokens_support_email_content', array('media_buttons' => false,  'editor_height' => 150)) ?>    
                                </td>
                            </tr>
                        </tbody>
                        <thead>
                            <tr>
                                <th colspan="2">For Admin</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="tdlabel"><b>Title</b></td>
                                <td>
                                    <input type="text" size="50" name="ticket_payment_processed_by_tokens_admin_email_title" id="ticket_payment_processed_by_tokens_admin_email_title" value="<?php echo $ticket_payment_processed_by_tokens_admin_email_title?>" />
                                </td>
                            </tr>
                            <tr>
                                <td class="tdlabel"><b>Content</b></td>
                                <td>
                                    <?php wp_editor($ticket_payment_processed_by_tokens_admin_email_content, 'ticket_payment_processed_by_tokens_admin_email_content', array('media_buttons' => false,  'editor_height' => 150)) ?>    
                                </td>
                            </tr>
                        </tbody>
                        
                    </table>
                </div>
                
                <!-- Begin Forum -->
                <div id="forum-new-post">
                    <?php
                    $forum_new_post_email_title = get_option('forum_new_post_email_title');
                    $forum_new_post_email_content = get_option('forum_new_post_email_content');
                    ?>
                    <h3>Forum New Post</h3>
                    <p><b>Short Codes:</b> [name], [username], [email], [website_url], [env], [topic_author_name], [topic_title], [topic_content], [community], [topic_url]</p>
                    <table class="widefat">
                        <thead>
                            <tr>
                                <th colspan="2">For User</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="tdlabel"><b>Title</b></td>
                                <td>
                                    <input type="text" size="50" name="forum_new_post_email_title" id="forum_new_post_email_title" value="<?php echo $forum_new_post_email_title?>" />
                                </td>
                            </tr>
                            <tr>
                                <td class="tdlabel"><b>Content</b></td>
                                <td>
                                    <?php wp_editor($forum_new_post_email_content, 'forum_new_post_email_content', array('media_buttons' => false,  'editor_height' => 150)) ?>    
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div id="forum-reply-post">
                    <?php
                    $forum_reply_post_email_title = get_option('forum_reply_post_email_title');
                    $forum_reply_post_email_content = get_option('forum_reply_post_email_content');
                    ?>
                    <h3>Form Reply Post</h3>
                    <p><b>Short Codes:</b> [name], [username], [email], [website_url], [env], [reply_author_name], [topic_title], [reply_content], [community], [reply_url]</p>
                    <table class="widefat">
                        <thead>
                            <tr>
                                <th colspan="2">For User</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="tdlabel"><b>Title</b></td>
                                <td>
                                    <input type="text" size="50" name="forum_reply_post_email_title" id="forum_reply_post_email_title" value="<?php echo $forum_reply_post_email_title?>" />
                                </td>
                            </tr>
                            <tr>
                                <td class="tdlabel"><b>Content</b></td>
                                <td>
                                    <?php wp_editor($forum_reply_post_email_content, 'forum_reply_post_email_content', array('media_buttons' => false,  'editor_height' => 150)) ?>    
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-- End Forum -->
                <div id="claim-created">
                    <?php
                    $claim_created_admin_email_title = get_option('claim_created_admin_email_title');
                    $claim_created_admin_email_content = get_option('claim_created_admin_email_content');
                    ?>
                    <h3>Claim is made</h3>
                    <p><b>Short Codes:</b> [claim_id], [product], [username], [useremail], [issuer], [certificate], [test_suite], [conformance_level], [role], [status], [date]</p>
                    <table class="widefat">
                        <thead>
                            <tr>
                                <th colspan="2">For User</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="tdlabel"><b>Title</b></td>
                                <td>
                                    <input type="text" size="50" name="claim_created_admin_email_title" id="claim_created_admin_email_title" value="<?php echo $claim_created_admin_email_title?>" />
                                </td>
                            </tr>
                            <tr>
                                <td class="tdlabel"><b>Content</b></td>
                                <td>
                                    <?php wp_editor($claim_created_admin_email_content, 'claim_created_admin_email_content', array('media_buttons' => false,  'editor_height' => 150)) ?>    
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-- End Claim -->
                
                </div>
            </div>
          </div>
          
      </form>
      <script type="text/javascript">
            jQuery(document).ready(function(){
                jQuery('#emails').tabs({"active": "<?php echo isset($_REQUEST['tab']) ? $_REQUEST['tab'] : 0?>"});                    
            })
            function saveEmailTemplates()
            {
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
    if(isset($_POST['email-templates']) && $_POST['email-templates'] == 'Save Changes')
    {
        check_admin_referer('save-email-templates');
        
          update_option('support_name', $_POST['support_name']);          
          update_option('support_email', $_POST['support_email']);          
          update_option('env', $_POST['env']);          
        
          $new_user_email_title = htmlentities(stripslashes_deep($_POST['new_user_email_title']));          
          update_option('new_user_email_title', $new_user_email_title);          
          $new_user_email_content = stripslashes_deep($_POST['new_user_email_content']);          
          update_option('new_user_email_content', $new_user_email_content);          
          $new_user_admin_email_title = htmlentities(stripslashes_deep($_POST['new_user_admin_email_title']));          
          update_option('new_user_admin_email_title', $new_user_admin_email_title);          
          $new_user_admin_email_content = stripslashes_deep($_POST['new_user_admin_email_content']);          
          update_option('new_user_admin_email_content', $new_user_admin_email_content);
          
          $email_changed_email_title = htmlentities(stripslashes_deep($_POST['email_changed_email_title']));          
          update_option('email_changed_email_title', $email_changed_email_title);          
          $email_changed_email_content = stripslashes_deep($_POST['email_changed_email_content']);          
          update_option('email_changed_email_content', $email_changed_email_content);          
          $email_changed_admin_email_title = htmlentities(stripslashes_deep($_POST['email_changed_admin_email_title']));          
          update_option('email_changed_admin_email_title', $email_changed_admin_email_title);          
          $email_changed_admin_email_content = stripslashes_deep($_POST['email_changed_admin_email_content']);          
          update_option('email_changed_admin_email_content', $email_changed_admin_email_content);

          $changed_email_verify_success_email_title = htmlentities(stripslashes_deep($_POST['changed_email_verify_success_email_title']));          
          update_option('changed_email_verify_success_email_title', $changed_email_verify_success_email_title);          
          $changed_email_verify_success_email_content = stripslashes_deep($_POST['changed_email_verify_success_email_content']);          
          update_option('changed_email_verify_success_email_content', $changed_email_verify_success_email_content);          
          $changed_email_verify_success_admin_email_title = htmlentities(stripslashes_deep($_POST['changed_email_verify_success_admin_email_title']));          
          update_option('changed_email_verify_success_admin_email_title', $changed_email_verify_success_admin_email_title);          
          $changed_email_verify_success_admin_email_content = stripslashes_deep($_POST['changed_email_verify_success_admin_email_content']);          
          update_option('changed_email_verify_success_admin_email_content', $changed_email_verify_success_admin_email_content);
          
          $user_verify_success_email_title = htmlentities(stripslashes_deep($_POST['user_verify_success_email_title']));          
          update_option('user_verify_success_email_title', $user_verify_success_email_title);          
          $user_verify_success_email_content = stripslashes_deep($_POST['user_verify_success_email_content']);          
          update_option('user_verify_success_email_content', $user_verify_success_email_content);          
          $user_verify_success_admin_email_title = htmlentities(stripslashes_deep($_POST['user_verify_success_admin_email_title']));          
          update_option('user_verify_success_admin_email_title', $user_verify_success_admin_email_title);          
          $user_verify_success_admin_email_content = stripslashes_deep($_POST['user_verify_success_admin_email_content']);          
          update_option('user_verify_success_admin_email_content', $user_verify_success_admin_email_content);          
          
          
          $purchase_subscription_email_title = htmlentities(stripslashes_deep($_POST['purchase_subscription_email_title']));          
          update_option('purchase_subscription_email_title', $purchase_subscription_email_title);          
          $purchase_subscription_email_content = stripslashes_deep($_POST['purchase_subscription_email_content']);          
          update_option('purchase_subscription_email_content', $purchase_subscription_email_content);          
          $purchase_subscription_admin_email_title = htmlentities(stripslashes_deep($_POST['purchase_subscription_admin_email_title']));          
          update_option('purchase_subscription_admin_email_title', $purchase_subscription_admin_email_title);          
          $purchase_subscription_admin_email_content = stripslashes_deep($_POST['purchase_subscription_admin_email_content']);          
          update_option('purchase_subscription_admin_email_content', $purchase_subscription_admin_email_content);
          
          $purchase_signup_fee_only_subscription_email_title = htmlentities(stripslashes_deep($_POST['purchase_signup_fee_only_subscription_email_title']));
          update_option('purchase_signup_fee_only_subscription_email_title', $purchase_signup_fee_only_subscription_email_title);
          $purchase_signup_fee_only_subscription_email_content = stripslashes_deep($_POST['purchase_signup_fee_only_subscription_email_content']);
          update_option('purchase_signup_fee_only_subscription_email_content', $purchase_signup_fee_only_subscription_email_content);
          $purchase_signup_fee_only_subscription_admin_email_title = htmlentities(stripslashes_deep($_POST['purchase_signup_fee_only_subscription_admin_email_title']));
          update_option('purchase_signup_fee_only_subscription_admin_email_title', $purchase_signup_fee_only_subscription_admin_email_title);
          $purchase_signup_fee_only_subscription_admin_email_content = stripslashes_deep($_POST['purchase_signup_fee_only_subscription_admin_email_content']);
          update_option('purchase_signup_fee_only_subscription_admin_email_content', $purchase_signup_fee_only_subscription_admin_email_content);

          $purchase_free_subscription_email_title = htmlentities(stripslashes_deep($_POST['purchase_free_subscription_email_title']));
          update_option('purchase_free_subscription_email_title', $purchase_free_subscription_email_title);
          $purchase_free_subscription_email_content = stripslashes_deep($_POST['purchase_free_subscription_email_content']);
          update_option('purchase_free_subscription_email_content', $purchase_free_subscription_email_content);
          $purchase_free_subscription_admin_email_title = htmlentities(stripslashes_deep($_POST['purchase_free_subscription_admin_email_title']));
          update_option('purchase_free_subscription_admin_email_title', $purchase_free_subscription_admin_email_title);
          $purchase_free_subscription_admin_email_content = stripslashes_deep($_POST['purchase_free_subscription_admin_email_content']);
          update_option('purchase_free_subscription_admin_email_content', $purchase_free_subscription_admin_email_content);

          
          $purchase_additional_subscription_email_title = htmlentities(stripslashes_deep($_POST['purchase_additional_subscription_email_title']));          
          update_option('purchase_additional_subscription_email_title', $purchase_additional_subscription_email_title);          
          $purchase_additional_subscription_email_content = stripslashes_deep($_POST['purchase_additional_subscription_email_content']);          
          update_option('purchase_additional_subscription_email_content', $purchase_additional_subscription_email_content);          
          $purchase_additional_subscription_admin_email_title = htmlentities(stripslashes_deep($_POST['purchase_additional_subscription_admin_email_title']));          
          update_option('purchase_additional_subscription_admin_email_title', $purchase_additional_subscription_admin_email_title);          
          $purchase_additional_subscription_admin_email_content = stripslashes_deep($_POST['purchase_additional_subscription_admin_email_content']);          
          update_option('purchase_additional_subscription_admin_email_content', $purchase_additional_subscription_admin_email_content);
          
          
          $purchase_organisational_subscription_email_title = htmlentities(stripslashes_deep($_POST['purchase_organisational_subscription_email_title']));          
          update_option('purchase_organisational_subscription_email_title', $purchase_organisational_subscription_email_title);          
          $purchase_organisational_subscription_email_content = stripslashes_deep($_POST['purchase_organisational_subscription_email_content']);          
          update_option('purchase_organisational_subscription_email_content', $purchase_organisational_subscription_email_content);          
          $purchase_organisational_subscription_admin_email_title = htmlentities(stripslashes_deep($_POST['purchase_organisational_subscription_admin_email_title']));          
          update_option('purchase_organisational_subscription_admin_email_title', $purchase_organisational_subscription_admin_email_title);          
          $purchase_organisational_subscription_admin_email_content = stripslashes_deep($_POST['purchase_organisational_subscription_admin_email_content']);          
          update_option('purchase_organisational_subscription_admin_email_content', $purchase_organisational_subscription_admin_email_content);
          
          $membership_request_received_admin_email_title = htmlentities(stripslashes_deep($_POST['membership_request_received_admin_email_title']));
          update_option('membership_request_received_admin_email_title', $membership_request_received_admin_email_title);          
          $membership_request_received_admin_email_content = stripslashes_deep($_POST['membership_request_received_admin_email_content']);
          update_option('membership_request_received_admin_email_content', $membership_request_received_admin_email_content);
          
          $membership_request_approved_email_title = htmlentities(stripslashes_deep($_POST['membership_request_approved_email_title']));
          update_option('membership_request_approved_email_title', $membership_request_approved_email_title);          
          $membership_request_approved_email_content = stripslashes_deep($_POST['membership_request_approved_email_content']);
          update_option('membership_request_approved_email_content', $membership_request_approved_email_content);
          $membership_request_approved_admin_email_title = htmlentities(stripslashes_deep($_POST['membership_request_approved_admin_email_title']));
          update_option('membership_request_approved_admin_email_title', $membership_request_approved_admin_email_title);          
          $membership_request_approved_admin_email_content = stripslashes_deep($_POST['membership_request_approved_admin_email_content']);
          update_option('membership_request_approved_admin_email_content', $membership_request_approved_admin_email_content);
          
          
          $membership_request_rejected_email_title = htmlentities(stripslashes_deep($_POST['membership_request_rejected_email_title']));
          update_option('membership_request_rejected_email_title', $membership_request_rejected_email_title);          
          $membership_request_rejected_email_content = stripslashes_deep($_POST['membership_request_rejected_email_content']);
          update_option('membership_request_rejected_email_content', $membership_request_rejected_email_content);
          $membership_request_rejected_admin_email_title = htmlentities(stripslashes_deep($_POST['membership_request_rejected_admin_email_title']));
          update_option('membership_request_rejected_admin_email_title', $membership_request_rejected_admin_email_title);          
          $membership_request_rejected_admin_email_content = stripslashes_deep($_POST['membership_request_rejected_admin_email_content']);
          update_option('membership_request_rejected_admin_email_content', $membership_request_rejected_admin_email_content);

          $member_promoted_email_title = htmlentities(stripslashes_deep($_POST['member_promoted_email_title']));
          update_option('member_promoted_email_title', $member_promoted_email_title);
          $member_promoted_email_content = stripslashes_deep($_POST['member_promoted_email_content']);
          update_option('member_promoted_email_content', $member_promoted_email_content);
          $member_promoted_admin_email_title = htmlentities(stripslashes_deep($_POST['member_promoted_admin_email_title']));
          update_option('member_promoted_admin_email_title', $member_promoted_admin_email_title);
          $member_promoted_admin_email_content = stripslashes_deep($_POST['member_promoted_admin_email_content']);
          update_option('member_promoted_admin_email_content', $member_promoted_admin_email_content);

          $remove_member_email_title = htmlentities(stripslashes_deep($_POST['remove_member_email_title']));
          update_option('remove_member_email_title', $remove_member_email_title);          
          $remove_member_email_content = stripslashes_deep($_POST['remove_member_email_content']);
          update_option('remove_member_email_content', $remove_member_email_content);
          $remove_member_admin_email_title = htmlentities(stripslashes_deep($_POST['remove_member_admin_email_title']));
          update_option('remove_member_admin_email_title', $remove_member_admin_email_title);          
          $remove_member_admin_email_content = stripslashes_deep($_POST['remove_member_admin_email_content']);
          update_option('remove_member_admin_email_content', $remove_member_admin_email_content);
          
          $member_leave_community_admin_email_title = htmlentities(stripslashes_deep($_POST['member_leave_community_admin_email_title']));          
          update_option('member_leave_community_admin_email_title', $member_leave_community_admin_email_title);          
          $member_leave_community_admin_email_content = stripslashes_deep($_POST['member_leave_community_admin_email_content']);
          update_option('member_leave_community_admin_email_content', $member_leave_community_admin_email_content);
          
          
          $cancel_subscription_email_title = htmlentities(stripslashes_deep($_POST['cancel_subscription_email_title']));          
          update_option('cancel_subscription_email_title', $cancel_subscription_email_title);          
          $cancel_subscription_email_content = stripslashes_deep($_POST['cancel_subscription_email_content']);          
          update_option('cancel_subscription_email_content', $cancel_subscription_email_content);          
          $cancel_subscription_admin_email_title = htmlentities(stripslashes_deep($_POST['cancel_subscription_admin_email_title']));          
          update_option('cancel_subscription_admin_email_title', $cancel_subscription_admin_email_title);          
          $cancel_subscription_admin_email_content = stripslashes_deep($_POST['cancel_subscription_admin_email_content']);          
          update_option('cancel_subscription_admin_email_content', $cancel_subscription_admin_email_content);
          
          $cancel_free_subscription_email_title = htmlentities(stripslashes_deep($_POST['cancel_free_subscription_email_title']));
          update_option('cancel_free_subscription_email_title', $cancel_free_subscription_email_title);
          $cancel_free_subscription_email_content = stripslashes_deep($_POST['cancel_free_subscription_email_content']);
          update_option('cancel_free_subscription_email_content', $cancel_free_subscription_email_content);
          $cancel_free_subscription_admin_email_title = htmlentities(stripslashes_deep($_POST['cancel_free_subscription_admin_email_title']));
          update_option('cancel_free_subscription_admin_email_title', $cancel_free_subscription_admin_email_title);
          $cancel_free_subscription_admin_email_content = stripslashes_deep($_POST['cancel_free_subscription_admin_email_content']);
          update_option('cancel_free_subscription_admin_email_content', $cancel_free_subscription_admin_email_content);
          
          $cancel_additional_subscription_email_title = htmlentities(stripslashes_deep($_POST['cancel_additional_subscription_email_title']));          
          update_option('cancel_additional_subscription_email_title', $cancel_additional_subscription_email_title);          
          $cancel_additional_subscription_email_content = stripslashes_deep($_POST['cancel_additional_subscription_email_content']);          
          update_option('cancel_additional_subscription_email_content', $cancel_additional_subscription_email_content);          
          $cancel_additional_subscription_admin_email_title = htmlentities(stripslashes_deep($_POST['cancel_additional_subscription_admin_email_title']));          
          update_option('cancel_additional_subscription_admin_email_title', $cancel_additional_subscription_admin_email_title);          
          $cancel_additional_subscription_admin_email_content = stripslashes_deep($_POST['cancel_additional_subscription_admin_email_content']);          
          update_option('cancel_additional_subscription_admin_email_content', $cancel_additional_subscription_admin_email_content);
          
          $unsubscribing_email_title = htmlentities(stripslashes_deep($_POST['unsubscribing_email_title']));
          update_option('unsubscribing_email_title', $unsubscribing_email_title);          
          $unsubscribing_email_content = stripslashes_deep($_POST['unsubscribing_email_content']);          
          update_option('unsubscribing_email_content', $unsubscribing_email_content);          
          $unsubscribing_admin_email_title = htmlentities(stripslashes_deep($_POST['unsubscribing_admin_email_title']));          
          update_option('unsubscribing_admin_email_title', $unsubscribing_admin_email_title);          
          $unsubscribing_admin_email_content = stripslashes_deep($_POST['unsubscribing_admin_email_content']);          
          update_option('unsubscribing_admin_email_content', $unsubscribing_admin_email_content);
          
          $inarrears_subscription_email_title = htmlentities(stripslashes_deep($_POST['inarrears_subscription_email_title']));
          update_option('inarrears_subscription_email_title', $inarrears_subscription_email_title);          
          $inarrears_subscription_email_content = stripslashes_deep($_POST['inarrears_subscription_email_content']);          
          update_option('inarrears_subscription_email_content', $inarrears_subscription_email_content);          
          $inarrears_subscription_admin_email_title = htmlentities(stripslashes_deep($_POST['inarrears_subscription_admin_email_title']));          
          update_option('inarrears_subscription_admin_email_title', $inarrears_subscription_admin_email_title);          
          $inarrears_subscription_admin_email_content = stripslashes_deep($_POST['inarrears_subscription_admin_email_content']);          
          update_option('inarrears_subscription_admin_email_content', $inarrears_subscription_admin_email_content);          
          
          $frozen_subscription_email_title = htmlentities(stripslashes_deep($_POST['frozen_subscription_email_title']));          
          update_option('frozen_subscription_email_title', $frozen_subscription_email_title);          
          $frozen_subscription_email_content = stripslashes_deep($_POST['frozen_subscription_email_content']);          
          update_option('frozen_subscription_email_content', $frozen_subscription_email_content);          
          $frozen_subscription_admin_email_title = htmlentities(stripslashes_deep($_POST['frozen_subscription_admin_email_title']));          
          update_option('frozen_subscription_admin_email_title', $frozen_subscription_admin_email_title);          
          $frozen_subscription_admin_email_content = stripslashes_deep($_POST['frozen_subscription_admin_email_content']);          
          update_option('frozen_subscription_admin_email_content', $frozen_subscription_admin_email_content);
          
          $active_subscription_email_title = htmlentities(stripslashes_deep($_POST['active_subscription_email_title']));          
          update_option('active_subscription_email_title', $active_subscription_email_title);          
          $active_subscription_email_content = stripslashes_deep($_POST['active_subscription_email_content']);          
          update_option('active_subscription_email_content', $active_subscription_email_content);          
          $active_subscription_admin_email_title = htmlentities(stripslashes_deep($_POST['active_subscription_admin_email_title']));          
          update_option('active_subscription_admin_email_title', $active_subscription_admin_email_title);          
          $active_subscription_admin_email_content = stripslashes_deep($_POST['active_subscription_admin_email_content']);          
          update_option('active_subscription_admin_email_content', $active_subscription_admin_email_content);
          
          $active_subscription2_email_title = htmlentities(stripslashes_deep($_POST['active_subscription2_email_title']));          
          update_option('active_subscription2_email_title', $active_subscription2_email_title);          
          $active_subscription2_email_content = stripslashes_deep($_POST['active_subscription2_email_content']);          
          update_option('active_subscription2_email_content', $active_subscription2_email_content);          
          $active_subscription2_admin_email_title = htmlentities(stripslashes_deep($_POST['active_subscription2_admin_email_title']));          
          update_option('active_subscription2_admin_email_title', $active_subscription2_admin_email_title);          
          $active_subscription2_admin_email_content = stripslashes_deep($_POST['active_subscription2_admin_email_content']);          
          update_option('active_subscription2_admin_email_content', $active_subscription2_admin_email_content);
          
          
                              
          $password_changed_email_title = htmlentities(stripslashes_deep($_POST['password_changed_email_title']));          
          update_option('password_changed_email_title', $password_changed_email_title);          
          $password_changed_email_content = stripslashes_deep($_POST['password_changed_email_content']);          
          update_option('password_changed_email_content', $password_changed_email_content);          
          $password_changed_admin_email_title = htmlentities(stripslashes_deep($_POST['password_changed_admin_email_title']));          
          update_option('password_changed_admin_email_title', $password_changed_admin_email_title);          
          $password_changed_admin_email_content = stripslashes_deep($_POST['password_changed_admin_email_content']);          
          update_option('password_changed_admin_email_content', $password_changed_admin_email_content);
          
          $forgot_password_email_title = htmlentities(stripslashes_deep($_POST['forgot_password_email_title']));          
          update_option('forgot_password_email_title', $forgot_password_email_title);          
          $forgot_password_email_content = stripslashes_deep($_POST['forgot_password_email_content']);          
          update_option('forgot_password_email_content', $forgot_password_email_content);
          
          $verify_email_title = htmlentities(stripslashes_deep($_POST['verify_email_title']));          
          update_option('verify_email_title', $verify_email_title);          
          $verify_email_content = stripslashes_deep($_POST['verify_email_content']);          
          update_option('verify_email_content', $verify_email_content);
          
          $suite_changed_email_title = htmlentities(stripslashes_deep($_POST['suite_changed_email_title']));          
          update_option('suite_changed_email_title', $suite_changed_email_title);          
          $suite_changed_email_content = stripslashes_deep($_POST['suite_changed_email_content']);          
          update_option('suite_changed_email_content', $suite_changed_email_content);
          
          $case_changed_email_title = htmlentities(stripslashes_deep($_POST['case_changed_email_title']));          
          update_option('case_changed_email_title', $case_changed_email_title);          
          $case_changed_email_content = stripslashes_deep($_POST['case_changed_email_content']);          
          update_option('case_changed_email_content', $case_changed_email_content);
          
          $ticket_created_email_title = htmlentities(stripslashes_deep($_POST['ticket_created_email_title']));          
          update_option('ticket_created_email_title', $ticket_created_email_title);          
          $ticket_created_email_content = stripslashes_deep($_POST['ticket_created_email_content']);          
          update_option('ticket_created_email_content', $ticket_created_email_content);
          $ticket_created_support_email_title = htmlentities(stripslashes_deep($_POST['ticket_created_support_email_title']));          
          update_option('ticket_created_support_email_title', $ticket_created_support_email_title);          
          $ticket_created_support_email_content = stripslashes_deep($_POST['ticket_created_support_email_content']);          
          update_option('ticket_created_support_email_content', $ticket_created_support_email_content);
          $ticket_created_admin_email_title = htmlentities(stripslashes_deep($_POST['ticket_created_admin_email_title']));          
          update_option('ticket_created_admin_email_title', $ticket_created_admin_email_title);          
          $ticket_created_admin_email_content = stripslashes_deep($_POST['ticket_created_admin_email_content']);          
          update_option('ticket_created_admin_email_content', $ticket_created_admin_email_content);
          
          $ticket_updated_email_title = htmlentities(stripslashes_deep($_POST['ticket_updated_email_title']));          
          update_option('ticket_updated_email_title', $ticket_updated_email_title);          
          $ticket_updated_email_content = stripslashes_deep($_POST['ticket_updated_email_content']);          
          update_option('ticket_updated_email_content', $ticket_updated_email_content);
          $ticket_updated_support_email_title = htmlentities(stripslashes_deep($_POST['ticket_updated_support_email_title']));          
          update_option('ticket_updated_support_email_title', $ticket_updated_support_email_title);          
          $ticket_updated_support_email_content = stripslashes_deep($_POST['ticket_updated_support_email_content']);          
          update_option('ticket_updated_support_email_content', $ticket_updated_support_email_content);
          $ticket_updated_admin_email_title = htmlentities(stripslashes_deep($_POST['ticket_updated_admin_email_title']));          
          update_option('ticket_updated_admin_email_title', $ticket_updated_admin_email_title);          
          $ticket_updated_admin_email_content = stripslashes_deep($_POST['ticket_updated_admin_email_content']);          
          update_option('ticket_updated_admin_email_content', $ticket_updated_admin_email_content);
          
          
          $ticket_started_email_title = htmlentities(stripslashes_deep($_POST['ticket_started_email_title']));          
          update_option('ticket_started_email_title', $ticket_started_email_title);          
          $ticket_started_email_content = stripslashes_deep($_POST['ticket_started_email_content']);          
          update_option('ticket_started_email_content', $ticket_started_email_content);          
          $ticket_started_support_email_title = htmlentities(stripslashes_deep($_POST['ticket_started_support_email_title']));          
          update_option('ticket_started_support_email_title', $ticket_started_support_email_title);          
          $ticket_started_support_email_content = stripslashes_deep($_POST['ticket_started_support_email_content']);          
          update_option('ticket_started_support_email_content', $ticket_started_support_email_content);
          $ticket_started_admin_email_title = htmlentities(stripslashes_deep($_POST['ticket_started_admin_email_title']));          
          update_option('ticket_started_admin_email_title', $ticket_started_admin_email_title);          
          $ticket_started_admin_email_content = stripslashes_deep($_POST['ticket_started_admin_email_content']);          
          update_option('ticket_started_admin_email_content', $ticket_started_admin_email_content);
          
          $ticket_solved_email_title = htmlentities(stripslashes_deep($_POST['ticket_solved_email_title']));          
          update_option('ticket_solved_email_title', $ticket_solved_email_title);          
          $ticket_solved_email_content = stripslashes_deep($_POST['ticket_solved_email_content']);          
          update_option('ticket_solved_email_content', $ticket_solved_email_content);
          $ticket_solved_support_email_title = htmlentities(stripslashes_deep($_POST['ticket_solved_support_email_title']));          
          update_option('ticket_solved_support_email_title', $ticket_solved_support_email_title);          
          $ticket_solved_support_email_content = stripslashes_deep($_POST['ticket_solved_support_email_content']);          
          update_option('ticket_solved_support_email_content', $ticket_solved_support_email_content);
          $ticket_solved_admin_email_title = htmlentities(stripslashes_deep($_POST['ticket_solved_admin_email_title']));          
          update_option('ticket_solved_admin_email_title', $ticket_solved_admin_email_title);          
          $ticket_solved_admin_email_content = stripslashes_deep($_POST['ticket_solved_admin_email_content']);          
          update_option('ticket_solved_admin_email_content', $ticket_solved_admin_email_content);
          
          
          
          $ticket_closed_email_title = htmlentities(stripslashes_deep($_POST['ticket_closed_email_title']));          
          update_option('ticket_closed_email_title', $ticket_closed_email_title);          
          $ticket_closed_email_content = stripslashes_deep($_POST['ticket_closed_email_content']);          
          update_option('ticket_closed_email_content', $ticket_closed_email_content);
          $ticket_closed_admin_email_title = htmlentities(stripslashes_deep($_POST['ticket_closed_admin_email_title']));          
          update_option('ticket_closed_admin_email_title', $ticket_closed_admin_email_title);          
          $ticket_closed_admin_email_content = stripslashes_deep($_POST['ticket_closed_admin_email_content']);          
          update_option('ticket_closed_admin_email_content', $ticket_closed_admin_email_content);
          
          $ticket_payment_processed_by_card_email_title = htmlentities(stripslashes_deep($_POST['ticket_payment_processed_by_card_email_title']));          
          update_option('ticket_payment_processed_by_card_email_title', $ticket_payment_processed_by_card_email_title);          
          $ticket_payment_processed_by_card_email_content = stripslashes_deep($_POST['ticket_payment_processed_by_card_email_content']);          
          update_option('ticket_payment_processed_by_card_email_content', $ticket_payment_processed_by_card_email_content);
          $ticket_payment_processed_by_card_support_email_title = htmlentities(stripslashes_deep($_POST['ticket_payment_processed_by_card_support_email_title']));          
          update_option('ticket_payment_processed_by_card_support_email_title', $ticket_payment_processed_by_card_support_email_title);          
          $ticket_payment_processed_by_card_support_email_content = stripslashes_deep($_POST['ticket_payment_processed_by_card_support_email_content']);          
          update_option('ticket_payment_processed_by_card_support_email_content', $ticket_payment_processed_by_card_support_email_content);
          $ticket_payment_processed_by_card_admin_email_title = htmlentities(stripslashes_deep($_POST['ticket_payment_processed_by_card_admin_email_title']));          
          update_option('ticket_payment_processed_by_card_admin_email_title', $ticket_payment_processed_by_card_admin_email_title);          
          $ticket_payment_processed_by_card_admin_email_content = stripslashes_deep($_POST['ticket_payment_processed_by_card_admin_email_content']);          
          update_option('ticket_payment_processed_by_card_admin_email_content', $ticket_payment_processed_by_card_admin_email_content);
          
          $ticket_payment_processed_by_tokens_email_title = htmlentities(stripslashes_deep($_POST['ticket_payment_processed_by_tokens_email_title']));          
          update_option('ticket_payment_processed_by_tokens_email_title', $ticket_payment_processed_by_tokens_email_title);          
          $ticket_payment_processed_by_tokens_email_content = stripslashes_deep($_POST['ticket_payment_processed_by_tokens_email_content']);          
          update_option('ticket_payment_processed_by_tokens_email_content', $ticket_payment_processed_by_tokens_email_content);
          $ticket_payment_processed_by_tokens_support_email_title = htmlentities(stripslashes_deep($_POST['ticket_payment_processed_by_tokens_support_email_title']));          
          update_option('ticket_payment_processed_by_tokens_support_email_title', $ticket_payment_processed_by_tokens_support_email_title);          
          $ticket_payment_processed_by_tokens_support_email_content = stripslashes_deep($_POST['ticket_payment_processed_by_tokens_support_email_content']);          
          update_option('ticket_payment_processed_by_tokens_support_email_content', $ticket_payment_processed_by_tokens_support_email_content);
          $ticket_payment_processed_by_tokens_admin_email_title = htmlentities(stripslashes_deep($_POST['ticket_payment_processed_by_tokens_admin_email_title']));          
          update_option('ticket_payment_processed_by_tokens_admin_email_title', $ticket_payment_processed_by_tokens_admin_email_title);          
          $ticket_payment_processed_by_tokens_admin_email_content = stripslashes_deep($_POST['ticket_payment_processed_by_tokens_admin_email_content']);          
          update_option('ticket_payment_processed_by_tokens_admin_email_content', $ticket_payment_processed_by_tokens_admin_email_content);
          
          $forum_new_post_email_title = htmlentities(stripslashes_deep($_POST['forum_new_post_email_title']));          
          update_option('forum_new_post_email_title', $forum_new_post_email_title);          
          $forum_new_post_email_content = stripslashes_deep($_POST['forum_new_post_email_content']);          
          update_option('forum_new_post_email_content', $forum_new_post_email_content);          

          $forum_reply_post_email_title = htmlentities(stripslashes_deep($_POST['forum_reply_post_email_title']));          
          update_option('forum_reply_post_email_title', $forum_reply_post_email_title);          
          $forum_reply_post_email_content = stripslashes_deep($_POST['forum_reply_post_email_content']);          
          update_option('forum_reply_post_email_content', $forum_reply_post_email_content);          
          
          $claim_created_admin_email_title = htmlentities(stripslashes_deep($_POST['claim_created_admin_email_title']));          
          update_option('claim_created_admin_email_title', $claim_created_admin_email_title);          
          $claim_created_admin_email_content = stripslashes_deep($_POST['claim_created_admin_email_content']);          
          update_option('claim_created_admin_email_content', $claim_created_admin_email_content);          
          
          wp_redirect("/wp-admin/admin.php?page=email-management&tab=" . (!$_REQUEST['tab'] ? 0 : $_REQUEST['tab']));
    }
}

//Front End Functions
function cp_send_email($to, $template_name, $data = array())
{
    global $phpmailer;
    
    if ( !is_object( $phpmailer ) || !is_a( $phpmailer, 'PHPMailer' ) ) {
        require_once ABSPATH . WPINC . '/class-phpmailer.php';
        require_once ABSPATH . WPINC . '/class-smtp.php';
        $phpmailer = new PHPMailer( true );
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
    if(!$supportName)
        $supportName = 'ComplianceTest';
        
    $supportEmail = get_option('support_email');
    if(!$supportEmail)
        $supportEmail = 'support@compliancetest.net';
    
    $emailTitle = get_option($template_name . "_email_title");
    $emailContent = get_option($template_name . "_email_content");
    
    if(!$emailTitle || !$emailContent)
    {
        return false;
    }
    
    //Add [env] and [website_url] shortcodes
    $data['[website_url]'] = get_site_url();
    $data['[env]'] = get_option('env');
    
    $shortCodes = array_keys($data);
    $values = array_values($data);
    $emailTitle = str_replace($shortCodes, $values, $emailTitle);
    $emailContent = str_replace($shortCodes, $values, $emailContent);
    $emailContent = apply_filters('the_content', $emailContent);
    
    $phpmailer->AddReplyTo($supportEmail, $supportName);
    $phpmailer->FromName = $supportName;
    $phpmailer->From = $supportEmail;
    
    if(isset($to['name']))
    {
        $phpmailer->AddAddress($to['email'], $to['name']);        
    }else{
        foreach($to as $u)
            $phpmailer->AddAddress($u['email'], $u['name']);        
    }
    
    $phpmailer->IsSMTP();
    $phpmailer->IsHTML(true);
    $phpmailer->Subject = $emailTitle;
    $phpmailer->Body = $emailContent;


    try{
        return $phpmailer->Send();    
    }catch(Exception $e){
        return false;
    }
}

function cp_send_email_to_admin($template_name, $data = array())
{
    //Getting Admin Users    
    $users = get_users(array('role'=>'administrator'));
    
    $to = array();
    foreach($users as $u)
    {           
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
    if(is_array($communities))
        $query = "SELECT u.* FROM {$bp->groups->table_name_members} AS p LEFT JOIN {$wpdb->users} AS u ON u.ID = p.user_id WHERE group_id IN (" . implode(", ", $communities) . ") AND p.is_admin = 1 AND p.is_banned = 0";
    else
        $query = $wpdb->prepare( "SELECT u.* FROM {$bp->groups->table_name_members} AS p LEFT JOIN {$wpdb->users} AS u ON u.ID = p.user_id WHERE group_id = %d AND p.is_admin = 1 AND p.is_banned = 0", $communities );
    
    $users = $wpdb->get_results($query);
    
    $to = array();
    foreach($users as $u)
    {           
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
    if(is_array($communities))
        $query = "SELECT u.* FROM {$bp->groups->table_name_members} AS p LEFT JOIN {$wpdb->users} AS u ON u.ID = p.user_id WHERE group_id IN (" . implode(", ", $communities) . ") AND p.is_mod = 1 AND p.is_banned = 0";
    else
        $query = $wpdb->prepare( "SELECT u.* FROM {$bp->groups->table_name_members} AS p LEFT JOIN {$wpdb->users} AS u ON u.ID = p.user_id WHERE group_id = %d AND p.is_mod = 1 AND p.is_banned = 0", $communities );
    
    $users = $wpdb->get_results($query);
    
    $to = array();
    foreach($users as $u)
    {           
        $fname = get_user_meta($u->ID, 'first_name', true);
        $lname = get_user_meta($u->ID, 'last_name', true);
        $to[] = array('name' => $fname . " " . $lname, 'email' => $u->user_email);
    }    
    
    cp_send_email($to, $template_name, $data);
}

