<?php
/**
  * Share icons settings page
  *
  **/

defined( 'ABSPATH' ) || exit;

class WPSR_Admin_Share_Icons{

    function __construct(){
        
        add_filter( 'wpsr_register_admin_page', array( $this, 'register' ) );
        
    }
    
    function register( $pages ){
        
        $pages[ 'share_icons' ] = array(
            'name' => __( 'Share icons', 'wpsr' ),
            'banner' => WPSR_ADMIN_URL . '/images/banners/share-icons.svg',
            'description' => __( 'Add icons above and below posts to share the content on social media sites.', 'wpsr' ),
            'category' => 'feature',
            'type' => 'feature',
            'form_name' => 'social_icons_settings',
            'callbacks' => array(
                'page' => array( $this, 'page' ),
                'form' => array( $this, 'form' ),
                'validation' => array( $this, 'validation' ),
            )
        );
        
        return $pages;
        
    }

    function page(){
        WPSR_Admin::settings_form( 'share_icons' );
    }

    function template( $values, $i ){

        if( !isset( $values[ 'tmpl' ][ $i ] ) ){
            $values[ 'tmpl' ][ $i ] = WPSR_Options::default_values( 'share_icons' );
            if( $i == 2 ){
                $values[ 'tmpl' ][ $i ][ 'selected_icons' ] = 'W10=';
                $values[ 'tmpl' ][ $i ][ 'share_counter' ] = '';
                $values[ 'tmpl' ][ $i ][ 'heading' ] = '';
            }
        }else{
            $values[ 'tmpl' ][ $i ] = WPSR_Lists::set_defaults( $values[ 'tmpl' ][ $i ], WPSR_Options::default_values( 'share_icons' ) );
        }

        $options = WPSR_Options::options( 'share_icons' );
        $form = new WPSR_Form( $values, $options );

        echo '<div class="template_wrap" data-id="' . $i . '">';

        $form->section_start( __( 'Choose the share icons', 'wpsr' ), '2' );
        $form->section_description( __( 'Add share icons to the template, re-arrange them and configure individual icon settings.', 'wpsr' ) );
        WPSR_Icons_Editor::editor( $values[ 'tmpl' ][ $i ][ 'selected_icons' ], 'tmpl[' . $i . '][selected_icons]' );
        $form->section_end();

        // Customization
        $form->section_start( __( 'Customization', 'wpsr' ), '3' );

        $form->tab_list(array(
            'style' => '<i class="fas fa-paint-brush"></i>' . __( 'Style', 'wpsr' ),
            'position' => '<i class="fas fa-arrows-alt"></i>' . __( 'Position', 'wpsr' ),
            'share_counter' => '<i class="fab fa-creative-commons-zero"></i>' . __( 'Share counter', 'wpsr' ),
            'responsiveness' => '<i class="fas fa-mobile-alt"></i>' . __( 'Responsiveness', 'wpsr' ),
            'misc' => '<i class="fas fa-cog"></i>' . __( 'Miscellaneous', 'wpsr' ),
        ));

        echo '<div class="tab_wrap">';
        $this->tab_style( $form, $i );
        $this->tab_position( $form, $i );
        $this->tab_share_counter( $form, $i );
        $this->tab_responsiveness( $form, $i );
        $this->tab_misc( $form, $i );
        echo '</div>';

        $form->section_end();

        // Location rules
        $form->section_start( __( 'Conditions to display the template', 'wpsr' ), '5' );
        $form->section_description( __( 'Choose the below options to select the pages which will display the template.', 'wpsr' ) );
        WPSR_Location_Rules::display_rules( "tmpl[$i][loc_rules]", $values['tmpl'][$i]['loc_rules'] );
        $form->section_end();

        echo '</div>';

    }

    function form( $values ){

        $form = new WPSR_Form();
        $values = WPSR_Lists::set_defaults( $values, array(
            'ft_status' => 'enable',
            'tmpl' => array()
        ));

        $form->section_start( __( 'Enable/disable share icons', 'wpsr' ), '1' );
        $form->label( __( 'Select to enable or disable share icons feature', 'wpsr' ) );
        $form->field( 'select', array(
            'name' => 'ft_status',
            'value' => $values[ 'ft_status' ],
            'list' => array(
                'enable' => __( 'Enable share icons', 'wpsr' ),
                'disable' => __( 'Disable share icons', 'wpsr' )
            ),
        ));
        $form->build();
        $form->section_end();

        $icon_settings = array( 'icon', 'text', 'hover_text', 'html' );
        WPSR_Icons_Editor::commons( $icon_settings );

        echo '<div class="feature_wrap">';

        $template_count = 2;

        echo '<ul class="template_tab">';
        for( $i = 1; $i <= $template_count; $i++ ){
            echo '<li>Template ' . $i . '</li>';
        }
        echo '</ul>';

        for( $i=1; $i<=$template_count; $i++ ){
            $this->template( $values, $i );
        }

        echo '</div>';

    }

