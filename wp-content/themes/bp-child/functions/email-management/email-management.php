<?php
/**
* Management Site Emails
*/
//Add New Menu
add_action('admin_menu', 'add_email_management_page');
function add_email_management_page()
{
    add_options_page('Email Template Management', 'Email Management', 'administrator', 'email-management', 'create_email_management_page');
}

function create_email_management_page()
{
    ?>
    <script type="text/javascript" src="<?php echo dirname(get_bloginfo('stylesheet_url'))?>/js/jquery-ui-1.10.3.custom.js"></script>
    <link href="<?php echo dirname(get_bloginfo('stylesheet_url'))?>/css/jquery-ui-1.10.3.custom.css"  type="text/css" rel="stylesheet" />
    <style type="text/css">
        #emails .ui-tabs-nav{
            padding: 0.2em 0.2em 0;
            border: solid 1px #aaa;
            background: #ccc;
            background: -moz-linear-gradient(top,  #cecece 0%, #ccc 9%, #eee 100%); /* FF3.6+ */
            background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#cecece), color-stop(9%,#ccc), color-stop(100%,#eee)); /* Chrome,Safari4+ */
            background: -webkit-linear-gradient(top,  #cecece 0%,#ccc 9%,#eee 100%); /* Chrome10+,Safari5.1+ */
            background: -o-linear-gradient(top,  #cecece 0%,#ccc 9%,#eee 100%); /* Opera 11.10+ */
            background: -ms-linear-gradient(top,  #cecece 0%,#ccc 9%,#eee 100%); /* IE10+ */
            background: linear-gradient(to bottom,  #cecece 0%,#ccc 9%,#eee 100%); /* W3C */
        }
        #emails textarea{
            width: 100%;
            height: 400px;
        }
        #emails input[type="text"]{
            width: 50%;
        }
        .mceIframeContainer{
            height: 500px;
        }
        .mceIframeContainer iframe{
            height: 100% !important;
            
        }
        .ui-tabs .ui-tabs-nav li{
            background: #eee;
            border: solid 1px #d3d3d3;
        }
        .ui-tabs .ui-tabs-nav li.ui-tabs-active{
            background: #fff;
        }
        #emails{
            border: solid 1px #aaa;
        }
        
    </style>
      <form name="adminform" method="post" action="admin.php">
          <div class="wrap">
            <h2>ComplianceTest Email Templates</h2>                
            <br />
            <div style="padding: 0 0 10px; text-align: left;">
                <input type="submit" value="Save Changes" name="email-templates" class="button-primary" />                    
                <?php wp_nonce_field('save-email-templates'); ?>
            </div>
            <p>
                <label><b>Support Name:</b> <input type="text" name="support_name" id="support_name" value="<?php echo get_option('support_name')?>" size="50" /></label>
                <label style="margin-left: 20px"><b>Support Email:</b> <input type="text" name="support_email" id="support_email" value="<?php echo get_option('support_email')?>" size="50" /></label>
            </p>
            <div id="emails">
                <ul>
                    <li><a href="#new-user">New User Registered</a></li>
                    <li><a href="#verification-email">Resend Verification Email</a></li>
                    <li><a href="#verification-success">User Verification Success</a></li>
                    <li><a href="#purchase-subscription">Purchase Subscription</a></li>       
                    <li><a href="#cancel-subscription">Cancel Subscription</a></li>       
                    <li><a href="#membership-request-received">Membership Request Received</a></li>       
                    <li><a href="#membership-request-approved">Membership Request Approved</a></li>       
                    <li><a href="#membership-request-rejcted">Membership Request Rejected</a></li>       
                    <li><a href="#member-leave-community">Member Leave Community</a></li>       
                    <li><a href="#forgot-password">Forgot Password</a></li>
                    <li><a href="#password-changed">Password Changed</a></li>
                </ul>
                <div id="new-user">
                    <?php
                        $new_user_email_title = get_option('new_user_email_title');
                        $new_user_email_content = get_option('new_user_email_content');
                        $new_user_admin_email_title = get_option('new_user_admin_email_title');
                        $new_user_admin_email_content = get_option('new_user_admin_email_content');
                        
                    ?>       
                    <p><b>Short Codes:</b> [name], [username], [email], [password], [link]</p>
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
                                    <?php wp_editor($new_user_email_content, 'new_user_email_content', array('media_buttons' => false)) ?>
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
                                    <?php wp_editor($new_user_admin_email_content, 'new_user_admin_email_content', array('media_buttons' => false)) ?>
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
                    <p><b>Short Codes:</b> [name],[username], [email], [link]</p>
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
                                    <?php wp_editor($verify_email_content, 'verify_email_content', array('media_buttons' => false)) ?>
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
                    <p><b>Short Codes:</b> [name], [email], [username]</p>
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
                                    <?php wp_editor($user_verify_success_email_content, 'user_verify_success_email_content', array('media_buttons' => false)) ?>
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
                                    <?php wp_editor($user_verify_success_admin_email_content, 'user_verify_success_admin_email_content', array('media_buttons' => false)) ?>
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
                    <p><b>Short Codes:</b> [name], [email], [suite_name], [suite_url], [paid_amount], [community_url]</p>
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
                                    <?php wp_editor($purchase_subscription_email_content, 'purchase_subscription_email_content', array('media_buttons' => false)) ?>
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
                                    <?php wp_editor($purchase_subscription_admin_email_content, 'purchase_subscription_admin_email_content', array('media_buttons' => false)) ?>
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
                    <p><b>Short Codes:</b> [name], [email], [test_suite], [paid_amount]</p>
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
                                    <?php wp_editor($cancel_subscription_email_content, 'cancel_subscription_email_content', array('media_buttons' => false)) ?>    
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
                                    <?php wp_editor($cancel_subscription_admin_email_content, 'cancel_subscription_admin_email_content', array('media_buttons' => false)) ?>
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
                    <p><b>Short Codes:</b> [name], [email], [username], [community], [community_url]</p>
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
                                    <?php wp_editor($membership_request_received_admin_email_content, 'membership_request_received_admin_email_content', array('media_buttons' => false)) ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div id="membership-request-approved">
                <?php
                    $membership_request_approved_email_title = get_option('membership_request_approved_email_title');
                    $membership_request_approved_email_content = get_option('membership_request_approved_email_content');
                ?>
                    <p><b>Short Codes:</b> [name], [email], [username], [community], [community_url]</p>
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
                                    <?php wp_editor($membership_request_approved_email_content, 'membership_request_approved_email_content', array('media_buttons' => false)) ?>     
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div id="membership-request-rejected">
                <?php
                    $membership_request_rejected_email_title = get_option('membership_request_rejected_email_title');
                    $membership_request_rejected_email_content = get_option('membership_request_rejected_email_content');
                ?>
                    <p><b>Short Codes:</b> [name], [email], [username], [community], [community_url]</p>
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
                                    <?php wp_editor($membership_request_rejected_email_content, 'membership_request_rejected_email_content', array('media_buttons' => false)) ?>     
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
                    <p><b>Short Codes:</b> [name], [email], [password]</p>
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
                                    <?php wp_editor($member_leave_community_admin_email_content, 'member_leave_community_admin_email_content', array('media_buttons' => false)) ?>                
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
                    <p><b>Short Codes:</b> [name], [email], [username], [link]</p>
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
                                <?php wp_editor($forgot_password_email_content, 'forgot_password_email_content', array('media_buttons' => false)) ?>                
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
                    <p><b>Short Codes:</b> [name], [email], [username]</p>
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
                                    <?php wp_editor($password_changed_email_content, 'password_changed_email_content', array('media_buttons' => false)) ?>    
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
                                    <?php wp_editor($password_changed_admin_email_content, 'password_changed_admin_email_content', array('media_buttons' => false)) ?>
                                </td>
                            </tr>
                        </tbody>
                        
                    </table>
                </div>
            </div>
          </div>
          
      </form>
      <script type="text/javascript">
            jQuery(document).ready(function(){
                jQuery('#emails').tabs();
                
            })
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
        
          $new_user_email_title = htmlentities(stripslashes_deep($_POST['new_user_email_title']));          
          update_option('new_user_email_title', $new_user_email_title);          
          $new_user_email_content = stripslashes_deep($_POST['new_user_email_content']);          
          update_option('new_user_email_content', $new_user_email_content);          
          $new_user_admin_email_title = htmlentities(stripslashes_deep($_POST['new_user_admin_email_title']));          
          update_option('new_user_admin_email_title', $new_user_admin_email_title);          
          $new_user_admin_email_content = stripslashes_deep($_POST['new_user_admin_email_content']);          
          update_option('new_user_admin_email_content', $new_user_admin_email_content);
          
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
          
          $membership_request_received_admin_email_title = htmlentities(stripslashes_deep($_POST['membership_request_received_admin_email_title']));
          update_option('membership_request_received_admin_email_title', $membership_request_received_admin_email_title);          
          $membership_request_received_admin_email_content = stripslashes_deep($_POST['membership_request_received_admin_email_content']);
          update_option('membership_request_received_admin_email_content', $membership_request_received_admin_email_content);
          
          $membership_request_approved_email_title = htmlentities(stripslashes_deep($_POST['membership_request_approved_email_title']));
          update_option('membership_request_approved_email_title', $membership_request_approved_email_title);          
          $membership_request_approved_email_content = stripslashes_deep($_POST['membership_request_approved_email_content']);
          update_option('membership_request_approved_email_content', $membership_request_approved_email_content);
          
          $membership_request_rejected_email_title = htmlentities(stripslashes_deep($_POST['membership_request_rejected_email_title']));
          update_option('membership_request_rejected_email_title', $membership_request_rejected_email_title);          
          $membership_request_rejected_email_content = stripslashes_deep($_POST['membership_request_rejected_email_content']);
          update_option('membership_request_rejected_email_content', $membership_request_rejected_email_content);
          
          
          
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
          
          
          wp_redirect("/wp-admin/options-general.php?page=email-management");
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
    
    return $phpmailer->Send();    
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