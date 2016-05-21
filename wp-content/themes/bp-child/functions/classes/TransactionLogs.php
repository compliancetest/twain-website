<?php

class TransactionLogs
{

    private $where = [];

    public function setWhereQuery($subscriptions, $filters)
    {
        if(!empty($subscriptions)){
            $this->where[] = ' subscription_id IN ('.implode(',', $subscriptions).') ';
        } else {
            $this->where[] = ' subscription_id IN (0) ';
        }
        if($filters['product_id']){
            $this->where[] = sprintf(' product_id = %d ', $filters['product_id']);
        }
        if($filters['test_case_id']){
            $this->where[] = sprintf(' test_case_id = %d ', $filters['test_case_id']);
        }
         if($filters['test_suite_id']){
            $this->where[] = sprintf(' test_suite_id = %d ', $filters['test_suite_id']);
        }
         if($filters['subscription_id']){
            $this->where[] = sprintf(' subscription_id = %d ', $filters['subscription_id']);
        }
         if($filters['date']){
            $this->where[] = " updated_at LIKE '".$filters['date']."%' ";
        }
        if($filters['data_argument_type']){
            $this->where[] = sprintf(" data_argument_type = '%s' ", $filters['data_argument_type']);
        }
        if($filters['data_group']){
            $this->where[] = sprintf(" data_group = '%s' ", $filters['data_group']);
        }
        if($filters['messages']){
            $this->where[] = sprintf(" messages = '%s' ", $filters['messages']);
        }
        return $this;
    }

    public function getUserTransactionLog($page = 1, $limit = 10, $orderby = 'updated_at', $order = 'desc')
    {
        global $wpdb;

        if ($limit == -1) {
            $limit = "";
        } else {
            $limit = ($page - 1) * $limit . ', ' . $limit;
            $limit = "  LIMIT $limit ";
        }
        return [
            'results' => $wpdb->get_results("SELECT t.* FROM transactions AS t
                                     LEFT JOIN transactions_logs AS tl ON tl.transaction_id = t.id
                                     WHERE " . implode(' AND ', $this->where) . " GROUP BY t.id ORDER BY $orderby $order $limit"),
            'total' => $wpdb->get_var("SELECT count(cc.id) FROM (SELECT t.id FROM transactions AS t
                                     LEFT JOIN transactions_logs AS tl ON tl.transaction_id = t.id
                                     WHERE " . implode(' AND ', $this->where) . " GROUP BY t.id ) as cc "),
        ];
    }

    public function getFilters($subscriptionsIds)
    {
        global $wpdb;

        if(!empty($subscriptionsIds)) {
            $where = ' subscription_id IN (' . implode(',', $subscriptionsIds) . ') ';
        } else {
           $where = ' subscription_id IN (0) ';
        }
        return [
            'product' => $wpdb->get_results("SELECT product_id FROM transactions WHERE $where GROUP BY product_id"),
            'test_case_id' => $wpdb->get_results("SELECT test_case_id FROM transactions WHERE $where GROUP BY test_case_id"),
            'test_suite_id' => $wpdb->get_results("SELECT test_suite_id FROM transactions WHERE $where GROUP BY test_suite_id"),
            'data_group' => $wpdb->get_results("SELECT data_group FROM transactions_logs WHERE transaction_id IN( SELECT id FROM transactions WHERE $where) GROUP BY data_group"),
            'data_type' => $wpdb->get_results("SELECT data_argument_type FROM transactions_logs WHERE transaction_id IN( SELECT id FROM transactions WHERE $where) GROUP BY data_argument_type"),
            'data_message' => $wpdb->get_results("SELECT messages FROM transactions_logs WHERE transaction_id IN( SELECT id FROM transactions WHERE $where) GROUP BY messages"),
        ];
    }

}