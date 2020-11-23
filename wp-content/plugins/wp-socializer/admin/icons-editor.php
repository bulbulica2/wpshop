<?php

defined( 'ABSPATH' ) || exit;

class WPSR_Icons_Editor{

    public static function editor( $selected_icons, $form_name = 'selected_icons' ){

        $social_icons = WPSR_Lists::social_icons();

        echo '<div class="sie_wrap">';

        echo '<div class="sie_editor">';
        echo '<ul class="sic_list sie_selected">';

        $si_selected = array();
        $si_saved = json_decode( base64_decode( $selected_icons ) );

        foreach( $si_saved as $icon ){
            $id = key( $icon );
            $settings = (array) $icon->$id;
            array_push( $si_selected, array( $id => $settings ) );
        }

        foreach( $si_saved as $si_icons ){
            foreach( $si_icons as $id => $settings ){

                if( !array_key_exists( $id, $social_icons ) ){
                    continue;
                }

                $datas = array();
                $props = $social_icons[ $id ];
                array_push( $datas, 'data-id="' . $id . '"' );

                foreach( $settings as $ics_id => $ics_value ){
                    array_push( $datas, 'data-icns_' . $ics_id . '="' . esc_attr( $ics_value ) . '"' );
                }

                $datas = implode( ' ', $datas );
                echo '<li ' . $datas . ' style="background-color: ' . $props[ 'colors' ][ 0 ] . '">';
                    echo '<i class="' . $props[ 'icon' ] . ' item_icon" ></i> ';
                    echo '<span>' . $props[ 'name' ] . '</span>';
                    echo '<i class="fa fa-times sic_action_btn sie_delete_btn" title="' . __( 'Delete icon', 'wpsr' ) . '"></i> ';
                    echo '<i class="fa fa-cog sic_action_btn sie_settings_btn" title="' . __( 'Icon settings', 'wpsr' ) . '"></i> ';
                echo '</li>';

            }
        }

        echo '</ul>';
        echo '</div>';

        echo '<div class="sie_toolbar">';
        echo '<button class="button button-primary sie_open_picker_btn"><i class="fas fa-plus" title="Add icon"></i> ' . __( 'Add social icon', 'wpsr' ) . '</button>';
        echo '</div>';

        echo '<input type="hidden" name="' . $form_name . '" class="sie_selected_icons" value="' . $selected_icons . '"/>';

        echo '</div>';

    }

    public static function commons( $allowed_icon_settings ){

        $social_icons = WPSR_Lists::social_icons();
        $icon_settings = self::icon_settings( $allowed_icon_settings );

        // Editor - Icon settings
        echo '<div class="sie_icon_settings sic_backdrop">
        <div class="sic_content">
        <header>
            <h3></h3>
            <i class="fa fa-times sic_close_btn"></i>
        </header>';
        echo '<section></section>';
        echo '<footer><button class="button button-primary sie_save_settings_btn">' . __( 'Save icon settings', 'wpsr' ) . '</button></footer>';
        echo '</div>
        </div>';

        // Picker
        echo '<div class="sip_picker sic_backdrop">
        <div class="sic_content">
        <header>
            <h3>Select an icon to add</h3>
            <i class="fa fa-times sic_close_btn"></i>
        </header>';

        echo '<section>';
        echo '<input type="search" class="widefat sip_filter" placeholder="Search icon"/>';
        echo '<p class="description">' . __( 'Note: Only services to which a link can be shared are listed below.', 'wpsr' ) . '</p>';
        echo '<ul class="sic_list sip_selector">';

        foreach( $social_icons as $id => $props ){
            $datas = array();
            array_push( $datas, 'data-id="' . $id . '"' );

            if( !in_array( 'for_share', $props[ 'features' ] ) ){
                continue;
            }

            foreach( $icon_settings as $is_id => $is_props ){

                if( $id == 'html' && $is_id != 'html' ){
                    continue;
                }
                
                if( $id != 'html' && $is_id == 'html' ){
                    continue;
                }

                array_push( $datas, 'data-icns_' . $is_id . '=""' );
            }

            $datas = implode( ' ', $datas );
            echo '<li ' . $datas . ' style="background-color: ' . $props[ 'colors' ][ 0 ] . '">';
                echo '<i class="' . $props[ 'icon' ] . ' item_icon" ></i> ';
                echo '<span>' . $props[ 'name' ] . '</span>';
                echo '<i class="fas fa-plus sic_action_btn sip_add_btn" title="' . __( 'Add icon', 'wpsr' ) . '"></i>';
                echo '<i class="fa fa-times sic_action_btn sie_delete_btn" title="' . __( 'Delete icon', 'wpsr' ) . '"></i> ';
                echo '<i class="fa fa-cog sic_action_btn sie_settings_btn" title="' . __( 'Icon settings', 'wpsr' ) . '"></i> ';
            echo '</li>';
        }

        echo '</ul>';
        echo '</section>';

        echo '</div>
        </div>';

        echo '<script>';
        echo 'var sip_icons = ' . json_encode( $social_icons ) . ';';
        echo 'var sip_icon_settings = ' . json_encode( $icon_settings ) . ';';
        echo '</script>';

    }

    public static function icon_settings( $allowed_icon_settings ){

        $all_icon_settings = array(
            'icon' => array(
                'type' => 'text',
                'helper' => __( 'Custom icon', 'wpsr' ),
                'placeholder' => __( 'Enter a custom icon URL for this site, starting with <code>http://</code>. You can also use class name of the icon font Example: <code>fa fa-star</code> Leave blank to use default icon', 'wpsr' )
            ),
            'text' => array(
                'type' => 'text',
                'helper' => __( 'Text to show next to icon', 'wpsr' ),
                'placeholder' => __( 'Enter custom text to appear to next to the icon. Leave blank to show no text.', 'wpsr' )
            ),
            'hover_text' => array(
                'type' => 'text',
                'helper' => __( 'Text to show on hovering the icon', 'wpsr' ),
                'placeholder' => __( 'Enter custom text to appear when the icon is hovered.', 'wpsr' )
            ),
            'html' => array(
                'type' => 'textarea',
                'helper' => __( 'Custom HTML', 'wpsr' ),
                'placeholder' => __( 'Enter custom HTML to occur. You can use the parameters <code>{url}</code> and <code>{title}</code> to replace them with the current page URL and title respectively. Shortcodes can also be used here.<br/><strong>Note:</strong> For any formatting issues, please use custom CSS to adjust the output as required.', 'wpsr' )
            )
        );

        foreach( $all_icon_settings as $id => $props ){
            if( !in_array( $id, $allowed_icon_settings ) ){
                unset( $all_icon_settings[ $id ] );
            }
        }

        return $all_icon_settings;

    }

}

?>