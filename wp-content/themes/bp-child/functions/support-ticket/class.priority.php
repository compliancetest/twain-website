<?php
/**
* Manage Support Ticket Priority
*/

class CT_TicketPriority
{
    var $last_id = null;
    
    public function getPriorities($orderby = 'sort_number', $order='asc')
    {
        global $wpdb;
        
        $query = "SELECT * FROM " . TABLE_TICKET_PRIORITIES . " ORDER BY $orderby $order";
        $rows = $wpdb->get_results($query);
        
        return $rows;
    }
    
    public function addPriority($data)
    {
        global $wpdb;
        
        $wpdb->insert(TABLE_TICKET_PRIORITIES, $data);        
        $this->last_id = $wpdb->insert_id;
        
    }
    
    public function updatePriority($id, $data)
    {
        global $wpdb;
        
        $wpdb->update(TABLE_TICKET_PRIORITIES, $data, array('id' => $id));
        
        $this->last_id = $id;    
            
    }
    
    public function getPriorityById($id)
    {
        global $wpdb;
        
        $query = $wpdb->prepare("SELECT * FROM " . TABLE_TICKET_PRIORITIES . " WHERE id=%d", $id);
        $data = $wpdb->get_row($query);
        
        return $data;
    }
    
    public function sortPriorities()
    {
        $priorities = $this->getPriorities('sort_number');
        $orders = array();
        
        if($this->last_id)
        {
            $last_data = $this->getPriorityById($this->last_id);
        }
        
        foreach($priorities as $c)
        {
            global $wpdb;
            
            if($this->last_id && $last_data->sort_number == $c->sort_number && !in_array($this->last_id, $orders))
            {
                $orders[] = $this->last_id;                
            }
            
            if(!in_array($c->id, $orders))
                $orders[] = $c->id;
        }
        
        foreach($orders as $i=>$r)
        {
            $wpdb->query("UPDATE " . TABLE_TICKET_PRIORITIES . " SET sort_number=" . ($i+1) . " WHERE id=" . $r);            
        }
    }
    
    public function getPrioritiesSelectboxHTML($name = 'ticket_priority', $id='ticket_priority', $default = null, $emptyOptionLabel = '- All -')
    {
        global $wpdb;
        $priorities = $this->getPriorities('sort_number');
        $html = "<select name='$name' id='$id' class='select'>";
        if($emptyOptionLabel)
            $html .= "<option value=''>$emptyOptionLabel</option>";
        foreach($priorities as $k => $p)
        {
            $price = $wpdb->get_var($wpdb->prepare("SELECT unit_price FROM {$wpdb->prefix}xeroitems WHERE code = %s", $p->item_code));
            $html .= "<option value='$p->id' " . ($p->id == $default ? "selected='selected'" : "") . " ttresolve='" . intval($p->ttresolve) . "' ttresponse='" . intval($p->ttresponse) . "' price='" . $price . "'>$p->priority</option>";
        }
        $html .= "</select>";
        
        return $html;
    }
}