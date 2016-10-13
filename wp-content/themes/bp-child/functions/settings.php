<?php
/**
* Manage Subscription Settings
*/

add_action('admin_menu', 'add_compliancetest_settings_page');
function add_compliancetest_settings_page()
{
    add_options_page('TWAIN Settings', 'TWAIN Settings', 'administrator', 'compliancetest-settings', 'create_compliancetest_settings_page');
    
    add_action('admin_init', 'register_eway_settings');
}

function create_compliancetest_settings_page()
{
    if (isset($_POST) && wp_verify_nonce($_POST['_wpnonce'], 'save-recaptcha-settings')) {
        update_option('recaptcha_public_key', $_POST['recaptcha_public_key']);
        update_option('recaptcha_private_key', $_POST['recaptcha_private_key']);
    } else if (isset($_POST) && wp_verify_nonce($_POST['_wpnonce'], 'save-website-settings')) {
        update_option('tw_site_title', $_POST['tw_site_title']);
        update_option('tw_site_organisation', $_POST['tw_site_organisation']);
        update_option('tw_contact_us_email', $_POST['tw_contact_us_email']);
    } else if (isset($_POST) && wp_verify_nonce($_POST['_wpnonce'], 'save-xml-size-limit')) {
        update_option('aws_s3_key', $_POST['aws_s3_key']);
        update_option('aws_s3_secret', $_POST['aws_s3_secret']);
        update_option('aws_s3_url', $_POST['aws_s3_url']);
    } else if (isset($_POST) && wp_verify_nonce($_POST['_wpnonce'], 'save-pdf-certificate-settings')) {
        if (!empty($_FILES) && is_uploaded_file($_FILES['pdf_certificate']['tmp_name'])) {
            $certificate = file_get_contents($_FILES['pdf_certificate']['tmp_name']);
            update_option('pdf_certificate', $certificate);
            update_option('pdf_certificate_name', $_FILES['pdf_certificate']['name']);
        }
        if (!empty($_FILES) && is_uploaded_file($_FILES['pdf_private_key']['tmp_name'])) {
            $private_key = file_get_contents($_FILES['pdf_private_key']['tmp_name']);
            update_option('pdf_private_key', $private_key);
            update_option('pdf_private_key_name', $_FILES['pdf_private_key']['name']);
        }
    } else if (isset($_POST) && wp_verify_nonce($_POST['_wpnonce'], 'save-cloudsearch-settings')) {
        update_option('cloudsearch_domain_name', $_POST['cloudsearch_domain_name']);
        update_option('cloudsearch_fulltext_domain_name', $_POST['cloudsearch_fulltext_domain_name']);
    } else if (isset($_POST) && wp_verify_nonce($_POST['_wpnonce'], 'save-surveys-settings')) {
        update_option('surveymonkey_key', $_POST['surveymonkey_key']);
        update_option('surveymonkey_secret', $_POST['surveymonkey_secret']);
        update_option('surveymonkey_token', $_POST['surveymonkey_token']);
    } else if (isset($_POST) && wp_verify_nonce($_POST['_wpnonce'], 'save-transaction-settings')) {
        update_option('transactions_purge_period', $_POST['transactions_purge_period']);
        if(!empty($_POST['server_validation'])) {
            update_option('server_validation', 'yes');
        } else {
            update_option('server_validation', 'no');
        }
        if(!empty($_POST['image_viewer'])) {
            update_option('image_viewer', 'yes');
        } else {
            update_option('image_viewer', 'no');
        }
        if(!empty($_POST['explain_requests'])) {
            update_option('explain_requests', 'yes');
        } else {
            update_option('explain_requests', 'no');
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
    <h2><?php echo get_site_title();?> Setting</h2>
    <div id="compliancetest-settings">
        <div id="compliancetest-settings-nav">
            <ul>                
                <li><a href="#ct-website">Website settings</a></li>
                <li><a href="#ct-recaptcha-settings">Recaptcha Settings</a></li>
                <li><a href="#ct-pdf-certificate-settings">PDF Certificate Settings</a></li>
                <li><a href="#ct-s3-xml-max-size">AWS</a></li>
                <li><a href="#ct-surveys">SurveyMonkey Settings</a></li>
                <li><a href="#ct-cloudsearch-settings">CloudSearch Settings</a></li>
                <li><a href="#ct-transactions">Transactions</a></li>
            </ul>
        </div>
        <div id="compliancetest-settings-wrapper">
            <div id="ct-website">
                <h3>Website</h3>
                <form method="post" action="">
                    <table  class="widefat">
                        <tr>
                            <td><label><b>Site Title:</b></label></td>
                            <td>
                                <input type="text" name="tw_site_title" id="tw_site_title" value="<?php echo get_site_title()?>" size="50" autocomplete="off" />
                            </td>
                        </tr>
                        <tr>
                            <td><label><b>Site Organization:</b></label></td>
                            <td><input type="text" name="tw_site_organisation" id="tw_site_organisation" value="<?php echo get_option('tw_site_organisation')?>" size="50" autocomplete="off" /></td>
                        </tr>
                        <tr>
                            <td><label><b>Contact email:</b></label></td>
                            <td><input type="text" name="tw_contact_us_email" id="tw_contact_us_email" value="<?php echo get_option('tw_contact_us_email')?>" size="50" autocomplete="off" /></td>
                        </tr>
                    </table>
                    <?php submit_button()   ?>
                    <?php wp_nonce_field('save-website-settings'); ?>
                    <input type="hidden" name="tab_index" value="4">
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
                    <input type="hidden" name="tab_index" value="4">
                </form>  
            </div>
            <div id="ct-pdf-certificate-settings">
                <h3>PDF Certificate Settings</h3>        
                <form method="post" action="" enctype="multipart/form-data">      
                    <table  class="widefat">
                        <tr>
                            <td><label><b>Certificate (*.pem):</b></label></td>
                            <td>
                                <input type="file" name="pdf_certificate" id="pdf_certificate" />
                                <?php $pdf_certificate_name = get_option('pdf_certificate_name'); ?>
                                <label><?php echo ($pdf_certificate_name) ? ('(Currently '.$pdf_certificate_name.')') : (''); ?></label>
                            </td>
                        </tr>
                        <tr>
                            <td><label><b>Private Key (*.pem):</b></label></td>
                            <td>
                                <input type="file" name="pdf_private_key" id="pdf_private_key" />
                                <?php $pdf_private_key_name = get_option('pdf_private_key_name'); ?>
                                <label><?php echo ($pdf_private_key_name) ? ('(Currently '.$pdf_private_key_name.')') : (''); ?></label>
                            </td>
                        </tr>
                    </table>      
                    <?php submit_button()   ?>
                    <?php wp_nonce_field('save-pdf-certificate-settings'); ?>
                    <input type="hidden" name="tab_index" value="6">
                </form>  
            </div>

            <div id="ct-cloudsearch-settings">
                <h3>CloudSearch Settings</h3>
                <form method="post" action="" enctype="multipart/form-data">
                    <table class="widefat">
                        <tr>
                            <th><label><b>Registry Search Domain Name:</b></label></th>
                            <td><input type="text" name="cloudsearch_domain_name" id="cloudsearch_domain_name" value="<?php echo get_option('cloudsearch_domain_name')?>" size="50" autocomplete="off" /></td>
                        </tr>
                        <tr>
                            <th><label><b>Site Search Domain Name:</b></label></th>
                            <td><input type="text" name="cloudsearch_fulltext_domain_name" id="cloudsearch_fulltext_domain_name" value="<?php echo get_option('cloudsearch_fulltext_domain_name')?>" size="50" autocomplete="off" /></td>
                        </tr>
                    </table>
                    <?php submit_button()   ?>
                    <?php wp_nonce_field('save-cloudsearch-settings'); ?>
                    <input type="hidden" name="tab_index" value="7">
                </form>
            </div>
            <div id="ct-s3-xml-max-size">
                <h3>AWS</h3>
                <form method="post" action="">
                    <table class="widefat">

                        <tr>
                            <td><label><b>S3 Access Key:</b></label></td>
                            <td><input type="text" name="aws_s3_key" id="aws_s3_key" size="50" value="<?php echo get_option('aws_s3_key')?>" autocomplete="off" /></td>
                        </tr>
                        <tr>
                            <td><label><b>S3 Secret Key:</b></label></td>
                            <td><input type="text" name="aws_s3_secret" id="aws_s3_secret" size="50" value="<?php echo get_option('aws_s3_secret')?>" autocomplete="off" /></td>
                        </tr>
                        <tr>
                            <td><label><b>Data Bucket:</b></label></td>
                            <td><input type="text" name="aws_s3_url" id="aws_s3_url" size="50" value="<?php echo get_option('aws_s3_url')?>" autocomplete="off" /></td>
                        </tr>
                    </table>
                    <?php submit_button()   ?>
                    <?php wp_nonce_field('save-xml-size-limit'); ?>
                    <input type="hidden" name="tab_index" value="8">
                </form>
            </div>

            <div id="ct-surveys">
                <h3>SurveyMonkey Settings</h3>
                <form method="post" action="" enctype="multipart/form-data">
                    <table class="widefat">
                        <tr>
                            <th><label><b>API Key:</b></label></th>
                            <td><input type="text" name="surveymonkey_key" id="surveymonkey_key" value="<?php echo get_option('surveymonkey_key')?>" size="50" autocomplete="off" /></td>
                        </tr>
                        <tr>
                            <th><label><b>API Secret:</b></label></th>
                            <td><input type="text" name="surveymonkey_secret" id="surveymonkey_secret" value="<?php echo get_option('surveymonkey_secret')?>" size="50" autocomplete="off" /></td>
                        </tr>
                        <tr>
                            <th><label><b>Access Token:</b></label></th>
                            <td><input type="text" name="surveymonkey_token" id="surveymonkey_token" value="<?php echo get_option('surveymonkey_token')?>" size="50" autocomplete="off" /></td>
                        </tr>
                    </table>
                    <?php submit_button()   ?>
                    <?php wp_nonce_field('save-surveys-settings'); ?>
                    <input type="hidden" name="tab_index" value="9">
                </form>
            </div>

            <div id="ct-transactions">
                <h3>Transactions Settings</h3>
                <form method="post" action="" enctype="multipart/form-data">
                    <table class="widefat">
                        <tr>
                            <th><label><b>Purge period(days):</b></label></th>
                            <td><input type="text" name="transactions_purge_period" id="transactions_purge_period" value="<?php echo get_option('transactions_purge_period')?>" size="50" autocomplete="off" /></td>
                        </tr>
                         <tr>
                            <td><label><b>Enable Server Validation:</b></label></td>
                            <td><input type="checkbox" name="server_validation" id="server_validation" size="50" <?php if( get_option('server_validation') == 'yes' ):?> checked="checked" <?php endif;?> autocomplete="off" /></td>
                        </tr>
                         <tr>
                            <td><label><b>Enable Image Viewer:</b></label></td>
                            <td><input type="checkbox" name="image_viewer" id="image_viewer" size="50" <?php if( get_option('image_viewer') == 'yes' ):?> checked="checked" <?php endif;?> autocomplete="off" /></td>
                        </tr>
                        <tr>
                            <td><label><b>Enable Explain Requests:</b></label></td>
                            <td><input type="checkbox" name="explain_requests" id="explain_requests" size="50" <?php if( get_option('explain_requests') == 'yes' ):?> checked="checked" <?php endif;?> autocomplete="off" /></td>
                        </tr>
                    </table>
                    <?php submit_button()   ?>
                    <?php wp_nonce_field('save-transaction-settings'); ?>
                    <input type="hidden" name="tab_index" value="9">
                </form>
            </div>

        </div>
    </div>
    
    <script type="text/javascript">
            jQuery(document).ready(function(){
                jQuery('#compliancetest-settings').tabs({"active": "<?php echo isset($_REQUEST['tab_index']) ? $_REQUEST['tab_index'] : 0?>"});                    
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
    register_setting('recaptcha-settings', 'recaptcha_public_key');
    register_setting('recaptcha-settings', 'recaptcha_private_key');        
}



