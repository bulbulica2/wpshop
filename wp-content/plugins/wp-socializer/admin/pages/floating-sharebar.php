<?php
/**
  * Floating sharebar settings page
  *
  **/

defined( 'ABSPATH' ) || exit;

class WPSR_Admin_Floating_Sharebar{
    
    function __construct(){
        
        add_filter( 'wpsr_register_admin_page', array( $this, 'register' ) );
        
    }
    
    function register( $pages ){
        
        $pages[ 'floating_sharebar' ] = array(
            'name' => __( 'Floating sharebar', 'wpsr' ),
            'banner' => WPSR_ADMIN_URL . '/images/banners/floating-sharebar.svg',
            'description' => __( 'Add floating/sticky share icons to share the content on social media sites.', 'wpsr' ),
            'category' => 'feature',
            'type' => 'feature',
            'form_name' => 'floating_sharebar_settings',
            'callbacks' => array(
                'page' => array( $this, 'page' ),
                'form' => array( $this, 'form' ),
                'validation' => array( $this, 'validation' ),
            )
        );
        
        return $pages;
        
    }

    function page(){
        
        WPSR_Admin::settings_form( 'floating_sharebar' );
        
    }

    function form( $values ){

        $values = WPSR_Lists::set_defaults( $values, WPSR_Options::default_values( 'floating_sharebar' ) );
        $options = WPSR_Options::options( 'floating_sharebar' );
        $form = new WPSR_Form( $values, $options );

        // Section 0
        $form->section_start( __( 'Enable/disable floating sharebar', 'wpsr' ), '1' );
        $form->label( __( 'Select to enable or disable floating sharebar feature', 'wpsr' ) );
        $form->field( 'select', array(
            'name' => 'ft_status',
            'value' => $form->values[ 'ft_status' ],
            'list' => $options[ 'ft_status' ],
        ));
        $form->build();
        $form->section_end();

        $icon_settings = array( 'icon', 'hover_text', 'html' );
        WPSR_Icons_Editor::commons( $icon_settings );

        echo '<div class="feature_wrap">';

        // Section 1
        $form->section_start( __( 'Choose the social icons', 'wpsr' ), '2' );
        $form->section_description( __( 'Add social icons to the template, re-arrange them and configure individual icon settings.', 'wpsr' ) );
        WPSR_Icons_Editor::editor( $values[ 'selected_icons' ], 'selected_icons' );
        $form->section_end();

        // Settings
        $form->section_start( __( 'Settings', 'wpsr' ), '3' );

        $form->tab_list(array(
            'style' => '<i class="fas fa-paint-brush"></i>' . __( 'Style', 'wpsr' ),
            'position' => '<i class="fas fa-arrows-alt"></i>' . __( 'Position', 'wpsr' ),
            'share_counter' => '<i class="fab fa-creative-commons-zero"></i>' . __( 'Share counter', 'wpsr' ),
            'responsiveness' => '<i class="fas fa-mobile-alt"></i>' . __( 'Responsiveness', 'wpsr' ),
            'misc' => '<i class="fas fa-cog"></i>' . __( 'Miscellaneous', 'wpsr' )
        ));

        echo '<div class="tab_wrap">';
        $this->tab_style( $form );
        $this->tab_position( $form );
        $this->tab_share_counter( $form );
        $this->tab_responsiveness( $form );
        $this->tab_misc( $form );
        echo '</div>';

        $form->section_end();

        // Location rules
        $form->section_start( __( 'Conditions to display the sharebar', 'wpsr' ), '4' );
        $form->section_description( __( 'Choose the below options to select the pages which will display the sharebar.', 'wpsr' ) );
        WPSR_Location_Rules::display_rules( 'loc_rules', $values['loc_rules'] );
        $form->section_end();

        echo '</div>';

    }

