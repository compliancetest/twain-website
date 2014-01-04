<?php
/**
* Manage Subscription Settings
*/

add_action('admin_menu', 'add_eway_options_page');
function add_eway_options_page()
{
    add_options_page('Payment Settings', 'Payment Settings', 'administrator', 'payment-settings', 'create_payment_settings_page');
    
    add_action('admin_init', 'register_eway_settings');
}
function create_payment_settings_page()
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
        
    }
?>
<div id="eway-options-wrap">    
    <div class="icon32" id="icon-tools"> <br /> </div>    
    <h2>eWay Settings</h2>    
    <p>You can config the information for eway payment.</p>    
    <form method="post" action="">      
        <p>
            <b>Payment Mode:</b> <label><input type="radio" name="eway_payment_mode" id="eway_payment_mode_live" value="live" <?php echo get_option('eway_payment_mode') == 'live' ? 'checked="checked"' : ''?> /> Live</label>
                <label><input type="radio" name="eway_payment_mode" id="eway_payment_mode_sandbox" value="sandbox" <?php echo get_option('eway_payment_mode') != 'live' ? 'checked="checked"' : ''?> /> Test
            </label>
        </p>
        <h3>Live Mode Settings</h3>
        <table cellpadding="5">
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
        <h3>Sandbox Mode Settings</h3>
        <table cellpadding="5">
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
    <hr />
    <h2>Subscription Settings</h2>        
    <form method="post" action="">      
        <table cellpadding="5">
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



