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
            border-bottom: solid 1px #aaa;
            border-radius: 0;
        }
        #emails textarea{
            width: 100%;
            height: 400px;
        }
        #emails input[type="text"]{
            width: 50%;
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
                    <li><a href="#verification-email">Verification Email</a></li>
                    <li><a href="#verification-success">User Verification Success</a></li>
                    <li><a href="#purchase-subscription">Purchase Subscription</a></li>       
                    <li><a href="#cancel-subscription">Cancel Subscription</a></li>       
                    <li><a href="#membership-request-received">Membership Request Received</a></li>       
                    <li><a href="#membership-request-approved">Membership Request Approved</a></li>       
                    <li><a href="#member-leave-community">Member Leave Community</a></li>       
                    <li><a href="#forgot-password">Forgot Password</a></li>
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
                                    <textarea cols="80" rows="8" id="new_user_email_content" name="new_user_email_content"><?php echo $new_user_email_content ?></textarea>                                
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
                                    <textarea cols="80" rows="8" id="new_user_admin_email_content" name="new_user_admin_email_content"><?php echo $new_user_admin_email_content ?></textarea>                                
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
                    <p><b>Short Codes:</b> [name], [email], [link]</p>
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
                                    <textarea cols="80" rows="8" id="verify_email_content" name="verify_email_content"><?php echo $verify_email_content ?></textarea>                                
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
                    <p><b>Short Codes:</b> [name], [email]</p>
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
                                    <textarea cols="80" rows="8" id="user_verify_success_email_content" name="user_verify_success_email_content"><?php echo $user_verify_success_email_content?></textarea>                                
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
                                    <textarea cols="80" rows="8" id="user_verify_success_admin_email_content" name="user_verify_success_admin_email_content"><?php echo $user_verify_success_admin_email_content?></textarea>                                
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
                    <p><b>Short Codes:</b> [name], [email], [paid_amount], [test_suite], [address]</p>
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
                                    <textarea cols="80" rows="8" id="purchase_subscription_email_content" name="purchase_subscription_email_content"><?php echo $purchase_subscription_email_content ?></textarea>                                
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
                                    <textarea cols="80" rows="8" id="purchase_subscription_admin_email_content" name="purchase_subscription_admin_email_content"><?php echo $purchase_subscription_admin_email_content ?></textarea>                                
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
                    <p><b>Short Codes:</b> [name], [email], [paid_amount], [test_suite], [address]</p>
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
                                    <textarea cols="80" rows="8" id="cancel_subscription_email_content" name="cancel_subscription_email_content"><?php echo $cancel_subscription_email_content ?></textarea>                                
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
                                    <textarea cols="80" rows="8" id="cancel_subscription_admin_email_content" name="cancel_subscription_admin_email_content"><?php echo $cancel_subscription_admin_email_content ?></textarea>                                
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
                                    <input type="text" size="50" name="membership_request_received_admin_email_title" id="membership_request_received_admin_email_title" value="<?php echo $membership_request_received_admin_email_title?>" />
                                </td>
                            </tr>
                            <tr>
                                <td class="tdlabel"><b>Content</b></td>
                                <td>
                                    <textarea cols="80" rows="8" id="membership_request_received_admin_email_content" name="membership_request_received_admin_email_content"><?php echo $membership_request_received_admin_email_content ?></textarea>                                
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
                    <p><b>Short Codes:</b> [name], [email], [password]</p>
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
                                    <textarea cols="80" rows="8" id="membership_request_approved_email_content" name="membership_request_approved_email_content"><?php echo $membership_request_approved_email_content ?></textarea>                                
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
                                    <textarea cols="80" rows="8" id="member_leave_community_admin_email_content" name="member_leave_community_admin_email_content"><?php echo $member_leave_community_admin_email_content ?></textarea>                                
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
                    <p><b>Short Codes:</b> [name], [email], [password]</p>
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
                                    <textarea cols="80" rows="8" id="forgot_password_email_content" name="forgot_password_email_content"><?php echo $forgot_password_email_content ?></textarea>                                
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
          
          $user_verify_success_email_title = $_POST['user_verify_success_email_title'];          
          update_option('user_verify_success_email_title', $user_verify_success_email_title);          
          $user_verify_success_email_content = $_POST['user_verify_success_email_content'];          
          update_option('user_verify_success_email_content', $user_verify_success_email_content);          
          $user_verify_success_admin_email_title = $_POST['user_verify_success_admin_email_title'];          
          update_option('user_verify_success_admin_email_title', $user_verify_success_admin_email_title);          
          $user_verify_success_admin_email_content = $_POST['user_verify_success_admin_email_content'];          
          update_option('user_verify_success_admin_email_content', $user_verify_success_admin_email_content);          
          
          
          $purchase_subscription_email_title = $_POST['purchase_subscription_email_title'];          
          update_option('purchase_subscription_email_title', $purchase_subscription_email_title);          
          $purchase_subscription_email_content = $_POST['purchase_subscription_email_content'];          
          update_option('purchase_subscription_email_content', $purchase_subscription_email_content);          
          $purchase_subscription_admin_email_title = $_POST['purchase_subscription_admin_email_title'];          
          update_option('purchase_subscription_admin_email_title', $purchase_subscription_admin_email_title);          
          $purchase_subscription_admin_email_content = $_POST['purchase_subscription_admin_email_content'];          
          update_option('purchase_subscription_admin_email_content', $purchase_subscription_admin_email_content);
          
          $membership_request_received_admin_email_title = $_POST['membership_request_received_admin_email_title'];          
          update_option('membership_request_received_admin_email_title', $membership_request_received_admin_email_title);          
          $membership_request_received_admin_email_content = $_POST['membership_request_received_admin_email_content'];          
          update_option('membership_request_received_admin_email_content', $membership_request_received_admin_email_content);
          
          $membership_request_approved_email_title = $_POST['membership_request_approved_email_title'];          
          update_option('membership_request_approved_email_title', $membership_request_approved_email_title);          
          $membership_request_approved_email_content = $_POST['membership_request_approved_email_content'];          
          update_option('membership_request_approved_email_content', $membership_request_approved_email_content);
          
          $member_leave_community_admin_email_title = $_POST['member_leave_community_admin_email_title'];          
          update_option('member_leave_community_admin_email_title', $member_leave_community_admin_email_title);          
          $member_leave_community_admin_email_content = $_POST['member_leave_community_admin_email_content'];          
          update_option('member_leave_community_admin_email_content', $member_leave_community_admin_email_content);
          
          
          $cancel_subscription_email_title = $_POST['cancel_subscription_email_title'];          
          update_option('cancel_subscription_email_title', $cancel_subscription_email_title);          
          $cancel_subscription_email_content = $_POST['cancel_subscription_email_content'];          
          update_option('cancel_subscription_email_content', $cancel_subscription_email_content);          
          $cancel_subscription_admin_email_title = $_POST['cancel_subscription_admin_email_title'];          
          update_option('cancel_subscription_admin_email_title', $cancel_subscription_admin_email_title);          
          $cancel_subscription_admin_email_content = $_POST['cancel_subscription_admin_email_content'];          
          update_option('cancel_subscription_admin_email_content', $cancel_subscription_admin_email_content);
          
          $forgot_password_email_title = $_POST['forgot_password_email_title'];          
          update_option('forgot_password_email_title', $forgot_password_email_title);          
          $forgot_password_email_content = $_POST['forgot_password_email_content'];          
          update_option('forgot_password_email_content', $forgot_password_email_content);
          
          $verify_email_title = $_POST['verify_email_title'];          
          update_option('verify_email_title', $verify_email_title);          
          $verify_email_content = $_POST['verify_email_content'];          
          update_option('verify_email_content', $verify_email_content);
          
          
          wp_redirect("/wp-admin/options-general.php?page=email-management");
    }
}

//Front End Functions
function cp_send_email($to, $template_name, $data = array())
{
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
    
    //Send Email
    return wp_mail($to, $emailTitle, $emailContent, array('From: ' . $supportName . ' <' . $supportEmail . '>'));
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
        $to[] = $fname . " " . $lname . " <" . $u->user_email . ">";
    }    
    
    cp_send_email($to, $template_name, $data);
}