    function tab_style( $form ){

        echo '<div data-tab="style">';

        $form->label( __( 'Sharebar style', 'wpsr' ) );
        $form->field( 'image_select', array(
            'name' => 'style',
            'value' => $form->values['style'],
            'class' => 'setting_sb_style',
            'list' => $form->options[ 'style' ],
        ));
        $form->end();

        $form->start( '', 'data-conditioner data-condr-input=".setting_sb_style" data-condr-value="enclosed" data-condr-action="simple?show:hide" data-condr-events="change"' );
        $form->label( __( 'Sharebar background color', 'wpsr' ) );
        $form->field( 'text', array(
            'name' => 'sb_bg_color',
            'value' => $form->values['sb_bg_color'],
            'class' => 'color_picker',
        ));
        $form->end();

        $form->label( __( 'Icon size', 'wpsr' ) );
        $form->field( 'image_select', array(
            'name' => 'icon_size',
            'value' => $form->values[ 'icon_size' ],
            'list' => $form->options[ 'icon_size' ],
        ));
        $form->end();

        $form->label( __( 'Icon shape', 'wpsr' ) );
        $form->field( 'image_select', array(
            'name' => 'icon_shape',
            'value' => $form->values['icon_shape'],
            'class' => 'setting_shape',
            'list' => $form->options[ 'icon_shape' ],
            'helper' => 'Note: Shapes marked * might not react well to certain hover effects and share counter styles.'
        ));
        $form->end();

        $form->label( __( 'Icon color', 'wpsr' ) );
        $form->field( 'text', array(
            'name' => 'icon_color',
            'value' => $form->values['icon_color'],
            'class' => 'color_picker',
            'helper' => __( 'Set empty value to use brand color', 'wpsr' )
        ));
        $form->end();

        $form->label( __( 'Icon background color', 'wpsr' ) );
        $form->field( 'text', array(
            'name' => 'icon_bg_color',
            'value' => $form->values['icon_bg_color'],
            'class' => 'color_picker',
            'helper' => __( 'Set empty value to use brand color', 'wpsr' )
        ));
        $form->end();

        $form->label( __( 'Hover effect', 'wpsr' ) );
        $form->field( 'select', array(
            'name' => 'hover_effect',
            'value' => $form->values['hover_effect'],
            'list' => $form->options[ 'hover_effect' ],
        ));
        $form->end();

        $form->start( '', 'data-conditioner data-condr-input=".setting_shape" data-condr-value="" data-condr-action="simple?show:hide" data-condr-events="change"' );
        $form->label( __( 'Space between the icons', 'wpsr' ) );
        $form->field( 'select', array(
            'name' => 'padding',
            'value' => $form->values['padding'],
            'list' => $form->options[ 'padding' ],
            'helper' => __( 'Select to add space between the icons', 'wpsr' ),
        ));
        $form->end();

        $form->build();

        echo '</div>';

    }

    function tab_position( $form ){

        echo '<div data-tab="position">';

        $form->label( __( 'Position of the sharebar', 'wpsr' ) );
        $form->field( 'select', array(
            'name' => 'sb_position',
            'value' => $form->values['sb_position'],
            'class' => 'setting_sb_position',
            'list' => $form->options[ 'sb_position' ]
        ));
        $form->end();

        $form->start( '', 'data-conditioner data-condr-input=".setting_sb_position" data-condr-value="scontent" data-condr-action="simple?show:hide" data-condr-events="change"' );
        $form->label( __( 'ID or CSS class name of the content to stick with', 'wpsr' ) );
        $form->field( 'text', array(
            'name' => 'stick_element',
            'value' => $form->values['stick_element'],
            'placeholder' => 'Ex: #content',
            'helper' => '<a href="https://www.youtube.com/watch?v=GQ1YO0xZ7WA" target="_blank">Watch quick video to identify this</a>'
        ));
        $form->end();

        $form->label( __( 'Offset from the window', 'wpsr' ) );
        $form->field( 'text', array(
            'name' => 'offset',
            'value' => $form->values[ 'offset' ],
            'class' => '',
            'helper' => __( 'Example: 20px (or) 10% (or) -30px' )
        ));
        $form->end();

        $form->label( __( 'Sharebar movement', 'wpsr' ) );
        $form->field( 'select', array(
            'name' => 'movement',
            'value' => $form->values['movement'], 
            'list' => $form->options[ 'movement' ],
        ));
        $form->end();

        $form->build();

        echo '</div>';

    }

