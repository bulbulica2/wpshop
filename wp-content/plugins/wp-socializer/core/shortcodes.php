<?php
/**
 * Shortcodes handler
 *
 **/

class WPSR_Shortcodes{

    public static function init(){

        add_shortcode( 'wpsr_share_icons', array( __class__, 'share_icons' ) );

        add_shortcode( 'wpsr_follow_icons', array( __class__, 'follow_icons' ) );

        add_shortcode( 'wpsr_share_link', array( __class__, 'share_link' ) );

    }

    public static function share_icons( $atts ){

        // If template parameter is set then use the saved configuration
        if( array_key_exists( 'template', $atts ) && !empty( $atts[ 'template' ] ) ){
            $si_settings = WPSR_Lists::set_defaults( get_option( 'wpsr_social_icons_settings' ), array(
                'ft_status' => 'enable',
                'tmpl' => array()
            ));
            $si_templates = $si_settings[ 'tmpl' ];
            
            if( empty( $si_templates ) ){
                $default_tmpl = WPSR_Options::default_values( 'share_icons' );
                array_push( $si_templates, $default_tmpl );
            }

            $index = $atts[ 'template' ];
            if( isset( $si_templates[ $index ] ) ) {
                $template = $si_templates[ $index ];
                $template = WPSR_Lists::set_defaults( $template, WPSR_Options::default_values( 'share_icons' ) );
                $out = WPSR_Template_Share_Icons::html( $template );
                return $out[ 'html' ];
            }else{
                return '<!-- Invalid template ID -->';
            }

        }

        $default_values = WPSR_Options::default_values( 'share_icons' );

        // Overwriting feature defaults
        $default_values[ 'heading' ] = '';
        $default_values[ 'icons' ] = 'facebook,twitter,linkedin,pinterest,email';

        // Merge got attributes with the defaults
        $atts = WPSR_Lists::set_defaults( $atts, $default_values );

        // Prepare the value as needed by selected_icons 
        $icons = explode( ',', $atts[ 'icons' ] );
        $icons_final = array();
        foreach( $icons as $icon ){
            $icon = trim( $icon );
            $icon_prop = array();
            $icon_prop[ $icon ] = array();
            array_push( $icons_final, $icon_prop );
        }

        $atts[ 'selected_icons' ] = base64_encode( json_encode( $icons_final ) );

        // Get the page metadata from the atts
        $page_info = array();
        foreach( $atts as $key => $val ){
            if( substr( $key, 0, 5 ) === 'page_' ){
                $meta_id = substr( $key, 5);
                $page_info[ $meta_id ] = $val;
                unset( $atts[ $key ] );
            }
        }

        $out = WPSR_Template_Share_Icons::html( $atts, $page_info );
        return $out[ 'html' ];

    }

    public static function follow_icons( $atts ){

        // Filter out the social media icons from the atts
        $social_icons = WPSR_Lists::social_icons();
        $social_icon_ids = array_keys( $social_icons );
        $atts_keys = array_keys( $atts );
        $follow_icons = array_intersect( $atts_keys, $social_icon_ids );

        if( empty( $follow_icons ) ){
            return '<!-- No valid follow icon parameter provided -->';
        }

        // Overwrite certain default values
        $default_values = WPSR_Options::default_values( 'follow_icons' );
        $default_values['orientation'] = 'horizontal';

        $atts = WPSR_Lists::set_defaults( $atts, $default_values );

        $icons_final = array();
        foreach( $follow_icons as $icon ){
            $icon_prop = array();
            $icon_prop[ $icon ] = array(
                'url' => $atts[ $icon ],
                'icon' => '',
                'text' => ''
            );
            array_push( $icons_final, $icon_prop );
        }

        $atts[ 'template' ] = base64_encode( json_encode( $icons_final ) );

        return WPSR_Template_Follow_Icons::html( $atts, false );

    }

    public static function share_link( $atts, $enclosed_content = null ){

        $atts = WPSR_Lists::set_defaults( $atts, array(
            'for' => '',
            'class' => '',
            'title' => '',
            'target' => '_blank'
        ));

        $social_icons = WPSR_Lists::social_icons();
        $id = $atts[ 'for' ];

        if( !array_key_exists( $id, $social_icons ) ){
            return $enclosed_content;
        }

        $props = $social_icons[ $id ];

        // Get the page metadata from the atts
        $custom_page_info = array();
        foreach( $atts as $key => $val ){
            if( substr( $key, 0, 5 ) === 'page_' ){
                $meta_id = substr( $key, 5);
                $custom_page_info[ $meta_id ] = $val;
                unset( $atts[ $key ] );
            }
        }

        // Set default page info
        $page_info = WPSR_Metadata::metadata();
        $page_info = WPSR_Lists::set_defaults( $custom_page_info, $page_info );

        $icon_link = $props[ 'link' ];
        $url = WPSR_Metadata::replace_params( $icon_link, $page_info );

        $link_attrs = array(
            'href' => esc_attr( $url ),
            'target' => esc_attr( $atts[ 'target' ] ),
            'class' => esc_attr( $atts[ 'class' ] ),
            'title' => esc_attr( $atts[ 'title' ] ),
            'rel' => 'nofollow'
        );

        $link_attr_text = '';
        foreach( $link_attrs as $link_attr_name => $link_attr_val ){
            if( empty( $link_attr_val ) ){
                continue;
            }
            $link_attr_text .= $link_attr_name . '="' . $link_attr_val . '" ';
        }

        return '<a ' . $link_attr_text . '>' . $enclosed_content . '</a>';

    }

}

WPSR_Shortcodes::init();

?>