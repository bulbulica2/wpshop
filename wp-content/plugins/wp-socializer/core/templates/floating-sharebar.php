<?php
/**
 * Floating sharebar template
 *
 **/

defined( 'ABSPATH' ) || exit;

class WPSR_Template_Floating_Sharebar{

    public static function init(){

        add_action( 'wp_footer', array( __CLASS__, 'output' ) );

    }

    public static function output(){

        $fsb_settings = WPSR_Lists::set_defaults( get_option( 'wpsr_floating_sharebar_settings' ), WPSR_Options::default_values( 'floating_sharebar' ) );
        $loc_rules_answer = WPSR_Location_Rules::check_rule( $fsb_settings[ 'loc_rules' ] );
        
        if( $fsb_settings[ 'ft_status' ] != 'disable' && $loc_rules_answer ){
            $gen_html = self::html( $fsb_settings );
            echo $gen_html;
            do_action( 'wpsr_do_floating_sharebar_print_template_end' );
        }

    }

    public static function html( $o ){

        $social_icons = WPSR_Lists::social_icons();
        $page_info = WPSR_Metadata::metadata();

        $counter_services = WPSR_Share_Counter::counter_services();
        $selected_icons = json_decode( base64_decode( $o[ 'selected_icons' ] ) );

        $classes = array( 'socializer', 'sr-popup', 'sr-vertical' );

        if( $o[ 'icon_size' ] != '' ){
            array_push( $classes, 'sr-' . $o[ 'icon_size' ] );
        }

        if( $o[ 'icon_shape' ] != '' ){
            array_push( $classes, 'sr-' . $o[ 'icon_shape' ] );
            array_push( $classes, 'sr-pad' );
        }

        if( $o[ 'hover_effect' ] != '' ){
            array_push( $classes, 'sr-' . $o[ 'hover_effect' ] );
        }

        if( $o[ 'padding' ] != '' && $o[ 'icon_shape' ] == '' ){
            array_push( $classes, 'sr-' . $o[ 'padding' ] );
        }

        $styles = array();
        if ( $o[ 'icon_bg_color' ] != '' ) array_push( $styles, 'background-color: ' . $o[ 'icon_bg_color' ] );
        if ( $o[ 'icon_color' ] != '' ) array_push( $styles, 'color: ' . $o[ 'icon_color' ] );
        $style = join( ';', $styles );
        $style = ( $style != '' ) ? ' style="' . $style . '"' : '';

        $icons_html = array();

        $counters_selected = array();

        foreach( $selected_icons as $icon ){

            $id = key( $icon );
            $props = $social_icons[ $id ];
            $icon_classes = array( 'sr-' . $id );

            if( !array_key_exists( $id, $social_icons ) ){
                continue;
            }

            $settings = WPSR_Lists::set_defaults( (array) $icon->$id, array(
                'icon' => '',
                'text' => '',
                'hover_text' => ''
            ));

            // If custom HTML button
            if( $id == 'html' ){
                $custom_html = WPSR_Metadata::replace_params( $settings[ 'html' ], $page_info );
                $ihtml = '<div class="sr-custom-html">' . do_shortcode( $custom_html ) . '</div>';
                array_push( $icons_html, $ihtml );
                continue;
            }

            $url = WPSR_Metadata::replace_params( $props[ 'link' ], $page_info );
            $onclick = isset( $props[ 'onclick' ] ) ? 'onclick="' . $props[ 'onclick' ] . '"' : '';

            $icon = '';
            if( $settings[ 'icon' ] == '' ){
                $icon = '<i class="' . esc_attr( $props[ 'icon' ] ) . '"></i>';
            }else{
                $icon_val = $settings[ 'icon' ];
                if (strpos( $settings[ 'icon' ], 'http' ) === 0) {
                    $icon = '<img src="' . esc_attr( $icon_val ) . '" alt="' . esc_attr( $id ) . '" height="50%" />';
                }else{
                    $icon = '<i class="' . esc_attr( $icon_val ) . '"></i>';
                }
            }

            $title = '';
            if( $settings[ 'hover_text' ] == '' ){
                $title = $props[ 'title' ];
            }else{
                $title = $settings[ 'hover_text' ];
            }

            $count_tag = '';
            if( ( $o[ 'share_counter' ] == 'individual' || $o[ 'share_counter' ] == 'total-individual' ) && array_key_exists( $id, $counter_services) ){
                $count_holder = WPSR_Share_Counter::placeholder( $page_info[ 'url' ], $id );
                $count_tag = '<span class="ctext">' . $count_holder . '</span>';
                array_push( $classes, 'sr-' . $o[ 'sc_style' ] );
                if( $o[ 'sc_style' ] != 'count-1' ){
                    array_push( $icon_classes, 'sr-text-in' );
                }
            }
            array_push( $counters_selected, $id );

            $data_attr = '';
            if( $id == 'pinterest' ){
                $data_attr = 'data-pin-custom="true"';
            }

            if( array_key_exists( 'link_mobile', $props ) ){
                $mobile_link = WPSR_Metadata::replace_params( $props[ 'link_mobile' ], $page_info );
                $data_attr .= ' data-mobile="' . $mobile_link . '"';
            }

            $ihtml = '<span class="' . implode( ' ', $icon_classes ) . '"><a rel="nofollow" href="' . esc_attr( $url ) . '" target="_blank" ' . $onclick . ' title="' . esc_attr( $title ) . '" ' . $style . ' ' . $data_attr . '>' . $icon . $count_tag . '</a></span>';
            array_push( $icons_html, $ihtml );

        }

        if( intval( $o[ 'more_icons' ] ) > 0 ){
            $more_count = intval( $o[ 'more_icons' ] );
            $more_icons = array_slice( $icons_html, -$more_count, $more_count );
            $more_html = '<span class="sr-more"><a href="#" target="_blank" title="More sites" ' . $style . '><i class="fa fa-share-alt"></i></a><ul class="socializer">' . implode( "\n", $more_icons ) . '</ul></span>';
            $icons_html = array_slice( $icons_html, 0, -$more_count );
            array_push( $icons_html, $more_html );
        }

        $all_icons_html = '<div class="' . implode( " ", $classes ) . '">' . implode( "\n", $icons_html ) . '</div>';
        $row_html = $all_icons_html;
        $html = '';

        if( $o[ 'share_counter' ] == 'total' || $o[ 'share_counter' ] == 'total-individual' ){
            $total_counter_html = WPSR_Share_Counter::total_count_html( array(
                'text' => 'Shares',
                'counter_color' => $o['sc_total_color'],
                'add_services' => $counters_selected,
                'size' => $o[ 'icon_size' ]
            ), $page_info);

            if( $o[ 'sc_total_position' ] == 'top' ){
                $row_html = $total_counter_html;
                $row_html .= $all_icons_html;
            }else{
                $row_html = $all_icons_html;
                $row_html .= $total_counter_html;
            }

        }

        $wrap_classes = array( 'wp-socializer wpsr-sharebar wpsr-sb-vl wpsr-hide' );
        $wrap_styles = array();

        array_push( $wrap_classes, 'wpsr-sb-vl-' . $o[ 'sb_position' ] );
        array_push( $wrap_classes, 'wpsr-sb-vl-' . $o[ 'movement' ] );

        if( $o[ 'style' ] == 'enclosed' ){
            array_push( $wrap_classes, 'wpsr-sb-simple' );
        }

        array_push( $wrap_styles, ( $o[ 'sb_position' ] == 'wleft' ? 'left' : ( $o[ 'sb_position' ] == 'scontent' ) ? 'margin-left' : 'right' ) . ':' . $o[ 'offset' ] );

        if($o['style'] == 'enclosed'){
            array_push( $wrap_styles, 'background-color: ' . $o[ 'sb_bg_color' ] );
        }

        $wrap_classes = implode( ' ', $wrap_classes );
        $wrap_styles = implode( ';', $wrap_styles );

        $open_icon = WPSR_Lists::public_icons( 'sb_open' );
        $close_icon = WPSR_Lists::public_icons( 'sb_close' );

        $data_attrs = array(
            'stick-to' => esc_attr( $o[ 'stick_element' ] ),
            'lg-action' => esc_attr( $o[ 'lg_screen_action' ] ),
            'sm-action' => esc_attr( $o[ 'sm_screen_action' ] ),
            'sm-width' => esc_attr( $o[ 'sm_screen_width' ] ),
        );
        $data = '';
        foreach( $data_attrs as $id => $val ){
            if( empty( $val ) ) continue;
            $data .= 'data-' . $id . '="' . $val . '" ';
        }

        $html .= '<div class="' . $wrap_classes . '" style="' . $wrap_styles . '" ' . $data . '>';
        $html .= '<div class="wpsr-sb-inner">';
        $html .= $row_html;
        $html .= '</div>';
        $html .= '<div class="wpsr-sb-close wpsr-close-btn" title="Open or close sharebar"><span class="wpsr-bar-icon">' . $open_icon . $close_icon . '</span></div>';
        $html .= '</div>';

        return $html;

    }


}

WPSR_Template_Floating_Sharebar::init();

?>