    function tab_share_counter( $form ){

        echo '<div data-tab="share_counter">';

        $form->label( __( 'Share counter', 'wpsr' ) );
        $form->field( 'select', array(
            'name' => 'share_counter',
            'value' => $form->values['share_counter'],
            'class' => 'setting_share_counter',
            'list' => $form->options[ 'share_counter' ],
        ));
        $form->end();

        $form->start( '', 'data-conditioner data-condr-input=".setting_share_counter" data-condr-value="individual" data-condr-action="pattern?show:hide" data-condr-events="change"' );
        $form->label( __( 'Share counter style', 'wpsr' ) );
        $form->field( 'image_select', array(
            'name' => 'sc_style',
            'value' => $form->values['sc_style'],
            'list' => $form->options[ 'sc_style' ],
            'helper' => __( 'To show count, in the same page under icons list, select an icon and enable gear icon &gt; show count', 'wpsr' )
        ));
        $form->end();

        $form->start( '', 'data-conditioner data-condr-input=".setting_share_counter" data-condr-value="total" data-condr-action="pattern?show:hide" data-condr-events="change"' );
        $form->label( __( 'Total share count position', 'wpsr' ) );
        $form->field( 'select', array(
            'name' => 'sc_total_position',
            'value' => $form->values['sc_total_position'],
            'list' => $form->options[ 'sc_total_position' ],
        ));
        $form->end();

        $form->start( '', 'data-conditioner data-condr-input=".setting_share_counter" data-condr-value="total" data-condr-action="pattern?show:hide" data-condr-events="change"' );
        $form->label( __( 'Total share count color', 'wpsr' ) );
        $form->field( 'text', array(
            'name' => 'sc_total_color',
            'value' => $form->values['sc_total_color'],
            'class' => 'color_picker',
            'helper' => __( 'Leave blank to use default color', 'wpsr' )
        ));
        $form->end();

        $form->build();

        WPSR_Share_Counter::admin_note();

        echo '</div>';

    }

    function tab_responsiveness( $form ){

        echo '<div data-tab="responsiveness">';

        $form->label( __( 'On desktop (or) large screen', 'wpsr' ) );
        $form->field( 'select', array(
            'name' => 'lg_screen_action',
            'value' => $form->values['lg_screen_action'],
            'list' => $form->options[ 'lg_screen_action' ],
        ));
        $form->end();

        $form->label( __( 'On mobile (or) small screen', 'wpsr' ) );
        $form->field( 'select', array(
            'name' => 'sm_screen_action',
            'value' => $form->values['sm_screen_action'],
            'list' => $form->options[ 'sm_screen_action' ],
        ));
        $form->end();

        $form->label( __( 'Responsive width', 'wpsr' ) );
        $form->field( 'text', array(
            'name' => 'sm_screen_width',
            'value' => $form->values[ 'sm_screen_width' ],
            'type' => 'number',
            'helper' => __( 'The width of the screen below which the sharebar switches to mobile mode.' ),
            'after_text' => 'px'
        ));
        $form->end();

        $form->build();

        echo '</div>';

    }

    function tab_misc( $form ){

        echo '<div data-tab="misc">';

        $form->label( __( 'Number of icons to group into one single icon', 'wpsr' ) );
        $form->field( 'select', array(
            'name' => 'more_icons',
            'value' => $form->values['more_icons'],
            'list' => $form->options[ 'more_icons' ],
            'helper' => __( 'Select the number of icons from the end which will should be grouped into one single icon called "More"', 'wpsr' )
        ));
        $form->end();

        $form->build();

        echo '</div>';

    }

    function validation( $input ){

        if( $input['ft_status'] == 'enable' ){
            $sb_settings = get_option( 'wpsr_sharebar_settings' );
            $sb_settings[ 'ft_status' ] = 'disable';
            update_option( 'wpsr_sharebar_settings', $sb_settings );
        }

        return $input;
    }
    
}

new WPSR_Admin_Floating_Sharebar();

?>