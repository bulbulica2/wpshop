<?php
/**
  * Text sharebar settings page
  *
  **/

defined( 'ABSPATH' ) || exit;

class WPSR_Admin_Text_Sharebar{
    
    function __construct(){
        
        add_filter( 'wpsr_register_admin_page', array( $this, 'register' ) );
        
    }
    
    function register( $pages ){

        $pages[ 'text_sharebar' ] = array(
            'name' => __( 'Text sharebar', 'wpsr' ),
            'banner' => WPSR_ADMIN_URL . '/images/banners/text-sharebar.svg',
            'description' => __( 'Add tooltip to share the text selected by the user on social media sites.', 'wpsr' ),
            'category' => 'feature',
            'type' => 'feature',
            'form_name' => 'text_sharebar_settings',
            'callbacks' => array(
                'page' => array( $this, 'page' ),
                'form' => array( $this, 'form' ),
                'validation' => array( $this, 'validation' ),
            ),
        );
        
        return $pages;
        
    }
    
    function page(){
        
        WPSR_Admin::settings_form( 'text_sharebar' );
        
    }

    function form( $values ){

        $values = WPSR_Lists::set_defaults( $values, WPSR_Options::default_values( 'text_sharebar' ) );
        $options = WPSR_Options::options( 'text_sharebar' );
        $form = new WPSR_Form();

        $form->section_start( __( 'Enable/disable text sharebar', 'wpsr' ), '1' );
        $form->label( __( 'Select to enable or disable text sharebar feature', 'wpsr' ) );
        $form->field( 'select', array(
            'name' => 'ft_status',
            'value' => $values[ 'ft_status' ],
            'list' => $options[ 'ft_status' ],
        ));
        $form->build();
        $form->section_end();

        echo '<div class="feature_wrap">';
        
        $sb_sites = WPSR_Lists::social_icons();
        
        $form->section_start( __( 'Add buttons to text sharebar', 'wpsr' ) );
        $form->section_description( __( 'Select buttons from the list below and add it to the selected list.', 'wpsr' ) );

        echo '<table class="form-table ssb_tbl"><tr><td width="90%">';
        echo '<select class="ssb_list widefat">';
        foreach( $sb_sites as $id=>$prop ){
            if( in_array( 'for_tsb', $prop[ 'features' ] ) ){
                echo '<option value="' . $id . '" data-color="' . $prop['colors'][0] . '">' . $prop[ 'name' ] . '</option>';
            }
        }
        echo '</select>';
        echo '</td><td>';
        echo '<button class="button button-primary ssb_add">' . __( 'Add button', 'wpsr' ) . '</button>';
        echo '</td></tr></table>';
        
        $decoded = base64_decode( $values[ 'template' ] );
        $tsb_btns = json_decode( $decoded );
        
        if( !is_array( $tsb_btns ) ){
            $tsb_btns = array();
        }
        
        echo '<h4>' . __( 'Selected buttons', 'wpsr' ) . '</h4>';
        echo '<ul class="ssb_selected_list clearfix">';
        if( count( $tsb_btns ) > 0 ){
            foreach( $tsb_btns as $tsb_item ){
                $sb_info = $sb_sites[ $tsb_item ];
                echo '<li title="' . $sb_info[ 'name' ] . '" data-id="' . $tsb_item . '" style="background-color:' . $sb_info['colors'][0] . '"><i class="' . $sb_info[ 'icon' ] . '"></i> <span class="ssb_remove" title="' . __( 'Delete button', 'wpsr' ) . '">x</span></li>';
            }
        }else{
            echo '<span class="ssb_empty">' . __( 'No buttons are selected for text sharebar', 'wpsr' ) . '</span>';
        }
        echo '</ul>';
        echo '<input type="hidden" name="template" class="ssb_template" value="' . $values[ 'template' ] . '"/>';
        
        $form->section_end();
        
        // Settings form
        $form->section_start( __( 'Settings' ), '3' );

        $form->label( __( 'ID or CSS class name of the content to show text sharebar', 'wpsr' ) );
        $form->field( 'text', array(
            'name' => 'content',
            'value' => $values['content'],
            'placeholder' => 'Ex: .entry-content',
            'helper' => '<a href="https://www.youtube.com/watch?v=GQ1YO0xZ7WA" target="_blank">Watch quick video to identify this</a>'
        ));
        $form->end();

        $form->label( __( 'Button size', 'wpsr' ) );
        $form->field( 'image_select', array(
            'name' => 'size',
            'value' => $values['size'], 
            'list' => $options[ 'size' ]
        ));
        $form->end();

        $form->label( __( 'Background color', 'wpsr' ) );
        $form->field( 'text', array(
            'name' => 'bg_color',
            'value' => $values['bg_color'],
            'class' => 'color_picker'
        ));
        $form->end();

        $form->label( __( 'Icon color', 'wpsr' ) );
        $form->field( 'text', array(
            'name' => 'icon_color',
            'value' => $values['icon_color'],
            'class' => 'color_picker'
        ));
        $form->end();

        $form->label( __( 'Maximum word count to quote', 'wpsr' ) );
        $form->field( 'text', array(
            'type' => 'number',
            'name' => 'text_count',
            'value' => $values['text_count'],
            'helper' => __( 'Set value to 0 to include all the selected text', 'wpsr' )
        ));
        $form->end();

        $form->build();
        $form->section_end();
        
        // Location rules
        $form->section_start( __( 'Conditions to display the text sharebar', 'wpsr' ), '4' );
        $form->section_description( __( 'Choose the below options to select the pages which will display the text sharebar.', 'wpsr' ) );
        WPSR_Location_Rules::display_rules( 'loc_rules', $values[ 'loc_rules' ] );
        $form->section_end();

        echo '</div>';
        
        echo '<script>';
        echo 'var sb_sites = ' . json_encode( $sb_sites ) . ';';
        echo '</script>';
        
    }
    
    function validation( $input ){
        return $input;
    }

}

new WPSR_Admin_Text_Sharebar();

?>