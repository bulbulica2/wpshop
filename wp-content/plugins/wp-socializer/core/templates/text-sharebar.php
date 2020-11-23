<?php
/**
 * Text sharebar template
 *
 **/

defined( 'ABSPATH' ) || exit;

class WPSR_Template_Text_Sharebar{
    
    public static function init(){

        add_action( 'wp_footer', array( __CLASS__, 'output' ) );

    }

    public static function output(){

        $tsb_settings = WPSR_Lists::set_defaults( get_option( 'wpsr_text_sharebar_settings' ), WPSR_Options::default_values( 'text_sharebar' ) );
        $loc_rules_answer = WPSR_Location_Rules::check_rule( $tsb_settings[ 'loc_rules' ] );
        
        if( $tsb_settings[ 'ft_status' ] != 'disable' && $loc_rules_answer && !wp_is_mobile() ){
            $gen_html = self::html( $tsb_settings );
            echo $gen_html;
            do_action( 'wpsr_do_text_sharebar_print_template_end' );
        }
        
    }
    
    public static function html( $opts ){
        
        $opts = WPSR_Lists::set_defaults( $opts, WPSR_Options::default_values( 'text_sharebar' ) );
        $template = $opts[ 'template' ];
        $decoded = base64_decode( $template );
        $btns = json_decode( $decoded );
        $sb_sites = WPSR_Lists::social_icons();
        $page_info = WPSR_Metadata::metadata();
        $html = '';
        
        if( !is_array( $btns ) || empty( $btns ) ){
            return '';
        }
        
        foreach( $btns as $btn ){
            $sb_info = $sb_sites[ $btn ];
            $link = array_key_exists( 'link_tsb', $sb_info ) ? $sb_info[ 'link_tsb' ] : $sb_info[ 'link' ];
            $onclick = array_key_exists( 'onclick', $sb_info ) ? 'onclick="' . esc_attr( $sb_info[ 'onclick' ] ) . '"' : '';
            
            $html .= '<li><a href="#" title="' . esc_attr( $sb_info[ 'title' ] ) . '" data-link="' . esc_attr( $link ) . '" style="color: ' . esc_attr( $opts[ 'icon_color' ] ) . '" ' . $onclick . '><i class="' . esc_attr( $sb_info[ 'icon' ] ) . '"></i></a></li>';
        }
        
        $html = '<ul class="wpsr-text-sb wpsr-tsb-' . esc_attr( $opts[ 'size' ] ) . ' wpsr-clearfix" data-content="' . esc_attr( $opts[ 'content' ] ) . '" data-tcount="' . esc_attr( $opts[ 'text_count' ] ) . '" style="background-color: ' . esc_attr( $opts[ 'bg_color' ] ) . '" data-url="' . esc_attr( $page_info[ 'url' ] ) . '" data-title="' . esc_attr( $page_info[ 'title' ] ) . '" data-surl="' . esc_attr( $page_info[ 'short_url' ] ) . '" data-tuname="' . esc_attr( $page_info[ 'twitter_username' ] ) . '">' . $html . '</ul>';
        
        return $html;
        
    }
    
}

WPSR_Template_Text_Sharebar::init();

?>