<?php
/**
* Pricing Plans
*/
require_once (THE_FUNCTION . "/pricing-plans/pricing-plans-list-table.php");

//Create Menus
add_action("admin_menu", "ct_add_manage_pricing_plans_menu");
function ct_add_manage_pricing_plans_menu()
{
    add_menu_page("Manage Pricing Plans", "Pricing Plans", "manage_options", "manage-pricing-plans", "ct_show_pricing_plans_list");
    add_submenu_page("manage-pricing-plans", "View Pricing Plan", "", "manage_options", "view-pricing-plan", "ct_view_pricing_plan");
}

function ct_show_pricing_plans_list()
{
    $listTable = new Pricing_Plans_List_Table();
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
    <?php
}


function ct_view_pricing_plan()
{
    $pricing_plan = new PricingPlan( intval( $_REQUEST['org-action'] ) );
    ?>
        <table class="widefat" style="width: auto; margin: 20px;">
            <tr>
                <td>ID</td>
                <td><?php echo $pricing_plan->id_str;?></td>
            </tr>
            <tr>
                <td>Title</td>
                <td><?php echo $pricing_plan->title;?></td>
            </tr>
            <tr>
                <td>Description</td>
                <td><?php echo $pricing_plan->description;?></td>
            </tr>
            <tr>
                <td>Type</td>
                <td><?php echo $pricing_plan->type;?></td>
            </tr>
            <tr>
                <td>Billing Type</td>
                <td><?php echo $pricing_plan->attribute_billing->value;?></td>
            </tr>
            <tr>
                <td>Item Codes</td>
                <td>
                    <table style="border: 1px solid #000000;">
                        <tr>
                            <td>Name</td>
                            <td>Code</td>
                            <td>Title</td>
                            <td>Description</td>
                        </tr>
                        <?php foreach( $pricing_plan->attribute_itemcodes AS $k => $code ):?>
                            <tr>
                                <td><?php echo $k;?></td>
                                <td><?php echo $code->value;?></td>
                                <td><?php echo $code->title;?></td>
                                <td><?php echo $code->description;?></td>
                            </tr>
                        <?php endforeach;?>
                    </table>
                </td>
            </tr>
            <tr>
                <td>Allowed Roles / Levels</td>
                <td>
                    <table style="border: 1px solid #000000;">
                        <tr>
                            <td>Role</td>
                            <td>Levels</td>
                        </tr>
                        <?php foreach( $pricing_plan->attribute_roles AS $k => $role ):?>
                            <tr>
                                <td><?php echo $k;?></td>
                                <td><?php echo implode( ',', $role );?></td>
                            </tr>
                        <?php endforeach;?>
                    </table>
                </td>
            </tr>
        </table>
    <?php

}