<?php
/**
 * Follow icons template
 *
 **/

defined( 'ABSPATH' ) || exit;

class WPSR_Template_Follow_Icons{
    
    public static function init(){

        add_action( 'wp_footer', array( __CLASS__, 'output' ) );

    }
    
    public static function output(){

        $fb_settings = WPSR_Lists::set_defaults( get_option( 'wpsr_followbar_settings' ), WPSR_Options::default_values( 'follow_icons' ) );
        $loc_rules_answer = WPSR_Location_Rules::check_rule( $fb_settings[ 'loc_rules' ] );
        
        if( $fb_settings[ 'ft_status' ] != 'disable' && $loc_rules_answer ){
            $gen_html = self::html( $fb_settings );
            echo $gen_html;
            do_action( 'wpsr_do_followbar_print_template_end' );
        }
        
    }
    
    public static function html( $opts, $floating = True ){
        
        $opts = WPSR_Lists::set_defaults( $opts, WPSR_Options::default_values( 'follow_icons' ) );
        $template = $opts[ 'template' ];
        $decoded = base64_decode( $template );
        $btns = json_decode( $decoded );
        $sb_sites = WPSR_Lists::social_icons();
        $html = '';
        
        if( !is_array( $btns ) || empty( $btns ) ){
            return '';
        }
        
        $styles = array();
        if ( $opts[ 'bg_color' ] != '' ) array_push( $styles, 'background-color: ' . $opts[ 'bg_color' ] . ';border-color: ' . $opts[ 'bg_color' ] );
        if ( $opts[ 'icon_color' ] != '' ) array_push( $styles, 'color: ' . $opts[ 'icon_color' ] );
        $style = join( ';', $styles );
        
        foreach( $btns as $btn_obj ){

            $btn_obj = apply_filters( 'wpsr_mod_followbar_button', $btn_obj );

            $id = key( (array) $btn_obj );
            
            if(!array_key_exists($id, $sb_sites)){
                continue;
            }
            
            $prop = $sb_sites[ $id ];
            
            $cicon = '';
            if ( $btn_obj->$id->icon != '' ){
                $cicon = 'sr-cicon';
            }
            
            $iclasses = array( 'sr-' . $id, $cicon );
            $onclick = array_key_exists( 'onclick', $prop ) ? 'onclick="' . esc_attr( $prop[ 'onclick' ] ) . '"' : '';
            $title = ( ( $btn_obj->$id->text == '' ) ? $prop[ 'name' ] : urldecode( $btn_obj->$id->text ) );
            
            $html .= '<span class="' . esc_attr( join( ' ', $iclasses ) ) . '">';
            $html .= '<a rel="nofollow" href="' . esc_attr( $btn_obj->$id->url ) . '" target="_blank" title="' . esc_attr( $title ) . '" style="' . esc_attr( $style ) . '" ' . $onclick . '>';
            
            if( $btn_obj->$id->icon == '' ){
                $html .= '<i class="' . esc_attr( $prop[ 'icon' ] ) . '"></i>';
            }else{
                $html .= '<img src="' . esc_attr( $btn_obj->$id->icon ) . '" alt="' . esc_attr( $prop[ 'name' ] ) . '" />';
            }
            
            $html .= '</a>';
            
            $html .= '</span>';
            
        }
        
        $classes = array( 'socializer', 'sr-followbar', 'sr-' . $opts[ 'size' ] );
        
        if( $opts[ 'shape' ] != '' ) array_push( $classes, 'sr-' . $opts[ 'shape' ] );
        if( $opts[ 'hover' ] != '' ) array_push( $classes, 'sr-' . $opts[ 'hover' ] );
        if( $opts[ 'pad' ] != '' ) array_push( $classes, 'sr-' . $opts[ 'pad' ] );
        if( $opts[ 'orientation' ] == 'vertical' ) array_push( $classes, 'sr-vertical' );
        if( $opts[ 'open_popup' ] == '' ) array_push( $classes, 'sr-popup' );
        if( !$floating ) array_push( $classes, 'sr-multiline' );
        
        $classes = join( ' ', $classes );
        
        $html = '<div class="' . $classes . '">' . $html . '</div>';
        
        $open_icon = WPSR_Lists::public_icons( 'fb_open' );
        $close_icon = WPSR_Lists::public_icons( 'fb_close' );
        
        if( $floating ){
            $title = ( $opts[ 'title' ] != '' ) ? '<div class="sr-fb-title">' . $opts[ 'title' ] . '</div>' : '';
            $close_btn = '<div class="wpsr-fb-close wpsr-close-btn" title="Open or close follow icons"><span class="wpsr-bar-icon">' . $open_icon . $close_icon . '</span></div>';
            $orientation = ( $opts[ 'orientation' ] == 'horizontal' ) ? 'sr-fb-hl' : 'sr-fb-vl';
            $fb_classes = array( 'wp-socializer wpsr-follow-icons', 'sr-fb-' . $opts[ 'position' ], $orientation );
            
            $data_attrs = array(
                'lg-action' => esc_attr( $opts[ 'lg_screen_action' ] ),
                'sm-action' => esc_attr( $opts[ 'sm_screen_action' ] ),
                'sm-width' => esc_attr( $opts[ 'sm_screen_width' ] ),
            );
            $data = '';
            foreach( $data_attrs as $id => $val ){
                if( empty( $val ) ) continue;
                $data .= 'data-' . $id . '="' . $val . '" ';
            }

            $html = '<div class="' . esc_attr( join( ' ', $fb_classes ) ) . '" ' . $data . '>' . $title . $html . $close_btn . '</div>';
        }
        
        if( !$floating && isset( $opts[ 'profile_text' ] ) && trim( $opts[ 'profile_text' ] ) != '' ){
            $html = '<p>' . $opts[ 'profile_text' ] . '</p>' . $html;
        }
        
        return $html;
        
    }
    
}

WPSR_Template_Follow_Icons::init();

?>