<?php

if( ! defined( 'ABSPATH' ) ) exit;

class WPSR_Admin_Tools{

    public static function init(){

        // Add TinyMCE button
        add_action( 'admin_init', array( __class__, 'register_mce' ) );

    }

    public static function register_mce(){

        add_filter( 'mce_buttons', array( __class__, 'register_mce_button' ) );

        add_filter( 'mce_external_plugins', array( __class__, 'register_mce_js' ) );

    }

    public static function register_mce_button( $buttons ){

        array_push( $buttons, 'separator', 'wp-socializer' );
        return $buttons;

    }
    
    public static function register_mce_js( $plugins ){

        $plugins[ 'wp-socializer' ] = WPSR_ADMIN_URL . '/js/tinymce.js';
        return $plugins;

    }

}

WPSR_Admin_Tools::init();

?>