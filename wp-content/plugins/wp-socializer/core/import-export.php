<?php
/**
  * Gives the page details for the services
  * 
  */

defined( 'ABSPATH' ) || exit;

class WPSR_Import_Export{
    
    public static function init(){
        
        add_action( 'wp_ajax_wpsr_import_ajax', array( __CLASS__, 'import_ajax' ) );
        
    }
    
    public static function settings_list(){
        
        return apply_filters( 'wpsr_mod_settings_list', array(
            'social_icons_settings' => 'wpsr_social_icons_settings',
            'floating_sharebar_settings' => 'wpsr_floating_sharebar_settings',
            'followbar_settings' => 'wpsr_followbar_settings',
            'text_sharebar_settings' => 'wpsr_text_sharebar_settings',
            'general_settings' => 'wpsr_general_settings'
        ));
        
    }
    
    public static function export(){
        
        $settings = self::settings_list();
        $exports = array();
        
        foreach( $settings as $id => $name ){
            $val = get_option( $name );
            if( $val ){
                $exports[ $id ] = $val;
            }
        }
        
        $export_json = wp_json_encode( $exports );
        return $export_json;
        
    }
    
    public static function import( $data = '' ){
        
        $settings = self::settings_list();
        $imports = array();
        $success_count = 0;
        
        if( trim( $data ) == '' )
            return false;
        
        try{
            $imports = json_decode( $data, true );
        }catch( Exception $e ){
            return false;
        }
        
        foreach( $imports as $id => $import_val ){
            if( array_key_exists( $id, $settings ) ){
                $name = $settings[ $id ];
                update_option( $name, $import_val );
                $success_count++;
            }
        }
        
        if( $success_count > 0 ){
            return true;
        }else{
            return false;
        }
        
    }
    
    public static function import_ajax(){
        
        if( !check_ajax_referer( 'wpsr_import_nonce', '_wpnonce', false ) ){
            echo 'auth_error';
            die( 0 );
        }
        
        $data = stripslashes( $_POST[ 'import_data' ] );
        $import_res = self::import( $data );
        
        if( $import_res ){
            echo 'import_success';
        }else{
            echo 'import_failed';
        }
        
        die( 0 );
        
    }
    
}

WPSR_Import_Export::init();

?>