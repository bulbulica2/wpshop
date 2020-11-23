<?php
/**
 * Share icons template
 *
 **/

defined( 'ABSPATH' ) || exit;

class WPSR_Template_Share_Icons{

    public static function init(){

        add_action( 'init', array( __CLASS__, 'output' ) );

    }

    public static function output(){

        $si_settings = WPSR_Lists::set_defaults( get_option( 'wpsr_social_icons_settings' ), array(
            'ft_status' => 'enable',
            'tmpl' => array()
        ));
        $si_templates = $si_settings[ 'tmpl' ];
        
        if( empty( $si_templates ) ){
            $default_tmpl = WPSR_Options::default_values( 'share_icons' );
            array_push( $si_templates, $default_tmpl );
        }

        if($si_settings[ 'ft_status' ] != 'disable'){
            foreach( $si_templates as $tmpl ){
                
                $content_obj = new wpsr_template_button_handler( $tmpl, 'content' );
                $excerpt_obj = new wpsr_template_button_handler( $tmpl, 'excerpt' );
                
                add_filter( 'the_content', array( $content_obj, 'print_template' ), 10 );
                add_filter( 'the_excerpt', array( $excerpt_obj, 'print_template' ), 10 );
                
            }
        }

    }

    public static function html( $tmpl, $default_page_info = array() ){

        $social_icons = WPSR_Lists::social_icons();
        $page_info = WPSR_Metadata::metadata();
        $page_info = WPSR_Lists::set_defaults( $default_page_info, $page_info );

        $counter_services = WPSR_Share_Counter::counter_services();
        $selected_icons = json_decode( base64_decode( $tmpl[ 'selected_icons' ] ) );

        $classes = array( 'socializer', 'sr-popup' );

        if( $tmpl[ 'layout' ] != '' ){
            array_push( $classes, 'sr-' . $tmpl[ 'layout' ] );
        }

        if( $tmpl[ 'icon_size' ] != '' ){
            array_push( $classes, 'sr-' . $tmpl[ 'icon_size' ] );
        }

        if( $tmpl[ 'icon_shape' ] != '' && $tmpl[ 'layout' ] == '' ){
            array_push( $classes, 'sr-' . $tmpl[ 'icon_shape' ] );
        }

        if( $tmpl[ 'hover_effect' ] != '' ){
            array_push( $classes, 'sr-' . $tmpl[ 'hover_effect' ] );
        }

        if( $tmpl[ 'padding' ] != '' ){
            array_push( $classes, 'sr-' . $tmpl[ 'padding' ] );
        }

        $styles = array();
        if ( $tmpl[ 'icon_bg_color' ] != '' ) array_push( $styles, 'background-color: ' . $tmpl[ 'icon_bg_color' ] );
        if ( $tmpl[ 'icon_color' ] != '' ) array_push( $styles, 'color: ' . $tmpl[ 'icon_color' ] );
        $style = join( ';', $styles );
        $style = ( $style != '' ) ? ' style="' . $style . '"' : '';

        $icons_html = array();

        $counters_selected = array();

        if( empty( $selected_icons ) ){
            return array(
                'html' => ''
            );
        }

        foreach( $selected_icons as $icon ){

            $id = key( $icon );

            if( !array_key_exists( $id, $social_icons ) ){
                continue;
            }

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

            $icon_link = $props[ 'link' ];
            $url = WPSR_Metadata::replace_params( $icon_link, $page_info );
            $onclick = isset( $props[ 'onclick' ] ) ? 'onclick="' . $props[ 'onclick' ] . '"' : '';
            
            $text = '';
            if( $settings[ 'text' ] != '' ){
                $text = '<span class="text">' . $settings[ 'text' ] . '</span>';
                array_push( $icon_classes, 'sr-text-in' );
            }

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
            if( ( $tmpl[ 'share_counter' ] == 'individual' || $tmpl[ 'share_counter' ] == 'total-individual' ) && array_key_exists( $id, $counter_services) ){
                $count_holder = WPSR_Share_Counter::placeholder( $page_info[ 'url' ], $id );
                $count_tag = '<span class="ctext">' . $count_holder . '</span>';
                array_push( $classes, 'sr-' . $tmpl[ 'sc_style' ] );
                if( $tmpl[ 'sc_style' ] != 'count-1' ){
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

            $ihtml = '<span class="' . implode( ' ', $icon_classes ) . '"><a rel="nofollow" href="' . esc_attr( $url ) . '" target="_blank" ' . $onclick . ' title="' . esc_attr( $title ) . '" ' . $style . ' ' . $data_attr . '>' . $icon . $text . $count_tag . '</a></span>';
            array_push( $icons_html, $ihtml );

        }

        if( intval( $tmpl[ 'more_icons' ] ) > 0 ){
            $more_count = intval( $tmpl[ 'more_icons' ] );
            $more_icons = array_slice( $icons_html, -$more_count, $more_count );
            $more_html = '<span class="sr-more"><a href="#" target="_blank" title="More sites" ' . $style . '><i class="fa fa-share-alt"></i></a><ul class="socializer">' . implode( "\n", $more_icons ) . '</ul></span>';
            $icons_html = array_slice( $icons_html, 0, -$more_count );
            array_push( $icons_html, $more_html );
        }

        $all_icons_html = '<div class="' . implode( " ", $classes ) . '">' . implode( "\n", $icons_html ) . '</div>';
        $row_html = $all_icons_html;

        $wrap_classes = '';
        $html = '';

        if( $tmpl[ 'share_counter' ] == 'total' || $tmpl[ 'share_counter' ] == 'total-individual' ){
            $total_counter_html = WPSR_Share_Counter::total_count_html( array(
                'text' => 'Shares',
                'counter_color' => '#000',
                'add_services' => $counters_selected,
                'size' => $tmpl[ 'icon_size' ]
            ), $page_info);

            if( $tmpl[ 'sc_total_position' ] == 'left' ){
                $row_html = $total_counter_html;
                $row_html .= $all_icons_html;
            }else{
                $row_html = $all_icons_html;
                $row_html .= $total_counter_html;
            }

        }

        if( $tmpl[ 'layout' ] == '' && $tmpl[ 'center_icons' ] == 'yes' ){
            $wrap_classes = 'wpsr-flex-center';
        }

        $data_attrs = array(
            'lg-action' => esc_attr( $tmpl[ 'lg_screen_action' ] ),
            'sm-action' => esc_attr( $tmpl[ 'sm_screen_action' ] ),
            'sm-width' => esc_attr( $tmpl[ 'sm_screen_width' ] ),
        );
        $data = '';
        foreach( $data_attrs as $id => $val ){
            if( empty( $val ) ) continue;
            $data .= 'data-' . $id . '="' . $val . '" ';
        }

        if( trim( $tmpl[ 'custom_html_above' ] ) != '' ){
            $html .= $tmpl[ 'custom_html_above' ];
        }

        $html .= '<div class="wp-socializer wpsr-share-icons ' . $wrap_classes . '" ' . $data . '>';
        if( trim( $tmpl[ 'heading' ] ) != '' ) $html .= $tmpl[ 'heading' ];
        $html .= '<div class="wpsr-si-inner">' . $row_html . '</div>';
        $html .= '</div>';

        if( trim( $tmpl[ 'custom_html_below' ] ) != '' ){
            $html .= $tmpl[ 'custom_html_below' ];
        }

        return array(
            'html' => $html
        );

    }

}

WPSR_Template_Share_Icons::init();

class wpsr_template_button_handler{
    
    private $props;
    private $type;
    
    function __construct( $properties, $type ){

        $this->type = $type;
        $this->props = WPSR_Lists::set_defaults( $properties, WPSR_Options::default_values( 'share_icons' ) );

    }
    
    function print_template( $content ){
        
        $call_from_excerpt = 0;
        $call_stack = debug_backtrace();
        
        foreach( $call_stack as $call ){
            if( $call['function'] == 'the_excerpt' || $call['function'] == 'get_the_excerpt' ){
                $call_from_excerpt = 1;
            }
        }
        
        $loc_rules_answer = WPSR_Location_Rules::check_rule( $this->props[ 'loc_rules' ] );
        $rule_in_excerpt = ( $this->props[ 'in_excerpt' ] == 'show' );
        $output = $content;
        
        if( $loc_rules_answer ){
            
            if( ( $this->type == 'content' && $call_from_excerpt != 1 ) || ( $this->type == 'excerpt' && $rule_in_excerpt == 1 ) ){
                
                $gen_out = WPSR_Template_Share_Icons::html( $this->props );
                
                if( !empty( $gen_out[ 'html' ] ) ){
                
                    $final_template = $gen_out[ 'html' ];
                    
                    if( $this->props[ 'position' ] == 'above_below_posts' )
                        $output = $final_template . $content . $final_template;
                    
                    if( $this->props[ 'position' ] == 'above_posts' )
                        $output = $final_template . $content;
                    
                    if( $this->props[ 'position' ] == 'below_posts' )
                        $output = $content . $final_template;
                    
                }
            }
            
            do_action( 'wpsr_do_buttons_print_template_end' );
            
        }
        
        return $output;
        
    }
    
}

?>