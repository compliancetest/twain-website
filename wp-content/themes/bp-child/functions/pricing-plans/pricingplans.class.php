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
            }
            if( $attr->visibility == 1 ) {
                if ($attr->type == 'itemcode') {
                    $this->attribute_all[$attr->title] = array('type' => $attr->type, 'desc' => $attr->description, 'value' => $this->getPriceByXeroCode($attr->value));
                } else {
                    $this->attribute_all[$attr->title] = array('type' => $attr->type, 'desc' => $attr->description, 'value' => $attr->value);
                }
            }
        }
    }
    
    public function getPricingPlanData( ){
        global $wpdb;
        return $wpdb->get_row( $wpdb->prepare("SELECT * FROM wp_pricing_plans WHERE id = %d ", $this->id ) );
    }

    public function getPricingPlanAttributes(){
        global $wpdb;
        return $wpdb->get_results( $wpdb->prepare("SELECT * FROM wp_pricing_plans_attributes WHERE pricing_plan_id = %d ", $this->id ) );
    }

    public static function getAllPlans( $plans_ids = false ){
        global $wpdb;
        if( $plans_ids && ! empty( $plans_ids ) ){
            return $wpdb->get_results( "SELECT * FROM wp_pricing_plans WHERE id IN( ".implode( ',', $plans_ids )." ) " );
        }
        return $wpdb->get_results("SELECT * FROM wp_pricing_plans" );
    }

    public function getPriceByXeroCode( $code ){
        global $wpdb;
        $price = $wpdb->get_var( $wpdb->prepare( "SELECT unit_price FROM wp_xeroitems WHERE code = %s ", $code ) );
        if( ! $price ){
            return 0;
        }
        return $price;
    }
    
}