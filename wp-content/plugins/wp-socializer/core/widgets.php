<?php
/**
 * Widget class for WP Socializer
 * 
 */

defined( 'ABSPATH' ) || exit;

class WPSR_Widgets{
    
    private static $widgets = array();
    
    public static function init(){

        add_action( 'widgets_init', array( __class__, 'register_widgets' ) );

        add_action( 'admin_enqueue_scripts', array( __class__, 'print_widget_scripts' ) );

    }

    public static function register_widgets(){

        $init_widgets = apply_filters( 'wpsr_register_widget', array() );
        $defaults = array(
            'name' => '',
            'widget_class' => ''
        );

        foreach( $init_widgets as $id => $config ){
            self::$widgets[ $id ] = WPSR_Lists::set_defaults( $config, $defaults );
            if( !empty( $config[ 'widget_class' ] ) ){
                register_widget( $config[ 'widget_class' ] );
            }
        }

    }

    public static function get_widgets(){
        
        return apply_filters( 'wpsr_mod_widgets', self::$widgets );
        
    }
    
    public static function print_widget_scripts( $hook ){

        if( $hook == 'widgets.php' ){
            
            echo '<script>window.wpsr_ppe_ajax = "' . esc_attr( get_admin_url() . 'admin-ajax.php' ) . '"; </script>';
            
            wp_enqueue_style( 'wpsr_admin_widget_css', WPSR_ADMIN_URL . 'css/style_widgets.css' );
            wp_enqueue_script( 'wpsr_admin_widget_js', WPSR_ADMIN_URL . 'js/script_widgets.js', array( 'jquery' ) );
            wp_enqueue_script( 'wp-color-picker' );
            wp_enqueue_style( 'wp-color-picker' );
            wp_enqueue_style( 'wpsr_fa', WPSR_Lists::ext_res( 'font-awesome-adm' ) );
            
            wp_enqueue_style( 'wpsr_ipopup', WPSR_ADMIN_URL . 'css/ipopup.css' );
            wp_enqueue_script( 'wpsr_ipopup', WPSR_ADMIN_URL . 'js/ipopup.js' );
        }

    }

    public static function before_widget( $args, $instance ){

        echo $args[ 'before_widget' ];
        if ( !empty( $instance[ 'title' ] ) ) {
            echo $args[ 'before_title' ] . apply_filters( 'widget_title', $instance[ 'title' ] ) . $args[ 'after_title' ];
        }

    }

    public static function after_widget( $args, $instance ){
        echo $args['after_widget'];
    }

}

WPSR_Widgets::init();

?>