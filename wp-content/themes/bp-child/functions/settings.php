<?php
/**
* Manage Subscription Settings
*/

add_action('admin_menu', 'add_compliancetest_settings_page');
function add_compliancetest_settings_page()
{
    add_options_page('ComplianceTest Settings', 'ComplianceTest Settings', 'administrator', 'compliancetest-settings', 'create_compliancetest_settings_page');
    
    add_action('admin_init', 'register_eway_settings');
}
function create_compliancetest_settings_page()
{
    if(isset($_POST) && wp_verify_nonce($_POST['_wpnonce'], 'save-eway-options'))
    {
        //Save Options
        update_option('eway_payment_mode', $_POST['eway_payment_mode'] == 'live' ? 'live' : 'sandbox');
        update_option('eway_live_customer_id', $_POST['eway_live_customer_id']);
        update_option('eway_live_user_name', $_POST['eway_live_user_name']);
        update_option('eway_live_user_pwd', $_POST['eway_live_user_pwd']);
        update_option('eway_sandbox_customer_id', $_POST['eway_sandbox_customer_id']);
        update_option('eway_sandbox_user_name', $_POST['eway_sandbox_user_name']);
        update_option('eway_sandbox_user_pwd', $_POST['eway_sandbox_user_pwd']);
        
    }else if(isset($_POST) && wp_verify_nonce($_POST['_wpnonce'], 'save-subscription-settings')){
        //Save Options
        update_option('inarrears_count', $_POST['inarrears_count']);
        update_option('frozen_count', $_POST['frozen_count']);
        
    }else if(isset($_POST) && wp_verify_nonce($_POST['_wpnonce'], 'save-recaptcha-settings')){
        //Save Options
        update_option('recaptcha_public_key', $_POST['recaptcha_public_key']);
        update_option('recaptcha_private_key', $_POST['recaptcha_private_key']);
        
    }
    else if(isset($_POST) && wp_verify_nonce($_POST['_wpnonce'], 'save-mailchimp-all-list-settings')){
        //Save Options
        update_option('mailchimp_all_list_id', $_POST['mailchimp_all_list_id']);
        
    }
    else if(isset($_POST) && wp_verify_nonce($_POST['_wpnonce'], 'save-esb-settings')){
        //Save Options
        update_option('esb_host', $_POST['esb_host']);
        update_option('esb_username', $_POST['esb_username']);
        update_option('esb_password', $_POST['esb_password']);
        update_option('esb_database', $_POST['esb_database']);
    }
    else if(isset($_POST) && wp_verify_nonce($_POST['_wpnonce'], 'save-pdf-certificate-settings')){    
        if (!empty($_FILES) && is_uploaded_file($_FILES['pdf_certificate']['tmp_name'])) {
            $certificate = file_get_contents($_FILES['pdf_certificate']['tmp_name']);
            update_option('pdf_certificate', $certificate);
            update_option('pdf_certificate_name', $_FILES['pdf_certificate']['name']);
        }
        if (!empty($_FILES) && is_uploaded_file($_FILES['pdf_private_key']['tmp_name'])) {
            $private_key = file_get_contents($_FILES['pdf_private_key']['tmp_name']);
            update_option('pdf_private_key', $certificate);
            update_option('pdf_private_key_name', $_FILES['pdf_private_key']['name']);
        }
    }
    
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
    #compliancetest-settings{
        padding: 0;
        border-radius: 0;
    }
    #compliancetest-settings-nav{
        width: 210px;
        float: left;
    }
    #compliancetest-settings-wrapper{
        margin-left: 210px;
        border-top: solid 1px #ddd;
    }
    #compliancetest-settings-wrapper .widefat{
        clear: none;
    }
    #compliancetest-settings-wrapper h3{
        border-bottom: 1px solid #A0A0A0;
        font-size: 18px;
        margin: 0;
        padding-bottom: 10px;
    }
