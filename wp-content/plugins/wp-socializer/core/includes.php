<?php
/**
  * Controls the script and styles to be printed on page
  * 
  */

defined( 'ABSPATH' ) || exit;

class WPSR_Includes{
    
    private static $all_includes = array();
    private static $active_includes = array();
    
    public static function init(){

        add_action( 'init', array( __class__, 'register_defaults') );

        // Print CSS in header
        add_action( 'wp_enqueue_scripts' , array( __CLASS__, 'print_styles' ) );

        // Print scripts in footer
        add_action( 'wp_footer', array( __CLASS__, 'print_scripts' ) );

    }
    
    public static function register( $includes ){
        
        if( is_array( $includes ) ){
            foreach( $includes as $inc_id => $inc_info ){
                if( !array_key_exists( $inc_id, self::$all_includes ) ){
                    
                    self::$all_includes[ $inc_id ] = $inc_info;
                    
                }
            }
        }
        
    }
    
    public static function register_defaults(){

        $gsettings = WPSR_Lists::set_defaults( get_option( 'wpsr_general_settings' ), WPSR_Options::default_values( 'general_settings' ) );

        // The default includes for template
        self::register( array(
            'main_css' => array(
                'type' => 'css',
                'link' => WPSR_URL . 'public/css/wpsr.min.css',
                'deps' => array(),
                'version' => WPSR_VERSION
            ),
            
            'main_js' => array(
                'type' => 'js',
                'link' => WPSR_URL . 'public/js/wp-socializer.min.js',
                'deps' => array(),
                'version' => WPSR_VERSION
            ),
            
            'ajax_url' => array(
                'type' => 'js',
                'code' => '<script>var wpsr_ajax_url = "' . esc_attr( admin_url( 'admin-ajax.php' ) ) . '";</script>',
                'deps' => array(),
                'version' => WPSR_VERSION
            ),

            'fa_icons' => array(
                'type' => 'css',
                'link' => WPSR_Lists::get_font_icon()['prop']['link'],
                'deps' => array(),
                'version' => WPSR_VERSION
            ),

            'facebook_js' => array(
                'type' => 'js',
                'code' => '<div id="fb-root"></div>
                <script async defer crossorigin="anonymous" src="https://connect.facebook.net/' . $gsettings[ 'facebook_lang' ] . '/sdk.js#xfbml=1&version=v7.0&appId=' . $gsettings[ 'facebook_app_id' ] . '"></script>',
                'deps' => array(),
                'version' => WPSR_VERSION
            ),

            'twitter_js' => array(
                'type' => 'js',
                'code' => '<script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>',
                'deps' => array(),
                'version' => WPSR_VERSION
            )

        ));
        
        self::add_active_includes( array( 'main_css', 'main_js', 'ajax_url' ) );

    }

    public static function do_wp_register(){
        
        $includes = self::list_all();
        
        foreach( $includes as $inc_id => $inc_info ){
            
            $deps = array();
            if( isset( $inc_info[ 'deps' ] ) ){
                $deps = $inc_info[ 'deps' ];
            }
            
            $ver = false;
            if( isset( $inc_info[ 'version' ] ) ){
                $ver = $inc_info[ 'version' ];
            }
            
            if( $inc_info[ 'type' ] == 'js' ){
                if( isset( $inc_info[ 'link' ] ) ){
                    wp_register_script( 'wpsr_' . $inc_id, $inc_info[ 'link' ], $deps, $ver );
                }
            }elseif( $inc_info[ 'type' ] == 'css' ){
                if( isset( $inc_info[ 'link' ] ) ){
                    wp_register_style( 'wpsr_' . $inc_id, $inc_info[ 'link' ], $deps, $ver );
                }
            }
            
        }
    }
    
    public static function list_all(){
        
        $includes = apply_filters( 'wpsr_mod_includes_list', self::$all_includes );
        
        if( !is_array( $includes ) ){
            return array();
        }
        
        return $includes;
        
    }
    
    public static function add_active_includes( $include_ids ){
        
        $includes = self::list_all();
        
        if( !is_array( $include_ids ) ){
            return false;
        }
        
        foreach( $include_ids as $inc_id ){
            if( array_key_exists( $inc_id, $includes ) && !in_array( $inc_id, self::$active_includes ) ){
                array_push( self::$active_includes, $inc_id );
            }
        }
        
    }
    
