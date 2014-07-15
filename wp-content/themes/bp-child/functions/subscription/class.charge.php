<?php
/**
* Charge Class
*/
class CT_Charge
{
    var $id = null;
    
    var $organisation_id = null;
    
    var $payment_id = null;
    
    var $item_code = null;
    
    var $start_date = null;
    
    var $end_date = null;
    
    var $reference_type = null;
    
    var $reference_id = null;
    
    var $invoice_identifier = null;
    
    var $is_paid = null;
    
    var $comment = null;

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
}