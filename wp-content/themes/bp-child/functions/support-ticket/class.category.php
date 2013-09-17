<?php
/**
* Manage Ticket Category Class
*/
class CT_TicketCategory
{
    var $last_id = null;
    
    public function __construct()
    {
            
    }
    
    public function getCategories($orderby = 'category_name', $order = 'asc')
    {
        global $wpdb;
        
        $query = "SELECT * FROM " . TABLE_TICKET_CATEGORIES . " ORDER BY $orderby $order";
        $rows = $wpdb->get_results($query);
        
        return $rows;
    }
    
    public function getCategoriesWithTicketCounts()
    {
        $query = "SELECT t.*, COUNT(t.id) AS ticket_counts FROM " . TABLE_TICKET_CATEGORIES . " AS tc " .
                 "LEFT JOIN " . TABLE_TICKETS . " AS t ON t.category_id=tc.id GROUP BY t.id ORDER BY category_name";
        $rows = $wpdb->get_results($query);
    }
    
    public function addCategory($data)
    {
        global $wpdb;
        
        $wpdb->insert(TABLE_TICKET_CATEGORIES, $data);
        
        $this->last_id = $wpdb->insert_id;
        
    }
    
    public function updateCategory($id, $data)
    {
        global $wpdb;
        
        $wpdb->update(TABLE_TICKET_CATEGORIES, $data, array('id' => $id));
        
        $this->last_id = $id;    
            
    }
    
    
    /**
    * Get Category Data By Id
    * 
    * @param Int $id
    */
    public function getCategoryById($id)
    {
        global $wpdb;
        
        $query = $wpdb->prepare("SELECT * FROM " . TABLE_TICKET_CATEGORIES . " WHERE id=%d", $id);
        $data = $wpdb->get_row($query);
        
        $this->last_id = $id;        
            
        return $data;
    }
    
    public function sortCategories()
    {
        $categories = $this->getCategories('sort_number');
        $orders = array();
        
        if($this->last_id)
        {
            $last_data = $this->getCategoryById($this->last_id);
        }
        
        foreach($categories as $c)
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
            $wpdb->query("UPDATE " . TABLE_TICKET_CATEGORIES . " SET sort_number=" . ($i+1) . " WHERE id=" . $r);
        }
    }
}