</style>
<div class="wrap">    
    <div class="icon32" id="icon-tools"> <br /> </div>    
    <h2>Compliancetest Setting</h2>            
    <div id="compliancetest-settings">
        <div id="compliancetest-settings-nav">
            <ul>
                <li><a href="#ct-eway-settings">eWay Settings</a></li>
                <li><a href="#ct-esb-settings">ESB Settings</a></li>
                <li><a href="#ct-subscriptions-settings">Subscriptions Settings</a></li>
                <li><a href="#ct-recaptcha-settings">Recaptcha Settings</a></li>
                <li><a href="#ct-mailchimp-settings">Mailchimp Settings</a></li>
                <li><a href="#ct-pdf-certificate-settings">PDF Certificate Settings</a></li>
            </ul>
        </div>
        <div id="compliancetest-settings-wrapper">
            <div id="ct-eway-settings">
                <h3>eWay Settings</h3>
                <form method="post" action="">      
                    <p>
                        <b>Payment Mode:</b> <label><input type="radio" name="eway_payment_mode" id="eway_payment_mode_live" value="live" <?php echo get_option('eway_payment_mode') == 'live' ? 'checked="checked"' : ''?> /> Live</label>
                            <label><input type="radio" name="eway_payment_mode" id="eway_payment_mode_sandbox" value="sandbox" <?php echo get_option('eway_payment_mode') != 'live' ? 'checked="checked"' : ''?> /> Test
                        </label>
                    </p>
                    <h3>Live Mode Settings</h3>
                    <table class="widefat">
                        <tr>
                            <td><label><b>Customer ID:</b></label></td>
                            <td><input type="text" name="eway_live_customer_id" id="eway_live_customer_id" value="<?php echo get_option('eway_live_customer_id')?>" autocomplete="off" /></td>
                        </tr>
                        <tr>
                            <td><label><b>User Name:</b></label></td>
                            <td><input type="text" name="eway_live_user_name" id="eway_live_user_name" value="<?php echo get_option('eway_live_user_name')?>" autocomplete="off" /></td>
                        </tr>
                        <tr>
                            <td><label><b>User PWD:</b></label></td>
                            <td><input type="password" name="eway_live_user_pwd" id="eway_live_user_pwd" value="<?php echo get_option('eway_live_user_pwd')?>" autocomplete="off" /></td>
                        </tr>
                    </table>
                    <br />
                    <h3>Sandbox Mode Settings</h3>
                    <table class="widefat">
                        <tr>
                            <td><label><b>Customer ID:</b></label></td>
                            <td><input type="text" name="eway_sandbox_customer_id" id="eway_sandbox_customer_id" value="<?php echo get_option('eway_sandbox_customer_id')?>" autocomplete="off" /></td>
                        </tr>
                        <tr>
                            <td><label><b>User Name:</b></label></td>
                            <td><input type="text" name="eway_sandbox_user_name" id="eway_sandbox_user_name" value="<?php echo get_option('eway_sandbox_user_name')?>" autocomplete="off" /></td>
                        </tr>
                        <tr>
                            <td><label><b>User PWD:</b></label></td>
                            <td><input type="password" name="eway_sandbox_user_pwd" id="eway_sandbox_user_pwd" value="<?php echo get_option('eway_sandbox_user_pwd')?>" autocomplete="off" /></td>
                        </tr>
                    </table>        
                    <?php submit_button()   ?>
                    <?php wp_nonce_field('save-eway-options'); ?>
                </form>  
            </div>
            <div id="ct-esb-settings">
                <h3>ESB Settings</h3>
                <form method="post" action="">
                    <table class="widefat">
                        <tr>
                            <th><label><b>Hostname:</b></label></th>
                            <td><input type="text" name="esb_host" id="esb_host" value="<?php echo get_option('esb_host')?>" autocomplete="off" /></td>
                        </tr>
                        <tr>
                            <th><label><b>Username:</b></label></th>
                            <td><input type="text" name="esb_username" id="esb_username" value="<?php echo get_option('esb_username')?>" autocomplete="off" /></td>
                        </tr>
                        <tr>
                            <th><label><b>Password:</b></label></th>
                            <td><input type="text" name="esb_password" id="esb_password" value="<?php echo get_option('esb_password')?>" autocomplete="off" /></td>
                        </tr>
                        <tr>
                            <th><label><b>Database Name:</b></label></th>
                            <td><input type="text" name="esb_database" id="esb_database" value="<?php echo get_option('esb_database')?>" autocomplete="off" /></td>
                        </tr>
                        
                    </table>      
                    <?php submit_button()   ?>
                    <?php wp_nonce_field('save-esb-settings'); ?>
                </form>
            </div>
            <div id="ct-subscriptions-settings">
                <h3>Subscription Settings</h3>        
                <form method="post" action="">      
                    <table class="widefat">
                        <tr>
                            <td><label><b>InArrears Count:</b></label></td>
                            <td><input type="text" name="inarrears_count" id="inarrears_count" value="<?php echo get_option('inarrears_count')?>" autocomplete="off" /> Days</td>
                        </tr>
                        <tr>
                            <td><label><b>Frozen Count:</b></label></td>
                            <td><input type="text" name="frozen_count" id="frozen_count" value="<?php echo get_option('frozen_count')?>" autocomplete="off" /> Days</td>
                        </tr>
                    </table>      
                    <?php submit_button()   ?>
                    <?php wp_nonce_field('save-subscription-settings'); ?>
                </form>  
            </div>
            <div id="ct-recaptcha-settings">
                <h3>Recaptcha Settings</h3>        
                <form method="post" action="">      
                    <table  class="widefat">
                        <tr>
                            <td><label><b>Public Key:</b></label></td>
                            <td><input type="text" name="recaptcha_public_key" id="recaptcha_public_key" value="<?php echo get_option('recaptcha_public_key')?>" size="50" autocomplete="off" /></td>
                        </tr>
                        <tr>
                            <td><label><b>Private Key:</b></label></td>
                            <td><input type="text" name="recaptcha_private_key" id="recaptcha_private_key" value="<?php echo get_option('recaptcha_private_key')?>" size="50" autocomplete="off" /></td>
                        </tr>
                    </table>      
                    <?php submit_button()   ?>
                    <?php wp_nonce_field('save-recaptcha-settings'); ?>
                </form>  
            </div>
            <div id="ct-mailchimp-settings">
                <h3>Mailchimp List for Registered Users</h3>
                <?php
                    $mailchimp = new Mailchimp(get_mailchimp_api_key(), array('ssl_verifypeer' => false));
                    $mailchimp_list = new mailchimp_lists($mailchimp);
                    $lists = $mailchimp_list->getList();
                    
                    ?>
                    <form method="post" action="">      
                    <?php
                    foreach($lists['data'] as $list)
                    {
                        ?><p><input type="radio" name="mailchimp_all_list_id" value="<?php echo $list['id']?>" <?php echo $list['id'] == get_option('mailchimp_all_list_id') ? 'checked="checked"' : ''?> /> <label><?php echo $list['name']?></label></p><?php
                    }
                    ?>
                        <?php submit_button()   ?>
                        <?php wp_nonce_field('save-mailchimp-all-list-settings'); ?>
                        <p>
                            <a href="<?php echo get_site_url() ?>?ext-action=add-users-to-mailchimp" target="_blank" class="button">Sync All Users with the selected list</a>
                            <br />(You will need to save your change first.)        
                        </p>
                    </form>  
                <?php
                ?>
            </div>
            <div id="ct-pdf-certificate-settings">
                <h3>PDF Certificate Settings</h3>        
                <form method="post" action="" enctype="multipart/form-data">      
                    <table  class="widefat">
                        <tr>
                            <td><label><b>Certificate (*.pem):</b></label></td>
                            <td>
                                <input type="file" name="pdf_certificate" id="pdf_certificate" />
                                <label><?php echo get_option('pdf_certificate_name')?></label>
                            </td>
                        </tr>
                        <tr>
                            <td><label><b>Private Key (*.pem):</b></label></td>
                            <td>
                                <input type="file" name="pdf_private_key" id="pdf_private_key" />
                                <label><?php echo get_option('pdf_private_key_name')?></label>
                            </td>
                        </tr>
                    </table>      
                    <?php submit_button()   ?>
                    <?php wp_nonce_field('save-pdf-certificate-settings'); ?>
                </form>  
            </div>
        </div>
    </div>
    
    <script type="text/javascript">
            jQuery(document).ready(function(){
                jQuery('#compliancetest-settings').tabs({"active": "<?php echo isset($_REQUEST['tab']) ? $_REQUEST['tab'] : 0?>"});                    
            })
            function saveEmailTemplates()
            {
                //Getting Actived Tabs
                var idx = jQuery('#compliancetest-settings-nav li.ui-state-default').index(jQuery('#compliancetest-settings-nav li.ui-tabs-active').get(0));
                jQuery('#email-tab-idx').val(idx);
                return true;
            }
            
      </script>