    public static function active_includes(){
        
        return apply_filters( 'wpsr_mod_includes_active', self::$active_includes );
        
    }
    
    public static function print_scripts(){
        
        $includes = self::list_all();
        $active_includes = self::active_includes();
        
        echo "\n<!-- WP Socializer " . WPSR_VERSION . " - JS - Start -->\n";
        foreach( $active_includes as $a_inc ){
            
            if( self::skip_include( $a_inc ) ){
                continue;
            }
            
            if( array_key_exists( $a_inc, $includes ) ){
                $inc_info = $includes[ $a_inc ];
                if( $inc_info[ 'type' ] == 'js' ){
                    
                    if( array_key_exists( 'link', $inc_info ) ){
                        wp_enqueue_script( 'wpsr_' . $a_inc );
                    }elseif( array_key_exists( 'code', $inc_info ) ){
                        
                        if( isset( $inc_info[ 'deps' ] ) ){
                            foreach( $inc_info[ 'deps' ] as $dep_handle ){
                                wp_enqueue_script( $inc_info[ 'deps' ] );
                            }
                        }
                        
                        echo $inc_info[ 'code' ];
                    }
                    
                }
            }
        }
        echo "\n<!-- WP Socializer - JS - End -->\n";
        
        $gs = get_option( 'wpsr_general_settings' );
        $gs = WPSR_Lists::set_defaults( $gs, WPSR_Options::default_values( 'general_settings' ) );
        
        if( trim( $gs[ 'misc_additional_css' ] ) != '' ){
            echo "<!-- WP Socializer - Custom CSS rules - Start --><style>" . $gs[ 'misc_additional_css' ] . "</style><!-- WP Socializer - Custom CSS rules - End -->\n";
        }
        
    }
    
    public static function print_styles(){
        
        // Forcefully include all CSS includes
        $includes = self::list_all();
        
        // Register all the includes including JS and CSS
        self::do_wp_register();
        
        foreach( $includes as $inc_id => $inc_info ){
            
            if( self::skip_include( $inc_id ) ){
                continue;
            }
            
            if( $inc_info[ 'type' ] == 'css' ){
                
                if( isset( $inc_info[ 'link' ] ) ){
                    wp_enqueue_style( 'wpsr_' . $inc_id );
                }
                
                if( isset( $inc_info[ 'code' ] ) ){
                    echo '<style type="text/css">' . wp_strip_all_tags( $inc_info[ 'code' ] ) . '</style>';
                }
                
            }
        }
        
    }
    
    public static function preview_print_includes(){
        
        $includes = self::list_all();
        $active_includes = self::active_includes();
        
        // Include all CSS forcefully
        foreach( $includes as $inc_id => $inc_info ){
            
            if( $inc_info[ 'type' ] == 'css' ){
                
                if( isset( $inc_info[ 'link' ] ) ){
                    echo '<link rel="stylesheet" id="' . $inc_id . '" href="'  . $inc_info[ 'link' ] . '" type="text/css"/>'. "\n";
                }
                
                if( isset( $inc_info[ 'code' ] ) ){
                    echo '<style type="text/css">' . wp_strip_all_tags( $inc_info[ 'code' ] ) . '</style>';
                }
                
            }
        }
        
        foreach( $active_includes as $a_inc ){
            if( array_key_exists( $a_inc, $includes ) ){
                $inc_info = $includes[ $a_inc ];
                if( $inc_info[ 'type' ] == 'js' ){

                    if( array_key_exists( 'link', $inc_info ) ){
                        echo '<script src="' . $inc_info[ 'link' ] . '"></script>' . "\n";
                    }elseif( array_key_exists( 'code', $inc_info ) ){
                        echo $inc_info[ 'code' ];
                    }
                    
                }
            }
        }
        
    }
    
    public static function skip_include( $id ){
        
        $gsettings = WPSR_Lists::set_defaults( get_option( 'wpsr_general_settings' ), WPSR_Options::default_values( 'general_settings' ) );
        $skip_res_load = $gsettings[ 'skip_res_load' ];
        
        if( empty( $skip_res_load ) ){
            return false;
        }
        
        $skip_res_load = array_map( 'trim', explode( ',', $skip_res_load ) );
        
        if( in_array( $id, $skip_res_load ) ){
            return true;
        }else{
            return false;
        }
    }
    
}

WPSR_Includes::init();

?>