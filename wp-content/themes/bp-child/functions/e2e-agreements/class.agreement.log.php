<?php

class AgreementLog
{
    public static function get_entry($id)
    {
        global $wpdb;
        return $wpdb->get_row($wpdb->prepare("SELECT * FROM wp_e2e_agreement_log WHERE id = %d ", $id));
    }

    public static function add_entry($data)
    {
        global $wpdb;
        $wpdb->insert('wp_e2e_agreement_log',
            array(
                'agreement_id' => $data['agreement_id'],
                'sent_by' => $data['sent_by'],
                'sent_by_user' => get_user_meta(get_current_user_id(), 'first_name', true) . ' ' . get_user_meta(get_current_user_id(), 'last_name', true),
                'message' => $data['message'],
                'date' => gmmktime(),
                'state' => $data['state']
            ),
            array('%d', '%d', '%s', '%s', '%d', '%s')
        );
        return true;
    }

    public static function get_agreement_log($agreement_id)
    {
        global $wpdb;
        return $wpdb->get_results($wpdb->prepare("SELECT * FROM wp_e2e_agreement_log WHERE agreement_id = %d ", $agreement_id));
    }
}