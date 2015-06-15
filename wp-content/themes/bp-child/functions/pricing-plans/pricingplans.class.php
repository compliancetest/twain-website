<?php
/***
* Pricing Plans Class
*/


class PricingPlan
{
    public $id = null;
    //Pricing Plan Information
    public $id_str = '';

    public $title = '';
    public $description = '';
    public $type = '';

    // Pricing Plan attributes
    public $attribute_itemcodes = array();
    public $attribute_roles     = array();
    public $attribute_levels    = array();
    public $attribute_boolean   = array();
    public $attribute_percent   = array();
    public $attribute_all       = array();
    public $attribute_billing   = '';

    public $attribute = array();

    public function __construct($id = null)
    {
        if($id !== null)
            $this->id = $id;

        if(!$this->id)
            return;

        //Load Information
        $pricing_plan = $this->getPricingPlanData();
        $this->id_str = $pricing_plan->id_str;
        $this->title  = $pricing_plan->title;
        $this->description = $pricing_plan->description;
        $this->type = $pricing_plan->type;

        $pricing_plan_attributes = $this->getPricingPlanAttributes();
        foreach( $pricing_plan_attributes AS $attr ){
            switch ($attr->type) {
                case 'itemcode':
                    if( $attr->visibility == 1 ) {
                        $this->attribute_itemcodes[$attr->name] = $attr;
                    }
                    break;
                case 'string':
                    if( $attr->visibility == 1 ) {
                        $this->attribute_billing = $attr;
                    }
                    break;
                case 'role';
                    $this->attribute_roles[$attr->name] = explode(',', str_replace(' ', '', $attr->value));
                    break;
                case 'number':
                    if( $attr->visibility == 1 ) {
                        $this->attribute[$attr->name] = $attr;
                    }
                    break;
                case 'boolean':
                    if( $attr->visibility == 1 ) {
                        $this->attribute_boolean[$attr->name] = $attr->value;
                    }
                    break;
                case 'percent':
                    if( $attr->visibility == 1 ) {
                        $this->attribute_percent[$attr->name] = $attr->value;
                    }
                    break;
                case 'discount':
                    if( $attr->visibility == 1 ) {
                        $this->attribute_percent[$attr->name] = $attr->value;
                    }
                    break;
            }
            if( $attr->visibility == 1 ) {
                if ($attr->type == 'itemcode') {
                    $this->attribute_all[$attr->title] = array( 'id' => $attr->id, 'type' => $attr->type, 'desc' => $attr->description, 'value' => $this->getPriceByXeroCode($attr->value));
                } else {
                    if( $attr->type == 'discount' ){
                        $this->attribute_all[$attr->name] = array('id' => $attr->id, 'type' => $attr->type, 'desc' => $attr->description, 'value' => $attr->value, 'title' => $attr->title );
                    } else {
                        $this->attribute_all[$attr->title] = array('id' => $attr->id, 'type' => $attr->type, 'desc' => $attr->description, 'value' => $attr->value, 'visibility' => $attr->visibility);
                    }
                }
            }
            if( $attr->type == 'boolean' && $attr->visibility == 0 ){
                $this->attribute_all[$attr->name] = array( 'id' => $attr->id, 'type' => $attr->type, 'desc' => $attr->description, 'value' => $attr->value, 'visibility' => $attr->visibility);
            }
        }
    }
    
    public function getPricingPlanData( ){
        global $wpdb;
        return $wpdb->get_row( $wpdb->prepare("SELECT * FROM wp_pricing_plans WHERE id = %d ", $this->id ) );
    }

    public function getPricingPlanAttributes(){
        global $wpdb;
        return $wpdb->get_results( $wpdb->prepare("SELECT * FROM wp_pricing_plans_attributes WHERE pricing_plan_id = %d ORDER BY `order` ASC", $this->id ) );
    }

    public static function getAllPlans( $plans_ids = false ){
        global $wpdb;
        if( $plans_ids && ! empty( $plans_ids ) ){
            $results = $wpdb->get_results( "SELECT * FROM wp_pricing_plans WHERE id IN( ".implode( ',', $plans_ids )." ) " );
            //display plans in defined by admin order
            $return = $multisort = array();
            foreach( $results AS $result ){
                $return[$result->id] = $result;
            }
            $response = array();
            foreach( $plans_ids AS $plan_id ){
                $response[] = $return[$plan_id];
            }
            return $response;

        } else {
            return $wpdb->get_results("SELECT * FROM wp_pricing_plans");
        }

    }

