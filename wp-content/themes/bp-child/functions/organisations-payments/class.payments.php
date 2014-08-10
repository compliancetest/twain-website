<?php
/**
* Xero Payments Class
*/
class CT_Payments
{
    public $id = null;

    public $invoice_id = null;

    public $account_code = null;

    public $date_added = null;

    public $amount = null;

    public $reference = null;

    public $organisation_id = null;

    public $payment_method_id = null;

    public $is_paid = null;

    public $date_paid = null;

    public function __construct($id = null)
    {
        global $wpdb;

        if($id)
        {
            $query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}organisations_payments WHERE id=%d", $id);
            $row = $wpdb->get_row($query, ARRAY_A);
            if($row)
            {
                foreach(array_keys($row) as $_m)
                {
                    $this->$_m = $row[$_m];
                }
            }
        }
    }

    public function bind($data)
    {
        $variables = get_class_vars('CT_Payments');

        foreach(array_keys($variables) as $_m)
        {
            if(isset($data[$_m]))
                $this->$_m = $data[$_m];
        }
    }

    public function save()
    {
        global $wpdb;

        $data = array();

        $variables = get_class_vars('CT_Payments');
        foreach(array_keys($variables) as $_m)
        {

            if($_m == 'id')
                continue;
            $data[$_m] = $this->$_m;
        }
        if(!$this->id)
        {
            if( ! $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}organisations_payments WHERE invoice_id = %s", $data['invoice_id'] ) ) ){
                return $wpdb->insert($wpdb->prefix . "organisations_payments", $data );
            }
            return false;
        }
    }
}