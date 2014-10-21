<?php

class Process
{
    public $id = null;

    public $name = '';

    public $title = '';

    public $major_version = '';

    public $minor_version = '';

    public $patch_version = '';

    public function __construct( $id = null )
    {        
        if($id !== null)   
            $this->id = $id;

    }

    public static function get_process_by_id( $id ){
        global $wpdb;
        return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM wp_processes WHERE id = %d ", $id ) );
    }

    public static function get_all( ){
        global $wpdb;
        return $wpdb->get_results("SELECT * FROM wp_processes");
    }

    public static function get_full_name( $process ){
        $full_name = $process->title.' v'.$process->version_major.'.'.$process->version_minor;
        if( $process->patch_version ){
            $full_name .= '.'.$process->patch_version;
        }
        return $full_name;
    }
}