</div>
<?php   
}


function register_eway_settings()
{    
    register_setting('eway-settings', 'eway_payment_mode');
    register_setting('eway-settings', 'eway_live_customer_id');
    register_setting('eway-settings', 'eway_live_user_name');
    register_setting('eway-settings', 'eway_live_user_pwd');
    register_setting('eway-settings', 'eway_sandbox_customer_id');
    register_setting('eway-settings', 'eway_sandbox_user_name');
    register_setting('eway-settings', 'eway_sandbox_user_pwd');        
    register_setting('subscription-settings', 'inarrears_count');        
    register_setting('subscription-settings', 'frozen_count');        
    register_setting('recaptcha-settings', 'recaptcha_public_key');        
    register_setting('recaptcha-settings', 'recaptcha_private_key');        
}

function get_eway_payment_url()
{
    if(get_option('eway_payment_mode') == 'live')
    {
        return 'https://www.eway.com.au/gateway_cvn/xmlpayment.asp';
    }else{
        return 'https://www.eway.com.au/gateway_cvn/xmltest/testpage.asp';        
    }
}

function get_eway_rebill_webservice_url()
{
    if(get_option('eway_payment_mode') == 'live')
    {
        return 'https://www.eway.com.au/gateway/rebill/manageRebill.asmx';
    }else{
        return 'https://www.eway.com.au/gateway/rebill/test/manageRebill_test.asmx';        
    }
    
}