    function tab_style( $form, $i ){

        echo '<div data-tab="style">';

        $form->label( __( 'Icon layout', 'wpsr' ) );
        $form->field( 'image_select', array(
            'name' => 'tmpl[' . $i . '][layout]',
            'value' => $form->values[ 'tmpl' ][ $i ]['layout'],
            'class' => 'setting_btn_layout' . $i,
            'list' => $form->options[ 'layout' ],
        ));
        $form->end();

        $form->label( __( 'Icon size', 'wpsr' ) );
        $form->field( 'image_select', array(
            'name' => 'tmpl[' . $i . '][icon_size]',
            'value' => $form->values[ 'tmpl' ][ $i ]['icon_size'],
            'list' => $form->options[ 'icon_size' ],
        ));
        $form->end();

        $form->start( '', 'data-conditioner data-condr-input=".setting_btn_layout' . $i . '" data-condr-value="" data-condr-action="simple?show:hide" data-condr-events="change"' );
        $form->label( __( 'Icon shape', 'wpsr' ) );
        $form->field( 'image_select', array(
            'name' => 'tmpl[' . $i . '][icon_shape]',
            'value' => $form->values[ 'tmpl' ][ $i ]['icon_shape'],
            'list' => $form->options[ 'icon_shape' ],
            'helper' => 'Note: Shapes marked * might not react well to certain hover effects and share counter styles.'
        ));
        $form->end();

        $form->label( __( 'Icon color', 'wpsr' ) );
        $form->field( 'text', array(
            'name' => 'tmpl[' . $i . '][icon_color]',
            'value' => $form->values[ 'tmpl' ][ $i ]['icon_color'],
            'class' => 'color_picker',
            'helper' => __( 'Set empty value to use brand color', 'wpsr' )
        ));
        $form->end();

        $form->label( __( 'Icon background color', 'wpsr' ) );
        $form->field( 'text', array(
            'name' => 'tmpl[' . $i . '][icon_bg_color]',
            'value' => $form->values[ 'tmpl' ][ $i ]['icon_bg_color'],
            'class' => 'color_picker',
            'helper' => __( 'Set empty value to use brand color', 'wpsr' )
        ));
        $form->end();

        $form->label( __( 'Hover effect', 'wpsr' ) );
        $form->field( 'select', array(
            'name' => 'tmpl[' . $i . '][hover_effect]',
            'value' => $form->values[ 'tmpl' ][ $i ]['hover_effect'],
            'list' => $form->options[ 'hover_effect' ],
        ));
        $form->end();

        $form->label( __( 'Space between icons', 'wpsr' ) );
        $form->field( 'select', array(
            'name' => 'tmpl[' . $i . '][padding]',
            'value' => $form->values[ 'tmpl' ][ $i ]['padding'],
            'list' => $form->options[ 'padding' ],
            'helper' => __( 'Select to add space between icons', 'wpsr' ),
        ));
        $form->end();

        $form->build();

        echo '</div>';

    }

    function tab_share_counter( $form, $i ){

        echo '<div data-tab="share_counter">';

        $form->label( __( 'Share counter', 'wpsr' ) );
        $form->field( 'select', array(
            'name' => 'tmpl[' . $i . '][share_counter]',
            'value' => $form->values[ 'tmpl' ][ $i ]['share_counter'],
            'class' => 'setting_share_counter' . $i,
            'list' => $form->options[ 'share_counter' ],
        ));
        $form->end();

        $form->start( '', 'data-conditioner data-condr-input=".setting_share_counter' . $i . '" data-condr-value="individual" data-condr-action="pattern?show:hide" data-condr-events="change"' );
        $form->label( __( 'Share counter style', 'wpsr' ) );
        $form->field( 'image_select', array(
            'name' => 'tmpl[' . $i . '][sc_style]',
            'value' => $form->values[ 'tmpl' ][ $i ]['sc_style'],
            'list' => $form->options[ 'sc_style' ]
        ));
        $form->end();

        $form->start( '', 'data-conditioner data-condr-input=".setting_share_counter' . $i . '" data-condr-value="total" data-condr-action="pattern?show:hide" data-condr-events="change"' );
        $form->label( __( 'Total share count position', 'wpsr' ) );
        $form->field( 'select', array(
            'name' => 'tmpl[' . $i . '][sc_total_position]',
            'value' => $form->values[ 'tmpl' ][ $i ]['sc_total_position'],
            'list' => $form->options[ 'sc_total_position' ],
        ));
        $form->end();

        $form->build();

        WPSR_Share_Counter::admin_note();

        echo '</div>';

    }

