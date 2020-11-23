<?php
/**
 * Facebook widget
 */

defined( 'ABSPATH' ) || exit;

class WPSR_Facebook_Widget extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'WPSR_Facebook_Widget',
            'Facebook page widget',
            array( 'description' => __( 'Display your Facebook page\'s posts using this widget.', 'wpsr' ), ),
            array('width' => 500, 'height' => 500)
        );
    }

    public static function init(){
        add_filter( 'wpsr_register_widget', array( __class__, 'register' ) );
        add_filter( 'wpsr_register_admin_page', array( __class__, 'register_admin_page' ) );
    }

    public static function register( $widgets ){

        $widgets[ 'facebook' ] = array(
            'name' => __( 'Facebook widget', 'wpsr' ),
            'widget_class' => __class__
        );

        return $widgets;

    }

    public static function register_admin_page( $pages ){
        
        $pages[ 'facebook-widget' ] = array(
            'name' => __( 'Facebook page widget', 'wpsr' ),
            'banner' => WPSR_ADMIN_URL . '/images/banners/facebook-widget.svg',
            'link' => admin_url('widgets.php#wp-socializer:facebook'),
            'category' => 'widget',
            'type' => 'widget',
            'description' => __( 'If you have a facebook page, add a widget to display it\'s posts to the sidebar/footer.', 'wpsr' )
        );

        return $pages;

    }

    public static function defaults(){

        return array(
            'title' => '',
            'fb_page_url' => '',
            'fb_page_tabs' => 'timeline',
            'fb_page_small_header' => 'false',
            'fb_page_hide_cover' => 'false',
            'fb_page_show_faces' => 'true',
        );

    }

    function widget( $args, $instance ){

        $instance = WPSR_Lists::set_defaults( $instance, self::defaults() );

        WPSR_Widgets::before_widget( $args, $instance );

        $instance = array_map( 'esc_attr', $instance );

        echo '<div class="fb-page" data-href="' . $instance[ 'fb_page_url' ] . '" data-tabs="' . $instance[ 'fb_page_tabs' ] . '" data-width="" data-height="" data-small-header="' . $instance[ 'fb_page_small_header' ] . '" data-adapt-container-width="true" data-hide-cover="' . $instance[ 'fb_page_hide_cover' ] . '" data-show-facepile="' . $instance[ 'fb_page_show_faces' ] . '"><blockquote cite="' . $instance[ 'fb_page_url' ] . '" class="fb-xfbml-parse-ignore"><a href="' . $instance[ 'fb_page_url' ] . '">Facebook page</a></blockquote></div>';

        WPSR_Includes::add_active_includes( array( 'facebook_js' ) );

        WPSR_Widgets::after_widget( $args, $instance );

    }

    function form( $instance ){

        $instance = WPSR_Lists::set_defaults( $instance, self::defaults() );
        $fields = new WPSR_Widget_Form_Fields( $this, $instance );
        
        echo '<div class="wpsr_widget_wrap">';

        $gs = WPSR_Lists::set_defaults( get_option( 'wpsr_general_settings' ), WPSR_Options::default_values( 'general_settings' ) );
        if( empty( $gs[ 'facebook_app_id' ] ) ){
            echo '<p>' . __( 'Note: Facebook app ID is not set and it is required for the facebook widget. Please set it in the settings page', 'wpsr' ) . '</p>';
            echo '<p><a href="' . admin_url( 'admin.php?page=wp_socializer&tab=general_settings' ) . '" target="_blank" class="button button-primary">' . __( 'Open settings', 'wpsr' ) . '</a></p>';
        }

        $yes_no = array(
            'true' => __( 'Yes', 'wpsr' ),
            'false' => __( 'No', 'wpsr' )
        );
        
        $fields->text( 'title', 'Title' );
        $fields->text( 'fb_page_url', 'Your facebook page URL', array( 'placeholder' => 'Example: https://facebook.com/aakashweb' ) );
        $fields->text( 'fb_page_tabs', 'Tabs to display in the widget ( Enter tab names separated by comma. Example: timeline, events, messages )' );
        
        $fields->select( 'fb_page_small_header', 'Display small header', $yes_no, array( 'class' => 'smallfat' ) );
        $fields->select( 'fb_page_hide_cover', 'Hide cover photo', $yes_no, array( 'class' => 'smallfat' ) );
        $fields->select( 'fb_page_show_faces', 'Show friend\'s faces', $yes_no, array( 'class' => 'smallfat' ) );

        $fields->footer();

        echo '</div>';

    }

    function update( $new_instance, $old_instance ){
        return $new_instance;
    }

}

WPSR_Facebook_Widget::init();

?>