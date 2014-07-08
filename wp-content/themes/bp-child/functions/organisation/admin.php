<?php
/**
* Manage Organisations
*/

require_once (THE_FUNCTION . "/organisation/organisations-list-table.php");
require_once(THE_FUNCTION . "/organisation/class.organisation.php");

//Create Menus
add_action("admin_menu", "ct_add_manage_organisation_menu");
function ct_add_manage_organisation_menu()
{
    add_menu_page("Manage Organsations", "Organisations", "manage_options", "manage-organisations", "ct_show_organisations_list");
    add_submenu_page("manage-organisations", "Add Organsation", "Add New", "manage_options", "add-organisation", "ct_show_new_organisation");
}

function ct_show_organisations_list()
{
    $listTable = new CT_Organisations_List_Table();
    $listTable->prepare_items();
    ?>
    <div class="wrap">
        <h2>Organisations</h2>        
        <?php if(isset($_SESSION['org-message'])){ ?>
        <div id="message" class="updated below-h2"><p><?php echo $_SESSION['org-message']?></p></div>
        <?php 
        $_SESSION['org-message'] = null;
        unset($_SESSION['org-message']);
        } ?>
        <br clear="all" />
        <form name="adminform" action="users.php?page=processing" method="post">
        <?php
            echo $listTable->display();
        ?>
        </form>
    </div>
    <?php       
}

/**
* Add or Edit Organisation
* 
*/
function ct_show_new_organisation()
{
    $variables = get_class_vars('CT_Organisation');
    $data = array();    
    foreach(array_keys($variables) as $_m)
    {            
        $data[$_m] = '';
    }
    
    if(isset($_GET['id']))
    {
        $id = $_GET['id'];        
        $organisationClass = new CT_Organisation($_GET['id']);
        foreach($data as $_m=>$_v)
        {            
            $data[$_m] = $organisationClass->$_m;
        }    
    }
    else
    {
        $id = null;    
    }
    ?>
    <div class="wrap">
        <h2><?php echo $id ? 'Edit' : 'New'?> Organisation</h2>
        <?php if(isset($_SESSION['org-message'])){ ?>
        <div id="message" class="updated below-h2"><p><?php echo $_SESSION['org-message']?></p></div>
        <?php 
        $_SESSION['org-message'] = null;
        unset($_SESSION['org-message']);
        } ?>
        <br clear="all" />
        <form name="adminform" action="<?php echo admin_url()?>admin.php?page=add-organisation<?php echo $id ? ('&id=' . $id) : ''?>" method="post">
            <table class="widefat" style="width: auto;">
                <tr>    
                    <th>Organisation Name</th>
                    <td><input type="text" name="organisation_name" id="organisation_name" value="<?php echo $data['organisation_name']?>" /></td>
                </tr>
                <tr>    
                    <th>Xero Contact Name</th>
                    <td><input type="text" name="xero_contact_name" id="xero_contact_name" value="<?php echo $data['xero_contact_name']?>" /></td>
                </tr>
                <tr>    
                    <th>Organisation Domain</th>
                    <td><input type="text" name="organisation_domain" id="organisation_domain" value="<?php echo $data['organisation_domain']?>" /></td>
                </tr>
                <tr>    
                    <th>Invoice Me</th>
                    <td><input type="checkbox" name="invoice_me" id="invoice_me" value="1" <?php echo $data['invoice_me'] == '1' ? 'checked="checked"' : ''?> /></td>
                </tr>
                <tr>
                    <td colspan="2"><b>Primary Contact</b></td>
                </tr>
                <tr>    
                    <th>First Name</th>
                    <td><input type="text" name="contact_first_name" id="contact_first_name" value="<?php echo $data['contact_first_name']?>" /></td>
                </tr>
                <tr>    
                    <th>Last Name</th>
                    <td><input type="text" name="contact_last_name" id="contact_last_name" value="<?php echo $data['contact_last_name']?>" /></td>
                </tr>
                <tr>    
                    <th>Email Address</th>
                    <td><input type="text" name="contact_email" id="contact_email" value="<?php echo $data['contact_email']?>" /></td>
                </tr>
                <tr>    
                    <th>ABN</th>
                    <td><input type="text" name="abn" id="abn" value="<?php echo $data['abn']?>" /></td>
                </tr>
                <tr><td colspan="2"><b>Billing Address</b></td></tr>
                <tr>    
                    <th>Attention</th>
                    <td><input type="text" name="billing_address_attention" id="billing_address_attention" value="<?php echo $data['billing_address_attention']?>" size="40" /></td>
                </tr>                
                <tr>    
                    <th>Address</th>
                    <td><input type="text" name="billing_address" id="billing_address" value="<?php echo $data['billing_address']?>" size="40" /></td>
                </tr>                
                <tr>    
                    <th>City</th>
                    <td><input type="text" name="billing_city" id="billing_city" value="<?php echo $data['billing_city']?>" /></td>
                </tr>                
                <tr>    
                    <th>State</th>
                    <td><input type="text" name="billing_state" id="billing_state" value="<?php echo $data['billing_state']?>" /></td>
                </tr>                
                <tr>    
                    <th>Postcode</th>
                    <td><input type="text" name="billing_postcode" id="billing_postcode" value="<?php echo $data['billing_postcode']?>" /></td>
                </tr>                
                <tr>    
                    <th>Country</th>
                    <td><input type="text" name="billing_country" id="billing_country" value="<?php echo $data['billing_country']?>" /></td>
                </tr>
                <tr>    
                    <th>Telephone</th>
                    <td>
                        <input type="text" name="phonenumber_countrycode" id="phonenumber_countrycode" size="5" placeholder="Country" value="<?php echo $data['phonenumber_countrycode']?>" />
                        <input type="text" name="phonenumber_areacode" id="phonenumber_areacode" size="8" placeholder="Area Code" value="<?php echo $data['phonenumber_areacode']?>" />
                        <input type="text" name="phonenumber" id="phonenumber" size="15" placeholder="Phonenumber" value="<?php echo $data['phonenumber']?>" />
                    </td>
                </tr>
                <tr><td colspan="2"><input type="submit" value="Save Organisation" class="button button-primary" /></td></tr>  
            </table>
            <input type="hidden" name="id" value="<?php echo $id?>" />
            <input type="hidden" name="org-action" value="save-organisation" />
        </form>
    </div>
    <?php
}


add_action("admin_init", "ct_process_organisation_admin_actions");
function ct_process_organisation_admin_actions()
{
    global $wpdb;
    
    if(isset($_REQUEST['org-action']))
    {
        $action = $_REQUEST['org-action'];
        
        if($action == 'save-organisation')
        {
            //Save Organisation    
            $organisationClass = new CT_Organisation($_POST['id']);
            if(!isset($_POST['invoice_me']))
                $_POST['invoice_me'] = 0;
            $organisationClass->bind($_POST);
            if($organisationClass->save())
            {
                $_SESSION['message'] = 'Organisation Saved.';
                wp_redirect(admin_url() . "admin.php?page=manage-organisations");                
                exit;
            }else{
                $_SESSION['message'] = $wpdb->last_error;
                return;
            }
        }
    }
    
}