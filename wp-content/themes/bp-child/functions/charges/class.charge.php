<?php
/**
* Charge Class
*/
class CT_Charge
{
    public $id = null;

    public $organisation_id = null;

    public $payment_id = null;

    public $item_code = null;

    public $quantity = null;

    public $start_date = null;

    public $end_date = null;

    public $reference_type = null;

    public $reference_id = null;

    public $invoice_identifier = null;

    public $is_paid = null;

    public $comment = null;

    public function __construct($id = null)
    {
        global $wpdb;

        if($id)
        {
            $query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}organisations_charge WHERE id=%d", $id);
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
        $variables = get_class_vars('CT_Charge');

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

        $variables = get_class_vars('CT_Charge');
        foreach(array_keys($variables) as $_m)
        {

            if($_m == 'id')
                continue;
            $data[$_m] = $this->$_m;
        }
        if(!$this->id)
        {
            return $wpdb->insert($wpdb->prefix . "organisations_charge", $data );
        }
        else
        {
            //Update
            return $wpdb->update($wpdb->prefix . "organisations_charge", array_merge( $data, array( 'id' => $this->id ) ), array('id' => $this->id));
        }
    }

    public function getOrganisationsList(){
        global $wpdb;
        return $wpdb->get_results("SELECT * FROM {$wpdb->prefix}organisations_charge AS c JOIN {$wpdb->prefix}organisations AS o ON o.id = c.organisation_id WHERE invoice_identifier = '' AND is_paid = 0 AND o.no_billing = 0 GROUP BY organisation_id");
    }
}