<?php

class TransactionLogs
{

    private $where = [];
    private $join = '';

    public function setWhereQuery($subscriptions, $filters)
    {
        if (!empty($subscriptions)) {
            $this->where[] = ' subscription_id IN (' . implode(',', $subscriptions) . ') ';
        } else {
            $this->where[] = ' subscription_id IN (0) ';
        }
        if ($filters['product_id']) {
            $this->where[] = sprintf(' product_id = %d ', $filters['product_id']);
        }
        if ($filters['test_case_id']) {
            $this->where[] = sprintf(' test_case_id = %d ', $filters['test_case_id']);
        }
        if ($filters['test_suite_id']) {
            $this->where[] = sprintf(' test_suite_id = %d ', $filters['test_suite_id']);
        }
        if ($filters['subscription_id']) {
            $this->where[] = sprintf(' subscription_id = %d ', $filters['subscription_id']);
        }
        if ($filters['date']) {
            $this->where[] = " ( t.updated_at > '" . date('Y-m-d H:i:s', getUTCTimeStamp($filters['date'])) . "' AND t.updated_at <  '" . date('Y-m-d H:i:s', getUTCTimeStamp($filters['date'] . ' 23:59:59')) . "' ) ";
        }
        if ($filters['outcome']) {
            $this->where[] = sprintf(" test_outcome_status_id = '%s' ", $filters['outcome']);
        }
        if ($filters['audit']) {
            $this->where[] = sprintf(" audit_record = '%s' ", $filters['audit']);
        }
        if ($filters['scenario']) {
            $this->join = " 
                JOIN wp_posts AS p1 ON t.test_case_id = p1.ID
                JOIN wp_postmeta AS pm1 ON pm1.post_id = p1.ID AND pm1.meta_key LIKE 'scenario_%' AND pm1.meta_value = '" . filter_var($filters['scenario'], FILTER_SANITIZE_STRING) . "'
            ";
        }
        return $this;
    }

    public function getUserTransactionLog($page = 1, $limit = 10, $orderby = 'created_at', $order = 'desc')
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
                                     $this->join
                                     LEFT JOIN transactions_logs AS tl ON tl.transaction_id = t.id
                                     WHERE " . implode(' AND ', $this->where) . " GROUP BY t.id ORDER BY $orderby $order $limit"),
            'total' => $wpdb->get_var("SELECT count(cc.id) FROM (SELECT t.id FROM transactions AS t
                                     $this->join
                                     LEFT JOIN transactions_logs AS tl ON tl.transaction_id = t.id
                                     WHERE " . implode(' AND ', $this->where) . " GROUP BY t.id ) as cc "),
        ];
    }

    public function getFilters($subscriptionsIds)
    {
        global $wpdb;

        if (!empty($subscriptionsIds)) {
            $where = ' subscription_id IN (' . implode(',', $subscriptionsIds) . ') ';
        } else {
            $where = ' subscription_id IN (0) ';
        }
        return [
            'product' => $wpdb->get_results("SELECT product_id FROM transactions AS t WHERE $where GROUP BY product_id"),
            'test_case_id' => $wpdb->get_results("SELECT test_case_id, p.post_title FROM transactions AS t JOIN wp_posts AS p ON t.test_case_id = p.ID WHERE $where GROUP BY test_case_id ORDER BY post_title"),
            'test_suite_id' => $wpdb->get_results("SELECT test_suite_id FROM transactions AS t WHERE $where GROUP BY test_suite_id"),
            'audit' => $wpdb->get_results("SELECT audit_record FROM transactions AS t WHERE $where GROUP BY audit_record ORDER BY audit_record DESC"),
            'test_outcome' => $wpdb->get_results("SELECT test_outcome_status_id, name FROM transactions AS t JOIN test_outcome_statuses AS os ON os.id = t.test_outcome_status_id WHERE $where GROUP BY t.test_outcome_status_id ORDER BY name ASC"),
            'scenario' => $wpdb->get_results("
                SELECT s.id, s.code FROM transactions AS t 
                JOIN wp_posts AS p ON p.ID = t.test_case_id
                JOIN wp_postmeta AS pm ON pm.meta_key LIKE 'scenario_%' AND pm.post_id = p.ID
                JOIN wp_test_suites_scenarios AS s ON s.id = pm.meta_value
                WHERE $where GROUP BY code ORDER BY s.sequence"),
        ];
    }

}