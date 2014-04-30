<?php
/**
* The Gate Way function for users action
*/

define('IS_GATEWAY_UPDATED', (isset($_POST['gateway_id']) && $_POST['gateway_id'] > 0));
define('IS_GATEWAY_EDIT', (isset($_REQUEST['gateway_id']) && $_REQUEST['gateway_id'] > 0));

add_action("admin_menu", "ct_gateways_menu");

function ct_gateways_menu()
{
    add_menu_page("Manage Gateways", "Gateways", "manage_options", "gateways", "ct_manage_gateways");
    add_submenu_page("gateways", "Manage New Gateway", "Add new", "manage_options", "gateway_edit", "ct_manage_gateway_edit");
}

function ct_manage_gateways()
{
    global $wpdb;
    
    require_once('gateway-list-table.php');
    
    $listTable = new CT_Gateway_List_Table();
    $listTable->prepare_items();
    ?>
    <div class="wrap">
        <h2>
            Gateways
            <a href="<?php echo admin_url() . 'admin.php?page=gateway_edit'; ?>" class="add-new-h2">Add New Gateway</a>
        </h2>
        <?php $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : null; ?>
        <?php if ($action == 'delete') { ?>
        <div id="message" class="updated below-h2"><p>Successfully Deleted Gateways!</p></div>
        <?php } ?>
        <form name="adminform" action="admin.php?page=gateways" method="post">
        <?php
            $listTable->search_box("Search", "search");
            echo $listTable->display();
        ?>
        </form>
    </div>
<?php
}

function ct_manage_gateway_edit()
{
    global $wpdb;
    
    $gateway_id = isset($_REQUEST['gateway_id']) ? $_REQUEST['gateway_id'] : '';
    $gateway_data = array('name' => '', 'abn' => '', 'url' => '');
    if ($gateway_id > 0) {
        $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "gateways WHERE gateway_id=%d", $gateway_id);
        $gateway_data = $wpdb->get_row($query);
    }
?>
    <style type="text/css">
        .mf_field_wrapper { margin: 0 6px 0; overflow: hidden; }
        .mf_field_wrapper label { display: block; clear: left; font-weight: bold; padding: .5em 0; }
        .mf_text { width: 95%; margin-left: 1px; }
    </style>
    <?php if (IS_GATEWAY_UPDATED || (isset($_REQUEST['message']) && $_REQUEST['message'] == '1')): ?>
    <div id="message" class="updated fade">    
        <p>
            <?php if (IS_GATEWAY_UPDATED): ?>
                Updated Gateway!
            <?php else: ?>
                Added New Gateway!
            <?php endif; ?>
        </p>
    </div>
    <?php endif; ?>
    <div class="wrap" id="poststuff">
        <?php if (IS_GATEWAY_EDIT): ?>
            <h2>Edit Gateway</h2>
        <?php else: ?>
            <h2>Add New Gateway</h2>
        <?php endif; ?>
        <form name="adminform" action="<?php echo admin_url() . 'admin.php?page=gateway_edit'; ?>" method="post" onsubmit="return validateGatewayForm()">
            <div id="infos-gateway" class="postbox">
                <h3 class="hndle"><span>Gateway Information</span></h3>
                <div class="inside">
                    <div class="mf_field_wrapper mf_field_test_case_id text">
                        <label for="test_case_id">Gateway Name</label> 
                        <input class="mf_text" type="text" id="gateway_name" name="gateway_name" value="<?php echo $gateway_data->name; ?>"> 
                        <p class="mf_caption"></p>
                    </div>
                    <div class="mf_field_wrapper mf_field_test_case_id text">
                        <label for="test_case_id">Gateway ABN</label> 
                        <input class="mf_text" type="text" id="gateway_abn" name="gateway_abn" value="<?php echo $gateway_data->abn; ?>"> 
                        <p class="mf_caption"></p>
                    </div>
                    <div class="mf_field_wrapper mf_field_test_case_id text">
                        <label for="test_case_id">Gateway URL</label> 
                        <input class="mf_text" type="text" id="gateway_url" name="gateway_url" value="<?php echo $gateway_data->url; ?>"> 
                        <p class="mf_caption"></p>
                    </div>
                </div>
            </div>
            <input type="submit" value="<?php echo (IS_GATEWAY_EDIT) ? ('Update Gateway') : ('Publish Gateway') ?>" class="button-primary" />
            <input type="hidden" name="action" value="save" />
            <input type="hidden" name="gateway_id" value="<?php echo $gateway_id; ?>" />
        </form>
    </div>
<?php
}

add_action('admin_init', 'gateway_actions');
function gateway_actions()
{
    global $wpdb;
    
    $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : null;
    
    if ($action == 'save') 
    {
        
        $gateway_id = isset($_REQUEST['gateway_id']) ? $_REQUEST['gateway_id'] : null;
        $gateway_name = isset($_REQUEST['gateway_name']) ? $_REQUEST['gateway_name'] : null;
        $gateway_abn = isset($_REQUEST['gateway_abn']) ? $_REQUEST['gateway_abn'] : null;
        $gateway_url = isset($_REQUEST['gateway_url']) ? $_REQUEST['gateway_url'] : null;
        $gateway_data = array('name' => $gateway_name, 'abn' => $gateway_abn, 'url' => $gateway_url);
    
        if ($gateway_id > 0) {
            $wpdb->update($wpdb->prefix . 'gateways', $gateway_data, array('gateway_id' => $gateway_id));
        } else {
            $wpdb->insert($wpdb->prefix . 'gateways', $gateway_data);
            wp_redirect(admin_url() . 'admin.php?page=gateway_edit&gateway_id=' . $wpdb->insert_id . '&message=1');
        }
    } 
    else if ($action == 'delete')
    {
        if ( empty($_REQUEST['gateway_ids']) )
            $gateway_ids = array( intval( $_REQUEST['gateway_id'] ) );
        else
            $gateway_ids = array_map( 'intval', (array) $_REQUEST['gateway_ids'] );
        
        foreach ($gateway_ids as $gateway_id)
        {
            $wpdb->query("DELETE FROM " . $wpdb->prefix . "gateways WHERE gateway_id IN (" . implode(', ', $gateway_ids) . ')');    
        }
        
        
        $msg = 'Successfully Verified!';
    }
}
?>

<script type="text/javascript">
function validateGatewayForm()
{
    return true;
}
</script>