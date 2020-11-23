<?php
/**
  * Import/export admin page
  *
  **/

defined( 'ABSPATH' ) || exit;

class WPSR_Admin_Import_Export{
    
    function __construct(){
        
        add_filter( 'wpsr_register_admin_page', array( $this, 'register' ) );
        
    }
    
    function register( $pages ){
        
        $pages[ 'import_export' ] = array(
            'name' => __( 'Import/Export', 'wpsr' ),
            'banner' => WPSR_ADMIN_URL . '/images/banners/import-export.svg',
            'category' => 'other',
            'type' => 'settings',
            'callbacks' => array(
                'page' => array( $this, 'page' )
            )
        );
        
        return $pages;
        
    }
    
    function page(){

        $form = new WPSR_Form();

        echo '<div class="notice notice-success inline hidden"><p>' . __( 'Successful imported data !', 'wpsr' ) . '</p></div>';
        echo '<div class="notice notice-error inline hidden"><p>' . __( 'Failed to import data, please re-import the data !', 'wpsr' ) . '</p></div>';

        echo '<form id="import_form">';

        $form->section_start( __( 'Import data', 'wpsr' ) );
        $form->section_description( __( 'Import the already exported WP Socializer data using the field below. Please note that importing will <b>overwrite</b> all the existing buttons created and the settings.', 'wpsr' ) );
        $form->label( __( 'Import data', 'wpsr' ) );
        $form->field( 'textarea', array(
                'name' => 'import_data',
                'value' => '',
                'helper' => __( 'Paste the exported data into the text box above.', 'wpsr' ),
                'rows' => '3',
                'cols' => '100'
        ));
        $form->build();
        $form->section_end();

        wp_nonce_field( 'wpsr_import_nonce' );

        echo '<p align="right"><input type="submit" class="import_form_submit button button-primary" value="' . __( 'Import', 'wpsr' ) . '" /></p>';
        echo '</form>';
        
        $form->section_start( __( 'Export', 'wpsr' ) );
        $form->label( __( 'Export data', 'wpsr' ) );
        $form->field( 'textarea', array(
            'name' => 'export_data',
            'value' => WPSR_Import_Export::export(),
            'helper' => __( 'Copy the data above, save it and import it later', 'wpsr' ),
            'rows' => '3',
            'cols' => '100',
            'custom' => 'onClick="this.select();"'
        ));
        $form->build();
        $form->section_end();

    }
    
    function validation( $input ){
        return $input;
    }
    
}

new WPSR_Admin_Import_Export();

?>