    function tab_position( $form, $i ){

        echo '<div data-tab="position">';

        $form->label( __( 'Position of the share icons on the page', 'wpsr' ) );
        $form->field( 'select', array(
            'name' => 'tmpl[' . $i . '][position]',
            'list' => $form->options[ 'position' ],
            'value' => $form->values[ 'tmpl' ][ $i ][ 'position' ]
        ));
        $form->end();

        $form->label( __( 'Share icons in post excerpts', 'wpsr' ) );
        $form->field( 'select', array(
            'name' => 'tmpl[' . $i . '][in_excerpt]',
            'list' => $form->options[ 'in_excerpt' ],
            'value' => $form->values[ 'tmpl' ][ $i ][ 'in_excerpt' ]
        ));
        $form->end();

        $form->build();

        WPSR_Admin_Shortcodes::note( __( 'Share icons', 'wpsr' ), 'wpsr_share_icons' );

        echo '</div>';

    }

    function tab_responsiveness( $form, $i ){

        echo '<div data-tab="responsiveness">';

        $form->label( __( 'On desktop (or) large screen', 'wpsr' ) );
        $form->field( 'select', array(
            'name' => 'tmpl[' . $i . '][lg_screen_action]',
            'value' => $form->values[ 'tmpl' ][ $i ]['lg_screen_action'],
            'list' => $form->options[ 'lg_screen_action' ],
        ));
        $form->end();

        $form->label( __( 'On mobile (or) small screen', 'wpsr' ) );
        $form->field( 'select', array(
            'name' => 'tmpl[' . $i . '][sm_screen_action]',
            'value' => $form->values[ 'tmpl' ][ $i ]['sm_screen_action'],
            'list' => $form->options[ 'sm_screen_action' ],
        ));
        $form->end();

        $form->label( __( 'Responsive width', 'wpsr' ) );
        $form->field( 'text', array(
            'name' => 'tmpl[' . $i . '][sm_screen_width]',
            'value' => $form->values[ 'tmpl' ][ $i ]['sm_screen_width'],
            'type' => 'number',
            'helper' => __( 'The width of the screen below which the share icons switches to mobile mode.' ),
            'after_text' => 'px'
        ));
        $form->end();

        $form->build();

        echo '</div>';

    }

    function tab_misc( $form, $i ){

        echo '<div data-tab="misc">';

        $form->start( '', 'data-conditioner data-condr-input=".setting_btn_layout' . $i . '" data-condr-value="" data-condr-action="simple?show:hide" data-condr-events="change"' );
        $form->label( __( 'Center the icons', 'wpsr' ) );
        $form->field( 'select', array(
            'name' => 'tmpl[' . $i . '][center_icons]',
            'value' => $form->values[ 'tmpl' ][ $i ]['center_icons'],
            'list' => $form->options[ 'center_icons' ]
        ));
        $form->end();

        $form->label( __( 'Heading text', 'wpsr' ) );
        $form->field( 'text', array(
            'name' => 'tmpl[' . $i . '][heading]',
            'value' => $form->values[ 'tmpl' ][ $i ][ 'heading' ],
            'class' => '',
            'helper' => __( 'Heading to show above the share buttons. HTML is allowed.' )
        ));
        $form->end();

        $form->label( __( 'Number of icons to group into one single icon', 'wpsr' ) );
        $form->field( 'select', array(
            'name' => 'tmpl[' . $i . '][more_icons]',
            'value' => $form->values[ 'tmpl' ][ $i ]['more_icons'],
            'list' => $form->options[ 'more_icons' ],
            'helper' => __( 'Select the number of icons from the end which will should be grouped into one single icon called "More"', 'wpsr' )
        ));
        $form->end();

        $form->label( __( 'Custom HTML above and below icons', 'wpsr' ) );
        $form->field( 'textarea', array(
            'name' => 'tmpl[' . $i . '][custom_html_above]',
            'value' => $form->values[ 'tmpl' ][ $i ][ 'custom_html_above' ],
            'class' => 'inline_field',
            'placeholder' => 'Above'
        ));
        $form->field( 'textarea', array(
            'name' => 'tmpl[' . $i . '][custom_html_below]',
            'value' => $form->values[ 'tmpl' ][ $i ][ 'custom_html_below' ],
            'class' => '',
            'placeholder' => 'Below'
        ));
        $form->description( __( 'Supports any HTML and shortcodes', 'wpsr' ) );
        $form->end();

        $form->build();

        echo '</div>';

    }

    function validation( $input ){

        if( $input['ft_status'] == 'enable' ){
            $btn_settings = get_option( 'wpsr_button_settings' );
            $btn_settings[ 'ft_status' ] = 'disable';
            update_option( 'wpsr_button_settings', $btn_settings );
        }

        return $input;
    }
    
}

new WPSR_Admin_Share_Icons();

?>