    public function getPriceByXeroCode( $code ){
        global $wpdb;
        $price = $wpdb->get_var( $wpdb->prepare( "SELECT unit_price FROM wp_xeroitems WHERE code = %s ", $code ) );
        if( ! $price ){
            return 0;
        }
        return $price;
    }

    public static function getPlanRolesAndLevels( $plans, $suite_plans = false ){
        global $wpdb;
        $levels = '';
        $roles_array = array();
        if( $suite_plans ){
            if( ! $suite_plans ){
                return false;
            }
            $results = $wpdb->get_row( "SELECT *, count(*) AS count FROM wp_pricing_plans_attributes WHERE pricing_plan_id IN (".implode( ',', $suite_plans ).") AND type='role' GROUP BY pricing_plan_id ORDER BY count DESC LIMIT 1" );
        } else {
            if( ! $plans ){
                return false;
            }
            $results = $wpdb->get_row( "SELECT *, count(*) AS count FROM wp_pricing_plans_attributes WHERE pricing_plan_id IN (".implode( ',', $plans ).") AND type='role' GROUP BY pricing_plan_id ORDER BY count DESC LIMIT 1" );
        }
        $results = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM wp_pricing_plans_attributes WHERE pricing_plan_id = %d AND type='role' ORDER BY `order` ", $results->pricing_plan_id ) );
        foreach( $results AS $result ){
            array_push( $roles_array, $result->name );
            if( strlen( $result->value ) > strlen( $levels ) ){
                $levels = $result->value;
            }
        }
        return array(
            'levels' => explode( ',', str_replace( ' ', '', $levels ) ),
            'roles'  => $roles_array
        );
    }

    public static function getPlanFinalDiscount( $pricingPlanId, $voucherName = false ){
        global $wpdb;
        $totalDiscount = 0;
        //check discount
        if( $row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM wp_pricing_plans_attributes WHERE pricing_plan_id = %d AND name = 'Discount'  AND visibility = 1", $pricingPlanId ) ) ){
            $totalDiscount = $row->value;
        }
        if( $voucherName ) {
            //check voucher discount
            if ($voucher = $wpdb->get_row($wpdb->prepare("SELECT * FROM wp_pricing_plans_attributes WHERE pricing_plan_id = %d AND type = 'discount' AND name = %s", $pricingPlanId, $voucherName ))) {
                $totalDiscount += $voucher->value;
            }
        }
        return $totalDiscount;
    }
    public static function getVoucherDiscount( $pricingPlanId, $voucherName = false ){
        global $wpdb;
        $totalDiscount = 0;
        if( $voucherName ) {
            //check voucher discount
            if ($voucher = $wpdb->get_row($wpdb->prepare("SELECT * FROM wp_pricing_plans_attributes WHERE pricing_plan_id = %d AND type = 'discount' AND name = %s AND visibility = 1", $pricingPlanId, $voucherName ))) {
                $totalDiscount = $voucher->value;
            }
        }
        return $totalDiscount;
    }

    public static function getVoucherByName( $pricingPlanId, $voucherName ){
        global $wpdb;
        return $wpdb->get_row($wpdb->prepare("SELECT * FROM wp_pricing_plans_attributes WHERE pricing_plan_id = %d AND type = 'discount' AND name = %s ", $pricingPlanId, $voucherName ) );
    }

    public static function isSupportVouchers( $pricingPlanId ){
        global $wpdb;
        return ( boolean ) $wpdb->get_row($wpdb->prepare("SELECT * FROM wp_pricing_plans_attributes WHERE pricing_plan_id = %d AND type = 'boolean' AND name = 'Vouchers' ", $pricingPlanId ) );
    }

    public static function isSupportBulk( $pricingPlanId = false ){
        global $wpdb;
        if( ! $pricingPlanId ){
            $pricingPlanId = $wpdb->get_var( $wpdb->prepare("SELECT pricing_plan_id FROM wp_organisations_subscriptions WHERE user_id = %d ", get_current_user_id() ) );
            if( ! $pricingPlanId ){
                return false;
            }
        }
        return ( boolean ) $wpdb->get_row($wpdb->prepare("SELECT * FROM wp_pricing_plans_attributes WHERE pricing_plan_id = %d AND type = 'boolean' AND name = 'Bulk' AND value = 1 ", $pricingPlanId ) );
    }
}