<?php

class CT_Xeroitem
{
    var $id = null;
    
    var $code = '';
    var $description = '';
    var $unit_price = 0;
    var $account_code = '';
    

    public function __construct($id = null)
    {
        global $wpdb;
        
        if($id)        
        {
            $query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}xeroitems WHERE id=%d", $id);
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
        $variables = get_class_vars('CT_Xeroitem');
        
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
        
        $variables = get_class_vars('CT_Xeroitem');
        foreach(array_keys($variables) as $_m)
        {
            
            if($_m == 'id')
                continue;
            $data[$_m] = $this->$_m;            
        }        
        $xero = new CT_Xero();
        if(!$this->id)
        {
            $itemData = $xero->addXeroItem( $data );
            if( $itemData ){
                $wpdb->insert($wpdb->prefix . "xeroitems", array_merge( $data, array( 'id' => $itemData['Items']['Item']['ItemID'] ) ) );
                return 'Xero Item Saved.';
            }
            return 'Cannot Add Item. Please verify all Fields';
        }
        else
        {
            //Update
            $itemData = $xero->addXeroItem( array_merge( $data, array( 'id' => $this->id ) ) );
            if( $itemData ){
                $wpdb->update($wpdb->prefix . "xeroitems", array_merge( $data, array( 'id' => $this->id ) ), array('id' => $this->id));
                return 'Xero Item Updated.';
            }
            return 'Cannot Update Item. Please verify all Fields';
        }
    }
    
}