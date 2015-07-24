<?php

class MC4WP_Subscribers_Log {

	private $table_name;
	private $db_version = '1.0.1';

	public function __construct() {
		add_action( 'mc4wp_subscribe_form', array( $this, 'log_form_subscriber' ), 10, 5 );
		add_action( 'mc4wp_subscribe_checkbox', array( $this, 'log_checkbox_subscriber' ), 10, 6 );

		if(!version_compare(get_option('mc4wp_log_db_version', 0), $this->db_version, '>=')) {
			$this->setup();
		}
	}

	public function log_form_subscriber( $email, array $list_ids, $form_id, array $merge_vars = array(), $url = '' ) {
		return $this->log( $email, $list_ids, 'form', 'sign-up-form', $merge_vars, $form_id, null, $url );
	}

	public function log_checkbox_subscriber( $email, array $list_ids, $signup_type, $merge_vars = array(), $comment_id = null, $url = '') {
		return $this->log( $email, $list_ids, 'checkbox', $signup_type, $merge_vars, null, $comment_id, $url );
	}

	public function log( $email, $list_ids, $signup_method, $signup_type, array $merge_vars = array(), $form_id = null, $comment_id = null, $url = '' ) {
		global $wpdb;

		$table_name = $wpdb->prefix . 'mc4wp_log';
		$list_ids = implode(',', $list_ids);

		return $wpdb->insert( $table_name, array(
				'email' => $email,
				'list_ids' => $list_ids,
				'signup_method' => $signup_method,
				'signup_type' => $signup_type,
				'form_ID' => $form_id,
				'comment_ID' => $comment_id,
				'merge_vars' => json_encode( $merge_vars ),
				'datetime' => current_time( 'mysql' ),
				'url' => $url
			)
		);

	}

	public function setup() {
		global $wpdb, $charset_collate;
		$table_name = $wpdb->prefix . 'mc4wp_log';

		$wpdb->hide_errors();
		// change column name, 
		// @v1.01
		$wpdb->query("ALTER TABLE {$table_name} CHANGE list_ID list_ids VARCHAR(100)");

		// create database table
		$sql = "CREATE TABLE {$table_name} (
			ID bigint(20) NOT NULL AUTO_INCREMENT,
			email VARCHAR(100) NOT NULL,
			list_ids VARCHAR(100) NOT NULL,
			signup_method VARCHAR(50) NOT NULL,
			signup_type VARCHAR(55) NOT NULL,
			form_ID bigint(20) NULL,
			comment_ID bigint(20) NULL,
			merge_vars longtext DEFAULT '',
			url VARCHAR(255) DEFAULT '',
			datetime datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			PRIMARY KEY  (ID)
			) $charset_collate; ";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );

		// change 'sign-up_form' in 'sign-up-form';
		$wpdb->query("UPDATE {$table_name} SET signup_type = 'sign-up-form' WHERE signup_type = 'sign-up_form'");

		$wpdb->show_errors();
		update_option('mc4wp_log_db_version', $this->db_version);
	}

	/**
	 * Query the database mc4wp_log table
	 */
	public function get_logs( array $args = array() ) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'mc4wp_log';

		$args = wp_parse_args( $args, array(
				'select' => '*',
				'email' => '',
				'signup_method' => '',
				'limit' => 1,
				'offset' => 0,
				'orderby' => 'id',
				'order' => 'DESC'
			) );

		extract( $args );
		$where = array();
		$params = array();

		$sql = "SELECT $select FROM {$table_name}";

		if ( !empty( $email ) ) {
			$where[]= "email LIKE %s";
			$params[] = $email. '%';
		}

		if ( !empty( $signup_method ) && in_array( $signup_method, array( 'form', 'checkbox' ) ) ) {
			$where[] = "signup_method = %s";
			$params[] = $signup_method;
		}

		if ( !empty( $where ) ) {
			$sql .= ' WHERE '. implode( ' AND ', $where );
		}

		$sql .= " ORDER BY $orderby $order LIMIT {$offset}, {$limit}";
		// prepare and run query
		$query = $wpdb->prepare( $sql, $params );

		if ( $select == 'COUNT(*)' ) {
			return (int) $wpdb->get_var( $query );
		} elseif ( $limit == 1 ) {
			return $wpdb->get_row( $query );
		} else {
			return $wpdb->get_results( $query );
		}

	}

	public function delete_logs( array $ids ) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'mc4wp_log';

		$comma_separated_ids = implode( ',', $ids );
		return $wpdb->query( "DELETE FROM {$table_name} WHERE id IN ({$comma_separated_ids})" );
	}

}
