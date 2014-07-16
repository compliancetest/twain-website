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
        <br clear="all" />
        <form name="adminform" action="users.php?page=processing" method="post">
        <?php
            echo $listTable->display();
        ?>
        </form>
    </div>
    <div class="wrap">
        <a href="<?php echo admin_url()?>admin.php?page=add-organisation&org-action=reload-organisation-from">Update Organisations From Xero</a>
        <div class="clear"></div>
        <a href="<?php echo admin_url()?>admin.php?page=add-organisation&org-action=reload-organisation-to">Load Organisations List To Xero</a>
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
        <?php if(isset($_GET['org-message'])){ ?>
        <div id="message" class="updated below-h2"><p><?php echo $_GET['org-message']?></p></div>
        <?php 
        $_GET['org-message'] = null;
        unset($_GET['org-message']);
        } ?>
        <br clear="all" />
        <form name="adminform" action="<?php echo admin_url()?>admin.php?page=add-organisation<?php echo $id ? ('&id=' . $id) : ''?>" method="post">
            <table class="widefat" style="width: auto;">
                <tr>
                    <th>Contact ID</th>
                    <td><input type="text" name="contact_id" id="contact_id" value="<?php echo $data['contact_id']?>" size="40" <?php if( $id ):?> readonly="readonly"<?php endif;?>/></td>
                </tr>
                <tr>
                    <th>Organisation Name</th>
                    <td><input type="text" name="organisation_name" id="organisation_name" value="<?php echo $data['organisation_name']?>"/></td>
                </tr>
                <tr>    
                    <th>Organisation Domain</th>
                    <td><input type="text" name="organisation_domain" id="organisation_domain" value="<?php echo $data['organisation_domain']?>"/></td>
                </tr>
                <tr>    
                    <th>Invoice Me</th>
                    <td><input type="checkbox" name="invoice_me" id="invoice_me" value="1" <?php echo $data['invoice_me'] == '1' ? 'checked="checked"' : ''?> /></td>
                </tr>
                <tr>
                    <th>Organisation Administrator</th>
                    <td>
                        <?php
                            //Getting All users
                            $users = get_users();
                        ?>
                        <select name="admin_id" id="admin_id">
                            <option value="">- Select -</option>
                            <?php foreach($users as $u): ?>
                            <option value="<?php echo $u->ID?>" <?php echo $u->ID == $data['admin_id'] ? 'selected="selected"' : ''?>><?php echo $u->display_name ?> (<?php echo $u->user_email ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td colspan="2"><b>Primary Contact</b></td>
                </tr>
                <tr>    
                    <th>First Name</th>
                    <td><input type="text" name="contact_first_name" id="contact_first_name" value="<?php echo $data['contact_first_name']?>"/></td>
                </tr>
                <tr>    
                    <th>Last Name</th>
                    <td><input type="text" name="contact_last_name" id="contact_last_name" value="<?php echo $data['contact_last_name']?>"/></td>
                </tr>
                <tr>    
                    <th>Email Address</th>
                    <td><input type="text" name="contact_email" id="contact_email" value="<?php echo $data['contact_email']?>"/></td>
                </tr>
                <tr>    
                    <th>ABN</th>
                    <td><input type="text" name="abn" id="abn" value="<?php echo $data['abn']?>"/></td>
                </tr>
                <tr><td colspan="2"><b>Billing Address</b></td></tr>
                <tr>    
                    <th>Attention</th>
                    <td><input type="text" name="billing_address_attention" id="billing_address_attention" value="<?php echo $data['billing_address_attention']?>" size="40"/></td>
                </tr>                
                <tr>    
                    <th>Address1</th>
                    <td><input type="text" name="billing_address1" id="billing_address1" value="<?php echo $data['billing_address1']?>" size="40"/></td>
                </tr>                
                <tr>    
                    <th>Address2</th>
                    <td><input type="text" name="billing_address2" id="billing_address2" value="<?php echo $data['billing_address2']?>" size="40"/></td>
                </tr>                
                <tr>    
                    <th>Address3</th>
                    <td><input type="text" name="billing_address3" id="billing_address3" value="<?php echo $data['billing_address3']?>" size="40"/></td>
                </tr>                
                <tr>    
                    <th>Address4</th>
                    <td><input type="text" name="billing_address4" id="billing_address4" value="<?php echo $data['billing_address4']?>" size="40"/></td>
                </tr>                
                <tr>    
                    <th>City</th>
                    <td><input type="text" name="billing_city" id="billing_city" value="<?php echo $data['billing_city']?>"/></td>
                </tr>                
                <tr>    
                    <th>State</th>
                    <td><input type="text" name="billing_state" id="billing_state" value="<?php echo $data['billing_state']?>"/></td>
                </tr>                
                <tr>    
                    <th>Postcode</th>
                    <td><input type="text" name="billing_postcode" id="billing_postcode" value="<?php echo $data['billing_postcode']?>"/></td>
                </tr>                
                <tr>    
                    <th>Country</th>
                    <td><input type="text" name="billing_country" id="billing_country" value="<?php echo $data['billing_country']?>"/></td>
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
            $response = $organisationClass->save();
            if( ! is_string( $response ) )
            {
                echo 'Organisation Saved.';
                redirect_and_exit();
            }else{
                $_GET['org-message'] = $wpdb->last_error | $response;
                return;
            }
        } else if( $action == 'reload-organisation-from' ){
            $xero = new CT_Xero();
            $contacts = $xero->getContacts();
            if( $contacts ){
                foreach( $contacts AS $contact ){
                    $address_key = $phone_key = 0;
                    foreach( $contact['Addresses']['Address'] AS $key => $address ) {
                        if( $address['AddressType'] == 'POBOX' ){
                            $address_key = $key;
                        }
                    }
                    foreach( $contact['Phones']['Phone'] AS $key => $phone ) {
                        if( $phone['PhoneType'] == 'DEFAULT' ){
                            $phone_key = $key;
                        }
                    }
                    $organisationData = array(
                        'contact_id'                => $contact['ContactID'],
                        'organisation_name'         => $contact['Name'],
                        'invoice_me'                => 0,
                        'contact_first_name'        => $contact['FirstName'],
                        'contact_last_name'         => $contact['LastName'],
                        'contact_email'             => $contact['EmailAddress'],
                        'abn'                       => $contact['TaxNumber'],
                        'billing_address_attention' => $contact['Addresses']['Address'][$address_key]['AttentionTo'],
                        'billing_address1'           => $contact['Addresses']['Address'][$address_key]['AddressLine1'],
                        'billing_address2'           => $contact['Addresses']['Address'][$address_key]['AddressLine2'],
                        'billing_address3'           => $contact['Addresses']['Address'][$address_key]['AddressLine3'],
                        'billing_address4'           => $contact['Addresses']['Address'][$address_key]['AddressLine4'],
                        'billing_city'              => $contact['Addresses']['Address'][$address_key]['City'],
                        'billing_postcode'          => $contact['Addresses']['Address'][$address_key]['PostalCode'],
                        'billing_state'             => $contact['Addresses']['Address'][$address_key]['Region'],
                        'billing_country'           => $contact['Addresses']['Address'][$address_key]['Country'],
                        'phonenumber'               => $contact['Phones']['Phone'][1]['PhoneNumber'],
                        'phonenumber_areacode'      => $contact['Phones']['Phone'][1]['PhoneAreaCode'],
                        'phonenumber_countrycode'   => $contact['Phones']['Phone'][1]['PhoneCountryCode']
                    );
                    $table_id = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$wpdb->prefix}organisations WHERE contact_id = %s ", $contact['ContactID'] ) );

                    //we update only entries that already exists in local database
                    if( $table_id ){
                        $wpdb->update( "{$wpdb->prefix}organisations",
                            $organisationData,
                            array( 'id' => $table_id ),
                            array(
                                '%s', '%s', '%d', '%s', '%s', '%s', "%d", '%s', '%s', '%s', '%s', '%s', '%s', '%d', "%s", '%s', '%d', '%d', '%d'
                            ),
                            array( '%d' )
                        );
                    }
                }
            }
            redirect_and_exit();
        } else if( $action == 'reload-organisation-to'){
            $organisations_list = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}organisations");
            if( $organisations_list ){
                foreach( $organisations_list AS $organisation ){
                    $xero = new CT_Xero();
                    $xeroContact = $xero->upsertContact( (array) $organisation );
                    if( isset( $xeroContact['Contacts']['Contact']['ContactID'] ) ){
                        $wpdb->update("{$wpdb->prefix}organisations",
                            array( 'contact_id' => $xeroContact['Contacts']['Contact']['ContactID'] ),
                            array( 'id' => $organisation->id ),
                            array( '%s' ),
                            array( '%d' )
                        );
                    }
                }
            }
            redirect_and_exit();
        }
    }
    
}
function redirect_and_exit(){
    ?>
    <script type="text/javascript">
        setTimeout(function(){
            document.location.href = '<?php echo admin_url()?>admin.php?page=manage-organisations';
        }, 1000);
    </script>
<?php

    exit;
}