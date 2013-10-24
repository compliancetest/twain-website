<?php
/**
* Manage Support Ticket Status
*/

class CT_TicketStatus
{
    var $last_id = null;
    
    public function getStatuses($orderby = 'sort_number', $order='asc')
    {
        global $wpdb;
        
        $query = "SELECT * FROM " . TABLE_TICKET_STATUSES . " ORDER BY $orderby $order";
        $rows = $wpdb->get_results($query);
        
        return $rows;
    }
    
    public function addStatus($data)
    {
        global $wpdb;
        
        $wpdb->insert(TABLE_TICKET_STATUSES, $data);                
        $this->last_id = $wpdb->insert_id;
        
    }
    
    public function updateStatus($id, $data)
    {
        global $wpdb;
        
        $wpdb->update(TABLE_TICKET_STATUSES, $data, array('id' => $id));
        
        $this->last_id = $id;    
            
    }
    
    public function getStatusById($id)
    {
        global $wpdb;
        
        $query = $wpdb->prepare("SELECT * FROM " . TABLE_TICKET_STATUSES . " WHERE id=%d", $id);
        $data = $wpdb->get_row($query);
        
        return $data;
    }
    
    public function sortStatues()
    {
        $statuses = $this->getStatuses('sort_number');
        $orders = array();
        
        if($this->last_id)
        {
            $last_data = $this->getStatusById($this->last_id);
        }
        
        foreach($statuses as $c)
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
            $wpdb->query("UPDATE " . TABLE_TICKET_STATUSES . " SET sort_number=" . ($i+1) . " WHERE id=" . $r);            
        }
    }
    
    public function defineStatusConstants()
    {
        $statuses = $this->getStatuses();
        $variables = array();
        
        foreach($statuses as $row)
        {
            if(!defined("TICKET_STATUS_" . strtoupper(str_replace(" ", "_", $row->status))))
                define("TICKET_STATUS_" . strtoupper(str_replace(" ", "_", $row->status)), $row->id);
        }        
        
    }
    
    public function getStatusesSelectboxHTML($name = 'ticket_status', $id='ticket_status', $default = null, $emptyOptionLabel = '- All -')
    {
        $priorities = $this->getStatuses('sort_number');
        $html = "<select name='$name' id='$id' class='select'>";
        if($emptyOptionLabel)
            $html .= "<option value=''>$emptyOptionLabel</option>";
        foreach($priorities as $p)
        {
            $html .= "<option value='$p->id' " . ($p->id == $default ? "selected='selected'" : "") . ">$p->status</option>";
        }
        $html .= "</select>";
        
        return $html;
    }
}