function get_eway_token_webservice_url()
{
    if(get_option('eway_payment_mode') == 'live')
    {
        return 'https://www.ewaygateway.com/gateway/ManagedPaymentService/managedCreditCardPayment.asmx';
    }else{
        return 'https://www.eway.com.au/gateway/ManagedPaymentService/test/managedCreditCardPayment.asmx';        
    }
    
}

function get_eway_pre_auth_url()
{
    if(get_option('eway_payment_mode') == 'live')
    {
        return 'https://www.eway.com.au/gateway_cvn/xmlauth.asp';
    }else{
        return 'https://www.eway.com.au/gateway_cvn/xmltest/authtestpage.asp';        
    }
    
}

function get_eway_pre_auth_void_url()
{
    if(get_option('eway_payment_mode') == 'live')
    {
        return 'https://www.eway.com.au/gateway/xmlauthvoid.asp';
    }else{
        return 'https://www.eway.com.au/gateway/xmltest/authvoidtestpage.asp';        
        /*https://www.eway.com.au/gateway/xmltest/authcompletetestpage.asp
        https://www.eway.com.au/gateway/xmltest/authvoidtestpage.asp*/
    }
    
}




function get_eway_customer_id()
{
    if(get_option('eway_payment_mode') == 'live')
    {
        return get_option('eway_live_customer_id');
    }else{
        return get_option('eway_sandbox_customer_id');
    }
}

function get_eway_user_name()
{
    if(get_option('eway_payment_mode') == 'live')
    {
        return get_option('eway_live_user_name');
    }else{
        return get_option('eway_sandbox_user_name');
    }
}

function get_eway_user_pwd()
{
    if(get_option('eway_payment_mode') == 'live')
    {
        return get_option('eway_live_user_pwd');
    }else{
        return get_option('eway_sandbox_user_pwd');
    }
}



