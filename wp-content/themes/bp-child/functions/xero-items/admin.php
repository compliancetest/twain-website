<?php
/**
* Manage Xero Items
*/
require_once (THE_FUNCTION . "/xero-api/CT_Xero.php");
require_once (THE_FUNCTION . "/xero-items/xero-items-list-table.php");
require_once (THE_FUNCTION . "/xero-items/xero-accounts-list-table.php");
require_once(THE_FUNCTION . "/xero-items/class.xeroitem.php");

//Create Menus
add_action("admin_menu", "ct_add_manage_xeroitems_menu");
function ct_add_manage_xeroitems_menu()
{
    add_menu_page("Manage Xero Items", "Xero Items", "manage_options", "manage-xeroitems", "ct_show_xeroitems_list");
    add_submenu_page("manage-xeroitems", "Xero Accounts", "Xero Accounts", "manage_options", "xero-accounts", "ct_show_xero_accounts_list");
    add_submenu_page("manage-xeroitems", "Add Xero Item", "Add New", "manage_options", "add-xeroitem", "ct_show_new_xeroitem");
    
    
}

function ct_show_xero_accounts_list()
{
    $listTable = new CT_Xero_Accounts_List_Table();
    $listTable->prepare_items();
    ?>
    <div class="wrap">
        <h2>Xero Accounts</h2>
        <br clear="all" />
        <form name="adminform" action="" method="post">
        <?php
            echo $listTable->display();
        ?>
        </form>
    </div>
    <div class="wrap">
        <a href="<?php echo admin_url()?>admin.php?org-action=reload-xeroaccounts">Update Accounts From Xero</a>
    </div>
    <?php       
}

function ct_show_xeroitems_list()
{
    $listTable = new CT_Xeroitems_List_Table();
    $listTable->prepare_items();
    ?>
    <div class="wrap">
        <h2>Xero Items</h2>
        <br clear="all" />
        <form name="adminform" action="users.php?page=processing" method="post">
        <?php
            echo $listTable->display();
        ?>
        </form>
    </div>
    <div class="wrap">
        <a href="<?php echo admin_url()?>admin.php?page=add-xeroitem&org-action=reload-xeroitems">Update Items From Xero</a>
    </div>
    <?php       
}

/**
* Add or Edit Xero Item
* 
*/
function ct_show_new_xeroitem()
{
    global $wpdb;
    
    $variables = get_class_vars('CT_Xeroitem');
    $data = array();    
    foreach(array_keys($variables) as $_m)
    {            
        $data[$_m] = '';
    }
    
    if(isset($_GET['id']))
    {
        $id = $_GET['id'];        
        $xeroitemClass = new CT_Xeroitem($_GET['id']);
        foreach($data as $_m=>$_v)
        {            
            $data[$_m] = $xeroitemClass->$_m;
        }    
    }
    else
    {
        $id = null;    
    }
    
    //Getting Accounts
    $query = "SELECT * FROM {$wpdb->prefix}xero_accounts ORDER BY Class ASC, Name ASC";
    $accounts = $wpdb->get_results($query);
    ?>
    <div class="wrap">
        <h2><?php echo $id ? 'Edit' : 'New'?> Xero Item</h2>
        <?php if(isset($_GET['org-message'])){ ?>
        <div id="message" class="updated below-h2"><p><?php echo $_GET['org-message']?></p></div>
        <?php 
        $_GET['org-message'] = null;
        unset($_GET['org-message']);
        } ?>
        <br clear="all" />
        <form name="adminform" action="<?php echo admin_url()?>admin.php?page=add-xeroitem<?php echo $id ? ('&id=' . $id) : ''?>" method="post">
            <table class="widefat" style="width: auto;">
                <tr>    
                    <th>Code</th>
                    <td><input type="text" name="code" id="code" value="<?php echo $data['code']?>" required="required"/></td>
                </tr>
                <tr>    
                    <th>Description</th>
                    <td><input type="text" name="description" id="description" value="<?php echo $data['description']?>" required="required"/></td>
                </tr>
                <tr>    
                    <th>Unit Price</th>
                    <td><input type="text" name="unit_price" id="unit_price" value="<?php echo $data['unit_price']?>" required="required"/></td>
                </tr>
                <tr>    
                    <th>Account Code</th>
                    <td>
                        <select name="account_code" id="account_code">
                            <option>- Select -</option>
                        <?php 
                        $optGroup = null;
                        foreach($accounts as $acc): ?>
                        <?php if($acc->Class != $optGroup) :?>
                        <optgroup label="<?php echo $acc->Class?>"><?php echo $acc->Class?></optgroup>
                        <?php endif; ?>
                            <option value="<?php echo $acc->Code?>" <?php echo $data['account_code'] == $acc->Code ? "selected='selected'" : ""?>><?php echo $acc->Name; ?></option>
                        
                        <?php 
                        $optGroup = $acc->Class;                        
                        
                        endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr><td colspan="2"><input type="submit" value="Save Xero Item" class="button button-primary" /></td></tr>
            </table>
            <input type="hidden" name="id" value="<?php echo $id?>" />
            <input type="hidden" name="org-action" value="save-xeroitem" />
        </form>
    </div>
    <?php
}


add_action("admin_init", "ct_process_xeroitem_admin_actions");
function ct_process_xeroitem_admin_actions()
{
    global $wpdb;
    
    if(isset($_REQUEST['org-action']))
    {
        $action = $_REQUEST['org-action'];
        
        if($action == 'save-xeroitem')
        {
            //Save Xero Items
            $xeroitemClass = new CT_Xeroitem($_POST['id']);
            $xeroitemClass->bind($_POST);
            $resp = $xeroitemClass->save();
            if( $resp )
            {
                echo $resp;
                ?>
                <script type="text/javascript">
                    setTimeout(function(){
                        document.location.href = '<?php echo admin_url()?>admin.php?page=manage-xeroitems';
                    }, 1000);
                </script>
                <?php
                
                exit;
            }else{
                $_GET['org-message'] = $wpdb->last_error;
                return;
            }
        } else if( $action == 'reload-xeroitems' ){
            $xero = new CT_Xero();
            $xero->updateItems();
            echo 'Done';
            ?>
            <script type="text/javascript">
                setTimeout(function(){
                    document.location.href = '<?php echo admin_url()?>admin.php?page=manage-xeroitems';
                }, 1000);
            </script>
            <?php

            exit;
        } else if( $action == 'reload-xeroaccounts' ){
            $xero = new CT_Xero();
            $xero->updateAccounts();
            echo 'Completed';
            ?>
            <script type="text/javascript">
                setTimeout(function(){
                    document.location.href = '<?php echo admin_url()?>admin.php?page=xero-accounts';
                }, 1000);
            </script>
            <?php

            exit;
        }
    }
    
}