<?php
/*
Plugin Name: superMail
Plugin URI: http://www.b.org
Description: edit mail  list
Author: invader Zim
*/

class spm_class{
    public $current_plugin_url;
    public $table_name; 
    public function __construct(){
    if (isset($_POST['si_contact_submitted'])){
        // if script called from outside of Wordpress
        define( 'SHORTINIT', true );
        require_once('../../../wp-load.php');
        $this->send_emails();
        /*define('WP_USE_THEMES', false);
        require_once('../../../wp-load.php');*/
        /*define( 'BLOCK_LOAD', true );
        require_once( '../../..//wp-config.php' );
        require_once('../../../wp-includes/wp-db.php' );
        $wpdb = new wpdb( DB_USER, DB_PASSWORD, DB_NAME, DB_HOST);*/
    }
        global $table_name,$wpdb;
        $table_name = $wpdb->prefix;
        $table_name .= 'spm_emails_list';

        add_action('admin_menu', array($this,'register_super_mail_form'));
        add_action('init', array($this, 'super_mail_form_add_emails'));
        $plugin = plugin_basename( __FILE__ );
        add_filter( "plugin_action_links_$plugin", array ($this, 'plugin_add_settings_link_super_mail'));
    }
    public function register_super_mail_form(){
        add_menu_page('Super mail','Super mail settings','activate_plugins','super_mail', array ($this, 'super_mail_form_show'),'',68);
    }
    private function super_mail_form_get_emails(){
        global $wpdb;
        global $table_name;
        if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name){
            exit;
        } else {
            return $wpdb->get_results("SELECT email_address FROM $table_name");
        }
    }
    private function super_mail_form_get_subject(){
        global $wpdb;
        global $table_name;
        if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name){
            exit;
        } else {
            return $wpdb->get_results("SELECT email_subject FROM $table_name");
        }
    }
    private function super_mail_create_table($table_name){
        #require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        require_once('../../../wp-admin/includes/upgrade.php' );
        $sql = "CREATE TABLE $table_name (
            email_address VARCHAR(63535),
            email_subject VARCHAR(200)
        );";
        dbDelta($sql);
    }
    public function super_mail_form_add_emails(){
        global $wpdb, $table_name;
        if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name){
            $this->super_mail_create_table($table_name);
        }
        if (isset($_POST['emails_form_submitted'])){


            $sql_delete_all_rows = "TRUNCATE TABLE $table_name";
            $emails_addressess = array_filter(array_map('trim',explode(',', $_POST['emails_list'])));
            $emails_string = implode(',',$emails_addressess);
            $all = array('email_address'=>$emails_string,'email_subject'=>$_POST['email_subject']);
            if (count($emails_addressess) === 0){
                $wpdb->query($sql_delete_all_rows);
            } else {
                if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name){
                    $this->super_mail_create_table($table_name);
                } else {
                    $wpdb->query($sql_delete_all_rows);
                    $wpdb->insert($table_name,$all);
                }
            }
            header('Location: '.$_SERVER['HTTP_REFERER']);
        }
    }
    public function super_mail_form_show(){
        $result = $this->super_mail_form_get_emails();
        $subject = $this->super_mail_form_get_subject();
        if ($result){
            $non_empty_form = "
                <form method='post' action=''>
                <fieldset>
                <legend><h3>E-mail To:</h3> </legend>
                <p>Specify comma separated list of e-mails addresses</p>
                <textarea cols='70' rows='6' name='emails_list'>".$result[0]->email_address."</textarea><br/>
                <input type='hidden' name='emails_form_submitted' value='1'/>
                <label>E-mail subject: <input type='text' value='".$subject[0]->email_subject."' name='email_subject'/></label>
                <input type='submit' name='submit_emails_list' value='Save list'/>
                </fieldset>
                </form>
                ";
            echo $non_empty_form;
        } else {
            $empty_form = '
                <form method="post" action="">
                <fieldset>
                <legend><h3>E-mail To:</h3> </legend>
                <p>Specify comma separated list of e-mails addresses</p>
                <textarea cols="70" rows="6" name="emails_list"></textarea><br/>
                <input type="hidden" name="emails_form_submitted" value="1"/>
                <label>E-mail subject: <input type="text" name="email_subject"/></label>
                <input type="submit" name="submit_emails_list" value="Save list"/>
                </fieldset>
                </form>
                ';
            echo $empty_form;
        }
    }
    public function send_emails(){
        #echo 'aj';exit;
        /*$to = $this->super_mail_form_get_emails();
        $subject = $this->super_mail_form_get_subject();
        $to = $to[0]->email_address;$subject = $subject[0]->email_subject;
        $to = array('xuser13@meta.ua','whoknows2012@myopera.com');
        wp_mail($to, $subject, 'fuck');
        exit;*/
        if (isset($_POST['si_contact_submitted'])){

            if (empty($_POST['si_contact_name1'])){exit;}
            if (empty($_POST['si_contact_email'])){exit;}
            if (empty($_POST['si_contact_message'])){exit;}

            $si_contact_name1 = trim($_POST['si_contact_name1']);
            $si_contact_email = trim($_POST['si_contact_email']);
            $si_contact_message = trim($_POST['si_contact_message']);
            $si_contact_ex_field1 = trim($_POST['si_contact_ex_field1']);

            require_once 'captcha/securimage.php';
            $image = new Securimage();
            if ($image->check($_POST['si_contact_captcha_code']) !== true) {
                // return to JS script
                echo 'code';exit;
            }
            //get email addressess from db
            //function returns a string with comma separated e-mail addressess
            $to = $this->super_mail_form_get_emails();
            $to = $to[0]->email_address;
            if (strpos($to, ',') !== false){
                // e-mail addressess expecting to be 
                $to = array_filter(array_map('trim',explode(',', $to)));
            } else {
                $to = (array) $to;
            }
            $subject = $this->super_mail_form_get_subject();
            print_r($subject);exit;

            $headers[] = 'From: '.$_POST['si_contact_email'];
            if (!empty($si_contact_ex_field1)){
                if (wp_mail($to, $subject, $si_contact_message.'  Phone number:'.$si_contact_ex_field1, $headers)){
                    echo 'success';exit;
                }
            } else {
                if (wp_mail($to, $subject, $si_contact_message, $headers)){
                    echo 'success';exit;
                }
            }
        }
    }
    public function plugin_add_settings_link_super_mail($links){
        $settings_link = '<a href="'.admin_url('admin.php?page=super_mail').'">Settings</a>';
        $links[] = $settings_link;
        return $links;
    }

}
new spm_class;